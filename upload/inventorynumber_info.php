<?php
namespace Mediathek;
use PDO;
include( '../init.inc.php' );

if( !$session->isLoggedIn()) {
    http_response_code(401);
    echo "User not signed in";
    exit;
}

$db = new \PDO("mysql:host={$config['indexer']['host']};dbname={$config['indexer']['database']};charset=utf8", $config['indexer']['user'], $config['indexer']['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$searchterm = $_GET['search'];

if(ctype_alpha($searchterm[0])){
    $searchterm = substr($searchterm, 1);
}

if(!is_numeric($searchterm)){
    http_response_code(400);
    echo "Invalid Number";
    exit;
}

$inventoryno = intval($searchterm);

if($result = $db->query("select * from inventoryno where id = $inventoryno")->fetchObject()){
    echo json_encode($result);
}else{
    http_response_code(404);
    echo "Info not found";
}
