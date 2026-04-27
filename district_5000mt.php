<?php
include("scripts/settings.php");
include("scripts/settings_dbase_upcod.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$range = [0.8093, 1000.0];
$title = "Division Wise 5000 MT And More Than 5000 MT Godown Feasibility Report";

include("district_common_report.php");