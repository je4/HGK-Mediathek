<?php
$id = isset( $_REQUEST['id']) ? $_REQUEST['id'] : null;
$test = isset( $_REQUEST['test']);

if( $test ) {
    $dom = new DOMDocument( "1.0", "UTF-8" );
    $url = $dom->createElement( "url");
    $url->appendChild( new DOMCdataSection('https://mediathek.hgk.fhnw.ch/testscreen.php'));
    $dom->appendChild( $url );
    
    header( "content-type: application/xml; charset=UTF-8" );
    echo $dom->saveXML();
}
?>