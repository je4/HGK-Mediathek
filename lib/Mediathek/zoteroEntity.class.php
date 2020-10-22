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
      $this->id =  $this->item->getLibraryId().'.'.$this->item->getKey();
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

		$tags = [];

        //$tags = array('index:type/'.md5( $this->item->getType() ).'/'.$this->item->getType() );

        $tags = $this->item->getLibrary()->getVar( 'tag' );
        $tags = array_merge( $this->tags, $tags);


        foreach( $this->item->getTags() as $tag ) {
        echo "tag:{$tag}\n";
          if( !preg_match( '/^(signature|barcode):[^:]+:[^:]+:/i', $tag )
            && !preg_match( '/^(doi|isbn|issn|acl_meta|acl_content|acl_preview|catalog):/i', $tag )) {
            if( preg_match( '/^[^:]+:[^\/]+\/[^\/]+\//', $tag)) {
              $tags[] = $tag;
            }
            elseif( preg_match( '/^([^:\/]+)(:|\/)([^:\/]+)(:|\/)(.*)$/', $tag, $matches )) {
              $tags[] = $matches[1].':'.$matches[3].'/'.md5( $matches[5] ).'/'.$matches[5];
            }
            elseif( preg_match( '/^([^:\/]+)(:|\/)(.*)$/', $tag, $matches )) {
              //$tags[] = $matches[1].':unknown/'.md5( $matches[3] ).'/'.$matches[3];
              $tags[] = $tag;
            }
            else {
              //$tags[] = 'index:unknown/'.md5( $tag ).'/'.$tag;
              $tags[] = $tag;
            }

          }
        }

        //$tags[] = 'index:unknown/'.md5( $this->item->getLibraryName() ).'/'.$this->item->getLibraryName();
        $tags[] = 'Collection:'.$this->item->getLibraryName();
        $tags[] = $this->item->getLibraryName();

        foreach( $this->item->getCollections() as $coll ) {
          $tag = $coll->getFullName();
          //$tags[] = 'coll:unknown/'.md5( $tag ).'/'.$tag;
          $tags[] = 'Path:'.$tag;

          foreach( $coll->getNames() as $tag ) {
           // $tags[] = 'subcoll:unknown/'.md5( $tag ).'/'.$tag;
           $tags[] = 'subcoll:'.$tag;
          }
		}
        $tags = array_map('strtolower', $tags);
        $tags = array_unique( $tags );
		foreach( $tags as $tag ) {
		  $this->tags[] = $tag;
          $pp = explode( ':', $tag );
          $lastIndex = count($pp)-1;
          if( $lastIndex > 0 ) {
             for( $i = $lastIndex; $i > 0; $i-- ) {
                $this->tags[] = implode(':', array_slice($pp, 1, $i));
                $this->tags[] = $pp[$i];
             }
          }
		}
        $this->tags = array_unique( $this->tags );
	
        $this->cluster = array();
        foreach( $this->tags as $tag ) {
              $ts = explode( '/', $tag );
//              if( !in_array( $ts[0], array( 'coll:unknown' ))) {
//                $this->cluster[] = $ts[count( $ts )-1];
//              }
              if( preg_match( '/^[^:]+:(.+)$/', $tag, $matches )) {
                 $ts = explode(':', $matches[1]);
                 $this->cluster = array_merge($this->cluster, $ts);
              }
        }
        $this->cluster = array_unique( $this->cluster );

        if( count($this->tags) > 1 ) {
          var_dump( $this->tags );
          var_dump( $this->cluster );
//          exit;
        }
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
		if( isset($this->data['data']['call-number'])) {
			$cns = $this->data['data']['call-number'];
			if( is_array( $cns )) {
				$sigs = array_merge( $sigs, $cns );
			} else {
				$sigs[] = $cns;
			}
			
		}
		var_dump( array_keys( $this->data['data']));
		var_dump( $sigs );
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
		  $r = $this->item->getRights();
      if( count( $r ) == 0 ) $r[] = 'unknown';
      return $r;
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
      return ($this->item->getUrl() != null) || (count($this->item->getAttachments()) > 0);
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
      $acls = $this->item->getVar( 'acl_meta' );
      $lib = $this->item->getLibrary();
      $acls = array_merge( $acls, $lib->getVar( 'acl_meta' ));
      if( count( $acls ) == 0 ) $acls[] = 'global/guest';

		  return $acls;
    }

    public function getContentACL() {
      if( $this->item == null ) throw new \Exception( "no entity loaded" );
      $acls = $this->item->getVar( 'acl_content' );
      $lib = $this->item->getLibrary();
      $acls = array_merge( $acls, $lib->getVar( 'acl_content' ));
      if( count( $acls ) == 0 ) $acls[] = 'location/fhnw';

		  return $acls;
    }

	public function getMediaTypes() {
      if( $this->item == null ) throw new \Exception( "no entity loaded" );
	  return $this->item->getMediaTypes();
	}

    public function getPreviewACL() {
      if( $this->item == null ) throw new \Exception( "no entity loaded" );
      $acls = $this->item->getVar( 'acl_preview' );
      $lib = $this->item->getLibrary();
      $acls = array_merge( $acls, $lib->getVar( 'acl_preview' ));
      if( count( $acls ) == 0 ) $acls[] = 'global/guest';

		  return $acls;
    }

    public function getLanguages() {
      return array();
    }

    public function getIssues() { return array( ); }

	public function getCategories() {
		$categories = parent::getCategories();
		$categories[] = 'fhnw!!hgk!!pub!!'.$this->item->getLibrary()->GetName();
    foreach( $this->item->getCollections() as $coll ) {
      $categories[] = 'fhnw!!hgk!!pub!!'.$this->item->getLibraryName().'!!'.str_replace( ':', '!!', $coll->getFullName() );
    }
		return $categories;
	}

	public function getCatalogs() {
    $cats = $this->item->getLibrary()->getVar( 'catalog' );
    $cats = array_merge( $cats, $this->item->getVar( 'catalog' ));
    $cats[] = $this->getSource();
    $cats[] = $this->item->getLibraryName();
    //$cats[] = 'HGKplus';
    return $cats;
  }

  public function getCSL() {
    return $this->item->getCSL();
  }

  public function getLibraryCatalog() {

  }

}

?>
