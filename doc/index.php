<?php 
$rd = '..';
include("$rd/resources/template-parts/header.php");
require('Parsedown.php');

$text = file_get_contents("$rd/README.md");
if (!$text) {
    echo 'Nothing to see here.';
} else {
    $result = Parsedown::instance()->parse($text);
    echo $result;
}

include("$rd/resources/template-parts/footer.php");
