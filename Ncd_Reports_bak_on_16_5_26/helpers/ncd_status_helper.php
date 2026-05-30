<?php
//function getValidationStatus($cooperative_id)
//{
//    $sql = "
//        SELECT *
//        FROM ncd_cooperatives_validation
//        WHERE ncd_cooperative_id = '$cooperative_id'
//        ORDER BY request_id DESC
//        LIMIT 2
//    ";
//
//    $res = execute_query($sql);
//
//    if (!$res || mysqli_num_rows($res) == 0) {
//        return formatStatus("Not Initiated");
//    }
//
//    $rows = [];
//    while ($r = mysqli_fetch_assoc($res)) {
//        $rows[] = $r;
//    }
//
//    $current = $rows[0];
//    $previous = $rows[1] ?? null;
//
//    $v = "v" . $current['request_id'];
//
//    //FINAL APPROVED
//    if ($current['final_status'] == 'approved') {
//        return formatStatus("Approved");
//    }
//
//    // ================================
//    // REJECTION FLOWS (IMPORTANT)
//    // ================================
//
//    // Checker rejected → back to Maker
//    if ($previous && $previous['checker_status'] == 2) {
//
//        if ($current['current_stage'] == 'maker') {
//            return formatStatus("Sent from Checker to Maker for Review");
//        }
//    }
//
//    // Admin rejected → back to Checker
//    if ($previous && $previous['admin_status'] == 2) {
//
//        if ($current['current_stage'] == 'checker') {
//            return formatStatus("Sent from Admin to Checker for Review");
//        }
//    }
//
//    // ================================
//    // NORMAL FLOW
//    // ================================
//
//    if ($current['current_stage'] == 'maker') {
//
//        // If fresh OR rework
//        if ($previous && ($previous['checker_status'] == 2 || $previous['admin_status'] == 2)) {
//            return formatStatus("Review in Progress at Maker");
//        }
//
//        return formatStatus("Pending at Maker");
//    }
//
//    if ($current['current_stage'] == 'checker') {
//        return formatStatus("Pending at Checker");
//    }
//
//    if ($current['current_stage'] == 'admin') {
//        return formatStatus("Pending at Admin");
//    }
//
//    // ================================
//    // FINAL REJECTED
//    // ================================
//    if ($current['final_status'] == 'rejected') {
//
//        if ($current['admin_status'] == 2) {
//            return formatStatus("Rejected by Admin");
//        }
//
//        if ($current['checker_status'] == 2) {
//            return formatStatus("Rejected by Checker");
//        }
//
//        return formatStatus("Rejected");
//    }
//
//    return formatStatus("In Progress");
//}

function getValidationStatus($cooperative_id)
{
    $cooperative_id = intval($cooperative_id);

    $sql = "
        SELECT *
        FROM ncd_cooperatives_validation
        WHERE ncd_cooperative_id = $cooperative_id
        ORDER BY request_id DESC
        LIMIT 2
    ";

    $res = execute_query($sql);

    if (!$res || mysqli_num_rows($res) == 0) {
        return formatStatus("Not Initiated");
    }

    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = $r;
    }

    $current  = $rows[0];
    $previous = $rows[1] ?? null;

    // =========================
    // FINAL APPROVED (TOP PRIORITY)
    // =========================
    if ($current['final_status'] === 'approved') {
        return formatStatus("Approved");
    }

    // =========================
    // 🔴 REJECTION HANDLING (IMPORTANT FIX)
    // =========================

    // Case 1: CURRENT itself is rejected (no new version yet)
    if ($current['final_status'] === 'rejected') {

        if ($current['admin_status'] == 2) {
            return formatStatus("Rejected by Admin");
        }

        if ($current['checker_status'] == 2) {
            return formatStatus("Rejected by Checker");
        }

        return formatStatus("Rejected");
    }

    // Case 2: Previous was rejected → new request created
    if ($previous && $previous['final_status'] === 'rejected') {

        // 🔁 Back to Maker after rejection
        if ($current['current_stage'] === 'maker') {
            return formatStatus("Sent Back to Maker for Correction");
        }

        // 🔁 Back to Checker (rare but safe)
        if ($current['current_stage'] === 'checker') {
            return formatStatus("Resubmitted → Pending at Checker");
        }
    }

    // =========================
    // 🟡 NORMAL FLOW
    // =========================

    if ($current['current_stage'] === 'maker') {
        return formatStatus("Pending at Maker");
    }

    if ($current['current_stage'] === 'checker') {
        return formatStatus("Pending at Checker");
    }

    if ($current['current_stage'] === 'admin') {
        return formatStatus("Pending at Admin");
    }

    // =========================
    // FALLBACK
    // =========================
    return formatStatus("In Progress");
}


function formatStatus($status)
{
    $color = "#777";

    if (stripos($status, "Pending") !== false) {
        $color = "#f39c12"; // orange
    }
    elseif (stripos($status, "Approved") !== false) {
        $color = "#27ae60"; // green
    }
    elseif (stripos($status, "Rejected") !== false) {
        $color = "#e74c3c"; // red
    }
    elseif (stripos($status, "Sent from") !== false || stripos($status, "Review") !== false) {
        $color = "#8e44ad"; // purple
    }

    return [
        "text" => $status,
        "color" => $color
    ];
}