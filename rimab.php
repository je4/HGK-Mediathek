<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once 'lib/adodb5/adodb-exceptions.inc.php';
require_once 'lib/adodb5/adodb.inc.php';


$db = NewAdoConnection( 'mysqli' );
$db->PConnect( 'v000306.adm.ds.fhnw.ch', 'rimab', 'KIxGoIO6ph6GpGV9CJpu', 'rimab_redirect' );
$db->SetCharSet('utf8');
$db->SetFetchMode(ADODB_FETCH_ASSOC);

$id = $_REQUEST['id'];
$row = $db->GetRow('SELECT zotero_key FROM object WHERE id='.$db->qStr($id));
if( $row == null ) {
	echo "not found: {$id}";
} else {
	header("Location: https://www.zotero.org/groups/2514673/rimab/items/{$row['zotero_key']}/library" );
}