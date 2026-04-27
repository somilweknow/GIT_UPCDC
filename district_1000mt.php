<?php
include("scripts/settings.php");
include("scripts/settings_dbase_upcod.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$range = [0.198, 0.237];
$title = "Division Wise 1000 MT Godown Feasibility Report";

include("district_common_report.php");