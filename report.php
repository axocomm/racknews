<?php
include 'init.php';
use \RackNews\Report as Report;
use \RackNews\ObjectUtils as ObjectUtils;

$report = new Report(ObjectUtils::get_objects());
$params = $_REQUEST;

$report->set_params($params);
try {
    $report->build();
    $report->display();
} catch (Exception $e) {
    die($e->getMessage());
}
