<?php
include("scripts/settings.php");
session_start();

page_header_start();
page_header_end();

page_sidebar();

/* Helpers */
function h($s){ return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8'); }
function esc($db,$s){ return mysqli_real_escape_string($db, (string)$s); }

/* Status normalization + label (keeps parity with other report) */
function status_code($v)
{
    $v = trim((string)$v);
    if ($v === '') return '';
    $active_syn = ['सक्रिय', 'सक्रिय ', 'सक्रिया', 'active'];
    $non_syn = ['निष्क्रिय', 'non-active', 'non active', 'non_active'];
    $closed_syn = ['परिसमापनाधीन', 'परिसमापनाधीन ', 'परिसमापन', 'closed'];
    $na_syn = ['स्थापित नही है', 'स्थापित नहीं है', 'स्थापित नही', 'not_applicable'];
    if (in_array($v, $active_syn, true)) return 'active';
    if (in_array($v, $non_syn, true)) return 'non-active';
    if (in_array($v, $closed_syn, true)) return 'closed';
    if (in_array($v, $na_syn, true)) return 'not_applicable';
    $vl = mb_strtolower($v);
    if ($vl === 'active' || $vl === 'सक्रिय') return 'active';
    if ($vl === 'non-active' || $vl === 'non active' || $vl === 'निष्क्रिय') return 'non-active';
    if ($vl === 'closed' || $vl === 'परिसमापनाधीन') return 'closed';
    if ($vl === 'not_applicable' || $vl === 'na' || strpos($vl, 'स्थापित नहीं है') === 0) return 'not_applicable';
    return $v;
}
function status_label($v)
{
    $code = status_code($v);
    if ($code === 'active') return 'सक्रिय';
    if ($code === 'non-active') return 'निष्क्रिय';
    if ($code === 'closed') return 'परिसमापनाधीन';
    if ($code === 'not_applicable') return 'स्थापित नही है';
    return h($v);
}

/* POST handling (unchanged, only minor safety/format) */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mandal_name              = $_POST['mandal_name']              ?? '';
    $janpad_name              = $_POST['janpad_name']              ?? '';
    $total_union              = $_POST['total_union']              ?? '';
    $active_union             = $_POST['active_union']             ?? '';
    $inactive_union           = $_POST['inactive_union']           ?? '';
    $liquidation_union        = $_POST['liquidation_union']        ?? '';

    $latitude  = $_POST['latitude']  ?? '';
    $longitude = $_POST['longitude'] ?? '';

    if ($mandal_name === '' || $janpad_name === '') {
        echo "<script>alert('कृपया मण्डल और जनपद चयन करें।');</script>";
    } else {

        $has = [
            'liquidator_name'      => false,
            'liquidator_from_date' => false,
            'samiti_naam'          => false,
            'ncd_id'               => false,
            'row_status'           => false,
            'latitude'             => false,
            'longitude'            => false,
        ];
        $colCheckSql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'block_union'AND COLUMN_NAME IN ('liquidator_name','liquidator_from_date','samiti_naam','ncd_id','row_status','latitude','longitude')";
        if ($resCols = mysqli_query($db, $colCheckSql)) {
            while ($rc = mysqli_fetch_assoc($resCols)) {
                $col = $rc['COLUMN_NAME'] ?? '';
                if (isset($has[$col])) $has[$col] = true;
            }
            mysqli_free_result($resCols);
        }

        $fixedCols = [
            "mandal_name","janpad_name",
            "total_union","active_union","inactive_union","liquidation_union",
            "sachiv_name",
            "land_area","land_sthiti","society_land","godown_suitable","rack_distance","arrived_land_type"
        ];

        $rowsInserted = 0;

        $a_samiti   = $_POST['active']['samiti_naam']         ?? [];
        $a_ncd      = $_POST['active']['ncd_id']              ?? [];
        $a_status   = $_POST['active']['active_status']       ?? [];
        $a_area     = $_POST['active']['land_area']           ?? [];
        $a_sthiti   = $_POST['active']['land_sthiti']         ?? [];
        $a_place    = $_POST['active']['society_land']        ?? [];
        $a_godam    = $_POST['active']['godown_suitable']     ?? [];
        $a_rack     = $_POST['active']['rack_distance']       ?? [];
        $a_road     = $_POST['active']['arrived_land_type']   ?? [];
        $a_liq_name = $_POST['active']['liquidator_name']     ?? [];
        $a_liq_date = $_POST['active']['liquidator_from_date'] ?? [];

        $n = max(
            count($a_samiti),
            count($a_ncd),
            count($a_status),
            count($a_area),
            count($a_sthiti),
            count($a_place),
            count($a_godam),
            count($a_rack),
            count($a_road),
            count($a_liq_name),
            count($a_liq_date)
        );
        for ($i=0; $i<$n; $i++){
            $samiti_naam_i       = trim($a_samiti[$i] ?? '');
            $ncd_id_i            = trim($a_ncd[$i]    ?? '');
            $active_status_i     = trim($a_status[$i] ?? '');
            $land_area_i         = trim($a_area[$i] ?? '');
            $land_sthiti_i       = trim($a_sthiti[$i] ?? '');
            $society_land_i      = trim($a_place[$i] ?? '');
            $godown_suitable_i   = trim($a_godam[$i] ?? '');
            $rack_distance_i     = trim($a_rack[$i] ?? '');
            $arrived_land_type_i = trim($a_road[$i] ?? '');
            $liquidator_name_i   = trim($a_liq_name[$i] ?? '');
            $liquidator_date_i   = trim($a_liq_date[$i] ?? '');

            if (
                $samiti_naam_i==='' &&
                $land_area_i==='' && $land_sthiti_i==='' && $society_land_i==='' &&
                $godown_suitable_i==='' && $rack_distance_i==='' && $arrived_land_type_i==='' 
            ) continue;

            $cols = $fixedCols;
            $vals = [
                "'".esc($db,$mandal_name)."'",
                "'".esc($db,$janpad_name)."'",
                "'".esc($db,$total_union)."'",
                "'".esc($db,$active_union)."'",
                "'".esc($db,$inactive_union)."'",
                "'".esc($db,$liquidation_union)."'",
                "''", // sachiv_name
                "'".esc($db,$land_area_i)."'",
                "'".esc($db,$land_sthiti_i)."'",
                "'".esc($db,$society_land_i)."'",
                "'".esc($db,$godown_suitable_i)."'",
                "'".esc($db,$rack_distance_i)."'",
                "'".esc($db,$arrived_land_type_i)."'"
            ];

            if ($has['row_status']) {
                $cols[] = "row_status";
                $finalStatus = ($active_status_i !== '') ? $active_status_i : 'सक्रिय';
                $vals[] = "'".esc($db,$finalStatus)."'";
            }
            if ($has['samiti_naam']) {
                $cols[] = "samiti_naam";
                $vals[] = "'".esc($db,$samiti_naam_i)."'";
            }
            if ($has['ncd_id']) {
                $cols[] = "ncd_id";
                $vals[] = ($ncd_id_i!=='') ? "'".esc($db,$ncd_id_i)."'" : "''";
            }
            if ($has['liquidator_name']) {
                $cols[] = "liquidator_name";
                $vals[] = ($active_status_i === 'परिसमापनाधीन' && $liquidator_name_i !== '') 
                          ? "'".esc($db,$liquidator_name_i)."'" 
                          : "''";
            }
            if ($has['liquidator_from_date']) {
                $cols[] = "liquidator_from_date";
                $vals[] = ($active_status_i === 'परिसमापनाधीन' && $liquidator_date_i !== '') 
                          ? "'".esc($db,$liquidator_date_i)."'" 
                          : "NULL";
            }
            if ($has['latitude']) {
                $cols[] = "latitude";
                $vals[] = ($latitude!=='') ? "'".esc($db,$latitude)."'" : "NULL";
            }
            if ($has['longitude']) {
                $cols[] = "longitude";
                $vals[] = ($longitude!=='') ? "'".esc($db,$longitude)."'" : "NULL";
            }

            $sql = "INSERT INTO block_union (".implode(",",$cols).") VALUES (".implode(",",$vals).")";
            if (!mysqli_query($db, $sql)) {
                echo "<div style='padding:12px;color:#b91c1c;'>Insert failed (row ".($i+1)."): ".h(mysqli_error($db))."</div>";
            } else {
                $rowsInserted++;
            }
        }

        if ($rowsInserted > 0) {
            echo "<script>alert('Inserted successfully (".$rowsInserted." rows)');</script>";
            $_POST = [];
        } else {
            echo "<script>alert('कोई वैध पंक्ति नहीं मिली।');</script>";
        }
    }
}

// --- FILTERS (land area from/to) ---
$land_from = isset($_GET['land_area_from']) ? trim($_GET['land_area_from']) : '';
$land_to   = isset($_GET['land_area_to'])   ? trim($_GET['land_area_to'])   : '';

// sanitize numeric inputs (allow decimal point). Use floatval to get numeric value; empty string -> null
$land_from_val = ($land_from === '') ? null : floatval(str_replace(',', '.', $land_from));
$land_to_val   = ($land_to === '')   ? null : floatval(str_replace(',', '.', $land_to));

$lat_pref  = $_POST['latitude']  ?? '';
$long_pref = $_POST['longitude'] ?? '';
$godown_filter = isset($_GET['godown_suitable']) ? trim($_GET['godown_suitable']) : '';
?>

<style>
    /* base card + form styles (kept concise) */
    .card {
        background: #f9f9f9;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .08)
    }

    /* Section heading: made larger (approx 2x) and stronger BG */
    .section-heading {
        background: #2b6fb3;
        /* stronger blue */
        color: #fff;
        padding: 18px 20px;
        /* increased padding */
        border-radius: 6px;
        margin-bottom: 18px;
        font-weight: 800;
        font-size: 2.0em;
        /* ~2x size */
        line-height: 1.05;
        text-transform: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .06);
    }

    .form-label {
        font-weight: 700;
        margin-bottom: 5px
    }

    .form-control,
    .form-select,
    textarea {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        font-size: .95em
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 15px 20px
    }

    .btn-primary {
        background: #4a90e2;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer
    }

    .btn-primary:hover {
        background: #357ab8
    }

    @media (max-width:768px) {
        .form-grid {
            grid-template-columns: 1fr
        }
    }

    .alert {
        margin: 12px 0;
        padding: 10px 12px;
        border-radius: 8px;
        font-weight: 700
    }

    .alert.success {
        background: #e8f7ee;
        color: #11634a;
        border: 1px solid #b7ebc6
    }

    .alert.error {
        background: #fdecea;
        color: #8a1c22;
        border: 1px solid #f5c2c7
    }

    /* Table container wrapper (visual wrap) */
    .table-container {
        background: linear-gradient(180deg, #ffffff, #fbfdff);
        border: 1px solid #e6eefc;
        padding: 14px;
        border-radius: 10px;
        box-shadow: 0 6px 18px rgba(10, 45, 85, 0.03);
    }

    .table-wrap {
        overflow: auto;
        margin-top: 12px
    }

    /* Table visuals */
    .report-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1100px
    }

    .report-table th,
    .report-table td {
        border: 1px solid #e6edf7;
        padding: 10px 12px;
        font-size: 13px;
        white-space: nowrap;
        vertical-align: middle
    }

    /* Header row — bigger (approx 2x visual weight compared to normal cells) */
    .report-table thead th {
        background: #e8f5ff;
        /* soft bluish background */
        text-align: left;
        font-size: 1.15em;
        /* larger than body cells */
        font-weight: 800;
        padding: 16px 14px;
        /* taller header */
        color: #08386b;
        letter-spacing: 0.2px;
    }

    /* emphasize the top heading row inside the card (if you want the heading above table also larger) */
    .card .top-row-highlight {
        background: #f0f7ff;
        border-radius: 6px;
        padding: 12px 14px;
        margin-bottom: 10px;
        font-weight: 700;
    }

    /* smaller screens: keep heading readable and allow wrapping */
    @media (max-width:900px) {
        .section-heading {
            font-size: 1.6em;
            padding: 14px 16px;
        }

        .report-table thead th {
            font-size: 1em;
            padding: 12px 8px;
        }

        .report-table {
            min-width: 900px
        }
    }

    #map_container {
        height: 280px
    }
</style>

<div class="card" style="margin-top: 40px;">
    <h3 class="section-heading" style="text-align: center;">ब्लाक यूनियन रिपोर्ट</h3>
    <!-- FILTERS: Land area range (uses GET so it won't interfere with insert POST) -->
    <div style="margin:12px 0 18px 0; display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        <form method="get" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <label style="font-weight:700;">भूमि क्षेत्र (From)</label>
            <input name="land_area_from" class="form-control" style="width:110px;" value="<?php echo h($land_from); ?>">
            <label style="font-weight:700;">(To)</label>
            <input name="land_area_to" class="form-control" style="width:110px;" value="<?php echo h($land_to); ?>">
             <label style="font-weight:700;">गोदाम उपयुक्त</label>
            <select name="godown_suitable" class="form-control" style="width:150px;">
                <option value="">Select</option>
                <option value="हाँ"  <?php if($godown_filter==='हाँ') echo 'selected'; ?>>हाँ</option>
                <option value="नहीं" <?php if($godown_filter==='नहीं') echo 'selected'; ?>>नहीं</option>
            </select>
            <button class="btn-primary" type="submit">Filter</button>
            <a href="?" style="margin-left:8px; padding:8px 10px; display:inline-block; background:#eee; border-radius:5px;">Reset</a>
            <!-- <div style="font-size:12px; color:#666; margin-left:16px;">नोट: मान्य numeric मान रखें, दशमलव के लिए "." प्रयोग करें (0.5)</div> -->
        </form>
    </div>

    <div class="table-container">
        <div class="table-wrap">
            <table id="general_stat_table" class="report-table">
                <thead>
                    <tr>
                        <th style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;">S.No.</th>
                        <th style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;">NCD ID</th>
                        <th style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;">मण्डल </th>
                        <th style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;">जिला </th>
                        <th style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;">समिति <br> सक्रिय?</th>
                        <th style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:300px;">समिति का नाम</th>
                        <th style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;">भूमि क्षेत्रफल</th>
                        <th style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;">भूमि स्थिति</th>
                        <th style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;">स्थान</th>
                        <th style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;">गोदाम उपयुक्त</th>
                        <th style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;">रैक दूरी<br> (किमी)</th>
                        <th style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;">पहुंच मार्ग</th>
                        <th style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;">परिसमापक <br> का नाम</th>
                        <th style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;">कब से<br> परिसमापक</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    // Adjusted SELECT to fetch renamed columns (table name kept same: block_union)
                    $sql = "SELECT s.*, dt.district_name, dv.division_name
                            FROM block_union s
                            LEFT JOIN master_district dt ON s.janpad_name = dt.sno
                            LEFT JOIN master_division dv ON s.mandal_name = dv.sno
                            WHERE 1=1";

                    if (!empty($_SESSION['district_id'])) {
                        $dis_ids = array_map('intval', (array)$_SESSION['district_id']);
                        if (!empty($dis_ids)) {
                            $sql .= " AND s.janpad_name IN (" . implode(',', $dis_ids) . ")";
                        }
                    }

                    // Apply land area filter when provided.
                    // NOTE: This assumes land_area column contains a numeric prefix (like "0.5" or "1.0").
                    // MySQL will coerce non-numeric trailing characters when using +0. Example: '0.5 hectares'+0 => 0.5
                    if ($land_from_val !== null && $land_to_val !== null) {
                        // both bounds
                        $sql .= " AND (s.land_area+0) BETWEEN " . (float)$land_from_val . " AND " . (float)$land_to_val;
                    } elseif ($land_from_val !== null) {
                        $sql .= " AND (s.land_area+0) >= " . (float)$land_from_val;
                    } elseif ($land_to_val !== null) {
                        $sql .= " AND (s.land_area+0) <= " . (float)$land_to_val;
                    }
                    if ($godown_filter !== '') {
                        $sql .= " AND s.godown_suitable = '" . esc($db, $godown_filter) . "'";
                    }

                    $sql .= " ORDER BY dv.division_name,dt.district_name, s.sno DESC";
                    $result = mysqli_query($db, $sql);

                    $i = 1;
                    $last = null;

                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $dist = $row['district_name'] ?? 'N/A';

                            if ($dist !== $last) {
                                // group header — colspan equals number of columns (14)
                                // echo '<tr class="group-row"><th colspan="14">जनपद: ' . h($dist) . '</th></tr>';
                                // $last = $dist;
                            }

                            echo "<tr>";
                            echo "<td style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;'>" . $i++ . "</td>";
                            echo "<td style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;'>" . h($row['ncd_id'] ?? '') . "</td>";
                            echo "<td style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;'>" . h($row['division_name'] ?? '') . "</td>";
                            echo "<td style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;'>" . h(strtoupper($row['district_name'] ?? '')) . "</td>";
                            echo "<td style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;'>" . h(status_label($row['row_status'] ?? '')) . "</td>";
                            echo "<td style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:300px;'>" . h($row['samiti_naam'] ?? '') . "</td>";
                            echo "<td style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;'>" . h($row['land_area'] ?? '') . "</td>";
                            echo "<td style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;'>" . h($row['land_sthiti'] ?? '') . "</td>";
                            echo "<td style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;'>" . h($row['society_land'] ?? '') . "</td>";
                            echo "<td style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;'>" . h($row['godown_suitable'] ?? '') . "</td>";
                            echo "<td style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;'>" . h($row['rack_distance'] ?? '') . "</td>";
                            echo "<td style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100px;'>" . h($row['arrived_land_type'] ?? '') . "</td>";
                            echo "<td style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;'>" . h($row['liquidator_name'] ?? '') . "</td>";
                            echo "<td style='white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:120px;'>" . h($row['liquidator_from_date'] ?? '') . "</td>";
                            echo "</tr>";
                        }

                        mysqli_free_result($result);
                    } else {
                        echo "<tr><td colspan='14' style='text-align:center;'>कोई रिकॉर्ड नहीं मिला</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var t = $('#general_stat_table').DataTable({
            paging: true,
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            searching: true,
            info: true,
            deferRender: true,
            processing: true,
            responsive: false,              // IMPORTANT: disable responsive hiding (no onclick collapse)
            scrollX: true,                  // allow horizontal scroll instead of hiding columns
            order: [],                      // keep DB order by default; change if needed
            columnDefs: [
                {
                    searchable: false,
                    orderable: false,
                    targets: 0 // serial number column
                }
            ],
            dom: 'lfrtip'
        });
        t.on('order.dt search.dt page.dt', function () {
            var info = t.page.info();
            var start = info.page * info.length; // safer calculation for the first index on current page
            t.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                cell.innerHTML = start + i + 1;
            });
        }).draw();
    });
</script>

<?php
page_footer_start();
page_footer_end();
?>
