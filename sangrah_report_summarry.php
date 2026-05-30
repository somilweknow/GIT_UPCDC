<?php
include("scripts/settings.php");
// if (empty($_SESSION['usersno'])) {
//     header('Location: index.php');
//     exit;
// }
$f_from = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
$f_to = isset($_GET['date_to']) ? $_GET['date_to'] : date('Y-m-d');
$do_report = isset($_GET['do_report']);

$f_from = preg_match('/^\d{4}-\d{2}-\d{2}$/', $f_from) ? $f_from : date('Y-m-01');
$f_to = preg_match('/^\d{4}-\d{2}-\d{2}$/', $f_to) ? $f_to : date('Y-m-d');
if ($f_from > $f_to)
    $f_to = $f_from;

/* ── Helpers ───────────────────────────────────────────────── */
function rupee($n)
{
    return number_format((float) $n, 2, '.', ',');
}
function dmy($ymd)
{
    if (!$ymd)
        return '-';
    $p = explode('-', $ymd);
    return count($p) === 3 ? $p[2] . '-' . $p[1] . '-' . $p[0] : $ymd;
}
function esc($s)
{
    return htmlspecialchars((string) ($s ?? ''), ENT_QUOTES, 'UTF-8');
}
function blank()
{
    return [
        'ts' => 0,
        'tp' => 0,
        'tr' => 0,
        'tt' => 0,
        'ms' => 0,
        'mp' => 0,
        'mr' => 0,
        'mt' => 0,
        'cs' => 0,
        'cp' => 0,
        'cr' => 0,
        'ct' => 0,
        'bs' => 0,
        'bp' => 0,
        'br' => 0,
        'bt' => 0,
        'emp' => 0
    ];
}
function add(&$t, $r)
{
    foreach (['ts', 'tp', 'tr', 'tt', 'ms', 'mp', 'mr', 'mt', 'cs', 'cp', 'cr', 'ct', 'bs', 'bp', 'br', 'bt'] as $k)
        $t[$k] += $r['_' . $k];
}

/* ── Query ─────────────────────────────────────────────────── */
$data = [];
$grand = blank();
$filter_err = '';

if ($do_report) {
    $df = mysqli_real_escape_string($db, $f_from);
    $dt = mysqli_real_escape_string($db, $f_to);

    $sql = "
        SELECT
            COALESCE(mdiv.division_name, 'अज्ञात मण्डल') AS division_name,
            COALESCE(md.district_name,   'अज्ञात जनपद')  AS district_name,
            SUM(s.total_salary)           AS total_sal,
            SUM(s.total_pension)          AS total_pen,
            SUM(s.total_retirement_dues)  AS total_ret,
            SUM(d.month_salary)           AS month_sal,
            SUM(d.month_pension)          AS month_pen,
            SUM(d.month_retirement_dues)  AS month_ret,
            SUM(latest.cum_sal)           AS cum_sal,
            SUM(latest.cum_pen)           AS cum_pen,
            SUM(latest.cum_ret)           AS cum_ret,
            COUNT(DISTINCT d.employee_name) AS emp_count
        FROM sangrah_payment_static s
        INNER JOIN sangrah_payment_daily d
               ON  d.district_id   = s.district_id
               AND d.employee_name = s.employee_name
               AND d.entry_date BETWEEN '$df' AND '$dt'
        INNER JOIN (
            SELECT district_id, employee_name, MAX(entry_date) AS max_date
            FROM sangrah_payment_daily
            WHERE entry_date BETWEEN '$df' AND '$dt'
            GROUP BY district_id, employee_name
        ) lk ON lk.district_id   = d.district_id
             AND lk.employee_name = d.employee_name
        INNER JOIN (
            SELECT district_id, employee_name, entry_date,
                   cum_salary AS cum_sal, cum_pension AS cum_pen,
                   cum_retirement_dues AS cum_ret
            FROM sangrah_payment_daily
            WHERE entry_date BETWEEN '$df' AND '$dt'
        ) latest ON latest.district_id   = d.district_id
                AND latest.employee_name = d.employee_name
                AND latest.entry_date    = lk.max_date
        LEFT JOIN master_district md   ON md.sno   = s.district_id
        LEFT JOIN master_division mdiv ON mdiv.sno = md.division_id
        GROUP BY mdiv.sno, md.sno
        ORDER BY mdiv.division_name, md.district_name";

    $res = mysqli_query($db, $sql);
    if (!$res) {
        $filter_err = 'डेटाबेस त्रुटि: ' . htmlspecialchars(mysqli_error($db));
    } else {
        while ($r = mysqli_fetch_assoc($res)) {
            $ts = (float) $r['total_sal'];
            $tp = (float) $r['total_pen'];
            $tr2 = (float) $r['total_ret'];
            $ms = (float) $r['month_sal'];
            $mp = (float) $r['month_pen'];
            $mr = (float) $r['month_ret'];
            $cs = (float) $r['cum_sal'];
            $cp = (float) $r['cum_pen'];
            $cr = (float) $r['cum_ret'];
            $bs = max(0, $ts - $cs);
            $bp = max(0, $tp - $cp);
            $br = max(0, $tr2 - $cr);
            $row = [
                'district_name' => $r['district_name'],
                '_emp' => (int) $r['emp_count'],
                '_ts' => $ts,
                '_tp' => $tp,
                '_tr' => $tr2,
                '_tt' => $ts + $tp + $tr2,
                '_ms' => $ms,
                '_mp' => $mp,
                '_mr' => $mr,
                '_mt' => $ms + $mp + $mr,
                '_cs' => $cs,
                '_cp' => $cp,
                '_cr' => $cr,
                '_ct' => $cs + $cp + $cr,
                '_bs' => $bs,
                '_bp' => $bp,
                '_br' => $br,
                '_bt' => $bs + $bp + $br,
            ];
            $div = $r['division_name'];
            $data[$div][] = $row;
            add($grand, $row);
            $grand['emp'] += $row['_emp'];
        }
    }
}

/* ── PHP helper: render one data cell ─────────────────────── */
function td_num($v, $bold = false)
{
    $cls = $bold ? 'nb' : 'n';
    echo '<td class="' . $cls . '">' . rupee($v) . '</td>';
}
function td_row($r, $bold_yog = true)
{
    td_num($r['_ts']);
    td_num($r['_tp']);
    td_num($r['_tr']);
    td_num($r['_tt'], $bold_yog);
    td_num($r['_ms']);
    td_num($r['_mp']);
    td_num($r['_mr']);
    td_num($r['_mt'], $bold_yog);
    td_num($r['_cs']);
    td_num($r['_cp']);
    td_num($r['_cr']);
    td_num($r['_ct'], $bold_yog);
    td_num($r['_bs']);
    td_num($r['_bp']);
    td_num($r['_br']);
    td_num($r['_bt'], $bold_yog);
}
function div_totals($districts)
{
    $dv = blank();
    foreach ($districts as $d) {
        add($dv, $d);
        $dv['emp'] += $d['_emp'];
    }
    return $dv;
}

/* ============================================================  HTML  */
page_header_start();
?>
<style>
    /* ══ BASE ════════════════════════════════════════════════════ */
    body {
        background: #f0f4f8 !important;
    }

    .rpt-wrap {
        max-width: 99%;
        margin: 0 auto;
        padding: 20px 10px 70px;
        /* font-family: Arial, 'Noto Sans Devanagari', sans-serif; */
    }

    /* ══ PAGE TITLE ══════════════════════════════════════════════ */
    .rpt-head {
        text-align: center;
        margin-bottom: 20px;
        position: relative;
    }

    .rpt-head h1 {
        font-size: 2rem;
        font-weight: 900;
        color: #1a1a2e;
        margin: 0 0 6px;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .rpt-head h2 {
        font-size: 1.35rem;
        font-weight: 800;
        color: #1a3a5c;
        margin: 0 0 5px;
    }

    .rpt-head p {
        font-size: 1.05rem;
        color: #444;
        margin: 0;
    }

    .date-badge {
        position: absolute;
        top: 0;
        right: 0;
        border: 2.5px solid #1a1a2e;
        padding: 6px 16px;
        font-size: 1.1rem;
        font-weight: 800;
        color: #1a1a2e;
        border-radius: 5px;
    }

    /* ══ FILTER FORM ═════════════════════════════════════════════ */
    .filter-box {
        background: #fff;
        border: 2px solid #90a4ae;
        border-radius: 10px;
        padding: 10px 24px 12px;
        margin-bottom: 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-end;
    }

    .fg {
        display: flex;
        flex-direction: column;
        gap: 7px;
        min-width: 180px;
        flex: 1 1 180px;
    }

    .fg label {
        font-size: 1.1rem;
        font-weight: 800;
        color: #37474f;
    }

    .fg input[type="date"] {
        border: 2px solid #90a4ae;
        border-radius: 7px;
        padding: 7px 28px;
        font-size: 1.15rem;
        /* font-family: inherit; */
        color: #1a1a1a;
        background: #f9fafb;
        width: 100%;
        outline: none;
    }

    .fg input[type="date"]:focus {
        border-color: #1565c0;
    }

    .btn-go {
        background: #1565c0;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 13px 36px;
        font-size: 1.2rem;
        font-weight: 900;
        cursor: pointer;
        /* font-family: inherit; */
        white-space: nowrap;
    }

    .btn-go:hover {
        background: #0d47a1;
    }

    .btn-print {
        background: #fff;
        color: #1565c0;
        border: 2.5px solid #1565c0;
        border-radius: 8px;
        padding: 12px 24px;
        font-size: 1.15rem;
        font-weight: 800;
        cursor: pointer;
        /* font-family: inherit; */
        white-space: nowrap;
    }

    .btn-print:hover {
        background: #e3f2fd;
    }

    .btn-excel {
        background: #1b5e20;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 13px 26px;
        font-size: 1.15rem;
        font-weight: 800;
        cursor: pointer;
        /* font-family: inherit; */
        white-space: nowrap;
    }

    .btn-excel:hover {
        background: #145217;
    }

    /* ══ INFO STRIP ══════════════════════════════════════════════ */
    .active-f {
        background: #e3f2fd;
        border: 1.5px solid #90caf9;
        border-radius: 8px;
        padding: 13px 20px;
        font-size: 1.1rem;
        font-weight: 700;
        color: #0d47a1;
        margin-bottom: 18px;
    }

    /* ══ ERROR / NO-DATA ════════════════════════════════════════ */
    .rpt-error {
        background: #ffebee;
        border-left: 6px solid #c62828;
        border-radius: 6px;
        padding: 14px 20px;
        font-size: 1.1rem;
        color: #b71c1c;
        margin-bottom: 18px;
        font-weight: 700;
    }

    .rpt-nodata {
        background: #fff8e1;
        border-left: 6px solid #f9a825;
        border-radius: 6px;
        padding: 16px 22px;
        font-size: 1.2rem;
        color: #6d4c00;
        font-weight: 800;
        margin-bottom: 18px;
    }

    /* ══ TABLE WRAPPER ══════════════════════════════════════════ */
    .tbl-wrap {
        overflow-x: auto;
        border-radius: 7px;
        border: 2px solid #90a4ae;
        margin-bottom: 30px;
        background: #fff;
    }

    /* ══ TABLE ══════════════════════════════════════════════════ */
    table.rpt {
        border-collapse: collapse;
        width: 100%;
        min-width: 1600px;
        background: #fff;
    }

    /* Header */
    table.rpt thead tr th {
        background: #b8cce4;
        color: #1a1a1a;
        font-size: 15px;
        font-weight: 900;
        text-align: center;
        vertical-align: middle;
        padding: 11px 7px;
        border: 1.5px solid #7a9bbf;
        line-height: 1.5;
        white-space: nowrap;
    }

    table.rpt thead tr.subhead th {
        background: #d6e4f0;
        font-size: 14px;
        font-weight: 800;
        padding: 9px 6px;
    }

    /* column group top-border colours */
    th.g-total {
        border-top: 5px solid #c05500 !important;
    }

    th.g-month {
        border-top: 5px solid #0d7a27 !important;
    }

    th.g-cum {
        border-top: 5px solid #1565c0 !important;
    }

    th.g-bal {
        border-top: 5px solid #b71c1c !important;
    }

    /* State Total */
    tr.state-total td {
        background: #dce6f1 !important;
        font-size: 15px !important;
        font-weight: 900 !important;
        border: 2px solid #7a9bbf !important;
        padding: 10px 7px !important;
        text-align: center !important;
    }

    tr.state-total td:nth-child(2) {
        text-align: left !important;
        font-size: 16px !important;
    }

    /* Division header */
    tr.div-hdr td {
        background: #d9e1f2 !important;
        font-size: 16px !important;
        font-weight: 900 !important;
        color: #1a1a2e !important;
        text-align: left !important;
        padding: 10px 14px !important;
        border: 1.5px solid #7a9bbf !important;
        letter-spacing: .5px;
    }

    /* District rows */
    tr.dist-row td {
        font-size: 18px;
        padding: 9px 7px;
        border: 1px solid #b0bec5;
        text-align: center;
        vertical-align: middle;
        color: #1a1a1a;
    }

    tr.dist-row td:nth-child(2) {
        text-align: left;
        font-weight: 700;
        font-size: 18px;
    }

    tr.dist-row:nth-child(even) td {
        background: #f4f8fc;
    }

    tr.dist-row:hover td {
        background: #e8f4fd;
    }

    td.sr {
        font-weight: 800;
        font-size: 14px;
        color: #444;
    }

    /* Division Total */
    tr.div-total td {
        background: #e2efda !important;
        font-size: 15px !important;
        font-weight: 900 !important;
        border: 2px solid #82a86e !important;
        padding: 10px 7px !important;
        text-align: center !important;
    }

    tr.div-total td:nth-child(2) {
        text-align: left !important;
        font-size: 15px !important;
    }

    /* Grand Total footer */
    tfoot tr td {
        background: #1565c0 !important;
        color: #fff !important;
        font-size: 16px !important;
        font-weight: 900 !important;
        padding: 12px 7px !important;
        border: 2px solid #0d47a1 !important;
        text-align: center !important;
    }

    tfoot tr td:nth-child(2) {
        text-align: left !important;
    }

    /* number cells */
    td.n {
        monospace;
        font-size: 14px;
    }

    td.nb {
        /* font-family:'Courier New',monospace; */
        font-size: 14px;
        font-weight: 900;
    }

    /* ══ PRINT ══════════════════════════════════════════════════ */
    @media print {
        table.rpt {
            min-width: 100% !important;
            width: 100% !important;
            table-layout: fixed;
        }

        table.rpt th,
        table.rpt td {
            font-size: 12px !important;
            padding: 3px !important;
            white-space: normal !important;
            word-break: break-word;
        }

        .rpt-wrap {
            padding: 0 !important;
            margin: 0 !important;
        }

        .filter-box,
        .no-print {
            display: none !important;
        }

        body {
            background: #fff !important;
        }

        table.rpt thead tr th {
            background: #b8cce4 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        tr.div-hdr td {
            background: #d9e1f2 !important;
            -webkit-print-color-adjust: exact;
        }

        tr.div-total td {
            background: #e2efda !important;
            -webkit-print-color-adjust: exact;
        }

        tr.state-total td {
            background: #dce6f1 !important;
            -webkit-print-color-adjust: exact;
        }

        tfoot tr td {
            background: #1565c0 !important;
            -webkit-print-color-adjust: exact;
        }

        @page {
            /* size: A4 portrait; */
            size: A3 landscape;
            margin: 2mm;
        }
    }
</style>
<?php page_header_end();
page_sidebar(); ?>

<div class="rpt-wrap">

    <!-- ══ HEADING ══════════════════════════════════════════════ -->
    <div class="rpt-head">
        <?php if ($do_report): ?>
            <div class="date-badge">Date: <?php echo date('d-m-Y'); ?></div>
        <?php endif; ?>
        <h1>प्रारूप – 7 | संग्रह योजना</h1>
        <h2>मण्डल / जनपदवार मासिक-क्रमिक भुगतान सारांश रिपोर्ट</h2>
        <p>संग्रह योजना के अन्तर्गत कार्यरत / सेवानिवृत्त लिपिक, चालक, सहयोगी, वैतनिक अमीन व अमीन सहयोगियों की भुगतान
            प्रगति</p>
    </div>

    <!-- ══ FILTER FORM ══════════════════════════════════════════ -->
    <div class="filter-box no-print">
        <form method="GET" action="" style="display:contents;">
            <input type="hidden" name="do_report" value="1">
            <div class="fg">
                <label>प्रारम्भ दिनांक</label>
                <input type="date" name="date_from" value="<?php echo esc($f_from); ?>" required>
            </div>
            <div class="fg">
                <label>समाप्ति दिनांक</label>
                <input type="date" name="date_to" value="<?php echo esc($f_to); ?>" required>
            </div>
            <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
                <button type="submit" class="btn-go">📋 रिपोर्ट देखें</button>
                <button type="button" class="btn-print" onclick="window.print()">Download<br>PDF</button>
                <?php if ($do_report && !empty($data)): ?>
                    <button type="button" class="btn-excel" onclick="exportExcel()">Download<br>Excel</button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if ($filter_err): ?>
        <div class="rpt-error">⚠ <?php echo esc($filter_err); ?></div>
    <?php endif; ?>

    <?php if ($do_report && !$filter_err): ?>

        <div class="active-f">
            📅 दिनांक: <strong><?php echo dmy($f_from); ?></strong> से <strong><?php echo dmy($f_to); ?></strong>
            <!-- &nbsp;|&nbsp; कुल मण्डल: <strong><?php echo count($data); ?></strong>
            &nbsp;|&nbsp; कुल जनपद: <strong><?php echo array_sum(array_map('count', $data)); ?></strong>
            &nbsp;|&nbsp; कर्मचारी रिकॉर्ड: <strong><?php echo $grand['emp']; ?></strong> -->
        </div>

        <?php if (empty($data)): ?>
            <div class="rpt-nodata">📭 चयनित दिनांक सीमा में कोई डेटा उपलब्ध नहीं है।</div>

        <?php else: ?>

            <!-- ══ TABLE ═══════════════════════════════════════════════ -->
            <div class="tbl-wrap">
                <table class="rpt" id="rpt_table">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width:50px;">SN.<br>(1)</th>
                            <th rowspan="2" style="min-width:180px;">DISTRICT<br>(2)</th>
                            <th rowspan="2">कर्मचारी<br>रिकॉर्ड<br>(3)</th>
                            <th colspan="4" class="g-total">कुल अधतन देयता</th>
                            <th colspan="4" class="g-month">माह में भुगतान</th>
                            <th colspan="4" class="g-cum">क्रमिक भुगतान</th>
                            <th colspan="4" class="g-bal">माह के अन्त में अवशेष देयता</th>
                        </tr>
                        <tr class="subhead">
                            <th class="g-total">वेतन (₹)<br>(4)</th>
                            <th class="g-total">पेंशन (₹)<br>(5)</th>
                            <th class="g-total">सेवानिवृत्तिक<br>देयता (₹)<br>(6)</th>
                            <th class="g-total">योग (₹)<br>(7)</th>
                            <th class="g-month">वेतन (₹)<br>(8)</th>
                            <th class="g-month">पेंशन (₹)<br>(9)</th>
                            <th class="g-month">सेवानिवृत्तिक<br>देयता (₹)<br>(10)</th>
                            <th class="g-month">योग (₹)<br>(11)</th>
                            <th class="g-cum">वेतन (₹)<br>(12)</th>
                            <th class="g-cum">पेंशन (₹)<br>(13)</th>
                            <th class="g-cum">सेवानिवृत्तिक<br>देयता (₹)<br>(14)</th>
                            <th class="g-cum">योग (₹)<br>(15)</th>
                            <th class="g-bal">वेतन (₹)<br>(16)</th>
                            <th class="g-bal">पेंशन (₹)<br>(17)</th>
                            <th class="g-bal">सेवानिवृत्तिक<br>देयता (₹)<br>(18)</th>
                            <th class="g-bal">योग (₹)<br>(19)</th>
                        </tr>
                    </thead>
                    <tbody>

                        <!-- State Total -->
                        <tr class="state-total">
                            <td></td>
                            <td>State Total</td>
                            <td><?php echo $grand['emp']; ?></td>
                            <?php
                            td_num($grand['ts']);
                            td_num($grand['tp']);
                            td_num($grand['tr']);
                            td_num($grand['tt'], true);
                            td_num($grand['ms']);
                            td_num($grand['mp']);
                            td_num($grand['mr']);
                            td_num($grand['mt'], true);
                            td_num($grand['cs']);
                            td_num($grand['cp']);
                            td_num($grand['cr']);
                            td_num($grand['ct'], true);
                            td_num($grand['bs']);
                            td_num($grand['bp']);
                            td_num($grand['br']);
                            td_num($grand['bt'], true);
                            ?>
                        </tr>

                        <?php $sr = 0;
                        foreach ($data as $div_name => $districts): ?>
                            <?php $dv = div_totals($districts); ?>

                            <!-- Division Header -->
                            <tr class="div-hdr">
                                <td colspan="19"><?php echo esc($div_name); ?> Division</td>
                            </tr>

                            <!-- District Rows -->
                            <?php foreach ($districts as $dist):
                                $sr++; ?>
                                <tr class="dist-row">
                                    <td class="sr"><?php echo $sr; ?></td>
                                    <td><?php echo esc($dist['district_name']); ?></td>
                                    <td><?php echo $dist['_emp']; ?></td>
                                    <?php td_row($dist); ?>
                                </tr>
                            <?php endforeach; ?>

                            <!-- Division Total -->
                            <tr class="div-total">
                                <!-- <td></td> -->
                                <td colspan="2">Division Total</td>
                                <td><?php echo $dv['emp']; ?></td>
                                <?php
                                td_num($dv['ts']);
                                td_num($dv['tp']);
                                td_num($dv['tr']);
                                td_num($dv['tt'], true);
                                td_num($dv['ms']);
                                td_num($dv['mp']);
                                td_num($dv['mr']);
                                td_num($dv['mt'], true);
                                td_num($dv['cs']);
                                td_num($dv['cp']);
                                td_num($dv['cr']);
                                td_num($dv['ct'], true);
                                td_num($dv['bs']);
                                td_num($dv['bp']);
                                td_num($dv['br']);
                                td_num($dv['bt'], true);
                                ?>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td>Grand Total / महायोग</td>
                            <td><?php echo $grand['emp']; ?></td>
                            <?php
                            td_num($grand['ts']);
                            td_num($grand['tp']);
                            td_num($grand['tr']);
                            td_num($grand['tt'], true);
                            td_num($grand['ms']);
                            td_num($grand['mp']);
                            td_num($grand['mr']);
                            td_num($grand['mt'], true);
                            td_num($grand['cs']);
                            td_num($grand['cp']);
                            td_num($grand['cr']);
                            td_num($grand['ct'], true);
                            td_num($grand['bs']);
                            td_num($grand['bp']);
                            td_num($grand['br']);
                            td_num($grand['bt'], true);
                            ?>
                        </tr>
                    </tfoot>
                </table>
            </div>

        <?php endif; ?>
    <?php endif; ?>

</div>

<!-- ══ JS: Excel export only ════════════════════════════════ -->
<?php if ($do_report && !empty($data)): ?>
    <script>
        function exportExcel() {
            var tbl = document.getElementById('rpt_table');
            if (!tbl) return;

            /* Build HTML table string that Excel can open */
            var html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" '
                + 'xmlns:x="urn:schemas-microsoft-com:office:excel" '
                + 'xmlns="http://www.w3.org/TR/REC-html40">'
                + '<head><meta charset="UTF-8">'
                + '<style>'
                + 'th,td{border:1px solid #999;padding:6px;font-size:13px;}'
                + '.state-total td,.div-total td,tfoot td{font-weight:900;}'
                + '.div-hdr td{background:#d9e1f2;font-weight:900;}'
                + '</style></head><body>'
                + '<h2>प्रारूप-7 | संग्रह योजना | <?php echo dmy($f_from); ?> से <?php echo dmy($f_to); ?></h2>'
                + tbl.outerHTML
                + '</body></html>';

            var blob = new Blob(['\uFEFF' + html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'Sangrah_Report_<?php echo $f_from; ?>_to_<?php echo $f_to; ?>.xls';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    </script>
<?php endif; ?>