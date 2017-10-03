<?php

namespace Mediathek;

include '../init.inc.php';

$hdlprefix = '20.500.11806/mediathek/';
$urlbase = 'https://mediathek.hgk.fhnw.ch/detail.php?id=';


$solr = new SOLR( $solrclient );

$filename =  $config['tmpprefix']."videokunst_ch.html";
$wgetfile = $config['tmpprefix']."videokunst_ch_load.sh";

if( !file_exists( $filename )) {
  file_put_contents( $filename, file_get_contents( 'http://www.videokunst.ch/Join2.asp' ));
}

$dom = new \DOMDocument( "1.0", "ISO-8859-15" );
@$dom->loadHTMLFile( $filename );
$finder = new \DomXPath($dom);

$data = array( 'artist'=>array(), 'video'=>array());

$nodes = $finder->query("//*[contains(@class, 'artist')]");
foreach( $nodes as $node ) {
  $artistid = $node->getAttribute( 'id' );
  $data['artist'][$artistid] = array( 'artistid'=>$artistid, 'video'=>array());
}

foreach( array( 'artistlink', 'artistname', 'artisttext', 'bio1', 'bio2', 'bio3' ) as $classname ) {
  $nodes = $finder->query("//*[contains(@class, '{$classname}')]");
  foreach( $nodes as $node ) {
    $artistid = $node->getAttribute( 'id' );
    $data['artist'][$artistid][$classname] = preg_replace( '/\[url=([^\]]+)\]([^\[]*)\[\/url\]/', '<a href="\1">\2</a>', html_entity_decode( $node->textContent ));
  }
}

for( $i = 1; $i < 20; $i++ ) {
  $video = array();
  $artistid = 0;
  foreach( array( 'vidtitel', 'viddate', 'vidtext', 'vidhtml', 'vidhight', 'vid-mp4', 'vid-ogv', 'vid-flv' ) as $classname ) {
    //echo "{$classname}{$i}\n";
    $nodes = $finder->query("//*[contains(@class, '{$classname}{$i}')]");
    foreach( $nodes as $node ) {
      $artistid = $node->getAttribute( 'id' );

      if( !array_key_exists($i, $data['artist'][$artistid]['video'] )) $data['artist'][$artistid]['video'][$i] = array('videoid'=>$artistid.'.'.$i, 'artistid'=>$artistid );
      $data['artist'][$artistid]['video'][$i][$classname] = preg_replace( '/\[url=([^\]]+)\]([^\[]*)\[\/url\]/', '<a href="\1">\2</a>', html_entity_decode( $node->textContent ));
    }
  }
}

foreach( $data['artist'] as $artistid=>$artist ) {
  foreach( $artist['video'] as $videoid=>$video ) {
    foreach( array( 'artistlink', 'artistname', 'artisttext', 'bio1', 'bio2', 'bio3' ) as $classname ) {
      if( array_key_exists( $classname, $artist )) $video[$classname] = $artist[$classname];
    }
    $data['video'][$artistid.'.'.$videoid] = $video;
  }
  unset( $data['artist'][$artistid]['video'] );
}

/*
unlink( $wgetfile );
foreach( $data['video'] as $k=>$v ) {
  if( !file_exists( '/data/www/vhosts/mediathek.fhnw.ch/content/videokunst.ch/'.substr( $v['vid-mp4'], strlen( 'http://web199.login-67.hoststar.ch/images/' )))) {
    file_put_contents( $wgetfile, "wget ".escapeshellarg( $v['vid-mp4'] )."\n", FILE_APPEND );
  }
  file_put_contents( $wgetfile, "ffmpeg -i ".escapeshellarg( $v['vid-mp4'] )." -r 0.05 -f image2 shots/{$k}_%05d.png\n", FILE_APPEND );

}
*/


$entity = new videokunst_ch_personEntity( $db );

foreach( $data['artist'] as $artist ) {
  $entity->reset();
  $entity->loadFromArray( $artist );
  $title = $entity->getTitle();
  echo "Title: ".$title."\n";

  $tags = $entity->getTags();
  echo "Tags: ";
  print_r( $tags );

  $cluster = $entity->getCluster();
  echo "Cluster: ";
  print_r( $cluster );

  $authors = $entity->getAuthors();
  echo "Authors: ";
  print_r( $authors );

  $urls = $entity->getURLs();
  echo "URLs: ";
  print_r( $urls );

  $solr->import( $entity );
}

$entity = new videokunst_ch_workEntity( $db );

$cnt = 1;
foreach( $data['video'] as $video ) {
  $entity->reset();
  $entity->loadFromArray( $video );
  $title = $entity->getTitle();
  echo "Title: ".$title."\n";

  $tags = $entity->getTags();
  echo "Tags: ";
  print_r( $tags );

  $cluster = $entity->getCluster();
  echo "Cluster: ";
  print_r( $cluster );

  $authors = $entity->getAuthors();
  echo "Authors: ";
  print_r( $authors );

  $urls = $entity->getURLs();
  echo "URLs: ";
  print_r( $urls );

  $solr->import( $entity );
  echo "{$cnt}\n";
  $cnt++;
}
