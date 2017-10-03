<?php
namespace Mediathek;

include( 'init.inc.php' );

if(!$session->isLoggedIn()) {
	header( "HTTP/1.1 403 Forbidden" );
	exit;
}

$sql = "SELECT cardid FROM card WHERE email LIKE ".$db->qstr( $session->shibGetMail());
$cardid = $db->getOne( $sql );

if( !file_exists( $config['wallet']['dir'].'/'.$cardid.'.pkpass' )) {
	header( "HTTP/1.1 404 Not Found" );
	exit;
}

header( 'Content-type: application/vnd.apple.pkpass' );
header('Content-Disposition: attachment; filename="'.$cardid.'.pkpass"');

readfile( $config['wallet']['dir'].'/'.$cardid.'.pkpass' );

?>