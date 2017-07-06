<?php

namespace Mediathek;
use PDO;
use Httpful;

include '../init.inc.php';

function thesis2zotero(){
    global $config;
    echo "Enter ikuthesis";

    $DBNAME = $config['db']['database'];
    $DBHOST = $config['db']['host'];
    $DBUSER = $config['db']['user'];
    $DBUPWD = $config['db']['password'];
    $APIKEY = $config['zotero']['apikey'];
    $APIURL = $config['zotero']['apiurl'];


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
        //var_dump($currThesis);
        postThesis($currThesis);
        //break;
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
    global $config;
    $APIKEY = $config['zotero']['apikey'];
    $APIURL = $config['zotero']['apiurl'];
    $ZOTGRP = '1510088';

    $uriPostThesis = "$APIURL/groups/$ZOTGRP/items";
    $jsonThesis = '['.json_encode($thesis).']';
    echo "Posting thesis {$thesis->title}...";
    $resPostThesis = Httpful\Request::post($uriPostThesis)->addHeader('Authorization', "Bearer $APIKEY")->body($jsonThesis)->sendsJson()->send();
    var_dump($resPostThesis->code);
    if($resPostThesis->code!=200){
        echo "Error: Code {$resPostThesis->code}\n";
        die();
    }
    echo "OK\n";

}

thesis2zotero();

?>
