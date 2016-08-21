<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

include 'lib/adodb5/adodb-exceptions.inc.php';
include 'lib/adodb5/adodb.inc.php';

include 'vendor/autoload.php';
require 'lib/Mediathek/Autoloader.php';
require 'lib/Passbook/Autoloader.php';
require 'lib/Phpoaipmh/Autoloader.php';
require 'lib/Psr/Autoloader.php';
require 'lib/GuzzleHttp/Autoloader.php';
//require 'lib/HalExplorer/Autoloader.php';
require 'lib/SimpleHal/Autoloader.php';

include 'lib/Google/autoload.php';

// include $_SERVER['SIMPLESAMLPHP_CONFIG_DIR'].'/../vendor/autoload.php';

include 'config.inc.php';

\Mediathek\Autoloader::register();
\Passbook\Autoloader::register();
\Phpoaipmh\Autoloader::register();
\Psr\Autoloader::register();
\GuzzleHttp\Autoloader::register();
//\HalExplorer\Autoloader::register();
\Stormsys\SimpleHal\Autoloader::register();

$db = NewAdoConnection( 'mysqli' );
$db->PConnect( $config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['database'] );
$db->SetCharSet('utf8');

$solrclient = new Solarium\Client($config['solarium']);
/*
$googleclient = new Google_Client();
$googleclient->setApplicationName( $config['google']['app'] );
$googleclient->setDeveloperKey($config['google']['key']);
$googleservice = new Google_Service_Books($googleclient);
*/
$session = new Mediathek\Session( $db, $_SERVER );

//print_r( $session->getGroups());
?>