<?php
if (php_sapi_name() === 'cli') {
    $script_mode = TRUE;
}

include 'init.php';
use \RackNews\Report as Report;
use \RackNews\ObjectUtils as ObjectUtils;

$report = new Report(ObjectUtils::get_objects());
if (php_sapi_name() === 'cli') {
    $longopts = array(
        'report:', 'has:', 'types:', 'names:',
        'id:', 'log:', 'comment:', 'fields:',
        'matching:', 'format:'
    );

    $params = getopt('', $longopts);
} else {
    $params = $_REQUEST;
}

$report->set_params($params);
try {
    $report->build();
    $report->display();
} catch (Exception $e) {
    die($e->getMessage());
}
