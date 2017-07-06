<?php

namespace Mediathek;
use PDO;
use Httpful;

include '../init.inc.php';

function thesis2zotero(){
    echo "Enter ikuthesis";

    $DBNAME = 'rfid';
    $DBHOST = 'ba14ns21402.adm.ds.fhnw.ch';
    $DBUSER = 'root';
    $DBUPWD = 'yGobAvttdBPCbZ9T2emGJq3';
    $APIKEY = 'S321LmGUjscgc2ZfDMmkb9Nh';
    $APIURL = 'https://api.zotero.org';


    //Get Zotero thesis template
    $uriGetTemplate = "$APIURL/items/new?itemType=thesis";
    $resGetTemplate = Httpful\Request::get($uriGetTemplate)->send();
    if($resGetTemplate->code!=200){
        echo "Zotero template not loaded";
        die();
    }
    $tmplThesis = $resGetTemplate->body;

    //Set temolate standard values
    $tmplThesis->university = 'HGK FHNW, Institut Kunst';
    $tmplThesis->place = 'Basel';
    $tmplThesis->rights = 'internal use only';

    $tmplThesis->creators = array();

    try {
        $db = new PDO("mysql:host=$DBHOST;dbname=$DBNAME;charset=utf8", $DBUSER, $DBUPWD);
        $db->exec("SET CHARACTER SET utf8");
    } catch (PDOException $e) {
        print "DB Connection Error: " . $e->getMessage();
        die();
    }

    //Clone thesis from template, fill the appropriate values and POST to Zotero
    foreach ($db->query('select * from source_ikuthesis where Strichcode is NULL', PDO::FETCH_OBJ) as $rowThesis){
        $currThesis = clone $tmplThesis;
        $currThesis->title = $rowThesis->Title;
        $authors = getCreators('author', $rowThesis->Autor);
        foreach ($authors as $author) {
            $currThesis->creators[] = $author;
        }
        $contributors = getCreators('contributor', $rowThesis->Contributor);
        foreach ($contributors as $contributor){
            $currThesis->creators[] = $contributor;
        }
        $currThesis->date = $rowThesis->Date;
        $currThesis->callNumber = $rowThesis->callNumber;
        $currThesis->extra = $rowThesis->Extra;
        $currThesis->tags[] = array('tag'=>$rowThesis->Tag1);
        $currThesis->tags[] = array('tag'=>$rowThesis->Tag2);
        var_dump($currThesis);
        postThesis($currThesis);
        break;
    }


    exit();
}

//Returns a generator object with all the creators of the type, like 'author', 'contributor'
function getCreators($creatorType, $name){

    if(empty($name)) return null;

  $creatornames = explode(';', $name);

  foreach ($creatornames as $creatorname){
      $creator = new \stdClass();
      $creator->creatorType = $creatorType;

      $exName = explode(',', $creatorname);
      $creator->lastName = trim($exName[0]);
      if(count($exName) < 2){
         $creator->firstName = '';
      }else{
          $creator->firstName = trim($exName[1]);
      }

      yield $creator;
  }
}
function postThesis($thesis){
    $APIKEY = 'S321LmGUjscgc2ZfDMmkb9Nh';
    $APIURL = 'https://api.zotero.org';
    $ZOTGRP = '1510088';

    $uriPostThesis = "$APIURL/groups/$ZOTGRP/items";
    $jsonThesis = json_encode($thesis);
    $resPostThesis = Httpful\Request::post($uriPostThesis)->addHeader('Authorization', "Bearer $APIKEY")->body($jsonThesis)->sendsJson()->send();
    var_dump($resPostThesis);
    if($resPostThesis->code!=200){
        echo "Zotero template not loaded";
        die();
    }

}

thesis2zotero();



exit();
$hdlprefix = '20.500.11806/mediathek/';
$urlbase = 'https://mediathek.hgk.fhnw.ch/detail.php?id=';
$groups = array( 1387750 );

$solr = new SOLR( $solrclient );

$zotero = new Zotero( $db, $config['zotero']['apiurl'], $config['zotero']['apikey'], $config['zotero']['mediapath'], STDOUT );
try {
  foreach( $groups as $group ) {
        $zotero->syncItems( $group );
  }
} catch (\ADODB_Exception $e) {
  var_dump($e);
  adodb_backtrace($e->gettrace());
}

$entity = new zoteroEntity( $db );
$cnt = 0;
foreach( $groups as $group ) {
  foreach( $zotero->loadChildren( $group ) as $item ) {
    echo $item; if( $item->isTrashed()) echo "  [TRASH]\n";
    $entity->reset();
    $data = $item->getData();
    $entity->loadFromArray( $data );
    $title = $entity->getTitle();
    echo "Title: ".$title."\n";
    if( $item->isTrashed()) {
      $solr->delete( $entity->getID() );
    }
    else {
      $solr->import( $entity );
    }
    echo "{$cnt}\n";
    $cnt++;
  }
}

?>
