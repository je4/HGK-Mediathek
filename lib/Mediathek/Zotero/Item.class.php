<?php
/**
 * This file is part of MediathekMiner.
 * MediathekMiner is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * Foobar is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details
 *
 * You should have received a copy of the GNU General Public License along with Foobar. If not, see http://www.gnu.org/licenses/.
 *
 *
 * @package     Mediathek
 * @subpackage  Zotero
 * @author      Juergen Enge (juergen@info-age.net)
 * @copyright   (C) 2016 Academy of Art and Design FHNW
 * @license     http://www.gnu.org/licenses/gpl-3.0
 * @link        http://mediathek.fhnw.ch
 *
 */

/**
 * @namespace
 */
namespace Mediathek\Zotero;

class Item {
  var $parent = null;
  var $trash = false;
  var $children = array();
  var $collections = array();
  var $library = null;
  var $vars = null;

  function __construct( $data, $trash = false ) {
    $this->data = $data;
    $this->trash = $trash;
    if( @is_array( $this->data['children'] )) {
      foreach( $this->data['children'] as $d ) {
        $this->addChild( new Item( $d ));
      }
    }
    if( @is_array( $this->data['collections'] )) {
      foreach( $this->data['collections'] as $d ) {
        $coll = new Collection( $d );
        $this->addCollection( $coll );
      }
    }
    if( @is_array( $this->data['group'] )) {
      $this->library = new Library( $this->data['group'] );
    }
    // kill nebis grabbed items
    if( array_key_exists( 'url', $this->data['data'] ) ) {
      if( preg_match( '@://[^/]*nebis.ch/@', $this->data['data']['url'] )) unset( $this->data['data']['url'] );
    }
  }

  function __toString() {
    $str = $this->getLibraryId().':'.$this->getKey().' ['.$this->getType().'] - '.$this->getTitle()."\n";
    foreach( $this->children as $child ) {
      $str .= '   '.$child;
    }
    return $str;
  }

  public function getVar( $key ) {
    if( $this->vars == null ) {
      $this->vars = array();
      foreach( $this->getTags() as $tag ) {
        if( preg_match( '/([a-z0-9_.]+):([a-z0-9_\/\-+.: ]+)/i', $tag, $match )) {
          if( !array_key_exists( $match[1], $this->vars )) $this->vars[$match[1]] = array();
          $this->vars[$match[1]][] = $match[2];
        }
      }
    }
    return array_key_exists( $key, $this->vars ) ? $this->vars[$key] : array();
  }

  public function isTrashed() {
    return $this->trash;
  }

  public function getData() {
    if( count( $this->children )) {
      $this->data['children'] = array();
      foreach( $this->children as $child ) {
        $this->data['children'][] = $child->getData();
      }
    }
    if( count( $this->collections )) {
      $this->data['collections'] = array();
      foreach( $this->collections as $coll ) {
        $this->data['collections'][] = $coll->getData();
      }
    }
    return $this->data;
  }

  public function getLibraryId() {
    if( $this->library )
      return $this->library->getId();
    return $this->data['library']['id'];
  }

  public function getLibraryName() {
    if( $this->library )
      return $this->library->getName();
    return $this->data['library']['name'];
  }

  public function getLibrary() {
    return $this->library;
  }

  public function getKey() {
    return $this->data['key'];
  }

  public function getType() {
	  if( @strlen($this->data['data']['videoRecordingFormat']) > 0 ) {
		  return $this->data['data']['videoRecordingFormat'];
	  }
	  if( @strlen($this->data['data']['audioRecordingFormat']) > 0 ) {
		  return $this->data['data']['audioRecordingFormat'];
	  }
    return $this->data['data']['itemType'];
  }

  public function getNote() {
    return $this->data['data']['note'];
  }

  public function getMediaTypes() {
	 $mediaTypes = [];
	 if( is_array( $this->data['children'] )) foreach( $this->data['children'] as $child ) {
		 if( isset( $child['data']['media']['metadata']['type'] )) {
			 $mediaTypes[] = $child['data']['media']['metadata']['type'];
		 }
	 }
    return array_unique( $mediaTypes );
  }

  public function getTitle() {
    return array_key_exists( 'title', $this->data['data'] ) ? $this->data['data']['title'] : '';
  }

  public function getPublisher() {
    return array_key_exists( 'publisher', $this->data['data'] ) ? $this->data['data']['publisher'] : null;
  }

  public function getISBN() {
    return array_key_exists( 'ISBN', $this->data['data'] ) ? $this->data['data']['ISBN'] : null;
  }

  public function getPages() {
    return array_key_exists( 'pages', $this->data['data'] ) ? $this->data['data']['pages'] : 0;
  }

  public function getLibraryCatalog() {
    $cat = array_key_exists( 'libraryCatalog', $this->data['data'] ) ? $this->data['data']['libraryCatalog'] : '';
    if( !strlen( $cat ) && $this->library ) {
      $ret = $this->library->getVar( 'library' );
      if( count( $ret )) return $ret[0];
    }
    return $cat;
  }

  public function getCreators() {
    if( !array_key_exists( 'creators', $this->data['data'] )) return array();
    $cs = array();
    foreach( $this->data['data']['creators'] as $c ) {
      $creator = new Creator( $c );
      $cs[] = $creator;
    }

    return $cs;
  }

  public function getTags() {
    if( !array_key_exists( 'tags', $this->data['data'] )) return array();

    $tags = array();
    foreach( $this->data['data']['tags'] as $tag ) {
      if( array_key_exists( 'tag', $tag )) $tags[] = $tag['tag'];
    }
    return $tags;
  }

  public function getYear() {
    //print_r($this->data['meta']['parsedDate'] );
    if( array_key_exists( 'parsedDate', $this->data['meta'] )) {
    	$matches = [];
    	if( preg_match( '/^([0-9]{4}).*$/', $this->data['meta']['parsedDate'], $matches )) {
    	   return intval( $matches[1] );
    	}
    } else {
    	return null; 
    }
 
    return intval( $this->data['meta']['parsedDate'] );
  }

  public function getPlace() {
    return array_key_exists( 'place', $this->data['data'] ) ? $this->data['data']['place'] : null;
  }

  public function getRights() {
    return array_key_exists( 'rights', $this->data['data'] ) ? array( $this->data['data']['rights'] ) : array();
  }

  public function getLanguage() {
    return array_key_exists( 'language', $this->data['data'] ) ? $this->data['data']['language'] : null;
  }

  public function getAccessDate() {
    return array_key_exists( 'accessDate', $this->data['data'] ) ? new \DateTime( $this->data['data']['accessDate'] ) : null;
  }

  public function getUrl() {
    if( array_key_exists( 'url', $this->data['data'] ) ) {
      if( !preg_match( '/[a-zA-Z0-9]+:/', $this->data['data']['url'] )) return null;
      return $this->data['data']['url'];
    }
    return null;
  }

  public function getContentType() {
    return array_key_exists( 'contentType', $this->data['data'] ) ? $this->data['data']['contentType'] : null;
  }

  public function getUrlMimetype() {
    return array_key_exists( 'urlMimetype', $this->data['data'] ) ? $this->data['data']['urlMimetype'] : null;
  }

  public function getNameImageserver() {
    return array_key_exists( 'nameImageserver', $this->data['data'] ) ? $this->data['data']['nameImageserver'] : null;
  }

  public function get($key) {
    return array_key_exists( $key, $this->data['data'] ) ? $this->data['data'][$key] : null;
  }

  public function getLinkMode() {
    return array_key_exists( 'linkMode', $this->data['data'] ) ? $this->data['data']['linkMode'] : null;
  }

    public function getFilename() {
      return array_key_exists( 'filename', $this->data['data'] ) ? $this->data['data']['filename'] : null;
    }

  public function getFulltext() {
    $ret = array_key_exists( 'fulltext', $this->data ) ? "{$this->data['fulltext']}" : '';
    foreach( $this->getChildren() as $child ) {
      $ret .= "\n".$child->getFulltext();
    }
    return $ret;
  }

  public function getUrls() {
    $urls = array();
    if( $this->getUrl()) $urls[] = $this->getUrl();
    foreach( $this->children as $child ) {
      $urls = array_merge( $urls, $child->getUrls());
    }
    return $urls;
  }

  public function getAbstract() {
    $abstract = array_key_exists( 'abstractNote', $this->data['data'] ) ? $this->data['data']['abstractNote'] : '';
    if( $this->getUrl()) $urls[] = $this->getUrl();
    foreach( $this->children as $child ) {
      $abstract .= "\n".$child->getAbstract();
    }
    return trim( $abstract );
  }

  public function getCollectionKeys() {
    if( !array_key_exists( 'collections', $this->data['data'])) return array();
    return $this->data['data']['collections'];
  }

  public function getCollections() {
    return $this->collections;
  }

  public function numChildren() {
    if( !array_key_exists( 'numChildren', $this->data['meta'])) return 0;
    return intval( $this->data['meta']['numChildren'] );
  }

  public function getMedia() {
    if( !array_key_exists( 'media', $this->data['data'])) return [];
    return $this->data['data']['media'];
  }

  public function addChild( $child ) {
      $this->children[] = $child;
  }

  public function addCollection( $coll ) {
      $this->collections[] = $coll;
  }

  public function getChildren() {
    return $this->children;
  }

  public function getChild( $key ) {
    foreach( $this->children as $child ) {
      if( $child->getKey() == $key ) {
        return $child;
      }
    }
    return null;
  }

  public function getNotes() {
    $notes = array();
    foreach( $this->children as $child ) {
      if( $child->getType() == 'note' ) $notes[] = $child;
    }
    return $notes;
  }

  public function getImages() {
    return $this->getAttachments( array( '/image\//' ));
  }

  public function getPDFs() {
    return $this->getAttachments( array( '/application\/pdf/' ));
  }
  public function getAttachments( $types = array()) {
    $attachments = array();
    foreach( $this->children as $child ) {
      if( $child->getType() == 'attachment' ) {
        $t = $child->getContentType();
        if( $t == null ) $t = $child->getLinkMode();
        if( count( $types ) == 0 ) $attachments[] = $child;
        else foreach( $types as $type ) {
          if( preg_match( $type, $t )) $attachments[] = $child;
        }
      }
    }
    return $attachments;
  }

  public function getRelations() {
    $ret = [];
    if( array_key_exists( 'relations', $this->data['data'] )) {
      foreach( $this->data['data']['relations'] as $key=>$rel ) {
        if( is_array( $rel )) {
          foreach( $rel as $val ) {
            $ret[] = "{$key}:{$val}";
          }
        } else {
			$ret[] = "{$key}:{$rel}";
 	    }
	  }
	  return $ret;
	}
  }


    /**
  	 * Mappings for names
  	 * Note that this is the reverse of the text variable map, since all mappings should be one to one
  	 * and it makes the code cleaner
  	 */
  	private static $zoteroNameMap = array(
      "author" => "author",
      "director" => "author",
  		"editor" => "editor",
  		"bookAuthor" => "container-author",
  		"composer" => "composer",
  		"interviewer" => "interviewer",
  		"recipient" => "recipient",
  		"seriesEditor" => "collection-editor",
  		"translator" => "translator"
  	);

  	/**
  	 * Mappings for text variables
  	 */
  	private static $zoteroFieldMap = array(
  		"title" => array("title"),
  		"container-title" => array("publicationTitle", "bookTitle",  "reporter", "code"), /* reporter and code should move to SQL mapping tables */
  		"collection-title" => array("seriesTitle", "series"),
  		"collection-number" => array("seriesNumber"),
  		"publisher" => array("publisher", "distributor"), /* distributor should move to SQL mapping tables */
  		"publisher-place" => array("place"),
  		"authority" => array("court"),
  		"page" => array("pages"),
  		"volume" => array("volume"),
  		"issue" => array("issue"),
  		"number-of-volumes" => array("numberOfVolumes"),
  		"number-of-pages" => array("numPages"),
  		"edition" => array("edition"),
  		"version" => array("versionNumber"),
  		"section" => array("section"),
  		"genre" => array("type", "artworkSize"), /* artworkSize should move to SQL mapping tables, or added as a CSL variable */
  		"medium" => array("medium", "system"),
  		"archive" => array("archive"),
  		"archive_location" => array("archiveLocation"),
  		"event" => array("meetingName", "conferenceName"), /* these should be mapped to the same base field in SQL mapping tables */
  		"event-place" => array("place"),
  		"abstract" => array("abstractNote"),
  		"URL" => array("url"),
  		"DOI" => array("DOI"),
  		"ISBN" => array("ISBN"),
  		"call-number" => array("callNumber"),
  		"note" => array("extra"),
  		"number" => array("number"),
  		"references" => array("history"),
  		"shortTitle" => array("shortTitle"),
  		"journalAbbreviation" => array("journalAbbreviation"),
  		"language" => array("language")
  	);

  	private static $zoteroDateMap = array(
  		"issued" => "date",
  		"accessed" => "accessDate"
  	);

  	private static $zoteroTypeMap = array(
  		'book' => "book",
  		'bookSection' => "chapter",
  		'journalArticle' => "article-journal",
  		'magazineArticle' => "article-magazine",
  		'newspaperArticle' => "article-newspaper",
  		'thesis' => "thesis",
  		'encyclopediaArticle' => "entry-encyclopedia",
  		'dictionaryEntry' => "entry-dictionary",
  		'conferencePaper' => "paper-conference",
  		'letter' => "personal_communication",
  		'manuscript' => "manuscript",
  		'interview' => "interview",
  		'film' => "motion_picture",
  		'artwork' => "graphic",
  		'webpage' => "webpage",
  		'report' => "report",
  		'bill' => "bill",
  		'case' => "legal_case",
  		'hearing' => "bill",				// ??
  		'patent' => "patent",
  		'statute' => "bill",				// ??
  		'email' => "personal_communication",
  		'map' => "map",
  		'blogPost' => "webpage",
  		'instantMessage' => "personal_communication",
  		'forumPost' => "webpage",
  		'audioRecording' => "song",		// ??
  		'presentation' => "speech",
  		'videoRecording' => "motion_picture",
  		'tvBroadcast' => "broadcast",
  		'radioBroadcast' => "broadcast",
  		'podcast' => "song",			// ??
  		'computerProgram' => "book"		// ??
  	);

    private static $citePaperJournalArticleURL=false;

    private static $quotedRegexp = '/^".+"$/';


    public function getCSL() {
      $itemType = $this->getType();
  		$cslType = isset(self::$zoteroTypeMap[$itemType]) ? self::$zoteroTypeMap[$itemType] : false;
  		if (!$cslType) $cslType = "article";
      $ignoreURL = (($this->getAccessDate() || $this->getUrl()) &&
      				in_array($itemType, array("journalArticle", "newspaperArticle", "magazineArticle"))
      				/* && $zoteroItem->getField("pages", false, false, true) */
      				&& self::$citePaperJournalArticleURL);

      $cslItem = array(
      			'id' => $this->getLibraryId() . "/" . $this->getKey(),
      			'type' => $cslType
      		);

      // get all text variables (there must be a better way)
  		// TODO: does citeproc-js permit short forms?
  		foreach (self::$zoteroFieldMap as $variable=>$fields) {
  			if ($variable == "URL" && $ignoreURL) continue;

//        echo "{$variable}\n";
  			foreach($fields as $field) {
  				$value = array_key_exists( $field, $this->data['data'] ) ? trim( $this->data['data'][$field] ) : '';
  				if ($value !== '') {
  					// Strip enclosing quotes
  					if (preg_match(self::$quotedRegexp, $value)) {
  						$value = substr($value, 1, strlen($value)-2);
  					}
  					$cslItem[$variable] = $value;
  					break;
  				}
  			}
  		}

      // separate name variables
  		//$authorID = Zotero_CreatorTypes::getPrimaryIDForType($zoteroItem->itemTypeID);
      if( array_key_exists( 'creators', $this->data['data'] )) {
        foreach( $this->data['data']['creators'] as $creator) {

  				$creatorType = $creator['creatorType'];

    			$creatorType = isset(self::$zoteroNameMap[$creatorType]) ? self::$zoteroNameMap[$creatorType] : false;
    			if (!$creatorType) continue;

          if( array_key_exists('lastName', $creator )) $nameObj = array('family' => $creator['lastName'], 'given' => $creator['firstName']);
          else $nameObj = array('family' => $creator['name'] );

    			if (isset($cslItem[$creatorType])) {
    				$cslItem[$creatorType][] = (object)$nameObj;
    			}
    			else {
    				$cslItem[$creatorType] = array((object)$nameObj);
    			}
    		}
      }


      // get date variables
  		foreach (self::$zoteroDateMap as $key=>$val) {
        if( array_key_exists( $val, $this->data['data'] )) {
          $t = trim( $this->data['data'][$val] );
          if( !strlen( $t )) continue;
          if( preg_match( '/^([0-9]{4})$/', $t, $matches )) {
              $cslItem[$key] = (object)array("date-parts" => array(array($matches[1] )));
          }
          elseif( preg_match( '/^([0-9]{1,2})\.([0-9]{4})$/', $t, $matches )) {
              $cslItem[$key] = (object)array("date-parts" => array(array($matches[2], $matches[1] )));
          }
          elseif( preg_match( '/^([0-9]{4)-([0-9]{1,2})$/', $t, $matches )) {
              $cslItem[$key] = (object)array("date-parts" => array(array($matches[1], $matches[2] )));
          }
          else {
            $dt = null;
            try {
              $dt = new \DateTime( $t );
              $cslItem[$key] = (object)array("date-parts" => array( array( $dt->format( 'Y' ), $dt->format( 'm' ), $dt->format( 'd' ))));
            }
            catch( \Exception $ex ) {
            }
          }
        }
  		}

      return $cslItem;
    }


}
