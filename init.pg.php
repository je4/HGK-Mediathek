<?php
require_once 'lib/adodb5/adodb-exceptions.inc.php';
require_once 'lib/adodb5/adodb.inc.php';

$pg = NewAdoConnection( 'postgres9' );
$pg->PConnect( $config['pg']['host'], $config['pg']['user'], $config['pg']['password'], $config['pg']['database'] );
$pg->SetCharSet('utf8');

?>