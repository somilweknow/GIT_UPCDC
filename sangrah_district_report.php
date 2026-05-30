<?php
include("scripts/settings.php");
// error_reporting(E_ALL);

page_header_start('Sangrah District Participation Report');
?>
<!-- Excel and PDF JS Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<?php
page_header_end();
page_sidebar();

// Mapping of the 7 forms
$sangrah_forms = [
    'sangrah_pacs_bakaya_daily_collection' => 'PACS Bakaya',
    'sangrah_sahkari_gram_vikas_daily_collection' => 'Gram Vikas',
    'sangrah_vividhikaran_daily_collection' => 'Vividhikaran',
    'sangrah_amin_daily_progress' => 'Amin Progress',
    'sangrah_nidhi_daily' => 'Nidhi',
    'sangrah_payment_daily' => 'Payment',
    'sangrah_sahkari_deyo_daily_collection' => 'Sahkari Deyo'
];

// --- DATA FETCHING ---
$state_totals = array_fill_keys(array_keys($sangrah_forms), 0);
$district_counts = [];

// Get all districts sorted by name
$sql = "SELECT sno as district_id, district_name FROM master_district WHERE sno != 28 ORDER BY district_name";
$res_dist = execute_query($sql);
while ($row = mysqli_fetch_assoc($res_dist)) {
    $did = $row['district_id'];
    $district_counts[$did] = [
        'name' => $row['district_name'],
        'counts' => array_fill_keys(array_keys($sangrah_forms), 0)
    ];
}

// Fetch participation
foreach ($sangrah_forms as $table => $label) {
    $sql = "SELECT DISTINCT district_id FROM $table";
    $res = execute_query($sql);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $did = $row['district_id'];
            if (isset($district_counts[$did])) {
                $district_counts[$did]['counts'][$table] = 1;
                $state_totals[$table]++;
            }
        }
    }
}
$state_grand_total = array_sum($state_totals);
?>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --accent-purple: #9c27b0;
        --deep-blue: #1a237e;
    }

    .premium-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
        background: #fff;
        overflow: hidden;
    }

    .report-header {
        background: var(--primary-gradient);
        color: white;
        padding: 25px;
        text-align: center;
    }

    .report-header h3 {
        margin: 0;
        font-weight: 800;
        font-size: 24px;
    }

    .table-responsive {
        padding: 25px;
    }

    .sangrah-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        border: 1px solid #eef0f7;
        border-radius: 16px;
    }

    .sangrah-table thead th { background-color: var(--deep-blue); color: #ffffff !important; font-weight: 900; font-size: 18px; padding: 20px 10px; border-bottom: 3px solid #764ba2; text-align: center; border: 1px solid rgba(255,255,255,0.1); text-transform: uppercase; letter-spacing: 0.5px; }
    .sangrah-table thead th * { color: #ffffff !important; }

    .sangrah-table tbody td {
        padding: 16px 10px;
        border: 1px solid #f0f2f9;
        text-align: center;
        font-size: 16px;
        color: #1a202c;
        font-weight: 700;
    }

    .sangrah-table tbody tr:hover {
        background-color: #f8faff;
    }

    .district-col {
        text-align: left !important;
        font-weight: 700;
        color: var(--deep-blue) !important;
        position: sticky;
        left: 0;
        z-index: 10;
        box-shadow: 3px 0 10px rgba(0, 0, 0, 0.05);
    }
    
    /* Ensure body cells stay white but header can be colored */
    tbody .district-col { background-color: #fff !important; }
    thead .district-col { background-color: var(--deep-blue) !important; color: #ffffff !important; }

    .count-badge {
        display: inline-block;
        padding: 4px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 16px;
    }

    .badge-positive {
        background-color: #ede7f6;
        color: #5e35b1;
        border: 1px solid #d1c4e9;
    }

    .badge-zero {
        background-color: #f1f3f5;
        color: #adb5bd;
    }

    .footer-row {
        background-color: var(--deep-blue) !important;
        color: white !important;
        font-weight: 800;
    }

    .footer-row td {
        color: white !important;
        padding: 15px !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-weight: bold !important;
    }

    .btn-export {
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: bold;
        margin-left: 10px;
        border: none;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-excel {
        background-color: #28a745;
        color: white;
    }

    .btn-pdf {
        background-color: #dc3545;
        color: white;
    }

    #exportTable {
        display: none;
    }
    @media print {
        @page {
            size: A4 portrait;
            /* size: A4 landscape; */
            margin: 4mm;
        }
    }
</style>

<div class="container-fluid">
    <div class="card premium-card">
        <div class="report-header">
            <h3><i class="fas fa-file-invoice"></i> Sangrah Participation Report</h3>
            <p class="mb-0">District-wise submission status (Aggregated)</p>
        </div>

        <div class="table-responsive">
            <div class="mb-4 text-right">
                <button onclick="exportExcel()" class="btn-export btn-excel"><i class="fas fa-file-excel"></i> Export
                    Excel</button>
                <button onclick="exportPDF()" class="btn-export btn-pdf"><i class="fas fa-file-pdf"></i> Export
                    PDF</button>
            </div>

            <!-- WEB UI TABLE -->
            <table class="table sangrah-table" id="reportTable">
                <thead>
                    <tr style="border-bottom: 2px solid rgba(255,255,255,0.3);">
                        <th style="width: 50px;">SNo.</th>
                        <th class="district-col">Janpad (District)</th>
                        <?php foreach ($sangrah_forms as $label): ?>
                            <th><?php echo $label; ?></th><?php endforeach; ?>
                        <th style="color: #ffffff;">Total</th>
                    </tr>
                    <tr style="background-color: var(--deep-blue) !important;">
                        <th style="color: #ffffff !important; font-size: 14px; padding: 10px; border-top: 1px solid rgba(255,255,255,0.2);">1</th>
                        <th class="district-col" style="background-color: var(--deep-blue) !important; color: #ffffff !important; font-size: 14px; padding: 10px; border-top: 1px solid rgba(255,255,255,0.2);">2</th>
                        <?php 
                        $cidx = 3;
                        foreach ($sangrah_forms as $label): ?>
                            <th style="color: #ffffff !important; font-size: 18px; padding: 10px; border-top: 1px solid rgba(255,255,255,0.2);"><?php echo $cidx++; ?></th>
                        <?php endforeach; ?>
                        <th style="color: #ffffff !important; font-size: 18px; padding: 10px; border-top: 1px solid rgba(255,255,255,0.2);"><?php echo $cidx; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="footer-row" style="background-color: #fff2cc !important; color: #000 !important; font-weight: bold;">
                        <td colspan="2" style="text-align: right; font-weight: bold; color: #000 !important; background-color: #fff2cc !important; font-size: 18px;">STATE TOTAL</td>
                        <?php foreach ($sangrah_forms as $table => $label): ?>
                            <td style="font-weight: bold; color: #000 !important; background-color: #fff2cc !important; font-size: 18px;"><?php echo $state_totals[$table]; ?></td>
                        <?php endforeach; ?>
                        <td style="font-weight: bold; color: #000 !important; background-color: #fff2cc !important; font-size: 18px;"><?php echo $state_grand_total; ?></td>
                    </tr>
                    <?php
                    $sl = 1;
                    foreach ($district_counts as $did => $dist):
                        $row_total = array_sum($dist['counts']);
                        ?>
                        <tr>
                            <td><?php echo $sl++; ?></td>
                            <td class="district-col"><?php echo $dist['name']; ?></td>
                            <?php foreach ($sangrah_forms as $table => $label):
                                $val = $dist['counts'][$table];
                                ?>
                                <td>
                                    <span class="count-badge <?php echo $val > 0 ? 'badge-positive' : 'badge-zero'; ?>">
                                        <?php echo $val; ?>
                                    </span>
                                </td>
                            <?php endforeach; ?>
                            <td style="background-color: #f3f0ff; color: #764ba2; font-weight: 800;">
                                <?php echo $row_total; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="footer-row" style="font-weight: bold;">
                        <td colspan="2" style="text-align: right; font-weight: bold;">STATE TOTAL</td>
                        <?php foreach ($sangrah_forms as $table => $label): ?>
                            <td style="font-weight: bold;"><?php echo $state_totals[$table]; ?></td>
                        <?php endforeach; ?>
                        <td style="font-weight: bold;"><?php echo $state_grand_total; ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- HIDDEN EXPORT TABLE -->
            <table id="exportTable" border="1"
                style="border-collapse: collapse; width: 100%; font-family: 'Arial', sans-serif;">
                <thead>
                    <tr
                        style="background-color: #d9e1f2; font-weight: bold; text-align: center; height: 60pt; mso-height-source:userset;">
                        <th style="border: 2px solid #000; font-size: 18pt; width: 80px;">Sl. No.</th>
                        <th style="border: 2px solid #000; font-size: 18pt; width: 350px;">District</th>
                        <?php foreach ($sangrah_forms as $label): ?>
                            <th style="border: 2px solid #000; font-size: 18pt; width: 160px;"><?php echo $label; ?></th>
                        <?php endforeach; ?>
                        <th style="border: 2px solid #000; font-size: 18pt; width: 100px;">Total</th>
                    </tr>
                    <tr
                        style="background-color: #f2f2f2; text-align: center; font-size: 11pt; height: 30pt; mso-height-source:userset;">
                        <th style="border: 1px solid #000;">1</th>
                        <th style="border: 1px solid #000;">2</th>
                        <?php
                        $idx = 3;
                        foreach ($sangrah_forms as $label): ?>
                            <th style="border: 1px solid #000;"><?php echo $idx++; ?></th>
                        <?php endforeach; ?>
                        <th style="border: 1px solid #000;"><?php echo $idx; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        style="background-color: #fff2cc; font-weight: bold; text-align: center; height: 50pt; mso-height-source:userset;">
                        <td style="border: 2px solid #000; font-weight: bold;"></td>
                        <td style="border: 2px solid #000; font-size: 16pt; font-weight: bold;">STATE TOTAL</td>
                        <?php foreach ($sangrah_forms as $table => $label): ?>
                            <td style="border: 2px solid #000; font-size: 16pt; font-weight: bold;">
                                <?php echo $state_totals[$table]; ?></td>
                        <?php endforeach; ?>
                        <td style="border: 2px solid #000; font-size: 16pt; font-weight: bold;">
                            <?php echo $state_grand_total; ?></td>
                    </tr>

                    <?php
                    $sl = 1;
                    foreach ($district_counts as $did => $dist):
                        $row_total = array_sum($dist['counts']);
                        ?>
                        <tr style="text-align: center; height: 35pt; mso-height-source:userset;">
                            <td style="border: 1px solid #000; font-size: 14pt;"><?php echo $sl++; ?></td>
                            <td style="border: 1px solid #000; font-size: 14pt; text-align: left; padding-left: 10px;">
                                <?php echo strtoupper($dist['name']); ?></td>
                            <?php foreach ($sangrah_forms as $table => $label):
                                $v = $dist['counts'][$table];
                                ?>
                                <td
                                    style="border: 1px solid #000; font-size: 14pt; <?php echo $v > 0 ? 'background-color: #e2efda;' : ''; ?>">
                                    <?php echo $v; ?></td>
                            <?php endforeach; ?>
                            <td
                                style="border: 1px solid #000; font-size: 14pt; background-color: #fce4d6; font-weight: bold;">
                                <?php echo $row_total; ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <tr
                        style="background-color: #fff2cc; font-weight: bold; text-align: center; height: 50pt; mso-height-source:userset;">
                        <td style="border: 2px solid #000; font-weight: bold;"></td>
                        <td style="border: 2px solid #000; font-size: 16pt; font-weight: bold;">STATE TOTAL</td>
                        <?php foreach ($sangrah_forms as $table => $label): ?>
                            <td style="border: 2px solid #000; font-size: 16pt; font-weight: bold;">
                                <?php echo $state_totals[$table]; ?></td>
                        <?php endforeach; ?>
                        <td style="border: 2px solid #000; font-size: 16pt; font-weight: bold;">
                            <?php echo $state_grand_total; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function exportExcel() {
        const table = document.getElementById('exportTable').outerHTML;
        const title = "<h1 style='text-align:center; font-size: 20pt; font-family: Calibri; font-weight: bold; height: 60pt; vertical-align: middle;'>Sangrah Participation Report — District Totals | Date: <?php echo date('d-m-Y'); ?></h1>";

        const html = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta charset="utf-8"/><style> .num {mso-number-format:General;} td, th { border: 1pt solid black; vertical-align: middle; } tr { mso-height-source:userset; } b { font-weight: bold; } </style></head>
        <body>${title}${table}</body>
        </html>`;

        const blob = new Blob([html], { type: "application/vnd.ms-excel" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = "Sangrah_District_Report_<?php echo date('d-m-Y'); ?>.xls";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }

    function exportPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'pt', 'a4');

        doc.setFontSize(20);
        doc.text("Sangrah Participation Report — District Totals | Date: <?php echo date('d-m-y'); ?>", doc.internal.pageSize.getWidth() / 2, 40, { align: "center" });

        doc.autoTable({
            html: '#exportTable',
            startY: 60,
            theme: 'grid',
            styles: { fontSize: 11, halign: 'center', cellPadding: 4, textColor: [0, 0, 0], lineColor: [0, 0, 0], lineWidth: 1, minCellHeight: 25 },
            headStyles: { fontSize: 14, fillColor: [217, 225, 242], textColor: [0, 0, 0], fontStyle: 'bold' },
            didParseCell: function (data) {
                const row = data.row.raw;
                if (row && row.style) {
                    const bg = row.style.backgroundColor;
                    // Make State Total rows bold in PDF
                    if (bg === 'rgb(255, 242, 204)') {
                        data.cell.styles.fillColor = [255, 242, 204];
                        data.cell.styles.fontStyle = 'bold';
                    }
                    if (bg === 'rgb(242, 242, 242)') {
                        data.cell.styles.fillColor = [242, 242, 242];
                        data.cell.styles.fontSize = 9;
                    }
                }
                if (data.cell.raw && data.cell.raw.style) {
                    const cbg = data.cell.raw.style.backgroundColor;
                    if (cbg === 'rgb(226, 239, 218)') data.cell.styles.fillColor = [226, 239, 218];
                    if (cbg === 'rgb(252, 228, 214)') {
                        data.cell.styles.fillColor = [252, 228, 214];
                        data.cell.styles.fontStyle = 'bold';
                    }
                }
            }
        });

        doc.save("Sangrah_District_Report_<?php echo date('d-m-Y'); ?>.pdf");
    }

    $(document).ready(function () {
        $('#reportTable').DataTable({
            "paging": false,
            "searching": true,
            "ordering": true,
            "info": true
        });
    });
</script>

<?php
page_footer_start();
page_footer_end();
?>