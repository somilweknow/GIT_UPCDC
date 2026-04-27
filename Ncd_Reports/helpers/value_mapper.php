<?php
function mapDisplayValue($column, $value) {

    static $cache = []; // 🔥 cache to avoid repeated DB hits

    if ($value === null || $value === '') {
        return 'N/A';
    }
    if ($column === 'is_approved' || $column === 'functional_status') {
        if ($value === '1') {
            return 'Functional';
        } else {
            return 'Non Functional';
        }
    }

    if ($column === 'location_of_head_quarter') {
        if ($value === '1') {
            return 'Urban';
        } elseif ($value === '2') {
            return 'Rural';
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
    if ($column === 'area_of_operation_id') {

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
            'table' => 'registration_authorities_master',
            'id_col' => 'id',
            'name_col' => 'authority_name'
        ],

        'cooperative_society_type_id' => [
            'table' => 'ncd_cooperative_society_type',
            'id_col' => 'sno',
            'name_col' => 'cooperative_society_types'
        ],

        'area_of_operation_id' => [
            'table' => 'area_of_operations_master',
            'id_col' => 'id',
            'name_col' => 'name'
        ],

        'water_body_type_id' => [
            'table' => 'water_body_types_master',
            'id_col' => 'id',
            'name_col' => 'name'
        ],

        'state_code' => [
            'table' => 'states_master',
            'id_col' => 'state_code',
            'name_col' => 'name'
        ],

        'district_code' => [
            'table' => 'districts_master',
            'id_col' => 'district_code',
            'name_col' => 'district_name'
        ],

        'block_code' => [
            'table' => 'blocks_master',
            'id_col' => 'block_code',
            'name_col' => 'block_name'
        ],

        'gram_panchayat_code' => [
            'table' => 'gp_villages_master',
            'id_col' => 'gram_panchayat_code',
            'name_col' => 'gram_panchayat_name'
        ],

        'village_code' => [
            'table' => 'gp_villages_master',
            'id_col' => 'village_code',
            'name_col' => 'village_name'
        ],

        'urban_local_body_type_code' => [
            'table' => 'urban_local_body_master',
            'id_col' => 'localbody_type_code',
            'name_col' => 'localbody_type_name'
        ],

        'urban_local_body_code' => [
            'table' => 'urban_local_body_master',
            'id_col' => 'localbody_code',
            'name_col' => 'local_body_name'
        ],

        'locality_ward_code' => [
            'table' => 'urban_local_body_ward_master',
            'id_col' => 'ward_code',
            'name_col' => 'ward_name'
        ],

        'designation' => [
            'table' => 'designations_master',
            'id_col' => 'id',
            'name_col' => 'name'
        ],

        'operation_area_location' => [
            'table' => 'area_of_operations_master',
            'id_col' => 'id',
            'name_col' => 'name'
        ],

        'category_audit' => [
            'table' => 'audit_categories_master',
            'id_col' => 'id',
            'name_col' => 'name'
        ],

        'cooperative_society_bank_id' => [
            'table' => 'cooperative_banks_master',
            'id_col' => 'id',
            'name_col' => 'bank_name'
        ],

        'sector_of_operation' => [
            'table' => 'sector_master',
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