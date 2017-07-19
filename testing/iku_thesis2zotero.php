<?php

namespace Mediathek;
use PDO;
use Httpful;

include '../init.inc.php';

function thesis2zotero(){
    global $config;
    echo "Entering ikuthesis\n";

    $DBNAME = $config['db']['database'];
    $DBHOST = $config['db']['host'];
    $DBUSER = $config['db']['user'];
    $DBUPWD = $config['db']['password'];
    $APIKEY = $config['zotero']['apikey'];
    $APIURL = $config['zotero']['apiurl'];

    //Get Zotero note template
    $uriGetTemplateNote = "$APIURL/items/new?itemType=note";
    $resGetTemplateNote = Httpful\Request::get($uriGetTemplateNote)->send();
    if($resGetTemplateNote->code!=200){
        echo "Zotero note template not loaded";
        die();
    }
    $tmplNote = $resGetTemplateNote->body;

    //Get Zotero thesis template
    $uriGetTemplateThesis = "$APIURL/items/new?itemType=thesis";
    $resGetTemplateThesis = Httpful\Request::get($uriGetTemplateThesis)->send();
    if($resGetTemplateThesis->code!=200){
        echo "Zotero template not loaded";
        die();
    }
    $tmplThesis = $resGetTemplateThesis->body;

    //Set template standard values
    $tmplThesis->university = 'HGK FHNW, Institut Hyperwerk';
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

    $stmtSetInserted = $db->prepare("Update source_ikuthesis set inserted = 1 where id = ?");

    //Clone thesis from template, fill the appropriate values and POST to Zotero
    foreach ($db->query('select * from source_ikuthesis where Strichcode is NULL and inserted = 0', PDO::FETCH_OBJ) as $rowThesis){
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
        $currThesis->url = $rowThesis->URL;
        if(!empty($rowThesis->Tag1))$currThesis->tags[] = array('tag'=>$rowThesis->Tag1);
        if(!empty($rowThesis->Tag2))$currThesis->tags[] = array('tag'=>$rowThesis->Tag2);
        //var_dump($currThesis);
        //exit();
        $keyThesis = postThesis($currThesis, $rowThesis->Note);
        if(is_null($keyThesis)) continue;

        //Add Note
        if(!empty($rowThesis->Note) ){
            $currNote = clone $tmplNote;
            $currNote->note = "<p>{$rowThesis->Note}</p>";
            $currNote->parentItem = $keyThesis;
            if(!postNote($currNote)) continue;
        }

        $stmtSetInserted->execute(array($rowThesis->id));


    }

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
function postNote($note){
    global $config;
    $APIKEY = $config['zotero']['apikey']; //'S321LmGUjscgc2ZfDMmkb9Nh';
    $APIURL = $config['zotero']['apiurl'];
    $ZOTGRP = '1512203';//'1510088';

    $uriPostNote = "$APIURL/groups/$ZOTGRP/items";
    $jsonNote = '['.json_encode($note).']';
    echo "Posting note...";
    $resPostNote = Httpful\Request::post($uriPostNote)->addHeader('Authorization', "Bearer $APIKEY")->body($jsonNote)->sendsJson()->send();
    //var_dump($resPostThesis);

    if($resPostNote->code==200 && count(get_object_vars($resPostNote->body->success)) ==1){
        echo "OK\n";
        return true;
    }else{
        echo "Error: Code {$resPostNote->code}\n";
        return false;
    }

}
function postThesis($thesis){
    global $config;
    $APIKEY = 'S321LmGUjscgc2ZfDMmkb9Nh';//$config['zotero']['apikey'];
    $APIURL = $config['zotero']['apiurl'];
    $ZOTGRP = '1512203';//'1510088';

    $uriPostThesis = "$APIURL/groups/$ZOTGRP/items";
    $jsonThesis = '['.json_encode($thesis).']';
    echo "Posting thesis {$thesis->title}...";
    $resPostThesis = Httpful\Request::post($uriPostThesis)->addHeader('Authorization', "Bearer $APIKEY")->body($jsonThesis)->sendsJson()->send();
    //var_dump($resPostThesis);

    if($resPostThesis->code==200 && count(get_object_vars($resPostThesis->body->success)) ==1){
        $keyThesis = get_object_vars($resPostThesis->body->success)[0];
        echo "OK, Key $keyThesis\n";
        return $keyThesis;

    }

    echo "Error: Code {$resPostThesis->code}\n";
    return null;
}

thesis2zotero();

?>
