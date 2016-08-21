<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

include 'config.inc.php';

include 'vendor/autoload.php';
require_once 'lib/Mediathek/Autoloader.php';
require_once 'lib/Passbook/Autoloader.php';

\Mediathek\Autoloader::register();
\Passbook\Autoloader::register();

include 'init.db.php';
include 'init.session.php';
include 'init.solr.php';
include 'init.oaipmh.php';

/*
$googleclient = new Google_Client();
$googleclient->setApplicationName( $config['google']['app'] );
$googleclient->setDeveloperKey($config['google']['key']);
$googleservice = new Google_Service_Books($googleclient);
*/

//print_r( $session->getGroups());
?>