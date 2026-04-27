<?php
include("scripts/settings.php");
$msg = '';
$response = 1;
$tab = 1;
error_reporting(E_ALL);

// Helper to safely get value from potential array or string
function get_val($source, $key, $index) {
    if (!isset($source[$key])) return '';
    $val = $source[$key];
    $decoded = json_decode($val, true);
    if (is_array($decoded)) {
        return $decoded[$index] ?? '';
    }
    return ($index === 0) ? $val : '';
}

$row_invoice = [
    'sno' => '',
    'apex_id' => '',
    'latitude' => '',
    'longitude' => '',
    'committee_status' => '',
    'email_id' => '',
    'photo_id' => '',
    'society_registration_no' => '',
    'society_registration_date' => '',
    'committee_date' => '',
    'pan_no' => '',
    'tan_no' => '',
    'gst_no' => '',
    'mobile_number' => '',
    'website' => '',
    'hq_ownership' => '',
    'sec_4_total_personnel' => ''
];
// echo '-------------------------------------', $_GET['exdid'];
if (isset($_GET['exdid'])) {
    $sql = 'SELECT apex_si_1_1.*, apex.* FROM `apex_si_1_1` LEFT JOIN apex ON apex.sno = apex_si_1_1.apex_id WHERE apex_si_1_1.apex_id="' . $_GET['exdid'] . '" ';
    $sql = 'SELECT apex_si_1_1.*, apex_si_1_1.`sno` as sno, apex_si_1_1.apex_id as `apex_id` FROM `apex_si_1_1` LEFT JOIN apex ON apex.sno = apex_si_1_1.apex_id WHERE apex_si_1_1.apex_id="' . $_GET['exdid'] . '" ';
    $result_invoice = execute_query($sql);
    if ($result_invoice && mysqli_num_rows($result_invoice) >= 1) {
        $row_invoice = mysqli_fetch_assoc($result_invoice);

        $_SESSION['survey_id'] = $row_invoice['sno'];
        $row_invoice['latitude'] = $row_invoice['latitude'] ?? '';
        $row_invoice['longitude'] = $row_invoice['longitude'] ?? '';
        $row_invoice['committee_status'] = $row_invoice['committee_status'] ?? '';
        $row_invoice['email_id'] = $row_invoice['email_id'] ?? '';
        $row_invoice['photo_id'] = $row_invoice['photo_id'] ?? '';
        $row_invoice['society_registration_no'] = $row_invoice['society_registration_no'] ?? '';
        $row_invoice['society_registration_date'] = $row_invoice['society_registration_date'] ?? '';
        $row_invoice['committee_date'] = $row_invoice['committee_date'] ?? '';
        $row_invoice['pan_no'] = $row_invoice['pan_no'] ?? '';
        $row_invoice['tan_no'] = $row_invoice['tan_no'] ?? '';
        $row_invoice['gst_no'] = $row_invoice['gst_no'] ?? '';
        $row_invoice['mobile_number'] = $row_invoice['mobile_number'] ?? '';
        $row_invoice['website'] = $row_invoice['website'] ?? '';
        $row_invoice['hq_ownership'] = $row_invoice['hq_ownership'] ?? '';
        $row_invoice['sec_4_total_personnel'] = $row_invoice['sec_4_total_personnel'] ?? '';
    }
    // echo $row_invoice['apex_id'], '-------------';

    $sql = 'SELECT * FROM survey_regional_offices WHERE survey_id="' . $row_invoice['sno'] . '"';
    $res_offices = execute_query($sql);

    $regional_offices = [];
    $i = 1;

    if (mysqli_num_rows($res_offices) > 0) {
        while ($row_office_temp = mysqli_fetch_assoc($res_offices)) {

            $regional_offices['office_name_' . $i] = $row_office_temp['office_name'];
            $regional_offices['district_' . $i]    = $row_office_temp['district'];
            $regional_offices['division_' . $i]    = $row_office_temp['division'];
            $regional_offices['tehsil_' . $i]      = $row_office_temp['tehsil'];
            $regional_offices['address_' . $i]     = $row_office_temp['address'];
            $regional_offices['phone_' . $i]       = $row_office_temp['phone'];
            $regional_offices['pincode_' . $i]     = $row_office_temp['pincode'];
            $regional_offices['email_' . $i]       = $row_office_temp['email'];

            $i++;
        }

        $regional_offices['count'] = $i - 1;

    } else {

        $regional_offices = [
            'count' => 1,
            'office_name_1' => '',
            'district_1' => '',
            'division_1' => '',
            'tehsil_1' => '',
            'address_1' => '',
            'phone_1' => '',
            'pincode_1' => '',
            'email_1' => '',
        ];
    }

    $sql = 'select * from survey_invoice_new_sec_6_2 where survey_id="' . $row_invoice['sno'] . '"';
    $res_6_2 = execute_query($sql);
    if (mysqli_num_rows($res_6_2) != 0) {
        $row_sec_6_2 = mysqli_fetch_assoc($res_6_2);
        $row_sec_6_2['sec_6_2_mgt_committee_is_elected'] = $row_sec_6_2['mgt_committee_is_elected'] ?? '';
        $row_sec_6_2['sec_6_2_election_year'] = $row_sec_6_2['election_year'] ?? '';
        $row_sec_6_2['sec_6_2_end_year'] = $row_sec_6_2['end_year'] ?? '';
        $row_sec_6_2['sec_6_2_mgt_committee_resolution_no'] = $row_sec_6_2['mgt_committee_resolution_no'] ?? '';
    } else {
        $row_sec_6_2['sec_6_2_mgt_committee_is_elected'] = '';
        $row_sec_6_2['sec_6_2_election_year'] = '';
        $row_sec_6_2['sec_6_2_end_year'] = '';
        $row_sec_6_2['sec_6_2_mgt_committee_resolution_no'] = '';
    }


    $sql = 'select * from survey_invoice_new_sec_6_2_1 where survey_id="' . $row_invoice['sno'] . '"';
    // echo $sql;
    $result_sec_6_2_1 = execute_query($sql);
    $row_6_2 = array();
    $d = 1;
    if (mysqli_num_rows($result_sec_6_2_1) > 0) {
        $row_6_2['count'] = mysqli_num_rows($result_sec_6_2_1);
        while ($row_section_6_2_1 = mysqli_fetch_assoc($result_sec_6_2_1)) {

            $row_6_2['sec_6_2_designation_' . $d] = $row_section_6_2_1['designation'];
            $row_6_2['sec_6_2_name_' . $d] = $row_section_6_2_1['full_name'];
            $row_6_2['sec_6_2_father_name_' . $d] = $row_section_6_2_1['father_name'];
            $row_6_2['sec_6_2__mob_no_' . $d] = $row_section_6_2_1['mobile_no'];

            $d++;
        }
    } else {
        $row_6_2['count'] = 1;
        $row_6_2['sec_6_2_designation_' . $d] = "";
        $row_6_2['sec_6_2_name_' . $d] = '';
        $row_6_2['sec_6_2_father_name_' . $d] = '';
        $row_6_2['sec_6_2__mob_no_' . $d] = '';
    }

    $sql = 'SELECT * FROM apex_si_7_2 WHERE survey_id="' . $row_invoice['sno'] . '" ORDER BY row_no ASC';
$res_emp = execute_query($sql);

$employees = [];

if (mysqli_num_rows($res_emp) > 0) {

    $i = 1;
    while ($row = mysqli_fetch_assoc($res_emp)) {

        $employees['post_' . $i]        = $row['post'];
        $employees['name_' . $i]        = $row['name'];
        $employees['father_name_' . $i] = $row['father_name'];
        $employees['phone_' . $i]       = $row['phone'];

        $i++;
    }

    $employees['count'] = $i - 1;

} else {

    $employees['count'] = 1;
    $employees['post_1']        = '';
    $employees['name_1']        = '';
    $employees['father_name_1'] = '';
    $employees['phone_1']       = '';
}

    $sql = 'SELECT * FROM apex_si_2_1 WHERE survey_id="' . $row_invoice['sno'] . '"';
    $res_vacant = execute_query($sql);

    $vacant_positions = [];
    if (mysqli_num_rows($res_vacant) != 0) {
        $i = 1;
        while ($row_vac = mysqli_fetch_assoc($res_vacant)) {
            $vacant_positions['post_' . $i] = $row_vac['post'];
            $vacant_positions['number_' . $i] = $row_vac['number'];
            $vacant_positions['vacant_' . $i] = $row_vac['vacant'];
            $vacant_positions['approved_' . $i] = $row_vac['approved'];
            $i++;
        }
        $vacant_positions['count'] = $i - 1;
    } else {
        $vacant_positions['count'] = 1;
        $vacant_positions['post_1'] = '';
        $vacant_positions['number_1'] = '';
        $vacant_positions['vacant_1'] = '';
        $vacant_positions['approved_1'] = '';
    }

    $sql = 'SELECT * FROM apex_si_district_positions WHERE survey_id="' . $row_invoice['sno'] . '"';
    $res_vp = execute_query($sql);

    if (mysqli_num_rows($res_vp) != 0) {
        $i = 1;
        while ($row = mysqli_fetch_assoc($res_vp)) {
            $vacant_positions['post_' . $i]     = $row['post_name'];
            $vacant_positions['number_' . $i]   = $row['number'];
            $vacant_positions['vacant_' . $i]   = $row['vacant'];
            $vacant_positions['approved_' . $i] = $row['approved'];
            $i++;
        }
        $vacant_positions['count'] = $i - 1;

    } else {
        $i = 1;
        $vacant_positions['count'] = 1;
        $vacant_positions['post_' . $i]     = '';
        $vacant_positions['number_' . $i]   = '';
        $vacant_positions['vacant_' . $i]   = '';
        $vacant_positions['approved_' . $i] = '';
    }

    $sql = 'SELECT * FROM apex_si_7_3 WHERE survey_id="' . $row_invoice['sno'] . '"';
    $res_purchase = execute_query($sql);

    if (mysqli_num_rows($res_purchase) != 0) {
        $i = 1;
        while ($row = mysqli_fetch_assoc($res_purchase)) {

            $purchase_sale['wheat_purchase_' . $i] = $row['wheat_purchase'];
            $purchase_sale['rice_purchase_' . $i] = $row['rice_purchase'];
            $purchase_sale['seed_' . $i] = $row['seed'];
            $purchase_sale['fertilizer_' . $i] = $row['fertilizer'];
            $purchase_sale['godown_rent_' . $i] = $row['godown_rent'];
            $purchase_sale['nefed_' . $i] = $row['nefed'];
            $purchase_sale['farmer_service_center_' . $i] = $row['farmer_service_center'];
            $purchase_sale['other_business_' . $i] = $row['other_business'];

            $i++;
        }
        $purchase_sale['count'] = $i - 1;
    } else {

        $purchase_sale['count'] = 1;
        $i = 1;

        $purchase_sale['wheat_purchase_' . $i] = '';
        $purchase_sale['rice_purchase_' . $i] = '';
        $purchase_sale['seed_' . $i] = '';
        $purchase_sale['fertilizer_' . $i] = '';
        $purchase_sale['godown_rent_' . $i] = '';
        $purchase_sale['nefed_' . $i] = '';
        $purchase_sale['farmer_service_center_' . $i] = '';
        $purchase_sale['other_business_' . $i] = '';
    }

    $sql = 'SELECT * FROM apex_si_7_4 WHERE survey_id="' . $row_invoice['sno'] . '"';
    $res_yearly = execute_query($sql);

    $yearly_rows = [];
    $i = 1;
    while ($row = mysqli_fetch_assoc($res_yearly)) {
        $yearly_rows[$i] = [
            'business_name' => $row['business_name'],
            'annual_target' => $row['annual_target'],
            'achievement'   => $row['achievement']
        ];
        $i++;
    }
    $count = $i - 1;
    if ($count == 0) {
        $count = 1;
        $yearly_rows[1] = [
            'business_name' => '',
            'annual_target' => '',
            'achievement' => ''
        ];
    }


    $stock_rows = [];

// Fetch existing rows from DB
$sql = 'SELECT * FROM apex_si_7_5 WHERE survey_id="' . $row_invoice['sno'] . '" ORDER BY row_no ASC';
$res = execute_query($sql);

if (mysqli_num_rows($res) > 0) {
    $i = 1;
    while ($row = mysqli_fetch_assoc($res)) {
        $stock_rows[$i] = $row;  // store by row_no
        $i++;
    }
    $stock_count = $i - 1; // total number of rows
} else {
    // default one empty row
    $stock_count = 1;
    $stock_rows[1] = [
        'item_name' => '',
        'closing_stock' => '',
        'book_value' => ''
    ];
}


    $sql = 'SELECT * FROM apex_si_7_6 WHERE survey_id="' . $row_invoice['sno'] . '" ORDER BY row_no ASC';
    $res = execute_query($sql);

    if (mysqli_num_rows($res) != 0) {
        $i = 1;
        while ($row = mysqli_fetch_assoc($res)) {
            $row_9_useless['item_' . $i]    = $row['item_name'];
            $row_9_useless['closing_' . $i] = $row['closing_stock'];
            $row_9_useless['book_' . $i]    = $row['book_value'];
            $i++;
        }
        $row_9_useless['count'] = $i - 1;

    } else {
        $row_9_useless['count'] = 1;
        $row_9_useless['item_1']    = '';
        $row_9_useless['closing_1'] = '';
        $row_9_useless['book_1']    = '';
    }


    $sql = 'SELECT * FROM apex_si_7_7 WHERE survey_id="' . $row_invoice['sno'] . '"';
    $res_pur = execute_query($sql);

    if (mysqli_num_rows($res_pur) != 0) {
        $i = 1;
        while ($row = mysqli_fetch_assoc($res_pur)) {
            $row_10_purchase['purchase_item_no_' . $i] = $row['item_no'];
            $row_10_purchase['purchase_item_desc_' . $i] = $row['item_desc'];
            $row_10_purchase['purchase_date_' . $i] = $row['purchase_date'];
            $row_10_purchase['purchase_value_' . $i] = $row['value'];
            $row_10_purchase['purchase_qty_' . $i] = $row['qty'];
            $i++;
        }
        $row_10_purchase['count'] = $i - 1;
    } else {
        $i = 1;
        $row_10_purchase['count'] = 1;
        $row_10_purchase['purchase_item_no_1'] = '';
        $row_10_purchase['purchase_item_desc_1'] = '';
        $row_10_purchase['purchase_date_1'] = '';
        $row_10_purchase['purchase_value_1'] = '';
        $row_10_purchase['purchase_qty_1'] = '';
    }

    $sql = 'select * from survey_invoice_sec_3_new_1 where survey_id = "' . $row_invoice['sno'] . '"';
    $res_3_new_1 = execute_query($sql);

    if (mysqli_num_rows($res_3_new_1) != 0) {
        $row_3_new_1 = mysqli_fetch_assoc($res_3_new_1);
        $row_3_new_1['sec_3_profit_loss_1'] = $row_3_new_1['profit_loss_1'];
        $row_3_new_1['sec_3_profit_loss_amount_1'] = $row_3_new_1['profit_loss_amount_1'];
        $row_3_new_1['sec_3_accumulated_1'] = $row_3_new_1['accumulated_1'];
        $row_3_new_1['sec_3_accumulated_amount_1'] = $row_3_new_1['accumulated_amount_1'];
        $row_3_new_1['sec_3_profit_loss_2'] = $row_3_new_1['profit_loss_2'];
        $row_3_new_1['sec_3_profit_loss_amount_2'] = $row_3_new_1['profit_loss_amount_2'];
        $row_3_new_1['sec_3_accumulated_2'] = $row_3_new_1['accumulated_2'];
        $row_3_new_1['sec_3_accumulated_amount_2'] = $row_3_new_1['accumulated_amount_2'];
        $row_3_new_1['sec_3_profit_loss_3'] = $row_3_new_1['profit_loss_3'];
        $row_3_new_1['sec_3_profit_loss_amount_3'] = $row_3_new_1['profit_loss_amount_3'];
        $row_3_new_1['sec_3_accumulated_3'] = $row_3_new_1['accumulated_3'];
        $row_3_new_1['sec_3_accumulated_amount_3'] = $row_3_new_1['accumulated_amount_3'];
        $row_3_new_1['sec_3_financial_audit_year'] = $row_3_new_1['financial_audit_year'];
        $row_3_new_1['sec_3_audit_grading'] = $row_3_new_1['audit_grading'];
        $row_3_new_1['sec_3_compliance_status'] = $row_3_new_1['compliance_status'];
        $row_3_new_1['sec_3_agm_year'] = $row_3_new_1['agm_year'];
        $row_3_new_1['sec_3_dividend_year'] = $row_3_new_1['dividend_year'];
        $row_3_new_1['sec_3_dividend_per'] = $row_3_new_1['dividend_per'];
        $row_3_new_1['sec_3_dividend_amt'] = $row_3_new_1['dividend_amt'];
        $row_3_new_1['sec_3_santulan_patra'] = $row_3_new_1['santulan_patra'];
    } else {
        $row_3_new_1 = [
            'sec_3_profit_loss_1' => '',
            'sec_3_profit_loss_amount_1' => '',
            'sec_3_accumulated_1' => '',
            'sec_3_accumulated_amount_1' => '',
            'sec_3_profit_loss_2' => '',
            'sec_3_profit_loss_amount_2' => '',
            'sec_3_accumulated_2' => '',
            'sec_3_accumulated_amount_2' => '',
            'sec_3_profit_loss_3' => '',
            'sec_3_profit_loss_amount_3' => '',
            'sec_3_accumulated_3' => '',
            'sec_3_accumulated_amount_3' => '',
            'sec_3_financial_audit_year' => '',
            'sec_3_audit_grading' => '',
            'sec_3_compliance_status' => '',
            'sec_3_agm_year' => '',
            'sec_3_dividend_year' => '',
            'sec_3_dividend_per' => '',
            'sec_3_dividend_amt' => '',
            'sec_3_santulan_patra' => ''
        ];
    }

    $sql = 'select * from survey_invoice_sec_2_1_2 where survey_id="' . $row_invoice['sno'] . '"';
    $res_2_1_2 = execute_query($sql);
    $i = 1;
    $a = 1;
    $other_msc = array();
    if (mysqli_num_rows($res_2_1_2) != 0) {
        $row_2_1_2['count'] = mysqli_num_rows($res_2_1_2);
        while ($row_temp = mysqli_fetch_assoc($res_2_1_2)) {
            if ($row_temp['other_description'] == 'msc') {
                $other_msc[$a] = $row_temp['other_amount'];
                $a++;
            } else {
                $row_2_1_2['sec_2_1_2_business_description_' . $i] = $row_temp['other_description'];
                $row_2_1_2['sec_2_1_2_value_' . $i] = $row_temp['other_amount'];
                $i++;
            }
        }
        $_POST['sec_1_1_2_msc_service'] = $other_msc;
        $row_2_1_2['count'] = $i - 1;
    } else {
        $row_2_1_2['count'] = 1;
        $row_2_1_2['sec_2_1_2_business_description_' . $i] = '';
        $row_2_1_2['sec_2_1_2_value_' . $i] = '';
    }

    $sql_human = "SELECT * FROM apex_si_1_3 WHERE survey_id = '" . $row_invoice['sno'] . "'";
    $result_human = execute_query($sql_human);

    $human_rows = [];
    if ($result_human && mysqli_num_rows($result_human) > 0) {
        while ($row_h = mysqli_fetch_assoc($result_human)) {
            $human_rows[] = [
                'post_id' => $row_h['post_id'],
                'sanctioned_post' => $row_h['sanctioned_post'],
                'vacant_post' => $row_h['vacant_post']
            ];
        }
    } else {
        $human_rows[] = [
            'post_id' => '',
            'sanctioned_post' => '',
            'vacant_post' => ''
        ];
    }

    $sql = 'select * from survey_invoice_plot_details where survey_id = "' . $row_invoice['sno'] . '"';
    $res_new_plot = execute_query($sql);
    if (mysqli_num_rows($res_new_plot) != 0) {
        $row_new_plot = mysqli_fetch_assoc($res_new_plot);
        $row_new_plot['sec_new_plot_area'] = $row_new_plot['plot_area'] ?? '';
        $row_new_plot['sec_new_plot_revenue_status'] = $row_new_plot['plot_revenue_status'] ?? '';
        $row_new_plot['sec_new_plot_reason_for_not_record'] = $row_new_plot['plot_reason_for_not_record'] ?? '';
        $row_new_plot['sec_new_plot_practices_if_not'] = $row_new_plot['plot_practices_if_not'] ?? '';
        $row_new_plot['sec_new_plot_gata_no'] = $row_new_plot['plot_gata_no'] ?? '';
        $row_new_plot['sec_3_ownership'] = $row_new_plot['sec_3_ownership'] ?? '';
        $row_new_plot['sec_3_building_area'] = $row_new_plot['society_building_area'] ?? '';
        $row_new_plot['sec_3_building_rent'] = $row_new_plot['society_building_rent_amount'] ?? '';
        $row_new_plot['sec_3_remark'] = $row_new_plot['society_building_remark'] ?? '';
        $row_new_plot['sec_new_remarks'] = $row_new_plot['remarks'] ?? '';
    } else {

        $row_new_plot['sec_new_plot_area'] = '';
        $row_new_plot['sec_new_plot_revenue_status'] = '';
        $row_new_plot['sec_new_plot_reason_for_not_record'] = '';
        $row_new_plot['sec_new_plot_practices_if_not'] = '';
        $row_new_plot['sec_new_plot_gata_no'] = '';
        $row_new_plot['sec_3_ownership'] = '';
        $row_new_plot['sec_3_building_area'] = '';
        $row_new_plot['sec_3_building_rent'] = '';
        $row_new_plot['sec_3_remark'] = '';
        $row_new_plot['sec_new_remarks'] = '';
    }

    $sql = 'SELECT * FROM survey_invoice_sec_3_1 WHERE survey_id="' . $row_invoice['sno'] . '"';
    $res_3_1 = execute_query($sql);

    if (mysqli_num_rows($res_3_1) > 0) {
        $row_3_1 = mysqli_fetch_assoc($res_3_1);
    } else {
        $row_3_1 = [
            'east_side' => '',
            'west_side' => '',
            'north_side' => '',
            'south_side' => '',
            'on_road_land' => '',
            'front_side' => '',
            'remarks' => '',
        ];
    }

    $sql = 'select * from survey_invoice_sec_3_4 where survey_id="' . $row_invoice['sno'] . '"';
    //echo $sql;
    $res_3_4 = execute_query($sql);
    if (mysqli_num_rows($res_3_4) != 0) {
        $i = 1;
        while ($row_3_4_temp = mysqli_fetch_assoc($res_3_4)) {
            $row_3_4['sec_3_b_storage_capacity_' . $i] = $row_3_4_temp['storage_capacity'];
            $row_3_4['sec_3_b_godown_year_' . $i] = $row_3_4_temp['godown_year'];
            $row_3_4['sec_3_b_wdra_certified_' . $i] = $row_3_4_temp['wdra_certified'];
            $row_3_4['sec_3_b_godown_type_of_fund_' . $i] = $row_3_4_temp['type_of_fund'];
            $row_3_4['sec_3_b_godown_status_' . $i] = $row_3_4_temp['construction_status'];
            $row_3_4['sec_3_b_godown_comment_' . $i] = $row_3_4_temp['remarks'];
            $i++;
        }
        $row_3_4['count'] = $i - 1;
    } else {
        $i = 1;
        $row_3_4['count'] = 1;
        $row_3_4['sec_3_b_storage_capacity_' . $i] = '';
        $row_3_4['sec_3_b_godown_year_' . $i] = '';
        $row_3_4['sec_3_b_wdra_certified_' . $i] = '';
        $row_3_4['sec_3_b_godown_type_of_fund_' . $i] = '';
        $row_3_4['sec_3_b_godown_status_' . $i] = '';
        $row_3_4['sec_3_b_godown_comment_' . $i] = '';
    }
    $sql = 'select * from survey_invoice_sec_3_5 where survey_id="' . $row_invoice['sno'] . '"';
    // $sql = 'SELECT *, 
    // 	 CONCAT("user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/", food_scheme) AS new_food_scheme FROM survey_invoice_sec_3_5 WHERE survey_id = "' . $row_invoice['sno'] . '"';
    $res_3_5_side = execute_query($sql);
    if (mysqli_num_rows($res_3_5_side) != 0) {
        $data_3_5 = array();
        $i = 1;
        while ($row_3_5_side = mysqli_fetch_assoc($res_3_5_side)) {
            $row_3_5['sec_3_c_length_' . $i] = $row_3_5_side['total_area'];
            $row_3_5['sec_3_c_vacant_land_status_' . $i] = $row_3_5_side['land_type'];
            $row_3_5['sec_3_c_land_location_' . $i] = $row_3_5_side['location'];
            $row_3_5['sec_3_c_suitable_godown_' . $i] = $row_3_5_side['suitable_godown'];
            $row_3_5['sec_3_c_rak_distance_' . $i] = $row_3_5_side['rak_distance'];
            $row_3_5['sec_3_c_approach_road_' . $i] = $row_3_5_side['approach_road'];
            $row_3_5['sec_3_c_paved_road_' . $i] = $row_3_5_side['approach_road'];
            $row_3_5['sec_3_c_other_' . $i] = ''; // Initialize generic other field
            $i++;
        }
        $row_3_5['sec_3_c_id'] = $i - 1;
    } else {
        $i = 1;
        $row_3_5['sec_3_c_id'] = 1;
        $row_3_5['sec_3_c_length_1'] = "";
        $row_3_5['sec_3_c_vacant_land_status_1'] = "";
        $row_3_5['sec_3_c_land_location_1'] = "";
        $row_3_5['sec_3_c_suitable_godown_1'] = "";
        $row_3_5['sec_3_c_rak_distance_1'] = "";
        $row_3_5['sec_3_c_approach_road_1'] = "";
        $row_3_5['sec_3_c_paved_road_1'] = "";
        $row_3_5['sec_3_c_other_1'] = ""; // Initialize generic other field
        $row_3_5['food_scheme' . $i] = "";
    }
    $sql = 'select * from survey_invoice_sec_2_1 where survey_id="' . $row_invoice['sno'] . '"';
    // $sql = 'select *, concat("user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/", approach_road_photo) as new_approach_road_photo  from survey_invoice_sec_2_1 where survey_id="' . $row_invoice['sno'] . '"';
    $res_2_1 = execute_query($sql);
    if (mysqli_num_rows($res_2_1) != 0) {
        $row_2_1 = mysqli_fetch_assoc($res_2_1);
        $row_2_1['sec_6_access_road'] = $row_2_1['sec_6_road'];
        $row_2_1['sec_6_paved_road'] = $row_2_1['approach_road'];
        $row_2_1['sec_6_2_truck_not_reach'] = $row_2_1['distance_from_approach_road'];
        $row_2_1['sec_7_electrical_connection'] = $row_2_1['electric_connection'];
        $row_2_1['sec_7_electrical_connection_working'] = $row_2_1['electric_connection_working'];
        $row_2_1['sec_7_if_yes'] = $row_2_1['electric_connection_proposal'];
        $row_2_1['sec_8_internet_connection'] = $row_2_1['internet_connectivity'];
        $row_2_1['sec_8_plot_frontage'] = $row_2_1['plot_frontage'];
    } else {
        $row_2_1['investment'] = '';
        $row_2_1['loan'] = '';
        $row_2_1['msp'] = '';
        $row_2_1['msp_comm'] = '';
        $row_2_1['subscribers'] = '';
        $row_2_1['pds'] = '';
        $row_2_1['total_business'] = '';
        $row_2_1['last_year_profit_loss'] = '';
        $row_2_1['last_year_pl_amount'] = '';
        $row_2_1['seq_year_profit_loss'] = '';
        $row_2_1['seq_year_pl_amount'] = '';
        $row_2_1['financial_audit_year'] = '';
        $row_2_1['approach_road_photo'] = '';

        $row_2_1['construction_status'] = '';
        $row_2_1['approach_road'] = '';
        $row_2_1['distance_from_approach_road'] = '';
        $row_2_1['electric_connection'] = '';
        $row_2_1['electric_connection_proposal'] = '';
        $row_2_1['internet_connectivity'] = '';
        $row_2_1['sec_6_access_road'] = '';
        $row_2_1['sec_6_2_truck_not_reach'] = '';
        $row_2_1['sec_7_electrical_connection'] = '';
        $row_2_1['sec_7_electrical_connection_working'] = '';
        $row_2_1['sec_7_if_yes'] = '';
        $row_2_1['sec_8_internet_connection'] = '';
        $row_2_1['sec_8_plot_frontage'] = '';
        $row_2_1['sec_6_paved_road'] = '';
    }
    $sql = 'select * from survey_invoice_new_sec_8 where survey_id="' . $row_invoice['sno'] . '"';
    // $sql = 'SELECT *, 
    // 	 CONCAT("user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/" , toilet_available_image) AS toilet_available_image_new, 
    // 	 CONCAT("user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/", toilet_available_women_image) AS toilet_available_women_image_new FROM survey_invoice_new_sec_8 WHERE survey_id = "' . $row_invoice['sno'] . '"';
    $res_8 = execute_query($sql);
    if (mysqli_num_rows($res_8) != 0) {
        $row_8 = mysqli_fetch_assoc($res_8);

        $row_8['sec_8_electrical_connection'] = $row_8['electrical_connection'];
        $row_8['sec_8_electrical_connection_working'] = $row_8['electrical_connection_working'];
        $row_8['sec_8_bill_paid_yes_no'] = $row_8['bill_paid_yes_no'];
        $row_8['sec_8_electricity_not_available_reason'] = $row_8['electricity_not_available_reason'];
        $row_8['sec_8_electricity_not_available_remark'] = $row_8['electricity_not_available_remark'];
        $row_8['sec_8_bill_not_paid_month'] = $row_8['bill_not_paid_month'];
        $row_8['sec_8_outstanding_amount'] = $row_8['outstanding_amount'];

        $row_8['sec_8_solar_connection'] = $row_8['solar_connection'];
        $row_8['sec_8_solar_work_status'] = $row_8['solar_work_status'];
        $row_8['sec_8_solar_bill_paid'] = $row_8['solar_bill_paid'];
        $row_8['sec_8_solar_rooftop'] = $row_8['roof_top'];
        $row_8['sec_8_solar_remark'] = $row_8['solar_remark'];
        $row_8['sec_8_solar_date'] = $row_8['solar_date'];
        $row_8['sec_8_solar_outstanding_amount'] = $row_8['solar_outstanding_amount'];

        $row_8['sec_8_internet_connection'] = $row_8['internet_connection'];
        $row_8['sec_8_internet_service_provider'] = $row_8['internet_service_provider'];
        $row_8['sec_8_internet_bill_paid'] = $row_8['internet_bill_paid'];
        $row_8['sec_8_select_internet_operator'] = $row_8['select_internet_operator'];
        // $row_8['internet_not_bill_paid_month'] = $row_8['internet_not_bill_paid_month'];
        $row_8['sec_8_internet_outstanding_amount'] = $row_8['internet_outstanding_amount'];

        $row_8['sec_8_narrow_tubes'] = $row_8['narrow_tubes'];
        $row_8['sec_8_water_tank'] = $row_8['water_tank'];
        $row_8['sec_8_samarsabel'] = $row_8['samarsabel'];
        $row_8['sec_8_handpump'] = $row_8['handpump'];
    } else {
        $row_8['sec_8_electrical_connection'] = '';
        $row_8['sec_8_electrical_connection_working'] = '';
        $row_8['sec_8_bill_paid_yes_no'] = '';
        $row_8['sec_8_electricity_not_available_reason'] = '';
        $row_8['sec_8_electricity_not_available_remark'] = '';
        $row_8['sec_8_bill_not_paid_month'] = '';
        $row_8['sec_8_outstanding_amount'] = '';

        $row_8['sec_8_solar_connection'] = '';
        $row_8['sec_8_solar_work_status'] = '';
        $row_8['sec_8_solar_bill_paid'] = '';
        $row_8['sec_8_solar_rooftop'] = '';
        $row_8['sec_8_solar_remark'] = '';
        $row_8['sec_8_solar_date'] = '';

        $row_8['sec_8_internet_connection'] = '';
        $row_8['sec_8_internet_service_provider'] = '';
        $row_8['sec_8_internet_bill_paid'] = '';
        $row_8['sec_8_select_internet_operator'] = '';
        $row_8['internet_not_bill_paid_month'] = '';

        $row_8['sec_8_narrow_tubes'] = '';
        $row_8['sec_8_water_tank'] = '';
        $row_8['sec_8_samarsabel'] = '';
        $row_8['sec_8_handpump'] = '';
    }
}

if (empty($row_invoice['apex_id']) && isset($_GET['exdid'])) {
    $row_invoice['apex_id'] = $_GET['exdid'];
}

?>

<?php
page_header_start();
?>
<link href="css/multistepform.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script src="js/survey_validate.js?v=1.4.0"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>


<style>
    .date-field {
        display: none;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .table-section {
        border-collapse: collapse;
    }

    .table-section th,
    .table-section td {
        border: 1px solid #000;
    }

    .table-section,
    .table-section th,
    .table-section td {
        border-color: transparent;
    }

    .table-section td:first-child {
        border-left-color: #000;
    }

    .table-section th:first-child {
        border-left-color: #000;
    }

    .table-section td:last-child {
        border-right-color: #000;
    }

    .table-section th:last-child {
        border-right-color: #000;
    }

    .table-section tr:first-child th {
        border-top-color: #000;
    }

    .table-section tr:last-child td {
        border-bottom-color: #000;
    }

    .form-section {
        margin-bottom: 0;
    }

    .form-section input {
        margin-bottom: 0;
    }

    .step h4 {
        color: white;
        background: #4a90e2;
        border-radius: 15px;
        padding: 10px 10px 6px 20px;
        font-weight: 700; /* Bold */
        font-size: 1.5rem; /* Increased size */
    }

    .step h5 {
        color: blue !important;
        background: #a4cbf8ff;
        border-radius: 15px;
        padding: 10px 10px 6px 20px;
        font-weight: 700; /* Bold */
        font-size: 1.25rem; /* Increased size */
    }

    .select-default {
        background-color: white;
    }

    .card label, .form-group label {
        font-size: 1.1rem; /* Increased size */
        font-weight: 700; /* Bold */
        color: #333;
    }
    .table thead {
        background-color: #b8daff !important;
        color: blue !important;
    }
</style>

<?php
page_header_end();
page_sidebar();
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="progress">
                            <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50"
                                class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                                role="progressbar" style="width: 0%">
                            </div>
                        </div>

                        <form action="scripts/ajax_pcf.php" method="post" enctype="multipart/form-data"
                            id="user_form" name="user_form">
                            <div id="steps-container">
                                <!-------------------1st start--------------------------------------------------------------------->
                                <div class="step">
                                    <?php echo $msg; ?>

                                    <h4><img src="images/logo/1.png" alt="text" class="img-fluid stat-icon"
                                            style="height:45px; width:45px;"> 1. समिति का विवरण </h4>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-sm-12 form-group">
                                                        <label>संस्था का प्रकार</label>
                                                        <div style="background-color: #f8f9fa; padding: 10px;">
                                                            <span style="color: #fd7e14; font-weight: bold; font-size: 1.1em; white-space: nowrap;">
                                                                शीर्ष सहकारी संस्था (APEX)
                                                            </span>
                                                        </div>
                                                        <input type="hidden" name="apex_type" value="शीर्ष सहकारी संस्था (APEX)">
                                                    </div>
                                                    <div class="col-sm-12 form-group" id="sec_1_institute_name_container">
                                                        <label>संस्था का नाम</label>
                                                        <div style="background-color: #f8f9fa; padding: 10px;">
                                                            <span style="color: #fd7e14; font-weight: bold; font-size: 1.1em; white-space: nowrap;">
                                                                यू०पी० कोआपरेटिव फेडरेशन लि० (पी०सी०एफ०)
                                                            </span>
                                                        </div>
                                                        <input type="hidden" name="apex_name" value="उत्तर प्रदेश राज्य निर्माण सहकारी संघ लि०, उ०प्र०">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-8">
                                                <input type="hidden" id="apex_code" name="apex_code"
                                                    value="<?php echo $row_invoice['apex_id']; ?>">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <label>Latitude</label>
                                                        <input type="text" id="lat" disabled="disabled"
                                                            value="<?php echo $row_invoice['latitude']; ?>"
                                                            class="form-control">
                                                        <label>Longitude</label>
                                                        <input type="text" id="long" disabled="disabled"
                                                            value="<?php echo $row_invoice['longitude']; ?>"
                                                            class="form-control">
                                                        <button type="button" class="btn btn-info"
                                                            onClick="getLocation(); this.innerHTML='Refreshing...'; setTimeout(()=>this.innerHTML='लोकेशन रिफ्रेश करें',1500);">
                                                            लोकेशन रिफ्रेश करें</button>
                                                        <!-- Place the text here right after the button -->
                                                        <div class="blinking-text">(लोकेशन मोबाईल से भरे)*</div>
                                                    </div>
                                                    <div class="col-md-10" id="map_container">
                                                        <iframe id="googlemap"
                                                            src="https://maps.google.com/maps?q=<?php echo $row_invoice['latitude'] . ',' . $row_invoice['longitude']; ?>&hl=en&z=13&amp;output=embed"
                                                            width="100%" height="100%"
                                                            style="border:1px solid; border-radius:10px;"
                                                            allowfullscreen="" loading="lazy"
                                                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr />

                                        <div class="row">
                                            <div class="col-sm-3 form-group" style="margin-block: -6px;">
                                                <label> संस्था का पंजीकरण संख्या</label>
                                                <input type="text" name="society_registration_no"
                                                    id="society_registration_no" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['society_registration_no']); ?>">
                                            </div>

                                            
                                            <div class="col-sm-3 form-group">
                                                <label> संस्था का पंजीकरण दिनांक</label>
                                                <input type="date" name="society_registration_date"
                                                    id="society_registration_date" tabindex="<?php echo $tab++; ?>"
                                                    class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['society_registration_date']); ?>">
                                            </div>

                                             <div class="col-sm-3 form-group">
                                                <label>पैन न०</label>
                                                <input type="text" name="pan_no" id="pan_no"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['pan_no'] ?? ''); ?>">
                                            </div>

                                            <div class="col-sm-3 form-group">
                                                <label>टैन न०</label>
                                                <input type="text" name="tan_no" id="tan_no"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['tan_no'] ?? ''); ?>">
                                            </div>
                                             <div class="col-sm-3 form-group">
                                                <label>जी०एस०टी०एन० न०</label>
                                                <input type="text" name="gst_no" id="gst_no"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['gst_no'] ?? ''); ?>">
                                            </div>
                                            <!-- <div class="col-sm-3 form-group">
                                                <label>क्या समिति सक्रिय है ?</label>
                                                <select class="form-control" id="committee_status"
                                                    name="committee_status" tabindex="<?php echo $tab++; ?>">
                                                    <option value="">--Select--</option>
                                                    <option value="yes" <?php if (($row_invoice['committee_status'] ?? '') == 'yes')
                                                        echo 'selected'; ?>>
                                                        हाँ</option>
                                                    <option value="no" <?php if (($row_invoice['committee_status'] ?? '') == 'no')
                                                        echo 'selected'; ?>>नहीं</option>
                                                </select>
                                            </div> -->

                                            <div class="col-sm-3 form-group">
                                                <label>ई-मेल आई.डी.</label>
                                                <input type="text" name="email_id" id="email_id"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['email_id'] ?? ''); ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>दूरभाष न०</label>
                                                <input type="text" name="mobile_number" id="mobile_number"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['mobile_number'] ?? ''); ?>">
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>वेबसाइट</label>
                                                <input type="text" name="website" id="website"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['website'] ?? ''); ?>">
                                            </div>
                                            <!-- <div class="col-sm-3 form-group">
                                                <label>पता (Address)</label>
                                                <input type="text" name="address" id="address"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                    value="<?php echo htmlspecialchars($row_invoice['address'] ?? ''); ?>">
                                            </div> -->
                                           
                                            <div class="col-sm-3 form-group">
                                                <label>मुख्यालय का स्वामित्व</label>
                                                <select name="hq_ownership" id="hq_ownership" class="form-control" tabindex="<?php echo $tab++; ?>">
                                                    <option value="">--Select--</option>
                                                    <option value="sway_ka" <?php if(($row_invoice['hq_ownership'] ?? '') == 'sway_ka') echo 'selected'; ?>>स्वयं का</option>
                                                    <option value="kiraye_pe" <?php if(($row_invoice['hq_ownership'] ?? '') == 'kiraye_pe') echo 'selected'; ?>>किराये का</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 form-group">
                                                <label>मुख्यालय की फोटो संलग्न करें</label>
                                                <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                    name="society_photo" id="society_photo"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control">

                                            </div>
                                            <?php
                                            if (!empty($row_invoice['photo_id']) && file_exists($row_invoice['photo_id'])) {
                                                ?>
                                                <div class="col-sm-2 form-group">
                                                    <img src="<?php echo $row_invoice['photo_id']; ?>"
                                                        class="img-fluid img-thumbnail" style="height:50px;"
                                                        id="society_photo_uploaded">
                                                    <label><a href="<?php echo $row_invoice['photo_id']; ?>"
                                                            target="_blank">संलग्न फोटो देखें</a></label>

                                                </div>
                                                <?php
                                            }
                                            ?>

                                            
                                        </div>
                                    </div>
                                    <h5>
                                            <img src="images/logo/7.png" alt="text" class="img-fluid stat-icon"
                                                style="height:50px; width:50px;">1.1शीर्ष संस्था के कार्यालय</h5>
                                            <br>

                                            <div class="card shadow-sm mb-3">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-3 form-group">
                                                            <label>जोन की संख्या</label>
                                                            <input type="text" name="no_of_zones" id="no_of_zones"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo htmlspecialchars($row_invoice['no_of_zones'] ?? ''); ?>"
                                                                oninput="updateOfficeRows(this.value)">
                                                        </div>
                                                        <div class="col-md-3 form-group">
                                                            <label>कार्यालय की संख्या</label>
                                                            <input type="text" id="global_prakhand_count" class="form-control"
                                                                oninput="updateSeparatePrakhandRows(this.value)">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="table-responsive" id="zoneTableWrapper" style="display:none;">
                                                <table class="table table-bordered" id="officeContainer" style="width: 100%; table-layout: fixed;">
                                                    <thead>
                                                        <tr class="office-block-header bg-light">
                                                            <th width="15%" style="color: black; font-weight: bold;">जोन का नाम</th>
                                                            <th width="15%" style="color: black; font-weight: bold;">जोन का दूरभाष न०</th>
                                                            <th width="20%" style="color: black; font-weight: bold;">जोन का ई-मेल आई.डी.</th>
                                                            <th width="20%" style="color: black; font-weight: bold;">जोन का पता</th>
                                                            <th width="100%" style="color: black; font-weight: bold;">जोन की फोटो संलग्न करें</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="office-block" data-zone-index="1" style="border-top: 2px solid #dee2e6;">
                                                        <tr>
                                                            <td style="padding: 5px;">
                                                                <input type="text" name="zone_name[]" class="form-control zone-name" placeholder="जोन का नाम">
                                                            </td>
                                                            <td style="padding: 5px;">
                                                                <input type="text" name="zone_mobile[]" class="form-control zone-mobile" placeholder="जोन का दूरभाष">
                                                            </td>
                                                            <td style="padding: 5px;">
                                                                <input type="text" name="zone_email[]" class="form-control zone-email" placeholder="जोन का ई-मेल">
                                                            </td>
                                                            <td style="padding: 5px;">
                                                                <input type="text" name="zone_address[]" class="form-control zone-address" placeholder="जोन का पता">
                                                            </td>
                                                            <td style="padding: 5px;">
                                                                <input type="file" name="zone_image[]" class="form-control">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="table-responsive" id="prakhandTableWrapper" style="display:none; margin-top: 15px;">
                                                <h6 class="text-primary mb-2">कार्यालय का विवरण</h6>
                                                <table class="table table-bordered" id="prakhandContainer" style="width: 100%; table-layout: fixed;">
                                                    <thead>
                                                        <tr class="bg-light">
                                                            <th width="15%" style="color: black; font-weight: bold;">कार्यालय का नाम</th>
                                                            <th width="15%" style="color: black; font-weight: bold;">कार्यालय का दूरभाष न०</th>
                                                            <th width="20%" style="color: black; font-weight: bold;">कार्यालय का ई-मेल आई.डी.</th>
                                                            <th width="20%" style="color: black; font-weight: bold;">कार्यालय का पता</th>
                                                            <th width="100%" style="color: black; font-weight: bold;">कार्यालय की फोटो</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="prakhand-main-tbody">
                                                        <tr class="prakhand-row-template">
                                                            <td><input type="text" name="prakhand_name[]" class="form-control" placeholder="कार्यालय का नाम"></td>
                                                            <td><input type="text" name="prakhand_mobile[]" class="form-control" placeholder="कार्यालय का दूरभाष"></td>
                                                            <td><input type="text" name="prakhand_email[]" class="form-control" placeholder="कार्यालय का ई-मेल"></td>
                                                            <td><input type="text" name="prakhand_address[]" class="form-control" placeholder="कार्यालय का पता"></td>
                                                            <td><input type="file" name="prakhand_image[]" class="form-control"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                </div>
                                <br>
                                <!----------------2.1 start-------------------------------------------------------->
                                <div class="step">
                                    <h4>
                                        <img src="images/logo/3.png" class="img-fluid stat-icon" style="height:50px; width:50px;">
                                        2. प्रदेश एवं प्रदेश के बाहर क्षेत्रीय/जिला कार्यालय का विवरण
                                    </h4>

                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="regional_offices_table">
                                                <thead style="background-color: #e9ecef !important;">
                                                    <tr style="color: black !important; font-weight: bold !important;">
                                                        <th style="color: black !important; font-weight: bold !important;">S.No.</th>
                                                        <th style="color: black !important; font-weight: bold !important;">क्षेत्रीय/जिला कार्यालय</th>
                                                        <th style="color: black !important; font-weight: bold !important;">मण्डल</th>
                                                        <th style="color: black !important; font-weight: bold !important;">तहसील</th>
                                                        <th style="color: black !important; font-weight: bold !important;">पता</th>
                                                        <th style="color: black !important; font-weight: bold !important;">दूरभाष नंबर</th>
                                                        <th style="color: black !important; font-weight: bold !important;">पिन कोड</th>
                                                        <th style="color: black !important; font-weight: bold !important;">ई-मेल</th>
                                                        <th style="color: black !important; font-weight: bold !important;">संस्था की फोटो</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="regional_offices_tbody">
                                                    <?php for ($i = 1; $i <= $regional_offices['count']; $i++) { ?>
                                                        <tr id="regional_office_row_<?php echo $i; ?>">
                                                            <td><?php echo $i; ?></td>
                                                            <td><input type="text" name="office_name_<?php echo $i; ?>" class="form-control" value="<?php echo $regional_offices['office_name_' . $i]; ?>"></td>
                                                            <td><input type="text" name="division_<?php echo $i; ?>" class="form-control" value="<?php echo $regional_offices['division_' . $i]; ?>"></td>
                                                            <td><input type="text" name="tehsil_<?php echo $i; ?>" class="form-control" value="<?php echo $regional_offices['tehsil_' . $i]; ?>"></td>
                                                            <td><input type="text" name="address_<?php echo $i; ?>" class="form-control" value="<?php echo $regional_offices['address_' . $i]; ?>"></td>
                                                            <td><input type="text" name="phone_<?php echo $i; ?>" class="form-control chk_mobile" data-maxlength="10" data-minlength="10" value="<?php echo $regional_offices['phone_' . $i]; ?>"></td>
                                                            <td><input type="text" name="pincode_<?php echo $i; ?>" class="form-control chk_number" data-maxlength="6" data-minlength="6" value="<?php echo $regional_offices['pincode_' . $i]; ?>"></td>
                                                            <td><input type="email" name="email_<?php echo $i; ?>" class="form-control" value="<?php echo $regional_offices['email_' . $i]; ?>"></td>
                                                            <td><input type="file" name="geo_image[]" class="form-control"></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-info" onclick="addRegionalOfficeRow()">नई पंक्ति जोड़ें [+]</button>
                                            <input type="hidden" id="regional_office_count" name="regional_office_count" value="<?php echo $regional_offices['count']; ?>">
                                        </div>
                                    </div>
                                    </div>
                                <div class="step">
                                    <h4><img src="images/logo/7.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 3. समिति की प्रबंध कमेटी</h4>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-md-3"><label for="">प्रबंध कमेटी निर्वाचित है?</label>
                                                <select name="sec_6_2_mgt_committee_is_elected"
                                                    id="sec_6_2_mgt_committee_is_elected" class="form-control"
                                                    onChange="hide_show(this.value, '#sec_6_2_election_year', 'yes'); hide_show(this.value, '#sec_6_2_end_year', 'yes'); hide_show(this.value, '#guard_count2', 'yes')">
                                                    <option value="">--Select--</option>
                                                    <option value="yes" <?php if ($row_sec_6_2['sec_6_2_mgt_committee_is_elected'] == 'yes') {
                                                        echo 'selected="selected"';
                                                    } ?>>निर्वाचित है</option>

                                                    <option value="no" <?php if ($row_sec_6_2['sec_6_2_mgt_committee_is_elected'] == 'no') {
                                                        echo 'selected="selected"';
                                                    } ?>>प्रशासनिक कमेटी</option>

                                                </select>
                                            </div>

                                            <div class="col-sm-3 form-group" id="sec_6_2_election_year"
                                                style="display:none;">
                                                <label>निर्वाचन का वर्ष</label>
                                                <select name="sec_6_2_election_year" id="sec_6_2_election_year"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control">
                                                    <option value="">--Select--</option>
                                                    <?php
                                                    for ($i = 2024; $i >= 1975; $i--) {
                                                        echo '<option value="' . $i . '" ';
                                                        if ($i == $row_sec_6_2['sec_6_2_election_year']) {
                                                            echo ' selected="selected" ';
                                                        }
                                                        echo ' >' . $i . '</option>';
                                                    }
                                                    ?>

                                                </select>
                                            </div>
                                            <div class="col-sm-5 form-group" id="sec_6_2_end_year"
                                                style="display:none;">
                                                <label style="white-space: nowrap;">निर्वाचित कमेटी की कार्यावधि पूर्ण होने का वर्ष</label>
                                                <select name="sec_6_2_end_year" id="sec_6_2_end_year"
                                                    tabindex="<?php echo $tab++; ?>" class="form-control">
                                                    <option value="">--Select--</option>
                                                    <?php
                                                    for ($i = 2024; $i <= 2030; $i++) {
                                                        echo '<option value="' . $i . '" ';
                                                        if ($i == $row_sec_6_2['sec_6_2_end_year']) {
                                                            echo ' selected="selected" ';
                                                        }
                                                        echo ' >' . $i . '</option>';
                                                    }
                                                    ?>

                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped" id="management_committee_table">
                                                    <thead style="background-color: #e9ecef !important;">
                                                        <tr style="color: black !important; font-weight: bold !important;">
                                                            <th style="color: black !important; font-weight: bold !important;">पदनाम</th>
                                                            <th style="color: black !important; font-weight: bold !important;">नाम</th>
                                                            <th style="color: black !important; font-weight: bold !important;">पिता / पति का नाम</th>
                                                            <th style="color: black !important; font-weight: bold !important;">मोबाईल नंबर</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="sec_6_2_tbody">
                                                        <?php
                                                        $row_6_2['count'] = 1;
                                                        for ($i = 1; $i <= $row_6_2['count']; $i++) {
                                                        ?>
                                                            <tr id="sec_6_2_row_<?php echo $i; ?>">
                                                                <td>
                                                                    <select class="form-control"
                                                                        id="sec_6_2_designation_<?php echo $i; ?>"
                                                                        name="sec_6_2_designation_<?php echo $i; ?>">
                                                                        <option value="">--Select--</option>
                                                                        <option value="अध्यक्ष" <?php echo $row_6_2['sec_6_2_designation_' . $i] == 'अध्यक्ष' ? 'selected="selected"' : ''; ?>>अध्यक्ष</option>
                                                                        <option value="उपाध्यक्ष" <?php echo $row_6_2['sec_6_2_designation_' . $i] == 'उपाध्यक्ष' ? 'selected="selected"' : ''; ?>>उपाध्यक्ष</option>
                                                                        <option value="संचालक" <?php echo $row_6_2['sec_6_2_designation_' . $i] == 'संचालक' ? 'selected="selected"' : ''; ?>>संचालक</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="sec_6_2_name_<?php echo $i; ?>"
                                                                        id="sec_6_2_name_<?php echo $i; ?>"
                                                                        class="form-control chk_text"
                                                                        data-type="4.II नाम शब्दों में भरे"
                                                                        value="<?php echo $row_6_2['sec_6_2_name_' . $i]; ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="sec_6_2_father_name_<?php echo $i; ?>"
                                                                        id="sec_6_2_father_name_<?php echo $i; ?>"
                                                                        class="form-control chk_text"
                                                                        data-type="4.II पिता का नाम शब्दों में भरे"
                                                                        value="<?php echo $row_6_2['sec_6_2_father_name_' . $i]; ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="sec_6_2__mob_no_<?php echo $i; ?>"
                                                                        id="sec_6_2__mob_no_<?php echo $i; ?>"
                                                                        class="form-control chk_mobile"
                                                                        data-minlength="10" data-maxlength="10"
                                                                        data-type="4.II 10 अंकों मे भरे"
                                                                        value="<?php echo $row_6_2['sec_6_2__mob_no_' . $i]; ?>">
                                                                </td>
                                                            </tr>
                                                        <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <button type="button" class="btn btn-info" onclick="sec_6_2_add_rows()">नई पंक्ति जोड़े [+]</button>
                                                <input type="hidden" name="sec_6_2_id" id="sec_6_2_id" value="<?php echo $row_6_2['count']; ?>">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="step">
                                    <h4>
                                        <img src="images/logo/5.png" class="img-fluid stat-icon" style="height:50px; width:50px;">
                                        4. संस्था में कार्यरत कुल अधिकारी / कार्मिकों का विवरण
                                    </h4>

                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-3 form-group">
                                                <label>कुल कर्मियों की सं०</label>
                                                <input type="text" name="sec_4_total_personnel" class="form-control chk_number"
                                                    value="<?php echo $row_invoice['sec_4_total_personnel'] ?? ''; ?>"
                                                    onkeyup="updateEmployeeRows(this.value)">
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="employees_table">
                                                <thead style="background-color: #e9ecef !important;">
                                                    <tr style="color: black !important; font-weight: bold !important;">
                                                        <th style="color: black !important; font-weight: bold !important;">क्रमांक</th>
                                                        <th style="color: black !important; font-weight: bold !important;">पद</th>
                                                        <th style="color: black !important; font-weight: bold !important;">नाम</th>
                                                        <th style="color: black !important; font-weight: bold !important;">पिता का नाम</th>
                                                        <th style="color: black !important; font-weight: bold !important;">दूरभाष नम्बर</th>
                                                    </tr>
                                                </thead>

                                                <tbody id="employees_tbody">

                                                <?php for ($i = 1; $i <= $employees['count']; $i++) { ?>
                                                    <tr id="employees_row_<?php echo $i; ?>">
                                                        <td><?php echo $i; ?></td>

                                                        <td><input type="text" name="employee_post_<?php echo $i; ?>" class="form-control"
                                                                value="<?php echo $employees['post_' . $i]; ?>"></td>

                                                        <td><input type="text" name="employee_name_<?php echo $i; ?>" class="form-control"
                                                                value="<?php echo $employees['name_' . $i]; ?>"></td>

                                                        <td><input type="text" name="employee_father_name_<?php echo $i; ?>" class="form-control"
                                                                value="<?php echo $employees['father_name_' . $i]; ?>"></td>

                                                        <td><input type="text" name="employee_phone_<?php echo $i; ?>" class="form-control"
                                                                value="<?php echo $employees['phone_' . $i]; ?>"></td>
                                                    </tr>
                                                <?php } ?>

                                                </tbody>
                                            </table>


                                            <input type="hidden" id="employees_row_count" name="employees_row_count"
                                                value="<?php echo $employees['count']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="step">
                                    <h4>
                                        <img src="images/logo/6.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">
                                        5. मानव सम्पदा 
                                    </h4>
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="district_address_table">
                                                <thead style="background-color: #ced4da;">
                                                    <tr>
                                                        <th style="color: black; font-weight: bold;">क्रमांक</th>
                                                        <th style="color: black; font-weight: bold;">पद</th>
                                                        <th style="color: black; font-weight: bold;">संख्या</th>
                                                        <th style="color: black; font-weight: bold;">रिक्त पद</th>
                                                        <th style="color: black; font-weight: bold;">स्वीकृत पद</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="district_address_tbody">
                                                    <?php
                                                    for ($i = 1; $i <= $vacant_positions['count']; $i++) { ?>
                                                        <tr id="district_address_row_<?php echo $i; ?>">
                                                            <td><?php echo $i; ?></td>
                                                            <td><input type="text" name="district_post_<?php echo $i; ?>" class="form-control"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    value="<?php echo $vacant_positions['post_' . $i]; ?>"></td>

                                                            <td><input type="text" name="district_number_<?php echo $i; ?>" class="form-control"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    value="<?php echo $vacant_positions['number_' . $i]; ?>"></td>

                                                            <td><input type="text" name="district_vacant_<?php echo $i; ?>" class="form-control"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    value="<?php echo $vacant_positions['vacant_' . $i]; ?>"></td>

                                                            <td><input type="text" name="district_approved_<?php echo $i; ?>" class="form-control"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    value="<?php echo $vacant_positions['approved_' . $i]; ?>"></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>

                                            <button type="button" class="btn btn-info" onclick="addDistrictAddressRow()">नई पंक्ति जोड़े [+]</button>
                                            <input type="hidden" id="district_address_row_count" name="district_address_row_count"
                                                value="<?php echo $vacant_positions['count']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="step">
                                    <h4>
                                        <img src="images/logo/7.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;">
                                        6. वर्षवार व्यवसाय विवरण 
                                    </h4>
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="purchase_sale_table">
                                                <thead style="background-color: #ced4da;">
                                                    <tr>
                                                        <th style="color: black; font-weight: bold;">क्रमांक</th>
                                                        <th style="color: black; font-weight: bold;">गेहूँ खरीद</th>
                                                        <th style="color: black; font-weight: bold;">धान खरीद</th>
                                                        <th style="color: black; font-weight: bold;">बीज</th>
                                                        <th style="color: black; font-weight: bold;">उर्वरक</th>
                                                        <th style="color: black; font-weight: bold;">गोदाम किराया</th>
                                                        <th style="color: black; font-weight: bold;">नेफेड (दलहन, तिलहन)</th>
                                                        <th style="color: black; font-weight: bold;">कृषक सेवा केंद्र</th>
                                                        <th style="color: black; font-weight: bold;">अन्य व्यवसाय</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="purchase_sale_tbody">
                                                    <?php
                                                    for ($i = 1; $i <= $purchase_sale['count']; $i++) { ?>
                                                        <tr id="purchase_sale_row_<?php echo $i; ?>">
                                                            <td><?php echo $i; ?></td>

                                                            <td><input type="text" name="wheat_purchase_<?php echo $i; ?>" class="form-control"
                                                                    value="<?php echo $purchase_sale['wheat_purchase_' . $i]; ?>"></td>

                                                            <td><input type="text" name="rice_purchase_<?php echo $i; ?>" class="form-control"
                                                                    value="<?php echo $purchase_sale['rice_purchase_' . $i]; ?>"></td>

                                                            <td><input type="text" name="seed_<?php echo $i; ?>" class="form-control"
                                                                    value="<?php echo $purchase_sale['seed_' . $i]; ?>"></td>

                                                            <td><input type="text" name="fertilizer_<?php echo $i; ?>" class="form-control"
                                                                    value="<?php echo $purchase_sale['fertilizer_' . $i]; ?>"></td>

                                                            <td><input type="text" name="godown_rent_<?php echo $i; ?>" class="form-control"
                                                                    value="<?php echo $purchase_sale['godown_rent_' . $i]; ?>"></td>

                                                            <td><input type="text" name="nefed_<?php echo $i; ?>" class="form-control"
                                                                    value="<?php echo $purchase_sale['nefed_' . $i]; ?>"></td>

                                                            <td><input type="text" name="farmer_service_center_<?php echo $i; ?>" class="form-control"
                                                                    value="<?php echo $purchase_sale['farmer_service_center_' . $i]; ?>"></td>

                                                            <td><input type="text" name="other_business_<?php echo $i; ?>" class="form-control"
                                                                    value="<?php echo $purchase_sale['other_business_' . $i]; ?>"></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>

                                            <button type="button" class="btn btn-info" onclick="addPurchaseSaleRow()">नई पंक्ति जोड़े [+]</button>

                                            <input type="hidden" id="purchase_sale_row_count" name="purchase_sale_row_count"
                                                value="<?php echo $purchase_sale['count']; ?>">
                                        </div>
                                    </div>
                                    <h4>
                                        <img src="images/logo/8.png" style="height:50px; width:50px;">
                                        7. कार्य एवं व्यवसाय
                                    </h4>

                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped" id="sec_7_yearly_business_table">
                                                <thead style="background-color: #ced4da;">
                                                    <tr>
                                                        <th style="width: 80px; color: black; font-weight: bold;">क्रमांक</th>
                                                        <th style="color: black; font-weight: bold;">व्यवसाय नाम</th>
                                                        <th style="color: black; font-weight: bold;">वार्षिक लक्ष्य</th>
                                                        <th style="color: black; font-weight: bold;">उपलब्धि</th>
                                                        <th style="min-width: 100px; color: black; font-weight: bold;">कार्रवाई</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    for ($i = 1; $i <= $count; $i++) { 
                                                        $y = $yearly_rows[$i];
                                                    ?>
                                                    <tr class="yearly_business_row">
                                                        <td><?php echo $i; ?></td>
                                                        <td>
                                                            <input type="text" name="sec_7_business_name_<?php echo $i; ?>" class="form-control" value="<?php echo $y['business_name'] ?? ''; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="sec_7_annual_target_<?php echo $i; ?>" class="form-control" value="<?php echo $y['annual_target'] ?? ''; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="sec_7_achievement_<?php echo $i; ?>" class="form-control" value="<?php echo $y['achievement'] ?? ''; ?>">
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($i == $count) { ?>
                                                                <button type="button" class="btn btn-info btn-sm" onclick="addYearlyBusinessRow()">+</button>
                                                                <input type="hidden" id="sec_7_row_count" name="sec_7_row_count" value="<?php echo $count; ?>">
                                                            <?php } else { ?>
                                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeYearlyBusinessRow(this)">-</button>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <script>
                                    function addYearlyBusinessRow() {
                                        var countInput = document.getElementById('sec_7_row_count');
                                        if (!countInput) return;

                                        var id = parseInt(countInput.value) + 1;
                                        countInput.value = id;
                                        
                                        var html = `
                                        <tr class="yearly_business_row">
                                            <td>${id}</td>
                                            <td><input type="text" name="sec_7_business_name_${id}" class="form-control"></td>
                                            <td><input type="text" name="sec_7_annual_target_${id}" class="form-control"></td>
                                            <td><input type="text" name="sec_7_achievement_${id}" class="form-control"></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeYearlyBusinessRow(this)">-</button>
                                            </td>
                                        </tr>
                                        `;
                                        var tbody = document.querySelector('#sec_7_yearly_business_table tbody');
                                        if (tbody) {
                                            tbody.insertAdjacentHTML('beforeend', html);
                                        }
                                    }

                                    function removeYearlyBusinessRow(btn) {
                                        var row = btn.closest('tr');
                                        if (row) {
                                            row.remove();
                                        }
                                    }
                                    </script>
                                
                                </div>
                                
                                


                                    <div class="step">
                                    <h4>
                                        <img src="images/logo/3.png" class="img-fluid stat-icon" style="height:50px;width:50px;">
                                        8. संस्था की वित्तीय सूचना
                                    </h4>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="financialMatrixTable" style="margin-bottom:0;">
                                            <thead style="background:#cce5ff">
                                                <tr>
                                                    <th style="width:15%; color:black;">वर्ष</th>
                                                    <th style="width:20%; color:black;">प्रकार</th>
                                                    <th style="width:20%; color:black;">स्थिति</th>
                                                    <th style="width:20%; color:black;">सकल लाभ/हानि धनराशि<br>(लाख में)</th>
                                                    <th style="width:100%; color:black;">शुद्ध लाभ/हानि धनराशि<br>(लाख में)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $startYear = 2022;
                                                $totalYears = 3;

                                                for ($i = 0; $i < $totalYears; $i++) {
                                                    $year = $startYear + $i;
                                                    $next = $year + 1;
                                                ?>
                                                    <tr>
                                                        <td rowspan="2" class="align-middle"><?php echo $year . '-' . substr($next, -2); ?></td>
                                                        <td>वार्षिक लाभ/हानि</td>
                                                        <td>
                                                            <select name="sec_3_profit_loss_<?php echo ($i+1); ?>" class="form-control" tabindex="<?php echo $tab++; ?>">
                                                                <option value="">--Select--</option>
                                                                <option value="profit" <?php echo ($row_3_new_1['sec_3_profit_loss_' . ($i+1)] == 'profit') ? 'selected' : ''; ?>>लाभ</option>
                                                                <option value="loss" <?php echo ($row_3_new_1['sec_3_profit_loss_' . ($i+1)] == 'loss') ? 'selected' : ''; ?>>हानि</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="sec_3_profit_loss_amount_<?php echo ($i+1); ?>"
                                                                   class="form-control chk_decimal" tabindex="<?php echo $tab++; ?>"
                                                                   value="<?php echo $row_3_new_1['sec_3_profit_loss_amount_' . ($i+1)] ?? ''; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="sec_3_accumulated_amount_<?php echo ($i+1); ?>"
                                                                   class="form-control chk_decimal" tabindex="<?php echo $tab++; ?>"
                                                                   value="<?php echo $row_3_new_1['sec_3_accumulated_amount_' . ($i+1)] ?? ''; ?>">
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>संचित लाभ/हानि</td>
                                                        <td>
                                                            <select name="sec_3_accumulated_<?php echo ($i+1); ?>" class="form-control" tabindex="<?php echo $tab++; ?>">
                                                                <option value="">--Select--</option>
                                                                <option value="profit" <?php echo ($row_3_new_1['sec_3_accumulated_' . ($i+1)] == 'profit') ? 'selected' : ''; ?>>लाभ</option>
                                                                <option value="loss" <?php echo ($row_3_new_1['sec_3_accumulated_' . ($i+1)] == 'loss') ? 'selected' : ''; ?>>हानि</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="sec_3_gross_accumulated_<?php echo ($i+1); ?>"
                                                                   class="form-control chk_decimal" tabindex="<?php echo $tab++; ?>"
                                                                   value="<?php echo $row_3_new_1['sec_3_profit_loss_amount_' . ($i+1)] ?? ''; ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="sec_3_net_accumulated_<?php echo ($i+1); ?>"
                                                                   class="form-control chk_decimal" tabindex="<?php echo $tab++; ?>"
                                                                   value="<?php echo $row_3_new_1['sec_3_accumulated_amount_' . ($i+1)] ?? ''; ?>">
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="text-end mt-3" style="margin-top:1rem !important;">
                                        <button type="button" class="btn btn-info btn-sm" id="addYearRowBtn">
                                            नई पंक्ति जोड़ें [+]
                                        </button>
                                    </div>

                                    <script>
                                        // वित्तीय सूचना: नई पंक्ति जोड़ें [+] (same behavior as visit_uprnss_1.php)
                                        // Adds next year (e.g., 2025-26) as 2 rows: वार्षिक + संचित
                                        (function () {
                                            let yearCount = 3; // Already 3 year blocks present (2022-23, 2023-24, 2024-25)

                                            const btn = document.getElementById("addYearRowBtn");
                                            const table = document.getElementById("financialMatrixTable");
                                            if (!btn || !table) return;

                                            btn.addEventListener("click", function () {
                                                yearCount++;

                                                const startYear = 2022 + (yearCount - 1);
                                                const endYear2 = (startYear + 1).toString().slice(-2);
                                                const yearLabel = startYear + "-" + endYear2;

                                                const tbody = table.querySelector("tbody");

                                                const html = `
                                                    <tr>
                                                        <td rowspan="2" class="align-middle">${yearLabel}</td>
                                                        <td>वार्षिक लाभ/हानि</td>
                                                        <td>
                                                            <select name="sec_3_profit_loss_${yearCount}" class="form-control">
                                                                <option value="">--Select--</option>
                                                                <option value="profit">लाभ</option>
                                                                <option value="loss">हानि</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="sec_3_profit_loss_amount_${yearCount}" class="form-control chk_decimal"></td>
                                                        <td><input type="text" name="sec_3_accumulated_amount_${yearCount}" class="form-control chk_decimal"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>संचित लाभ/हानि</td>
                                                        <td>
                                                            <select name="sec_3_accumulated_${yearCount}" class="form-control">
                                                                <option value="">--Select--</option>
                                                                <option value="profit">लाभ</option>
                                                                <option value="loss">हानि</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="sec_3_gross_accumulated_${yearCount}" class="form-control chk_decimal"></td>
                                                        <td><input type="text" name="sec_3_net_accumulated_${yearCount}" class="form-control chk_decimal"></td>
                                                    </tr>
                                                `;

                                                tbody.insertAdjacentHTML("beforeend", html);
                                            });
                                        })();
                                    </script>
                                </div>

                    
                                
                    <!----------9th start-------------------------------------------------------->

                    <div id="success" class="step">
                        <div class="mt-5 text-center">
                            <h4>प्रपत्र सफलता पूर्वक भरा गया</h4>
                            <p>आपने प्रपत्र सफलता पूर्वक भर लिया है । अब प्रपत्र को उच्चाधिकारी के पास
                                सत्यापन हेतु भेजा जाना है । कृप्या सत्यापन पर भेजने से पहले नीचे
                                दर्शायें
                                लिंक से फार्म खोल कर पुनः जाच कर लें । सब कुछ सही होने कि दशा में नीचे
                                दिये
                                बटन के माध्यम से सत्यापन के लिये आगे प्रेषित करें ।</p>
                            <button class="btn btn-info"
                                onclick="window.open('preview.php?exdid=<?php echo $_GET['exdid']; ?>', '_blank');">प्रपत्र
                                पुनः निरीक्षण के लिये देखे</button>
                        </div>
                        <div class="col-md-12 text-center">
                            <p><input type="checkbox" style="height: 20px; border:1px solid;" id="review_ack"
                                    onClick="if($('#review_ack').prop('checked') == true){$('#verification_button').prop('disabled', false);}else{$('#verification_button').prop('disabled', true);}">
                                मै एतत्द्वारा घोषित करता/करती हूं कि उपरोक्त प्रपत्र में भरी गयी सभी
                                सूचनायें मेरी जानकारी अनुसार सत्य एवम सही है । </p>
                            <button type="button" class="btn btn-danger" id="verification_button"
                                disabled="disabled">सत्यापन के लिये आगे प्रेषित
                                करें
                            </button>


                        </div>

                        <div class="col-sm-12 form-group my-auto" id="send_otp_button2" style="display: none">
                            <button type="button" name="verify_otp_btn" id="verify_otp_btn"
                                tabindex="<?php echo $tab++; ?>" class="btn btn-info"
                                onClick="verify_otp($('#survey_id').val());">आगे प्रेषित करे
                            </button>
                        </div>
                    </div>

                </div>

                <div id="q-box__buttons">
                    <button id="prev-btn" class="btn btn-info" type="button" onClick="save_draft()">Previous</button>
                    <button id="next-btn" class="btn btn-success" type="button" onClick="save_draft()">Next</button>
                    <button id="submit-btn" class="btn btn-danger" type="submit"
                        onClick="validate_input(); save_draft();">Submit</button>
                </div>
                <button class="btn btn-warning" type="button" onClick="save_draft()"><i class="fas fa-save"></i> Save
                    Draft</button>
                <input type="hidden" id="term" name="term" value="a">
                <input type="hidden" id="latitude" name="latitude" value="<?php echo $row_invoice['latitude']; ?>">
                <input type="hidden" id="longitude" name="longitude" value="<?php echo $row_invoice['longitude']; ?>">
                <input type="hidden" id="id" name="id" value="submit_form_pcf">
                <input type="hidden" id="current_step_count" name="current_step_count" value="">
                <input type="hidden" id="survey_id" name="survey_id" value="<?php echo $row_invoice['sno']; ?>">
                </form>
            </div>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" id="otp_form"
                name="otp_form"></form>
        </div>
    </div>
</div>
</div>
</div>

<div id="preloader-wrapper">
    <div id="preloader"></div>
    <div class="preloader-section section-left"></div>
    <div class="preloader-section section-right"></div>
</div>

<script>
    function save_draft() {
        var form = $("#user_form");
        var actionUrl = form.attr('action');
        $("#current_step_count").val(current_step);
        //console.log(form[0]);
        var formData = new FormData(form[0]);
        $.ajax({
            type: "POST",
            url: actionUrl,
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                data = JSON.parse(data);
                //data = data[0];
                //console.log(data);
                var err = 0;
                $.each(data, function (key, value) {
                    //console.log(value);
                    if (value.id == 'error') {
                        err = 1;
                        //alert(value.error);
                        $.notify({
                            icon: 'pe-7s-gift',
                            message: value.error

                        }, {
                            type: 'danger',
                            timer: 2000
                        });
                    }
                });
                if (err == 0) {
                    $.notify({
                        icon: 'pe-7s-gift',
                        message: 'Data Saved'

                    }, {
                        type: 'success',
                        timer: 2000
                    });
                }
            }
        });
    }

    function add_more_business() {
        var id = parseFloat($("#other_business_id").val());
        if (!id) {
            id = 0;
        }
        for (var i = 1; i <= id; i++) {
            if ($("#sec_2_1_2_business_description_" + i).val() == '' || $("#sec_2_1_2_value_" + i).val() == '') {
                alert("पंक्ति संख्या " + i + " खाली है");
                $("#sec_2_1_2_business_description_" + i).focus();
                return;
            }
        }
        id = id + 1;
        $(".add_business_row").hide();
        var txt = '<div class="row" id="business_row_' + id + '">' +
            '<div class="col-sm-4 form-group">' +
            '<label>व्यवसाय का विवरण </label>' +
            '<select name="sec_2_1_2_business_description_' + id + '" id="sec_2_1_2_business_description_' + id + '" class="form-control">' +
            '<option value="">--select--</option>' +
            '<option value="cattle_feed">कैटल फीड</option>' +
            '<option value="any_other">अन्य</option>' +
            '</select>' +
            '</div>' +
            '<div class="col-sm-4 form-group">' +
            '<label>वार्षिक टर्नोवर</label>' +
            '<input type="text" name="sec_2_1_2_value_' + id + '" id="sec_2_1_2_value_' + id + '" class="form-control chk_decimal" data-type=" 7.3.I वार्षिक टर्नोवर को धनराशि रु० लाख मे भरे">' +
            '</div>' +
            '<div class="col-sm-2 form-group my-auto add_business_row" id="add_business_row_' + id + '">' +
            '<button type="button" class="btn btn-info" onclick="add_more_business();">नईं पंक्ति जोड़े [+]</button>' +
            '<input type="hidden" name="other_business_id" id="other_business_id" value="' + id + '">' +
            '</div>' +
            '</div>';
        $("#other_business").append(txt);
        $("#other_business_id").val(id);
        $("#add_business_row_" + id).show();
    }

    function sec_3_c_add_rows() {
        var id = parseFloat($("#sec_3_c_id").val());
        if (!id) {
            id = 0;
        }
        for (var i = 0; i <= id; i++) {
            if ($("#sec_3_c_length_" + i).val() == '') {
                alert("पंक्ति संख्या " + i + " खाली है");
                $("#sec_3_c_length_" + i).focus();
                return;
            }
        }
        id = id + 1;
        $("#sec_3_c_rows").remove();

        var txt = '<div class="row"><div class="col-sm-2 form-group"><label>क्षेत्रफल (हेक्टेयर में)</label><input type="text" name="sec_3_c_length_' + id + '" id="sec_3_c_length_' + id + '" class="form-control chk_number" data-type="क्षेत्रफल हेक्टेयर में मे लिखे"></div><div class="col-sm-2 form-group"><label>भूमि की स्थिति (उपजाऊ /बंजर)</label><select name="sec_3_c_vacant_land_status_' + id + '" id="sec_3_c_vacant_land_status_' + id + '" class="form-control"><option value="">--select-- </option><option value="fertile">उपजाऊ </option><option value="barren">बंजर </option></select></div><div class="col-sm-2 form-group"><label>स्थान (समिति प्रांगण या अन्य स्थान)</label><select name="sec_3_c_land_location_' + id + '" id="sec_3_c_land_location_' + id + '" class="form-control"><option value="">--select-- </option><option value="inpremise">समिति प्रांगण </option><option value="other">अन्य स्थान </option></select></div><div class="col-sm-2 form-group"><label>गोदाम के लिए उपयुक्त है या नहीं ?</label><select class="form-control" type="checkbox" value="yes" id="sec_2_accountant" name="sec_3_c_suitable_godown_' + id + '" id="sec_3_c_suitable_godown_' + id + '"><option value="">--Select--</option><option value="yes">है</option><option value="no" style="background:#f00">नहीं</option></select></div><div class="col-sm-2 form-group"><label>जनपद से रैक पाइण्ट की दूरी</label><input type="text" name="sec_3_c_rak_distance_' + id + '" id="sec_3_c_rak_distance_' + id + '" class="form-control"></div><div class="col-sm-2 form-group" id="land_access_road_<?php echo $i; ?>"><label>पहुच मार्ग का प्रकार</label><select name="sec_3_c_paved_road_' + id + '" id="sec_3_c_paved_road_' + id + '" class="form-control"><option value="">--select--</option><option value="ordinary">कच्ची सड़क</option><option value="nh">नेशनल हाईवे</option><option value="sh">स्टेट हाईवे</option><option value="mdr">एम.डी.आर.</option><option value="odr">ओ.डी.आर.</option><option value="rural_road">ग्रामीण सड़क</option><option value="other">अन्य</option></select></div><div class="col-sm-2 form-group"><label>अन्य</label><input type="text" name="sec_3_c_other_' + id + '" id="sec_3_c_other_' + id + '" class="form-control"></div><div class="col-sm-2 form-group my-auto" id="sec_3_c_rows"><button type="button" class="btn btn-info" onClick="sec_3_c_add_rows();">नई पंक्ति जोड़े</button><input type="hidden" name="sec_3_c_id" id="sec_3_c_id" value="' + id + '"></div></div>';

        $("#sec_3_c").append(txt);
    }

    function sec_6_2_add_rows() {
        var id = parseFloat($("#sec_6_2_id").val());
        if (!id) {
            id = 0;
        }
        for (var i = 1; i <= id; i++) {
            if ($("#sec_6_2_name_" + i).val() == '' || $("#sec_6_2_father_name_" + i).val() == '' || $("#sec_6_2__mob_no_" + i).val() == '') {
                alert("पंक्ति संख्या " + i + " खाली है");
                $("#sec_6_2_name_" + i).focus();
                return;
            }
        }
        id = id + 1;
        
        var txt = '<tr id="sec_6_2_row_' + id + '">';
        txt += '<td><select class="form-control" id="sec_6_2_designation_' + id + '" name="sec_6_2_designation_' + id + '"><option value="">--Select--</option><option value="अध्यक्ष">अध्यक्ष</option><option value="उपाध्यक्ष">उपाध्यक्ष</option><option value="संचालक">संचालक</option></select></td>';
        txt += '<td><input type="text" name="sec_6_2_name_' + id + '" id="sec_6_2_name_' + id + '" class="form-control chk_text" data-type="नाम शब्दों में भरे"></td>';
        txt += '<td><input type="text" name="sec_6_2_father_name_' + id + '" id="sec_6_2_father_name_' + id + '" class="form-control chk_text" data-type="पिता का नाम शब्दों में भरे"></td>';
        txt += '<td><input type="text" name="sec_6_2__mob_no_' + id + '" id="sec_6_2__mob_no_' + id + '" class="form-control chk_mobile" data-minlength="10" data-maxlength="10" data-type="10 अंकों मे भरे"></td>';
        txt += '</tr>';

        $("#sec_6_2_tbody").append(txt);
        $("#sec_6_2_id").val(id);
    }
    let tabIndex = <?php echo $tab; ?>;

    function addRegionalOfficeRow() {

        var id = parseFloat($("#regional_office_count").val());
        if (!id) id = 1;

        // Validate existing rows
        for (var i = 1; i <= id; i++) {

            if ($("#office_name_" + i).val() == '' ||
                $("#district_" + i).val() == '' ||
                $("#division_" + i).val() == '' ||
                $("#tehsil_" + i).val() == '' ||
                $("#address_" + i).val() == '' ||
                $("#phone_" + i).val() == '' ||
                $("#pincode_" + i).val() == '' ||
                $("#email_" + i).val() == '') {

                alert("पंक्ति संख्या " + i + " पूरी भरें");
                $("#office_name_" + i).focus();
                return;
            }
        }

        id++;

        // Remove previous [+] button block
        $("#regional_office_btn").remove();

        var txt = '<div class="row mt-2" id="regional_office_row_' + id + '">';

        txt += '<div class="col-sm-2 form-group"><label>क्षेत्रीय/जिला कार्यालय</label>' +
            '<input type="text" name="office_name_' + id + '" id="office_name_' + id + '" class="form-control"></div>';

        txt += '<div class="col-sm-2 form-group"><label>जनपद</label>' +
            '<input type="text" name="district_' + id + '" id="district_' + id + '" class="form-control"></div>';

        txt += '<div class="col-sm-2 form-group"><label>मण्डल</label>' +
            '<input type="text" name="division_' + id + '" id="division_' + id + '" class="form-control"></div>';

        txt += '<div class="col-sm-2 form-group"><label>तहसील</label>' +
            '<input type="text" name="tehsil_' + id + '" id="tehsil_' + id + '" class="form-control"></div>';

        txt += '<div class="col-sm-2 form-group"><label>पता</label>' +
            '<input type="text" name="address_' + id + '" id="address_' + id + '" class="form-control"></div>';

        txt += '<div class="col-sm-2 form-group"><label>दूरभाष नंबर</label>' +
            '<input type="text" name="phone_' + id + '" id="phone_' + id + '" class="form-control"></div>';

        txt += '<div class="col-sm-2 form-group"><label>पिन कोड</label>' +
            '<input type="text" name="pincode_' + id + '" id="pincode_' + id + '" class="form-control"></div>';

        txt += '<div class="col-sm-2 form-group"><label>ई-मेल</label>' +
            '<input type="text" name="email_' + id + '" id="email_' + id + '" class="form-control"></div>';

        // Add new [+] button under last row
        txt += '<div class="col-sm-2 form-group my-auto" id="regional_office_btn">' +
            '<button type="button" class="btn btn-info" onclick="addRegionalOfficeRow()">नई पंक्ति [+]</button>' +
            '<input type="hidden" id="regional_office_count" name="regional_office_count" value="' + id + '">' +
            '</div>';

        txt += '</div>';

        $("#regional_offices_section").append(txt);
    }

    function addHumanRow() {
        let id = parseInt($("#human_resource_id").val());
        if (isNaN(id)) id = 0;

        // Validate existing rows before adding new one
        for (let i = 1; i <= id; i++) {
            let post = $("#post_id_" + i).val();
            let sanc = $("#sanctioned_post_" + i).val();
            let vac = $("#vacant_post_" + i).val();

            if (!post || !sanc || !vac) {
                alert("पंक्ति संख्या " + i + " खाली है। कृपया सभी फ़ील्ड भरें।");
                $("#post_id_" + i).focus();
                return;
            }
        }

        id += 1;
        $("#human_resource_id").val(id);

        // Generate HTML dynamically — no PHP echo inside JS string!
        let postOptions = $("#post_id_1").html(); // clone options from first dropdown

        let rowHTML = `
            <div class="row human_row mb-2" id="human_row_${id}">
                <div class="col-md-3 form-group">
                    <label>पद</label>
                    <select name="post_id[]" id="post_id_${id}" class="form-control">
                        ${postOptions}
                    </select>
                </div>

                <div class="col-md-3 form-group">
                    <label>स्वीकृत पद</label>
                    <input type="number" name="sanctioned_post[]" id="sanctioned_post_${id}" class="form-control">
                </div>

                <div class="col-md-3 form-group">
                    <label>रिक्त पद</label>
                    <input type="number" name="vacant_post[]" id="vacant_post_${id}" class="form-control">
                </div>

                <div class="col-md-2 form-group my-auto add_human_row" id="add_human_row_${id}">
                    <button type="button" class="btn btn-info" onclick="addHumanRow()">नई पंक्ति जोड़े [+]</button>
                </div>
            </div>
        `;

        $("#human_resource_rows").append(rowHTML);
    }
    function handleDropdownColorChange(selectElement, yesValue, yesColor, noValue, noColor) {
        if (selectElement.value === yesValue) {
            selectElement.style.backgroundColor = yesColor;
        } else if (selectElement.value === noValue) {
            selectElement.style.backgroundColor = noColor;
        } else {
            selectElement.style.backgroundColor = 'white'; // Default background color
        }
    }
</script>
<script>
    function hide_show(value, containerId, showValue) {
        var testServicesContainer = document.querySelector(containerId);
        if (!testServicesContainer) {
            console.warn('Element with ID "' + containerId + '" not found.');
            return; // Exit early if the element is not found
        }
        if (Array.isArray(showValue)) {
            if (showValue.includes(value)) {
                testServicesContainer.style.display = 'block';
            } else {
                testServicesContainer.style.display = 'none';
            }
        } else {
            if (value === showValue) {
                testServicesContainer.style.display = 'block';
            } else {
                testServicesContainer.style.display = 'none';
            }
        }
    }
</script>
<script language="javascript" type="text/javascript">
    function validate_input() {
        // var regexp_text = /^[\p{Letter}\u0900-\u097F ]+$/u;
        var regexp_text = /^[A-Za-z\u0900-\u097F,.\s]+$/;
        // var regexp_spltext = /^[\p{Letter}\u0900-\u097F -,./]+$/u;
        var regexp_spltext = /^[\p{Letter}\u0900-\u097F ,.\-!?]+$/u;
        var regexp_number = /^\d+$/;
        var regexp_decimal = /^-?\d+(\.\d+)?$/;
        // var regexp_email = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/;
        var regexp_email = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        var error_status = 0;
        var error_msg = '';

        // Validate Text Inputs (e.g., names, descriptions)
        $(".chk_text").each(function () {
            var value_text = $(this).val();
            if (value_text != "") {
                if (!regexp_text.test(value_text)) {
                    $(this).addClass("danger");
                    $(this).removeClass("success");
                    error_msg += $(this).data("type") + "\n"; // Error message for invalid input
                    error_status = 1;
                } else {
                    $(this).addClass("success");
                    $(this).removeClass("danger");
                }
            } else {
                $(this).removeClass("danger success");
            }
        });

        // Validate Special Text Inputs (e.g., addresses, descriptions with punctuation)
        $(".chk_spltext").each(function () {
            var value_text = $(this).val();
            if (value_text != "") {
                if (!regexp_spltext.test(value_text)) {
                    $(this).addClass("danger");
                    $(this).removeClass("success");
                    error_msg += $(this).data("type") + "\n";
                    error_status = 1;
                } else {
                    $(this).addClass("success");
                    $(this).removeClass("danger");
                }
            } else {
                $(this).removeClass("danger success");
            }
        });

        $(".chk_special_text").each(function () {
            var value_text = $(this).val();
            // Regex allows letters (Latin and Devanagari), comma, and full stop only
            var regexp_spltext = /^[A-Za-z\u0900-\u097F,.\s]+$/; // Allows letters, comma, full stop, and space

            if (value_text != "") {
                if (!regexp_spltext.test(value_text)) {
                    $(this).addClass("danger");
                    $(this).removeClass("success");
                    error_msg += $(this).data("type") + " (अवैध वर्ण)\n"; // Error message for invalid characters
                    error_status = 1;
                } else {
                    $(this).addClass("success");
                    $(this).removeClass("danger");
                }
            } else {
                $(this).removeClass("danger success");
            }
        });



        // Validate Number Inputs (with max 5 digits)
        $(".chk_number").each(function () {
            var value_number = $(this).val();
            var minlength = $(this).data("minlength"); // Minimum length if defined
            var maxlength = $(this).data("maxlength"); // Maximum length (set as data-maxlength="5")

            // Ensure the value is not empty and validate length
            if (value_number != "") {
                // Check for minimum length
                if (value_number.length < minlength) {
                    error_msg += $(this).data("type") + ". न्यूनतम " + minlength + " अंक भरें। \n";
                    $(this).addClass("danger");
                    $(this).removeClass("success");
                    error_status = 1;
                }
                // Check if the value exceeds the maximum limit (e.g., 5 digits max)
                else if (value_number.length > maxlength) {
                    error_msg += $(this).data("type") + " 5 अंकों से अधिक नहीं हो सकता। \n"; // Max 5 digits
                    $(this).addClass("danger");
                    $(this).removeClass("success");
                    error_status = 1;
                }
                // Check if the value is numeric (only digits allowed)
                else if (!regexp_number.test(value_number)) {
                    error_msg += $(this).data("type") + " केवल अंक भरें। \n"; // Only numbers allowed
                    $(this).addClass("danger");
                    $(this).removeClass("success");
                    error_status = 1;
                } else {
                    $(this).addClass("success");
                    $(this).removeClass("danger");
                }
            } else {
                $(this).removeClass("danger success");
            }
        });

        $(".chk_mobile").each(function () {
            $(this).on("blur", function () { // Trigger on blur or keyup, based on your preference
                var value_mobile = $(this).val().trim(); // Strip extra spaces from the input

                // Define the minimum and maximum length for mobile number (10 digits)
                var minlength = 10;
                var maxlength = 10;

                // Ensure the value is not empty and validate length
                if (value_mobile != "") {
                    // Check if the value is not exactly 10 digits
                    if (value_mobile.length < minlength || value_mobile.length > maxlength) {
                        error_msg += $(this).data("type") + " केवल 10 अंक भरें। \n"; // Should be exactly 10 digits
                        $(this).addClass("danger");
                        $(this).removeClass("success");
                        error_status = 1;
                    }
                    // Check if the value is numeric (only digits allowed)
                    else if (!/^\d{10}$/.test(value_mobile)) {
                        error_msg += $(this).data("type") + " केवल अंक भरें। \n"; // Only numbers allowed
                        $(this).addClass("danger");
                        $(this).removeClass("success");
                        error_status = 1;
                    } else {
                        $(this).addClass("success");
                        $(this).removeClass("danger");
                    }
                } else {
                    $(this).removeClass("danger success");
                }
            });
        });

        // Validate Decimal Inputs (for numbers with decimals, negative numbers allowed)
        $(".chk_decimal").each(function () {
            var value_decimal = $(this).val().trim();
            if (value_decimal != "") {
                if (!regexp_decimal.test(value_decimal)) {
                    $(this).addClass("danger");
                    $(this).removeClass("success");
                    error_msg += $(this).data("type") + "\n";
                    error_status = 1;
                } else {
                    $(this).addClass("success");
                    $(this).removeClass("danger");
                }
            } else {
                $(this).removeClass("danger success");
            }
        });

        // Validate Email Inputs
        $(".chk_email").each(function () {
            var value_email = $(this).val();
            if (value_email != "") {
                if (!regexp_email.test(value_email)) {
                    $(this).addClass("danger");
                    $(this).removeClass("success");
                    error_msg += $(this).data("type") + ". \n";
                    error_status = 1;
                } else {
                    $(this).addClass("success");
                    $(this).removeClass("danger");
                }
            } else {
                $(this).removeClass("danger success");
            }
        });

        // Final check: Set the error status
        $("#error_status").val(error_status);

        // Show error messages if there were validation issues
        if (error_msg != "") {
            alert(error_msg);
        }

        return error_status === 0; // Return false to prevent form submission if there are errors
    }
</script>

<script>
    function sec_2_nirmit_godown_add_rows() {
        var id = parseFloat($("#sec_2_nirmit_godown_id").val());
        if (!id) {
            id = 0;
        }
        for (var i = 1; i <= id; i++) {
            if ($("#sec_3_b_storage_capacity_" + i).val() == '' || $("#sec_3_b_godown_year_" + i).val() == '' || $("#sec_3_b_wdra_certified_" + i).val() == '') {
                alert("पंक्ति संख्या " + i + " खाली है");
                $("#sec_3_b_storage_capacity_" + i).focus();
                return;
            }
        }
        id = id + 1;
        $("#sec_2_nirmit_godown_rows").remove();
        var txt = '<div class="row">';
        txt += '<div class="col-sm-2 form-group"><label>क्षमता (मेट्रिक टन में)</label><input type="text" name="sec_3_b_storage_capacity_' + id + '" id="sec_3_b_storage_capacity_' + id + '" class="form-control chk_number" data-type="क्षमता मेट्रिक टन में भरे"></div>';
        txt += '<div class="col-sm-2 form-group"><label>निर्माण का वर्ष</label><select name="sec_3_b_godown_year_' + id + '" id="sec_3_b_godown_year_' + id + '" class="form-control"><option value="">--Select--</option><option value="1999">2000 से पूर्व</option>';
        for (var a = 2000; a <= 2024; a++) {
            txt += '<option value="' + a + '">' + a + '</option>';
        }
        txt += '</select></div>';

        txt += '<div class="col-sm-2 form-group"><label>WDRA से प्रमाणित है?</label><select name="sec_3_b_wdra_certified_' + id + '" id="sec_3_b_wdra_certified_' + id + '" class="form-control"><option value="">--select--</option><option value="yes">हाँ</option><option value="no">नहीं</option></select></div>';
        txt += '<div class="col-sm-2 form-group"><label>किस योजना के तहत बना है</label><input type="text" name="sec_3_b_godown_type_of_fund_' + id + '" id="sec_3_b_godown_type_of_fund_' + id + '" class="form-control chk_text" data-type="योजना का नाम शब्दों में भरे"></div>';
        txt += '<div class="col-sm-2 form-group"><label>गोदाम की भौतिक स्थिति</label><select name="sec_3_b_godown_status_' + id + '" id="sec_3_b_godown_status_' + id + '" class="form-control"><option value="">--select--</option><option value="good">अच्छा</option><option value="repairable">खराब/मरम्मत योग्य</option><option value="discarded">जर्जर/निष्प्रयोज्य्य</option></select></div>';
        txt += '<div class="col-sm-1 form-group"><label>टिप्पणी</label><input type="text" name="sec_3_b_godown_comment_' + id + '" id="sec_3_b_godown_comment_' + id + '" class="form-control"></div>';
        txt += '<div class="col-sm-1 form-group my-auto" id="sec_2_nirmit_godown_rows"><button type="button" class="btn btn-info" onClick="sec_2_nirmit_godown_add_rows()">नई पंक्ति जोड़े [+]</button><input type="hidden" name="sec_2_nirmit_godown_id" id="sec_2_nirmit_godown_id" value="' + id + '"></div>';
        txt += '</div>';
        $("#sec_2_nirmit_godown").append(txt);
    }

    function updateEmployeeRows(count) {
        var targetCount = parseInt(count);
        if (isNaN(targetCount) || targetCount < 0) targetCount = 0;

        var currentCount = parseInt($("#employees_row_count").val());
        if (isNaN(currentCount)) currentCount = 0;

        if (targetCount > currentCount) {
            // Add rows
            for (var i = currentCount + 1; i <= targetCount; i++) {
                var row = '<tr id="employees_row_' + i + '">';
                row += '<td>' + i + '</td>';
                row += '<td><input type="text" name="employee_post_' + i + '" class="form-control"></td>';
                row += '<td><input type="text" name="employee_name_' + i + '" class="form-control"></td>';
                row += '<td><input type="text" name="employee_father_name_' + i + '" class="form-control"></td>';
                row += '<td><input type="text" name="employee_phone_' + i + '" class="form-control chk_mobile" maxlength="10"></td>';
                row += '</tr>';
                $("#employees_tbody").append(row);
            }
        } else if (targetCount < currentCount) {
            // Remove rows
            for (var i = currentCount; i > targetCount; i--) {
                $("#employees_row_" + i).remove();
            }
        }
        $("#employees_row_count").val(targetCount);
    }

    function addDistrictAddressRow() {
        var id = parseInt($("#district_address_row_count").val());
        if (!id) id = 0;

        id++;

        var tr = '<tr id="district_address_row_' + id + '">';
        tr += '<td>' + id + '</td>';
        tr += '<td><input type="text" name="district_post_' + id + '" class="form-control"></td>';
        tr += '<td><input type="text" name="district_number_' + id + '" class="form-control"></td>';
        tr += '<td><input type="text" name="district_vacant_' + id + '" class="form-control"></td>';
        tr += '<td><input type="text" name="district_approved_' + id + '" class="form-control"></td>';
        tr += '</tr>';

        $("#district_address_tbody").append(tr);

        $("#district_address_row_count").val(id);
    }

    function addPurchaseSaleRow() {
        var id = parseInt($("#purchase_sale_row_count").val());
        if (!id) id = 0;

        // Validation before adding new row
        for (var i = 1; i <= id; i++) {
            if (
                $("input[name='wheat_purchase_" + i + "']").val() == "" ||
                $("input[name='rice_purchase_" + i + "']").val() == "" ||
                $("input[name='seed_" + i + "']").val() == ""
            ) {
                alert("पंक्ति संख्या " + i + " में डेटा खाली है");
                return;
            }
        }

        id++;

        var row = '<tr id="purchase_sale_row_' + id + '">';

        row += '<td>' + id + '</td>';
        row += '<td><input type="text" name="wheat_purchase_' + id + '" class="form-control"></td>';
        row += '<td><input type="text" name="rice_purchase_' + id + '" class="form-control"></td>';
        row += '<td><input type="text" name="seed_' + id + '" class="form-control"></td>';
        row += '<td><input type="text" name="fertilizer_' + id + '" class="form-control"></td>';
        row += '<td><input type="text" name="godown_rent_' + id + '" class="form-control"></td>';
        row += '<td><input type="text" name="nefed_' + id + '" class="form-control"></td>';
        row += '<td><input type="text" name="farmer_service_center_' + id + '" class="form-control"></td>';
        row += '<td><input type="text" name="other_business_' + id + '" class="form-control"></td>';

        row += '</tr>';

        $("#purchase_sale_tbody").append(row);
        $("#purchase_sale_row_count").val(id);
    }




    function stock_add_rows() {
    var id = parseInt($("#stock_row_count").val());
    if (!id) id = 1;

    // Validate existing rows
    for (var i = 1; i <= id; i++) {
        if ($("#stock_item_" + i).val() == '' ||
            $("#stock_closing_" + i).val() == '' ||
            $("#stock_book_" + i).val() == '') {

            alert("पंक्ति संख्या " + i + " पूरी भरें");
            $("#stock_item_" + i).focus();
            return;
        }
    }

    id++;

    $("#stock_rows_btn").remove();

    var txt = '<div class="row stock_row mt-2">';

    txt += '<div class="col-sm-1 form-group"><label>क्रमांक</label>' +
           '<input class="form-control" value="' + id + '" readonly></div>';

    txt += '<div class="col-sm-3 form-group"><label>स्टॉक आइटम</label>' +
           '<input type="text" name="stock_item_' + id + '" id="stock_item_' + id + '" class="form-control"></div>';

    txt += '<div class="col-sm-3 form-group"><label>31 मार्च 2020 को समापन स्टॉक</label>' +
           '<input type="text" name="stock_closing_' + id + '" id="stock_closing_' + id + '" class="form-control"></div>';

    txt += '<div class="col-sm-3 form-group"><label>बुक वैल्यू</label>' +
           '<input type="text" name="stock_book_' + id + '" id="stock_book_' + id + '" class="form-control chk_number"></div>';

           
    txt += '<div class="col-sm-3 form-group"><label>31 मार्च 2020 को अंतिम स्टॉक</label>' +
           '<input type="text" name="final_stock_' + id + '" id="final_stock_' + id + '" class="form-control"></div>';

    txt += '<div class="col-sm-3 form-group"><label>बुक वैल्यू</label>' +
           '<input type="text" name="final_book_' + id + '" id="final_book_' + id + '" class="form-control chk_number"></div>';

    txt += '<div class="col-sm-2 form-group my-auto" id="stock_rows_btn">';
    txt += '<button type="button" class="btn btn-info" onclick="stock_add_rows()">नई पंक्ति [+]</button>';
    txt += '<input type="hidden" id="stock_row_count" name="stock_row_count" value="' + id + '">';
    txt += '</div>';

    txt += '</div>';

    $("#sec_8_stock").append(txt);
}



    function useless_add_rows() {

    var id = parseInt($("#useless_row_count").val());
    if (!id) id = 1;

    // Validate existing rows
    for (var i = 1; i <= id; i++) {
        if ($("#useless_item_" + i).val() == '' ||
            $("#useless_closing_" + i).val() == '' ||
            $("#useless_book_" + i).val() == '') {

            alert("पंक्ति संख्या " + i + " पूरी भरें");
            $("#useless_item_" + i).focus();
            return;
        }
    }

    id++;

    $("#useless_rows_btn").remove();

    var txt = '<div class="row useless_row mt-2">';

    txt += '<div class="col-sm-1 form-group"><label>क्रमांक</label>' +
           '<input class="form-control" value="' + id + '" readonly></div>';

    txt += '<div class="col-sm-3 form-group"><label>वस्तु का नाम</label>' +
           '<input type="text" name="useless_item_' + id + '" id="useless_item_' + id + '" class="form-control"></div>';

    txt += '<div class="col-sm-3 form-group"><label>31 मार्च 2023 को समापन स्टॉक</label>' +
           '<input type="text" name="useless_closing_' + id + '" id="useless_closing_' + id + '" class="form-control"></div>';

    txt += '<div class="col-sm-3 form-group"><label>बुक वैल्यू</label>' +
           '<input type="text" name="useless_book_' + id + '" id="useless_book_' + id + '" class="form-control chk_number"></div>';

    txt += '<div class="col-sm-2 form-group my-auto" id="useless_rows_btn">';
    txt += '<button type="button" class="btn btn-info" onclick="useless_add_rows()">नई पंक्ति [+]</button>';
    txt += '<input type="hidden" id="useless_row_count" name="useless_row_count" value="' + id + '">';
    txt += '</div>';

    txt += '</div>';

    $("#sec_9_useless").append(txt);
}

    function purchase_add_rows() {

        var id = parseFloat($("#purchase_row_count").val());
        if (!id) id = 1;

        // Validation existing rows
        for (var i = 1; i <= id; i++) {
            if ($("#purchase_item_no_" + i).val() == '' ||
                $("#purchase_item_desc_" + i).val() == '' ||
                $("#purchase_date_" + i).val() == '') {

                alert("पंक्ति संख्या " + i + " पूरी भरें");
                $("#purchase_item_no_" + i).focus();
                return;
            }
        }

        id++;

        $("#purchase_rows_btn").remove();

        var txt = '<div class="row purchase_row mt-2">';

        txt += '<div class="col-sm-1 form-group"><label>क्रमांक</label><input class="form-control" value="' + id + '" readonly></div>';

        txt += '<div class="col-sm-2 form-group"><label>आइटम नंबर</label><input type="text" name="purchase_item_no_' + id + '" id="purchase_item_no_' + id + '" class="form-control"></div>';

        txt += '<div class="col-sm-3 form-group"><label>आइटम विवरण</label><input type="text" name="purchase_item_desc_' + id + '" id="purchase_item_desc_' + id + '" class="form-control"></div>';

        txt += '<div class="col-sm-2 form-group"><label>दिनांक</label><input type="date" name="purchase_date_' + id + '" id="purchase_date_' + id + '" class="form-control"></div>';

        txt += '<div class="col-sm-2 form-group"><label>वैल्यू</label><input type="text" name="purchase_value_' + id + '" id="purchase_value_' + id + '" class="form-control chk_number"></div>';

        txt += '<div class="col-sm-1 form-group"><label>संख्या</label><input type="text" name="purchase_qty_' + id + '" id="purchase_qty_' + id + '" class="form-control chk_number"></div>';

        txt += '<div class="col-sm-1 form-group my-auto" id="purchase_rows_btn">';
        txt += '<button type="button" class="btn btn-info" onclick="purchase_add_rows()">नई पंक्ति [+]</button>';
        txt += '<input type="hidden" id="purchase_row_count" name="purchase_row_count" value="' + id + '">';
        txt += '</div>';

        txt += '</div>';

        $("#sec_10_purchase_2024_25").append(txt);
    }

    function getCurrentLocationDynamic(btn) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var row = $(btn).closest('.row');
                row.find('.current-lat').val(position.coords.latitude);
                row.find('.current-long').val(position.coords.longitude);
            }, function (error) {
                alert("Error getting location: " + error.message);
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }
</script>

<script type="text/javascript" src="js/multistepform_pcf.js?v=2"></script>
<script src="js/light-bootstrap-dashboard.js?v=1.4.0"></script>

<script>
    function addRegionalOfficeRow() {
        var id = parseFloat($("#regional_office_count").val());
        if (!id) id = 0;
        
        // Simple validation check (optional, can be expanded)
        // for (var i = 1; i <= id; i++) {
        //     if ($("#office_name_" + i).val() == '') {
        //         alert("Please fill details for row " + i);
        //         return;
        //     }
        // }

        id++;

        var txt = '<tr id="regional_office_row_' + id + '">';
        txt += '<td>' + id + '</td>';
        txt += '<td><input type="text" name="office_name_' + id + '" class="form-control"></td>';
        txt += '<td><input type="text" name="division_' + id + '" class="form-control"></td>';
        txt += '<td><input type="text" name="tehsil_' + id + '" class="form-control"></td>';
        txt += '<td><input type="text" name="address_' + id + '" class="form-control"></td>';
        txt += '<td><input type="text" name="phone_' + id + '" class="form-control chk_mobile" data-maxlength="10" data-minlength="10"></td>';
        txt += '<td><input type="text" name="pincode_' + id + '" class="form-control chk_number" data-maxlength="6" data-minlength="6"></td>';
        txt += '<td><input type="email" name="email_' + id + '" class="form-control"></td>';
        txt += '<td><input type="file" name="geo_image[]" class="form-control"></td>';
        txt += '</tr>';

        $("#regional_offices_tbody").append(txt);
        $("#regional_office_count").val(id);
    }
</script>
<script>
    function updateOfficeRows(val) {
        var count = parseInt(val, 10);
        var container = document.getElementById('officeContainer');
        var wrapper = document.getElementById('zoneTableWrapper');
        if (!container || !wrapper) return;

        // Toggle visibility based on count
        if (isNaN(count) || count < 1) {
            wrapper.style.display = 'none';
            return; 
        } else {
            wrapper.style.display = 'block';
        }

        var tbody = container.getElementsByClassName('office-block')[0];
        // Ensure we operate on rows inside the tbody
        var currentRows = tbody.getElementsByTagName('tr');
        if(currentRows.length === 0) return; 

        var diff = count - currentRows.length;

        if (diff > 0) {
            var template = currentRows[0];
            for (var i = 0; i < diff; i++) {
                var clone = template.cloneNode(true);
                var inputs = clone.querySelectorAll('input, select, textarea');
                inputs.forEach(function (inp) {
                    if (inp.type === 'file') {
                        inp.value = '';
                    } else {
                        inp.value = '';
                    }
                    if (inp.tagName === 'SELECT') inp.selectedIndex = 0;
                });
                tbody.appendChild(clone);
            }
        } else if (diff < 0) {
            while (currentRows.length > count) {
                tbody.removeChild(currentRows[currentRows.length - 1]);
            }
        }
    }

    function updateSeparatePrakhandRows(val) {
        var count = parseInt(val, 10);
        var wrapper = document.getElementById('prakhandTableWrapper');
        var tbody = document.getElementById('prakhand-main-tbody');
        
        if (!wrapper || !tbody) return;

        if (isNaN(count) || count < 1) {
            wrapper.style.display = 'none';
            return;
        } else {
            wrapper.style.display = 'block';
        }

        var currentRows = tbody.getElementsByClassName('prakhand-row-template');
        var diff = count - currentRows.length;

        if (diff > 0) {
            var template = currentRows[0];
            for (var i = 0; i < diff; i++) {
                var clone = template.cloneNode(true);
                var inputs = clone.querySelectorAll('input, select, textarea');
                inputs.forEach(function (inp) {
                    if (inp.type === 'file') {
                        inp.value = '';
                    } else {
                        inp.value = '';
                    }
                });
                tbody.appendChild(clone);
            }
        } else if (diff < 0) {
            while (currentRows.length > count) {
                tbody.removeChild(currentRows[currentRows.length - 1]);
            }
        }
    }

    // If page loads with existing zone count, render blocks accordingly
    document.addEventListener('DOMContentLoaded', function () {
        var z = document.getElementById('no_of_zones');
        if (z && z.value) updateOfficeRows(z.value);
        
        
    });
</script>


<?php
page_footer_start();
?>