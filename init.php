<?php
include 'php/RackNews/Report.class.php';
include 'php/RackNews/ObjectUtils.class.php';
include 'php/RackNews/Util.class.php';

$script_mode = true;
include '../inc/init.php';

#if (!isset($racktables_rootdir)) {
#    $racktables_rootdir = substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), '/'));
#    if ($script_mode or authenticate_rt()) {
#        try {
#            include("$racktables_rootdir/inc/init.php");
#        } catch (Exception $e) {
#            if ($e->getCode() == RackTablesError::NOT_AUTHENTICATED) {
#                authenticate_rt(1);
#            }
#        }
#    }
#}

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
