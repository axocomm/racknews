<?php
require('php/RackNews/Report.class.php');
require('php/RackNews/ObjectUtils.class.php');
require('php/RackNews/Util.class.php');

if (!isset($racktables_rootdir)) {
    $racktables_rootdir = substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), '/'));
    if ((isset($script_mode) && $script_mode) or authenticate_rt()) {
        try {
            require("$racktables_rootdir/inc/init.php");
        } catch (Exception $e) {
            if ($e->getCode() == RackTablesError::NOT_AUTHENTICATED) {
                authenticate_rt(1);
            }
        }
    }
}

/**
 * This function was renamed in later versions of RackTables.
 */
if (!function_exists('loadIPv4AddrList')) {
    function loadIPv4AddrList(&$info) {
        \loadIPAddrList($info);
    }
}

function authenticate_rt($f = 0) {
    if ($f or !isset($_SERVER['PHP_AUTH_USER']) or !strlen($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="RackTables"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'You must enter a username and password.';
        exit;
    } else {
        return 1;
    }
}
