<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include( 'init.inc.php' );

$name = isset( $_REQUEST['name'] ) ? trim( ($_REQUEST['name'])) : null;
$value = isset( $_REQUEST['value'] ) ? trim( ($_REQUEST['value'])) : null;
$itemid = isset( $_REQUEST['pk'] ) ? trim( ($_REQUEST['pk'])) : null;

list( $serial, $passid ) = explode( '-', $itemid );

$sql = "SELECT `group` FROM wallet.pass WHERE id=".intval( $passid );
$grp = $db->GetOne( $sql );
if(!$session->inGroup( $grp )) {
	header( "HTTP/1.1 403 Forbidden" );
	exit;
}


try {

	$field = null;
	switch( $name ) {
		case 'name':
		case 'barcode':
		case 'email2':
			$field = $name;
			break;
	}

	$sql = "SELECT * FROM wallet.pass WHERE id=".$passid;
	$pass = $db->GetRow( $sql );

	if( !$pass ) {
		http_response_code( 500 );
		echo "{status: 'error', msg: 'pass {$passid} not found'}";
		exit;
	}

	$passFile = "{$pass['folder']}/{$serial}.pkpass";
	if( file_exists( $passFile )) unlink( $passFile );

	$sql = "UPDATE wallet.card SET `{$name}`=".$db->qstr( $value )." WHERE pass=".intval( $passid )." AND serial=".intval( $serial );
	$db->Execute( $sql );
	print_r( $_REQUEST );
}
catch( Exception $ex ) {
	header( "HTTP/1.1 500 Internal Server Error" );
	echo "{status: 'error', msg: 'database error!'}";
}
?>
