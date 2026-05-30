<?php
session_start();
include(__DIR__ . "/../../scripts/settings.php");

header("Content-Type: application/json; charset=UTF-8");

global $db; // IMPORTANT (your connection variable)

// =======================
// VALIDATE ID
// =======================
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

$user_type = $_SESSION['user_type'] ?? '';
$action = $_POST['action'] ?? 'save';

if ($id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid ID"
    ]);
    exit;
}

$table = "ncd_cooperative_registrations";

// =======================
// ALLOWED FIELDS
// =======================
$allowedFields = [

    'cooperative_id',
    'cooperative_society_name',
    'local_langauge_society_name',
    'registration_authoritie_id',
    'reference_year',
    'date_registration',
    'registration_number',

    'cooperative_society_type_id',
    'area_of_operation_id',
    'water_body_type_id',
    'sector_of_operation_type',
    'sector_of_operation',
    'functional_status',
    'location_of_head_quarter',

    'state_code',
    'district_code',
    'block_code',
    'gram_panchayat_code',
    'village_code',
    'urban_local_body_type_code',
    'urban_local_body_code',
    'locality_ward_code',

    'pincode',
    'full_address',
    'address_line',

    'contact_person',
    'designation',
    'mobile',
    'landline',
    'email',

    'full_time_secretary',
    'mobile_number_of_secretary',
    'alternate_contact_no_for_pacs',
    'pacs_id',

    'is_approved',
    'is_coastal',
    'is_affiliated_union_federation',
    'financial_audit',
    'is_profit_making',
    'is_dividend_paid',

    'members_of_society',
    'audit_complete_year',
    'category_audit',

    'annual_turnover',
    'annual_profit',
    'annual_loss',
    'dividend_rate',

    'bank_type',
    'cooperative_society_bank_id',
    'other_bank',

    'pan_no',
    'gst_no',

    'how_many_branches'
];

// =======================
// BUILD SAFE UPDATE QUERY
// =======================
$set = [];

foreach ($allowedFields as $field) {
    if (isset($_POST[$field])) {

        $value = $_POST[$field];

        // ✅ HANDLE EMPTY VALUES
        if ($value === '' || $value === null) {
            $set[] = "$field = NULL";
        } else {

            // escape properly
            $value = mysqli_real_escape_string($db, $value);

            // numeric fields (no quotes)
            if (is_numeric($value)) {
                $set[] = "$field = $value";
            } else {
                $set[] = "$field = '$value'";
            }
        }
    }
}
if (empty($set)) {
    echo json_encode([
        "status" => "error",
        "message" => "No valid fields to update"
    ]);
    exit;
}

// =======================
// FINAL QUERY
// =======================
$sql = "UPDATE $table SET " . implode(", ", $set) . " WHERE id = $id";

// =======================
// EXECUTE
// =======================
$result = execute_query($sql);

if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => mysqli_error($db)
    ]);
    exit;
}

// =======================
// SUCCESS
// =======================

// CALL WORKFLOW
    updateValidationStatus(
        $db,
        $id,
        $user_type,
        $action,
        $_POST
    );

    echo json_encode([
        "status" => "success",
        "message" => "Data updated successfully"
    ]);




function updateValidationStatus($db, $cooperative_id, $user_type, $action, $post)
{
    $cooperative_id = intval($cooperative_id);

    // =========================
    // GET LATEST REQUEST
    // =========================
    $res = mysqli_query($db, "
        SELECT * 
        FROM ncd_cooperatives_validation
        WHERE ncd_cooperative_id = $cooperative_id
        ORDER BY request_id DESC
        LIMIT 1
    ");

    $row = mysqli_fetch_assoc($res);

    // =========================
    // FIRST ENTRY (SAFE INIT)
    // =========================
    if (!$row) {
        mysqli_query($db, "
            INSERT INTO ncd_cooperatives_validation (
                ncd_cooperative_id,
                request_id,
                maker_status,
                checker_status,
                admin_status,
                current_stage,
                final_status
            ) VALUES (
                $cooperative_id,
                1,
                0,
                0,
                0,
                'maker',
                'in_progress'
            )
        ");

        // reload
        $res = mysqli_query($db, "
            SELECT * 
            FROM ncd_cooperatives_validation
            WHERE ncd_cooperative_id = $cooperative_id
            ORDER BY request_id DESC
            LIMIT 1
        ");

        $row = mysqli_fetch_assoc($res);
    }

    $id = $row['id'];
    $current_request = (int)$row['request_id'];
    $next_request = $current_request + 1;

    // =========================
    // HELPER: PREVENT DUPLICATE VERSION
    // =========================
    $versionExists = function() use ($db, $cooperative_id, $next_request) {
        $check = mysqli_query($db, "
            SELECT id FROM ncd_cooperatives_validation
            WHERE ncd_cooperative_id = $cooperative_id
            AND request_id = $next_request
            LIMIT 1
        ");
        return mysqli_num_rows($check) > 0;
    };

    // =========================
    // MAKER
    // =========================
    if ($user_type === 'ncd_maker') {

        if ($action === 'save') {
            return; // no workflow change
        }

        if ($action === 'submit') {

            mysqli_query($db, "
                UPDATE ncd_cooperatives_validation
                SET maker_status = 1,
                    checker_status = 0,
                    admin_status = 0,
                    current_stage = 'checker',
                    final_status = 'in_progress',
                    updated_at = NOW()
                WHERE id = $id
            ");
        }
    }

    // =========================
    // CHECKER
    // =========================
    if ($user_type === 'ncd_checker') {

        // only if maker submitted
        if ((int)$row['maker_status'] !== 1) return;

        if ($action === 'verify') {

            mysqli_query($db, "
                UPDATE ncd_cooperatives_validation
                SET checker_status = 1,
                    current_stage = 'admin',
                    final_status = 'in_progress',
                    updated_at = NOW()
                WHERE id = $id
            ");
        }

        if ($action === 'checker_reject') {

            $remark = mysqli_real_escape_string($db, $post['checker_remark'] ?? '');

            mysqli_query($db, "
                UPDATE ncd_cooperatives_validation
                SET checker_status = 2,
                    checker_remark = '$remark',
                    final_status = 'rejected',
                    current_stage = 'completed',
                    updated_at = NOW()
                WHERE id = $id
            ");

            // NEW VERSION → BACK TO MAKER
            if (!$versionExists()) {
                mysqli_query($db, "
                    INSERT INTO ncd_cooperatives_validation (
                        ncd_cooperative_id,
                        request_id,
                        maker_status,
                        checker_status,
                        admin_status,
                        current_stage,
                        final_status
                    ) VALUES (
                        $cooperative_id,
                        $next_request,
                        0,
                        0,
                        0,
                        'maker',
                        'in_progress'
                    )
                ");
            }
        }
    }

    // =========================
    // ADMIN
    // =========================
    if ($user_type === 'ncd_admin') {

        // only if checker approved
        if ((int)$row['checker_status'] !== 1) return;

        if ($action === 'approve') {

            mysqli_query($db, "
                UPDATE ncd_cooperatives_validation
                SET admin_status = 1,
                    final_status = 'approved',
                    current_stage = 'completed',
                    updated_at = NOW()
                WHERE id = $id
            ");
        }

        if ($action === 'admin_reject') {

            $remark = mysqli_real_escape_string($db, $post['admin_remark'] ?? '');

            mysqli_query($db, "
                UPDATE ncd_cooperatives_validation
                SET admin_status = 2,
                    admin_remark = '$remark',
                    final_status = 'rejected',
                    current_stage = 'completed',
                    updated_at = NOW()
                WHERE id = $id
            ");

            //NEW VERSION → BACK TO CHECKER (FIXED)
            if (!$versionExists()) {
                mysqli_query($db, "
                    INSERT INTO ncd_cooperatives_validation (
                        ncd_cooperative_id,
                        request_id,
                        maker_status,
                        checker_status,
                        admin_status,
                        current_stage,
                        final_status
                    ) VALUES (
                        $cooperative_id,
                        $next_request,
                        1,  -- maker already done
                        0,
                        0,
                        'checker',
                        'in_progress'
                    )
                ");
            }
        }
    }
}



//function updateValidationStatus($db, $cooperative_id, $user_type, $action, $post)
//{
//    $cooperative_id = intval($cooperative_id);
//
//    // =========================
//    // GET LATEST REQUEST
//    // =========================
//    $sql = "SELECT *
//            FROM ncd_cooperatives_validation
//            WHERE ncd_cooperative_id = $cooperative_id
//            ORDER BY request_id DESC
//            LIMIT 1";
//
//    $res = mysqli_query($db, $sql);
//    $row = mysqli_fetch_assoc($res);
//
//    // =========================
//    // FIRST ENTRY
//    // =========================
//    if (!$row) {
//        mysqli_query($db, "INSERT INTO ncd_cooperatives_validation (
//            ncd_cooperative_id,
//            request_id,
//            maker_status,
//            checker_status,
//            admin_status,
//            current_stage,
//            final_status
//        ) VALUES (
//            $cooperative_id,
//            1,
//            0,
//            0,
//            0,
//            'maker',
//            'in_progress'
//        )");
////        return;
//
//
//        $res = mysqli_query($db, "
//        SELECT *
//        FROM ncd_cooperatives_validation
//        WHERE ncd_cooperative_id = $cooperative_id
//        ORDER BY request_id DESC
//        LIMIT 1
//    ");
//
//        $row = mysqli_fetch_assoc($res);
//
//    }
//
//    $id = $row['id'];
//    $current_request = $row['request_id'];
//    $next_request = $current_request + 1;
//
//    // =========================
//    // MAKER
//    // =========================
//    if ($user_type === 'ncd_maker') {
//
//        if ($action === 'save') {
//            return; // no workflow change
//        }
//
//        if ($action === 'submit') {
//
//            mysqli_query($db, "UPDATE ncd_cooperatives_validation
//                SET maker_status = 1,
//                    checker_status = 0,
//                    admin_status = 0,
//                    current_stage = 'checker',
//                    final_status = 'in_progress',
//                    updated_at = NOW()
//                WHERE id = $id");
//        }
//    }
//
//    // =========================
//    // CHECKER
//    // =========================
//    if ($user_type === 'ncd_checker') {
//
//        // ❗ allow only if maker submitted
//        if ($row['maker_status'] != 1) return;
//
//        if ($action === 'verify') {
//
//            mysqli_query($db, "UPDATE ncd_cooperatives_validation
//                SET checker_status = 1,
//                    current_stage = 'admin',
//                    final_status = 'in_progress',
//                    updated_at = NOW()
//                WHERE id = $id");
//        }
//
//        if ($action === 'checker_reject') {
//
//            $remark = mysqli_real_escape_string($db, $post['checker_remark'] ?? '');
//
//            mysqli_query($db, "UPDATE ncd_cooperatives_validation
//                SET checker_status = 2,
//                    checker_remark = '$remark',
//                    final_status = 'rejected',
//                    current_stage = 'completed',
//                    updated_at = NOW()
//                WHERE id = $id");
//
//            // NEW REQUEST
//            mysqli_query($db, "INSERT INTO ncd_cooperatives_validation (
//                ncd_cooperative_id,
//                request_id,
//                maker_status,
//                checker_status,
//                admin_status,
//                current_stage,
//                final_status
//            ) VALUES (
//                $cooperative_id,
//                $next_request,
//                0,
//                0,
//                0,
//                'maker',
//                'in_progress'
//            )");
//        }
//    }
//
//    // =========================
//    // ADMIN
//    // =========================
//    if ($user_type === 'ncd_admin') {
//
//        // ❗ allow only if checker approved
//        if ($row['checker_status'] != 1) return;
//
//        if ($action === 'approve') {
//
//            mysqli_query($db, "UPDATE ncd_cooperatives_validation
//                SET admin_status = 1,
//                    final_status = 'approved',
//                    current_stage = 'completed',
//                    updated_at = NOW()
//                WHERE id = $id");
//        }
//
//        if ($action === 'admin_reject') {
//
//            $remark = mysqli_real_escape_string($db, $post['admin_remark'] ?? '');
//
//            mysqli_query($db, "UPDATE ncd_cooperatives_validation
//                SET admin_status = 2,
//                    admin_remark = '$remark',
//                    final_status = 'rejected',
//                    current_stage = 'completed',
//                    updated_at = NOW()
//                WHERE id = $id");
//
//            // NEW REQUEST
//            mysqli_query($db, "INSERT INTO ncd_cooperatives_validation (
//                ncd_cooperative_id,
//                request_id,
//                maker_status,
//                checker_status,
//                admin_status,
//                current_stage,
//                final_status
//            ) VALUES (
//                $cooperative_id,
//                $next_request,
//                0,
//                0,
//                0,
//                'maker',
//                'in_progress'
//            )");
//        }
//    }
//}
