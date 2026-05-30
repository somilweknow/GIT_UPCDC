<?php
include("scripts/settings.php");

$district_id = isset($_GET['district_id']) ? intval($_GET['district_id']) : 0;

$user_type = $_SESSION['user_type'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;

$ar_districts = [];
$ado_blocks = [];

if ($user_type == 'ar') {

    $res_ar = execute_query("SELECT district_id FROM ar_details WHERE ar_id='$user_id'");

    while ($row_ar = mysqli_fetch_assoc($res_ar)) {
        $ar_districts[] = $row_ar['district_id'];
    }

    if (count($ar_districts) > 0) {
        $district_id = $ar_districts[0];
    }
}

if ($user_type == 'ado') {

    $res_ado = execute_query("SELECT block_id FROM ado_details WHERE ado_id='$user_id'");

    while ($row_ado = mysqli_fetch_assoc($res_ado)) {
        $ado_blocks[] = $row_ado['block_id'];
    }

    if (count($ado_blocks) > 0) {

        $block_ids = implode(",", $ado_blocks);

        $district_row = mysqli_fetch_assoc(execute_query("SELECT col2 FROM test2 WHERE col6 IN ($block_ids) LIMIT 1"));

        $district_id = $district_row['col2'] ?? 0;
    }
}

page_header_start();
page_header_end();
page_sidebar();
?>
<style>

.card{
    border:none;
    border-radius:14px;
    box-shadow:0 4px 18px rgba(0,0,0,0.08);
    overflow:hidden;
    background:#fff;
}

.card-body{
    padding:25px;
}

h3{
    color:#0b4f8a;
    font-weight:700;
    text-align:center;
    margin-bottom:5px;
    letter-spacing:.5px;
}

h4{
    color:#333;
    font-weight:600;
    display:block;
    text-align:center;
    margin-bottom:
}
b{
    color:#333;
    font-weight:600;
    display:block;
    text-align:center;
    margin-bottom:5px;
}

.table{
    border-collapse:collapse;
    width:100%;
    margin-bottom:20px;
}

.table thead{
    background:linear-gradient(90deg,#0b4f8a,#1565c0);
    font-size:18px;
}

.table thead th{
    color:#fff;
    font-size:18px;
    font-weight:700;
    text-align:center;
    vertical-align:middle;
    padding:14px 12px;
    border:1px solid #d7e3f3;
    white-space:nowrap;
}
.table-warning th{
    font-size:18px !important;
    font-weight:700 !important;
    padding:14px 12px !important;
}

.table tbody td{
    padding:10px 8px;
    border:1px solid #e4eaf2;
    vertical-align:middle;
    font-size:16px;
}

.table tbody tr:nth-child(even){
    background:#f8fbff;
}

.table tbody tr:hover{
    background:#eef5ff;
    transition:.2s ease;
}

.form-control{
    border:1px solid #c8d4e3;
    border-radius:8px;
    height:40px;
    font-size:16px;
    padding:6px 12px;
    box-shadow:none;
    transition:.2s ease;
}

.form-control:focus{
    border-color:#1565c0;
    box-shadow:0 0 0 3px rgba(21,101,192,.15);
}

.form-control[readonly]{
    background:#f1f5f9 !important;
    color:#333;
    font-weight:600;
    cursor:not-allowed;
}

.btn{
    border-radius:8px;
    font-weight:600;
    padding:9px 22px;
    font-size:14px;
    transition:.2s ease;
    border:none;
}

.btn-success{
    background:linear-gradient(90deg,#198754,#28a745);
    color:#fff;
}

.btn-success:hover{
    transform:translateY(-1px);
    box-shadow:0 4px 12px rgba(25,135,84,.25);
}

.btn-secondary{
    background:linear-gradient(90deg,#6c757d,#5a6268);
    color:#fff;
}

.btn-secondary:hover{
    transform:translateY(-1px);
    box-shadow:0 4px 12px rgba(108,117,125,.25);
}

.text-center{
    margin-top:20px;
}

@media(max-width:768px){

    .card-body{
        padding:15px;
    }

    .table thead th,
    .table tbody td{
        font-size:12px;
        padding:8px 6px;
    }

    .form-control{
        height:36px;
        font-size:12px;
    }

    .btn{
        width:100%;
        margin-bottom:10px;
    }
}

</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <?php
                $d = mysqli_fetch_assoc(execute_query("SELECT district_name FROM master_district WHERE sno='$district_id'"));
                ?>
                <?php $r = mysqli_fetch_assoc(execute_query("SELECT block_name FROM master_block WHERE sno='$block_id'")); ?>
                <h4 class="text-center mb-3">
                    
                    <?php if($_SESSION['user_type']=='ar'){ ?>
                    <h3><u> मुख्यालय से प्राप्त कृषक पंजिका कि संख्या </u></h3><br>
                    <b><?= $d['district_name'] ?> जिले की कृषक पंजिका वितरण सूचना</b>
                    <?php } ?>
                    <?php if($_SESSION['user_type']=='ado'){ ?>
                    <b><?= $d['district_name'] ?> जिले के ब्लाक <?= $r['block_name'] ?> की कृषक पंजिका वितरण सूचना</b>
                    <?php } ?>
                </h4>
                <form id="register_form">
                    <input type="hidden" name="district_id" value="<?= $district_id ?>">
                    <table class="table table-striped table-bordered">
                        <thead class="table-warning">
                            <tr>
                                <th>क्र०स०</th>
                                <th>समिति</th>
                                <th>तहसील</th>
                                <th>ब्लॉक</th>
                                <?php if($user_type=='ar'){ ?>
                                    <th>वितरित कृषक पंजिका</th>
                                <?php }else{ ?>
                                    <th>प्राप्त कृषक पंजिका</th>
                                    <th>वितरित कृषक पंजिका</th>
                                <?php } ?>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $where = '';
                            if ($user_type == 'ar' && count($ar_districts) > 0) {
                                $district_ids = implode(",", $ar_districts);
                                $where .= " AND t.col2 IN ($district_ids)";
                            }
                            if ($user_type == 'ado' && count($ado_blocks) > 0) {
                                $block_ids = implode(",", $ado_blocks);
                                $where .= " AND t.col6 IN ($block_ids)";
                            }
                            $sql="SELECT t.sno AS pacs_id,t.col4 AS pacs_name,mt.tehseel_name,mb.block_name,pr.register_1,pr.register_2 FROM test2 t LEFT JOIN master_tehseel mt ON mt.sno=t.col5 LEFT JOIN master_block mb ON mb.sno=t.col6 LEFT JOIN pacs_register pr ON pr.pacs_id=t.sno AND pr.district_id=t.col2 WHERE (t.status='0' or t.status is null) $where ORDER BY mt.tehseel_name ASC,mb.block_name ASC,t.col4 ASC";

                            $res = execute_query($sql);
                            $i = 1;
                            while ($row = mysqli_fetch_assoc($res)) {
                                echo "<tr>
                                    <td>$i</td>
                                    <td>B-PACS {$row['pacs_name']}</td>
                                    <td>{$row['tehseel_name']}</td>
                                    <td>{$row['block_name']}</td>";
                                if ($user_type == 'ado') {
                                    echo "<td>
                                            <input type='text' class='form-control' value='" . ($row['register_1']) . "' readonly style='background:#f5f5f5;'>
                                        </td>";
                                } else {
                                    echo "<td>
                                            <input type='text' class='form-control' name='register_1[{$row['pacs_id']}]' value='" . ($row['register_1']) . "'>
                                        </td>";
                                }
                                if ($user_type != 'ar') {
                                    echo "<td>
                                            <input type='text' class='form-control' name='register_2[{$row['pacs_id']}]' value='" . ($row['register_2']) . "'>
                                        </td>";
                                }
                                echo "</tr>";

                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>

                    <div class="text-center">
                        <button class="btn btn-success">Submit</button>
                        <a href="javascript:history.back()" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('register_form').onsubmit = function (e) {

        e.preventDefault();

        fetch('scripts/ajax_pacs_register.php', {
            method: 'POST',
            body: new FormData(this)
        })
            .then(r => r.json())
            .then(d => {

                alert(d.msg);

                if (d.status == 'success') {
                    location.reload();
                }

            });

    };
</script>