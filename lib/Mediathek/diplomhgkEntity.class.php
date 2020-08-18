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

class diplomhgkEntity extends SOLRSource {
    private $json = null;
    private $idprefix = 'diplomhgk';
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
    }

    public function loadFromDoc( $doc) {
    	$this->data = ( array )json_decode( gzdecode( base64_decode( $doc->metagz )));
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

    function loadFromArray( array $row ) {
        $this->reset();

        $this->data = $row;

        $this->id = "{$this->data['year']}{$this->data['idperson']}";
        // $this->idprefix = $idprefix;

        //echo $this->id."\n";

        //var_dump( $this->data );
    }

    public function getID() {
        return $this->idprefix.'-'.str_pad($this->id, 12, '0', STR_PAD_LEFT );
    }

	public function getOriginalID() {
		return $this->id;
	}

    public function getSource() {
        return 'diplomhgk';
    }

    public function getType() {
		return "project";
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
        $title = trim( $this->data['meta']['titel'] );
        return $title;
    }

    public function getPublisher() {
        return array( 'FHNW Academy of Art and Design Basel' );
    }

    public function getYear() {
        return $this->data['year'];
    }

    public function getCity() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return 'Basel';
    }

	public function getTags() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );

        $this->tags = array( 'diplom'
                                ,'diplom'.$this->data['year']
                                ,strtolower($this->data['anlassbezeichnung'])
                                ,strtolower($this->data['institut'])
                                ,strtolower($this->data['abschluss'])
                               );
        $this->cluster = array('diplom'
                                ,'diplom'.$this->data['year']
                                , strtolower($this->data['anlassbezeichnung'])
				, strtolower($this->data['institut'])
				, strtolower($this->data['abschluss'])
								);


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

        return array("{$this->data['nachname']}, {$this->data['vornamen']}");
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

        $doURLs = function( $str ) {
          $_lines = explode( '\n', $str );
          $_urls = array();
          foreach( $_lines as $_l ) {
            $_l = trim( $_l );
            if( !preg_match( '/^http(s)?:\/\//', $_l )) $_l = 'http://'.$_l;
            $_urls[] = $_l;
          }
          return $_urls;
        };

        $urls = array();
        if( @strlen( $this->data['meta']['web1'] )) foreach( $doURLs( $this->data['meta']['web1'] ) as $url ) { $urls[] = 'web:'.$url; }
        if( @strlen( $this->data['meta']['web2'] )) foreach( $doURLs( $this->data['meta']['web2'] ) as $url ) { $urls[] = 'web:'.$url; }
        if( @strlen( $this->data['meta']['webmedia'] )) foreach( $doURLs( $this->data['meta']['webmedia'] ) as $url ) { $urls[] = 'media:'.$url; }

        return $urls;
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
        return $this->data['meta']['beschreibung'];
    }

   public function getContent() { return null; }
   public function getCodes() {
        $codes = array();

        return $codes;
    }
	
	public function getMediaTypes() {
			$types = [];
		   foreach( $this->data['files'] as $f ) {
			   $types[] = $f['metadata']['type'];
		   }
		   
		   $types = array_unique( $types );
		   return $types;
	}

    public function getMetaACL() { return ($this->data['year'] == 2017 ? array( 'location/fhnw' ) : array( 'global/guest' )); }
    public function getContentACL() { return ($this->data['year'] == 2017 ? array( 'location/fhnw' ) : array( 'global/guest' )); }
    public function getPreviewACL() { return ($this->data['year'] == 2017 ? array( 'location/fhnw' ) : array( 'global/guest' )); }

	public function getLanguages() { return array(); }
	public function getIssues()  { return array(); }
	public function getCatalogs() { return array( $this->getSource(), 'HGK' ); }

}

?>
