<?php
namespace Mediathek;

ob_start();

include( 'init.inc.php' );

global $db;

$id = isset( $_REQUEST['id'] ) ? trim( $_REQUEST['id'] ) : null;
$key = isset( $_REQUEST['key'] ) ? trim( $_REQUEST['key'] ) : null;

$squery = $solrclient->createSelect();
$helper = $squery->getHelper();
if( $id ) $qstr = 'id:'.$helper->escapePhrase($id);
else $qstr = "id:none";

$acl_query = '';
foreach( $session->getGroups() as $grp ) {
	if( strlen( $acl_query ) > 0 ) $acl_query .= ' OR';
	$acl_query .= ' acl_meta:'.$helper->escapePhrase($grp);
}
$squery->createFilterQuery('acl_meta')->setQuery($acl_query);

$squery->setQuery( $qstr );

$rs = $solrclient->select( $squery );
$numResults = $rs->getNumFound();
//echo "<!-- ".$qstr." (Documents: {$numResults}) -->\n";
$doc = ( $numResults > 0 ) ? $rs->getDocuments()[0] : null;
if( !$doc ) return;

$data = json_decode( gzdecode( base64_decode( $doc->metagz )), true);
$item = new Zotero\Item( $data );
if( $key ) {
  $child = $item->getChild( $key );
  if( $child ) {
    $path = $config['zotero']['mediapath'].'/enclosure/'.$child->getLibraryId().'/'.$child->getKey();
    if( !file_exists( $path )) {
      echo $path." not found";
    }
    else {
      ob_end_clean();
/*
      echo $path."<br />";
      echo "size: ".filesize( $path );
      return;
*/
      header( "Content-type: ".$child->getType());
      header( "Content-disposition: attachment; filename=\"".$child->getFilename()."\"" );
      readfile( $path );
    }

  }
}
