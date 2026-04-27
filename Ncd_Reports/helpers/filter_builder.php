<?php
/**
 * Filter Builder for Cooperatives
 * Builds WHERE clause for all cooperative filters
 */

function buildCooperativeFilters($request) {
    $where = " WHERE 1=1 ";
    
    // Authority ID (from dashboard)
    if (!empty($request['authority_id'])) {
        $authority_id = intval($request['authority_id']);
        if ($authority_id > 0) {
            $where .= " AND c.registration_authoritie_id = $authority_id";
        }
    }

    // 1. Reference Year (FIXED)
    if (!empty($request['reference_year'])) {
        $year = intval($request['reference_year']);
        $where .= " AND c.reference_year = $year";
    }
    
    // 2. Area of operations
    if (!empty($request['area_of_operation_id'])) {
        $area_id = intval($request['area_of_operation_id']);
        $where .= " AND c.area_of_operation_id = $area_id";
    }
    
    // 3. Water Body Type
    if (!empty($request['water_body_type_id'])) {
        $water_id = intval($request['water_body_type_id']);
        $where .= " AND c.water_body_type_id = $water_id";
    }
    
    // 4. Is Approved (Yes/No)
    if (isset($request['is_approved']) && $request['is_approved'] !== '') {
        $approved = intval($request['is_approved']);
        $where .= " AND c.is_approved = $approved";
    }
    
    // 5. Sector of operations Type
    if (!empty($request['sector_of_operation'])) {
        $sector_id = intval($request['sector_of_operation']);
        $where .= " AND c.sector_of_operation = $sector_id";
    }
    
    // 6. Functional Status (Functional/Non Functional)
    if (isset($request['functional_status']) && $request['functional_status'] !== '') {
        $status = intval($request['functional_status']);
        $where .= " AND c.functional_status = $status";
    }
    
    // 7. Full Time Secretary (Yes/No)
    if (isset($request['full_time_secretary']) && $request['full_time_secretary'] !== '') {
        $secretary = intval($request['full_time_secretary']);
        $where .= " AND c.full_time_secretary = $secretary";
    }
    
    // 8. Location of Head quarter (Urban/Rural)
    if (!empty($request['location_of_head_quarter'])) {
        $location = intval($request['location_of_head_quarter']);
        $where .= " AND c.location_of_head_quarter = $location";
    }
    
    // 9. Operation Area location
    if (!empty($request['operation_area_location'])) {
        $op_location = intval($request['operation_area_location']);
        $where .= " AND c.operation_area_location = $op_location";
    }
    
    // 10. Is Coastal (Yes/No)
    if (isset($request['is_coastal']) && $request['is_coastal'] !== '') {
        $coastal = intval($request['is_coastal']);
        $where .= " AND c.is_coastal = $coastal";
    }
    
    // 11. Is affiliated union federation (Yes/No)
    if (isset($request['is_affiliated_union_federation']) && $request['is_affiliated_union_federation'] !== '') {
        $affiliated = intval($request['is_affiliated_union_federation']);
        $where .= " AND c.is_affiliated_union_federation = $affiliated";
    }
    
    // 12. Financial Audit (Yes/No)
    if (isset($request['financial_audit']) && $request['financial_audit'] !== '') {
        $audit = intval($request['financial_audit']);
        $where .= " AND c.financial_audit = $audit";
    }
    
    // 13. Is profit making (Yes/No)
    if (isset($request['is_profit_making']) && $request['is_profit_making'] !== '') {
        $profit = intval($request['is_profit_making']);
        $where .= " AND c.is_profit_making = $profit";
    }
    
    // 14. Is dividend paid (Yes/No)
    if (isset($request['is_dividend_paid']) && $request['is_dividend_paid'] !== '') {
        $dividend = intval($request['is_dividend_paid']);
        $where .= " AND c.is_dividend_paid = $dividend";
    }
    
    // 15. State (state_code)
    if (!empty($request['state_code'])) {
        $state = addslashes($request['state_code']);
        $where .= " AND c.state_code = '$state'";
    }

    // 🔥 Cooperative Type Filter (from dashboard)
    if (!empty($request['type_id'])) {
        $type_id = intval($request['type_id']);
        if ($type_id > 0) {
            $where .= " AND c.cooperative_society_type_id = $type_id";
        }
    }

    return $where;
}

/**
 * Get dropdown options for filters
 */
function getFilterOptions($column) {
    global $db;
    
    $options = [];
    
    switch($column) {
        case 'area_of_operation_id':
            $res = execute_query("SELECT id, name FROM area_of_operations_master ORDER BY name");
            while ($row = mysqli_fetch_assoc($res)) {
                $options[$row['id']] = $row['name'];
            }
            break;
            
        case 'water_body_type_id':
            $res = execute_query("SELECT id, name FROM water_body_types_master ORDER BY name");
            while ($row = mysqli_fetch_assoc($res)) {
                $options[$row['id']] = $row['name'];
            }
            break;
            
        // case 'sector_of_operation':
        //     $res = execute_query("SELECT id, name FROM sector_master ORDER BY name");
        //     while ($row = mysqli_fetch_assoc($res)) {
        //         $options[$row['id']] = $row['name'];
        //     }
        //     break;

        case 'sector_of_operation':

            // 🔥 Apply only dashboard-level filters (simple fix)
            $where = "WHERE 1=1";

            if (!empty($_GET['authority_id'])) {
                $where .= " AND c.registration_authoritie_id = " . intval($_GET['authority_id']);
            }

            if (!empty($_GET['type_id'])) {
                $where .= " AND c.cooperative_society_type_id = " . intval($_GET['type_id']);
            }

            // 🔥 Only sectors present in data
            $sql = "SELECT DISTINCT c.sector_of_operation, sm.name FROM cooperatives c LEFT JOIN sector_master sm ON c.sector_of_operation = sm.id $where ORDER BY sm.name ";

            $res = execute_query($sql);

            while ($row = mysqli_fetch_assoc($res)) {
                if (!empty($row['sector_of_operation'])) {
                    $options[$row['sector_of_operation']] = $row['name'];
                }
            }

            break;
            
        case 'operation_area_location':
            $res = execute_query("SELECT id, name FROM area_of_operations_master ORDER BY name");
            while ($row = mysqli_fetch_assoc($res)) {
                $options[$row['id']] = $row['name'];
            }
            break;
            
        case 'state_code':
            $res = execute_query("SELECT state_code, name FROM states_master ORDER BY name");
            while ($row = mysqli_fetch_assoc($res)) {
                $options[$row['state_code']] = $row['name'];
            }
            break;
    }
    
    return $options;
}

/**
 * Get year options (from cooperatives data)
 */

function getYearOptions() {
    $years = [];

    $res = execute_query("SELECT MIN(reference_year) as min_year, MAX(reference_year) as max_year FROM cooperatives");
    $row = mysqli_fetch_assoc($res);

    $min = (int)$row['min_year'];
    $max = (int)$row['max_year'];

    if ($min > 0 && $max > 0) {
        for ($y = $max; $y >= $min; $y--) {
            $years[$y] = $y;
        }
    }

    return $years;
}
