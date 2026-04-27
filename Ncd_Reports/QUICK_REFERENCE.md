# 🚀 Quick Reference Guide

## File Locations
```
Project Root/
├── ncd_data_reports/
│   ├── dashboard_cooperatives.php
│   ├── ncd_cooperatives_info.php
│   ├── fetch_cooperatives.php
│   ├── export_excel.php
│   ├── helpers/
│   │   ├── filter_builder.php          ← NEW FILE
│   │   └── value_mapper.php
│   └── css/
│       └── style.css
├── scripts/
│   └── settings.php                    (Database connection)
└── ...
```

---

## 🔌 API Reference

### buildCooperativeFilters($request)
**File:** `helpers/filter_builder.php`

**Purpose:** Build SQL WHERE clause from filter parameters

**Parameters:**
- `$request` - Array of filter values (from $_GET, $_POST, or $_REQUEST)

**Returns:** String - SQL WHERE clause

**Example:**
```php
include("helpers/filter_builder.php");

$where = buildCooperativeFilters($_REQUEST);
// Returns: " WHERE 1=1 AND c.is_approved = 1 AND c.state_code = 'UP'"

// Use in query
$query = "SELECT * FROM cooperatives c $where";
```

**Supported Parameters:**
```
authority_id              (int)
reference_year            (int)
area_of_operation_id      (int)
water_body_type_id        (int)
is_approved               (0/1)
sector_of_operation       (int)
functional_status         (0/1)
full_time_secretary       (0/1)
location_of_head_quarter  (1=Urban, 2=Rural)
operation_area_location   (int)
is_coastal                (0/1)
is_affiliated_union_federation (0/1)
financial_audit           (0/1)
is_profit_making          (0/1)
is_dividend_paid          (0/1)
state_code                (string)
```

---

### getFilterOptions($column)
**File:** `helpers/filter_builder.php`

**Purpose:** Get dropdown options for a specific filter

**Parameters:**
- `$column` - Column name (see list above)

**Returns:** Array - [id => name] pairs

**Example:**
```php
$options = getFilterOptions('area_of_operation_id');
// Returns: [1 => 'Area 1', 2 => 'Area 2', ...]
```

---

### getYearOptions()
**File:** `helpers/filter_builder.php`

**Purpose:** Get available years from cooperatives data

**Returns:** Array - [year => year] pairs

**Example:**
```php
$years = getYearOptions();
// Returns: [2023 => '2023', 2022 => '2022', ...]
```

---

## 📡 AJAX Endpoints

### fetch_cooperatives.php
**Method:** POST

**Expected Parameters:**
```javascript
{
    draw: 1,                              // DataTable sequence number
    start: 0,                             // Pagination offset
    length: 25,                           // Rows per page
    search: {value: ""},                  // Search text
    order: [{column: 0, dir: "desc"}],   // Sort settings
    
    // Filter values
    authority_id: 1,
    reference_year: 2023,
    area_of_operation_id: 5,
    is_approved: 1,
    state_code: "UP",
    // ... all 15 filters
}
```

**Returns:**
```json
{
    "draw": 1,
    "recordsTotal": 500,
    "recordsFiltered": 45,
    "data": [
        {
            "id": 1,
            "cooperative_name": "ABC Cooperative",
            "is_approved": "Yes",
            ...
        }
    ]
}
```

---

### export_excel.php
**Method:** GET/POST

**Expected Parameters:**
```
authority_id=1
reference_year=2023
area_of_operation_id=5
is_approved=1
state_code=UP
... (all filter parameters)
```

**Returns:** Binary Excel file (.xls)

**Example:**
```javascript
// From JavaScript
window.location.href = 'export_excel.php?' + new URLSearchParams({
    authority_id: 1,
    reference_year: 2023,
    area_of_operation_id: 5,
    ...
});
```

---

## 🎯 JavaScript Functions

### applyFilters()
Reloads DataTable with current filter values
```javascript
applyFilters();  // Re-fetch and update table
```

### resetFilters()
Clears all filter inputs and reloads table
```javascript
resetFilters();  // Clear filters and reload
```

### toggleFilters()
Show/hide filter panel
```javascript
toggleFilters();  // Toggle filter visibility
```

### exportData()
Download Excel with current filters
```javascript
exportData();  // Generate and download Excel
```

---

## 💾 Database Table Structure

### cooperatives table (required columns)
```sql
ALTER TABLE cooperatives ADD COLUMN IF NOT EXISTS full_time_secretary TINYINT DEFAULT 0;
ALTER TABLE cooperatives ADD COLUMN IF NOT EXISTS operation_area_location INT;
ALTER TABLE cooperatives ADD COLUMN IF NOT EXISTS sector_of_operation INT;
```

### Reference tables (required)
```
area_of_operations_master
├── id (INT PRIMARY KEY)
└── name (VARCHAR)

water_body_types_master
├── id (INT PRIMARY KEY)
└── name (VARCHAR)

sector_master
├── id (INT PRIMARY KEY)
└── name (VARCHAR)

states_master
├── state_code (VARCHAR PRIMARY KEY)
└── name (VARCHAR)
```

---

## 🎨 CSS Classes Reference

### Filter Panel Classes
```css
.filters-panel              /* Main filter container */
.filter-toggle              /* Toggle button */
.filters-container          /* Collapsible container */
.filters-grid               /* Grid layout for filters */
.filter-group               /* Individual filter wrapper */
.filter-input               /* Select/input elements */
.filter-actions             /* Action buttons area */
```

### Button Classes
```css
.btn                        /* Base button style */
.btn-primary                /* Primary (blue) button */
.btn-secondary              /* Secondary (gray) button */
```

### Table Classes (DataTable)
```css
.table-wrapper              /* Table container */
table.dataTable             /* Main table */
.dataTables_processing      /* Loading indicator */
.dataTables_paginate        /* Pagination controls */
.dataTables_info            /* Info text */
.dataTables_filter          /* Search box */
.dataTables_length          /* Rows-per-page select */
```

---

## 🔄 Data Flow Diagram

```
User Actions
    ↓
[ncd_cooperatives_info.php] (HTML/JS)
    ├─ Displays filters
    ├─ Handles user interactions
    └─ Makes AJAX calls
    ↓
[JavaScript Functions]
    ├─ applyFilters()
    ├─ resetFilters()
    ├─ exportData()
    └─ toggleFilters()
    ↓
AJAX Request
    ↓
[fetch_cooperatives.php]
    ├─ Receives filter data
    ├─ Includes filter_builder.php
    ├─ Calls buildCooperativeFilters()
    ├─ Builds WHERE clause
    ├─ Executes SQL query
    └─ Returns JSON
    ↓
Browser receives JSON
    ↓
DataTable updates (no reload)
    ↓
User sees filtered results

OR (for export)

[exportData() function]
    ↓
[export_excel.php]
    ├─ Receives filter data
    ├─ Includes filter_builder.php
    ├─ Calls buildCooperativeFilters()
    ├─ Builds WHERE clause
    ├─ Executes SQL query
    ├─ Formats data
    └─ Outputs as .xls file
    ↓
Browser downloads file
    ↓
User opens in Excel
```

---

## 🐛 Common Issues & Solutions

| Issue | Cause | Solution |
|-------|-------|----------|
| Filters don't populate | Reference tables empty | Add data to reference tables |
| AJAX returns error | fetch_cooperatives.php not found | Check file path and permissions |
| Export doesn't apply filters | Filter values not passed | Check URLSearchParams in exportData() |
| Page reloads when applying filters | JavaScript error | Check browser console for errors |
| Special characters corrupted in Excel | Encoding issue | Verify BOM is output in export_excel.php |

---

## 📊 SQL Debugging

### Check available filters
```sql
SHOW COLUMNS FROM cooperatives;
```

### Check reference data
```sql
SELECT COUNT(*) FROM area_of_operations_master;
SELECT COUNT(*) FROM water_body_types_master;
SELECT COUNT(*) FROM sector_master;
SELECT COUNT(*) FROM states_master;
```

### Test WHERE clause manually
```sql
SELECT COUNT(*) FROM cooperatives c
WHERE 1=1
AND c.is_approved = 1
AND c.state_code = 'UP'
AND c.area_of_operation_id = 5;
```

---

## 🔐 Security Checklist

- [ ] All integer inputs use `intval()`
- [ ] All string inputs use `addslashes()`
- [ ] SQL queries use parameterized inputs
- [ ] XSS prevention with `htmlspecialchars()`
- [ ] Session validation in place
- [ ] File uploads restricted
- [ ] Error messages don't expose DB structure

---

## 📈 Performance Tips

1. **Use indexes** on filtered columns:
```sql
CREATE INDEX idx_is_approved ON cooperatives(is_approved);
CREATE INDEX idx_state_code ON cooperatives(state_code);
CREATE INDEX idx_area_operation ON cooperatives(area_of_operation_id);
```

2. **Paginate results** - DataTable default is 25 rows per page

3. **Cache dropdown options** - Generated from database

4. **Use EXPLAIN** to analyze slow queries:
```sql
EXPLAIN SELECT * FROM cooperatives WHERE is_approved = 1;
```

---

## 📚 File Dependencies

```
ncd_cooperatives_info.php
├─ settings.php (database)
├─ filter_builder.php
├─ jQuery library
└─ DataTables library

fetch_cooperatives.php
├─ settings.php
├─ filter_builder.php
├─ value_mapper.php
└─ (AJAX endpoint)

export_excel.php
├─ settings.php
├─ filter_builder.php
└─ (No external libraries)

filter_builder.php
├─ settings.php (for execute_query)
└─ (Standalone helper)
```

---

## ✨ Features Summary

| Feature | Status | File |
|---------|--------|------|
| Authority-wise grouping | ✅ | dashboard_cooperatives.php |
| Real-time AJAX filtering | ✅ | fetch_cooperatives.php |
| 15 advanced filters | ✅ | filter_builder.php |
| Filtered Excel export | ✅ | export_excel.php |
| Collapsible filter panel | ✅ | ncd_cooperatives_info.php |
| Server-side DataTable | ✅ | fetch_cooperatives.php |
| Responsive design | ✅ | style.css |
| Value mapping (ID→Name) | ✅ | value_mapper.php |
| Boolean formatting | ✅ | value_mapper.php |
| Reference data joins | ✅ | export_excel.php |

---

**Ready to use! Questions? Check INSTALLATION_GUIDE.md**
