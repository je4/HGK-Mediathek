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

    $stmtInsert = $db->prepare('insert into inventoryno(type,created_by,created_at) values(:type, :created_by, now())');
    $stmtInsert->bindValue(':created_by', $session->shibGetMail());

    $typecode = $_POST['type'];
    if(strpos("BS",$typecode)===false){
        http_response_code(400);
        echo "Unknown Type";
        exit;
    }
    $stmtInsert->bindValue(':type', $typecode);
    if($stmtInsert->execute()){
        $insertId = $db->lastInsertId();
        echo $typecode.str_pad($insertId, 10, "0", STR_PAD_LEFT);
    }else{
        http_response_code(500);
        echo "DB Insert Error";
        exit;
    }

