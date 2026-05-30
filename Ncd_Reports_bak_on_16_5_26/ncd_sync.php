<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'upcdc_ncd');
define('DB_USER', 'root');
define('DB_PASS', 'mysql');
define('DB_CHARSET', 'utf8mb4');

define('API_BASE', 'https://api.cooperatives.gov.in');
define('API_KEY', '84950dfe63c3a294f83e8e656763475c50625dc8c577c84f479785b6d00e4e31');
define('STATE_CODE', 9);   // 9 = Uttar Pradesh

// ── cURL timeout settings ─────────────────────
define('CURL_CONNECT_TIMEOUT', 30);   // seconds to establish connection
define('CURL_EXEC_TIMEOUT', 120);   // seconds to wait for full response
define('CURL_MAX_RETRIES', 3);   // retry count on transient failure
define('CURL_RETRY_DELAY', 5);   // seconds between retries

// How many rows to fetch per paginated page (Area of Operation - Rural)
define('AOP_PAGE_LIMIT', 100000);
// ─────────────────────────────────────────────

// ── Bootstrap ────────────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);
ini_set('memory_limit', '512M');

if (!function_exists('curl_init')) {
    die("[FATAL] PHP cURL extension is not installed/enabled.\n");
}

$db = connectDB();
createAllTables($db);

// ── Decide which sections to run ─────────────
$only = null;
foreach ($argv ?? [] as $arg) {
    if (str_starts_with($arg, '--only=')) {
        $only = substr($arg, 7);
    }
}

$sections = ['masters', 'geo', 'cooperatives', 'sectors', 'aop'];
foreach ($sections as $s) {
    if ($only && $only !== $s) continue;
    log_msg("=== Starting section: $s ===");
    call_user_func("sync_$s", $db);
}

log_msg("=== NCD Sync complete ===");

function connectDB(): mysqli
{
    $db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$db) {
        die("DB Connection failed: " . mysqli_connect_error() . "\n");
    }
    mysqli_set_charset($db, DB_CHARSET);
    mysqli_query($db, "SET time_zone = '+05:30'");
    return $db;
}

function createAllTables(mysqli $db): void
{
    log_msg("Creating / verifying tables …");

    $ddl = <<<'SQL'

    -- ── Sync log ──────────────────────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_sync_log (
        id            INT AUTO_INCREMENT PRIMARY KEY,
        api_name      VARCHAR(120),
        records_saved INT DEFAULT 0,
        ran_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- ── 1. States ─────────────────────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_states (
        id            INT AUTO_INCREMENT PRIMARY KEY,
        state_code    INT UNIQUE,
        name          VARCHAR(120),
        hindi_name    VARCHAR(255),
        state_or_ut   CHAR(1),
        sr_no         INT,
        updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- ── 2. Districts ──────────────────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_districts (
        id            INT AUTO_INCREMENT PRIMARY KEY,
        state_code    INT,
        district_code INT UNIQUE,
        district_name VARCHAR(120),
        updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_state (state_code)
    );

    -- ── 3. Blocks ─────────────────────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_blocks (
        id                INT AUTO_INCREMENT PRIMARY KEY,
        state_code        INT,
        district_code     INT,
        block_code        INT UNIQUE,
        block_version     VARCHAR(20),
        name              VARCHAR(150),
        block_name_hindi  VARCHAR(255),
        name_local        VARCHAR(255),
        updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_state   (state_code),
        INDEX idx_dist    (district_code)
    );

    -- ── 4. Districts–Blocks–GP–Villages ───────────────────────
    CREATE TABLE IF NOT EXISTS ncd_state_district_block_gp_village (
        id                        INT AUTO_INCREMENT PRIMARY KEY,
        state_code                INT,
        state_name                VARCHAR(120),
        district_code             INT,
        district_name             VARCHAR(120),
        block_code                INT,
        block_name                VARCHAR(150),
        gram_panchayat_code       INT,
        gram_panchayat_name       VARCHAR(200),
        gram_panchayat_name_hindi VARCHAR(255),
        village_code              INT UNIQUE,
        village_name              VARCHAR(200),
        updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_state           (state_code),
        INDEX idx_gp              (gram_panchayat_code)
    );

    -- ── 5. Urban Local Bodies ─────────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_urban_local_bodies (
        id                        INT AUTO_INCREMENT PRIMARY KEY,
        state_code                INT,
        state_name                VARCHAR(120),
        district_code             INT,
        district_name             VARCHAR(120),
        localbody_type_code       INT,
        localbody_type_name       VARCHAR(120),
        localbody_type_name_hindi VARCHAR(255),
        localbody_code            INT UNIQUE,
        local_body_name           VARCHAR(200),
        local_body_name_hindi     VARCHAR(255),
        updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_state           (state_code)
    );

    -- ── 6. Urban Local Body Wards ─────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_urban_local_body_wards (
        id                    INT AUTO_INCREMENT PRIMARY KEY,
        state_code            INT,
        state_name            VARCHAR(120),
        local_body_code       INT,
        local_body_name       VARCHAR(200),
        local_body_name_hindi VARCHAR(255),
        ward_code             INT UNIQUE,
        ward_name             VARCHAR(200),
        ward_name_hindi       VARCHAR(255),
        updated_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_state       (state_code),
        INDEX idx_ulb         (local_body_code)
    );

    -- ── 7. Sectors ────────────────────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_sectors (
        id         INT UNIQUE,
        name       VARCHAR(200),
        hindi_name VARCHAR(400),
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- ── 8. Sub-Sectors ────────────────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_sub_sectors (
        id                    INT AUTO_INCREMENT PRIMARY KEY,
        sub_sector_id         INT UNIQUE,
        primary_activities_id INT,
        sub_sector_name       VARCHAR(255),
        sub_sector_name_hindi VARCHAR(400),
        updated_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- ── 9a. Audit Categories ──────────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_audit_categories (
        id                     INT UNIQUE,
        name                   VARCHAR(200),
        audit_categories_hindi VARCHAR(400),
        updated_at             TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- ── 9b. Society Implementing Schemes ─────────────────────
    CREATE TABLE IF NOT EXISTS ncd_society_implementing_schemes (
        id               INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id           VARCHAR(40),
        gov_scheme_name  VARCHAR(500),
        gov_scheme_type  VARCHAR(200),
        total_amount     DECIMAL(17,2),
        st_code          INT,
        dist_code        INT,
        updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uk_ncd_scheme (ncd_id, gov_scheme_name(100))
    );

    -- ── 9c. Cooperative Registration Lands ───────────────────
    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_lands (
        id            INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id        VARCHAR(40) UNIQUE,
        land_owned    DECIMAL(15,3),
        land_leased   DECIMAL(15,3),
        land_allotted DECIMAL(15,3),
        land_total    DECIMAL(15,3),
        st_code       INT,
        dist_code     INT,
        updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- ── 9d. Type of Activities Khadi Gram ────────────────────
    CREATE TABLE IF NOT EXISTS ncd_type_of_activities_khadi_gram (
        id                            INT UNIQUE,
        type_of_activities_name       VARCHAR(255),
        type_of_activities_name_hindi VARCHAR(400),
        updated_at                    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- ── 9e. Members Details ───────────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_members_details (
        id        INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id    VARCHAR(40) UNIQUE,
        m_general INT, m_sc INT, m_st INT, m_obc INT,
        f_general INT, f_sc INT, f_st INT, f_obc INT,
        t_general INT, t_sc INT, t_st INT, t_obc INT,
        st_code   INT,
        dist_code INT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- ── 9f. Society Audit Years ───────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_society_audit_years (
        id                    INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                VARCHAR(40),
        audit_category        INT,
        audit_year            VARCHAR(10),
        sector_type           INT,
        sector                INT,
        annual_profit         DECIMAL(17,2),
        annual_loss           DECIMAL(17,2),
        state_code            INT,
        district_code         INT,
        share_capital         DECIMAL(17,2),
        reserve_fund          DECIMAL(17,2),
        revenue               DECIMAL(17,2),
        deposit               DECIMAL(17,2),
        loan_and_advance      DECIMAL(17,2),
        borrowings            DECIMAL(17,2),
        total_assets          DECIMAL(17,2),
        no_of_members         INT,
        no_of_branches        INT,
        total_credit_provided DECIMAL(17,2),
        paid_up_share         DECIMAL(17,2),
        annual_turnover       DECIMAL(17,2),
        annual_income         DECIMAL(17,2),
        annual_ucb_expenditr  DECIMAL(17,2),
        asset_ucb             DECIMAL(17,2),
        liability_ucb         DECIMAL(17,2),
        paid_up_members       DECIMAL(17,2),
        paid_up_government_bodies DECIMAL(17,2),
        paid_up_total         DECIMAL(17,2),
        annual_expenses       DECIMAL(17,2),
        updated_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uk_ncd_year (ncd_id, audit_year)
    );

    -- ── 9g. Registration Authorities ─────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_registration_authorities (
        id                   INT UNIQUE,
        authority_name       VARCHAR(255),
        authority_name_hindi VARCHAR(400),
        primary_activity     INT,
        updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- ── 9h. Area of Operations (master lookup) ────────────────
    CREATE TABLE IF NOT EXISTS ncd_area_of_operations (
        id          INT UNIQUE,
        urban_rural INT,
        name        VARCHAR(100),
        updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- ── 9i. Cooperative Society Banks ────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_cooperative_society_banks (
        id         INT UNIQUE,
        bank_name  VARCHAR(200),
        bank_type  INT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- ── 9j. Designations ─────────────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_designations (
        id                 INT UNIQUE,
        name               VARCHAR(200),
        designations_hindi VARCHAR(400),
        updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- ── 9k. Cooperative Society Facilities ───────────────────
    CREATE TABLE IF NOT EXISTS ncd_cooperative_society_facilities (
        id                  INT UNIQUE,
        name                VARCHAR(255),
        primary_activity_id INT,
        updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- ── 9l. Office Building Types ─────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_office_building_types (
        id                          INT UNIQUE,
        name                        VARCHAR(200),
        office_building_types_hindi VARCHAR(400),
        updated_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- ── 9m. Water Body Types ─────────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_water_body_types (
        id                     INT UNIQUE,
        name                   VARCHAR(200),
        water_body_types_hindi VARCHAR(400),
        updated_at             TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    
    -- ── 9o. Cooperative Society Types ─────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_cooperative_society_types (
        id INT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );
    
    INSERT INTO ncd_cooperative_society_types (id, name) VALUES
    (1, 'Primary Cooperative'),
    (2, 'District Cooperative'),
    (3, 'State Cooperative'),
    (8, 'Central Cooperative')
    ON DUPLICATE KEY UPDATE 
    name = VALUES(name);

    -- ── 9n. Board of Directors ────────────────────────────────
    CREATE TABLE IF NOT EXISTS ncd_board_of_directors (
        id                           INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                       VARCHAR(40),
        bod_name                     VARCHAR(255),
        father_name                  VARCHAR(255),
        gender                       TINYINT,
        mobile_number                VARCHAR(15),
        email_id                     VARCHAR(255),
        id_proof_type                VARCHAR(50),
        id_proof_no                  VARCHAR(100),
        bod_designation              INT,
        from_date                    DATE,
        to_date                      DATE,
        is_any_other_cooperative     TINYINT,
        other_cooperative_society_name VARCHAR(255),
        sector_code                  INT,
        st_code                      INT,
        dist_code                    INT,
        updated_at                   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_ncd                (ncd_id)
    );

    -- ── 10. Main Cooperative Registrations ───────────────────

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations (
        id                              INT AUTO_INCREMENT PRIMARY KEY,
    
        -- PRIMARY UNIQUE KEY
        cooperative_id                  VARCHAR(40) UNIQUE,
    
        -- BASIC INFO
        cooperative_society_name        VARCHAR(255),
        local_langauge_society_name     VARCHAR(255),
        registration_authoritie_id      INT,
        reference_year                  INT,
        date_registration               DATE,
        registration_number             VARCHAR(255),
    
        -- CLASSIFICATION
        cooperative_society_type_id     INT,
        area_of_operation_id            INT,
        water_body_type_id              INT,
        sector_of_operation_type        INT,
        sector_of_operation             INT,
        functional_status               INT,
    
        -- LOCATION
        location_of_head_quarter        INT,
        state_code                      INT,
        district_code                   INT,
        block_code                      INT,
        gram_panchayat_code             INT,
        village_code                    INT,
        urban_local_body_type_code      INT,
        urban_local_body_code           INT,
        locality_ward_code              INT,
        pincode                         INT,
    
        -- ADDRESS
        full_address                    VARCHAR(255),
        address_line                    VARCHAR(255),   -- ✅ ADDED
    
        -- CONTACT
        contact_person                  VARCHAR(255),
        designation                     INT,
        mobile                          VARCHAR(15),
        landline                        VARCHAR(17),
        email                           VARCHAR(255),
    
        -- SECRETARY / PACS
        full_time_secretary             VARCHAR(10),    -- ✅ FIXED (was CHAR(3))
        mobile_number_of_secretary      VARCHAR(15),
        alternate_contact_no_for_pacs   VARCHAR(15),
        pacs_id                         VARCHAR(40),
    
        is_approved                     TINYINT,        -- ✅ ADDED
        is_coastal                      TINYINT,
        is_affiliated_union_federation  TINYINT,        -- ✅ ADDED
        financial_audit                 TINYINT,
        is_profit_making                TINYINT,
        is_dividend_paid                TINYINT,
    
        -- MEMBERS / AUDIT
        members_of_society              MEDIUMINT,
        audit_complete_year             INT,
        category_audit                  INT,
    
        -- FINANCIAL
        annual_turnover                 DECIMAL(17,2),
        annual_profit                   DECIMAL(17,2),  -- ✅ ADDED
        annual_loss                     DECIMAL(17,3),
        dividend_rate                   DECIMAL(10,3),
    
        -- OPERATION
        operation_area_location         INT,
    
        -- BANKING
        bank_type                       VARCHAR(10),
        cooperative_society_bank_id     VARCHAR(150),   -- ✅ KEEP STRING
        other_bank                      VARCHAR(255),
    
        -- IDS
        pan_no                          VARCHAR(12),
        gst_no                          VARCHAR(40),
        how_many_branches               INT,
    
        -- TIMESTAMP
        updated_at                      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
        -- INDEXES
        INDEX idx_state (state_code),
        INDEX idx_sector (sector_of_operation),
        INDEX idx_district (district_code),
        INDEX idx_block (block_code)
    
    );

    -- ── Sector Tables ─────────────────────────────────────────

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_agriculture (
        id                        INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                    VARCHAR(40) UNIQUE,
        st_code                   INT,
        dist_code                 INT,
        type_society              VARCHAR(255),
        has_building              TINYINT,
        building_type             INT,
        authorised_share          DOUBLE,
        paid_up_members           DOUBLE,
        paid_up_government_bodies DOUBLE,
        paid_up_total             DOUBLE,
        annual_turn_over          DOUBLE,
        individual_member         MEDIUMINT,
        institutional_member      MEDIUMINT,
        has_pool_land             TINYINT,
        has_gov_land              TINYINT,
        member_vested_right       TINYINT,
        is_member_work            TINYINT,
        society_common_pool       TINYINT,
        is_utilize_pool           TINYINT,
        harvesting                TINYINT,
        farming_mech              VARCHAR(100),
        irrigation_means          VARCHAR(100),
        updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_processing (
        id                        INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                    VARCHAR(40) UNIQUE,
        st_code                   INT,
        dist_code                 INT,
        individual_member         MEDIUMINT,
        institutional_member      MEDIUMINT,
        total_member              MEDIUMINT,
        type_society              INT,
        has_building              TINYINT,
        building_type             INT,
        authorised_share          DOUBLE,
        paid_up_members           DOUBLE,
        paid_up_government_bodies DOUBLE,
        paid_up_total             DOUBLE,
        annual_turn_over          DOUBLE,
        processing_unit           TINYINT,
        processing_unit_number    BIGINT,
        processing_by_members     TINYINT,
        work_divided              TINYINT,
        product_taken             TINYINT,
        material_available        TINYINT,
        wastes_generated          TINYINT,
        waste_management_facility TINYINT,
        operate_shops             TINYINT,
        operate_shops_number      BIGINT,
        product_sale_out_of_area  TINYINT,
        updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_bee (
        id                        INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                    VARCHAR(40) UNIQUE,
        st_code                   INT,
        dist_code                 INT,
        type_bee                  INT,
        has_building              TINYINT,
        building_type             INT,
        authorised_share          DOUBLE,
        paid_up_members           DOUBLE,
        paid_up_government_bodies DOUBLE,
        paid_up_total             DOUBLE,
        annual_turn_over          DOUBLE,
        common_yard               TINYINT,
        no_of_behives             BIGINT,
        type_behives              INT,
        rear_by_member            TINYINT,
        guidance_by_member        TINYINT,
        type_product              VARCHAR(100),
        is_bee_plant_grow         TINYINT,
        is_cleaning_process       TINYINT,
        is_waste_facility         TINYINT,
        own_brand_honey           TINYINT,
        is_operate_retail         TINYINT,
        no_of_retail              BIGINT,
        is_product_sale_out       TINYINT,
        facilities                VARCHAR(100),
        updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_consumer (
        id               INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id           VARCHAR(40) UNIQUE,
        st_code          INT,
        dist_code        INT,
        has_building     TINYINT,
        has_store        TINYINT,
        no_of_outlets    INT,
        building_type    INT,
        authorised_share FLOAT,
        paid_up_share    FLOAT,
        annual_turn_over FLOAT,
        facilities       VARCHAR(100),
        updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_credit_thrift (
        id                          INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                      VARCHAR(40) UNIQUE,
        st_code                     INT,
        dist_code                   INT,
        has_building                TINYINT,
        building_type               INT,
        authorised_share            FLOAT,
        paid_up_share               FLOAT,
        total_deposit               FLOAT,
        pack_total_outstanding_loan FLOAT,
        facilities                  VARCHAR(100),
        updated_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registration_dairy (
        id                    INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                VARCHAR(40) UNIQUE,
        st_code               INT,
        dist_code             INT,
        milk_collection       INT,
        credit_facility       TINYINT,
        credit_provided       DECIMAL(17,3),
        milk_collection_unit  TINYINT,
        milk_collection_capicity INT,
        transport_milk        TINYINT,
        bulk_milk_unit        TINYINT,
        milk_testing          TINYINT,
        processing            TINYINT,
        other_facility        TEXT,
        is_bank_mitra         TINYINT,
        bank_mitra_details    TEXT,
        is_micro_atm          TINYINT,
        micro_atm_details     TEXT,
        updated_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_education (
        id                               INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                           VARCHAR(40) UNIQUE,
        st_code                          INT,
        dist_code                        INT,
        type_society                     INT,
        has_building                     TINYINT,
        building_type                    INT,
        has_land                         TINYINT,
        authorised_share                 DOUBLE,
        paid_up_members                  DOUBLE,
        paid_up_government_bodies        DOUBLE,
        paid_up_total                    DOUBLE,
        annual_turn_over                 DOUBLE,
        individual_member                BIGINT,
        institutional_member             BIGINT,
        level_of_edu                     INT,
        duration_of_course               INT,
        level_and_duration_of_course     VARCHAR(255),
        course_in_audit                  DOUBLE,
        stu_in_audit                     DOUBLE,
        training_course_in_audit         DOUBLE,
        participants_in_audit            DOUBLE,
        course_international_participant TINYINT,
        no_of_training_course            INT,
        attended_training                INT,
        society_recruit                  TINYINT,
        no_regular_faculty               INT,
        no_other_faculty                 INT,
        facilities                       VARCHAR(100),
        updated_at                       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registration_pacs (
        id                              INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                          VARCHAR(40) UNIQUE,
        st_code                         INT,
        dist_code                       INT,
        has_building                    TINYINT,
        building_type                   INT,
        fertilizer_distribution         TINYINT,
        fertilizer_distribution_qty     INT,
        fertilizer_distribution_details TEXT,
        pesticide_distribution          TINYINT,
        pesticide_distribution_qty      INT,
        seed_distribution               TINYINT,
        seed_distribution_qty           INT,
        fair_price                      TINYINT,
        fair_price_qty                  INT,
        fair_price_details              TEXT,
        is_foodgrains                   TINYINT,
        foodgrains_qty                  INT,
        agricultural_implements         TINYINT,
        agricultural_implements_text    TEXT,
        dry_storage                     TINYINT,
        dry_storage_capicity            DECIMAL(10,2),
        cold_storage                    TINYINT,
        cold_storage_capicity           DECIMAL(10,2),
        milk_unit                       TINYINT,
        milk_capicity_unit              VARCHAR(20),
        food_processing                 TINYINT,
        food_processing_type            TEXT,
        other_facility                  TEXT,
        is_socitey_has_land             INT,
        pack_involved_fish_catch        INT,
        pack_annual_fish_catch          DECIMAL(10,3),
        pack_total_outstanding_loan     DOUBLE,
        pack_revenue_non_credit         DECIMAL(17,3),
        is_lgs_program                  TINYINT,
        lgs_capacity                    DECIMAL(17,3),
        is_csc                          TINYINT,
        csc_revenue                     INT,
        csc_details                     TEXT,
        is_fpo                          TINYINT,
        fpo_details                     TEXT,
        is_lpg_distributership          TINYINT,
        lpg_distributership_details     TEXT,
        is_bcp_pump                     TINYINT,
        bcp_pump_details                TEXT,
        is_dpp_diesel                   TINYINT,
        dpp_diesel_details              TEXT,
        is_jak                          TINYINT,
        jak_qty                         INT,
        is_pmksk                        TINYINT,
        pmksk_details                   TEXT,
        is_paani_samity                 TINYINT,
        paani_samity_details            TEXT,
        is_pm_kusum_scheme              TINYINT,
        pm_kusum_scheme_details         TEXT,
        updated_at                      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registration_fishery (
        id                    INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                VARCHAR(40) UNIQUE,
        st_code               INT,
        dist_code             INT,
        annual_fish_catch     DECIMAL(17,3),
        credit_facility       TINYINT,
        total_credit_provided DECIMAL(17,3),
        fuel_distribution     TINYINT,
        marketing             TINYINT,
        cold_storage          TINYINT,
        transportation        TINYINT,
        other_facility        TEXT,
        is_fpo_fisheries      TINYINT,
        fpo_fisheries_details TEXT,
        updated_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_handicraft (
        id                        INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                    VARCHAR(40) UNIQUE,
        st_code                   INT,
        dist_code                 INT,
        has_building              TINYINT,
        building_type             INT,
        authorised_share          DOUBLE,
        paid_up_members           DOUBLE,
        paid_up_government_bodies DOUBLE,
        paid_up_total             DOUBLE,
        individual_member         MEDIUMINT,
        institutional_member      MEDIUMINT,
        annual_turn_over          DOUBLE,
        type_raw                  INT,
        type_produce              INT,
        common_work_place         TINYINT,
        workplace_operate         INT,
        is_work_by_member         TINYINT,
        is_training_provide       TINYINT,
        is_raw_provide            TINYINT,
        is_raw_easy_avail         TINYINT,
        is_waste_generate         TINYINT,
        is_waste_facility         TINYINT,
        is_operate_retail         TINYINT,
        no_of_retail              INT,
        is_product_sale_out       TINYINT,
        facilities                INT,
        updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_handloom (
        id                        INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                    VARCHAR(40) UNIQUE,
        st_code                   INT,
        dist_code                 INT,
        has_building              TINYINT,
        building_type             INT,
        authorised_share          DOUBLE,
        paid_up_members           DOUBLE,
        paid_up_government_bodies DOUBLE,
        paid_up_total             DOUBLE,
        annual_turn_over          DOUBLE,
        individual_member         MEDIUMINT,
        institutional_member      MEDIUMINT,
        power_loom_type           VARCHAR(100),
        hand_loom_type            VARCHAR(100),
        no_of_loom                BIGINT,
        raw_product_taken         TINYINT,
        raw_material_available    TINYINT,
        waste_generate            TINYINT,
        waste_available           TINYINT,
        operate_retail            TINYINT,
        no_of_retail              BIGINT,
        product_sale_out          TINYINT,
        operated_member_themself  TINYINT,
        is_user_work_divide       TINYINT,
        updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_housing (
        id                            INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                        VARCHAR(40) UNIQUE,
        st_code                       INT,
        dist_code                     INT,
        type_society                  INT,
        has_building                  TINYINT,
        building_type                 INT,
        has_land                      TINYINT,
        authorised_share              DOUBLE,
        paid_up_members               DOUBLE,
        paid_up_government_bodies     DOUBLE,
        paid_up_total                 DOUBLE,
        annual_turn_over              DOUBLE,
        annual_expenses               DOUBLE,
        loan_facilities               TINYINT,
        number_of_houses_audit_year   INT,
        number_of_houses_during_year  INT,
        number_of_houses_construction INT,
        facilities                    INT,
        updated_at                    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_jute (
        id                   INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id               VARCHAR(40) UNIQUE,
        st_code              INT,
        dist_code            INT,
        has_building         TINYINT,
        building_type        INT,
        authorised_share     DOUBLE,
        annual_turn_over     DOUBLE,
        type_raw             VARCHAR(100),
        type_produce         VARCHAR(255),
        common_work_place    TINYINT,
        workplace_operate    DOUBLE,
        is_work_by_member    TINYINT,
        is_training_provide  TINYINT,
        is_raw_provide       TINYINT,
        is_raw_easy_avail    TINYINT,
        is_waste_generate    TINYINT,
        is_waste_facility    TINYINT,
        is_operate_retail    TINYINT,
        no_of_retail         BIGINT,
        is_product_sale_out  TINYINT,
        facilities           INT,
        updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_labour (
        id                               INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                           VARCHAR(40) UNIQUE,
        st_code                          INT,
        dist_code                        INT,
        type_society                     INT,
        has_building                     TINYINT,
        building_type                    INT,
        authorised_share                 DOUBLE,
        paid_up_members                  DOUBLE,
        paid_up_government_bodies        DOUBLE,
        paid_up_total                    DOUBLE,
        annual_turn_over                 DOUBLE,
        annual_expenses                  DOUBLE,
        work_allot_state_dist_federation TINYINT,
        work_guide_state_dist_federation TINYINT,
        concession_state_gov             TINYINT,
        concession_centre_gov            TINYINT,
        facilities                       INT,
        updated_at                       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_livestock (
        id                        INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                    VARCHAR(40) UNIQUE,
        st_code                   INT,
        dist_code                 INT,
        type_society              VARCHAR(100),
        has_building              TINYINT,
        building_type             INT,
        authorised_share          DOUBLE,
        paid_up_members           DOUBLE,
        paid_up_government_bodies DOUBLE,
        paid_up_total             DOUBLE,
        individual_member         DOUBLE,
        institutional_member      DOUBLE,
        annual_turn_over          DOUBLE,
        type_produce              VARCHAR(100),
        common_work_place         TINYINT,
        is_work_by_member         TINYINT,
        is_training_provide       TINYINT,
        is_poultry_feed           TINYINT,
        is_collected_from_member  TINYINT,
        is_waste_facility         TINYINT,
        is_operate_retail         TINYINT,
        no_of_retail              INT,
        is_product_sale_out       TINYINT,
        facilities                VARCHAR(100),
        updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_marketing (
        id                        INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                    VARCHAR(40) UNIQUE,
        st_code                   INT,
        dist_code                 INT,
        has_building              TINYINT,
        building_type             INT,
        has_land                  TINYINT,
        has_warehouses            TINYINT,
        capacity_warehouses       DOUBLE,
        authorised_share          DOUBLE,
        paid_up_members           DOUBLE,
        paid_up_government_bodies DOUBLE,
        paid_up_total             DOUBLE,
        annual_turn_over          DOUBLE,
        annual_expenses           DOUBLE,
        liecense_to_sell          VARCHAR(40),
        sell_the_item             VARCHAR(40),
        updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_cmiscellaneous (
        id                        INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                    VARCHAR(40) UNIQUE,
        st_code                   INT,
        dist_code                 INT,
        has_building              TINYINT,
        building_type             INT,
        authorised_share          DOUBLE,
        paid_up_members           DOUBLE,
        paid_up_government_bodies DOUBLE,
        paid_up_total             DOUBLE,
        annual_turn_over          DOUBLE,
        total_deposit             DOUBLE,
        loan_outstanding          DOUBLE,
        individual_member         MEDIUMINT,
        institutional_member      MEDIUMINT,
        facilities                VARCHAR(100),
        updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_miscellaneous (
        id                        INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                    VARCHAR(40) UNIQUE,
        st_code                   INT,
        dist_code                 INT,
        has_building              TINYINT,
        building_type             INT,
        authorised_share          DOUBLE,
        paid_up_members           DOUBLE,
        paid_up_government_bodies DOUBLE,
        paid_up_total             DOUBLE,
        annual_turn_over          DOUBLE,
        updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_multi (
        id                        INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                    VARCHAR(40) UNIQUE,
        st_code                   INT,
        dist_code                 INT,
        sec_activity              VARCHAR(100),
        has_building              TINYINT,
        building_type             INT,
        authorised_share          DOUBLE,
        paid_up_members           DOUBLE,
        paid_up_government_bodies DOUBLE,
        paid_up_total             DOUBLE,
        annual_turn_over          DOUBLE,
        has_storage               TINYINT,
        storage_capacity          DOUBLE,
        provide_raw               TINYINT,
        guidance_by_member        TINYINT,
        is_operate_retail         TINYINT,
        no_of_retail              BIGINT,
        is_product_sale_out       TINYINT,
        facilities                VARCHAR(100),
        updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_sericulture (
        id                          INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                      VARCHAR(40) UNIQUE,
        st_code                     INT,
        dist_code                   INT,
        type_society                VARCHAR(100),
        has_building                TINYINT,
        building_type               INT,
        authorised_share            DOUBLE,
        annual_turn_over            DOUBLE,
        common_work_place           TINYINT,
        no_rear_house               INT,
        is_work_by_member           TINYINT,
        is_training_provide         TINYINT,
        is_rear_appliance           TINYINT,
        is_mulberry_easy_available  TINYINT,
        is_cleaning_facility_cocoon TINYINT,
        is_spinning_weav            TINYINT,
        is_waste_facility           TINYINT,
        is_operate_retail           TINYINT,
        no_of_retail                BIGINT,
        facilities                  VARCHAR(100),
        updated_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_social (
        id                           INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                       VARCHAR(40) UNIQUE,
        st_code                      INT,
        dist_code                    INT,
        type_society                 VARCHAR(100),
        has_building                 TINYINT,
        building_type                INT,
        authorised_share             DOUBLE,
        paid_up_members              DOUBLE,
        paid_up_government_bodies    DOUBLE,
        paid_up_total                DOUBLE,
        annual_turn_over             DOUBLE,
        type_social_culture_activity VARCHAR(100),
        has_common                   TINYINT,
        is_operate_by_member         TINYINT,
        guidance_by_member           TINYINT,
        is_operate_vehicle           TINYINT,
        no_of_vehicle                BIGINT,
        facilities                   VARCHAR(100),
        updated_at                   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_sugar (
        id                          INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                      VARCHAR(40) UNIQUE,
        st_code                     INT,
        dist_code                   INT,
        has_building                TINYINT,
        building_type               INT,
        authorised_share            DOUBLE,
        paid_up_members             DOUBLE,
        paid_up_government_bodies   DOUBLE,
        paid_up_total               DOUBLE,
        suger_mills_no              INT,
        build_up_area               DOUBLE,
        open_land_area              DOUBLE,
        total_area                  DOUBLE,
        liecensed_capicity          DOUBLE,
        installed_capicity          DOUBLE,
        crushing_period_start       DATE,
        crushing_period_end         DATE,
        product_produced            VARCHAR(255),
        retail_shops                TINYINT,
        retail_shops_no             INT,
        sugercane_input_provided    TINYINT,
        loan_facility               TINYINT,
        waste_management            TINYINT,
        central_government_benefits TINYINT,
        state_government_benefits   TINYINT,
        annual_turn_over            DOUBLE,
        updated_at                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_tourism (
        id                        INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                    VARCHAR(40) UNIQUE,
        st_code                   INT,
        dist_code                 INT,
        type_society              INT,
        has_building              TINYINT,
        building_type             INT,
        authorised_share          DOUBLE,
        paid_up_members           DOUBLE,
        paid_up_government_bodies DOUBLE,
        paid_up_total             DOUBLE,
        individual_member         MEDIUMINT,
        institutional_member      MEDIUMINT,
        annual_turn_over          DOUBLE,
        pool_resource             TINYINT,
        any_resource_taken        TINYINT,
        is_right_vested           TINYINT,
        facilities                VARCHAR(100),
        updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_transport (
        id                         INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                     VARCHAR(40) UNIQUE,
        st_code                    INT,
        dist_code                  INT,
        type_society               INT,
        has_building               TINYINT,
        building_type              INT,
        authorised_share           DOUBLE,
        paid_up_members            DOUBLE,
        paid_up_government_bodies  DOUBLE,
        paid_up_total              DOUBLE,
        annual_turn_over           DOUBLE,
        individual_member          MEDIUMINT,
        institutional_member       MEDIUMINT,
        type_owner                 INT,
        bus_type_detail            INT,
        truck_type_detail          INT,
        other_type_detail          INT,
        no_passenger_vehicle       BIGINT,
        no_member_travel           BIGINT,
        no_freight_vehicle         BIGINT,
        quantity_good_transport    BIGINT,
        member_themself            TINYINT,
        is_user_transport_facility TINYINT,
        updated_at                 TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_tribal (
        id                           INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                       VARCHAR(40) UNIQUE,
        st_code                      INT,
        dist_code                    INT,
        type_society                 INT,
        has_building                 TINYINT,
        building_type                INT,
        authorised_share             DOUBLE,
        paid_up_members              DOUBLE,
        paid_up_government_bodies    DOUBLE,
        paid_up_total                DOUBLE,
        annual_turn_over             DOUBLE,
        state_district_federation    TINYINT,
        society_provide_raw_material TINYINT,
        facilities                   VARCHAR(100),
        updated_at                   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_ucb (
        id                        INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                    VARCHAR(40) UNIQUE,
        st_code                   INT,
        dist_code                 INT,
        has_building              TINYINT,
        building_type             INT,
        ucb_branch                DOUBLE,
        has_nafcub                TINYINT,
        authorised_share          DOUBLE,
        annual_turn_over          DOUBLE,
        annual_income             DOUBLE,
        annual_ucb_expenditr      DOUBLE,
        asset_ucb                 DOUBLE,
        liability_ucb             DOUBLE,
        total_deposit             DOUBLE,
        loan_outstanding          DOUBLE,
        is_gov_scheme_implemented TINYINT,
        is_computerized           TINYINT,
        no_computer_working       INT,
        have_ifsc                 TINYINT,
        have_corebanking          TINYINT,
        have_doorstepservice      TINYINT,
        is_aeps                   TINYINT,
        offer_debitcard           TINYINT,
        have_internetbanking      TINYINT,
        offer_creditcard          TINYINT,
        cibil_membership          TINYINT,
        conducting_gab            TINYINT,
        cgtmsemli_member          TINYINT,
        is_saf_to_cust            TINYINT,
        networth                  DOUBLE,
        fswm_comp                 TINYINT,
        updated_at                TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_wocoop (
        id                    INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                VARCHAR(40) UNIQUE,
        st_code               INT,
        dist_code             INT,
        type_society          INT,
        has_building          TINYINT,
        building_type         INT,
        authorised_share      DOUBLE,
        annual_turn_over      DOUBLE,
        is_raw_material_taken TINYINT,
        facilities            VARCHAR(100),
        updated_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_khadi_gram (
        id                                          INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id                                      VARCHAR(40) UNIQUE,
        st_code                                     INT,
        dist_code                                   INT,
        has_building                                TINYINT,
        building_type                               INT,
        authorised_share                            DOUBLE,
        paid_up_members                             DOUBLE,
        paid_up_government_bodies                   DOUBLE,
        paid_up_total                               DOUBLE,
        annual_turn_over                            DOUBLE,
        individual_member                           MEDIUMINT,
        institutional_member                        MEDIUMINT,
        power_loom_type                             VARCHAR(100),
        hand_loom_type                              VARCHAR(100),
        hand_loom_other_type                        VARCHAR(100),
        no_of_loom                                  BIGINT,
        raw_product_taken                           TINYINT,
        raw_material_available                      TINYINT,
        waste_generate                              TINYINT,
        waste_available                             TINYINT,
        operate_retail                              TINYINT,
        no_of_retail                                BIGINT,
        product_sale_out                            TINYINT,
        operated_member_themself                    TINYINT,
        is_user_work_divide                         TINYINT,
        type_of_activities_khadi_gram               VARCHAR(120),
        do_you_want_to_enter_type_products_produced TINYINT,
        fswm_comp                                   TINYINT,
        updated_at                                  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS ncd_area_of_operation_urban (
        id                   INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id               VARCHAR(40),
        area_of_operation_id INT,
        state_code           INT,
        district_code        INT,
        local_body_type_code INT,
        local_body_code      INT,
        locality_ward_code   INT,
        updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uk_aop_urban (ncd_id, local_body_code, locality_ward_code),
        INDEX idx_ncd (ncd_id)
    );

    CREATE TABLE IF NOT EXISTS ncd_area_of_operation_rural (
        id                   INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id               VARCHAR(40),
        area_of_operation_id INT,
        state_code           INT,
        district_code        INT,
        block_code           INT,
        panchayat_code       INT,
        village_code         INT,
        gp_village_all       TINYINT,
        updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uk_aop_rural (ncd_id, village_code),
        INDEX idx_ncd (ncd_id)
    );
    
    CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_lamp (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id VARCHAR(40) UNIQUE,
        st_code INT,
        dist_code INT,
    
        type_society INT,
        has_building TINYINT,
        building_type INT,
    
        authorised_share DOUBLE,
        paid_up_members DOUBLE,
        paid_up_government_bodies DOUBLE,
        paid_up_total DOUBLE,
    
        total_deposit DOUBLE,
        loan_outstanding DOUBLE,
    
        credit_facility TINYINT,
        total_credit_provided DOUBLE,
    
        fertilizer_distribution TINYINT,
        seed_distribution TINYINT,
        pesticide_distribution TINYINT,
    
        consumer_activity TINYINT,
        marketing_activity TINYINT,
        storage_facility TINYINT,
        storage_capacity DOUBLE,
    
        number_of_members INT,
        number_of_branches INT,
    
        annual_turn_over DOUBLE,
        annual_profit DOUBLE,
        annual_loss DOUBLE,
    
        govt_scheme_implemented TINYINT,
        scheme_details TEXT,
    
        other_facility TEXT,
    
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
        INDEX idx_ncd (ncd_id),
        INDEX idx_state (st_code),
        INDEX idx_dist (dist_code)
);
CREATE TABLE IF NOT EXISTS ncd_cooperative_registrations_fss (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ncd_id VARCHAR(40) UNIQUE,
        st_code INT,
        dist_code INT,
    
        type_society INT,
        has_building TINYINT,
        building_type INT,
    
        authorised_share DOUBLE,
        paid_up_members DOUBLE,
        paid_up_government_bodies DOUBLE,
        paid_up_total DOUBLE,
    
        total_deposit DOUBLE,
        loan_outstanding DOUBLE,
    
        credit_facility TINYINT,
        crop_loan_provided DOUBLE,
        kcc_loan_provided DOUBLE,
    
        fertilizer_distribution TINYINT,
        seed_distribution TINYINT,
        pesticide_distribution TINYINT,
    
        fair_price_shop TINYINT,
        foodgrain_distribution DOUBLE,
    
        storage_facility TINYINT,
        cold_storage TINYINT,
        storage_capacity DOUBLE,
    
        number_of_members INT,
        number_of_branches INT,
    
        annual_turn_over DOUBLE,
        annual_profit DOUBLE,
        annual_loss DOUBLE,
    
        govt_scheme_implemented TINYINT,
        scheme_details TEXT,
    
        is_pacs_integrated TINYINT,
    
        other_facility TEXT,
    
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
        INDEX idx_ncd (ncd_id),
        INDEX idx_state (st_code),
        INDEX idx_dist (dist_code)
);
SQL;

    foreach (array_filter(array_map('trim', explode(';', $ddl))) as $sql) {
        if ($sql) {
            if (!mysqli_query($db, $sql)) {
                log_msg("  [DDL ERROR] " . mysqli_error($db) . " | SQL: " . substr($sql, 0, 80));
            }
        }
    }
    log_msg("All tables verified.");
}

function sync_masters(mysqli $db): void
{
    // States — key is "state_code" per working test
    $data = api_post('/Api/findStateLgCode', ['state_code' => STATE_CODE]);
    if (!empty($data['message'])) {
        foreach ((array)$data['message'] as $row) {
            upsert($db, 'ncd_states', ['state_code' => (int)$row['state_code']], [
                'name' => $row['name'] ?? null,
                'hindi_name' => $row['hindi_name'] ?? null,
                'state_or_ut' => $row['state_or_ut'] ?? null,
                'sr_no' => $row['sr_no'] ?? null,
            ]);
        }
        log_saved($db, 'States', count((array)$data['message']));
    }

//    $data = api_post('/en/Api/findNcdSectorCode', []);
    $data = api_post('/en/Api/findNcdSectorCode', [
        'state_code' => 9]);
    if (!empty($data['message'])) {
        foreach ((array)$data['message'] as $row) {
            upsert($db, 'ncd_sectors', ['id' => $row['id']], [
                'name' => $row['name'] ?? null,
                'hindi_name' => $row['hindi_name'] ?? null,
            ]);
        }
        log_saved($db, 'Sectors', count((array)$data['message']));
    }

    sync_misc_level($db, 15, 'ncd_sub_sectors', 'Sub Sector Details', function (array $r): array {
        return [
            'sub_sector_id' => $r['sub_sector_id'],
            'primary_activities_id' => $r['primary_activities_id'] ?? null,
            'sub_sector_name' => $r['sub_sector_name'] ?? null,
            'sub_sector_name_hindi' => $r['sub_sector_name_hindi'] ?? null,
        ];
    }, 'sub_sector_id');

    $miscMap = [
        8 => ['ncd_audit_categories', 'Society Audit Years Details', 'id',
            fn($r) => ['id' => $r['id'], 'name' => $r['name'] ?? null, 'audit_categories_hindi' => $r['audit_categories_hindi'] ?? null]],
        5 => ['ncd_type_of_activities_khadi_gram', 'Type Of Activities Khadi Gram Details', 'id',
            fn($r) => ['id' => $r['id'], 'type_of_activities_name' => $r['type_of_activities_name'] ?? null, 'type_of_activities_name_hindi' => $r['type_of_activities_name_hindi'] ?? null]],
        9 => ['ncd_registration_authorities', 'Registration Authorities Details', 'id',
            fn($r) => ['id' => $r['id'], 'authority_name' => $r['authority_name'] ?? null, 'authority_name_hindi' => $r['authority_name_hindi'] ?? null, 'primary_activity' => $r['primary_activity'] ?? null]],
        10 => ['ncd_area_of_operations', 'Area Of Operations Details', 'id',
            fn($r) => ['id' => $r['id'], 'urban_rural' => $r['urban_rural'] ?? null, 'name' => $r['name'] ?? null]],
        11 => ['ncd_cooperative_society_banks', 'Area Of Operations Details', 'id',
            fn($r) => ['id' => $r['id'], 'bank_name' => $r['bank_name'] ?? null, 'bank_type' => $r['bank_type'] ?? null]],
        12 => ['ncd_designations', 'Designations Details', 'id',
            fn($r) => ['id' => $r['id'], 'name' => $r['name'] ?? null, 'designations_hindi' => $r['designations_hindi'] ?? null]],
        13 => ['ncd_cooperative_society_facilities', 'Cooperative Society Facilities Details', 'id',
            fn($r) => ['id' => $r['id'], 'name' => $r['name'] ?? null, 'primary_activity_id' => $r['primary_activity_id'] ?? null]],
        16 => ['ncd_office_building_types', 'Sub Sector Details', 'id',
            fn($r) => ['id' => $r['id'], 'name' => $r['name'] ?? null, 'office_building_types_hindi' => $r['office_building_types_hindi'] ?? null]],
        17 => ['ncd_water_body_types', 'Sub Sector Details', 'id',
            fn($r) => ['id' => $r['id'], 'name' => $r['name'] ?? null, 'water_body_types_hindi' => $r['water_body_types_hindi'] ?? null]],
    ];

    foreach ($miscMap as $level => [$table, $resultKey, $uk, $mapper]) {
        sync_misc_level($db, $level, $table, $resultKey, $mapper, $uk);
    }

    sync_misc_level($db, 3, 'ncd_society_implementing_schemes',
        'Society Implementing Schemes Details',
        fn($r) => [
    'ncd_id' => $r['ncd_id'],
    'gov_scheme_name' => $r['gov_scheme_name'] ?? null,
    'gov_scheme_type' => $r['gov_scheme_type'] ?? null,
    'total_amount' => $r['total_amount'] ?? null,
    'st_code' => $r['st_code'] ?? null,
    'dist_code' => $r['dist_code'] ?? null,
], null
    );

    sync_misc_level($db, 4, 'ncd_cooperative_registrations_lands',
        'Cooperative Registrations Lands Details',
        fn($r) => [
    'ncd_id' => $r['ncd_id'],
    'land_owned' => $r['land_owned'] ?? null,
    'land_leased' => $r['land_leased'] ?? null,
    'land_allotted' => $r['land_allotted'] ?? null,
    'land_total' => $r['land_total'] ?? null,
    'st_code' => $r['st_code'] ?? null,
    'dist_code' => $r['dist_code'] ?? null,
], 'ncd_id'
    );

    sync_misc_level($db, 6, 'ncd_members_details',
        'Members Details',
        fn($r) => [
    'ncd_id' => $r['ncd_id'],
    'm_general' => $r['m_general'] ?? 0, 'm_sc' => $r['m_sc'] ?? 0,
    'm_st' => $r['m_st'] ?? 0, 'm_obc' => $r['m_obc'] ?? 0,
    'f_general' => $r['f_general'] ?? 0, 'f_sc' => $r['f_sc'] ?? 0,
    'f_st' => $r['f_st'] ?? 0, 'f_obc' => $r['f_obc'] ?? 0,
    't_general' => $r['t_general'] ?? 0, 't_sc' => $r['t_sc'] ?? 0,
    't_st' => $r['t_st'] ?? 0, 't_obc' => $r['t_obc'] ?? 0,
    'st_code' => $r['st_code'] ?? null,
    'dist_code' => $r['dist_code'] ?? null,
], 'ncd_id'
    );

    sync_misc_level($db, 7, 'ncd_society_audit_years',
        'Society Audit Years Details',
        fn($r) => [
    'ncd_id' => $r['ncd_id'],
    'audit_category' => $r['audit_category'] ?? null,
    'audit_year' => $r['audit_year'] ?? null,
    'sector_type' => $r['sector_type'] ?? null,
    'sector' => $r['sector'] ?? null,
    'annual_profit' => nullNum($r['annual_profit'] ?? null),
    'annual_loss' => nullNum($r['annual_loss'] ?? null),
    'state_code' => $r['state_code'] ?? null,
    'district_code' => $r['district_code'] ?? null,
    'share_capital' => nullNum($r['share_capital'] ?? null),
    'reserve_fund' => nullNum($r['reserve_fund'] ?? null),
    'revenue' => nullNum($r['revenue'] ?? null),
    'deposit' => nullNum($r['deposit'] ?? null),
    'loan_and_advance' => nullNum($r['loan_and_advance'] ?? null),
    'borrowings' => nullNum($r['borrowings'] ?? null),
    'total_assets' => nullNum($r['total_assets'] ?? null),
    'no_of_members' => $r['no_of_members'] ?? null,
    'no_of_branches' => $r['no_of_branches'] ?? null,
    'total_credit_provided' => nullNum($r['total_credit_provided'] ?? null),
    'paid_up_share' => nullNum($r['paid_up_share'] ?? null),
    'annual_turnover' => nullNum($r['annual_turnover'] ?? null),
    'annual_income' => nullNum($r['annual_income'] ?? null),
    'annual_ucb_expenditr' => nullNum($r['annual_ucb_expenditr'] ?? null),
    'asset_ucb' => nullNum($r['asset_ucb'] ?? null),
    'liability_ucb' => nullNum($r['liability_ucb'] ?? null),
    'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
    'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
    'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
    'annual_expenses' => nullNum($r['annual_expenses'] ?? null),
], null
    );

    sync_misc_level($db, 18, 'ncd_board_of_directors',
        'Board Of Director Details',
        fn($r) => [
    'ncd_id' => $r['ncd_id'] ?? null,
    'bod_name' => $r['bod_name'] ?? null,
    'father_name' => $r['father_name'] ?? null,
    'gender' => $r['gender'] ?? null,
    'mobile_number' => (string)($r['mobile_number'] ?? ''),
    'email_id' => $r['email_id'] ?? null,
    'id_proof_type' => $r['id_proof_type'] ?? null,
    'id_proof_no' => $r['id_proof_no'] ?? null,
    'bod_designation' => $r['bod_designation'] ?? null,
    'from_date' => dateOrNull($r['from_date'] ?? null),
    'to_date' => dateOrNull($r['to_date'] ?? null),
    'is_any_other_cooperative' => $r['is_any_other_cooperative'] ?? null,
    'other_cooperative_society_name' => $r['other_cooperative_society_name'] ?? null,
    'sector_code' => $r['sector_code'] ?? null,
    'st_code' => $r['st_code'] ?? null,
    'dist_code' => $r['dist_code'] ?? null,
], null
    );
}

function sync_geo(mysqli $db): void
{
    // Districts — use "state_code" as parameter key
    $data = api_post('/Api/districtdetailsbystate', ['state_code' => STATE_CODE]);
    if (!empty($data['result'])) {
        foreach ($data['result'] as $row) {
            upsert($db, 'ncd_districts', ['district_code' => $row['district_code']], [
                'state_code' => STATE_CODE,
                'district_name' => $row['district_name'] ?? null,
            ]);
        }
        log_saved($db, 'Districts', count($data['result']));
    }

    function api_post_block_data($endpoint, $payload = [])
    {
        $url = "https://api.cooperatives.gov.in" . $endpoint;

        $ch = curl_init($url);

        $jsonData = json_encode($payload);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonData,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ],
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            die("cURL Error: " . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    $data = api_post_block_data('/en/Api/apiforblocksdata', [
        'key' => API_KEY,
        'state_code' => STATE_CODE
    ]);

    if (!empty($data['data']['blocks'])) {

        $n = 0;
        foreach ($data['data']['blocks'] as $item) {

            // STRICT extraction (since structure is confirmed)
            if (!isset($item['Blocks Details'])) {
                continue;
            }

            $row = $item['Blocks Details'];

            // Safety check
            if (empty($row['block_code'])) {
                log_msg("Skipped row (missing block_code): " . json_encode($row));
                continue;
            }

            upsert($db, 'ncd_blocks', ['block_code' => $row['block_code']], [
                'state_code' => $row['state_code'],
                'district_code' => $row['district_code'],
                'block_version' => $row['block_version'], // null safe
                'name' => $row['name'],
                'block_name_hindi' => $row['block_name_hindi'],
                'name_local' => $row['name_local']
            ]);

            $n++;
        }

        log_saved($db, 'Blocks', $n);

    } else {
        log_msg("❌ No blocks data found");
    }

    $data = api_post('/en/Api/apifordistrictsblocksgpvillagesdata', ['state_code' => STATE_CODE]);
    if (!empty($data['result'])) {
        $n = 0;
        foreach ($data['result'] as $item) {
            $r = $item['Districts - Blocks - GP - Villages Details'] ?? $item;
            $fields = [
                'state_code' => $r['state_code'] ?? STATE_CODE,
                'state_name' => $r['state_name'] ?? null,
                'district_code' => $r['district_code'] ?? null,
                'district_name' => $r['district_name'] ?? null,
                'block_code' => $r['block_code'] ?? null,
                'block_name' => $r['block_name'] ?? null,
                'gram_panchayat_code' => $r['gram_panchayat_code'] ?? null,
                'gram_panchayat_name' => $r['gram_panchayat_name'] ?? null,
                'gram_panchayat_name_hindi' => $r['gram_panchayat_name_hindi'] ?? null,
                'village_code' => $r['village_code'] ?? null,
                'village_name' => $r['village_name'] ?? null,
            ];
            upsert($db, 'ncd_state_district_block_gp_village', ['village_code' => $fields['village_code']], $fields);
            $n++;
        }
        log_saved($db, 'GP-Villages', $n);
    }

    $data = api_post('/en/Api/apiforulbdata', ['state_code' => STATE_CODE]);
    if (!empty($data['result'])) {
        $n = 0;
        foreach ($data['result'] as $item) {
            $r = $item['Urban Local Bodies Details'] ?? $item;
            upsert($db, 'ncd_urban_local_bodies', ['localbody_code' => $r['localbody_code']], [
                'state_code' => $r['state_code'] ?? STATE_CODE,
                'state_name' => $r['state_name'] ?? null,
                'district_code' => $r['district_code'] ?? null,
                'district_name' => $r['district_name'] ?? null,
                'localbody_type_code' => $r['localbody_type_code'] ?? null,
                'localbody_type_name' => $r['localbody_type_name'] ?? null,
                'localbody_type_name_hindi' => $r['localbody_type_name_hindi'] ?? null,
                'local_body_name' => $r['local_body_name'] ?? null,
                'local_body_name_hindi' => $r['local_body_name_hindi'] ?? null,
            ]);
            $n++;
        }
        log_saved($db, 'Urban Local Bodies', $n);
    }

    $data = api_post('/en/Api/apiforulbwarddata', ['state_code' => STATE_CODE]);

    if (!empty($data['result']) && is_array($data['result'])) {

        $total = count($data['result']);
        $saved = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($data['result'] as $item) {

            $r = $item['Urban Local Bodies Ward Details'] ?? null;

            // ✅ Validate structure
            if (!$r || !isset($r['ward_code']) || $r['ward_code'] === '') {
                $skipped++;
                continue;
            }

            $wardCode = nvl_int($r['ward_code']);

            // Extra safety (avoid 0 or invalid keys)
            if ($wardCode <= 0) {
                $skipped++;
                continue;
            }

            // ✅ Perform upsert
            $result = upsert($db, 'ncd_urban_local_body_wards',
                [
                    'ward_code' => $wardCode
                ],
                [
                    'state_code' => nvl_int($r['state_code'] ?? STATE_CODE),
                    'state_name' => nvl($r['state_name'] ?? null),
                    'local_body_code' => nvl_int($r['local_body_code'] ?? null),
                    'local_body_name' => nvl($r['local_body_name'] ?? null),
                    'local_body_name_hindi' => nvl($r['local_body_hindi'] ?? null),
                    'ward_name' => nvl($r['ward_name'] ?? null),
                    'ward_name_hindi' => nvl($r['ward_name_hindi'] ?? null),
                ]
            );

            // ✅ Count properly
            if ($result) {
                $saved++;
            } else {
                $errors++;
                log_msg("DB ERROR: " . $db->error . " | ward_code: " . $wardCode);
            }
        }

        // ✅ Final logging (accurate)
        log_msg("ULB Wards Sync → Total: $total | Saved: $saved | Skipped: $skipped | Errors: $errors");

        log_saved($db, 'ULB Wards', $saved);
    }
}

function sync_cooperatives(mysqli $db): void
{
    $data = api_post('/MasterApi/stateWiseBasicDetails', [
        'state_code' => STATE_CODE
    ]);

    if (empty($data['result'])) {
        log_msg("No cooperative data returned.");
        return;
    }

    $rows = $data['result'];
    $chunkSize = 500;
    $total = 0;

    foreach (array_chunk($rows, $chunkSize) as $chunk) {

        $values = [];
        $params = [];
        $types  = '';

        foreach ($chunk as $r) {

            // ✅ 55 placeholders EXACT
            $values[] = "(" . implode(",", array_fill(0, 54, "?")) . ")";

            $mapped = [

                // PRIMARY
                nvl($r['cooperative_id'] ?? null),

                // BASIC
                nvl($r['cooperative_society_name'] ?? null),
                nvl($r['local_langauge_society_name'] ?? null),
                nvl_int($r['registration_authoritie_id'] ?? null),
                nvl_int($r['reference_year'] ?? null),
                dateOrNull($r['date_registration'] ?? null),
                nvl($r['registration_number'] ?? null),

                // CLASSIFICATION
                nvl_int($r['cooperative_society_type_id'] ?? null),
                nvl_int($r['area_of_operation_id'] ?? null),
                nvl_int($r['water_body_type_id'] ?? null),
                nvl_int($r['sector_of_operation_type'] ?? null),
                nvl_int($r['sector_of_operation'] ?? null),
                nvl_int($r['functional_status'] ?? null),

                // LOCATION
                nvl_int($r['location_of_head_quarter'] ?? null),
                nvl_int($r['state_code'] ?? null),
                nvl_int($r['district_code'] ?? null),
                nvl_int($r['block_code'] ?? null),
                nvl_int($r['gram_panchayat_code'] ?? null),
                nvl_int($r['village_code'] ?? null),
                nvl_int($r['urban_local_body_type_code'] ?? null),
                nvl_int($r['urban_local_body_code'] ?? null),
                nvl_int($r['locality_ward_code'] ?? null),
                nvl_int($r['pincode'] ?? null),

                // ADDRESS
                nvl($r['full_address'] ?? null),
                nvl($r['address_line'] ?? null),

                // CONTACT
                nvl($r['contact_person'] ?? null),
                nvl_int($r['designation'] ?? null),
                nvl((string)($r['mobile'] ?? '')),
                nvl($r['landline'] ?? null),
                nvl($r['email'] ?? null),

                // SECRETARY
                nvl($r['full_time_secretary'] ?? null),
                nvl((string)($r['mobile_number_of_secretary'] ?? '')),
                nvl($r['alternate_contact_no_for_pacs'] ?? null),
                nvl($r['pacs_id'] ?? null),

                // FLAGS
                nvl_int($r['is_approved'] ?? null),
                nvl_int($r['is_coastal'] ?? null),
                nvl_int($r['is_affiliated_union_federation'] ?? null),
                nvl_int($r['financial_audit'] ?? null),
                nvl_int($r['is_profit_making'] ?? null),
                nvl_int($r['is_dividend_paid'] ?? null),

                // MEMBERS
                nvl_int($r['members_of_society'] ?? null),
                nvl_int($r['audit_complete_year'] ?? null),
                nvl_int($r['category_audit'] ?? null),

                // FINANCIAL
                nullNum($r['annual_turnover'] ?? null),
                nullNum($r['annual_profit'] ?? null),
                nullNum($r['annual_loss'] ?? null),
                nullNum($r['dividend_rate'] ?? null),

                // OPERATION
                nvl_int($r['operation_area_location'] ?? null),

                // BANK
                nvl($r['bank_type'] ?? null),
                nvl($r['cooperative_society_bank_id'] ?? null),
                nvl($r['other_bank'] ?? null),

                // IDS
                nvl($r['pan_no'] ?? null),
                nvl($r['gst_no'] ?? null),
                nvl_int($r['how_many_branches'] ?? null),
            ];

            // ✅ IMPORTANT (you removed this earlier)
            foreach ($mapped as $val) {
                $params[] = $val;
                $types .= is_int($val) ? 'i' :
                    (is_float($val) ? 'd' : 's');
            }
        }

        // ✅ FINAL SQL FIXED
        $sql = "
            INSERT INTO ncd_cooperative_registrations (

                cooperative_id,

                cooperative_society_name,
                local_langauge_society_name,
                registration_authoritie_id,
                reference_year,
                date_registration,
                registration_number,

                cooperative_society_type_id,
                area_of_operation_id,
                water_body_type_id,
                sector_of_operation_type,
                sector_of_operation,
                functional_status,

                location_of_head_quarter,
                state_code,
                district_code,
                block_code,
                gram_panchayat_code,
                village_code,
                urban_local_body_type_code,
                urban_local_body_code,
                locality_ward_code,
                pincode,

                full_address,
                address_line,

                contact_person,
                designation,
                mobile,
                landline,
                email,

                full_time_secretary,
                mobile_number_of_secretary,
                alternate_contact_no_for_pacs,
                pacs_id,

                is_approved,
                is_coastal,
                is_affiliated_union_federation,
                financial_audit,
                is_profit_making,
                is_dividend_paid,

                members_of_society,
                audit_complete_year,
                category_audit,

                annual_turnover,
                annual_profit,
                annual_loss,
                dividend_rate,

                operation_area_location,

                bank_type,
                cooperative_society_bank_id,
                other_bank,

                pan_no,
                gst_no,
                how_many_branches
            )
            VALUES " . implode(",", $values) . "

            ON DUPLICATE KEY UPDATE
                cooperative_society_name = VALUES(cooperative_society_name),
                full_address = VALUES(full_address),
                mobile = VALUES(mobile),
                updated_at = NOW()
        ";

        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $total += count($chunk);
    }

    log_saved($db, 'Cooperative Registrations (Bulk)', $total);
}

function sync_sectors(mysqli $db): void
{
    $sectorMap = getSectorMap();

    foreach ($sectorMap as $sectorCode => [$table, $dataKey, $mapper]) {

        log_msg("=== Checking Sector: $sectorCode ===");

        try {

            // ✅ API CALL
            $data = api_post('/en/Api/sectorchildtablerawdatastatewise', [
                'state_code' => STATE_CODE,
                'sector_code' => (string)$sectorCode,
            ]);

            // ❌ EMPTY RESPONSE
            if (empty($data)) {
                log_msg("❌ Sector $sectorCode ($table) → Empty API response");
                continue;
            }

            // ❌ API FAILED
            if (($data['status'] ?? '') !== 'Success') {
                $msg = $data['message'] ?? 'No message';
                log_msg("❌ Sector $sectorCode ($table) → API FAILED: $msg");
                continue;
            }

            // ⚠ NO DATA
            if (empty($data['result'])) {
                log_msg("⚠ Sector $sectorCode ($table) → No data returned");
                continue;
            }

            // ✅ PROCESS DATA
            $n = 0;

            foreach ($data['result'] as $item) {

                // 🔥 IMPORTANT: correct extraction
                $r = $item[$dataKey] ?? null;

                if (!$r || empty($r['ncd_id'])) {
                    continue;
                }

                $row = $mapper($r);

                // ✅ ADD COMMON FIELDS
                $row['ncd_id'] = $r['ncd_id'];
                $row['st_code'] = STATE_CODE;
                $row['dist_code'] = $r['district_code'] ?? null;

                // ✅ SAVE
                upsert($db, $table, ['ncd_id' => $row['ncd_id']], $row);

                $n++;
            }

            log_saved($db, "✅ Sector $sectorCode – $table", $n);

        } catch (Throwable $e) {

            log_msg("💥 Sector $sectorCode ($table) → EXCEPTION: " . $e->getMessage());
        }

        usleep(200000); // delay
    }
}


function sync_aop(mysqli $db): void
{
    // ============================
    // 🏙️ URBAN (level=2)
    // ============================
    $data = api_post('/en/Api/apimiscellanousdata', [
        'key' => API_KEY,
        'state_code' => STATE_CODE,
        'level_code' => 2
    ]);

    $n = 0;

    if (empty($data) || ($data['status'] ?? '') !== 'Success') {
        log_msg("❌ AOP Urban API Failed: " . ($data['message'] ?? 'No response'));
    } else {

        foreach ($data['result'] ?? [] as $item) {

            $r = $item['Area of Operation Urban Details'] ?? $item;

            if (empty($r['ncd_id'])) continue;

            $sql = "INSERT INTO ncd_area_of_operation_urban
                        (ncd_id, area_of_operation_id, state_code, district_code,
                         local_body_type_code, local_body_code, locality_ward_code)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        area_of_operation_id = VALUES(area_of_operation_id),
                        local_body_type_code = VALUES(local_body_type_code),
                        locality_ward_code   = VALUES(locality_ward_code)";

            $stmt = mysqli_prepare($db, $sql);

            // ✅ Safe casting
            $ncd_id = $r['ncd_id'];
            $area_of_operation_id = $r['area_of_operation_id'] ?? 0;
            $state_code = $r['state_code'] ?? STATE_CODE;
            $district_code = $r['district_code'] ?? 0;
            $local_body_type_code = $r['local_body_type_code'] ?? 0;
            $local_body_code = $r['local_body_code'] ?? 0;
            $locality_ward_code = $r['locality_ward_code'] ?? 0;

            mysqli_stmt_bind_param($stmt, 'siiiiii',
                $ncd_id,
                $area_of_operation_id,
                $state_code,
                $district_code,
                $local_body_type_code,
                $local_body_code,
                $locality_ward_code
            );

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $n++;
        }
    }

    log_saved($db, 'AOP Urban', $n);

    // ============================
    // 🌾 RURAL (level=1)
    // ============================
    $page = 1;
    $totalSaved = 0;
    $totalPages = 1;

    do {

        $data = api_post('/en/Api/apimiscellanousdata', [
            'key' => API_KEY,
            'state_code' => STATE_CODE,
            'level_code' => 1,
            'page' => $page
        ]);

        // ❌ API failure handling
        if (empty($data) || ($data['status'] ?? '') !== 'Success') {
            log_msg("❌ AOP Rural API Failed at page $page: " . ($data['message'] ?? 'No response'));
            break;
        }

        if (empty($data['result'])) {
            log_msg("⚠ AOP Rural page $page → No data");
            break;
        }

        foreach ($data['result'] as $item) {

            $r = $item['Area of Operation Rural Details'] ?? $item;

            if (empty($r['ncd_id'])) continue;

            $sql = "INSERT INTO ncd_area_of_operation_rural
                        (ncd_id, area_of_operation_id, state_code, district_code,
                         block_code, panchayat_code, village_code, gp_village_all)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        area_of_operation_id = VALUES(area_of_operation_id),
                        block_code           = VALUES(block_code),
                        panchayat_code       = VALUES(panchayat_code),
                        gp_village_all       = VALUES(gp_village_all)";

            $stmt = mysqli_prepare($db, $sql);

            $ncd_id = $r['ncd_id'];
            $aop_id = $r['area_of_operation_id'] ?? 0;
            $state_code = $r['state_code'] ?? STATE_CODE;
            $district_code = $r['district_code'] ?? 0;
            $block_code = $r['block_code'] ?? 0;
            $panchayat_code = $r['panchayat_code'] ?? 0;
            $village_code = $r['village_code'] ?? 0;
            $gp_village_all = $r['gp_village_all'] ?? '';

            mysqli_stmt_bind_param($stmt, 'siiiiiis',
                $ncd_id,
                $aop_id,
                $state_code,
                $district_code,
                $block_code,
                $panchayat_code,
                $village_code,
                $gp_village_all
            );

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $totalSaved++;
        }

        $totalPages = $data['pagination']['total_pages'] ?? 1;

        log_msg("✅ AOP Rural page $page / $totalPages");

        $page++;

        usleep(200000); // prevent API throttle

    } while ($page <= $totalPages);

    log_saved($db, 'AOP Rural', $totalSaved);
}

function getSectorMap(): array
{
    return [
        // ── 1  PACS ─────────────────────────────────────────────────────────────
        1 => [
            'ncd_cooperative_registration_pacs',
            'PACS Details',
            fn($r) => [
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'is_socitey_has_land' => nvl_int($r['is_socitey_has_land'] ?? null),

                'total_outstanding_loan' => nullNum($r['total_outstanding_loan'] ?? null),
                'pack_revenue_non_credit' => nullNum($r['pack_revenue_non_credit'] ?? null),

                'fertilizer_distribution' => nvl_int($r['fertilizer_distribution'] ?? null),
                'fertilizer_distribution_qty' => nullNum($r['fertilizer_distribution_qty'] ?? null),
                'fertilizer_distribution_details' => nvl($r['fertilizer_distribution_details'] ?? null),

                'pesticide_distribution' => nvl_int($r['pesticide_distribution'] ?? null),
                'pesticide_distribution_qty' => nullNum($r['pesticide_distribution_qty'] ?? null),

                'seed_distribution' => nvl_int($r['seed_distribution'] ?? null),
                'seed_distribution_qty' => nullNum($r['seed_distribution_qty'] ?? null),

                'is_foodgrains' => nvl_int($r['is_foodgrains'] ?? null),
                'foodgrains_qty' => nullNum($r['foodgrains_qty'] ?? null),

                'agricultural_implements' => nvl_int($r['agricultural_implements'] ?? null),
                'agricultural_implements_text' => nvl($r['agricultural_implements_text'] ?? null),

                'dry_storage' => nvl_int($r['dry_storage'] ?? null),
                'dry_storage_capicity' => nullNum($r['dry_storage_capicity'] ?? null),

                'cold_storage' => nvl_int($r['cold_storage'] ?? null),
                'cold_storage_capicity' => nullNum($r['cold_storage_capicity'] ?? null),

                'milk_unit' => nvl_int($r['milk_unit'] ?? null),
                'milk_capicity_unit' => nullNum($r['milk_capicity_unit'] ?? null),

                'pack_involved_fish_catch' => nvl_int($r['pack_involved_fish_catch'] ?? null),
                'pack_annual_fish_catch' => nullNum($r['pack_annual_fish_catch'] ?? null),

                'food_processing' => nvl_int($r['food_processing'] ?? null),
                'food_processing_type' => nvl($r['food_processing_type'] ?? null),

                'is_csc' => nvl_int($r['is_csc'] ?? null),
                'csc_revenue' => nullNum($r['csc_revenue'] ?? null),
                'csc_details' => nvl($r['csc_details'] ?? null),

                'is_fpo' => nvl_int($r['is_fpo'] ?? null),
                'fpo_details' => nvl($r['fpo_details'] ?? null),

                'is_lpg_distributership' => nvl_int($r['is_lpg_distributership'] ?? null),
                'lpg_distributership_details' => nvl($r['lpg_distributership_details'] ?? null),

                'is_bcp_pump' => nvl_int($r['is_bcp_pump'] ?? null),
                'bcp_pump_details' => nvl($r['bcp_pump_details'] ?? null),

                'is_dpp_diesel' => nvl_int($r['is_dpp_diesel'] ?? null),
                'dpp_diesel_details' => nvl($r['dpp_diesel_details'] ?? null),

                'is_jak' => nvl_int($r['is_jak'] ?? null),
                'jak_qty' => nullNum($r['jak_qty'] ?? null),

                'is_pmksk' => nvl_int($r['is_pmksk'] ?? null),
                'pmksk_details' => nvl($r['pmksk_details'] ?? null),

                'is_pm_kusum_scheme' => nvl_int($r['is_pm_kusum_scheme'] ?? null),
                'pm_kusum_scheme_details' => nvl($r['pm_kusum_scheme_details'] ?? null),

                'fair_price' => nvl_int($r['fair_price'] ?? null),
                'fair_price_qty' => nullNum($r['fair_price_qty'] ?? null),
                'fair_price_details' => nvl($r['fair_price_details'] ?? null),

                'is_paani_samity' => nvl_int($r['is_paani_samity'] ?? null),
                'paani_samity_details' => nvl($r['paani_samity_details'] ?? null),

                'other_facility' => nvl($r['other_facility'] ?? null),

                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_share' => nullNum($r['paid_up_share'] ?? null),
                'total_deposit' => nullNum($r['total_deposit'] ?? null),
            ]
        ],
        // ── 77  Agriculture ───────────────────────────────────────────────────
        77 => ['ncd_cooperative_registrations_agriculture',
            'Agriculture & Allied Cooperative Details',
            fn($r) => [
                'type_society' => nvl($r['type_society'] ?? null),
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'individual_member' => nvl_int($r['individual_member'] ?? null),
                'institutional_member' => nvl_int($r['institutional_member'] ?? null),
                'has_pool_land' => nvl_int($r['has_pool_land'] ?? null),
                'has_gov_land' => nvl_int($r['has_gov_land'] ?? null),
                'member_vested_right' => nvl_int($r['member_vested_right'] ?? null),
                'is_member_work' => nvl_int($r['is_member_work'] ?? null),
                'society_common_pool' => nvl_int($r['society_common_pool'] ?? null),
                'is_utilize_pool' => nvl_int($r['is_utilize_pool'] ?? null),
                'harvesting' => nvl_int($r['harvesting'] ?? null),
                'farming_mech' => nvl($r['farming_mech'] ?? null),
                'irrigation_means' => nvl($r['irrigation_means'] ?? null),
            ]],

        // ── 2  Processing ─────────────────────────────────────────────────────
        31 => ['ncd_cooperative_registrations_processing',
            'Processing Details',
            fn($r) => [
                'individual_member' => nvl_int($r['individual_member'] ?? null),
                'institutional_member' => nvl_int($r['institutional_member'] ?? null),
                'total_member' => nvl_int($r['total_member'] ?? ($r['total_members'] ?? null)),
                'type_society' => nvl_int($r['type_society'] ?? null),
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'processing_unit' => nvl_int($r['processing_unit'] ?? null),
                'processing_unit_number' => nvl_int($r['processing_unit_number'] ?? null),
                'processing_by_members' => nvl_int($r['processing_by_members'] ?? null),
                'work_divided' => nvl_int($r['work_divided'] ?? null),
                'product_taken' => nvl_int($r['product_taken'] ?? null),
                'material_available' => nvl_int($r['material_available'] ?? null),
                'wastes_generated' => nvl_int($r['wastes_generated'] ?? null),
                'waste_management_facility' => nvl_int($r['waste_management_facility'] ?? null),
                'operate_shops' => nvl_int($r['operate_shops'] ?? null),
                'operate_shops_number' => nvl_int($r['operate_shops_number'] ?? null),
                'product_sale_out_of_area' => nvl_int($r['product_sale_out_of_area'] ?? null),
            ]],

        // ── 79  Bee ───────────────────────────────────────────────────────────
        79 => ['ncd_cooperative_registrations_bee',
            'Bee Cooperative Details',
            fn($r) => [
                'type_bee' => nvl_int($r['type_bee'] ?? null),
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'common_yard' => nvl_int($r['common_yard'] ?? null),
                'no_of_behives' => nvl_int($r['no_of_behives'] ?? null),
                'type_behives' => nvl_int($r['type_behives'] ?? null),
                'rear_by_member' => nvl_int($r['rear_by_member'] ?? null),
                'guidance_by_member' => nvl_int($r['guidance_by_member'] ?? null),
                'type_product' => nvl($r['type_product'] ?? null),
                'is_bee_plant_grow' => nvl_int($r['is_bee_plant_grow'] ?? null),
                'is_cleaning_process' => nvl_int($r['is_cleaning_process'] ?? null),
                'is_waste_facility' => nvl_int($r['is_waste_facility'] ?? null),
                'own_brand_honey' => nvl_int($r['own_brand_honey'] ?? null),
                'is_operate_retail' => nvl_int($r['is_operate_retail'] ?? null),
                'no_of_retail' => nvl_int($r['no_of_retail'] ?? null),
                'is_product_sale_out' => nvl_int($r['is_product_sale_out'] ?? null),
                'facilities' => nvl($r['facilities'] ?? null),
            ]],

        // ── 80  Consumer ──────────────────────────────────────────────────────
        80 => ['ncd_cooperative_registrations_consumer',
            'Consumer Cooperative Details',
            fn($r) => [
                'has_building' => nvl_int($r['has_building'] ?? null),
                'has_store' => nvl_int($r['has_store'] ?? null),
                'no_of_outlets' => nvl_int($r['no_of_outlets'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_share' => nullNum($r['paid_up_share'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'facilities' => nvl($r['facilities'] ?? null),
            ]],

        // ── 18  Credit & Thrift ───────────────────────────────────────────────
        18 => ['ncd_cooperative_registrations_credit_thrift',
            'Credit And Thrift Details',
            fn($r) => [
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_share' => nullNum($r['paid_up_share'] ?? null),
                'total_deposit' => nullNum($r['total_deposit'] ?? null),
                'pack_total_outstanding_loan' => nullNum($r['pack_total_outstanding_loan'] ?? null),
                'facilities' => nvl($r['facilities'] ?? null),
            ]],

        // ── 9  Dairy ──────────────────────────────────────────────────────────
        9 => ['ncd_cooperative_registration_dairy',
            'Dairy Details',
            fn($r) => [
                'milk_collection' => nvl_int($r['milk_collection'] ?? null),
                'credit_facility' => nvl_int($r['credit_facility'] ?? null),
                'credit_provided' => nullNum($r['credit_provided'] ?? null),
                'milk_collection_unit' => nvl_int($r['milk_collection_unit'] ?? null),
                'milk_collection_capicity' => nvl_int($r['milk_collection_capicity'] ?? null),
                'transport_milk' => nvl_int($r['transport_milk'] ?? null),
                'bulk_milk_unit' => nvl_int($r['bulk_milk_unit'] ?? null),
                'milk_testing' => nvl_int($r['milk_testing'] ?? null),
                'processing' => nvl_int($r['processing'] ?? null),
                'other_facility' => nvl($r['other_facility'] ?? null),
                'is_bank_mitra' => nvl_int($r['is_bank_mitra'] ?? null),
                'bank_mitra_details' => nvl($r['bank_mitra_details'] ?? null),
                'is_micro_atm' => nvl_int($r['is_micro_atm'] ?? null),
                'micro_atm_details' => nvl($r['micro_atm_details'] ?? null),
            ]],

        // ── 84  Education ─────────────────────────────────────────────────────
        84 => ['ncd_cooperative_registrations_education',
            'Education Cooperative Details',
            fn($r) => [
                'type_society' => nvl_int($r['type_society'] ?? null),
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'has_land' => nvl_int($r['has_land'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'individual_member' => nvl_int($r['individual_member'] ?? null),
                'institutional_member' => nvl_int($r['institutional_member'] ?? null),
                'level_of_edu' => nvl_int($r['level_of_edu'] ?? null),
                'duration_of_course' => nvl_int($r['duration_of_course'] ?? null),
                'level_and_duration_of_course' => nvl($r['level_and_duration_of_course'] ?? null),
                'course_in_audit' => nullNum($r['course_in_audit'] ?? null),
                'stu_in_audit' => nullNum($r['stu_in_audit'] ?? null),
                'training_course_in_audit' => nullNum($r['training_course_in_audit'] ?? null),
                'participants_in_audit' => nullNum($r['participants_in_audit'] ?? null),
                'course_international_participant' => nvl_int($r['course_international_participant'] ?? null),
                'no_of_training_course' => nvl_int($r['no_of_training_course'] ?? null),
                'attended_training' => nvl_int($r['attended_training'] ?? null),
                'society_recruit' => nvl_int($r['society_recruit'] ?? null),
                'no_regular_faculty' => nvl_int($r['no_regular_faculty'] ?? null),
                'no_other_faculty' => nvl_int($r['no_other_faculty'] ?? null),
                'facilities' => nvl($r['facilities'] ?? null),
            ]],

        // ── 10  Fishery ───────────────────────────────────────────────────────
        10 => ['ncd_cooperative_registration_fishery',
            'Fishery Details',
            fn($r) => [
                'annual_fish_catch' => nullNum($r['annual_fish_catch'] ?? null),
                'credit_facility' => nvl_int($r['credit_facility'] ?? null),
                'total_credit_provided' => nullNum($r['total_credit_provided'] ?? null),
                'fuel_distribution' => nvl_int($r['fuel_distribution'] ?? null),
                'marketing' => nvl_int($r['marketing'] ?? null),
                'cold_storage' => nvl_int($r['cold_storage'] ?? null),
                'transportation' => nvl_int($r['transportation'] ?? null),
                'other_facility' => nvl($r['other_facility'] ?? null),
                'is_fpo_fisheries' => nvl_int($r['is_fpo_fisheries'] ?? null),
                'fpo_fisheries_details' => nvl($r['fpo_fisheries_details'] ?? null),
            ]],

        // ── 14  Handicraft ────────────────────────────────────────────────────
        14 => ['ncd_cooperative_registrations_handicraft',
            'Handicraft Details',
            fn($r) => [
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'individual_member' => nvl_int($r['individual_member'] ?? null),
                'institutional_member' => nvl_int($r['institutional_member'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'type_raw' => nvl_int($r['type_raw'] ?? null),
                'type_produce' => nvl_int($r['type_produce'] ?? null),
                'common_work_place' => nvl_int($r['common_work_place'] ?? null),
                'workplace_operate' => nvl_int($r['workplace_operate'] ?? null),
                'is_work_by_member' => nvl_int($r['is_work_by_member'] ?? null),
                'is_training_provide' => nvl_int($r['is_training_provide'] ?? null),
                'is_raw_provide' => nvl_int($r['is_raw_provide'] ?? null),
                'is_raw_easy_avail' => nvl_int($r['is_raw_easy_avail'] ?? null),
                'is_waste_generate' => nvl_int($r['is_waste_generate'] ?? null),
                'is_waste_facility' => nvl_int($r['is_waste_facility'] ?? null),
                'is_operate_retail' => nvl_int($r['is_operate_retail'] ?? null),
                'no_of_retail' => nvl_int($r['no_of_retail'] ?? null),
                'is_product_sale_out' => nvl_int($r['is_product_sale_out'] ?? null),
                'facilities' => nvl_int($r['facilities'] ?? null),
            ]],

        // ── 13  Handloom ──────────────────────────────────────────────────────
        13 => ['ncd_cooperative_registrations_handloom',
            'Handloom Details',
            fn($r) => [
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'individual_member' => nvl_int($r['individual_member'] ?? null),
                'institutional_member' => nvl_int($r['institutional_member'] ?? null),
                'power_loom_type' => nvl($r['power_loom_type'] ?? null),
                'hand_loom_type' => nvl($r['hand_loom_type'] ?? null),
                'no_of_loom' => nvl_int($r['no_of_loom'] ?? null),
                'raw_product_taken' => nvl_int($r['raw_product_taken'] ?? null),
                'raw_material_available' => nvl_int($r['raw_material_available'] ?? null),
                'waste_generate' => nvl_int($r['waste_generate'] ?? null),
                'waste_available' => nvl_int($r['waste_available'] ?? null),
                'operate_retail' => nvl_int($r['operate_retail'] ?? null),
                'no_of_retail' => nvl_int($r['no_of_retail'] ?? null),
                'product_sale_out' => nvl_int($r['product_sale_out'] ?? null),
                'operated_member_themself' => nvl_int($r['operated_member_themself'] ?? null),
                'is_user_work_divide' => nvl_int($r['is_user_work_divide'] ?? null),
            ]],

        // ── 47  Housing ───────────────────────────────────────────────────────
        47 => ['ncd_cooperative_registrations_housing',
            'Miscellaneous Credit Cooperative Details',
            fn($r) => [
                'type_society' => nvl_int($r['type_society'] ?? null),
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'has_land' => nvl_int($r['has_land'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'annual_expenses' => nullNum($r['annual_expenses'] ?? null),
                'loan_facilities' => nvl_int($r['loan_facilities'] ?? null),
                'number_of_houses_audit_year' => nvl_int($r['number_of_houses_audit_year'] ?? null),
                'number_of_houses_during_year' => nvl_int($r['number_of_houses_during_year'] ?? null),
                'number_of_houses_construction' => nvl_int($r['number_of_houses_construction'] ?? null),
                'facilities' => nvl_int($r['facilities'] ?? null),
            ]],

        // ── 90  Jute & Coir ───────────────────────────────────────────────────
        90 => ['ncd_cooperative_registrations_jute',
            'Jute & Coir Cooperative Details',
            fn($r) => [
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'type_raw' => nvl($r['type_raw'] ?? null),
                'type_produce' => nvl($r['type_produce'] ?? null),
                'common_work_place' => nvl_int($r['common_work_place'] ?? null),
                'workplace_operate' => nullNum($r['workplace_operate'] ?? null),   // DOUBLE in DDL
                'is_work_by_member' => nvl_int($r['is_work_by_member'] ?? null),
                'is_training_provide' => nvl_int($r['is_training_provide'] ?? null),
                'is_raw_provide' => nvl_int($r['is_raw_provide'] ?? null),
                'is_raw_easy_avail' => nvl_int($r['is_raw_easy_avail'] ?? null),
                'is_waste_generate' => nvl_int($r['is_waste_generate'] ?? null),
                'is_waste_facility' => nvl_int($r['is_waste_facility'] ?? null),
                'is_operate_retail' => nvl_int($r['is_operate_retail'] ?? null),
                'no_of_retail' => nvl_int($r['no_of_retail'] ?? null),
                'is_product_sale_out' => nvl_int($r['is_product_sale_out'] ?? null),
                'facilities' => nvl_int($r['facilities'] ?? null),
            ]],

        // ── 51  Labour ────────────────────────────────────────────────────────
        51 => ['ncd_cooperative_registrations_labour',
            'Labour Cooperative Details',
            fn($r) => [
                'type_society' => nvl_int($r['type_society'] ?? null),
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'annual_expenses' => nullNum($r['annual_expenses'] ?? null),
                'work_allot_state_dist_federation' => nvl_int($r['work_allot_state_dist_federation'] ?? null),
                'work_guide_state_dist_federation' => nvl_int($r['work_guide_state_dist_federation'] ?? null),
                'concession_state_gov' => nvl_int($r['concession_state_gov'] ?? null),
                'concession_centre_gov' => nvl_int($r['concession_centre_gov'] ?? null),
                'facilities' => nvl_int($r['facilities'] ?? null),
            ]],

        // ── 54  Livestock & Poultry ───────────────────────────────────────────
        54 => ['ncd_cooperative_registrations_livestock',
            'Livestock & Poultry Cooperative Details',
            fn($r) => [
                'type_society' => nvl($r['type_society'] ?? null),
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'individual_member' => nullNum($r['individual_member'] ?? null),   // DOUBLE in DDL
                'institutional_member' => nullNum($r['institutional_member'] ?? null),   // DOUBLE in DDL
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'type_produce' => nvl($r['type_produce'] ?? null),
                'common_work_place' => nvl_int($r['common_work_place'] ?? null),
                'is_work_by_member' => nvl_int($r['is_work_by_member'] ?? null),
                'is_training_provide' => nvl_int($r['is_training_provide'] ?? null),
                'is_poultry_feed' => nvl_int($r['is_poultry_feed'] ?? null),
                'is_collected_from_member' => nvl_int($r['is_collected_from_member'] ?? null),
                'is_waste_facility' => nvl_int($r['is_waste_facility'] ?? null),
                'is_operate_retail' => nvl_int($r['is_operate_retail'] ?? null),
                'no_of_retail' => nvl_int($r['no_of_retail'] ?? null),
                'is_product_sale_out' => nvl_int($r['is_product_sale_out'] ?? null),
                'facilities' => nvl($r['facilities'] ?? null),
            ]],

        // ── 82  Marketing ─────────────────────────────────────────────────────
        82 => ['ncd_cooperative_registrations_marketing',
            'Marketing Cooperative Details',
            fn($r) => [
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'has_land' => nvl_int($r['has_land'] ?? null),
                'has_warehouses' => nvl_int($r['has_warehouses'] ?? null),
                'capacity_warehouses' => nullNum($r['capacity_warehouses'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'annual_expenses' => nullNum($r['annual_expenses'] ?? null),
                'liecense_to_sell' => nvl($r['liecense_to_sell'] ?? null),
                'sell_the_item' => nvl($r['sell_the_item'] ?? null),
            ]],

        // ── 35  Miscellaneous Credit ──────────────────────────────────────────
        35 => ['ncd_cooperative_registrations_cmiscellaneous',
            'Miscellaneous Credit Cooperative Details',
            fn($r) => [
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'total_deposit' => nullNum($r['total_deposit'] ?? null),
                'loan_outstanding' => nullNum($r['loan_outstanding'] ?? null),
                'individual_member' => nvl_int($r['individual_member'] ?? null),
                'institutional_member' => nvl_int($r['institutional_member'] ?? null),
                'facilities' => nvl($r['facilities'] ?? null),
            ]],

        // ── 29  Miscellaneous Non-Credit ──────────────────────────────────────
        29 => ['ncd_cooperative_registrations_miscellaneous',
            'Miscellaneous Non Credit Details',
            fn($r) => [
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
            ]],

        // ── 16  Multipurpose ──────────────────────────────────────────────────
        16 => ['ncd_cooperative_registrations_multi',
            'Multipurpose Details',
            fn($r) => [
                'sec_activity' => nvl($r['sec_activity'] ?? null),
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'has_storage' => nvl_int($r['has_storage'] ?? null),
                'storage_capacity' => nullNum($r['storage_capacity'] ?? null),
                'provide_raw' => nvl_int($r['provide_raw'] ?? null),
                'guidance_by_member' => nvl_int($r['guidance_by_member'] ?? null),
                'is_operate_retail' => nvl_int($r['is_operate_retail'] ?? null),
                'no_of_retail' => nvl_int($r['no_of_retail'] ?? null),
                'is_product_sale_out' => nvl_int($r['is_product_sale_out'] ?? null),
                'facilities' => nvl($r['facilities'] ?? null),
            ]],

        // ── 96  Sericulture ───────────────────────────────────────────────────
        96 => ['ncd_cooperative_registrations_sericulture',
            'Sericulture Cooperative Details',
            fn($r) => [
                'type_society' => nvl($r['type_society'] ?? null),
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'common_work_place' => nvl_int($r['common_work_place'] ?? null),
                'no_rear_house' => nvl_int($r['no_rear_house'] ?? null),
                'is_work_by_member' => nvl_int($r['is_work_by_member'] ?? null),
                'is_training_provide' => nvl_int($r['is_training_provide'] ?? null),
                'is_rear_appliance' => nvl_int($r['is_rear_appliance'] ?? null),
                'is_mulberry_easy_available' => nvl_int($r['is_mulberry_easy_available'] ?? null),
                'is_cleaning_facility_cocoon' => nvl_int($r['is_cleaning_facility_cocoon'] ?? null),
                'is_spinning_weav' => nvl_int($r['is_spinning_weav'] ?? null),
                'is_waste_facility' => nvl_int($r['is_waste_facility'] ?? null),
                'is_operate_retail' => nvl_int($r['is_operate_retail'] ?? null),
                'no_of_retail' => nvl_int($r['no_of_retail'] ?? null),
                'facilities' => nvl($r['facilities'] ?? null),
            ]],

        // ── 98  Social ────────────────────────────────────────────────────────
        98 => ['ncd_cooperative_registrations_social',
            'Social Cooperative Details',
            fn($r) => [
                'type_society' => nvl($r['type_society'] ?? null),
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'type_social_culture_activity' => nvl($r['type_social_culture_activity'] ?? null),
                'has_common' => nvl_int($r['has_common'] ?? null),
                'is_operate_by_member' => nvl_int($r['is_operate_by_member'] ?? null),
                'guidance_by_member' => nvl_int($r['guidance_by_member'] ?? null),
                'is_operate_vehicle' => nvl_int($r['is_operate_vehicle'] ?? null),
                'no_of_vehicle' => nvl_int($r['no_of_vehicle'] ?? null),
                'facilities' => nvl($r['facilities'] ?? null),
            ]],

        // ── 11  Sugar ─────────────────────────────────────────────────────────
        11 => ['ncd_cooperative_registrations_sugar',
            'Sugar Details',
            fn($r) => [
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'suger_mills_no' => nvl_int($r['suger_mills_no'] ?? null),
                'build_up_area' => nullNum($r['build_up_area'] ?? null),
                'open_land_area' => nullNum($r['open_land_area'] ?? null),
                'total_area' => nullNum($r['total_area'] ?? null),
                'liecensed_capicity' => nullNum($r['liecensed_capicity'] ?? null),
                'installed_capicity' => nullNum($r['installed_capicity'] ?? null),
                'crushing_period_start' => dateOrNull($r['crushing_period_start'] ?? null),
                'crushing_period_end' => dateOrNull($r['crushing_period_end'] ?? null),
                'product_produced' => nvl($r['product_produced'] ?? null),
                'retail_shops' => nvl_int($r['retail_shops'] ?? null),
                'retail_shops_no' => nvl_int($r['retail_shops_no'] ?? null),
                'sugercane_input_provided' => nvl_int($r['sugercane_input_provided'] ?? null),
                'loan_facility' => nvl_int($r['loan_facility'] ?? null),
                'waste_management' => nvl_int($r['waste_management'] ?? null),
                'central_government_benefits' => nvl_int($r['central_government_benefits'] ?? null),
                'state_government_benefits' => nvl_int($r['state_government_benefits'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
            ]],

        // ── 99  Tourism ───────────────────────────────────────────────────────
        99 => ['ncd_cooperative_registrations_tourism',
            'Tourism Cooperative Details',
            fn($r) => [
                'type_society' => nvl_int($r['type_society'] ?? null),
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'individual_member' => nvl_int($r['individual_member'] ?? null),
                'institutional_member' => nvl_int($r['institutional_member'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'pool_resource' => nvl_int($r['pool_resource'] ?? null),
                'any_resource_taken' => nvl_int($r['any_resource_taken'] ?? null),
                'is_right_vested' => nvl_int($r['is_right_vested'] ?? null),
                'facilities' => nvl($r['facilities'] ?? null),
            ]],

        // ── 68  Transport ─────────────────────────────────────────────────────
        68 => ['ncd_cooperative_registrations_transport',
            'Transport Cooperative Details',
            fn($r) => [
                'type_society' => nvl_int($r['type_society'] ?? null),
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'individual_member' => nvl_int($r['individual_member'] ?? null),
                'institutional_member' => nvl_int($r['institutional_member'] ?? null),
                'type_owner' => nvl_int($r['type_owner'] ?? null),
                'bus_type_detail' => nvl_int($r['bus_type_detail'] ?? null),
                'truck_type_detail' => nvl_int($r['truck_type_detail'] ?? null),
                'other_type_detail' => nvl_int($r['other_type_detail'] ?? null),
                'no_passenger_vehicle' => nvl_int($r['no_passenger_vehicle'] ?? null),
                'no_member_travel' => nvl_int($r['no_member_travel'] ?? null),
                'no_freight_vehicle' => nvl_int($r['no_freight_vehicle'] ?? null),
                'quantity_good_transport' => nvl_int($r['quantity_good_transport'] ?? null),
                'member_themself' => nvl_int($r['member_themself'] ?? null),
                'is_user_transport_facility' => nvl_int($r['is_user_transport_facility'] ?? null),
            ]],

        // ── 102  Tribal / SC-ST ───────────────────────────────────────────────
        102 => ['ncd_cooperative_registrations_tribal',
            'Tribal-SC / ST Cooperative Details',
            fn($r) => [
                'type_society' => nvl_int($r['type_society'] ?? null),
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'state_district_federation' => nvl_int($r['state_district_federation'] ?? null),
                'society_provide_raw_material' => nvl_int($r['society_provide_raw_material'] ?? null),
                'facilities' => nvl($r['facilities'] ?? null),
            ]],

        // ── 7  UCB ────────────────────────────────────────────────────────────
        7 => ['ncd_cooperative_registrations_ucb',
            'UCB Details',
            fn($r) => [
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'ucb_branch' => nullNum($r['ucb_branch'] ?? null),
                'has_nafcub' => nvl_int($r['has_nafcub'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'annual_income' => nullNum($r['annual_income'] ?? null),
                'annual_ucb_expenditr' => nullNum($r['annual_ucb_expenditr'] ?? null),
                'asset_ucb' => nullNum($r['asset_ucb'] ?? null),
                'liability_ucb' => nullNum($r['liability_ucb'] ?? null),
                'total_deposit' => nullNum($r['total_deposit'] ?? null),
                'loan_outstanding' => nullNum($r['loan_outstanding'] ?? null),
                'is_gov_scheme_implemented' => nvl_int($r['is_gov_scheme_implemented'] ?? null),
                'is_computerized' => nvl_int($r['is_computerized'] ?? null),
                'no_computer_working' => nvl_int($r['no_computer_working'] ?? null),
                'have_ifsc' => nvl_int($r['have_ifsc'] ?? null),
                'have_corebanking' => nvl_int($r['have_corebanking'] ?? null),
                'have_doorstepservice' => nvl_int($r['have_doorstepservice'] ?? null),
                'is_aeps' => nvl_int($r['is_aeps'] ?? null),
                'offer_debitcard' => nvl_int($r['offer_debitcard'] ?? null),
                'have_internetbanking' => nvl_int($r['have_internetbanking'] ?? null),
                'offer_creditcard' => nvl_int($r['offer_creditcard'] ?? null),
                'cibil_membership' => nvl_int($r['cibil_membership'] ?? null),
                'conducting_gab' => nvl_int($r['conducting_gab'] ?? null),
                'cgtmsemli_member' => nvl_int($r['cgtmsemli_member'] ?? null),
                'is_saf_to_cust' => nvl_int($r['is_saf_to_cust'] ?? null),
                'networth' => nullNum($r['networth'] ?? null),
                'fswm_comp' => nvl_int($r['fswm_comp'] ?? null),
            ]],

        // ── 15  Women Co-op (WoCoop) ──────────────────────────────────────────
        15 => ['ncd_cooperative_registrations_wocoop',
            'Women Welfare Details',
            fn($r) => [
                'type_society' => nvl_int($r['type_society'] ?? null),
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'is_raw_material_taken' => nvl_int($r['is_raw_material_taken'] ?? null),
                'facilities' => nvl($r['facilities'] ?? null),
            ]],

        // ── 20  LAMP ─────────────────────────────────────────────────────
        20 => [
            'ncd_cooperative_registrations_lamp',
            'PACS Details',
            fn($r) => [
                'type_society' => nvl($r['type_society'] ?? null),

                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'has_land' => nvl_int($r['has_land'] ?? null),

                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),

                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),

                'individual_member' => nvl_int($r['individual_member'] ?? null),
                'institutional_member' => nvl_int($r['institutional_member'] ?? null),

                'minor_forest_produce' => nvl_int($r['minor_forest_produce'] ?? null),
                'mfp_collection_qty' => nullNum($r['mfp_collection_qty'] ?? null),

                'marketing' => nvl_int($r['marketing'] ?? null),
                'processing' => nvl_int($r['processing'] ?? null),

                'storage_facility' => nvl_int($r['storage_facility'] ?? null),
                'storage_capacity' => nullNum($r['storage_capacity'] ?? null),

                'transport_facility' => nvl_int($r['transport_facility'] ?? null),

                'financial_support' => nvl_int($r['financial_support'] ?? null),

                'other_facility' => nvl($r['other_facility'] ?? null),
            ]
        ],

        // ── 22  FSS ─────────────────────────────────────────────────────
        22 => [
            'ncd_cooperative_registrations_fss',
            'PACS Details',
            fn($r) => [
                'type_society' => nvl($r['type_society'] ?? null),

                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'has_land' => nvl_int($r['has_land'] ?? null),

                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),

                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),

                'individual_member' => nvl_int($r['individual_member'] ?? null),
                'institutional_member' => nvl_int($r['institutional_member'] ?? null),

                'fertilizer_distribution' => nvl_int($r['fertilizer_distribution'] ?? null),
                'fertilizer_qty' => nullNum($r['fertilizer_qty'] ?? null),

                'seed_distribution' => nvl_int($r['seed_distribution'] ?? null),
                'seed_qty' => nullNum($r['seed_qty'] ?? null),

                'pesticide_distribution' => nvl_int($r['pesticide_distribution'] ?? null),
                'pesticide_qty' => nullNum($r['pesticide_qty'] ?? null),

                'credit_facility' => nvl_int($r['credit_facility'] ?? null),
                'credit_amount' => nullNum($r['credit_amount'] ?? null),

                'storage_facility' => nvl_int($r['storage_facility'] ?? null),
                'storage_capacity' => nullNum($r['storage_capacity'] ?? null),

                'marketing' => nvl_int($r['marketing'] ?? null),

                'other_facility' => nvl($r['other_facility'] ?? null),
            ]
        ],

        // ── 117  Khadi & Gram Udyog ───────────────────────────────────────────
        117 => ['ncd_cooperative_registrations_khadi_gram',
            'Khadi Gram Cooperative Details',
            fn($r) => [
                'has_building' => nvl_int($r['has_building'] ?? null),
                'building_type' => nvl_int($r['building_type'] ?? null),
                'authorised_share' => nullNum($r['authorised_share'] ?? null),
                'paid_up_members' => nullNum($r['paid_up_members'] ?? null),
                'paid_up_government_bodies' => nullNum($r['paid_up_government_bodies'] ?? null),
                'paid_up_total' => nullNum($r['paid_up_total'] ?? null),
                'annual_turn_over' => nullNum($r['annual_turn_over'] ?? null),
                'individual_member' => nvl_int($r['individual_member'] ?? null),
                'institutional_member' => nvl_int($r['institutional_member'] ?? null),
                'power_loom_type' => nvl($r['power_loom_type'] ?? null),
                'hand_loom_type' => nvl($r['hand_loom_type'] ?? null),
                'hand_loom_other_type' => nvl($r['hand_loom_other_type'] ?? null),
                'no_of_loom' => nvl_int($r['no_of_loom'] ?? null),
                'raw_product_taken' => nvl_int($r['raw_product_taken'] ?? null),
                'raw_material_available' => nvl_int($r['raw_material_available'] ?? null),
                'waste_generate' => nvl_int($r['waste_generate'] ?? null),
                'waste_available' => nvl_int($r['waste_available'] ?? null),
                'operate_retail' => nvl_int($r['operate_retail'] ?? null),
                'no_of_retail' => nvl_int($r['no_of_retail'] ?? null),
                'product_sale_out' => nvl_int($r['product_sale_out'] ?? null),
                'operated_member_themself' => nvl_int($r['operated_member_themself'] ?? null),
                'is_user_work_divide' => nvl_int($r['is_user_work_divide'] ?? null),
                'type_of_activities_khadi_gram' => nvl($r['type_of_activities_khadi_gram'] ?? null),
                'do_you_want_to_enter_type_products_produced' => nvl_int($r['do_you_want_to_enter_type_products_produced'] ?? null),
                'fswm_comp' => nvl_int($r['fswm_comp'] ?? null),
            ]],

    ];
}

function api_post(string $path, array $extra = []): array
{
    // Always merge API key first, then caller params
    $payload = array_merge(['key' => API_KEY], $extra);
    $url = API_BASE . $path;
    $body = json_encode($payload);

    $attempt = 0;
    while ($attempt < CURL_MAX_RETRIES) {
        $attempt++;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,

            CURLOPT_CONNECTTIMEOUT => CURL_CONNECT_TIMEOUT,
            CURLOPT_TIMEOUT => CURL_EXEC_TIMEOUT,

            CURLOPT_LOW_SPEED_LIMIT => 100,
            CURLOPT_LOW_SPEED_TIME => 30,

            // ✅ POST with JSON body
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,

            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',   // ✅ JSON, not form-encoded
                'Accept: application/json',
                'Content-Length: ' . strlen($body),
            ],

            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,

            CURLOPT_ENCODING => '',
            CURLOPT_USERAGENT => 'NCD-Sync/2.0 PHP/' . PHP_VERSION,
        ]);

        $raw = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        $curlErrNo = curl_errno($ch);
        curl_close($ch);

        if ($raw === false) {
            log_msg("  [WARN] attempt $attempt – cURL error: $curlErr ($curlErrNo) → $url");
            if ($attempt < CURL_MAX_RETRIES) sleep(CURL_RETRY_DELAY);
            continue;
        }

        if ($httpCode !== 200) {
            log_msg("  [WARN] attempt $attempt – HTTP $httpCode → $url");
            if ($attempt < CURL_MAX_RETRIES) sleep(CURL_RETRY_DELAY);
            continue;
        }

        $decoded = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            log_msg("  [WARN] attempt $attempt – JSON decode error: " . json_last_error_msg() . " → $url");
            if ($attempt < CURL_MAX_RETRIES) sleep(CURL_RETRY_DELAY);
            continue;
        }

        if (($decoded['status'] ?? '') !== 'Success') {
            log_msg("  [WARN] API non-success: " . json_encode($decoded) . " → $url");
            return [];
        }

        return $decoded;
    }

    log_msg("  [ERROR] All $attempt attempts failed → $url");
    return [];
}

function upsert(mysqli $db, string $table, array $uniqueRow, array $dataRow): void
{
    $allData = array_merge($uniqueRow, $dataRow);
    $cols = array_keys($allData);
    $values = array_values($allData);
    $placeholders = implode(',', array_fill(0, count($cols), '?'));
    $colList = implode(',', $cols);
    $updateList = implode(',', array_map(fn($c) => "$c=VALUES($c)", array_keys($dataRow)));

    $sql = "INSERT INTO $table ($colList) VALUES ($placeholders) ON DUPLICATE KEY UPDATE $updateList";
    $stmt = mysqli_prepare($db, $sql);
    if (!$stmt) {
        log_msg("  [PREPARE ERROR] $table : " . mysqli_error($db));
        return;
    }
    $types = str_repeat('s', count($values));
    mysqli_stmt_bind_param($stmt, $types, ...$values);
    if (!mysqli_stmt_execute($stmt)) {
        log_msg("  [EXEC ERROR] $table : " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

/** Pull a misc-level endpoint and upsert / insert into table */
function sync_misc_level(
    mysqli $db, int $level, string $table,
    string $resultKey, callable $mapper, ?string $uniqueField
): void
{
    $data = api_post('/en/Api/apimiscellanousdata', ['state_code' => STATE_CODE, 'level_code' => $level]);
    if (empty($data['result'])) {
        log_msg("  Level $level ($table): no data.");
        return;
    }

    $n = 0;
    foreach ($data['result'] as $item) {
        $r = $item[$resultKey] ?? reset($item);
        $row = $mapper($r);
        if ($uniqueField && isset($row[$uniqueField])) {
            upsert($db, $table, [$uniqueField => $row[$uniqueField]], $row);
        } else {
            $cols = array_keys($row);
            $values = array_values($row);
            $placeholders = implode(',', array_fill(0, count($cols), '?'));
            $colList = implode(',', $cols);
            $stmt = mysqli_prepare($db, "INSERT IGNORE INTO $table ($colList) VALUES ($placeholders)");
            if ($stmt) {
                $types = str_repeat('s', count($values));
                mysqli_stmt_bind_param($stmt, $types, ...$values);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        $n++;
    }
    log_saved($db, "$table (level $level)", $n);
}

/** Shared mapper for PACS / FSS / LAMPS */
function pacsMapper(array $r): array
{
    return [
        'has_building' => $r['has_building'] ?? null,
        'building_type' => $r['building_type'] ?? null,
        'fertilizer_distribution' => $r['fertilizer_distribution'] ?? null,
        'fertilizer_distribution_qty' => $r['fertilizer_distribution_qty'] ?? null,
        'fertilizer_distribution_details' => $r['fertilizer_distribution_details'] ?? null,
        'pesticide_distribution' => $r['pesticide_distribution'] ?? null,
        'pesticide_distribution_qty' => $r['pesticide_distribution_qty'] ?? null,
        'seed_distribution' => $r['seed_distribution'] ?? null,
        'seed_distribution_qty' => $r['seed_distribution_qty'] ?? null,
        'fair_price' => $r['fair_price'] ?? null,
        'fair_price_qty' => $r['fair_price_qty'] ?? null,
        'fair_price_details' => $r['fair_price_details'] ?? null,
        'is_foodgrains' => $r['is_foodgrains'] ?? null,
        'foodgrains_qty' => $r['foodgrains_qty'] ?? null,
        'agricultural_implements' => $r['agricultural_implements'] ?? null,
        'agricultural_implements_text' => $r['agricultural_implements_text'] ?? null,
        'dry_storage' => $r['dry_storage'] ?? null,
        'dry_storage_capicity' => nullNum($r['dry_storage_capicity'] ?? null),
        'cold_storage' => $r['cold_storage'] ?? null,
        'cold_storage_capicity' => nullNum($r['cold_storage_capicity'] ?? null),
        'milk_unit' => $r['milk_unit'] ?? null,
        'milk_capicity_unit' => $r['milk_capicity_unit'] ?? null,
        'food_processing' => $r['food_processing'] ?? null,
        'food_processing_type' => $r['food_processing_type'] ?? null,
        'other_facility' => $r['other_facility'] ?? null,
        'is_socitey_has_land' => $r['is_socitey_has_land'] ?? null,
        'pack_involved_fish_catch' => $r['pack_involved_fish_catch'] ?? null,
        'pack_annual_fish_catch' => nullNum($r['pack_annual_fish_catch'] ?? null),
        'pack_total_outstanding_loan' => nullNum($r['pack_total_outstanding_loan'] ?? null),
        'pack_revenue_non_credit' => nullNum($r['pack_revenue_non_credit'] ?? null),
        'is_lgs_program' => $r['is_lgs_program'] ?? null,
        'lgs_capacity' => nullNum($r['lgs_capacity'] ?? null),
        'is_csc' => $r['is_csc'] ?? null,
        'csc_revenue' => $r['csc_revenue'] ?? null,
        'csc_details' => $r['csc_details'] ?? null,
        'is_fpo' => $r['is_fpo'] ?? null,
        'fpo_details' => $r['fpo_details'] ?? null,
        'is_lpg_distributership' => $r['is_lpg_distributership'] ?? null,
        'lpg_distributership_details' => $r['lpg_distributership_details'] ?? null,
        'is_bcp_pump' => $r['is_bcp_pump'] ?? null,
        'bcp_pump_details' => $r['bcp_pump_details'] ?? null,
        'is_dpp_diesel' => $r['is_dpp_diesel'] ?? null,
        'dpp_diesel_details' => $r['dpp_diesel_details'] ?? null,
        'is_jak' => $r['is_jak'] ?? null,
        'jak_qty' => $r['jak_qty'] ?? null,
        'is_pmksk' => $r['is_pmksk'] ?? null,
        'pmksk_details' => $r['pmksk_details'] ?? null,
        'is_paani_samity' => $r['is_paani_samity'] ?? null,
        'paani_samity_details' => $r['paani_samity_details'] ?? null,
        'is_pm_kusum_scheme' => $r['is_pm_kusum_scheme'] ?? null,
        'pm_kusum_scheme_details' => $r['pm_kusum_scheme_details'] ?? null,
    ];
}

/** Parse a date string that may come in various formats; return MySQL date or null */
function dateOrNull(?string $v): ?string
{
    if (empty($v) || $v === 'null') return null;
    if (str_contains($v, 'T')) {
        $ts = strtotime($v);
        return $ts ? date('Y-m-d', $ts) : null;
    }
    if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $v, $m)) {
        return "{$m[3]}-{$m[2]}-{$m[1]}";
    }
    $ts = strtotime($v);
    return $ts ? date('Y-m-d', $ts) : null;
}

function nvl($v): ?string
{
    if ($v === null) return null;

    $v = trim((string)$v);
    return ($v === '' || strtolower($v) === 'null') ? null : $v;
}

function nvl_int($v): ?int
{
    if ($v === null) return null;

    if (is_string($v)) {
        $v = trim($v);
        if ($v === '' || strtolower($v) === 'null') return null;
    }

    return is_numeric($v) ? (int)$v : null;
}

function nullNum($v): ?float
{
    if ($v === null) return null;

    if (is_string($v)) {
        $v = trim($v);
        if ($v === '' || strtolower($v) === 'null') return null;
    }

    return is_numeric($v) ? (float)$v : null;
}

function log_msg(string $msg): void
{
    $ts = date('[Y-m-d H:i:s]');
    echo "$ts $msg\n";
    flush();
}

function log_saved(mysqli $db, string $apiName, int $n): void
{
    log_msg("  Saved $n rows → $apiName");
    $stmt = mysqli_prepare($db, "INSERT INTO ncd_sync_log (api_name, records_saved) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, 'si', $apiName, $n);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function nvl_bool($v): ?int
{
    if ($v === null) return null;

    if (is_string($v)) {
        $v = strtolower(trim($v));

        if ($v === '' || $v === 'null') return null;
        if ($v === 'yes' || $v === '1') return 1;
        if ($v === 'no' || $v === '0') return 0;
    }

    if (is_numeric($v)) {
        return (int)$v;
    }

    return null;
}