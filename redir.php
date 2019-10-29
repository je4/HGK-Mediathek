<?php
namespace Mediathek;

require 'navbar.inc.php';
require 'searchbar.inc.php';
require 'header.inc.php';
require 'footer.inc.php';

include( 'init.inc.php' );


header( 'Location: https://mediathek.hgk.fhnw.ch/search.php' );
exit;

global $db, $config;

$url = isset( $_REQUEST['url'] ) ? trim( $_REQUEST['url'] ) : null;
$id = isset( $_REQUEST['id'] ) ? trim( $_REQUEST['id'] ) : null;

if( $url ) {
    $sql = "INSERT INTO session_extlink( php_session_id, objectid, targeturl, accesstime )
        VALUES(".$db->qstr( $session->getID()).", ".$db->qstr( $id ).", ".$db->qstr( $url ).", NOW())";
    $db->Execute( $sql );
}
else {
    $url = $config['baseurl'];
}
header( 'Location: '.$url );

?>