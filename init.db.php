<?php
require_once 'lib/adodb5/adodb-exceptions.inc.php';
require_once 'lib/adodb5/adodb.inc.php';

$db = NewAdoConnection( 'mysqli' );
$db->PConnect( $config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database'] );
$db->SetCharSet('utf8');

?>