<?php
$script_mode = TRUE;
include('../init.php');

if (php_sapi_name() == 'cli') {
    $in = '';
    $fh = fopen('php://stdin', 'r');
    if (!$fh) {
        die('Could not get file handle.');
    }

    while (($line = fgetc($fh)) !== FALSE) {
        $in .= $line;
    }

    fclose($fh);

    $attrs = array();
    foreach (explode(',', rtrim($in)) as $in_attr) {
        $attr = explode('=', $in_attr);
        $attrs[$attr[0]] = $attr[1];
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $attrs = $_POST;
} else {
    die('Invalid request');
}

if (!isset($attrs['hostname']) or
    !(isset($attrs['attr_name']) or isset($attrs['attr_id'])) or
    !isset($attrs['attr_value'])) {
        die('Missing parameters');
}

$hostname = $attrs['hostname'];
if (!isset($attrs['attr_id'])) {
    $attr_name = $attrs['attr_name'];
    if (($attr_id = RackNews\ObjectUtils::get_attr_id($attr_name)) === FALSE) {
        die("$attr_name is not a valid attribute.");
    }
} else {
    $attr_id = $attrs['attr_id'];
}

$attr_value = $attrs['attr_value'];

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
