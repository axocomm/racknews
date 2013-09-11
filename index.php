<?php
include 'init.php';
use \RackNews\Report as Report;
use \RackNews\RTObject as RTObject;

$report = new Report(RTObject::get_objects());
$params = $_REQUEST;

$report->set_params($params);
$report->build();
$report->display();
