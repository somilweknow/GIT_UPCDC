<?php
// date_default_timezone_set('Asia/Calcutta');
include("scripts/settings.php");
// error_reporting(E_ALL);
// print_r($_SESSION);
$apex_id = $_GET['apex_id'] ?? '';
if (!$apex_id) die("Invalid Apex ID");

$sql_apex = "SELECT apex_name FROM apex_1 WHERE sno = '$apex_id'";
$apex = mysqli_fetch_assoc(execute_query($sql_apex));

$res_posts = execute_query("SELECT sno, post_name FROM master_designation_apex_new ORDER BY post_name");

$selected_posts = [];
$res_selected = execute_query("SELECT post_id FROM survey_invoice_apex_designation WHERE apex_id = '$apex_id'");
while ($row = mysqli_fetch_assoc($res_selected)) {
    $selected_posts[] = (int)$row['post_id'];
}
page_header_start();
page_header_end();
page_sidebar();
?>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            background: #f4f6f9;
            font-family: Arial;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.08);
        }

        .main-heading {
            color: white;
            background: #4a90e2;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .sub-heading {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: bold;
            color: #1565c0;
            margin: 20px 0;
            border-left: 5px solid #1976d2;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 12px;
        }

        .item {
            background: #fff;
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #e0e0e0;
            transition: 0.2s;
        }

        .item:hover {
            background: #f1f8ff;
        }

        .item input {
            margin-right: 6px;
        }

        .new-post {
            background: #eef5ff;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .btn-primary {
            background: #003366;
            border: none;
        }

        .btn-success {
            background: #28a745;
        }

        .toast-msg {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 6px;
            color: #fff;
            z-index: 9999;
        }

    </style>
    <script>
        function addRow() {
            let div = document.createElement('div');
            div.className = "mt-2";

            div.innerHTML = `
                <input type="text" name="new_post_name[]" class="form-control d-inline-block" style="width:60%" placeholder="पद का नाम">
                <select name="technical[]" class="form-control d-inline-block" style="width:35%">
                    <option value="">Non Technical</option>
                    <option value="T">Technical</option>
                </select>
            `;

            document.getElementById('newPosts').appendChild(div);
        }

        function saveData() {

            let form = document.getElementById("mainForm");
            let formData = new FormData(form);

            formData.append("apex_id", "<?php echo $apex_id; ?>");
            formData.append("action", "save");

            fetch("scripts/apex_designation_save.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {

                    if (data.status === 'success') {
                        showToast(data.msg, 'green');
                    } else {
                        showToast(data.msg, 'red');
                    }

                })
                .catch(() => {
                    showToast("Server Error", "red");
                });
        }

        function showToast(msg, color) {

            let div = document.createElement("div");
            div.className = "toast-msg";
            div.style.background = color;
            div.innerText = msg;

            document.body.appendChild(div);

            setTimeout(() => div.remove(), 3000);
        }
    </script>

<div class="container mt-4">

    <!-- ✅ MAIN HEADER -->
    <div class="main-heading text-center">
        <?php echo $apex['apex_name']; ?>
    </div>

    <!-- ✅ SUB HEADING -->
    <div class="sub-heading">
        मानव सम्पदा हेतु पद का चयन करें
    </div>

    <form id="mainForm">

        <div class="card p-4">

            <!-- ✅ CHECKBOX GRID -->
            <div class="grid">
                <?php while ($row = mysqli_fetch_assoc($res_posts)) { ?>
                    <div class="item">
                        <label>
                            <input type="checkbox"
                                   name="posts[]"
                                   value="<?php echo $row['sno']; ?>"
                                    <?php echo in_array((int)$row['sno'], $selected_posts) ? 'checked' : ''; ?>>
                            <?php echo $row['post_name']; ?>
                        </label>
                    </div>
                <?php } ?>
            </div>

            <!-- ✅ ADD NEW POSTS -->
            <div class="new-post">
                <h5>➕ नया पद जोड़े</h5>

                <div id="newPosts">
                    <div>
                        <input type="text" name="new_post_name[]" class="form-control d-inline-block" style="width:60%"
                               placeholder="पद का नाम">
                        <select name="technical[]" class="form-control d-inline-block" style="width:35%">
                            <option value="">Non Technical</option>
                            <option value="T">Technical</option>
                        </select>
                    </div>
                </div>

                <button type="button" class="btn btn-success mt-3" onclick="addRow()">+ Add More</button>
            </div>

            <!-- ✅ SAVE BUTTON -->
            <div class="text-center">
                <button type="button" class="btn btn-primary mt-4 px-5" onclick="saveData()">Save</button>
            </div>

        </div>

    </form>

</div>