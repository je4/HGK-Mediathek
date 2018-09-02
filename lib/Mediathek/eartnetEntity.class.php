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

class eartnetEntity extends SOLRSource {
    private $json = null;
    private $idprefix = 'eartnet';
    private $db = null;
    private $barcode = null;
    private $signature = null;
    private $loans = null;
    private $authors = null;
    private $tags = null;
    private $cluster = null;
    private $licenses = null;
    private $urls = null;
    private $signatures = null;
    private $online = false;


    function __construct( \ADOConnection $db ) {
        $this->db = $db;
        //die( "to be implemented" );
    }

    public function loadFromDoc( $doc) {
    	$this->data = ( array )json_decode( gzdecode( base64_decode( $doc->metagz )), true);
    	$this->id = $doc->originalid;
    }

    public function reset() {
    	parent::reset();
        $this->json = null;
        $this->barcode = null;
        $this->signature = null;
        $this->authors = null;
        $this->loans = null;
        $this->tags = null;
        $this->licenses = null;
        $this->urls = null;
        $this->signatures = null;
        $this->online = false;

    }

    function loadFromArray( array $rows ) {
        $this->reset();

        $this->data = $rows;

        $this->id = md5( $rows[0]['bmdm_name'] );
    }

    public function getID() {
        return $this->idprefix.'-'.$this->id;
    }

	public function getOriginalID() {
		return $this->id;
	}

    public function getSource() {
        return 'eartnet';
    }

    public function getType() {
        switch( trim( $this->data[0]['person_kategorie_2'] )) {
          case '':
          case 'person':
          case 'Artist':
          case 'art-critic':
          case 'art-curator':
		        return 'person';
            break;
          case 'group':
          case 'Art-Group':
            return 'group';
            break;
          default:
            return 'institution';
        }
	}


	public function getEmbedded() {
		return false;
	}

	public function getOpenAccess() {
		return false;
	}

	public function getLocations() {
		return array( 'online' );
	}

    public function getTitle() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return "European Artnet";
    }

    public function getPublisher() {
        return array( 'European Artnet' );
    }

    public function getYear() {
        return null;
    }

    public function getCity() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return null;
    }

	public function getTags() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );

        $this->tags = array();
        $this->cluster = array();
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

        $authors = array();
        foreach( $this->data as $row ) {
          $authors[] = $row['person_nachname'].', '.$row['person_vorname'];
        }
        return array_unique( $authors );
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
        if( $this->licenses == null ) {
            $this->licenses = array( 'restricted' );
        }
        return $this->licenses;
    }

    public function getURLs() {
      return array();
    }

    public function getSys() {
        return $this->id;
    }

    public function getMeta() {
        $json = json_encode( $this->data );
        return $json;
    }

    public function getOnline() {
		return true;
    }

   public function getAbstract() {
        return null;
    }

   public function getContent() { return null; }
   public function getCodes() {
        $codes = array();

        return $codes;
    }

    public function getMetaACL() { return array( 'global/user' ); }
    public function getContentACL() { return array( 'global/user' ); }
    public function getPreviewACL() { return array( 'global/user' ); }

	public function getLanguages() { return array(); }
	public function getIssues()  { return array(); }
	public function getCatalogs() { return array( $this->getSource(), 'HGK' ); }

}

?>
