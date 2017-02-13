<?php
header( "content-type: text/json ");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include( 'init.inc.php' );

$query = isset( $_REQUEST['query'] ) ? $_REQUEST['query'] : null;

//echo json_encode( $_SERVER );
//var_dump( $_REQUEST );
//return;

if( !$query ) {
	echo json_encode( null );
	exit;
}

$md5 = null;
$qobj = json_decode( $query, true );
$invalidQuery = !( array_key_exists( 'query', $qobj )
		&& array_key_exists( 'area', $qobj )
		&& array_key_exists( 'filter', $qobj )
		&& array_key_exists( 'facets', $qobj )
		);

if( !$invalidQuery ) {
	if( count( $qobj['facets'] ) == 0) {
		$qobj['facets'][(DEBUG ? 'catalog':'source')] = (DEBUG ? $config['defaultcatalog'] : $config['defaultsource']);
	}
	
	$query = json_encode( $qobj );
	
	$md5 = md5($query);
	
	$sql = "SELECT COUNT(*) FROM web_query WHERE queryid=".$db->qstr( $md5 );
	$num = intval( $db->GetOne( $sql ));
	if( $num == 0 ) {
		$sql = "INSERT INTO web_query VALUES( ".$db->qstr( $md5 ).", ".$db->qstr( $qobj['query'] ). ", ".$db->qstr( $qobj['area'] ). ", ".$db->qstr( $query ).")";
		$db->Execute( $sql );
	}
}

echo json_encode( $md5 );

?>