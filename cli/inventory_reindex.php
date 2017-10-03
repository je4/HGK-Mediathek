<?php

namespace Mediathek;

include '../init.inc.php';
include '../init.pg.php';

//include( 'reindex.inc.php' );

$idfile = $config['tmpprefix']."inventory_ids.dat";
$statusname = 'inventory_reindex.php';

$entity = new swissbibEntity( $db );
$solr = new SOLR( $solrclient );

$sql = "SELECT COUNT(*) FROM status WHERE name=".$db->qstr( $idfile );
$num = intval( $db->GetOne( $sql ));
$entities = array();

if( !file_exists( $idfile ) || $num == 0 ) {
	$tmp = fopen( $idfile, 'w' );

  $sql = "SELECT NOW()";
  $now = $db->GetOne( $sql );

  $sql = "SELECT value FROM status WHERE name=".$db->qstr( $statusname );
  $lastreindex = @trim( $db->GetOne( $sql ));
  if( !strlen( $lastreindex )) $lastreindex = '1970-01-01 00:00:00';

  $counter = 0;
  $inventorytime = null;
  $sql = "SELECT itemid, marker, inventorytime FROM inventory_cache WHERE inventorytime >= ".$db->qstr( $lastreindex ). "ORDER BY inventorytime ASC";
  $rs = $db->Execute( $sql );
  foreach( $rs as $row ) {
    $barcode = $row['itemid'];
    $location = $row['marker'];
    $inventorytime = $row['inventorytime'];

    echo "{$barcode}:{$location}:{$inventorytime}\n";
  	$query = $solrclient->createSelect();
  	$helper = $query->getHelper();
    $qstr = 'signature:'.$helper->escapePhrase( 'barcode:NEBIS:E75:'.$barcode );
    echo "{$qstr}\n";
  	$query->setQuery( $qstr );
  	$resultset = $solrclient->select( $query );
  	if( $resultset->getNumFound() < 5 ) {
  		foreach( $resultset->getDocuments() as $doc ) {
  			$source = $doc->source;
  			$data = gzdecode( base64_decode( $doc->metagz ));
  			if( !array_key_exists( $source, $entities )) {
  				$class = '\\Mediathek\\'.$source.'Entity';
  				echo "creating class {$class}\n";
  				$entities[$source] = new $class( $db );
  			}
  			$entity = $entities[$source];
  			echo "   loading...";
  			$entity->loadFromDoc( $doc );
  			echo "   importing...\n";
  			$solr->import( $entity, ($counter % 5000 == 0) );
  			echo sprintf( "%08u - ", $counter ).$barcode."\n";
  			usleep( 10000 );
  			if($counter % 1000 == 0) {
  				$sql = "REPLACE INTO status(name, value) VALUES( ".$db->qstr( $statusname).", ".$db->qstr( $inventorytime ).")";
          $db->Execute( $sql );
  			}
  			if( $counter % 10000 == 0  && $counter > 0 ) {
  				echo "sleeping 5sec...\n";
  				sleep( 5 );
  			}
        $counter++;
  		}
  	}
  }
  $rs->Close();
  fclose( $tmp );

  if( $inventorytime != null ) {
    $sql = "REPLACE INTO status(name, value) VALUES( ".$db->qstr( $statusname).", ".$db->qstr( $inventorytime ).")";
    $db->Execute( $sql );
  }
}

$update = $solrclient->CreateUpdate();
$update->addCommit();
$result = $solrclient->update( $update );
echo "Commit query executed\n";
echo 'Query status: ' . $result->getStatus()."\n";
echo 'Query time: ' . $result->getQueryTime()."\n";

doConnectMySQL();
?>
