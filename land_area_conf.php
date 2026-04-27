<?php
include("scripts/settings.php");

page_header_start();
page_header_end();
page_sidebar();
?>

<button onclick="exportExcel()" style="margin:10px;padding:8px 15px;">Download Excel</button>
<button onclick="exportPDF()" style="margin:10px;padding:8px 15px;">Download PDF</button>

<div id="reportContent" style="background:#fff;padding:10px;">
    <style>
        body {
            font-family: Arial;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 14px;
            text-align: center;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
        }

        h3 {
            text-align: center;
            margin: 15px 0;
        }

        .total {
            font-weight: bold;
            background: #fafafa;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        thead tr:first-child th {
            background: #d9e1f2;
            font-size: 15px;
        }
    </style>

    <?php

    echo "<h3>Land Area Correction Status Report</h3>";

    echo "<table>
<thead>

<tr>
<th rowspan='2'>S.No</th>
<th rowspan='2'>Division</th>
<th rowspan='2'>District</th>

<th colspan='3'>Marketing</th>
<th colspan='3'>Block Union</th>
<th colspan='3'>Jila Sahkari</th>
<th colspan='3'>Consumer</th>
</tr>

<tr>
<th>Total</th>
<th>Area > 0.5</th>
<th>Corrected</th>

<th>Total</th>
<th>Area > 0.5</th>
<th>Corrected</th>

<th>Total</th>
<th>Area > 0.5</th>
<th>Corrected</th>

<th>Total</th>
<th>Area > 0.5</th>
<th>Corrected</th>
</tr>

</thead>
<tbody>";

    $i = 1;

    $tot = ['m' => 0, 'ma' => 0, 'mc' => 0, 'b' => 0, 'ba' => 0, 'bc' => 0, 'j' => 0, 'ja' => 0, 'jc' => 0, 'c' => 0, 'ca' => 0, 'cc' => 0];

    $res = mysqli_query($db, "SELECT md.sno did,md.district_name,dv.division_name 
FROM master_district md 
LEFT JOIN master_division dv ON dv.sno=md.division_id 
WHERE md.sno!=28 
ORDER BY dv.division_name,md.district_name");

    while ($row = mysqli_fetch_assoc($res)) {

        $did = $row['did'];

        $m_total = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM marketing WHERE is_deleted!=1 AND district_id='$did'"))['c'];
        $m_area = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM marketing WHERE is_deleted!=1 AND district_id='$did' AND land_area+0 > 0.5"))['c'];
        $m_correct = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM marketing WHERE is_deleted!=1 AND district_id='$did' AND land_conf=1"))['c'];

        $b_total = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM block_union WHERE is_deleted!=1 AND janpad_name='$did'"))['c'];
        $b_area = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM block_union WHERE is_deleted!=1 AND janpad_name='$did' AND land_area+0 > 0.5"))['c'];
        $b_correct = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM block_union WHERE is_deleted!=1 AND janpad_name='$did' AND land_conf=1"))['c'];

        $j_total = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM jila_sehkari WHERE is_deleted!=1 AND janpad_name='$did'"))['c'];
        $j_area = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM jila_sehkari WHERE is_deleted!=1 AND janpad_name='$did' AND bhumi_area+0 > 0.5"))['c'];
        $j_correct = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM jila_sehkari WHERE is_deleted!=1 AND janpad_name='$did' AND land_conf=1"))['c'];

        $c_total = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM upss WHERE is_deleted!=1 AND janpad_name='$did'"))['c'];
        $c_area = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM upss WHERE is_deleted!=1 AND janpad_name='$did' AND bhumi_area+0 > 0.5"))['c'];
        $c_correct = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM upss WHERE is_deleted!=1 AND janpad_name='$did' AND land_conf=1"))['c'];

        echo "<tr>
    <td>$i</td>
    <td>{$row['division_name']}</td>
    <td>{$row['district_name']}</td>

    <td>$m_total</td>
    <td>$m_area</td>
    <td>$m_correct</td>

    <td>$b_total</td>
    <td>$b_area</td>
    <td>$b_correct</td>

    <td>$j_total</td>
    <td>$j_area</td>
    <td>$j_correct</td>

    <td>$c_total</td>
    <td>$c_area</td>
    <td>$c_correct</td>
    </tr>";

        $tot['m'] += $m_total;
        $tot['ma'] += $m_area;
        $tot['mc'] += $m_correct;

        $tot['b'] += $b_total;
        $tot['ba'] += $b_area;
        $tot['bc'] += $b_correct;

        $tot['j'] += $j_total;
        $tot['ja'] += $j_area;
        $tot['jc'] += $j_correct;

        $tot['c'] += $c_total;
        $tot['ca'] += $c_area;
        $tot['cc'] += $c_correct;

        $i++;
    }

    echo "</tbody>
<tfoot>
<tr class='total'>
<td colspan='3'>TOTAL</td>

<td>{$tot['m']}</td>
<td>{$tot['ma']}</td>
<td>{$tot['mc']}</td>

<td>{$tot['b']}</td>
<td>{$tot['ba']}</td>
<td>{$tot['bc']}</td>

<td>{$tot['j']}</td>
<td>{$tot['ja']}</td>
<td>{$tot['jc']}</td>

<td>{$tot['c']}</td>
<td>{$tot['ca']}</td>
<td>{$tot['cc']}</td>
</tr>
</tfoot>
</table>";
    ?>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
    function exportPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4');

        let title = "Land Area Correction Status Report";
        let date = "Date: " + new Date().toLocaleDateString();

        let originalTable = document.querySelector("#reportContent table");
        let tableClone = originalTable.cloneNode(true);

        let tfoot = tableClone.querySelector("tfoot");
        let tbody = tableClone.querySelector("tbody");

        if (tfoot && tbody) {
            let totalRow = tfoot.querySelector("tr");
            if (totalRow) tbody.appendChild(totalRow);
            tfoot.remove();
        }

        doc.autoTable({
            html: tableClone,
            startY: 20,
            theme: 'grid',
            styles: { fontSize: 8, cellPadding: 3, halign: 'center' },
            headStyles: { fillColor: [220, 220, 220], textColor: 0, fontStyle: 'bold' },
            alternateRowStyles: { fillColor: [245, 245, 245] },
            didDrawPage: function () {
                doc.setFontSize(14);
                doc.text(title, doc.internal.pageSize.getWidth() / 2, 10, { align: 'center' });
                doc.setFontSize(9);
                doc.text(date, doc.internal.pageSize.getWidth() - 10, 10, { align: 'right' });
            },
            margin: { top: 18 }
        });

        doc.save("Land_Correction_Report.pdf");
    }
</script>

<script>
    function exportExcel() {
        var table = document.querySelector("#reportContent table");
        var wb = XLSX.utils.table_to_book(table, { sheet: "Report" });
        XLSX.writeFile(wb, "Land_Correction_Report.xlsx");
    }
</script>

<?php
page_footer_start();
page_footer_end();
?>