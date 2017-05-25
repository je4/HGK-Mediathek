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

/**
 * Handling of MARC Data from Nebis
 *
 */

class videokunst_ch_workEntity extends SOLRSource {

    private $idprefix = 'videokunst_ch_work';
    private $db = null;



    function __construct( \ADOConnection $db ) {
        $this->db = $db;
    }

    public function loadFromDoc( $doc) {
    	$this->data = json_decode( gzdecode( base64_decode( $doc->metagz )), true);
    	$this->id = $doc->originalid;
    }

    public function reset() {
      $this->data = null;
      $this->id = null;
    }

    public function loadFromArray( $data ) {
      $this->data = $data;
      $this->id = $data['videoid'];
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
		return "projectable";
	}

    public function getEmbedded() {
		return true;
	}

    public function getSource() {
        return 'videokunst_ch_work';
    }

	public function getLocations() {
		return array( 'videokunst.ch:online' );
	}

	public function getOpenAccess() {
		return false;
	}

    public function getTitle() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $title = trim( $this->data['vidtitel'] );

        return $title;
    }

    public function getPublisher() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return "videokunst.ch";
    }

    public function getCodes() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
		$codes = array();
		return $codes;
    }

    public function getYear() {
        return null;
    }

    public function getCity() {
        return null;
    }

	public function getTags() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );

        $this->tags = array('index:type/'.md5( 'videokunst' ).'/videokunst' );


        $this->tags = array_unique( $this->tags );
        $this->cluster = array();
        foreach( $this->tags as $tag ) {
              $ts = explode( '/', $tag );
              $this->cluster[] = $ts[count( $ts )-1];
        }
        $this->cluster = array_unique( $this->cluster );

        return $this->tags;
	}


    public function getCluster() {
        if( $this->cluster == null) $this->getTags();
        return $this->cluster;
    }

    public function getSignatures() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );

        return array();
    }

    public function getAuthors() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $authors = array();
  			$authors[] = trim( $this->data['artistname'] );
		    return $authors;
    }

    public function getLoans() {
        return array();
    }

    public function getBarcode() {
        return null;
    }

    public function getSignature() {
        return null;
    }

    public function getLicenses() {
      $licenses = array( 'unknown' );
		    return $licenses;
    }

    public function getURLs() {
  		$urls = array( $this->data['vidhtml'] );
  		return $urls;
    }

    public function getSys() {
        return $this->id;
    }

    public function getMeta() {
        return json_encode( $this->data );
    }

    public function getOnline() {
        return true;
    }

   public function getAbstract() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return strip_tags( str_replace( '<br', "\n<br", $this->data['vidtext'] ));
    }

   public function getContent() {
     if( $this->data == null ) throw new \Exception( "no entity loaded" );
     return null;
   }

    public function getMetaACL() { return array( 'global/guest' ); }
    public function getContentACL() { return array( 'global/guest' ); }
    public function getPreviewACL() { return array(); }
    public function getLanguages() { return array(); }
    public function getIssues() { return array( ); }

	public function getCategories() {
		$categories = parent::getCategories();
		$categories[] = 'extarchives!!videokunst_ch!!work';
		return $categories;
	}

	public function getCatalogs() { return array( $this->getSource(), 'videokunst_ch' ); }

}

?>
