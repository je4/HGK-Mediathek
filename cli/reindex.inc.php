<?php

function reindex( $idfile ) {
  global $db, $solr;

  $entities = array();

  $counter = 0;
  $sql = "SELECT value FROM status WHERE name=".$db->qstr( $idfile );
  $start = intval( $db->GetOne( $sql ));
  $tmp = fopen( $idfile, 'r' );
  while( $id = fgets( $tmp ) ) {
  	echo $id;
  	if( $start > $counter ) {
  		echo "{$start} > {$counter}: skip\n";
  		$counter++;
  		continue;
  	}
  	echo "\n";
  	$query = $solrclient->createSelect();
  	$helper = $query->getHelper();
  	$query->setQuery( 'id:'.$helper->escapeTerm( $id ));
  	$resultset = $solrclient->select( $query );
  	if( $resultset->getNumFound() == 1 ) {
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
  			echo sprintf( "%08u - ", $counter ).$id."\n";
  			usleep( 10000 );
  			if($counter % 1000 == 0) {
  				$sql = "REPLACE INTO status(name, value) VALUES( ".$db->qstr( $idfile).", {$counter})";
          $db->Exectue( $sql );
  			}
  			if( $counter % 10000 == 0  && $counter > 0 ) {
  				echo "sleeping 5sec...\n";
  				sleep( 5 );
  			}
  		}
  	}
  	$counter++;
  }
  fclose( $tmp );
  $sql = "DELETE FROM status WHERE name=".$db->qstr( $idfile );
  $db->Execute( $sql );
}

?>
