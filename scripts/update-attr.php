<?php
$script_mode = TRUE;
include('../init.php');

if (php_sapi_name() == 'cli') {
    $script_mode = 1;
    $longopts = array(
        'lookup', 'hostname:', 'id:', 'attr_id:', 'attr_name:', 'attr_value:'
    );

    $params = getopt('', $longopts);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $params = $_POST;
} else {
    die('Invalid request');
}

if (isset($params['lookup'])) {
    if (isset($params['attr_id'])) {
        ;
    } elseif (isset($params['attr_name'])) {
        $attr_name = $params['attr_name'];
        if (($attr_id = RackNews\ObjectUtils::get_attr_id($attr_name)) === FALSE) {
            die("$attr_name is not a valid attribute.\n");
        } else {
            echo "$attr_name = $attr_id\n";
        }
    } else {
        die("Missing parameters\n");
    }

    exit;
}

if (!isset($params['hostname']) or
    !(isset($params['attr_name']) or isset($params['attr_id'])) or
    !isset($params['attr_value'])) {
        die("Missing parameters\n");
}

$hostname = $params['hostname'];
if (!isset($params['attr_id'])) {
    $attr_name = $params['attr_name'];
    if (($attr_id = RackNews\ObjectUtils::get_attr_id($attr_name)) === FALSE) {
        die("$attr_name is not a valid attribute.\n");
    }
} else {
    $attr_id = $params['attr_id'];
}

$attr_value = $params['attr_value'];

update_attr($hostname, $attr_id, $attr_value);

function update_attr($hostname, $attr_id, $attr_value) {
    $objects = RackNews\ObjectUtils::get_objects();
    if (!$object = RackNews\ObjectUtils::find_by_name($objects, $hostname)) {
        die("$hostname does not exist.");
    }

    $host_id = $object['id'];
    if (check_host($object, $attr_id)) {
        try {
            commitUpdateAttrValue($host_id, $attr_id, $attr_value);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    } else {
        die("Attribute $attr_id doesn't exist for this object");
    }
}

function check_host($object, $attr_id) {
    $host_type = $object['objtype_id'];
    $map = getAttrMap();
    $attr_types = $map[$attr_id];
    if (!$attr_types or !count($attr_types)) {
        return 0;
    }

    foreach ($attr_types['application'] as $type) {
        if ($type['objtype_id'] == $host_type) {
            return 1;
        }
    }

    return 0;
}
