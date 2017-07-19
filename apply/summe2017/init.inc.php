<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

include 'config.inc.php';

require_once dirname(__FILE__) .'/../../lib/adodb5/adodb-exceptions.inc.php';
require_once dirname(__FILE__) .'/../../lib/adodb5/adodb.inc.php';

$db = null;

function doConnectMySQL($force = false) {
    global $db, $config;
    if( $db == null ) {
        $db = NewAdoConnection( 'mysqli2' );
    }
    if( $force || !$db->isConnected()) {
        try {
            if( !$db->PConnect( $config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database'] )) {
                sleep( 1 );
                $db->PConnect( $config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database'] );
            }
        }
        catch( Exception $e ) {
            sleep( 1 );
            $db->PConnect( $config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database'] );
        }
        $db->SetCharSet('utf8');
    }
}

doConnectMySQL();
