<?php
namespace Mediathek;

require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );


global $db, $config;

$barcode = isset( $_REQUEST['barcode'] ) ? trim( $_REQUEST['barcode'] ) : null;

if( $barcode ) {
    $sql = "INSERT INTO session_extlink( php_session_id, objectid, nebis, targeturl, accesstime )
        VALUES(".$db->qstr( $session->getID()).", ".$db->qstr( $barcode ).", 1, ".$db->qstr( 'nebis' ).", NOW())";
    $db->Execute( $sql );
    $url = 'https://mediathek.hgk.fhnw.ch/detail.php?barcode='.$barcode;
}
else {
    $url = $config['baseurl'];
}
header( 'Location: '.$url );

?>