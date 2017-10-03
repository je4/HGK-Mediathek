<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include( 'init.inc.php' );

if(!$session->inGroup( 'mediathek/inventory' )) {
	header( "HTTP/1.1 403 Forbidden" );
	exit;
}


$value = isset( $_REQUEST['value'] ) ? trim( ($_REQUEST['value'])) : null;
$itemid = isset( $_REQUEST['pk'] ) ? trim( ($_REQUEST['pk'])) : null;

$user = $session->shibGetUniqueID();

try {

	$sql = "INSERT INTO inventory_m (`itemid`, `inventorytime`, `marker`, `userid`) VALUES(".$db->qstr( $itemid ).", NOW(), ".$db->qstr( $value ).", ".$db->qstr( $user ).")";
	$db->Execute( $sql );
	if( !$db->Affected_Rows() ) {
		header( "HTTP/1.1 500 Internal Server Error" );
		echo "{status: 'error', msg: 'affected rows == 0'}";
		return;
	}

	$sql = "REPLACE INTO inventory_cache (`itemid`, `inventorytime`, `marker`)  VALUES(".$db->qstr( $itemid ).", NOW(), ".$db->qstr( $value ).")";
	$db->Execute( $sql );

}
catch( Exception $ex ) {
	header( "HTTP/1.1 500 Internal Server Error" );
	echo "{status: 'error', msg: 'database error!'}";
}
?>