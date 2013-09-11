<?php
include 'init.php';
use \RackNews\Report as Report;
use \RackNews\RTObject as RTObject;

$report = new Report(RTObject::get_objects());
$params = array(
    'fields' => array('name', 'FQDN'),
    'format' => Report::FORMAT_CSV,
    'has'    => array('name'),
    'types'  => array('server')
);

$report->set_params($params);
try {
    $report->build();
    $report->display();
} catch (Exception $e) {
    die($e->getMessage());
}
