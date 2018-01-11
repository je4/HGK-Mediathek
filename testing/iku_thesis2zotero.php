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

    //Get Zotero Attachment Link template
    $uriGetTemplateLinkAttch = "$APIURL/items/new?itemType=attachment&linkMode=linked_url";
    $resGetTemplateLinkAttch = Httpful\Request::get($uriGetTemplateLinkAttch)->send();
    if($resGetTemplateLinkAttch->code!=200){
        echo "Zotero note template not loaded";
        die();
    }
    $tmplLinkAttch = $resGetTemplateLinkAttch->body;

    //Get Zotero blogPost template
    $uriGetTemplateBlogPost = "$APIURL/items/new?itemType=blogPost";
    $resGetTemplateBlogPost = Httpful\Request::get($uriGetTemplateBlogPost)->send();
    if($resGetTemplateNote->code!=200){
        echo "Zotero note template not loaded";
        die();
    }
    $tmplBlogPost = $resGetTemplateBlogPost->body;

    //Get Zotero webpage template
    $uriGetTemplateWebpage = "$APIURL/items/new?itemType=webpage";
    $resGetTemplateWebpage = Httpful\Request::get($uriGetTemplateWebpage)->send();
    if($resGetTemplateWebpage->code!=200){
        echo "Zotero note template not loaded";
        die();
    }
    $tmplWebpage = $resGetTemplateWebpage->body;

    //Get Zotero thesis template
    $uriGetTemplateThesis = "$APIURL/items/new?itemType=thesis";
    $resGetTemplateThesis = Httpful\Request::get($uriGetTemplateThesis)->send();
    if($resGetTemplateThesis->code!=200){
        echo "Zotero template not loaded";
        die();
    }
    $tmplThesis = $resGetTemplateThesis->body;

    //Get Zotero video recording template
    $uriGetTemplateVideo = "$APIURL/items/new?itemType=videoRecording";
    $resGetTemplateVideo = Httpful\Request::get($uriGetTemplateVideo)->send();
    if($resGetTemplateVideo->code!=200){
        echo "Zotero template not loaded";
        die();
    }
    $tmplVideo = $resGetTemplateVideo->body;

    //Set template standard values
    $tmplThesis->university = 'HGK FHNW';
    $tmplThesis->place = 'Basel';
    $tmplThesis->rights = 'internal use only';

    $tmplThesis->creators = array();
    $tmplBlogPost->creators = array();
    $tmplWebpage->creators = array();
    $tmplVideo->creators = array();

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

        switch ($rowThesis->ItemType){
            case "thesis":
                $objZotero = clone $tmplThesis;
                break;
            case "blogPost":
                $objZotero = clone $tmplBlogPost;
                break;
            case "webpage":
                $objZotero = clone $tmplWebpage;
                break;
            case "videoRecording":
                $objZotero = clone $tmplVideo;
                break;
            default:
                echo "Unknown Item Type: {$rowThesis->ItemType}";
                continue;
        }

        if (in_array($rowThesis->ItemType, ['thesis','blogPost','webpage','videoRecording'])){
            $objZotero->title = $rowThesis->Title;
            $authors = getCreators('author', $rowThesis->Autor);
            foreach ($authors as $author) {
                $objZotero->creators[] = $author;
            }
            $objZotero->date = $rowThesis->Date;
            $objZotero->extra = $rowThesis->Extra;
            $objZotero->url = $rowThesis->URL;
            $objZotero->callNumber = $rowThesis->CallNumber;
            if(!empty($rowThesis->Tag1))$objZotero->tags[] = array('tag'=>$rowThesis->Tag1);
            if(!empty($rowThesis->Tag2))$objZotero->tags[] = array('tag'=>$rowThesis->Tag2);
        }
        if (in_array($rowThesis->ItemType, ['thesis'])){
            $contributors = getCreators('contributor', $rowThesis->Contributor);
            foreach ($contributors as $contributor){
                $objZotero->creators[] = $contributor;
            }


        }
        if (in_array($rowThesis->ItemType, ['videoRecording'])){
            $objZotero->creators = array();
            $authors = getCreators('director', $rowThesis->Autor);
            foreach ($authors as $author) {
                $objZotero->creators[] = $author;
            }
            $contributors = getCreators('castMember', $rowThesis->Contributor);
            foreach ($contributors as $contributor) {
                $objZotero->creators[] = $contributor;
            }
            $objZotero->url = $rowThesis->Filename;
            $objZotero->abstractNote = $rowThesis->Abstract;
            $objZotero->seriesTitle = $rowThesis->SeriesTitle;
            $objZotero->place = $rowThesis->Place;
            $objZotero->runningTime = $rowThesis->RunningTime;
            $objZotero->libraryCatalog = $rowThesis->LibraryCatalog;
        }




        //var_dump($objZotero);
        //exit();
        $keyThesis = postObject($objZotero);
        if(is_null($keyThesis)) continue;

        //Add Note
        if(!empty($rowThesis->Note) ){
            $currNote = clone $tmplNote;
            $currNote->note = "<p>{$rowThesis->Note}</p>";
            $currNote->parentItem = $keyThesis;
            if(!postNote($currNote)) continue;
        }
        for ($i = 1; $i < 10; $i++) {
            if(array_key_exists("Note$i",$rowThesis)){
                if(!empty($rowThesis->{'Note'.$i}) ){
                    $currNote = clone $tmplNote;
                    $currNote->note = "<p>0$i: {$rowThesis->{'Note'.$i}}</p>";
                    $currNote->parentItem = $keyThesis;
                    if(!postNote($currNote)) continue;
                }
            }
        }

        //Add Link Attachment
        if(!empty($rowThesis->Attachment) ){
            $currLinkAttch = clone $tmplLinkAttch;
            $currLinkAttch->title = "Videolink";
            $currLinkAttch->url = $rowThesis->Attachment;
            $currLinkAttch->parentItem = $keyThesis;
            if(!postNote($currLinkAttch)) continue;
        }

        $stmtSetInserted->execute(array($rowThesis->id));


    }

}

//Returns a generator object with all the creators of the type, like 'author', 'contributor'
function getCreators($creatorType, $name){

    if(empty($name)) return null;
    $name = rtrim($name,';');
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
    //var_dump($resPostNote);

    if($resPostNote->code==200 && count(get_object_vars($resPostNote->body->success)) ==1){
        echo "OK\n";
        return true;
    }else{
        echo "Error: Code {$resPostNote->code}\n";
        return false;
    }

}
function postObject($thesis){
    //var_dump($thesis);
    global $config;
    $APIKEY = 'S321LmGUjscgc2ZfDMmkb9Nh';//$config['zotero']['apikey'];
    $APIURL = $config['zotero']['apiurl'];
    $ZOTGRP = '1512203';//'1510088';

    $uriPostThesis = "$APIURL/groups/$ZOTGRP/items";
    $jsonThesis = '['.json_encode($thesis).']';
    echo "Posting object {$thesis->title}...";
    $resPostThesis = Httpful\Request::post($uriPostThesis)->addHeader('Authorization', "Bearer $APIKEY")->body($jsonThesis)->sendsJson()->send();
    //var_dump($resPostThesis);
    //exit();
    if($resPostThesis->code==200 && count(get_object_vars($resPostThesis->body->success)) ==1){
        $keyThesis = get_object_vars($resPostThesis->body->success)[0];
        echo "OK, Key $keyThesis\n";
        return $keyThesis;
    }

    echo "Error: {$resPostThesis->body->failed->{"0"}->message}\n";
    echo "\n";

//    var_dump($resPostThesis->body);
//    var_dump($thesis);
//    exit();
    return null;
}

thesis2zotero();

?>
