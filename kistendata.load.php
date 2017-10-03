<?php
header( "content-type: text/json ");

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);


include('kisten.inc.php');

echo json_encode($kisten);
?>