<?php
include("scripts/settings.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
page_header_start();
page_header_end();
page_sidebar();
?>

<button onclick="downloadPDF()" style="margin:10px;padding:8px 15px;cursor:pointer;">Download PDF</button>

<div id="reportContent" style="background:#fff;padding:10px;">

<style>
body{font-family:Arial;}
table{border-collapse:collapse;width:100%;}
th,td{border:1px solid #000;padding:6px;font-size:12px;text-align:center;}
th{background:#f2f2f2;font-weight:bold;}
h3{text-align:center;margin:15px 0;}
.total{font-weight:bold;background:#fafafa;}

/* Repeat thead on every printed/PDF page */
thead{display:table-header-group;}
tfoot{display:table-footer-group;}

@media print {
    thead{display:table-header-group !important;}
    tr{page-break-inside:avoid;}
}
</style>

<?php

echo "<h3>Society Geo Location Status Report</h3>";

echo "<div class='table-responsive'>
<table class='table table-bordered table-striped'>
<thead>
<tr>
<th>S.No</th>
<th>Division</th>
<th>District</th>
<th>Marketing Total</th>
<th>Marketing Lat-Long Filled</th>
<th>Block Union Total</th>
<th>Block Union Lat-Long Filled</th>
<th>Jila Sahkari Total</th>
<th>Jila Sahkari Lat-Long Filled</th>
<th>Consumer Total</th>
<th>Consumer Lat-Long Filled</th>
</tr>
</thead>
<tbody>";

$i = 1;

$tot = ['m' => 0, 'mll' => 0, 'b' => 0, 'bll' => 0, 'j' => 0, 'jll' => 0, 'c' => 0, 'cll' => 0];

$res = mysqli_query($db, "SELECT md.sno did,md.district_name,dv.division_name FROM master_district md LEFT JOIN master_division dv ON dv.sno=md.division_id WHERE md.sno!=28 ORDER BY dv.division_name,md.district_name");

while ($row = mysqli_fetch_assoc($res)) {

    $did = $row['did'];

    $m_total = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM marketing WHERE is_deleted!=1 AND district_id='$did' and district_id != 28"))['c'];
    $m_ll = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM marketing WHERE is_deleted!=1 AND district_id='$did' and district_id != 28 AND latitude IS NOT NULL AND latitude!='' AND longitude IS NOT NULL AND longitude!=''"))['c'];

    $b_total = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM block_union WHERE is_deleted!=1 AND janpad_name='$did' and janpad_name != 28"))['c'];
    $b_ll = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM block_union WHERE is_deleted!=1 AND janpad_name='$did' and janpad_name != 28 AND latitude IS NOT NULL AND latitude!='' AND longitude IS NOT NULL AND longitude!=''"))['c'];

    $j_total = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM jila_sehkari WHERE is_deleted!=1 and society_status !='not_applicable' AND janpad_name='$did' and janpad_name != 28"))['c'];
    $j_ll = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM jila_sehkari WHERE is_deleted!=1 and society_status !='not_applicable' AND janpad_name='$did' and janpad_name != 28 AND latitude IS NOT NULL AND latitude!='' AND longitude IS NOT NULL AND longitude!=''"))['c'];

    $c_total = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM upss WHERE is_deleted!=1 AND janpad_name='$did' and janpad_name != 28"))['c'];
    $c_ll = mysqli_fetch_assoc(mysqli_query($db, "SELECT COUNT(*) c FROM upss WHERE is_deleted!=1 AND janpad_name='$did' and janpad_name != 28 AND latitude IS NOT NULL AND latitude!='' AND longitude IS NOT NULL AND longitude!=''"))['c'];

    echo "<tr>
<td>$i</td>
<td>{$row['division_name']}</td>
<td>{$row['district_name']}</td>
<td>$m_total</td>
<td>$m_ll</td>
<td>$b_total</td>
<td>$b_ll</td>
<td>$j_total</td>
<td>$j_ll</td>
<td>$c_total</td>
<td>$c_ll</td>
</tr>";

    $tot['m'] += $m_total;
    $tot['mll'] += $m_ll;
    $tot['b'] += $b_total;
    $tot['bll'] += $b_ll;
    $tot['j'] += $j_total;
    $tot['jll'] += $j_ll;
    $tot['c'] += $c_total;
    $tot['cll'] += $c_ll;

    $i++;
}

echo "</tbody>
<tfoot>
<tr class='total'>
<td colspan='3'>TOTAL</td>
<td>{$tot['m']}</td>
<td>{$tot['mll']}</td>
<td>{$tot['b']}</td>
<td>{$tot['bll']}</td>
<td>{$tot['j']}</td>
<td>{$tot['jll']}</td>
<td>{$tot['c']}</td>
<td>{$tot['cll']}</td>
</tr>
</tfoot>
</table></div>";
?>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
function downloadPDF() {
    var element = document.getElementById('reportContent');
    var opt = {
        margin: 5,
        filename: 'Society_Geo_Report.pdf',
        image: { type: 'jpeg', quality: 1 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
    };
    html2pdf().set(opt).from(element).save();
}
</script>

<?php
page_footer_start();
page_footer_end();
?>