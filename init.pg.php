<?php
require_once 'lib/adodb5/adodb-exceptions.inc.php';
require_once 'lib/adodb5/adodb.inc.php';


$pg = null;

function doConnectPostgres($force = false) {
    global $pg, $config;
    if( $pg == null ) {
        $pg = NewAdoConnection( 'postgres9' );     
    }
    if( $force || !$pg->isConnected()) {
        $pg->PConnect( $config['pg']['host'], $config['pg']['user'], $config['pg']['password'], $config['pg']['database'] );
        $pg->SetCharSet('utf8');
    }
}

doConnectPostgres();




?>