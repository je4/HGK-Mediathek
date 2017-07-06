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
 * @subpackage  NebisImport
 * @author      Juergen Enge (juergen@info-age.net)
 * @copyright   (C) 2016 Academy of Art and Design FHNW
 * @license     http://www.gnu.org/licenses/gpl-3.0
 * @link        http://mediathek.fhnw.ch
 *
 */

/**
 * @namespace
 */

namespace Mediathek;

use \Mediathek\Zotero\Item;

/**
 * Handling of MARC Data from Nebis
 *
 */

class zoteroEntity extends SOLRSource {

    private $idprefix = 'zotero';
    private $db = null;
    private $item = null;
    private $tags = array();
    private $cluster = array();



    function __construct( \ADOConnection $db ) {
        $this->db = $db;
    }

    public function loadFromDoc( $doc) {
    	$this->data = json_decode( gzdecode( base64_decode( $doc->metagz )), true);
    	$this->id = $doc->originalid;
      $this->item = new Item( $this->data );
    }

    public function reset() {
      parent::reset();
      $this->data = null;
      $this->id = null;
      $this->item = null;
      $this->tags = array();
      $this->cluster = array();
    }

    public function loadFromArray( $data ) {
      $this->data = $data;
      $this->item = new Item( $this->data );
      $this->id =  $this->item->getGroupId().'.'.$this->item->getKey();
    }

    public function getItem() {
      return $this->item;
    }

    public function getData() {
        return $this->data;
    }

    public function getID() {
        return $this->idprefix.'-'.$this->id;
    }

	public function getOriginalID() {
		return $this->id;
	}

    public function getType() {
		return $this->item->getType();
	}

    public function getEmbedded() {
		return true;
	}

    public function getSource() {
        return 'zotero';
    }

	public function getLocations() {
		return array( 'zotero:online' );
	}

	public function getOpenAccess() {
    if( $this->item == null ) throw new \Exception( "no entity loaded" );
    foreach( $this->item->getRights() as $right ) {
      if( preg_match( '/cc/i', $right )) return true;
      if( preg_match( '/creativecommons/i', $right )) return true;
      if( preg_match( '/openaccess/i', $right )) return true;
    }
    return false;
	}

    public function getTitle() {
        if( $this->item == null ) throw new \Exception( "no entity loaded" );
        return $this->item->getTitle();
    }

    public function getPublisher() {
      if( $this->item == null ) throw new \Exception( "no entity loaded" );
      return $this->item->getPublisher();
    }

    public function getCodes() {
      if( $this->item == null ) throw new \Exception( "no entity loaded" );
      $codes = array();
      if( $this->item->getISBN()) $codes[] = 'ISBN:'.$this->item->getISBN();

      foreach( $this->item->getTags() as $tag ) {
        if( preg_match( '/^(doi|isbn|issn):/i', $tag )) {
          $codes[] = $tag;
        }
      }

		  return $codes;
    }

    public function getYear() {
        return $this->item->getYear();
    }

    public function getCity() {
        return $this->item->getPlace();
    }

	   public function getTags() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );

        //$this->tags = array('index:type/'.md5( $this->item->getType() ).'/'.$this->item->getType() );

        foreach( $this->item->getTags() as $tag ) {
          if( !preg_match( '/^(signature|barcode):[^:]+:[^:]+:/i', $tag )
            && !preg_match( '/^(doi|isbn|issn|aclmeta|aclcontent|aclpreview|catalog):/i', $tag )) {
            if( preg_match( '/^[^:]+:[^\/]+\/[^\/]+\//', $tag)) {
              $this->tags[] = $tag;
            }
            elseif( preg_match( '/^([^:\/]+)(:|\/)([^:\/]+)(:|\/)(.*)$/', $tag, $matches )) {
              $this->tags[] = $matches[1].':'.$matches[3].'/'.md5( $matches[5] ).'/'.$matches[5];
            }
            elseif( preg_match( '/^([^:\/]+)(:|\/)(.*)$/', $tag, $matches )) {
              $this->tags[] = $matches[1].':unknown/'.md5( $matches[3] ).'/'.$matches[3];
            }
            else {
              $this->tags[] = 'index:unknown/'.md5( $tag ).'/'.$tag;
            }

          }
        }
        foreach( $this->item->getCollections() as $coll ) {
          $tag = $coll->getFullName();
          $this->tags[] = 'coll:unknown/'.md5( $tag ).'/'.$tag;

          foreach( $coll->getNames() as $tag ) {
            $this->tags[] = 'subcoll:unknown/'.md5( $tag ).'/'.$tag;
          }
        }

        $this->tags = array_unique( $this->tags );
        $this->cluster = array();
        foreach( $this->tags as $tag ) {
              $ts = explode( '/', $tag );
              if( !in_array( $ts[0], array( 'coll:unknown' ))) {
                $this->cluster[] = $ts[count( $ts )-1];
              }
        }
        $this->cluster = array_unique( $this->cluster );

        return $this->tags;
	}


    public function getCluster() {
        if( $this->cluster == null) $this->getTags();
        return $this->cluster;
    }

    public function getSignatures() {
        if( $this->item == null ) throw new \Exception( "no entity loaded" );

        $sigs = array();
        foreach( $this->item->getTags() as $tag ) {
          if( preg_match( '/^(signature|barcode):[^:]+:[^:]+:/i', $tag )) {
            $sigs[] = $tag;
          }
        }
        return $sigs;
    }

    public function getAuthors() {
      if( $this->item == null ) throw new \Exception( "no entity loaded" );
        $authors = array();
        foreach( $this->item->getCreators()  as $creator ) {
            $authors[] = (string)$creator;
        }

		    return $authors;
    }

    public function getLoans() {
        return array();
    }

    public function getLicenses() {
      if( $this->item == null ) throw new \Exception( "no entity loaded" );
		  return $this->item->getRights();
    }

    public function getURLs() {
      if( $this->item == null ) throw new \Exception( "no entity loaded" );
      return $this->item->getUrls();
    }

    public function getSys() {
        return $this->id;
    }

    public function getMeta() {
      $json = json_encode( $this->data );

      $err = json_last_error();
      switch($err) {
        case JSON_ERROR_NONE:
        break;
        case JSON_ERROR_DEPTH:
            echo $err.' - Maximale Stacktiefe überschritten';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo $err.' - Unterlauf oder Nichtübereinstimmung der Modi';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo $err.' - Unerwartetes Steuerzeichen gefunden';
        break;
        case JSON_ERROR_SYNTAX:
            echo $err.' - Syntaxfehler, ungültiges JSON';
        break;
        case JSON_ERROR_UTF8:
            echo $err.' - Missgestaltete UTF-8 Zeichen, möglicherweise fehlerhaft kodiert';
        break;
        default:
            echo $err.' - Unbekannter Fehler';
        break;
    }

        return $json;
    }

    public function getOnline() {
      if( $this->item == null ) throw new \Exception( "no entity loaded" );
      return $this->item->getUrl() != null;
    }

   public function getAbstract() {
     if( $this->item == null ) throw new \Exception( "no entity loaded" );
      return $this->item->getAbstract();
    }

   public function getContent() {
     return $this->item->getFulltext();
   }

    public function getMetaACL() {
      if( $this->item == null ) throw new \Exception( "no entity loaded" );
      $acls = array();
      foreach( $this->item->getTags() as $tag ) {
        if( preg_match( '/^(aclmeta):([^\/]+)\/([^\/]+)$/i', $tag, $matches )) {
          $acls[] = $matches[2].'/'.$matches[3];
        }
      }
      if( count( $acls ) == 0 ) $acls[] = 'global/guest';

		  return $acls;
    }

    public function getContentACL() {
      if( $this->item == null ) throw new \Exception( "no entity loaded" );
      $acls = array();
      foreach( $this->item->getTags() as $tag ) {
        if( preg_match( '/^(aclcontent):([^\/]+)\/([^\/]+)$/i', $tag, $matches )) {
          $acls[] = $matches[2].'/'.$matches[3];
        }
      }
      if( count( $acls ) == 0 ) $acls[] = 'global/guest';

		  return $acls;
    }

    public function getPreviewACL() {
      if( $this->item == null ) throw new \Exception( "no entity loaded" );
      $acls = array();
      foreach( $this->item->getTags() as $tag ) {
        if( preg_match( '/^(aclpreview):([^\/]+)\/([^\/]+)$/i', $tag, $matches )) {
          $acls[] = $matches[2].'/'.$matches[3];
        }
      }
      if( count( $acls ) == 0 ) $acls[] = 'global/guest';

		  return $acls;
    }

    public function getLanguages() {
      return array();
    }

    public function getIssues() { return array( ); }

	public function getCategories() {
		$categories = parent::getCategories();
    foreach( $this->item->getCollections() as $coll ) {
      $categories[] = 'fhnw!!hgk!!pub!!'.str_replace( ':', '!!', $coll->getFullName() );
    }
		return $categories;
	}

	public function getCatalogs() {
    $cats = array( $this->getSource());
    foreach( $this->item->getTags() as $tag ) {
      if( preg_match( '/^(catalog):(.*)$/i', $tag, $matches )) {
        $cats[] = $matches[2];
      }
    }
    return $cats;
  }

  public function getCSL() {
    return $this->item->getCSL();
  }

}

?>
