<?php
include("scripts/settings.php");
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

page_header_start();
page_header_end();
page_sidebar();

$sql = "SELECT  d.sno, d.district_name, (SELECT COUNT(*) FROM marketing m  WHERE m.district_id = d.sno AND m.is_deleted = 0) AS marketing_summary, (SELECT COUNT(*) FROM jila_sehkari j  WHERE j.janpad_name = d.sno AND j.is_deleted = 0 and j.status = 0) AS jila_summary, (SELECT COUNT(*) FROM block_union b  WHERE b.janpad_name = d.sno AND b.is_deleted = 0) AS block_summary, (SELECT COUNT(*) FROM upss u  WHERE u. janpad_name = d.sno AND u.is_deleted = 0) AS upss_summary FROM master_district d where d.sno != 28 ORDER BY d.district_name ASC ";

$result = execute_query($sql);
?>
<style>
    .card {
        background: #fff;
        border: 1px solid #e6eefc;
        padding: 18px;
        border-radius: 8px;
        margin: 20px 0;
    }

    .section-heading {
        background: #2b6fb3;
        color: #fff;
        padding: 12px;
        border-radius: 6px;
        font-weight: 800;
        font-size: 1.4em;
        text-align: center;
    }

    .summary-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
        min-width: 1000px;
    }

    .summary-table th,
    .summary-table td {
        border: 1px solid #e6edf7;
        padding: 8px 10px;
        font-size: 16px;
        text-align: left;
    }

    .summary-table thead th {
        background: #e8f5ff;
        font-weight: 800;
        color: #08386b;
        padding: 12px;
        text-align: center;
    }

    .district-row {
        background: #ffffff;
    }

    .table-wrap {
        overflow: auto;
    }

    #summary_table th,
    #summary_table td {
        text-align: center !important;
        vertical-align: middle !important;
    }
    #summary_table td:nth-child(2) {
        text-align: left !important;
    }
    #summary_table tfoot td {
        text-align: center !important;
    }
    #summary_table tfoot td:nth-child(2) {
        text-align: left !important;
    }

</style>

<div class="col-md-12">
    <h3 class="section-heading">LAND REPORT - SUMMARY</h3>

    <div class="table-wrap" style="margin-top:12px;">
        <table id="summary_table" class="summary-table">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>District</th>
                    <th>Marketing</th>
                    <th>DCDF</th>
                    <th>Consumer Forum</th>
                    <th>Block Union</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $i = 1;

                // Total accumulators
                $t_marketing = 0;
                $t_jila = 0;
                $t_upss = 0;
                $t_block = 0;
                $t_grand = 0;

                while ($row = mysqli_fetch_assoc($result)) {

                    // calculate row total
                    $total = $row['marketing_summary'] + $row['jila_summary'] + $row['block_summary'] + $row['upss_summary'];

                    // accumulate totals
                    $t_marketing += $row['marketing_summary'];
                    $t_jila += $row['jila_summary'];
                    $t_upss += $row['upss_summary'];
                    $t_block += $row['block_summary'];
                    $t_grand += $total;
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td style="text-align:left !important;"><?php echo $row['district_name']; ?></td>
                        <td><?php echo $row['marketing_summary']; ?></td>
                        <td><?php echo $row['jila_summary']; ?></td>
                        <td><?php echo $row['upss_summary']; ?></td>
                        <td><?php echo $row['block_summary']; ?></td>
                        <td><b><?php echo $total; ?></b></td>
                    </tr>
                <?php } ?>
            </tbody>

            <tfoot>
                <tr style="background:#e8f5ff; font-weight:bold;">
                    <td></td>
                    <td style="text-align:left !important;">TOTAL</td>
                    <td><?php echo $t_marketing; ?></td>
                    <td><?php echo $t_jila; ?></td>
                    <td><?php echo $t_upss; ?></td>
                    <td><?php echo $t_block; ?></td>
                    <td><?php echo $t_grand; ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        var t = $('#summary_table').DataTable({
            paging: true,
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            searching: true,
            info: true,
            deferRender: true,
            processing: true,
            scrollX: true,
            autoWidth: false,
            order: [], // important fix
            columnDefs: [{ searchable: false, orderable: false, targets: 0 }],
            dom: 'lfrtip'
        });

        // Auto S.No numbering
        t.on('draw', function () {
            var idx = 1;
            $('#summary_table tbody tr').each(function () {
                $(this).find('td:first').text(idx++);
            });
        }).draw();
    });

</script>

<?php
page_footer_start();
page_footer_end();
?>



<!-- <style>
@page {
  size: A4 portrait;
  margin: 10mm;
}
@media print {
  #print-area { display: block !important; position: absolute; left: 0; top: 0; width: 100%; }

  table.printable { width: 100% !important; border-collapse: collapse !important; font-size: 10px !important; }
  table.printable th, table.printable td {
    padding: 6px !important;
    border: 1px solid #e6edf7 !important;
    vertical-align: middle !important;
    word-break: break-word !important;
  }

  .no-print { display: none !important; }
}
@media print {
    table.printable th:nth-child(2),
    table.printable td:nth-child(2) {
        width:20% !important;
    }
}
</style> -->

<!-- <script>
$(document).ready(function () {
    var t = $('#summary_table').DataTable({
        paging: true,
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        searching: true,
        info: true,
        deferRender: true,
        processing: true,
        scrollX: true,
        autoWidth: false,
        order: [],
        columnDefs: [{ searchable: false, orderable: false, targets: 0 }],
        dom: 'Bflrtip',
        buttons: [
            {
                extend: 'print',
                text: 'Quick Print',
                title: '',
                exportOptions: { columns: ':visible' },

                customize: function (win) {
                    $(win.document.body).prepend('<h3 style="text-align:center;margin-bottom:8px;">LAND REPORT - SUMMARY</h3>');
                    $(win.document.body).find('table').addClass('printable');
                    var css = '@page{size:A4 portrait;margin:10mm;}'
                            + 'table.printable{width:100% !important;border-collapse:collapse !important;font-size:10px !important;}'
                            + 'table.printable th, table.printable td{padding:6px !important; border:1px solid #e6edf7 !important; vertical-align:middle !important; word-break:break-word !important;}'
                            + '.no-print{display:none !important;}';
                    $(win.document.head).append('<style>' + css + '</style>');
                    $(win.document.body).css('font-size', '10px');
                }
            },
            {
                text: 'Custom Print (New Window)',
                action: function (e, dt, node, config) {
                    var $clone = $('#summary_table').clone();
                    $clone.removeAttr('id').addClass('printable');
                    var html = '<!doctype html><html><head><meta charset="utf-8"><title>Print</title>';
                    html += '<style>';
                    html += '@page{size:A4 portrait;margin:10mm;}';
                    html += 'body{font-family:Arial, Helvetica, sans-serif; font-size:10px; margin:8mm;}';
                    html += 'h3{text-align:center;margin-bottom:6px;}';
                    html += 'table.printable{width:100%;border-collapse:collapse;font-size:10px;}';
                    html += 'table.printable th, table.printable td{padding:6px;border:1px solid #e6edf7;vertical-align:middle;word-break:break-word;}';
                    html += '</style></head><body>';
                    html += '<h3>LAND REPORT - SUMMARY</h3>';
                    html += $clone.prop('outerHTML');
                    html += '</body></html>';

                    var newWin = window.open('', '_blank', 'height=800,width=1200');
                    newWin.document.open();
                    newWin.document.write(html);
                    newWin.document.close();
                    setTimeout(function () {
                        newWin.focus();
                        newWin.print();
                    }, 500);
                }
            }
        ]
    });
    t.on('draw', function () {
        var info = t.page.info();
        var idx = info.start + 1;
        $('#summary_table tbody tr').each(function () {
            $(this).find('td:first').text(idx++);
        });
    }).draw();
});
</script> -->