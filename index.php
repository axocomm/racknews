<?php
include 'init.php';

use \RackNews\Report as Report;

$report = new Report();
$report->build_report();
