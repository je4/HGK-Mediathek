<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
include 'config.inc.php';

include 'vendor/autoload.php';
require_once 'lib/Mediathek/Autoloader.php';
require_once 'lib/Passbook/Autoloader.php';
require_once 'lib/Httpful/Autoloader.php';

\Mediathek\Autoloader::register();
\Passbook\Autoloader::register();
\Httpful\Autoloader::register();

require_once 'lib/adodb5/adodb-exceptions.inc.php';
require_once 'lib/adodb5/adodb.inc.php';

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
            try {
              $db->PConnect( $config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database'] );
            }
            catch( Exception $e ) {
              print_r( $config['db']);
              throw $e;

            }
        }
        $db->SetCharSet('utf8');
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
    }
}

doConnectMySQL();

$session = new Mediathek\Session( $db, $_SERVER );
$solrclient = new Solarium\Client(DEBUG ? $config['solariumdev']:$config['solarium']);
$solrclient->getPlugin('postbigrequest');

require 'lib/Phpoaipmh/Autoloader.php';
require 'lib/Psr/Autoloader.php';
require 'lib/GuzzleHttp/Autoloader.php';
//require 'lib/HalExplorer/Autoloader.php';
require 'lib/SimpleHal/Autoloader.php';

\Phpoaipmh\Autoloader::register();
\Psr\Autoloader::register();
\GuzzleHttp\Autoloader::register();
//\HalExplorer\Autoloader::register();
\Stormsys\SimpleHal\Autoloader::register();

/*
$googleclient = new Google_Client();
$googleclient->setApplicationName( $config['google']['app'] );
$googleclient->setDeveloperKey($config['google']['key']);
$googleservice = new Google_Service_Books($googleclient);
*/

//print_r( $session->getGroups());
?>
