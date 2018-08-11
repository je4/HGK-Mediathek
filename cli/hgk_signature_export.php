<?php

namespace Mediathek;

include '../init.inc.php';
include '../init.pg.php';

$tmpfile = $config['tmpprefix']."ids_hgk.dat";
$cntfile = $config['tmpprefix']."counter_hgk.dat";
$cursorfile = $config['tmpprefix']."cursor_hgk.dat";

$entity = new swissbibEntity( $db );
$solr = new SOLR( $solrclient );
$entities = array();


if(false) {

	$customizer = $solrclient->getPlugin('customizerequest');

	$pagesize = 10000;
	$page = 0;
	$numPages = 1;

	$counter = 1;

	$cursormark = '*';

  $entity = new swissbibEntity( $db );

	do {
		$squery = $solrclient->createSelect();
		$squery->setRows( $pagesize );
		//$squery->createFilterQuery('notdone')->setQuery("-catalog:*");
		$squery->createFilterQuery('nodelete')->setQuery("deleted:false");
		//$squery->setFields( array( 'id' ));
		$squery->createFilterQuery('source')->setQuery( 'source:"swissbib"' );

		$squery->createFilterQuery( 'category' )->setQuery( "category:2!!signature!!NEBIS!!E75" );

		$squery->addSort('id', $squery::SORT_DESC);
		$customizer->createCustomization( 'cursorMark' )
			->setType( 'param' )
			->setName( 'cursorMark' )
			->setValue( $cursormark );
		$rs = $solrclient->select( $squery );
		$numResults = $rs->getNumFound();
		$numPages = floor( $numResults / $pagesize );
		foreach( $rs as $doc ) {
			echo sprintf( "%0.2f - %08d ", $counter*100/$numResults, $counter ).$doc->id."\n";
			//echo $doc->id."\n";
      $source = $doc->source;
      if( !strlen( $doc->metagz )) {
        echo "    ERROR!!!\n";
        continue;
      }
      $data = gzdecode( base64_decode( $doc->metagz ));
      echo "   loading...";
      $entity->loadFromDoc( $doc );
      $codes = $entity->getSigCode( 'NEBIS', 'E75' );
      $libsigcode = $entity->getLibSigCodes();
      foreach( $libsigcode as $c ) {
        $signature = $c['sig'];
        $lib = $c['lib'];
        if( $signature == null ) continue;
        if( $lib != 'E75' ) continue;

        $sql = "SELECT DISTINCT a.`Signatur` AS signatur, i.marker
        			FROM inventory_cache i, ARC a
        			WHERE a.`Strichcode`=i.itemid AND a.`Signatur` LIKE ".$db->qstr("{$signature}");
        //echo "<--\n {$sql}\n -->\n";
        $drs = $db->Execute( $sql );
        foreach( $drs as $row ) {
          $kiste = $row['marker'];
          if( !$kiste ) $kiste = 'unknown';
          $reihe = null;
          if( preg_match( '/([A-Z])_[0-9]{3}_([a-z])/', $kiste, $matches )) {
            $reihe = "{$matches[1]}{$matches[2]}";
          }
          $area = null;
          if( preg_match( '/^(P 700|ZS|KP)/', $signature, $matches )) {
            $area = $matches[1];
          }
          $sql = "INSERT INTO temp_signatures( signature, kiste, reihe, area )
            VALUES( ".$db->qstr( $signature ).", ".$db->qstr( $kiste ).", ".$db->qstr( $reihe ).", ".$db->qstr( $area )." )";
            echo "{$signature}:{$kiste}:{$reihe}\n";
  //          echo "{$sql}\n";
          try {
            $db->Execute( $sql );
          }
          catch( \Exception $e ) {
            echo "duplicate\n";
          }
        }
        $drs->Close();
      }
			$counter++;
		}
		$data = $rs->getData();
		if( $cursormark == $data["nextCursorMark"] )
			break;

		$cursormark = $data["nextCursorMark"];
		$page++;
	} while( true );

}
else {

  $pattern = array();
	$sql = "SELECT signature, category2 FROM curate_signature2category ORDER BY signature, category2";
	$rs = $db->Execute( $sql );
	foreach( $rs as $row ) {
		$pattern[$row['signature']] = $row['category2'];
	}
	$rs->Close();



  foreach( $pattern as $sig=>$a ) {
    if( !strlen( trim( $a ))) continue;


		$sql = "INSERT INTO curate_signature2category VAlUES (".$db->qstr( $sig ).", ".$db->qstr( 'field!!'.$a ).", ".$db->qstr( 'area!!'.$a )." )";
		echo "{$sql}\n";
    $db->Execute( $sql );

    $sql = "UPDATE temp_signatures SET area=".$db->qstr( $a )." WHERE signature LIKE ".$db->qstr( "{$sig}%");
    echo "{$sql}\n";
    $db->Execute( $sql );
  }
}


doConnectMySQL();
?>
