<?php
function mapDisplayValue($column, $value) {

    static $cache = []; // 🔥 cache to avoid repeated DB hits

    if ($value === null || $value === '') {
        return 'N/A';
    }
    if ($column === 'is_approved') {
        if ($value === '1') {
            return 'Approved';
        } else {
            return 'Non Approved';
        }
    }

    if ($column === 'location_of_head_quarter') {
        if ($value === '1') {
            return 'Urban';
        } elseif ($value === '2') {
            return 'Rural';
        }
    }

    if ($column === 'functional_status') {
        if ($value === '1') {
            return 'Functional';
        } elseif ($value === '2') {
            return 'Non-Functional';
        } elseif ($value === '3') {
            return 'Liquidation';
        }
    }

    if ($column === 'is_coastal') {
        if ((int)$value === 1) {
            return 'Yes';
        } else {
            return 'No';
        }
    }

    if ($column === 'is_affiliated_union_federation') {
        if ((int)$value === 1) {
            return 'Yes';
        } else {
            return 'No';
        }
    }

    if ($column === 'financial_audit') {
        if ($value === '1') {
            return 'Yes';
        } else {
            return 'No';
        }
    }

    if ($column === 'is_profit_making') {
        if ($value === '1') {
            return 'Yes';
        } else {
            return 'No';
        }
    }
    if ($column === 'is_dividend_paid') {
        if ($value === '1') {
            return 'Yes';
        } else {
            return 'No';
        }
    }
    if ($column === 'full_time_secretary') {
        if ($value) {
            return 'Yes';
        } else {
            return 'No';
        }
    }
    if ($column === 'sector_of_operation_type') {
        if ((int)$value === 1) {
            return 'Rural';
        } elseif ((int)$value === 2) {
            return 'Urban';
        }
        return 'N/A';
    }
    if ($column === 'operation_area_location') {

        $map = [
            0 => 'Village',
            1 => 'Village',
            2 => 'Gram Panchayat',
            3 => 'District',
            6 => 'Block / Mandal / Town',
            7 => 'District (Urban)',
            9 => 'Urban Local Body',
            10 => 'Locality / Ward'
        ];

        return $map[(int)$value] ?? 'N/A';
    }

    // 🔥 Column mapping config
    $map = [

        'registration_authoritie_id' => [
            'table' => 'ncd_registration_authorities',
            'id_col' => 'id',
            'name_col' => 'authority_name'
        ],

        'cooperative_society_type_id' => [
            'table' => 'ncd_cooperative_society_types',
            'id_col' => 'id',
            'name_col' => 'name'
        ],

        'area_of_operation_id' => [
            'table' => 'ncd_area_of_operations',
            'id_col' => 'id',
            'name_col' => 'name'
        ],

        'water_body_type_id' => [
            'table' => 'ncd_water_body_types',
            'id_col' => 'id',
            'name_col' => 'name'
        ],

        'state_code' => [
            'table' => 'ncd_state_district_block_gp_village',
            'id_col' => 'state_code',
            'name_col' => 'state_name'
        ],

        'district_code' => [
            'table' => 'ncd_state_district_block_gp_village',
            'id_col' => 'district_code',
            'name_col' => 'district_name'
        ],

        'block_code' => [
            'table' => 'ncd_state_district_block_gp_village',
            'id_col' => 'block_code',
            'name_col' => 'block_name'
        ],

        'gram_panchayat_code' => [
            'table' => 'ncd_state_district_block_gp_village',
            'id_col' => 'gram_panchayat_code',
            'name_col' => 'gram_panchayat_name'
        ],

        'village_code' => [
            'table' => 'ncd_state_district_block_gp_village',
            'id_col' => 'village_code',
            'name_col' => 'village_name'
        ],

        'urban_local_body_type_code' => [
            'table' => 'ncd_urban_local_bodies',
            'id_col' => 'localbody_type_code',
            'name_col' => 'localbody_type_name'
        ],

        'urban_local_body_code' => [
            'table' => 'ncd_urban_local_bodies',
            'id_col' => 'localbody_code',
            'name_col' => 'local_body_name'
        ],

        'locality_ward_code' => [
            'table' => 'ncd_urban_local_body_wards',
            'id_col' => 'ward_code',
            'name_col' => 'ward_name'
        ],

        'designation' => [
            'table' => 'ncd_designations',
            'id_col' => 'id',
            'name_col' => 'name'
        ],

        'operation_area_location' => [
            'table' => 'ncd_area_of_operations',
            'id_col' => 'id',
            'name_col' => 'name'
        ],

        'category_audit' => [
            'table' => 'ncd_audit_categories',
            'id_col' => 'id',
            'name_col' => 'name'
        ],

        'cooperative_society_bank_id' => [
            'table' => 'ncd_cooperative_society_banks',
            'id_col' => 'id',
            'name_col' => 'bank_name'
        ],

        'sector_of_operation' => [
            'table' => 'ncd_sectors',
            'id_col' => 'id',
            'name_col' => 'name'
        ],
    ];

    // ❌ if not mapped column
    if (!isset($map[$column])) {
        return $value;
    }

    $conf = $map[$column];
    $key = $column . '_' . $value;

    // 🔥 Return from cache if already fetched
    if (isset($cache[$key])) {
        return $cache[$key];
    }

    $id = intval($value);

    $res = execute_query("
        SELECT {$conf['name_col']} as name 
        FROM {$conf['table']} 
        WHERE {$conf['id_col']} = $id
        LIMIT 1
    ");

    if ($row = mysqli_fetch_assoc($res)) {
        $name = $row['name'];

        // 🔥 Final format: Name (ID)
        $final = $name;
    } else {
        $final = $value;
    }

    // 🔥 Save in cache
    $cache[$key] = $final;

    return $final;
}