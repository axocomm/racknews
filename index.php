<?php
include 'init.php';
use \RackNews\Report as Report;
use \RackNews\RTObject as RTObject;

$report = new Report(RTObject::get_objects());
$params = $_REQUEST;

$report->set_params($params);
try {
    $report->build();
    $report->display();
} catch (Exception $e) {
    die($e->getMessage());
}
