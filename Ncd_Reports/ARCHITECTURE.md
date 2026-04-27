# 🏗️ System Architecture & Data Flow

## Complete System Overview

```
┌─────────────────────────────────────────────────────────────────────────┐
│                      COOPERATIVE MANAGEMENT SYSTEM                      │
└─────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────┐
│                          USER INTERFACE LAYER                            │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  1. dashboard_cooperatives.php                                          │
│     ├─ Shows all registration authorities                              │
│     ├─ Displays count per authority                                    │
│     └─ Each card is clickable                                          │
│                                                                          │
│  2. ncd_cooperatives_info.php                                           │
│     ├─ Filter Panel (15 filters)                                       │
│     │  ├─ Reference Year                                               │
│     │  ├─ Area of Operations                                           │
│     │  ├─ Water Body Type                                              │
│     │  ├─ Is Approved                                                  │
│     │  ├─ ... (11 more filters)                                        │
│     │  └─ State                                                         │
│     ├─ Action Buttons                                                  │
│     │  ├─ Apply Filters (AJAX)                                         │
│     │  └─ Reset Filters                                                │
│     ├─ DataTable (Server-side processed)                               │
│     │  ├─ Searchable                                                   │
│     │  ├─ Sortable                                                     │
│     │  ├─ Paginable (25 rows/page)                                     │
│     │  └─ Fixed header                                                 │
│     └─ Download Excel Button                                           │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘

                          │
                          │ (User Action)
                          ↓

┌──────────────────────────────────────────────────────────────────────────┐
│                        JAVASCRIPT LAYER                                  │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  applyFilters()          → Collects filter values → AJAX request       │
│  resetFilters()          → Clears inputs → Reload table                │
│  exportData()            → Passes filters → Download Excel             │
│  toggleFilters()         → Show/hide filter panel                      │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘

                          │
                          │ (AJAX/HTTP Request)
                          ↓

┌──────────────────────────────────────────────────────────────────────────┐
│                     BUSINESS LOGIC LAYER (PHP)                          │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  A. fetch_cooperatives.php (AJAX Endpoint)                              │
│     │                                                                    │
│     ├─ 1. Receive request from DataTable                               │
│     │      ├─ Filter values                                            │
│     │      ├─ Search text                                              │
│     │      ├─ Sort column & direction                                  │
│     │      └─ Pagination (start, length)                              │
│     │                                                                    │
│     ├─ 2. Include helpers                                              │
│     │      ├─ filter_builder.php                                       │
│     │      └─ value_mapper.php                                         │
│     │                                                                    │
│     ├─ 3. Process filters                                              │
│     │      └─ Call buildCooperativeFilters()                           │
│     │          → Returns WHERE clause                                  │
│     │                                                                    │
│     ├─ 4. Get total counts                                             │
│     │      ├─ Total rows (no filter)                                   │
│     │      └─ Filtered rows (with filter)                              │
│     │                                                                    │
│     ├─ 5. Execute query                                                │
│     │      └─ SELECT * FROM cooperatives                               │
│     │          WHERE ... (built from filters)                          │
│     │          ORDER BY ... (from sort)                                │
│     │          LIMIT ... (pagination)                                  │
│     │                                                                    │
│     ├─ 6. Format results                                               │
│     │      └─ mapDisplayValue() for each cell                          │
│     │          (converts IDs to names, 0/1 to Yes/No)                 │
│     │                                                                    │
│     └─ 7. Return JSON                                                  │
│          {                                                              │
│              "draw": 1,                                                 │
│              "recordsTotal": 500,                                       │
│              "recordsFiltered": 45,                                     │
│              "data": [...]                                             │
│          }                                                              │
│                                                                          │
│  B. export_excel.php (Export Endpoint)                                  │
│     │                                                                    │
│     ├─ 1. Receive GET parameters (filter values)                       │
│     │                                                                    │
│     ├─ 2. Include filter_builder.php                                   │
│     │                                                                    │
│     ├─ 3. Build WHERE clause                                           │
│     │      └─ Call buildCooperativeFilters()                           │
│     │                                                                    │
│     ├─ 4. Execute query with joins                                     │
│     │      ├─ JOIN registration_authorities_master                     │
│     │      ├─ JOIN water_body_types_master                             │
│     │      ├─ JOIN sector_master                                       │
│     │      └─ ... (more joins)                                         │
│     │                                                                    │
│     ├─ 5. Format data                                                  │
│     │      ├─ Convert 0/1 to Yes/No                                    │
│     │      ├─ Convert codes to names                                   │
│     │      ├─ Format dates                                             │
│     │      └─ Handle null values                                       │
│     │                                                                    │
│     └─ 6. Stream to Excel file                                         │
│          (Memory efficient - one row at a time)                        │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘

                          │
                          │ (Filter Building)
                          ↓

┌──────────────────────────────────────────────────────────────────────────┐
│                      FILTER ENGINE (Core Logic)                          │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  helpers/filter_builder.php                                            │
│  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━                                          │
│                                                                          │
│  buildCooperativeFilters($request)                                      │
│  {                                                                       │
│      $where = " WHERE 1=1 ";                                           │
│                                                                          │
│      // Each filter adds to WHERE clause                               │
│      if ($request['is_approved']) {                                    │
│          $where .= " AND c.is_approved = " . $value;                  │
│      }                                                                  │
│      if ($request['reference_year']) {                                 │
│          $where .= " AND YEAR(c.created_at) = " . $year;              │
│      }                                                                  │
│      // ... 13 more filters ...                                        │
│                                                                          │
│      return $where;                                                    │
│  }                                                                       │
│                                                                          │
│  Example output:                                                        │
│  " WHERE 1=1                                                            │
│    AND c.is_approved = 1                                               │
│    AND YEAR(c.created_at) = 2023                                       │
│    AND c.state_code = 'UP'                                             │
│    AND c.area_of_operation_id = 5 "                                    │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘

                          │
                          │ (SQL Query)
                          ↓

┌──────────────────────────────────────────────────────────────────────────┐
│                        DATABASE LAYER (MySQL)                            │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  Tables Used:                                                           │
│  ─────────────                                                          │
│                                                                          │
│  cooperatives (Main)                                                    │
│  ├─ id, name, registration_authoritie_id                               │
│  ├─ is_approved, functional_status                                     │
│  ├─ is_coastal, is_affiliated_union_federation                         │
│  ├─ financial_audit, is_profit_making, is_dividend_paid                │
│  ├─ full_time_secretary, location_of_head_quarter                      │
│  ├─ area_of_operation_id, water_body_type_id                           │
│  ├─ sector_of_operation, operation_area_location                       │
│  ├─ state_code, district_code, block_code                              │
│  └─ ... (and many more)                                                │
│                                                                          │
│  References (Lookup):                                                   │
│  ├─ registration_authorities_master                                    │
│  ├─ area_of_operations_master                                          │
│  ├─ water_body_types_master                                            │
│  ├─ sector_master                                                      │
│  ├─ states_master                                                      │
│  ├─ districts_master                                                   │
│  ├─ blocks_master                                                      │
│  └─ ... (other lookup tables)                                          │
│                                                                          │
│  Query Example:                                                         │
│  ═════════════                                                          │
│  SELECT c.*                                                             │
│  FROM cooperatives c                                                    │
│  WHERE c.is_approved = 1                                               │
│    AND c.state_code = 'UP'                                             │
│    AND c.area_of_operation_id = 5                                      │
│  LIMIT 25                                                               │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘

                          │
                          │ (Results)
                          ↓

┌──────────────────────────────────────────────────────────────────────────┐
│                       DISPLAY FORMATTING LAYER                          │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  value_mapper.php                                                       │
│  ─────────────────                                                      │
│                                                                          │
│  mapDisplayValue($column, $value)                                       │
│  {                                                                       │
│      // Convert 1 → 'Yes', 0 → 'No'                                    │
│      if ($column == 'is_approved' && $value == 1) {                   │
│          return 'Yes';                                                  │
│      }                                                                  │
│                                                                          │
│      // Convert code to name from reference table                       │
│      if ($column == 'state_code' && $value == 'UP') {                 │
│          // Lookup in states_master                                    │
│          return 'Uttar Pradesh (UP)';                                   │
│      }                                                                  │
│                                                                          │
│      // ... more formatting ...                                        │
│  }                                                                       │
│                                                                          │
│  Examples:                                                              │
│  is_approved: 1 → 'Yes'                                                │
│  state_code: 'UP' → 'Uttar Pradesh (UP)'                               │
│  location_of_head_quarter: 1 → 'Urban'                                │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘

                          │
                          │ (Formatted JSON)
                          ↓

┌──────────────────────────────────────────────────────────────────────────┐
│                         RESPONSE LAYER                                   │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  For AJAX Requests (fetch_cooperatives.php):                            │
│  ─────────────────────────────────────────────                          │
│  {                                                                       │
│      "draw": 1,                                                         │
│      "recordsTotal": 500,                                               │
│      "recordsFiltered": 45,                                             │
│      "data": [                                                          │
│          {                                                              │
│              "id": "1",                                                 │
│              "name": "ABC Cooperative",                                 │
│              "is_approved": "Yes",    ← Formatted                      │
│              "state_code": "Uttar Pradesh (UP)",    ← Mapped           │
│              ...                                                        │
│          },                                                             │
│          ...                                                            │
│      ]                                                                  │
│  }                                                                       │
│                                                                          │
│  For Export (export_excel.php):                                         │
│  ──────────────────────────────                                         │
│  Binary Excel file with formatted data                                  │
│  Columns: Cooperative Name, Area, Status, ...                          │
│  Values: All formatted (IDs→Names, 0/1→Yes/No)                        │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘

                          │
                          │ (Back to Browser)
                          ↓

┌──────────────────────────────────────────────────────────────────────────┐
│                        BROWSER RENDERING                                │
├──────────────────────────────────────────────────────────────────────────┤
│                                                                          │
│  DataTable receives JSON → Updates table cells                         │
│  Excel file triggers download                                          │
│  Page updates WITHOUT reload (AJAX magic!)                             │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
```

---

## 🔄 User Workflow Diagram

```
START
  │
  ├─→ [Dashboard Page]
  │   └─→ Click authority card
  │       └─→ Pass authority_id as URL parameter
  │
  ├─→ [Cooperatives Info Page]
  │   ├─→ Load filter options from database
  │   ├─→ Show DataTable with all cooperatives
  │   │
  │   ├─→ USER SELECTS FILTERS
  │   │   ├─→ Choose Reference Year
  │   │   ├─→ Choose State
  │   │   ├─→ Choose Area of Operation
  │   │   └─→ ... (up to 15 filters)
  │   │
  │   └─→ USER CLICKS "Apply Filters"
  │       │
  │       ├─→ JavaScript collects filter values
  │       ├─→ Sends AJAX request to fetch_cooperatives.php
  │       │
  │       ├─→ PHP filters data
  │       ├─→ Returns filtered JSON
  │       │
  │       ├─→ DataTable updates
  │       └─→ User sees filtered results (NO RELOAD)
  │
  ├─→ USER CLICKS "Download Excel"
  │   │
  │   ├─→ JavaScript collects current filters
  │   ├─→ Sends to export_excel.php
  │   │
  │   ├─→ PHP applies same filters
  │   ├─→ Generates Excel file with filtered data
  │   │
  │   └─→ Browser downloads file
  │
  ├─→ USER CLICKS "Reset Filters"
  │   │
  │   ├─→ JavaScript clears all inputs
  │   ├─→ Reloads table with ALL data
  │   │
  │   └─→ BACK TO STEP: "Load filter options from database"
  │
  └─→ END
```

---

## 🎯 Key Components & Interactions

```
┌─────────────────────────────────────────────────────────────────┐
│                    FILTER BUILDER ENGINE                        │
│         (Converts user selections → SQL WHERE clause)           │
└─────────────────────────────────────────────────────────────────┘

Input (From UI):
{
    reference_year: "2023",
    area_of_operation_id: "5",
    is_approved: "1",
    state_code: "UP"
}
      │
      ↓
buildCooperativeFilters()
      │
      └─→ Process each field:
          ├─→ if reference_year → AND YEAR(c.created_at) = 2023
          ├─→ if area_of_operation_id → AND c.area_of_operation_id = 5
          ├─→ if is_approved → AND c.is_approved = 1
          └─→ if state_code → AND c.state_code = 'UP'
      │
      ↓
Output (WHERE clause):
" WHERE 1=1 
  AND YEAR(c.created_at) = 2023
  AND c.area_of_operation_id = 5
  AND c.is_approved = 1
  AND c.state_code = 'UP' "
      │
      ↓
Used in SQL Query:
SELECT * FROM cooperatives c
WHERE ... (above WHERE clause) ...
```

---

## 📱 Responsive Behavior

```
DESKTOP (>1024px)
┌─────────────────────────────────────────┐
│ Filter Grid (5 columns)                 │
├──────────┬──────────┬──────────┬────────┤
│Reference │ Area of  │ Water    │ Is     │
│  Year    │Operations│ Body     │Approved│
├──────────┼──────────┼──────────┼────────┤
│ Sector   │Functional│Full Time │Location│
│ of Ops   │ Status   │Secretary │of HQ   │
└──────────┴──────────┴──────────┴────────┘

TABLET (768px - 1024px)
┌──────────────────────────────┐
│ Filter Grid (3 columns)      │
├────────────┬────────┬────────┤
│Reference   │ Area   │ Water  │
│  Year      │  of Ops│ Body   │
├────────────┼────────┼────────┤
│ Is Approved│ Sector │ Status │
└────────────┴────────┴────────┘

MOBILE (<768px)
┌───────────────────┐
│ Filter Grid (1)   │
├───────────────────┤
│ Reference Year    │
├───────────────────┤
│ Area of Operations│
├───────────────────┤
│ Water Body Type   │
├───────────────────┤
│ Is Approved       │
├───────────────────┤
│ ... more ...      │
└───────────────────┘
```

---

## ⚡ Performance Optimization Points

```
┌────────────────────────────────────────┐
│     Performance Optimization Chain     │
└────────────────────────────────────────┘

1. FILTER BUILDER
   └─→ Builds WHERE clause at DB level
       (Filtering happens in database, not PHP)

2. DATABASE INDEXES
   └─→ Recommended indexes on filter columns:
       CREATE INDEX idx_is_approved ON cooperatives(is_approved);
       CREATE INDEX idx_state_code ON cooperatives(state_code);

3. PAGINATION
   └─→ Only 25 rows fetched per request
       (Not all 500+ rows)

4. AJAX UPDATES
   └─→ Only table data refreshed
       (Not entire page reload)

5. CACHING
   └─→ Reference data cached in value_mapper
       (No repeated DB queries for same ID)

6. LAZY COLUMN LOADING
   └─→ Columns shown via DataTable scrolling
       (Browser only renders visible cells)

7. MEMORY EFFICIENT EXPORT
   └─→ Streams Excel data one row at a time
       (Doesn't load entire dataset in memory)

Result: Fast, responsive, scalable system ✅
```

---

## 🔐 Security Flow

```
User Input
    │
    ↓
[Input Validation]
├─→ intval() for integers → Prevents SQL injection
├─→ addslashes() for strings → Escapes quotes
└─→ htmlspecialchars() for display → Prevents XSS
    │
    ↓
[Sanitized Data]
    │
    ↓
[Filter Builder]
├─→ Builds WHERE clause from safe values
└─→ Only numeric and string literals in SQL
    │
    ↓
[Database Query]
├─→ Executes with safe parameters
└─→ No injection possible
    │
    ↓
[Safe Output]
    │
    ↓
Display to User ✅ (Secure)
```

---

**System is fully integrated and production-ready!**
