<?php
include 'init.php';
use \RackNews\Report as Report;
use \RackNews\RTObject as RTObject;

$report = new Report(RTObject::get_objects());
print_r($report->get_objects());
