<?php
include("scripts/settings.php");
$msg = '';
$response = 1;
$tab = 1;

// print_r($_SESSION);
if (isset($_POST['district_name'])) {
    //$sql = 'select * from survey_invoice where society_id="'.$_POST['society_name'].'" and mobile_number="'.$_POST['mobile_number'].'" and otp_verify="1"';
    $sql = 'SELECT survey_invoice.sno as sno, survey_invoice.society_id as society_id, test2.col4 as society_name, master_society_type.type_name as type_name, division_name, district_name, tehseel_name, block_name,  survey_invoice.latitude as latitude, survey_invoice.longitude as longitude, survey_invoice.mobile_number as mobile_number, liquidation, litigation, concat("user_data/", col2, "/", col6, "/", photo_id) as photo_id, society_building_ownership, society_building_rent_amount, society_building_area, society_registration_no, society_registration_date, email_id, respondent_name, respondent_designation, respondent_aadhaar, active_members, inactive_members, others, col1, col2, col3, col5, col6 FROM `survey_invoice` left join test2 on test2.sno = society_id left join master_block on master_block.sno = col6 left join master_tehseel on master_tehseel.sno = col5 left join master_district on master_district.sno = col2 left join master_division on master_division.sno = col1 left join master_society_type on master_society_type.sno = col3  where society_id="' . $_POST['society_name'] . '" and mobile_number="' . $_POST['mobile_number'] . '" and otp_verify="1"';
    //echo $sql;
    $result_invoice = execute_query($sql);
    if (mysqli_num_rows($result_invoice) == 1) {
        $row_invoice = mysqli_fetch_assoc($result_invoice);
        if ($row_invoice['society_registration_date'] == '') {
            $row_invoice['society_registration_date'] = date("Y-m-d");
        }
        $_SESSION['survey_id'] = $row_invoice['sno'];
        $sql = 'select * from survey_invoice_sec_2_1 where survey_id="' . $row_invoice['sno'] . '"';
        $res_2_1 = execute_query($sql);
        if (mysqli_num_rows($res_2_1) != 0) {
            $row_2_1 = mysqli_fetch_assoc($res_2_1);
            $row_2_1['sec_6_access_road'] = $row_2_1['approach_road'];
            $row_2_1['sec_6_2_truck_not_reach'] = $row_2_1['distance_from_approach_road'];
            $row_2_1['sec_7_electrical_connection'] = $row_2_1['electric_connection'];
            $row_2_1['sec_7_electrical_connection_working'] = $row_2_1['electric_connection_working'];
            $row_2_1['sec_7_if_yes'] = $row_2_1['electric_connection_proposal'];
            $row_2_1['sec_8_internet_connection'] = $row_2_1['internet_connectivity'];
            $row_2_1['sec_8_if_yes'] = $row_2_1['internet_service_provider'];
            $row_2_1['sec_6_narrow_tubes'] = $row_2_1['water_govt_tap'];
            $row_2_1['sec_6_water_tank'] = $row_2_1['water_tank'];
            $row_2_1['sec_6_samarsabel'] = $row_2_1['water_submersible'];
            $row_2_1['sec_6_handpump'] = $row_2_1['water_hand_pump'];
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
            $row_2_1['balance_sheet_year'] = '';
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
            $row_2_1['sec_8_if_yes'] = '';
            $row_2_1['sec_6_narrow_tubes'] = '';
            $row_2_1['sec_6_water_tank'] = '';
            $row_2_1['sec_6_samarsabel'] = '';
            $row_2_1['sec_6_handpump'] = '';

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
            $row_2_1_2['count'] = $i - 1;
        } else {
            $row_2_1_2['count'] = 1;
            $row_2_1_2['sec_2_1_2_business_description_' . $i] = '';
            $row_2_1_2['sec_2_1_2_value_' . $i] = '';
        }

        $sql = 'select * from survey_invoice_sec_2_2 where survey_id="' . $row_invoice['sno'] . '"';
        $res_2_2 = execute_query($sql);
        if (mysqli_num_rows($res_2_2) != 0) {
            $row_2_2 = mysqli_fetch_assoc($res_2_2);
        } else {
            $row_2_2['secretary'] = '';
            $row_2_2['secretary_status'] = '';
            $row_2_2['secretary_cader'] = '';
            $row_2_2['secretary_name'] = '';
            $row_2_2['secretary_mobile'] = '';
            $row_2_2['secretary_aadhaar'] = '';
            $row_2_2['accountant'] = '';
            $row_2_2['assistant_accountant'] = '';
            $row_2_2['seller'] = '';
            $row_2_2['support_staff'] = '';
            $row_2_2['guard'] = '';
            $row_2_2['computer_operator'] = '';
            $row_2_2['govt_program'] = '';
            $row_2_2['other_description'] = '';

        }

        $sql = 'select *, concat("user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/", photo_id) as new_photo_id from survey_invoice_sec_3_1 where survey_id="' . $row_invoice['sno'] . '"';
        $res_3_1 = execute_query($sql);
        if (mysqli_num_rows($res_3_1) != 0) {
            $row_3_1 = mysqli_fetch_assoc($res_3_1);
            $row_3_1['sec_3_ownership'] = $row_invoice['society_building_ownership'];
            $row_3_1['sec_3_d_boundry'] = $row_3_1['boundry_wall'];
            $row_3_1['sec_3_d_main_gate'] = $row_3_1['main_gate'];
        } else {
            $row_3_1['number_of_sides'] = '';
            $row_3_1['total_area'] = '';
            $row_3_1['govt_records'] = '';
            $row_3_1['gata_no'] = '';
            $row_3_1['new_photo_id'] = '';
            $row_3_1['east_side'] = '';
            $row_3_1['west_side'] = '';
            $row_3_1['south_side'] = '';
            $row_3_1['north_side'] = '';
            $row_3_1['on_road_land'] = '';
            $row_3_1['front_side'] = '';
            $row_3_1['remarks'] = '';
            $row_3_1['sec_3_ownership'] = '';
            $row_3_1['sec_3_d_boundry'] = '';
            $row_3_1['sec_3_d_main_gate'] = '';
        }

        $sql = 'select * from survey_invoice_sec_3_3 where survey_id="' . $row_invoice['sno'] . '"';
        $res_3_3 = execute_query($sql);
        $i = 1;
        $a = 1;
        if (mysqli_num_rows($res_3_3) != 0) {
            $row_3_3['count'] = mysqli_num_rows($res_3_3);
            while ($row_temp = mysqli_fetch_assoc($res_3_3)) {
                $row_3_3['sec_3_b_type_of_construction_' . $i] = $row_temp['type_of_construction'];
                $row_3_3['sec_3_b_type_of_fund_' . $i] = $row_temp['type_of_fund'];
                $row_3_3['sec_3_b_length_' . $i] = $row_temp['length'];
                $row_3_3['sec_3_b_width_' . $i] = $row_temp['width'];
                $row_3_3['sec_3_b_comment_' . $i] = $row_temp['remarks'];
                $i++;
            }
            $row_3_3['count'] = $i - 1;
        } else {
            $row_3_3['count'] = 1;
            $row_3_3['sec_3_b_type_of_construction_' . $i] = '';
            $row_3_3['sec_3_b_type_of_fund_' . $i] = '';
            $row_3_3['sec_3_b_length_' . $i] = '';
            $row_3_3['sec_3_b_width_' . $i] = '';
            $row_3_3['sec_3_b_comment_' . $i] = '';
        }

        $sql = 'select * from survey_invoice_sec_3_1_sides where survey_id="' . $row_invoice['sno'] . '"';
        $res_3_3_side = execute_query($sql);
        if (mysqli_num_rows($res_3_3_side) != 0) {
            $sides_data = array();
            $i = 1;
            while ($row_3_3_side = mysqli_fetch_assoc($res_3_3_side)) {
                $sides_data['sec_3_a_' . $i] = $row_3_3_side['length'];
                $i++;
            }
        }

        $sql = 'select * from survey_invoice_sec_3_4 where survey_id="' . $row_invoice['sno'] . '"';
        //echo $sql;
        $res_3_4 = execute_query($sql);
        if (mysqli_num_rows($res_3_4) != 0) {
            $i = 1;
            while ($row_3_4_temp = mysqli_fetch_assoc($res_3_4)) {
                $row_3_4['sec_3_b_godown_length_' . $i] = $row_3_4_temp['length'];
                $row_3_4['sec_3_b_godown_width_' . $i] = $row_3_4_temp['width'];
                $row_3_4['sec_3_b_storage_capacity_' . $i] = $row_3_4_temp['storage_capacity'];
                $row_3_4['sec_3_b_godown_status_' . $i] = $row_3_4_temp['construction_status'];
                $row_3_4['sec_3_b_godown_type_of_fund_' . $i] = $row_3_4_temp['type_of_fund'];
                $row_3_4['sec_3_b_comment_' . $i] = $row_3_4_temp['remarks'];
                $i++;
            }
            $row_3_4['count'] = $i - 1;
        } else {
            $i = 1;
            $row_3_4['count'] = 1;
            $row_3_4['sec_3_b_godown_length_' . $i] = '';
            $row_3_4['sec_3_b_godown_width_' . $i] = '';
            $row_3_4['sec_3_b_storage_capacity_' . $i] = '';
            $row_3_4['sec_3_b_godown_status_' . $i] = '';
            $row_3_4['sec_3_b_godown_type_of_fund_' . $i] = '';
            $row_3_4['sec_3_b_comment_' . $i] = '';

        }
        $sql = 'select * from survey_invoice_sec_3_5 where survey_id="' . $row_invoice['sno'] . '"';
        $res_3_5_side = execute_query($sql);
        if (mysqli_num_rows($res_3_5_side) != 0) {
            $data_3_5 = array();
            $i = 1;
            while ($row_3_5_side = mysqli_fetch_assoc($res_3_5_side)) {
                $row_3_5['sec_3_c_length_' . $i] = $row_3_5_side['total_area'];
                $row_3_5['sec_3_c_vacant_land_status_' . $i] = $row_3_5_side['land_type'];
                $row_3_5['sec_3_c_land_location_' . $i] = $row_3_5_side['location'];
                $row_3_5['sec_3_c_approach_road_' . $i] = $row_3_5_side['approach_road'];
                $row_3_5['sec_3_c_paved_road_' . $i] = $row_3_5_side['approach_road'];
                $i++;
            }
            $row_3_5['sec_3_c_id'] = $i - 1;
        } else {
            $i = 1;
            $row_3_5['sec_3_c_id'] = $i;
            $row_3_5['sec_3_c_length_' . $i] = '';
            $row_3_5['sec_3_c_vacant_land_status_' . $i] = '';
            $row_3_5['sec_3_c_land_location_' . $i] = '';
            $row_3_5['sec_3_c_approach_road_' . $i] = '';
            $row_3_5['sec_3_c_paved_road_' . $i] = '';
        }

        $sql = 'select * from survey_invoice_sec_3_6 where survey_id="' . $row_invoice['sno'] . '"';
        $res_3_6_side = execute_query($sql);
        if (mysqli_num_rows($res_3_6_side) != 0) {
            $data_3_6 = array();
            $i = 1;
            while ($row_3_6_side = mysqli_fetch_assoc($res_3_6_side)) {
                $row_3_6['sec_3_6_type_of_construction'] = $row_3_6_side['type_of_construction'];
                $row_3_6['sec_3_6_rent'] = $row_3_6_side['rent_amount'];
                $row_3_6['sec_3_6_area'] = $row_3_6_side['total_area'];
            }
        } else {
            $i = 1;
            $row_3_6['sec_3_6_type_of_construction'] = '';
            $row_3_6['sec_3_6_rent'] = '';
            $row_3_6['sec_3_6_area'] = '';
        }

        $sql = 'select * from survey_invoice_sec_4 where survey_id="' . $row_invoice['sno'] . '"';
        $res_4 = execute_query($sql);
        $custom_hiring_other = array();
        if (mysqli_num_rows($res_4) != 0) {
            $row_4 = mysqli_fetch_assoc($res_4);
            $row_4['sec_4_micro_atm'] = $row_4['micro_atm'];
            $row_4['sec_4_custom_hiring'] = $row_4['custom_hiring_center'];
            $row_4['sec_4_drone'] = $row_4['drone'];
            $row_4['sec_4_chhanna'] = $row_4['chalana'];
            $row_4['sec_4_power_duster'] = $row_4['power_duster'];
            $row_4['sec_4_tractor'] = $row_4['tractor'];
            $row_4['sec_4_chair'] = $row_4['office_chair'];
            $row_4['sec_4_table'] = $row_4['office_table'];
            $row_4['sec_4_almari'] = $row_4['office_almirah'];
            $row_4['sec_4_remarks'] = $row_4['remarks'];
        } else {
            $row_4['sec_4_micro_atm'] = '';
            $row_4['sec_4_custom_hiring'] = '';
            $row_4['sec_4_drone'] = '';
            $row_4['sec_4_chhanna'] = '';
            $row_4['sec_4_power_duster'] = '';
            $row_4['sec_4_tractor'] = '';
            $row_4['sec_4_chair'] = '';
            $row_4['sec_4_table'] = '';
            $row_4['sec_4_almari'] = '';
            $row_4['sec_4_remarks'] = '';

        }

        $sql = 'select * from survey_invoice_sec_5 where survey_id="' . $row_invoice['sno'] . '"';
        $res_5 = execute_query($sql);
        if (mysqli_num_rows($res_5) != 0) {
            $row_5 = mysqli_fetch_assoc($res_5);
            $row_5['sec_5_built_building'] = $row_5['building_status'];
            $row_5['sec_5_detailed_information'] = $row_5['building_status_remarks'];
            $row_5['sec_6_a_length'] = $row_5['floor_length'];
            $row_5['sec_6_a_width'] = $row_5['floor_width'];
            $row_5['sec_6_b_length'] = $row_5['wall_length'];
            $row_5['sec_6_b_width'] = $row_5['wall_width'];
            $row_5['sec_6_c_length'] = $row_5['paint_length'];
            $row_5['sec_6_c_width'] = $row_5['paint_width'];
            $row_5['sec_6_d_length'] = $row_5['roof_length'];
            $row_5['sec_6_d_width'] = $row_5['roof_width'];

            $row_5['sec_6_e_floor'] = $row_5['washroom_floor'];
            $row_5['sec_6_e_plaster'] = $row_5['washroom_plaster'];
            $row_5['sec_6_e_ceiling'] = $row_5['washroom_roof'];
            $row_5['sec_6_e_seat'] = $row_5['washroom_seat'];
            $row_5['sec_6_e_plumbing'] = $row_5['washroom_plumbing'];
            $row_5['sec_6_f_number_of_door'] = $row_5['doors'];
            $row_5['sec_6_g_number_of_window'] = $row_5['windows'];
            $row_5['sec_6_h_length'] = $row_5['plaster_wall'];
            $row_5['sec_6_h_width'] = $row_5['plaster_roof'];
            $row_5['sec_6_i_other'] = $row_5['others'];

            if ($row_5['floor_image'] != '') {
                $row_5['sec_6_a_img'] = 'user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/' . $row_5['floor_image'];
            } else {
                $row_5['sec_6_a_img'] = '';
            }
            if ($row_5['wall_image'] != '') {
                $row_5['sec_6_b_img'] = 'user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/' . $row_5['wall_image'];
            } else {
                $row_5['sec_6_b_img'] = '';
            }
            if ($row_5['paint_image'] != '') {
                $row_5['sec_6_c_img'] = 'user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/' . $row_5['paint_image'];
            } else {
                $row_5['sec_6_c_img'] = '';
            }
            if ($row_5['roof_image'] != '') {
                $row_5['sec_6_d_img'] = 'user_data/' . $row_invoice['col2'] . '/' . $row_invoice['col6'] . '/' . $row_5['roof_image'];
            } else {
                $row_5['sec_6_d_img'] = '';
            }
        } else {
            $row_5['sec_5_built_building'] = "";
            $row_5['sec_5_detailed_information'] = "";
            $row_5['sec_6_a_length'] = "";
            $row_5['sec_6_a_width'] = "";
            $row_5['sec_6_b_length'] = "";
            $row_5['sec_6_b_width'] = "";
            $row_5['sec_6_c_length'] = "";
            $row_5['sec_6_c_width'] = "";
            $row_5['sec_6_d_width'] = "";
            $row_5['sec_6_d_length'] = "";
            $row_5['sec_6_d_width'] = "";
            $row_5['sec_6_e_floor'] = "";
            $row_5['sec_6_e_plaster'] = "";
            $row_5['sec_6_e_ceiling'] = "";
            $row_5['sec_6_e_seat'] = "";
            $row_5['sec_6_e_plumbing'] = "";
            $row_5['sec_6_f_number_of_door'] = "";
            $row_5['sec_6_g_number_of_window'] = "";
            $row_5['sec_6_h_length'] = "";
            $row_5['sec_6_h_width'] = "";
            $row_5['sec_6_i_other'] = "";
            $row_5['sec_6_a_img'] = '';
            $row_5['sec_6_b_img'] = '';
            $row_5['sec_6_c_img'] = '';
            $row_5['sec_6_d_img'] = '';
        }


        $sql = 'select * from survey_invoice_sec_11 where survey_id="' . $row_invoice['sno'] . '"';
        $res_9 = execute_query($sql);
        if (mysqli_num_rows($res_9) != 0) {
            $row_9 = mysqli_fetch_assoc($res_9);
            $row_9['sec_9_a_length'] = $row_9['godown_length'];
            $row_9['sec_9_a_width'] = $row_9['godown_width'];
            $row_9['sec_9_a_capacity_in_mt'] = $row_9['godown_capacity'];
            $row_9['sec_9_b_length'] = $row_9['bathroom_length'];
            $row_9['sec_9_b_width'] = $row_9['bathroom_width'];
            $row_9['sec_9_c_length'] = $row_9['showroom_length'];
            $row_9['sec_9_c_width'] = $row_9['showroom_width'];
            $row_9['sec_9_d_boundary_wall_length'] = $row_9['boundary_length'];
            $row_9['sec_9_d_boundary_wall_width'] = $row_9['boundary_width'];
            $row_9['sec_9_e_multipurpose_hall_length'] = $row_9['multipurpose_length'];
            $row_9['sec_9_e_multipurpose_hall_width'] = $row_9['multipurpose_width'];
        } else {
            $row_9['sec_9_a_length'] = '';
            $row_9['sec_9_a_width'] = '';
            $row_9['sec_9_a_capacity_in_mt'] = '';
            $row_9['sec_9_b_length'] = '';
            $row_9['sec_9_b_width'] = '';
            $row_9['sec_9_c_length'] = '';
            $row_9['sec_9_c_width'] = '';
            $row_9['sec_9_d_boundary_wall_length'] = '';
            $row_9['sec_9_d_boundary_wall_width'] = '';
            $row_9['sec_9_e_multipurpose_hall_length'] = '';
            $row_9['sec_9_e_multipurpose_hall_width'] = '';

        }
        $response = 2;
    }
}
?>

<?php
page_header_start();
?>
<link href="css/multistepform.css" rel="stylesheet" type="text/css" media="all" />
<script src="js/survey_validate.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    .date-field {
        display: none;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }
</style>
<style>
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
        color: #FFFFFF;
        background: #FF8E00;
        border-radius: 15px;
        padding: 10px 10px 6px 20px;
    }

    .step h5 {
        color: #000000;
        background: #FFDB44;
        border-radius: 15px;
        padding: 10px 10px 6px 20px;
    }
</style>
<style>
    .select-default {
        background-color: white;
    }

    .card label {
        font-size: 0.80rem;
    }
</style>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 20px;
    }

    th,
    td {
        padding: 8px 12px;
        border: 1px solid #ccc;
        text-align: left;
    }

    input[type="text"],
    input[type="number"] {
        width: 100%;
        box-sizing: border-box;
    }

    button {
        margin-top: 10px;
        padding: 6px 12px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    button:hover {
        background-color: #45a049;
    }

    .tree-output {
        margin-top: 30px;
        font-family: 'Courier New', Courier, monospace;
    }

    .tree-output ul {
        padding-left: 20px;
        list-style-type: none;
    }

    .tree-output ul li {
        margin: 10px 0;
        position: relative;
    }

    .tree-output ul li::before {
        content: "";
        position: absolute;
        left: -20px;
        top: 10px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #000;
    }

    .tree-output ul li ul {
        margin-top: 5px;
        padding-left: 20px;
    }

    .tree-output ul li ul li::before {
        left: -10px;
        top: 8px;
    }

    .tree-output ul li ul li {
        margin: 5px 0;
    }
</style>
<?php
page_header_end();
page_sidebar();

// $sql = 'select * from survey_invoice_validation where survey_id="' . $_SESSION['survey_id'] . '" and approval_status="reject" order by creation_time desc limit 1';
// $result_rejection = execute_query($sql);
// if (mysqli_num_rows($result_rejection) != 0) {
//     $row_rejection = mysqli_fetch_assoc($result_rejection);
//     $msg = '<p class="text-danger">आपका प्रपत्र निम्न कारणों से सत्यापन में वापस भेजा गया है : <br/>' . $row_rejection['remarks'] . '</p>';
// }
        ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex my-auto">
                                <div class="col-md-12">
                                    <div class="progress">
                                        <div aria-valuemax="100" aria-valuemin="0" aria-valuenow="50"
                                            class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                                            role="progressbar" style="width: 0%">
                                        </div>
                                    </div>
                                    <form action="scripts/ajax.php" method="post" enctype="multipart/form-data" id="user_form"
                                        name="user_form">
                                        <div id="steps-container">
                                            <!-------------------1st start--------------------------------------------------------------------->
                                <div class="step">
                                    <!-- <marquee style="font-size: 18px; color: red;">
                                            नोट: समस्त विवरण ADO अथवा (ADO विकास खंड के तैनात न होने पर ADCO द्वारा)
                                            प्रत्येक माह की पांच (5) तारीख तक भरना अनिवार्य है, जिसके उपरांत AR अथवा CEO
                                            द्वारा परीक्षण कर 10 तारीख तक अनुमोदन करना आवश्यक है।
                                        </marquee><br><br> -->
                                                <div?php echo $msg; ?>

                                                    <h4><img src="images/logo/1.png" alt="text" class="img-fluid stat-icon"
                                                            style="height:45px; width:45px;"> 1. समिति का विवरण </h4>
                                                    <div class="col-sm-12">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="row">
                                                                    <div class="col-sm-8 form-group">
                                                                        <label>संस्था का प्रकार</label>
                                                                        <input type="text" id="" name="" tabindex = "<?php echo $tab++; ?>" readonly value="शीर्ष सहकारी संस्था (APEX)">
                                                                    </div>
                                                                    <div class="col-sm-8 form-group" style="margin: 9px;"   id="sec_1_institute_name_container">
                                                                        <label>संस्था का नाम</label>
                                                                        <input type="text" id="" name="" tabindex = "<?php echo $tab++; ?>" readonly value="उद्योग एवं उद्यम प्रोत्साहन निदेशालय, उ०प्र०">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="row">
                                                                    <div class="col-md-2">
                                                                        <label>Latitude</label>
                                                                        <input type="text" id="lat" value="<?php echo $row_invoice['latitude']; ?>" class="form-control">
                                                                        <label>Longitude</label>
                                                                        <input type="text" id="long" value="<?php echo $row_invoice['longitude']; ?>" class="form-control">
                                                                        <button type="button" class="btn btn-info" onClick="getLocation();">मुख्यालय की जियो-लोकेशन</button>
                                                                    </div>
                                                                    <div class="col-md-10" id="map_container">
                                                                        <iframe id="googlemap"
                                                                            src="https://maps.google.com/maps?q=<?php echo $row_invoice['latitude'] . ',' . $row_invoice['longitude']; ?>&hl=en&z=13&amp;output=embed"
                                                                            width="100%" height="100%" style="border: 1px solid; border-radius: 10px;" allowfullscreen=""
                                                                            loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <div class="row" style="margin: 7px;">
                                                                    <div class="col-md-3">
                                                                        <h6>समिति का नाम : </h6>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        बी-पैक्स <?php echo $row_invoice['society_name']; ?>
                                                                        <input type="hidden" id="society_code" value="<?php echo $row_invoice['society_id']; ?>">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <h6>मण्डल : </h6>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <?php echo $row_invoice['division_name']; ?>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <h6>जिला : </h6>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <?php echo $row_invoice['district_name']; ?>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <h6>तहसील : </h6>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <?php echo $row_invoice['tehseel_name']; ?>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <h6>ब्लाक : </h6>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <?php echo $row_invoice['block_name']; ?>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <h6>समिति का प्रकार : </h6>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <?php echo $row_invoice['type_name']; ?>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <h6>मोबाइल नंबर : </h6>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <?php echo $row_invoice['mobile_number']; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr/>
                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>क्या समिति सक्रिय है ?</label>
                                                                <select class="form-control" id="committee_status"
                                                                    name="committee_status" tabindex="<?php echo $tab++; ?>"
                                                                    onChange="hide_show(this.value, '#committee_date_section', 'no'); color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                                    <option value="">--Select--</option>
                                                                    <option value="yes">हाँ</option>
                                                                    <option value="no">नहीं</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-3 form-group" style="margin-block: -6px;">
                                                                <label>ग्राम पंचायत</label>
                                                                <br />
                                                                <input type="text" name="society_registration_no"
                                                                    id="society_registration_no" tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['society_registration_no']; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-3 form-group" style="margin-block: -6px;">
                                                                <label>समिति पंजीकरण संख्या</label>
                                                                <br />
                                                                <input type="text" name="society_registration_no"
                                                                    id="society_registration_no" tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_invoice['society_registration_no']; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>समिति पंजीकरण दिनांक</label>
                                                                <label><small>नहीं पता होने कि स्थिति में आज का ही दिनांक
                                                                        दर्शायें</small></label>
                                                                <script type="text/javascript" language="javascript">
                                                                    document.writeln(DateInput('society_registration_date', 'society_registration_date', true, 'YYYY-MM-DD', '<?php echo $row_invoice['society_registration_date']; ?>', <?php echo $tab++;
                                                                       $tab = $tab + 3; ?>));
                                                                </script>
                                                            </div>
                                                            <div class="col-sm-3 form-group" id="committee_date_section"
                                                                style="display: none;">
                                                                <label>समिति की तिथि</label><br>
                                                                <label><small>नहीं पता होने की स्थिति में आज का ही दिनांक
                                                                        दर्शायें</small></label>
                                                                <input type="date" name="committee_date" id="committee_date"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo !empty($row_invoice['committee_date']) ? $row_invoice['committee_date'] : date('Y-m-d'); ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>ई-मेल आई.डी.</label>
                                                                <input type="text" name="sec_1_email" id="sec_1_email"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    value="<?php echo $row_invoice['email_id']; ?>">
                                                            </div>
                                                            <?php
                                                            if (!empty($row_invoice['photo_id']) && file_exists($row_invoice['photo_id'])) {
                                                                ?>
                                                                <div class="col-sm-2 form-group">
                                                                    <label>मुख्यालय की फोटो संलग्न करें</label>
                                                                    <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                        name="society_photo" id="society_photo"
                                                                        tabindex="<?php echo $tab++; ?>" class="form-control">
                                                                </div>
                                                                <div class="col-sm-2 form-group">
                                                                    <img src="<?php echo $row_invoice['photo_id']; ?>"
                                                                        class="img-fluid img-thumbnail" style="height:50px;"
                                                                        id="society_photo_uploaded">
                                                                    <label><a href="<?php echo $row_invoice['photo_id']; ?>"
                                                                            target="_blank">संलग्न फोटो देखें</a></label>

                                                                </div>
                                                            <?php
                                                            } else {
                                                                ?>
                                                                <div class="col-sm-3 form-group">
                                                                    <label>मुख्यालय की फोटो संलग्न करें</label>
                                                                    <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                        name="society_photo" id="society_photo"
                                                                        tabindex="<?php echo $tab++; ?>" class="form-control">

                                                                </div>

                                                            <?php

                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>क्या समिति परिसमापन (Liquidation) में है?</label>
                                                                <select name="sec_1_liquidation" id="sec_1_liquidation"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    onchange="hide_show(this.value, '#liquidation_date_container', 'yes');color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                                    <option value="">--Select--</option>
                                                                    <option value="yes" <?php echo ($row_invoice['liquidation'] == 'yes') ? ' selected="selected"' : ''; ?>>
                                                                        हाँ</option>
                                                                    <option value="no" <?php echo ($row_invoice['liquidation'] == 'no') ? ' selected="selected"' : ''; ?>>
                                                                        नहीं
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <div class="col-sm-2 form-group" id="liquidation_date_container"
                                                                style="display: none;">
                                                                <label>परिसमापन की तिथि</label>
                                                                <input type="date" id="sec_1_liquidation_date"
                                                                    name="sec_1_liquidation_date" class="form-control"
                                                                    placeholder="Choose Date"
                                                                    value="<?php echo isset($row_invoice['liquidation_date']) ? $row_invoice['liquidation_date'] : ''; ?>">
                                                            </div>

                                                            <div class="col-sm-3 form-group">
                                                                <label>क्या समिति पर कोई वाद (Litigation) न्यायालय में विचाराधीन
                                                                    हैं?</label>
                                                                <select name="sec_1_litigation" id="sec_1_litigation"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                    onchange="document.getElementById('property_possession_container').style.display = this.value === 'yes' ? 'block' : 'none'; color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                                    <option value="">--Select--</option>
                                                                    <option value="yes" <?php echo ($row_invoice['litigation'] == 'yes') ? ' selected="selected"' : ''; ?>>हाँ
                                                                    </option>
                                                                    <option value="no" <?php echo ($row_invoice['litigation'] == 'no') ? ' selected="selected"' : ''; ?>>
                                                                        नहीं
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <br>
                                                        <h5> <img src="images/logo/2.png" alt="text" class="img-fluid stat-icon"
                                                                style="height:50px; width:50px;"> 1.1 सदस्यों का विवरण </h5><br>
                                                        <small><b>(I) सदस्यों का विवरण :</b></small><br>
                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>पंजीकरण ले समय सदस्यों की संख्या</label>
                                                                <input type="text" name="sec_1_members_ordinary_no"
                                                                    id="sec_1_members_ordinary_no" tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_2_1['msp_comm']; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>वर्तमान मे सदस्यों की संख्या</label>
                                                                <input type="text" name="sec_1_members_ordinary_no"
                                                                    id="sec_1_members_ordinary_no" tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_2_1['msp_comm']; ?>">
                                                            </div>
                                                        </div>
                                                        <small><b>(II) बनाए गए सदस्यों की संख्या :</b></small><br>
                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <label>01-अप्रैल-2024 से भर्ती किए गए सदस्यों की संख्या</label>
                                                                <input type="text" name="sec_1_members_ordinary_no"
                                                                    id="sec_1_members_ordinary_no" tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_2_1['msp_comm']; ?>">
                                                            </div>
                                                            <div class="col-sm-3 form-group">
                                                                <label>01-अप्रैल-2024 से प्राप्त अंशधन</label>
                                                                <input type="text" name="sec_1_members_ordinary_no"
                                                                    id="sec_1_members_ordinary_no" tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_2_1['msp_comm']; ?>">
                                                            </div>
                                                        </div>
                                                        <small><b> (III) कुल सदस्यों की संख्या :</b></small>
                                                        <div class="row">
                                                            <div class="col-sm-3 form-group">
                                                                <input type="text" name="sec_1_all_members" id="sec_1_all_members"
                                                                    tabindex="<?php echo $tab++; ?>" class="form-control" disabled
                                                                    value="<?php echo $row_2_1['loan']; ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            <!----------------2.1 start-------------------------------------------------------->
                                            
                                            <!------ 3td start ------->
                                    <div class="step">
                                        <h4><img src="images/logo/3.png" alt="text" class="img-fluid stat-icon"
                                                style="height:50px; width:50px;"> 2(I) वित्तीय सूचना</h4>
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <div class="col-sm-3 form-group">
                                                    <label>आय व्ययक विवरण किस वर्ष तक बना है</label>
                                                    <select name="sec2_santulan_patra" class="form-control">
                                                        <option value="">
                                                            <?php echo '--Select--'; ?>
                                                        </option>
                                                        <?php
                                                        if (date('m') > 3) {
                                                            $select_start_session = date('Y');
                                                        } else {
                                                            $select_start_session = date('Y') - 1;
                                                        }
                                                        $session_start = date('Y') - 7;
                                                        for ($i = $session_start; $i <= $session_start + 7; $i++) {
                                                            $end_session = $i + 1;
                                                            ?>
                                                        <option value="<?php echo $i . '-' . $end_session; ?>" <?php
                                                            if (isset($_POST['sec2_santulan_patra']) && $_POST['sec2_santulan_patra'] == $i . '-' . $end_session) {
                                                                echo 'selected';
                                                            }
                                                            ?>
                                                            >
                                                            <?php echo $i . '-' . $end_session; ?>
                                                        </option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <small><b>(I) वित्तीय वर्ष 2021-22</b></small>
                                            <div class="row">
                                                <div class="col-sm-3 form-group">
                                                    <label>वार्षिक लाभ/हानि की स्थिति</label>
                                                    <select name="sec_1_profit_loss_2022" id="sec_1_profit_loss_2022"
                                                        tabindex="4" class="form-control"
                                                        onchange="color_change(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                        <option value="">--Select--</option>
                                                        <option value="profit">लाभ</option>
                                                        <option value="loss">हानि</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-3 form-group">
                                                    <label>(धनराशि रु० लाख मे)</label>
                                                    <input type="text" name="sec_1_profit_loss_amount_2022"
                                                        id="sec_1_profit_loss_amount_2022" tabindex="5"
                                                        class="form-control">
                                                </div>
                                                <!-- <div class="col-sm-3 form-group">
                                                    <label>संचित लाभ/हानि की स्थिति</label>
                                                    <select name="sec_1_accumulated_2022" id="sec_1_accumulated_2022"
                                                        tabindex="4" class="form-control"
                                                        onchange="color_change(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                        <option value="">--Select--</option>
                                                        <option value="profit">लाभ</option>
                                                        <option value="loss">हानि</option>
                                                    </select>
                                                </div> -->
                                                <!-- <div class="col-sm-3 form-group">
                                                    <label>(धनराशि रु० लाख मे)</label>
                                                    <input type="text" name="sec_1_accumulated_amount_2022"
                                                        id="sec_1_accumulated_amount_2022" tabindex="5"
                                                        class="form-control">
                                                </div> -->
                                            </div>
                                            <small><b>(II) वित्तीय वर्ष 2022-23</b></small>
                                            <div class="row">
                                                <div class="col-sm-3 form-group">
                                                    <label>वार्षिक लाभ/हानि की स्थिति</label>
                                                    <select name="sec_1_profit_loss_2023" id="sec_1_profit_loss_2023"
                                                        tabindex="6" class="form-control"
                                                        onchange="color_change(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                        <option value="">--Select--</option>
                                                        <option value="profit">लाभ</option>
                                                        <option value="loss">हानि</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-3 form-group">
                                                    <label>(धनराशि रु० लाख मे)</label>
                                                    <input type="text" name="sec_1_profit_loss_amount_2023"
                                                        id="sec_1_profit_loss_amount_2023" tabindex="7"
                                                        class="form-control">
                                                </div>
                                                <!-- <div class="col-sm-3 form-group">
                                                    <label>संचित लाभ/हानि की स्थिति</label>
                                                    <select name="sec_1_accumulated_2023" id="sec_1_accumulated_2023"
                                                        tabindex="6" class="form-control"
                                                        onchange="color_change(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                        <option value="">--Select--</option>
                                                        <option value="profit">लाभ</option>
                                                        <option value="loss">हानि</option>
                                                    </select>
                                                </div> -->
                                                <!-- <div class="col-sm-3 form-group">
                                                    <label>(धनराशि रु० लाख मे)</label>
                                                    <input type="text" name="sec_1_accumulated_amount_2023"
                                                        id="sec_1_accumulated_amount_2023" tabindex="7"
                                                        class="form-control">
                                                </div> -->
                                            </div>
                                            <small><b>(III) वित्तीय वर्ष 2023-24</b></small>
                                            <div class="row">
                                                <div class="col-sm-3 form-group">
                                                    <label>वार्षिक लाभ/हानि की स्थिति</label>
                                                    <select name="sec_1_profit_loss_2024" id="sec_1_profit_loss_2024"
                                                        tabindex="8" class="form-control"
                                                        onchange="color_change(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                        <option value="">--Select--</option>
                                                        <option value="profit">लाभ</option>
                                                        <option value="loss">हानि</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-3 form-group">
                                                    <label>(धनराशि रु० लाख मे)</label>
                                                    <input type="text" name="sec_1_profit_loss_amount_2024"
                                                        id="sec_1_profit_loss_amount_2024" tabindex="9"
                                                        class="form-control">
                                                </div>
                                                <!-- <div class="col-sm-3 form-group">
                                                    <label>संचित लाभ/हानि की स्थिति</label>
                                                    <select name="sec_1_accumulated_2024" id="sec_1_accumulated_2024"
                                                        tabindex="8" class="form-control"
                                                        onchange="color_change(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                        <option value="">--Select--</option>
                                                        <option value="profit">लाभ</option>
                                                        <option value="loss">हानि</option>
                                                    </select>
                                                </div> -->
                                                <!-- <div class="col-sm-3 form-group">
                                                    <label>(धनराशि रु० लाख मे)</label>
                                                    <input type="text" name="sec_1_accumulated_amount_2024"
                                                        id="sec_1_accumulated_amount_2024" tabindex="9"
                                                        class="form-control">
                                                </div> -->
                                            </div>
                                            <small><b>(IV) आडिट</b></small>
                                            <div class="row">
                                                <div class="col-sm-3 form-group">
                                                    <label>आडिट किस वित्तीय वर्ष तक हुआ है</label>
                                                    <select name="sec2_audit_year" class="form-control">
                                                        <option value="">
                                                            <?php echo '--Select--'; ?>
                                                        </option>
                                                        <?php
                                                        if (date('m') > 3) {
                                                            $select_start_session = date('Y');
                                                        } else {
                                                            $select_start_session = date('Y') - 1;
                                                        }
                                                        $session_start = date('Y') - 7;
                                                        for ($i = $session_start; $i <= $session_start + 7; $i++) {
                                                            $end_session = $i + 1;
                                                            ?>
                                                        <option value="<?php echo $i . '-' . $end_session; ?>" <?php
                                                            if (isset($_POST['sec2_audit_year']) && $_POST['sec2_audit_year'] == $i . '-' . $end_session) {
                                                                echo 'selected';
                                                            }
                                                            ?>
                                                            >
                                                            <?php echo $i . '-' . $end_session; ?>
                                                        </option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-2 form-group">
                                                    <label>ऑडिट वर्गीकरण</label>
                                                    <select name="sec_2_audit_grading" class="form-control">
                                                        <option value="">--select-- </option>
                                                        <option value="A">A</option>
                                                        <option value="B">B</option>
                                                        <option value="C">C</option>
                                                        <option value="D">D</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-2 form-group">
                                                    <label>अनुपालन की स्थिति</label>
                                                    <select name="sec_2_compliance" class="form-control"
                                                        onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                        <option value="">--select-- </option>
                                                        <option value="yes">हाँ</option>
                                                        <option value="no">नहीं</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                            <!-------4th start------->
                                            <div class="step">  
                                                <h4><img src="images/logo/4.png" alt="text" class="img-fluid stat-icon"
                                                style="height:50px; width:50px;"> 4. अन्य कार्य व व्यवसाय</h4>
                                                <div class="col-sm-12">
                                                    <?php
                                                    $count = !empty($row_2_1_2['count']) ? (int)$row_2_1_2['count'] : 1;
                                                    for ($i = 1; $i <= $count; $i++) {
                                                        ?>
                                                    <div class="row">
                                                        <div class="col-sm-3 form-group">
                                                            <label>व्यवसाय का विवरण </label>
                                                            <input type="text"
                                                                name="sec_2_1_2_business_description_<?php echo $i; ?>"
                                                                id="sec_2_1_2_business_description_<?php echo $i; ?>"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_2_1_2['sec_2_1_2_business_description_' . $i]; ?>">
                                                        </div>

                                                        <div class="col-sm-3 form-group">
                                                            <label>वार्षिक टर्नोवर</label>
                                                            <input type="text" name="sec_2_1_2_value_<?php echo $i; ?>"
                                                                id="sec_2_1_2_value_<?php echo $i; ?>"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_2_1_2['sec_2_1_2_value_' . $i]; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>लाभ/हानि</label>
                                                            <select name="sec_1_profit_loss_2022" id="sec_1_profit_loss_2022"
                                                                tabindex="4" class="form-control"
                                                                onchange="color_change(this, 'profit', '#42ecf5', 'loss', '#f28546');">
                                                                <option value="">--Select--</option>
                                                                <option value="profit">लाभ</option>
                                                                <option value="loss">हानि</option>
                                                            </select>
                                                        </div>
                                                        <?php
                                                        if ($i == $row_2_1_2['count']) {
                                                            ?>
                                                        <div class="col-sm-2 form-group my-auto" id="add_business_row">
                                                            <button type="button" class="btn btn-info"
                                                                onClick="add_more_business();">नईं पंक्ति
                                                                जोड़े [+]</button>
                                                            <input type="hidden" name="other_business_id" id="other_business_id"
                                                                value="<?php echo $row_2_1_2['count']; ?>">
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                    <?php } ?>
                                                </div>
                                            </div>   
                                            <!-------5th start------->
                                            
                                            <div class="step">                                            
                                                <div class="col-sm-12">
                                                    <h5><img src="images/logo/6.png" alt="text" class="img-fluid stat-icon"
                                                            style="height:50px; width:50px;"> (I) मानव सम्पदा</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                            <tr>
                                                                <th>क्रं.</th>
                                                                <th>पद</th>
                                                                <th>नाम</th>
                                                                <th>पिता का नाम</th>
                                                                <th>पता</th>
                                                                <th>जन्मतिथि</th>
                                                                <th>शैक्षिक योग्यता</th>
                                                                <th>कंप्युटर अनुभव</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>1.</td>
                                                                    <td>सभापति</td>
                                                                    <td><input type="text" class="form-control" name="" id=""
                                                                            value=""></td>
                                                                    <td><input type="text" class="form-control" name="" id=""
                                                                            value=""></td>
                                                                    <td><input type="text" class="form-control" name="" id=""
                                                                             value=""></td>
                                                                    <td><input type="text" class="form-control" name="" id=""
                                                                            value=""></td>
                                                                    <td><input type="text" class="form-control" name="" id=""
                                                                            value=""></td>
                                                                    <td><input type="text" class="form-control" name="" id=""
                                                                             value=""></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>2.</td>
                                                                    <td>सचिव</td>
                                                                    <td><input type="text" class="form-control" name="" id=""
                                                                            value=""></td>
                                                                    <td><input type="text" class="form-control" name="" id=""
                                                                            value=""></td>
                                                                    <td><input type="text" class="form-control" name="" id=""
                                                                             value=""></td>
                                                                    <td><input type="text" class="form-control" name="" id=""
                                                                            value=""></td>
                                                                    <td><input type="text" class="form-control" name="" id=""
                                                                            value=""></td>
                                                                    <td><input type="text" class="form-control" name="" id=""
                                                                             value=""></td>
                                                                </tr>                                                               
                                                            </tbody>
                                                        </table>
                                                        <!-- </div> -->
                                                    </div>

                                                    <br>
                                                    <h5><img src="images/logo/7.png" alt="text" class="img-fluid stat-icon"
                                                            style="height:50px; width:50px;"> (II) प्रबंध कमेटी / संचालक मण्डल</h5>
                                                    <div class="row">
                                                        <div class="col-md-3"><label for="">प्रबंध कमेटी / संचालक मण्डल?</label>
                                                            <select name="" id="" class="form-control"
                                                                onChange="hide_show(this.value, '#guard_count123', '0');hide_show(this.value, '#guard_count2', '0');hide_show(this.value, '#guard_count3', '0');">
                                                                <option value="">--Select--</option>
                                                                <option value="0">निर्वाचित है</option>
                                                                <option value="1">प्रशासनिक कमेटी</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-3 form-group" id="guard_count123" style="display:none;">
                                                            <label>निर्वाचन का वर्ष</label>
                                                            <select name="sec2_balance_sheet" id="sec2_balance_sheet"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control">
                                                                <option value="">--Select--</option>
                                                                <?php
                                                                for ($i = 2022; $i >= 1975; $i--) {
                                                                    echo '<option value="' . $i . '" ';
                                                                    if ($i == $row_2_1['balance_sheet_year']) {
                                                                        echo ' selected="selected" ';
                                                                    }
                                                                    echo ' >' . $i . '</option>';
                                                                }
                                                                ?>
                                                                <option value="old" <?php echo $row_2_1['balance_sheet_year'] == 'old' ? ' selected="selected"' : '' ?>>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        
                                                        <div class="col-sm-2 form-group">
                                                            <label>पदनाम</label>
                                                            <select class="form-control " type="checkbox" id="sec_2_guard"
                                                                name="sec_2_guard" value="yes" tabindex="<?php echo $tab++; ?>">
                                                                <option value="">--Select--</option>
                                                                <option value="yes" <?php $guard_display = 'none';
                                                                if ($row_2_2['guard'] != '') {
                                                                    echo ' selected="selected" ';
                                                                    $guard_display = 'block';
                                                                } ?>>
                                                                    अध्यक्ष</option>
                                                                <option value="no" <?php if ($row_2_2['guard'] == 'no') {
                                                                    echo ' selected="selected" ';
                                                                    $guard_display = 'none';
                                                                } ?>>उपाध्यक्ष
                                                                </option>
                                                                <option value="no" <?php if ($row_2_2['guard'] == 'no') {
                                                                    echo ' selected="selected" ';
                                                                    $guard_display = 'none';
                                                                } ?>>संचालक
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-2 form-group">
                                                            <label>नाम</label>
                                                            <input type="text" name="sec_2_guard_count" id="sec_2_guard_count"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                tabindex="<?php echo $tab++; ?>" value="">
                                                        </div>                                                        
                                                        <div class="col-sm-2 form-group" id="guard_count">
                                                            <label>पिता का नाम</label>
                                                            <input type="text" name="sec_2_guard_count" id="sec_2_guard_count"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                tabindex="<?php echo $tab++; ?>" value="">
                                                        </div>
                                                        <div class="col-sm-2 form-group" id="guard_count">
                                                            <label>मोबाईल नंबर</label>
                                                            <input type="text" name="sec_2_guard_count" id="sec_2_guard_count"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                tabindex="<?php echo $tab++; ?>" value="">
                                                        </div>
                                                        <div class="col-sm-2 form-group">
                                                            <label>आधार नंबर</label>
                                                            <input type="text" name="sec_2_guard_count" id="sec_2_guard_count"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                tabindex="<?php echo $tab++; ?>" value="">
                                                        </div>
                                                        <div class="col-sm-2 form-group">
                                                            <button type="button" class="btn btn-info"
                                                                onclick="sec_3_b_add_rows()">नई
                                                                पंक्ति जोड़े [+]</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--------------------------------------------------------------->
                                            <div class="step">
                                    <h4><img src="images/logo/11.png" alt="text" class="img-fluid stat-icon"
                                            style="height:50px; width:50px;"> 7. समिति भवन/सम्पत्ति का विवरण</h2>
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="col-sm-3 form-group">
                                                        <label>(I)समिति भवन का स्वामित्व </label>
                                                        <select name="sec_3_ownership" id="sec_3_ownership"
                                                            tabindex="<?php echo $tab++; ?>" class="form-control"
                                                            onChange="hide_show(this.value, '#sec_3_rented', 'rent'); hide_show(this.value, '#sec_3_other', 'other');">
                                                            <option value="">--Select--</option>
                                                            <option value="own" <?php $sec_3_rented_display = 'none';
                                                            $sec_3_other_display = 'none';
                                                            $sec_3_display = 'flex';
                                                            if ($row_3_1['sec_3_ownership'] == 'own') {
                                                                echo ' selected="selected" ';
                                                                $sec_3_display = 'flex';
                                                            } ?>
                                                                >समिति
                                                                के
                                                                स्वामित्व में है</option>
                                                            <option value="rent" <?php if ($row_3_1['sec_3_ownership'] == 'rent') {
                                                                echo ' selected="selected" ';
                                                                $sec_3_rented_display = 'flex';
                                                            } ?>>
                                                                किराये पर है</option>
                                                            <option value="other" <?php if ($row_3_1['sec_3_ownership'] != 'rent' && $row_3_1['sec_3_ownership'] != 'own' && $row_3_1['sec_3_ownership'] != '') {
                                                                echo ' selected="selected" ';
                                                                $sec_3_other_display = 'flex';
                                                            } ?>
                                                                >
                                                                अन्य स्थिती</option>
                                                        </select>
                                                    </div>
                                                    <div id="sec_3_rented"
                                                        style="display: <?php echo $sec_3_rented_display; ?>">
                                                        <div class="col-sm-3 form-group">
                                                            <label>समिति भवन का मासिक किराया </label>
                                                            <input name="sec_3_building_rent" id="sec_3_building_rent"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_invoice['society_building_rent_amount']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>समिति भवन का क्षेत्रफल (स्क्वायर मीटर
                                                                में)</label>
                                                            <input name="sec_3_building_area" id="sec_3_building_area"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_invoice['society_building_area']; ?>">
                                                        </div>

                                                    </div>
                                                    <div id="sec_3_other"
                                                        style="display: <?php echo $sec_3_other_display; ?>">
                                                        <div class="col-sm-3 form-group">
                                                            <label>कृपया विवरण दर्ज करें</label>
                                                            <input name="sec_3_building_rent1" id="sec_3_building_rent1"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_invoice['society_building_rent_amount']; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" id="sec_3" style="display: <?php echo $sec_3_display; ?>;">
                                                <div class="col-sm-12">
                                                    <h5> (I) भूखंड स्वामित्व का विवरण </h5>
                                                    <div class="row">
                                                        <div class="col-sm-3 form-group">
                                                            <label>भूखण्ड स्वामित्व की स्थिति</label>

                                                            <select name="sec_3_a_land_length" id="sec_3_a_land_length"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                onChange="show_sides_of_land(this.value);">
                                                                <option value="">--Select--</option>
                                                                <option value="3" <?php echo $row_3_1['number_of_sides'] == '3' ? ' selected="selected" ' : ''; ?>>खुद के स्वामित्व
                                                                    में
                                                                </option>
                                                                <option value="4" <?php echo $row_3_1['number_of_sides'] == '4' ? ' selected="selected" ' : ''; ?>>पट्टे पर</option>
                                                                <option value="5" <?php echo $row_3_1['number_of_sides'] == '5' ? ' selected="selected" ' : ''; ?>>अन्य</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row" id="sec_3" style="display: <?php echo $sec_3_display; ?>;">
                                                <div class="col-sm-12">
                                                    <h5> (II) भूखंड का विवरण </h5>
                                                    <div class="row">
                                                        <!-- <div class="col-sm-3 form-group">
                                                            <label>भूखण्ड में भुजा कि संख्या</label>
                                                            <label><small>(उदाहरण के लिये - यदि भूखण्ड आयताकार है तो
                                                                    भुजाओं
                                                                    कि संख्या 4 लिखें)</small></label>
                                                            <select name="sec_3_a_land_length" id="sec_3_a_land_length"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                onChange="show_sides_of_land(this.value);">
                                                                <option value="">--Select--</option>
                                                                <option value="3" <?php echo $row_3_1['number_of_sides'] == '3' ? ' selected="selected" ' : ''; ?>>3</option>
                                                                <option value="4" <?php echo $row_3_1['number_of_sides'] == '4' ? ' selected="selected" ' : ''; ?>>4</option>
                                                                <option value="5" <?php echo $row_3_1['number_of_sides'] == '5' ? ' selected="selected" ' : ''; ?>>5</option>
                                                                <option value="6" <?php echo $row_3_1['number_of_sides'] == '6' ? ' selected="selected" ' : ''; ?>>6</option>
                                                            </select>
                                                        </div> -->
                                                        <div class="col-sm-3 form-group">
                                                            <label>क्षेत्रफल (हेक्टेयर में)</label><br />
                                                            <label><small>&nbsp;</small></label>
                                                            <input type="text" name="sec_3_a_area" id="sec_3_a_area"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_3_1['total_area']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>राजस्व अभिलेख में दर्ज होने की स्थिति( हाँ
                                                                /नहीं)</label><br />
                                                            <label><small>&nbsp;</small></label>
                                                            <select name="sec_3_a_govt_records"
                                                                id="sec_3_a_govt_records"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                onChange="hide_show(this.value, '#land_records', 'no'); color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                                <option value="">--Select--</option>
                                                                <option value="yes" <?php $land_records_display = 'none';
                                                                if ($row_3_1['govt_records'] == 'yes') {
                                                                    echo ' selected="selected" ';
                                                                } ?>>हाँ</option>
                                                                <option value="no" <?php if ($row_3_1['govt_records'] != 'yes') {
                                                                    echo ' selected="selected" ';
                                                                    $land_records_display = 'block';
                                                                } ?>>नहीं
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-3 form-group" id="land_records"
                                                            style="display: <?php echo $land_records_display; ?>;">
                                                            <label>यदि नहीं है तो किये जाने वाले प्रयास का
                                                                विवरण</label>
                                                            <label><small>&nbsp;</small></label>
                                                            <input type="text" name="sec_3_a_if_yes" id="sec_3_a_if_yes"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_3_1['govt_records']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>गाटा/खसरा संख्या</label>
                                                            <input type="text" name="sec_3_a_gata" id="sec_3_a_gata"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_3_1['gata_no']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-2 form-group">
                                                            <label>समिति भूखण्ड फोटो संलग्न करें</label>
                                                            <input type="file" accept=".jpg, .jpeg, .gif, .png, .bmp"
                                                                name="sec_3_a_image" id="sec_3_a_image"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control">
                                                        </div>
                                                        <?php
                                                        if (!empty($row_3_1['new_photo_id']) && file_exists($row_3_1['new_photo_id'])) {
                                                            ?>
                                                        <div class="col-sm-2 form-group">
                                                            <img src="<?php echo $row_3_1['new_photo_id']; ?>"
                                                                class="img-fluid img-thumbnail" style="height:50px;"
                                                                id="sec_3_a_image_uploaded">
                                                            <label><a href="<?php echo $row_3_1['new_photo_id']; ?>"
                                                                    target="_blank">संलग्न फोटो देखें</a></label>

                                                        </div>
                                                        <?php
                                                        }
                                                        ?>
                                                        <div class="col-sm-3 form-group">
                                                            <label>टिप्पणी</label>
                                                            <input type="text" name="sec_3_a_comment"
                                                                id="sec_3_a_comment" tabindex="<?php echo $tab++; ?>"
                                                                class="form-control"
                                                                value="<?php echo $row_3_1['remarks']; ?>">
                                                        </div>

                                                    </div>
                                                    <?php
                                                    if (empty($sides_data)) {
                                                        ?>
                                                    <div id="sides_display" style="display: none;" class="row">

                                                    </div>
                                                    <?php
                                                    } else {
                                                        $col = ceil(12 / sizeof($sides_data));
                                                        ?>
                                                    <div id="sides_display" class="row">
                                                        <?php
                                                        $i = 1;
                                                        foreach ($sides_data as $k => $v) {
                                                            ?>
                                                        <div class="col-sm-<?php echo $col; ?> form-group">
                                                            <label>भुजा
                                                                <?php echo $i; ?> की लम्बाई
                                                            </label>
                                                            <input type="text" name="sec_3_a_side_<?php echo $i; ?>"
                                                                id="sec_3_a_<?php echo $i; ?>" class="form-control"
                                                                value="<?php echo $v; ?>">
                                                        </div>

                                                        <?php
                                                        $i++;
                                                        }
                                                        ?>
                                                    </div>
                                                    <?php
                                                    }
                                                    ?>
                                                    <h5> (III) भूखंड की चौहद्दी का विवरण </h5>
                                                    <div class="row">
                                                        <div class="col-sm-3 form-group">
                                                            <label>भूखण्ड की पूर्व दिशा का विवरण</label>
                                                            <input type="text" name="sec_3_a_land_chauhaddi_east"
                                                                id="sec_3_a_land_chauhaddi_east"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_3_1['east_side']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>भूखण्ड की पश्चिम दिशा का विवरण</label><input
                                                                type="text" name="sec_3_a_land_chauhaddi_west"
                                                                id="sec_3_a_land_chauhaddi_west"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_3_1['west_side']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>भूखण्ड की उत्तर दिशा का विवरण</label><input
                                                                type="text" name="sec_3_a_land_chauhaddi_north"
                                                                id="sec_3_a_land_chauhaddi_north"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_3_1['north_side']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>भूखण्ड की दक्षिण दिशा का विवरण</label><input
                                                                type="text" name="sec_3_a_land_chauhaddi_south"
                                                                id="sec_3_a_land_chauhaddi_south"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_3_1['south_side']; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-3 form-group">
                                                            <label>सड़क पर भूमि कि लम्बाई (आन रोड जमीन) मीटर
                                                                में</label>
                                                            <input type="text" name="sec_3_a_land_on_road"
                                                                id="sec_3_a_land_on_road"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_3_1['on_road_land']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>प्रमुख द्वार कि दिशा (फ्र्न्ट साईड)</label>
                                                            <select name="sec_3_a_land_frontage"
                                                                id="sec_3_a_land_frontage"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control">
                                                                <option value="east" <?php if ($row_3_1['front_side'] == 'east') {
                                                                    echo 'selected="selected"';
                                                                } ?>>पूर्व</option>
                                                                <option value="west" <?php if ($row_3_1['front_side'] == 'west') {
                                                                    echo 'selected="selected"';
                                                                } ?>>पश्चिम</option>
                                                                <option value="north" <?php if ($row_3_1['front_side'] == 'north') {
                                                                    echo 'selected="selected"';
                                                                } ?>>उत्तर</option>
                                                                <option value="south" <?php if ($row_3_1['front_side'] == 'south') {
                                                                    echo 'selected="selected"';
                                                                } ?>>दक्षिण</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <h5> (IV) निर्मित भवन का विवरण </h5>
                                                    <div id="sec_3_b">
                                                        <?php
                                                        $count_3b = !empty($row_3_3['count']) ? (int)$row_3_3['count'] : 1;
                                                        for ($i = 1; $i <= $count_3b; $i++) {
                                                            ?>
                                                        <div class="row">
                                                            <div class="col-sm-2 form-group">
                                                                <label>लंबाई (मीटर में)</label>
                                                                <input type="text"
                                                                    name="sec_3_b_length_<?php echo $i; ?>"
                                                                    id="sec_3_b_length_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_b_length_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-2 form-group">
                                                                <label>चौड़ाई (मीटर में)</label>
                                                                <input type="text"
                                                                    name="sec_3_b_width_<?php echo $i; ?>"
                                                                    id="sec_3_b_width_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_b_width_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-2 form-group">
                                                                <label>भवन का प्रकार</label>
                                                                <select
                                                                    name="sec_3_b_type_of_construction_<?php echo $i; ?>"
                                                                    id="sec_3_b_type_of_construction_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control">
                                                                    <option value="">--Select--</option>
                                                                    <?php
                                                                    $sql = 'select * from master_type_of_construction';
                                                                    $result_const = execute_query($sql);
                                                                    while ($row_const = mysqli_fetch_assoc($result_const)) {
                                                                        echo '<option value="' . $row_const['sno'] . '" ';
                                                                        if ($row_const['sno'] == $row_3_3['sec_3_b_type_of_construction_' . $i]) {
                                                                            echo ' selected="selected" ';
                                                                        }
                                                                        echo '>' . $row_const['type_of_construction'] . '</option>';
                                                                    }

                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2 form-group">
                                                                <label>किस फण्ड से बना है</label>
                                                                <select name="sec_3_b_type_of_fund_<?php echo $i; ?>"
                                                                    id="sec_3_b_type_of_fund_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control">
                                                                    <option value="">--Select--</option>
                                                                    <?php
                                                                    $sql = 'select * from master_type_of_fund';
                                                                    $result_const = execute_query($sql);
                                                                    while ($row_const = mysqli_fetch_assoc($result_const)) {
                                                                        echo '<option value="' . $row_const['sno'] . '" ';
                                                                        if ($row_const['sno'] == $row_3_3['sec_3_b_type_of_fund_' . $i]) {
                                                                            echo ' selected="selected" ';
                                                                        }
                                                                        echo '>' . $row_const['type_of_fund'] . '</option>';
                                                                    }

                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2 form-group">
                                                                <label>टिप्पणी</label>
                                                                <input type="text"
                                                                    name="sec_3_b_comment_<?php echo $i; ?>"
                                                                    id="sec_3_b_comment_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_3['sec_3_b_comment_' . $i]; ?>">
                                                            </div>
                                                            <?php
                                                            if ($i == $row_3_3['count']) {
                                                                ?>

                                                            <div class="col-sm-2 form-group my-auto" id="sec_3_b_rows">
                                                                <button type="button" class="btn btn-info"
                                                                    onClick="sec_3_b_add_rows()">नई पंक्ति जोड़े
                                                                    [+]</button>
                                                                <input type="hidden" name="sec_3_b_id" id="sec_3_b_id"
                                                                    value="<?php echo $row_3_3['count']; ?>">
                                                            </div>
                                                            <?php } ?>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                    <h5> (V) खाली पड़ी भूमि का विवरण </h5>
                                                    <div id="sec_3_c">
                                                        <?php
                                                        $count_3c = !empty($row_3_5['sec_3_c_id']) ? (int)$row_3_5['sec_3_c_id'] : 1;
                                                        for ($i = 1; $i <= $count_3c; $i++) {

                                                            ?>
                                                        <div class="row">
                                                            <div class="col-sm-2 form-group">
                                                                <label>क्षेत्रफल (हेक्टेयर में)</label>
                                                                <input type="text"
                                                                    name="sec_3_c_length_<?php echo $i; ?>"
                                                                    id="sec_3_c_length_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    value="<?php echo $row_3_5['sec_3_c_length_' . $i]; ?>">
                                                            </div>
                                                            <div class="col-sm-2 form-group">
                                                                <label>भूमि की स्थिति (उपजाऊ /बंजर)</label>
                                                                <select
                                                                    name="sec_3_c_vacant_land_status_<?php echo $i; ?>"
                                                                    id="sec_3_c_vacant_land_status_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control">
                                                                    <option value="">--select-- </option>
                                                                    <option value="fertile" <?php if ($row_3_5['sec_3_c_vacant_land_status_' . $i] == 'fertile') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>
                                                                        उपजाऊ </option>
                                                                    <option value="barren" <?php if ($row_3_5['sec_3_c_vacant_land_status_' . $i] == 'barren') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>
                                                                        बंजर </option>
                                                                </select>
                                                            </div>

                                                            <div class="col-sm-2 form-group">
                                                                <label>स्थान (समिति प्रांगण या अन्य स्थान)*</label>
                                                                <select name="sec_3_c_land_location_<?php echo $i; ?>"
                                                                    id="sec_3_c_land_location_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    onChange="hide_show(this.value, '#land_connectivity1', 'other'); hide_show(this.value, '#land_access_road', 'na');">
                                                                    <option value="">--select-- </option>
                                                                    <option value="inpremise" <?php $land_location_display = 'none';
                                                                    if ($row_3_5['sec_3_c_land_location_' . $i] == 'inpremise') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>समिति प्रांगण
                                                                    </option>
                                                                    <option value="other" <?php if ($row_3_5['sec_3_c_land_location_' . $i] == 'other') {
                                                                        echo ' selected="selected"';
                                                                        $land_location_display = 'block';
                                                                    } ?>>अन्य
                                                                        स्थान
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2  form-group">
                                                                <label>गोदाम के लिए उपयुक्त है या नहीं ?</label>
                                                                <select class="form-control " type="checkbox"
                                                                    value="yes" id="sec_2_accountant"
                                                                    name="sec_2_accountant"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    onChange="hide_show(this.value, '#accountant_count', 'yes')">
                                                                    <option value="">--Select--</option>
                                                                    <option value="yes" <?php $accountant_display = 'none';
                                                                    if ($row_2_2['accountant'] != '') {
                                                                        echo ' selected="selected" ';
                                                                        $accountant_display = 'block';
                                                                    } ?>>है
                                                                    </option>
                                                                    <option value="no" <?php if ($row_2_2['accountant'] == 'no') {
                                                                        echo ' selected="selected" ';
                                                                        $accountant_display = 'none';
                                                                    } ?>>नहीं
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2  form-group">
                                                                <label>जनपद से रैक की दूरी</label>
                                                                <input type="text" class="form-control">
                                                            </div>
                                                            <div class="col-sm-2 form-group"
                                                                id="land_connectivity<?php echo $i; ?>"
                                                                style="display: <?php echo $land_location_display; ?>">
                                                                <label>संपर्क मार्ग*</label>
                                                                <select name="sec_3_c_approach_road_<?php echo $i; ?>"
                                                                    id="sec_3_c_approach_road_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control"
                                                                    onChange="hide_show(this.value, '#land_access_road<?php echo $i; ?>', 'proper');">
                                                                    <option value="">--select-- </option>
                                                                    <option value="ordinary" <?php $approach_road_display = 'none';
                                                                    if ($row_3_5['sec_3_c_approach_road_' . $i] == 'ordinary') {
                                                                        echo ' selected="selected"';
                                                                    } ?>
                                                                        >कच्ची सड़क </option>
                                                                    <option value="proper" <?php if ($row_3_5['sec_3_c_approach_road_' . $i] == 'proper') {
                                                                        echo ' selected="selected"';
                                                                        $approach_road_display = 'block';
                                                                    } ?>>पक्की
                                                                        सड़क
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-2 form-group"
                                                                id="land_access_road<?php echo $i; ?>"
                                                                style="display: <?php echo $approach_road_display; ?>">
                                                                <label>पक्की सड़क का प्रकार</label>
                                                                <select name="sec_3_c_paved_road_<?php echo $i; ?>"
                                                                    id="sec_3_c_paved_road_<?php echo $i; ?>"
                                                                    tabindex="<?php echo $tab++; ?>"
                                                                    class="form-control">
                                                                    <option value="">--select-- </option>
                                                                    <option value="nh" <?php if ($row_3_5['sec_3_c_paved_road_' . $i] == 'nh') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>नेशनल
                                                                        हाईवे</option>
                                                                    <option value="sh" <?php if ($row_3_5['sec_3_c_paved_road_' . $i] == 'sh') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>स्टेट
                                                                        हाईवे</option>
                                                                    <option value="mdr" <?php if ($row_3_5['sec_3_c_paved_road_' . $i] == 'mdr') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>
                                                                        एम.डी.आर.</option>
                                                                    <option value="odr" <?php if ($row_3_5['sec_3_c_paved_road_' . $i] == 'odr') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>
                                                                        ओ.डी.आर.</option>
                                                                    <option value="rural_road" <?php if ($row_3_5['sec_3_c_paved_road_' . $i] == 'rural_road') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>ग्रामीण सड़क
                                                                    </option>
                                                                    <option value="other" <?php if ($row_3_5['sec_3_c_paved_road_' . $i] == 'other') {
                                                                        echo ' selected="selected"';
                                                                    } ?>>अन्य
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <?php
                                                            if ($i == $row_3_5['sec_3_c_id']) {
                                                                ?>
                                                            <div class="col-sm-2 form-group my-auto" id="sec_3_c_rows">
                                                                <button type="button" class="btn btn-info"
                                                                    onClick="sec_3_c_add_rows();">नई पंक्ति
                                                                    जोड़े</button>
                                                                <input type="hidden" name="sec_3_c_id" id="sec_3_c_id"
                                                                    value="<?php echo $row_3_5['sec_3_c_id']; ?>">
                                                            </div>
                                                            <?php } ?>
                                                        </div>
                                                        <?php } ?>

                                                    </div>

                                                    <h5>(VI) पहुंच मार्ग का विवरण</h5>
                                                    <div class="row">
                                                        <div class="col-sm-4 form-group">
                                                            <label>पहुंच मार्ग -</label>
                                                            <select name="sec_6_access_road" id="sec_6_access_road"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                onChange="hide_show(this.value, '#access_road', 'proper'); hide_show(this.value, '#access_road_truck', 'ordinary');">
                                                                <option value="">--select-- </option>
                                                                <option value="ordinary" <?php $access_road_display = 'none';
                                                                $access_road_truck = 'none';
                                                                if ($row_2_1['sec_6_access_road'] == 'ordinary') {
                                                                    echo 'selected="selected"';
                                                                    $access_road_truck = 'block';
                                                                } ?>>
                                                                    कच्ची
                                                                    सडक </option>
                                                                <option value="proper" <?php if ($row_2_1['sec_6_access_road'] == 'proper') {
                                                                    echo 'selected="selected"';
                                                                    $access_road_display = 'block';
                                                                } ?>>
                                                                    पक्की सडक</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-4 form-group" id="access_road"
                                                            style="display: <?php echo $access_road_display; ?>">
                                                            <label>पक्की सड़क का प्रकार</label>
                                                            <select name="sec_6_paved_road" id="sec_6_paved_road_road"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control">
                                                                <option value="">--select-- </option>
                                                                <option value="nh" <?php if ($row_2_1['sec_6_access_road'] == 'nh') {
                                                                    echo 'selected="selected"';
                                                                } ?>>नेशनल हाईवे</option>
                                                                <option value="sh" <?php if ($row_2_1['sec_6_access_road'] == 'sh') {
                                                                    echo 'selected="selected"';
                                                                } ?>>स्टेट हाईवे</option>
                                                                <option value="mdr" <?php if ($row_2_1['sec_6_access_road'] == 'mdr') {
                                                                    echo 'selected="selected"';
                                                                } ?>>एम.डी.आर.</option>
                                                                <option value="odr" <?php if ($row_2_1['sec_6_access_road'] == 'odr') {
                                                                    echo 'selected="selected"';
                                                                } ?>>ओ.डी.आर.</option>
                                                                <option value="rural_road" <?php if ($row_2_1['sec_6_access_road'] == 'rural_road') {
                                                                    echo 'selected="selected"';
                                                                } ?>>ग्रामीण सड़क
                                                                </option>
                                                                <option value="other" <?php if ($row_2_1['sec_6_access_road'] == 'other') {
                                                                    echo 'selected="selected"';
                                                                } ?>>अन्य</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-4 form-group" id="access_road_truck"
                                                            style="display: <?php echo $access_road_truck; ?>">
                                                            <label>यदि समिति भवन तक ट्रक नही पहुंचता है तो पक्के
                                                                मार्ग
                                                                से समिति
                                                                भवन की दूरी (की. मी. में)</label>
                                                            <input type="text" name="sec_6_2_truck_not_reach"
                                                                id="sec_6_2_truck_not_reach"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_2_1['sec_6_2_truck_not_reach']; ?>">
                                                        </div>
                                                    </div>
                                                    
                                                    
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                <!---------------7th Start---------------------------------------------------------------->
                                            <div class="step">
                                                <h5>(I)विद्युत कनेक्शन</h5>
                                                <div class="col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-4 form-group">
                                                            <label>विद्युत कनेक्शन</label>
                                                            <select name="sec_7_electrical_connection"
                                                                id="sec_7_electrical_connection" tabindex="<?php echo $tab++; ?>"
                                                                class="form-control"
                                                                onChange="hide_show(this.value, '#electricity_not_available', 'no'); hide_show(this.value, '#electricity_available', 'yes'); hide_show(this.value, '#sec_8_bill_paid1', 'yes'); hide_show(this.value, '#electricity_available_not_working', 'na');hide_show(this.value, '#sec_8_bill_paid2', 'no'); color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                                <option value="">--select-- </option>
                                                                <option value="yes" <?php $electricity_available_display = 'none';
                                                                $electricity_not_available_display = 'none';
                                                                if ($row_2_1['sec_7_electrical_connection'] == 'yes') {
                                                                    echo 'selected="selected"';
                                                                    $electricity_available_display = 'block';
                                                                } ?>>
                                                                    हाँ
                                                                </option>
                                                                <option value="no" <?php if ($row_2_1['sec_7_electrical_connection'] == 'no') {
                                                                    echo 'selected="selected"';
                                                                    $electricity_not_available_display = 'block';
                                                                } ?>>नहीं
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-3 form-group" id="electricity_available"
                                                            style="display: <?php echo $electricity_available_display; ?>">
                                                            <label>यदि है तो कार्यरत है या नहीं ?</label>
                                                            <select name="sec_7_electrical_connection_working"
                                                                id="sec_7_electrical_connection_working"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                onChange="hide_show(this.value, '#electricity_available_not_working', 'no'); color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                                <option value="">--select-- </option>
                                                                <option value="yes" <?php $electricity_available_not_working = 'none';
                                                                if ($row_2_1['sec_7_electrical_connection_working'] == 'yes') {
                                                                    echo 'selected="selected"';
                                                                } ?>>हाँ </option>
                                                                <option value="no" <?php if ($row_2_1['sec_7_electrical_connection_working'] == 'no') {
                                                                    echo 'selected="selected"';
                                                                    $electricity_available_not_working = 'block';
                                                                } ?>>नहीं
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-3 form-group" id="sec_8_bill_paid1"
                                                            style="display:none;">
                                                            <label>बिल पेड है या नहीं ?</label>
                                                            <select name="sec_8_bill_paid1" class="form-control"
                                                                onchange="hide_show(this.value, '#sec_7_bill_status', 'no'); color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                                <option value="">--select-- </option>
                                                                <option value="yes">हाँ</option>
                                                                <option value="no">नहीं</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-3 form-group" id="electricity_available_not_working"
                                                            style="display: <?php echo $electricity_available_not_working; ?>">
                                                            <label>यदि कार्यरत नहीं तो कारण</label>
                                                            <input type="text" name="sec_7_electrical_connection_notworking"
                                                                id="sec_7_electrical_connection_notworking"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_2_1['sec_7_electrical_connection_working']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group" id="electricity_not_available"
                                                            style="display: <?php echo $electricity_not_available_display; ?>">
                                                            <label>यदि नहीं तो प्रस्ताव</label>
                                                            <input type="text" name="sec_7_if_yes" id="sec_7_if_yes"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_2_1['sec_7_if_yes']; ?>">
                                                        </div>
                                                        <div class="col-sm-3 form-group" id="sec_8_bill_paid2"
                                                            style="display:none;">
                                                            <label>बिल पेड कितने माह से नहीं है ?</label>
                                                            <select name="sec_8_bill_paid1" class="form-control"
                                                                onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                                <option value="">--select-- </option>
                                                                <option value="YES">हाँ</option>
                                                                <option value="NO">नहीं</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-3 form-group" id="sec_7_bill_status"
                                                            style="display:none;">
                                                            <label>अगर बकाया है तो धनराशि लिखे</label>
                                                            <input type="text" name="sec_7_bill_status" id="sec_7_bill_status"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                value="<?php echo $row_2_1['sec_7_if_yes']; ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <h5>(II) सोलर कनेक्शन</h5>
                                                <div class="col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-4 form-group">
                                                            <label>गोदाम के लिए उपयुक्त है या नहीं ?</label>
                                                            <select class="form-control" value="yes" id="sec_2_accountant"
                                                                name="sec_2_accountant" tabindex="<?php echo $tab++; ?>"
                                                                onChange="hide_show(this.value, '#accountant_count', 'yes');hide_show(this.value, '#sec_8_roof_top', 'yes');hide_show(this.value, '#sec_8_kw', 'yes');hide_show(this.value, '#sec_8_date', 'yes');color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                                <option value="">--Select--</option>
                                                                <option value="yes" <?php $accountant_display = 'none';
                                                                if ($row_2_2['accountant'] != '') {
                                                                    echo ' selected="selected" ';
                                                                    $accountant_display = 'block';
                                                                } ?>>है</option>
                                                                <option value="no" <?php if ($row_2_2['accountant'] == 'no') {
                                                                    echo ' selected="selected" ';
                                                                    $accountant_display = 'none';
                                                                } ?>>नहीं
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-3 form-group" id="sec_8_roof_top"
                                                            style="display:<?php echo $accountant_display; ?>;">
                                                            <label>बिल पेड है या नहीं ?</label>
                                                            <select name="sec_8_roof_top" class="form-control"
                                                                onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                                <option value="">--select--</option>
                                                                <option value="yes">हैं</option>
                                                                <option value="no">नहीं</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-3 form-group" id="sec_8_kw"
                                                            style="display:<?php echo $accountant_display; ?>;">
                                                            <label>बिल पेड है या नहीं ?</label>
                                                            <select name="sec_8_kw" class="form-control">
                                                                <option value="">--select--</option>
                                                                <option value="yes">हैं</option>
                                                                <option value="no">नहीं</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-3 form-group" id="sec_8_date"
                                                            style="display:<?php echo $accountant_display; ?>;">
                                                            <label>बिल पेड है या नहीं ?</label>
                                                            <input type="date" name="sec_8_date" id="sec_8_date"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <h5>(III) इण्टरनेट कनेक्शन</h5>
                                                <div class="col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-4 form-group">
                                                            <label>इण्टरनेट कनेक्शन</label>
                                                            <select name="sec_8_internet_connection" id="sec_8_internet_connection"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                onChange="hide_show(this.value, '#net_con_available', 'yes'); hide_show(this.value, '#net_con_notavailable', 'no'); color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                                <option value="">--select--</option>
                                                                <option value="yes" <?php $net_con_available_display = 'none';
                                                                $net_con_notavailable_display = 'none';
                                                                if ($row_2_1['sec_8_internet_connection'] == 'yes') {
                                                                    echo 'selected="selected"';
                                                                    $net_con_available_display = 'block';
                                                                } ?>>
                                                                    हाँ
                                                                </option>
                                                                <option value="no" <?php if ($row_2_1['sec_8_internet_connection'] == 'no') {
                                                                    echo 'selected="selected"';
                                                                    $net_con_notavailable_display = 'block';
                                                                } ?>>
                                                                    नहीं
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-8" id="net_con_available"
                                                            style="display: <?php echo $net_con_available_display; ?>">
                                                            <div class="row">
                                                                <div class="col-sm-6 form-group">
                                                                    <label>यदि है तो सर्विस प्रोवाइडर का
                                                                        नाम</label>
                                                                    <select name="sec_8_if_yes" id="sec_8_if_yes"
                                                                        tabindex="<?php echo $tab++; ?>" class="form-control">
                                                                        <option value="">--Select--</option>
                                                                        <option value="bsnl" <?php if ($row_2_1['sec_8_if_yes'] == 'bsnl') {
                                                                            echo ' selected="selected"';
                                                                        } ?>>
                                                                            BSNL
                                                                        </option>
                                                                        <option value="jio" <?php if ($row_2_1['sec_8_if_yes'] == 'jio') {
                                                                            echo ' selected="selected"';
                                                                        } ?>>
                                                                            JIO
                                                                        </option>
                                                                        <option value="vodafone" <?php if ($row_2_1['sec_8_if_yes'] == 'vodafone') {
                                                                            echo ' selected="selected"';
                                                                        } ?>>
                                                                            Vodafone
                                                                        </option>
                                                                        <option value="airtel" <?php if ($row_2_1['sec_8_if_yes'] == 'airtel') {
                                                                            echo ' selected="selected"';
                                                                        } ?>>
                                                                            Airtel
                                                                        </option>
                                                                        <option value="sdwan" <?php if ($row_2_1['sec_8_if_yes'] == 'sdwan') {
                                                                            echo ' selected="selected"';
                                                                        } ?>>
                                                                            SDWAN
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-sm-6 form-group">
                                                                    <label>बिल पेड है या नहीं ?</label>
                                                                    <select name="sec_8_bill_paid" class="form-control">
                                                                        <option value="">--select-- </option>
                                                                        <option value="yes">हैं</option>
                                                                        <option value="no">नहीं</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4 form-group" id="net_con_notavailable"
                                                            style="display: <?php echo $net_con_notavailable_display; ?>">
                                                            <label>क्षेत्र में उपलब्ध ईण्टरनेट सर्विस प्रोवाइडर
                                                                के
                                                                नाम (सभी उपलब्ध आपरेटर का चयन करें)</label>
                                                            <select name="sec_6_select_operator[]" id="sec_6_select_operator"
                                                                tabindex="<?php echo $tab++; ?>" multiple="multiple"
                                                                class="form-control">
                                                                <?php
                                                                $internet_provider = explode(", ", $row_2_1['sec_8_if_yes']);
                                                                ?>
                                                                <option value="bsnl" <?php if (in_array('bsnl', $internet_provider)) {
                                                                    echo ' selected="selected"';
                                                                } ?>>
                                                                    BSNL</option>
                                                                <option value="jio" <?php if (in_array('jio', $internet_provider)) {
                                                                    echo ' selected="selected"';
                                                                } ?>>JIO
                                                                </option>
                                                                <option value="vodafone" <?php if (in_array('vodafone', $internet_provider)) {
                                                                    echo ' selected="selected"';
                                                                } ?>>
                                                                    Vodafone
                                                                </option>
                                                                <option value="airtel" <?php if (in_array('airtel', $internet_provider)) {
                                                                    echo ' selected="selected"';
                                                                } ?>>
                                                                    Airtel
                                                                </option>
                                                                <option value="sdwan" <?php if (in_array('sdwan', $internet_provider)) {
                                                                    echo ' selected="selected"';
                                                                } ?>>
                                                                    SDWAN
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h5>(IV) पेयजल की उपलब्धता</h5>
                                                <div class="col-sm-12">
                                                    <div class="row">
                                                        <div class="col-sm-3 form-group">
                                                            <label>सरकारी नलके का पानी</label>
                                                            <select name="sec_6_narrow_tubes" id="sec_6_narrow_tubes"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                color_change(this, 'yes' , '#42ecf5' , 'no' , '#f28546' );>
                                                                <option value="">--select-- </option>
                                                                <option value="yes" <?php echo $row_2_1['sec_6_narrow_tubes'] == 'yes' ? 'selected="selected"' : ''; ?>>
                                                                    हाँ </option>
                                                                <option value="no" <?php echo $row_2_1['sec_6_narrow_tubes'] == 'no' ? 'selected="selected"' : ''; ?>>
                                                                    नहीं</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-3 form-group">
                                                            <label>पानी कि टंकी</label>
                                                            <select name="sec_6_water_tank" id="sec_6_water_tank"
                                                                tabindex="<?php echo $tab++; ?>" class="form-control"
                                                                onchange="color_change(this, 'yes', '#42ecf5', 'no', '#f28546');">
                                                                <option value="">--select-- </option>
                                                                <option value="yes" <?php echo $row_2_1['sec_6_water_tank'] == 'yes' ? 'selected="selected"' : ''; ?>>
                                                                    हाँ </option>
                                                                <option value="no" <?php echo $row_2_1['sec_6_water_tank'] == 'no' ? 'selected="selected"' : ''; ?>>
                                                                    नहीं</option>
                                                            </select>
                                                        </div>
                                                        
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                <div id="success">
                                    <div class="mt-5 text-center">
                                        <h4>प्रपत्र सफलता पूर्वक भरा गया</h4>
                                        <p>आपने प्रपत्र सफलता पूर्वक भर लिया है । अब प्रपत्र को उच्चाधिकारी के पास
                                            सत्यापन हेतु भेजा जाना है । कृप्या सत्यापन पर भेजने से पहले नीचे दर्शायें
                                            लिंक से फार्म खोल कर पुनः जाच कर लें । सब कुछ सही होने कि दशा में नीचे दिये
                                            बटन के माध्यम से सत्यापन के लिये आगे प्रेषित करें ।</p>
                                        <button class="btn btn-info" onclick="window.open('preview.php','_blank');">प्रपत्र
                                            पुनः
                                            निरीक्षण के लिये
                                            देखे</button>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <p><input type="checkbox" style="height: 20px; border:1px solid;" id="review_ack"
                                                onClick="if($('#review_ack').prop('checked') == true){$('#verification_button').prop('disabled', false);}else{$('#verification_button').prop('disabled', true);}">
                                            मै एतत्द्वारा घोषित करता/करती हूं कि उपरोक्त प्रपत्र में भरी गयी सभी
                                            सूचनायें मेरी जानकारी अनुसार सत्य एवम सही है । </p>
                                        <button type="button" class="btn btn-danger" onClick="form_validate();"
                                            id="verification_button" disabled="disabled">सत्यापन के लिये आगे प्रेषित
                                            करें</button>
                                    </div>

                                    <div class="col-sm-12 form-group my-auto" id="send_otp_button1" style="display: none">
                                        <button type="button" name="send_otp_btn" id="send_otp_btn" tabindex="<?php echo $tab++; ?>"
                                            class="btn btn-info" onClick="send_otp($('#survey_id').val(), '');">ओ.टी.पी.
                                            भेजे</button>
                                    </div>
                                    <div class="col-sm-12 form-group" id="otp_verify" style="display: none">
                                        <div class="row">
                                            <div class="col-sm-4 form-group my-auto">
                                                <label>ओ.टी.पी. कोड दर्ज करें</label>
                                                <input type="text" class="form-control" id="user_otp">
                                            </div>
                                            <div class="col-sm-8 form-group my-auto">
                                                <button type="button" name="verify_otp_btn" id="verify_otp_btn"
                                                    tabindex="<?php echo $tab++; ?>" class="btn btn-info"
                                                    onClick="verify_otp($('#survey_id').val(), '', $('#user_otp').val());">वेरिफाई
                                                    करें</button>
                                                <button type="button" name="send_otp_btn" id="send_otp_btn"
                                                    tabindex="<?php echo $tab++; ?>" class="btn btn-info"
                                                    onClick="send_otp($('#survey_id').val(), '');">पुनः ओ.टी.पी.
                                                    भेजे</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="q-box__buttons">
                                <button id="prev-btn" class="btn btn-info" type="button" onClick="save_draft()">Previous</button>
                                <button id="next-btn" class="btn btn-success" type="button" onClick="save_draft()">Next</button>
                                <button id="submit-btn" class="btn btn-danger" type="submit" onClick="save_draft()">Submit</button>
                            </div>
                            <button class="btn btn-warning" type="button" onClick="save_draft()"><i class="fas fa-save"></i>
                                Save
                                Draft</button>
                            <input type="hidden" id="term" name="term" value="a">
                            <input type="hidden" id="latitude" name="latitude" value="<?php echo $row_invoice['latitude']; ?>">
                            <input type="hidden" id="longitude" name="longitude" value="<?php echo $row_invoice['longitude']; ?>">
                            <input type="hidden" id="id" name="id" value="submit_form">
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
                function add_more_business(val) {
                    var id = parseFloat($("#other_business_id").val());
                    if (!id) {
                        id = 0;
                    }
                    for (var i = 0; i <= id; i++) {
                        if ($("#sec_2_1_2_business_description_" + i).val() == '' || $("#sec_2_1_2_value_" + i).val() == '') {
                            alert("पंक्ति संख्या " + i + " खाली है");
                            $("#sec_2_1_2_business_description_" + i).focus();
                            return;
                        }
                    }
                    id = id + 1;
                    $("#add_business_row").remove();
                    var txt = '<div class="row"><div class="col-sm-3 form-group"><label>व्यवसाय का विवरण </label><input type="text" name="sec_2_1_2_business_description_' + id + '" id="sec_2_1_2_business_description_' + id + '" class="form-control"></div><div class="col-sm-3 form-group"><label>वार्षिक टर्नोवर </label><input type="text" name="sec_2_1_2_value_' + id + '" id="sec_2_1_2_value_' + id + '" class="form-control"></div><div class="col-sm-2 form-group my-auto" id="add_business_row"><button type="button" class="btn btn-info" onclick="add_more_business();">नईं पंक्ति जोड़े [+]</button><input type="hidden" name="other_business_id" id="other_business_id" value="' + id + '"></div></div>';
                    $("#other_business").append(txt);
                }

                function sec_3_b_add_rows() {
                    var id = parseFloat($("#sec_3_b_id").val());
                    if (!id) {
                        id = 0;
                    }
                    for (var i = 0; i <= id; i++) {
                        if ($("#sec_3_b_length_" + i).val() == '' || $("#sec_3_b_width_" + i).val() == '') {
                            alert("पंक्ति संख्या " + i + " खाली है");
                            $("#sec_3_b_length_" + i).focus();
                            return;
                        }
                    }
                    id = id + 1;
                    var const_options = $("#sec_3_b_type_of_construction_1").html();
                    var fund_options = $("#sec_3_b_type_of_fund_1").html();
                    $("#sec_3_b_rows").remove();

                    var txt = '<div class="row" id="sec_3_b"><div class="col-sm-2 form-group"><label>लंबाई (मीटर में)</label><input type="text" name="sec_3_b_length_' + id + '" id="sec_3_b_length_' + id + '" class="form-control"></div><div class="col-sm-2 form-group"><label>चौड़ाई (मीटर में)</label><input type="text" name="sec_3_b_width_' + id + '" id="sec_3_b_width_' + id + '" class="form-control"></div><div class="col-sm-2 form-group"><label>भवन का प्रकार</label><select name="sec_3_b_type_of_construction_' + id + '" id="sec_3_b_type_of_construction_' + id + '" class="form-control">' + const_options + '</select></div><div class="col-sm-2 form-group"><label>किस फण्ड से बना है</label><select name="sec_3_b_type_of_fund_' + id + '" id="sec_3_b_type_of_fund_' + id + '" class="form-control">' + fund_options + '</select></div><div class="col-sm-2 form-group"><label>टिप्पणी</label><input type="text" name="sec_3_b_comment_' + id + '" id="sec_3_b_comment_' + id + '" class="form-control"></div><div class="col-sm-2 form-group my-auto" id="sec_3_b_rows"><button type="button" class="btn btn-info" onClick="sec_3_b_add_rows()">नई पंक्ति जोड़े [+]</button><input type="hidden" name="sec_3_b_id" id="sec_3_b_id" value="' + id + '"></div></div>';
                    $("#sec_3_b").append(txt);
                }

                function sec_3_b_godown_add_rows() {
                    var id = parseFloat($("#sec_3_b_godown_id").val());
                    if (!id) {
                        id = 0;
                    }
                    for (var i = 0; i <= id; i++) {
                        if ($("#sec_3_b_godown_length_" + i).val() == '' || $("#sec_3_b_godown_width_" + i).val() == '') {
                            alert("पंक्ति संख्या " + i + " खाली है");
                            $("#sec_3_b_godown_length_" + i).focus();
                            return;
                        }
                    }
                    id = id + 1;
                    var fund_options = $("#sec_3_b_godown_type_of_fund_1").html();
                    $("#sec_3_b_godown_rows").remove();

                    var txt = '<div class="row"><div class="col-sm-2 form-group"><label>लंबाई (मीटर में)</label><input type="text" name="sec_3_b_godown_length_' + id + '" id="sec_3_b_godown_length_' + id + '" tabindex="" class="form-control" value=""></div>	<div class="col-sm-2 form-group"><label>चौड़ाई (मीटर में)</label><input type="text" name="sec_3_b_godown_width_' + id + '" id="sec_3_b_godown_width_' + id + '" tabindex="" class="form-control" value=""></div><div class="col-sm-2 form-group"><label>क्षमता (मेट्रिक टन में)</label><input type="text" name="sec_3_b_storage_capacity_' + id + '" id="sec_3_b_storage_capacity_' + id + '" tabindex="" class="form-control" value=""></div><div class="col-sm-2 form-group"><label>किस फण्ड से बना है</label><select name="sec_3_b_godown_type_of_fund_' + id + '" id="sec_3_b_godown_type_of_fund_' + id + '" class="form-control">' + fund_options + '</select></div><div class="col-sm-2 form-group"><label>गोदाम के निर्माण कि स्थिति</label><select name="sec_3_b_godown_status_' + id + '" id="sec_3_b_godown_status_' + id + '" tabindex="" class="form-control"><option value="">--select-- </option><option value="good">अच्छा</option><option value="repairable">खराब/मरम्मत योग्य</option><option value="discarded">जर्जर/निषप्रयोज्य</option></select></div><div class="col-sm-1 form-group"><label>टिप्पणी</label><input type="text" name="sec_3_b_godown_comment_' + id + '" id="sec_3_b_godown_comment_' + id + '" tabindex="" class="form-control" value=""></div><div class="col-sm-1 form-group my-auto" id="sec_3_b_godown_rows"><button type="button" class="btn btn-info" onclick="sec_3_b_godown_add_rows()">नई पंक्ति जोड़े [+]</button><input type="hidden" name="sec_3_b_godown_id" id="sec_3_b_godown_id" value="' + id + '"></div></div>';
                    $("#sec_3_b_godown").append(txt);
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

                    var txt = '<div class="row"><div class="col-sm-2 form-group"><label>क्षेत्रफल (हेक्टेयर में)</label><input type="text" name="sec_3_c_length_' + id + '" id="sec_3_c_length_' + id + '" lass="form-control"></div>	<div class="col-sm-2 form-group"><label>भूमि की स्थिति (उपजाऊ /बंजर)</label><select name="sec_3_c_vacant_land_status_' + id + '" id="sec_3_c_vacant_land_status_' + id + '" class="form-control"><option value="">--select-- </option><option value="उपजाऊ">उपजाऊ </option><option value="बंजर">बंजर </option></select></div><div class="col-sm-2 form-group"><label>स्थान (समिति प्रांगण या अन्य स्थान)*</label><select name="sec_3_c_land_location_' + id + '" id="sec_3_c_land_location_' + id + '" class="form-control" onChange="hide_show(this.value, \'#land_connectivity' + id + '\', \'other\'); hide_show(this.value, \'#land_access_road' + id + '\', \'na\');"><option value="">--select-- </option><option value="inpremise">समिति प्रांगण </option><option value="other">अन्य स्थान </option></select></div><div class="col-sm-2 form-group" id="land_connectivity' + id + '" style="display: none;"><label>संपर्क मार्ग*</label><select name="sec_3_c_approach_road_' + id + '" id="sec_3_c_approach_road_' + id + '" class="form-control" onChange="hide_show(this.value, \'#land_access_road' + id + '\', \'proper\');"><option value="">--select-- </option><option value="ordinary">कच्ची सड़क </option><option value="proper">पक्की सड़क </option></select></div><div class="col-sm-2 form-group" id="land_access_road' + id + '" style="display: none;"><label>पक्की सड़क का प्रकार</label><select name="sec_3_c_paved_road_' + id + '" id="sec_3_c_paved_road_' + id + '" class="form-control"><option value="">--select-- </option><option value="nh">नेशनल हाईवे</option><option value="sh">स्टेट हाईवे</option><option value="mdr">एम.डी.आर.</option><option value="odr">ओ.डी.आर.</option><option value="rural_road">ग्रामीण सड़क</option><option value="other">अन्य</option></select></div><div class="col-sm-2 form-group my-auto" id="sec_3_c_rows"><button type="button" class="btn btn-info" onClick="sec_3_c_add_rows();">नई पंक्ति जोड़े</button><input type="hidden" name="sec_3_c_id" id="sec_3_c_id" value="' + id + '"></div></div>';

                    $("#sec_3_c").append(txt);
                }

                function msc_services_other1() {
                    var disp = 0;
                    var msc_value = $("#sec_1_1_2_msc_service").val();
                    //console.log(msc_value);
                    $.each(msc_value, function (key, value) {
                        if (value == 'other') {
                            $("#msc_services_other").show();
                            disp = 1;
                        }
                    });
                    if (disp == 0) {
                        $("#msc_services_other").hide();
                    }
                }
                function msc_services_other() {
                    var disp = 0;
                    var msc_value = $("#sec_1_1_2_msc_service").val();
                    //console.log(msc_value);
                    $.each(msc_value, function (key, value) {
                        if (value == 'other') {
                            $("#msc_services_other").show();
                            disp = 1;
                        }
                    });
                    if (disp == 0) {
                        $("#msc_services_other").hide();
                    }
                }

                $('select[multiple]').multiselect({
                    columns: 1,
                    placeholder: 'Select options'
                });
            </script>
            <script>

                $(document).ready(function () {
                    //getLocation();
                });
            </script>

            <script>
                function hide_show(value, containerId, showValue) {
                    var testServicesContainer = document.querySelector(containerId);
                    if (Array.isArray(showValue)) {
                        if (showValue.includes(value)) {
                            testServicesContainer.style.display = 'block';
                        } else {
                            testServicesContainer.style.display = 'none';
                        }
                    }
                    else {
                        if (value === showValue) {
                            testServicesContainer.style.display = 'block';
                        } else {
                            testServicesContainer.style.display = 'none';
                        }
                    }
                }
                hide_show(document.getElementById('sec_1_1_2_test').value, '#test_services', 'yes');
                document.getElementById('sec_1_1_2_test').addEventListener('change', function () {
                    hide_show(this.value, '#test_services', 'yes');
                });
            </script>

            <script>
                function color_change(selectElement, yesValue, yesColor, noValue, noColor) {
                    if (selectElement.value === yesValue) {
                        selectElement.style.backgroundColor = yesColor;
                    } else if (selectElement.value === noValue) {
                        selectElement.style.backgroundColor = noColor;
                    } else {
                        selectElement.style.backgroundColor = 'white'; // Default background color
                    }
                }
                function toggleFields(value) {
                    const loanDetails = document.getElementById('loan_details');
                    if (value === 'yes') {
                        loanDetails.style.display = 'block';
                    } else {
                        loanDetails.style.display = 'none';
                    }
                }
            </script>

            <script>
                function addRow() {
                    var id = parseFloat($("#sec_3_row_count").val());
                    if (!id) {
                        id = 0;
                    }
                    for (var i = 0; i <= id; i++) {
                        if ($("#sec_3_cpmt_" + i).val() == '' || $("#sec_3_post_" + i).val() == '') {
                            alert("पंक्ति संख्या " + i + " खाली है");
                            $("#sec_3_cpmt_" + i).focus();
                            return;
                        }
                    }
                    id = id + 1;
                    $("#sec_3_add_rows").remove();

                    var txt = '<div class="row" id="row_' + id + '"><div class="col-sm-4"><label>नाम:-सहकारी प्रबंध प्रशिक्षण केंद्र</label><input name="sec_3_cpmt_' + id + '" id="sec_3_cpmt_' + id + '" class="form-control"></div><div class="col-sm-4"><label>पता</label><input name="sec_3_address_' + id + '" id="sec_3_address_' + id + '" class="form-control" ></div></div><div class="row"><div class="col-sm-4"><label>पदेन प्रधानाचार्य नाम</label><input name="sec_3_principal_name_' + id + '" id="sec_3_principal_name_' + id + '" class="form-control" ></div><div class="col-sm-4"><label>मूलपद</label><input name="sec_3_post_' + id + '" id="sec_3_post_' + id + '" class="form-control" ></div></div>	<div class="row"><div class="col-sm-4"><label>प्रधानाचार्य आवास</label><select name="sec_3_principal_house_' + id + '" id="sec_3_principal_house_' + id + '" class="form-control"onChange="hide_show(this.value, '#sec_3_building_rent', 'yes');"><option value="">--select-- </option><option value="">हाँ </option><option value="">नहीं </option></select></div><div class="col-sm-4"><label>संख्या</label><input name="sec_3_principal_house_no_' + id + '" id="sec_3_principal_house_no_' + id + '" class="form-control" ></div></div><div class="row"><div class="col-sm-4"><label>प्रधानाचार्य कार्यालय</label><select name="sec_3_principal_office_' + id + '" id="sec_3_principal_office_' + id + '"class="form-control"onChange="hide_show(this.value, '#sec_3_building_rent', 'yes');"><option value="">--select-- </option><option value="">हाँ </option><option value="">नहीं </option></select></div><div class="col-sm-4"><label>संख्या</label><input name="sec_3_principal_office_no_' + id + '" id="sec_3_principal_office_no_' + id + '" class="form-control" ></div></div>	<div class="row"><div class="col-sm-4"><label>कक्षा संख्या</label><input name="sec_3_class_no_' + id + '" id="sec_3_class_no_' + id + '" class="form-control" ></div><div class="col-sm-4"><label>क्षमता</label><input name="sec_3_class_capacity_' + id + '" id="sec_3_class_capacity_' + id + '" class="form-control" ></div></div><div class="row"><div class="col-sm-4"><label>हॉस्टल संख्या</label><input name="sec_3_hostel_no_' + id + '" id="sec_3_hostel_no_' + id + '" class="form-control" ></div><div class="col-sm-4"><label>क्षमता</label><input name="sec_3_hostel_capacity_' + id + '" id="sec_3_hostel_capacity_' + id + '" class="form-control" ></div></div><div class="row"><div class="col-sm-4"><label>पुस्तकालय संख्या</label><input name="sec_3_library_no_' + id + '" id="sec_3_library_no_' + id + '" class="form-control" ></div><div class="col-sm-4"><label>क्षमता</label><input name="sec_3_library_capacity_' + id + '" id="sec_3_library_capacity_' + id + '" class="form-control" ></div></div><div class="row"><div class="col-sm-4"><label>कंप्युटर लैब संख्या</label><input name="sec_3_computer_lab_no_' + id + '" id="sec_3_computer_lab_no_' + id + '" class="form-control" ></div><div class="col-sm-4"><label>क्षमता</label><input name="sec_3_computer_lab_capacity_' + id + '" id="sec_3_computer_lab_capacity_' + id + '" class="form-control" ></div></div><div class="row"><div class="col-sm-4"><label>अध्यापक / अतिथि प्रवक्ता संख्या</label><input name="sec_3_teacher_no_' + id + '" id="sec_3_teacher_no_' + id + '" class="form-control" ></div><div class="col-sm-4"><label>कर्मचारी विवरण</label><textarea name="sec_3_employee_remarks_' + id + '" id="sec_3_employee_remarks_' + id + '" class="form-control"></textarea></div></div><div class="row"><div class="col-sm-4"><label>प्रशिक्षण सत्रों की संख्या</label><input name="sec_3_training_sessions_no_' + id + '" id="sec_3_training_sessions_no_' + id + '" class="form-control"></div><div class="col-sm-4"><label>प्रशिक्षण विषय के नाम </label><input name="sec_3_training_subject_name_' + id + '" id="sec_3_training_subject_name_' + id + '" class="form-control"></div><div class="col-sm-3"><label>प्रशिक्षण सत्र अवधि</label><input type="date" name="sec_3_training_sessions_duration_' + id + '" id="sec_3_training_sessions_duration_' + id + '" class="form-control"></div></div><div class="row"><div class="col-sm-4"><label>विभागीय प्रशिक्षार्थियों की संख्या</label><input name="sec_3_departmental_trainees_no_' + id + '" id="sec_3_departmental_trainees_no_' + id + '" class="form-control"></div><div class="col-sm-4"><label>गैर-विभागीय प्रशिक्षार्थियों की संख्या</label><input name="sec_3_non_departmental_trainees_no_' + id + '" id="sec_3_non_departmental_trainees_no_' + id + '" class="form-control"></div><div class="col-sm-4"><label>प्रशिक्षार्थियों की संख्या</label><input name="sec_3_trainees_no_' + id + '" id="sec_3_trainees_no_' + id + '" class="form-control"></div></div><div class="row"><div class="col-sm-4"><label>विभागीय प्रशिक्षण शुल्क</label><input name="sec_3_departmental_trainees_fee_' + id + '" id="sec_3_departmental_trainees_fee_' + id + '" class="form-control"></div><div class="col-sm-4"><label>गैर-विभागीय प्रशिक्षण शुल्क</label><input name="sec_3_non_departmental_trainees_fee_' + id + '" id="sec_3_non_departmental_trainees_fee_' + id + '" class="form-control"></div><div class="col-sm-4"><label>प्रशिक्षण शुल्क</label><input name="sec_3_trainees_fee_' + id + '" id="sec_3_trainees_fee_' + id + '" class="form-control"></div></div><div class="row"><div class="col-sm-4"><label>विभागीय हॉस्टल शुल्क</label><input name="sec_3_departmental_hostel_fee_' + id + '" id="sec_3_departmental_hostel_fee_' + id + '" class="form-control"></div><div class="col-sm-4"><label>गैर-विभागीय हॉस्टल शुल्क</label><input name="sec_3_non_departmental_hostel_fee_' + id + '" id="sec_3_non_departmental_hostel_fee_' + id + '" class="form-control"></div><div class="col-sm-4"><label>हॉस्टल शुल्क</label><input name="sec_3_hostel_fee_' + id + '" id="sec_3_hostel_fee_' + id + '" class="form-control"></div></div><div class="row"><div class="col-sm-3"><label>निर्माण वर्ष</label><select name="sec2_santulan_patra" class="form-control"><option value="">--Select--</option><option value="">2024</option><option value="">2023</option><option value="">2022</option><option value="">2021</option><option value="">2020</option><option value="">2019</option><option value="">2018</option></select></div><div class="col-sm-3"><label>संचालन वर्ष</label><select name="sec2_santulan_patra" class="form-control"><option value="">--Select--</option><option value="">2024</option><option value="">2023</option><option value="">2022</option><option value="">2021</option><option value="">2020</option><option value="">2019</option><option value="">2018</option></select></div><div class="col-sm-3"><label>प्रशिक्षण कोर्स लाभ</label><textarea name="sec_3_training_course_benefits_' + id + '" id="sec_3_training_course_benefits_' + id + '" class="form-control"></textarea></div><div class="col-sm-3"><label>भवन/हॉस्टल स्तिथि</label><textarea name="sec_3_building_hostel_status_' + id + '" id="sec_3_building_hostel_status_' + id + '" class="form-control"></textarea></div></div><div class="col-sm-2 form-group my-auto" id="sec_3_add_rows"><button type="button" class="btn btn-info" onClick="addRow()">नई पंक्ति जोड़े [+]</button><input type="hidden" name="sec_3_row_count" id="sec_3_row_count" value="' + id + '"></div></div>';
                    $("#sec_3_b").append(txt);
                }
            </script>
            <script>
                function handleCountInput(input, type) {
                    const count = parseInt(input.value);
                    if (count > 11) {
                        alert('You cannot add more than 11 entries.');
                        input.value = '';
                        return;
                    }

                    const tableBody = document.getElementById('postTableBody');
                    const existingRows = document.querySelectorAll(`.${type}_dynamic`);

                    // Remove existing rows for the post type
                    existingRows.forEach(row => tableBody.removeChild(row));

                    // Remove the existing header if it exists
                    const existingHeader = document.getElementById(`${type}_header`);
                    if (existingHeader) {
                        tableBody.removeChild(existingHeader);
                    }

                    // Add the header for नाम, रिक्त पद, स्वीकृत पद
                    const headerRow = document.createElement('tr');
                    headerRow.id = `${type}_header`;
                    headerRow.innerHTML = `
                                            <th style="text-align: center;">पद</th>
                                            <th style="text-align: center;">नाम</th>
                                            <th style="text-align: center;">स्वीकृत पद</th>
                                            <th style="text-align: center;">रिक्त पद</th>
                                        `;
                    tableBody.insertBefore(headerRow, input.parentNode.parentNode.nextSibling);

                    // Add new rows based on the count
                    for (let i = 0; i < count; i++) {
                        const row = document.createElement('tr');
                        row.className = `${type}_dynamic`;
                        row.innerHTML = `
                                                <td>${type.toUpperCase()} ${i + 1}</td>
                                                <td><input type="text" name="name_${type}[]"></td>
                                                <td><input type="text" name="vacant_${type}[]"></td>
                                                <td><input type="text" name="sanctioned_${type}[]"></td>
                                            `;
                        tableBody.insertBefore(row, input.parentNode.parentNode.nextSibling);
                    }
                }

                function showTree() {
                    const nameMd = document.getElementById('name_md').value;
                    const mobileMd = document.getElementById('mobile_md').value;
                    const educationMd = document.getElementById('education_md').value;

                    const nameAmd = document.getElementById('name_amd').value;
                    const mobileAmd = document.getElementById('mobile_amd').value;
                    const educationAmd = document.getElementById('education_amd').value;

                    const nameCgm = document.getElementById('name_cgm').value;
                    const mobileCgm = document.getElementById('mobile_cgm').value;
                    const educationCgm = document.getElementById('education_cgm').value;

                    const gmNames = document.getElementsByName('name_gm[]');
                    const gmVacant = document.getElementsByName('vacant_gm[]');
                    const gmSanctioned = document.getElementsByName('sanctioned_gm[]');

                    const dgmNames = document.getElementsByName('name_dgm[]');
                    const dgmVacant = document.getElementsByName('vacant_dgm[]');
                    const dgmSanctioned = document.getElementsByName('sanctioned_dgm[]');

                    const agmNames = document.getElementsByName('name_agm[]');
                    const agmVacant = document.getElementsByName('vacant_agm[]');
                    const agmSanctioned = document.getElementsByName('sanctioned_agm[]');

                    let outputHtml = `
                                            <ul>
                                                <li>प्रबंध निदेशक- नाम:${nameMd}, Mobile: ${mobileMd}, Education: ${educationMd}</li>
                                                <li>उप-प्रबंध निदेशक- नाम:${nameAmd}, Mobile: ${mobileAmd}, Education: ${educationAmd}</li>
                                                <li>मुख्य महाप्रबंधक- नाम:${nameCgm}, Mobile: ${mobileCgm}, Education: ${educationCgm}</li>
                                                <li>महाप्रबंधक:
                                                    <ul>
                                        `;

                    for (let i = 0; i < gmNames.length; i++) {
                        outputHtml += `<li>नाम: ${gmNames[i].value}, रिक्त पद: ${gmVacant[i].value}, स्वीकृत पद: ${gmSanctioned[i].value}</li>`;
                    }

                    outputHtml += `
                                                    </ul>
                                                </li>
                                                <li>उप-महाप्रबंधक:
                                                    <ul>
                                        `;

                    for (let i = 0; i < dgmNames.length; i++) {
                        outputHtml += `<li>नाम: ${dgmNames[i].value}, रिक्त पद: ${dgmVacant[i].value}, स्वीकृत पद: ${dgmSanctioned[i].value}</li>`;
                    }

                    outputHtml += `
                                                    </ul>
                                                </li>
                                                <li>सहायक महाप्रबंधक:
                                                    <ul>
                                        `;

                    for (let i = 0; i < agmNames.length; i++) {
                        outputHtml += `<li>नाम: ${agmNames[i].value}, रिक्त पद: ${agmVacant[i].value}, स्वीकृत पद: ${agmSanctioned[i].value}</li>`;
                    }

                    outputHtml += `
                                                    </ul>
                                                </li>
                                            </ul>
                                        `;

                    document.getElementById('output').innerHTML = outputHtml;
                }

            </script>


            <script type="text/javascript" src="js/multistepform_industry.js?v=1">
                <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
                < script src="js/light-bootstrap-dashboard.js?v=1.4.0">
            </script>


<?php
page_footer_start();
?>