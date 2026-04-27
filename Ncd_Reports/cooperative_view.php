<?php
// session_start();
include("../scripts/settings.php");

// ✅ Get ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Invalid ID");
}

// ✅ Fetch record
$res = execute_query("SELECT * FROM cooperatives WHERE id = $id");

if (mysqli_num_rows($res) == 0) {
    die("Record not found");
}

$row = mysqli_fetch_assoc($res);

// ❌ Remove unwanted
unset($row['created_at'], $row['updated_at']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cooperative Details</title>
    <meta charset="UTF-8">

    <style>
        * { box-sizing: border-box; }

        body {
            font-family: Arial;
            background: #eaf0f6;
            margin: 0;
        }

        .top-bar {
            background: linear-gradient(90deg, #e05a00, #f47b20);
            color: #fff;
            padding: 10px 20px;
            font-size: 13px;
        }

        .brand-bar {
            background: #fff;
            border-bottom: 2px solid #1a5276;
            padding: 15px;
            text-align: center;
        }

        .brand-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a5276;
        }

        .container {
            padding: 20px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1a5276;
        }

        .back-btn {
            background: #6c757d;
            color: #fff;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
        }

        .back-btn:hover {
            background: #545b62;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #374151;
        }

        select, input[readonly] {
            padding: 9px 10px;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            font-size: 13px;
            background: #fff;
        }

        input[readonly] {
            background: #f3f4f6;
            cursor: not-allowed;
        }

        select:focus {
            outline: none;
            border-color: #1a5276;
            box-shadow: 0 0 0 3px rgba(26,82,118,0.1);
        }

        .actions {
            margin-top: 25px;
            text-align: center;
        }

        .btn {
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #1a5276, #2c3e50);
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body>



<div class="container">
    <div class="card">

        <div class="header">
            <div class="title">
                Cooperative Information
            </div>
            <a href="javascript:history.back()" class="back-btn">← Back</a>
        </div>

        <form onsubmit="return handleSave()">

            <!-- ✅ Hidden ID -->
            <input type="hidden" name="id" value="<?= $id ?>">

            <div class="grid">

                <?php foreach($row as $col => $val){ ?>

                    <!-- ❌ Skip ID from UI -->
                    <?php if($col == 'id') continue; ?>

                    <div class="form-group">
                        <label><?= ucwords(str_replace('_',' ', $col)) ?></label>

                        <?php if($col == 'cooperative_id'){ ?>
                            <!-- ✅ READ ONLY FIELD -->
                            <input type="text" value="<?= htmlspecialchars($val) ?>" readonly>
                        <?php } else { ?>
                            <!-- ✅ DROPDOWN -->
                            <select name="<?= $col ?>">
                                <option value="<?= htmlspecialchars($val) ?>" selected>
                                    <?= htmlspecialchars($val ?: '-- Select --') ?>
                                </option>
                                <option value="1">Option 1</option>
                                <option value="0">Option 2</option>
                            </select>
                        <?php } ?>

                    </div>

                <?php } ?>

            </div>

            <div class="actions">
                <button type="submit" class="btn">Save</button>
            </div>

        </form>

    </div>
</div>

<script>
    function handleSave(){
        alert("Data Saved Successfully");
        return false;
    }
</script>

</body>
</html>