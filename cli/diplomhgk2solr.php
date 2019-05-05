<?php

namespace Mediathek;

include '../init.inc.php';

$db->SetFetchMode(ADODB_FETCH_ASSOC);

$entity = new diplomhgkEntity( $db );
$solr = new SOLR( $solrclient, $db );

$sql = "SELECT DISTINCT * FROM source_diplomhgk";
$rs = $db->Execute( $sql );

foreach( $rs as $row ) {
  $data = $row;

  $data['meta'] = array();
  $sql = "SELECT * FROM source_diplomhgk_data WHERE year={$row['year']} AND idperson={$row['IDPerson']}";
  $rs2 = $db->Execute( $sql );
  foreach( $rs2 as $row2 ) {
    $data['meta'][$row2['name']] = $row2['value'];
  }
  $rs2->Close();

  $data['files'] = array();
  $sql = "SELECT * FROM source_diplomhgk_files WHERE year={$row['year']} AND idperson={$row['IDPerson']}";
  $rs2 = $db->Execute( $sql );
  foreach( $rs2 as $row2 ) {
    $data['files'][] = $row2;
  }
  $rs2->Close();

  if( $data['Anlassnummer'] == 'test' ) {
    //continue;
  }

  echo "{$row['Nachname']}, {$row['Vornamen']}\n";

  if( $data['done'] != 1 ) {
    $_id = 'diplomhgk-'.str_pad("{$data['year']}{$data['IDPerson']}", 12, '0', STR_PAD_LEFT );
    echo "   deleting {$_id}...\n";
    $solr->delete( $_id);
    continue;
  }
  $entity->loadFromArray( $data );

  echo $entity->getTitle()."\n";
  //print_r( $entity->getURLs());
  $solr->import( $entity );
}
$rs->Close();

$update = $solrclient->createUpdate();
// add commit command to the update query
$update->addCommit();
// this executes the query and returns the result
$result = $solrclient->update($update);
