<?php
namespace Mediathek;

$matcher = array();
$matcher[] = '/^SFX:(.*)$/';

include( 'init.inc.php' );

$q = isset( $_REQUEST['q'] ) ? trim( $_REQUEST['q'] ) : null;
$qr = isset( $_REQUEST['qr'] );

if( $qr ) {
	header( "Location: https://chart.googleapis.com/chart?chs=130x130&cht=qr&chld=M2&chl=".urlencode($_SERVER['SCRIPT_URI'].'?q='.urlencode( $q )));
	return;
}

$squery = $solrclient->createSelect();
$helper = $squery->getHelper();
$squery->setRows( 1 );
$squery->setStart( 0 );

$qstr = 'originalid:'.$helper->escapePhrase( $q );
$qstr .= ' OR id:'.$helper->escapePhrase( $q );
$qstr .= ' OR code:*'.$helper->escapeTerm( ':'.$q );

$squery->setQuery( $qstr );
$rs = $solrclient->select( $squery );
$numResults = $rs->getNumFound();

if( $numResults == 0 ) {
	http_response_code( 404 );
	header( 'Content-type: text/plain' );

	echo "Not found: ".$qstr;
	return;	
} elseif( $numResults > 1 ) {
	http_response_code( 418 );
	header( 'Content-type: text/plain' );

	echo "More than one result: {$qstr}\n";
	foreach( $rs as $doc ) {
		print_r( $doc );
	}
	
	return;	
}

foreach( $rs as $doc ) {
	$urls = $doc->url;
	foreach( $urls as $url ) {
		foreach( $matcher as $match ) {
			if( preg_match( $match, $url, $matches )) {
				$url = $matches[1];
				$sql = "INSERT INTO session_extlink( php_session_id, objectid, targeturl, accesstime )
					VALUES(".$db->qstr( $session->getID()).", ".$db->qstr( $doc->id ).", ".$db->qstr( $url ).", NOW())";
				$db->Execute( $sql );

				http_response_code( 303 );
				header( 'Location: '.$url );
				return;
			}
		}
	}
}

?>