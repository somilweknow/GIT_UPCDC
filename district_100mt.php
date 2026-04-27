<?php
include("scripts/settings.php");
include("scripts/settings_dbase_upcod.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$range = [0.015829, 0.02886];
$title = "Division Wise 100 MT Godown Feasibility Report";

include("district_common_report.php");