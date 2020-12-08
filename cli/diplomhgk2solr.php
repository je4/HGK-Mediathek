<?php

namespace Mediathek;

include '../init.inc.php';

$filedir = '/data/www/vhosts/mediathek.fhnw.ch/html/diplomhgk';
$ressourcedir = '/data/www/vhosts/mediathek.fhnw.ch/html/diplomhgk_res';
$ressourceurl = 'http://mediathek.hgk.fhnw.ch/diplomhgk_res';

$institut_map = array(
  'biku'=>'Kunst',
  'h'=>'Hyperwerk',
  'lgk'=>'Lehrberufe für Gestaltung und Kunst',
  'id'=>'Industrial Design',
  'in3'=>'Innenarchitektur und Szenografie',
  'kuk'=>'Mode-Design',
  'msd'=>'Integrative Gestaltung Masterstudio',
  'vico'=>'Visuelle Kommunikation',
  'viwb'=>'Visuelle Kommunikation',
  'uic'=>'Visuelle Kommunikation',
  'test'=>'Testing 123',
);


function dataFromURL( $url ) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, trim($url, "'") );
    curl_setopt($ch, CURLOPT_HEADER, 0);
//    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $ret = curl_exec($ch);
    $code = intval( curl_getinfo( $ch, CURLINFO_RESPONSE_CODE ));
    curl_close($ch);
    if( $code == 404 || $code == 500 ) {
      throw( new \Exception( "Response Error {$code}" ));
    }
    return $ret;
  }


$db->SetFetchMode(ADODB_FETCH_ASSOC);

$entity = new diplomhgkEntity( $db );
$solr = new SOLR( $solrclient, $db );

//$sql = "SELECT DISTINCT * FROM source_diplomhgk";
$sql = "SELECT DISTINCT * FROM source_diplomhgk WHERE year='12020'";
echo $sql."\n";
$rs = $db->Execute( $sql );

foreach( $rs as $row ) {
	foreach( $row as $key=>$val ) {
		$data[strtolower($key)] = $val;
	}
  

  $data['meta'] = array();
  $sql = "SELECT * FROM source_diplomhgk_data WHERE year={$data['year']} AND idperson={$data['idperson']}";
  echo $sql."\n";
  $rs2 = $db->Execute( $sql );
  foreach( $rs2 as $row2 ) {
    $data['meta'][strtolower($row2['name'])] = $row2['value'];
  }
  $rs2->Close();

	echo "{$data['nachname']}\n";
	
    $idperson = $data['idperson'];
    $year = $data['year'];
    $anlassnummer = str_replace( '/', '_', $data['anlassnummer'] );
    $char = strtoupper( $data['nachname']{0} );
    $inst = null;
    $mb = null;
//    if( preg_match( '/[0-9]+-[A-Z]+-([BMAS]+)-([A-Za-z0-9]+)[-_\/]/', $anlassnummer, $matches )) {
    if( preg_match( '/[A-Z]+-([BMAS]+)-([A-Za-z0-9]+)[-_\/]/', $anlassnummer, $matches )) {
      $inst = $matches[2];
      $mb = $matches[1];
      if( $mb == 'MAS') $mb = 'M';
      echo "{$inst} // {$mb}\n";
    }
    $webmedia = @trim( $data['meta']['webmedia'] )."\n".@trim( $data['meta']['web1'] )."\n".@trim( $data['meta']['web2'] );
    $video = array();
    $links = array();
    $refs = array();
    if( strlen( $webmedia )) {
      $regex = "((https?|ftp)\:\/\/)?"; // Checking scheme
      $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Checking host name and/or IP
      $regex .= "(\:[0-9]{2,5})?"; // Check it it has port number
      $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // The real path
      $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // Check the query string params
      $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Check anchors if are used.
      //echo "{$webmedia}\n";
      //echo $regex."\n";
      if( preg_match_all( "/({$regex})/i", $webmedia, $matches )) {
        foreach( $matches[1] as $l ) {
          if( preg_match( '/:\/\/[a-z0-9-.]*\.?vimeo\.com\/([0-9]+)/i', $l, $matches2 )) {
            $video[] = "vimeo:{$matches2[1]}";
            $refs[] = "vimeo:{$matches2[1]}";
          }
          elseif( preg_match( '/:\/\/[a-z0-9-.]*\.?(youtu\.be|youtube\.com)\/watch\?v=(.+)/i', $l, $matches2 )) {
            $video[] = "youtube:{$matches2[2]}";
            $refs[] = "youtube:{$matches2[2]}";
          }
          else {
            if( !preg_match( '/:\/\//', $l )) $l = "http://{$l}";
              $links[] = "{$l}";
              $refs[] = "link:{$l}";
          }
        }
      }
    }
	
	
    $data['references'] = $links;
    $data['videos'] = $video;
    foreach( $data as $key=>$val ) {
      if( is_array( $val )) continue;
      if( $key == 'titel' ) $key = 'title';
	  $val = str_replace( array("`",chr(96)),array("'","'"), $val );
      $data[$key] = $val;
    }
    foreach( $data['meta'] as $key=>$val ) {
      if( $key == 'titel' ) $key = 'title';
	  $val = str_replace( array("`",chr(96)),array("'","'"), $val );
      $data[$key] = $val;
    }
    $data['abschluss']=($mb == 'M' ? 'Master':'Bachelor' );
    if( array_key_exists(strtolower($inst), $institut_map )) {
      $data['institut']=$institut_map[strtolower($inst)];
    }
    else {
      $data['institut']='Unbekannt';
      print_r( $data );
	  echo "{$inst}\n";
      die();
    }
    //$data['institut']=array_key_exists(strtolower($inst), $institut_map ) ? $institut_map[strtolower($inst)] : 'Unbekannt';

    $data['categories']=array( ($mb == 'M' ? 'master':'bachelor' ) );
    if( $inst ) $data['categories'][]= $inst;
    $data['tags']=array(  );
    $text = $data['meta']['beschreibung'];
    if( array_key_exists( 'tags', $data['meta'] )) $text .= "\n".$data['meta']['tags'];
    $text = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $text);
    if( preg_match_all( '/#([a-z0-9]+)/', $text, $matches )) {
      foreach( $matches[1] as $m ) {
        $data['tags'][] = $m;
      }
    }
    $slug = iconv("UTF-8", "ISO-8859-1//TRANSLIT", "{$year}-{$data['nachname']}_{$data['vornamen']}-{$data['meta']['titel']}");
    $slug = preg_replace( '/[^a-zA-Z0-9-_]/', '_', $slug );
    $data['slug'] = substr( $slug, 0, 50 );
    $data['series']=array( "Diplom {$year}");

  $collname = "DiplomHGK{$year}";
  echo "{$collname}\n";
  $sql = "SELECT collectionid FROM mediaserver.collection WHERE name=".$db->qstr( $collname );
  echo "{$sql}\n";
  $mediaserver_collection = intval( $db->getOne( $sql ));
  
  if( $mediaserver_collection == 0 ) die( "mediaserver collection {$collname} does not exists" );


  $counter = 0;
  $data['resources'] = array();
  $data['references'] = array();
  $data['files'] = array();
  $data["images"] = array();
  $data["pdfs"] = array();
  $data["videos"] = array();
  $data["references"] = array();
  $sql = "SELECT * FROM source_diplomhgk_files WHERE year={$data['year']} AND idperson={$data['idperson']}";
  echo "{$sql}\n";
  $rs2 = $db->Execute( $sql );
  foreach( $rs2 as $row2 ) {
	  $counter++;
	  $target = "{$filedir}/{$row2['filename']}";
      if( !file_exists( $target )) continue;

		$fn = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $row2['name']);
		$fn = preg_replace( '/[^a-zA-Z0-9-_]/', '_', $fn );

      $furl = "https://mediathek.hgk.fhnw.ch/mediaintern/diplomhgk{$year}/{$row2['filename']}";
      $signature = substr( $slug, 0, 50 )."-{$fn}-".$counter;
      $sql = "INSERT INTO mediaserver.master (collectionid, signature, urn ) VALUES( {$mediaserver_collection}, ".$db->qstr( $signature ).", ".$db->qstr( $furl ).")";
      echo "{$sql}\n";
      try {
        $db->Execute( $sql );
      }
      catch( \ADODB_Exception $ex ) {
        echo "already in mediaserver\n";
      }
            $url = "{$config['mediaserver']['baseurl']}{$collname}/{$signature}";
            $metaurl = $url.'/metadata';
            if( isset( $config['mediaserver']['key'] )) {
              $metaurl .= '?token='.jwt_encode(
                    ['sub'=>"{$config['mediaserver']['sub_prefix']}{$collname}/{$signature}/metadata", 'exp'=>time()+1000],
                    $config['mediaserver']['key']
                  );
            }
            echo "loading metadata from {$url}/metadata...\n";
            try {
              $meta = dataFromURL( $metaurl );
              $metaarr = json_decode( $meta, true );
              $row2['metadata'] = $metaarr;
			  $row2['url'] = "mediaserver:{$collname}/{$signature}";
              $mimetype = array_key_exists( 'mimetype', $metaarr ) ? $metaarr['mimetype'] : 'application/octet-stream';
              //var_dump( $metaarr );
            }
            catch( \Exception $ex ) {
              echo( $ex->getMessage()."\n");
            }
	  
	
	  $ext = pathinfo( $target, PATHINFO_EXTENSION );
      $mimetype = mime_content_type( $target );
      if( preg_match( '/^image\//', $mimetype )) {
        $data["images"][] = "image:mediaserver:DiplomHGK{$year}/{$signature}";
      }
      elseif( preg_match( '/^video\//', $mimetype )) {
        $data['videos'][] = "mediaserver:DiplomHGK{$year}/{$signature}";
      }
      elseif( preg_match( '/^application\/pdf/', $mimetype )) {
        $data['pdfs'][] = "mediaserver:DiplomHGK{$year}/{$signature}";
      } else {
        $data['resources'][] = array(
          'src'=>"mediaserver:DiplomHGK{$year}/{$signature}",
          'name'=>$file['name'],
          'param'=> array(
            'type'=>'unknown',
            'resourcetype'=>$mimetype,
          ),
        );
        $data['references'][] = "original:mediaserver:DiplomHGK{$year}/{$signature}";
    }

    sort( $data['videos'] );
    //$data['videos'] = array_reverse( $data['videos'] );

	
    $data['files'][] = $row2;
  }
  $rs2->Close();

  if( $data['anlassnummer'] == 'test' ) {
    //continue;
  }
  
  $slug = iconv("UTF-8", "ISO-8859-1//TRANSLIT", "{$year}-{$data['nachname']}_{$data['vornamen']}-{$data['meta']['titel']}");

  echo "{$data['nachname']}, {$data['vornamen']}\n";

  if( $data['done'] != 1 ) {
    $_id = 'diplomhgk-'.str_pad("{$data['year']}{$data['idperson']}", 12, '0', STR_PAD_LEFT );
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
