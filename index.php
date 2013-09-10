<?php
include 'php/Report.class.php';
use \RT_News\Report as Report;

$report = new Report();
$report->build_report();
