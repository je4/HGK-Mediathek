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

class DOABEntity extends SOLRSource {
    private static $table = 'source_doajoai';
    private $idprefix = 'doab';
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
    
    function loadFromDatabase( string $id, string $idprefix ) {
        $this->reset();
        
        $this->id = $id;
        //$this->idprefix = $idprefix;
        
        $sql = "SELECT * FROM `".self::$table."` WHERE `identifier` = ".$this->db->qstr( $id );
        $row = $this->db->GetRow( $sql );
        $this->data = (array)json_decode( $row['data'] );
    }
    
    function loadFromArray( string $id, array $data, string $idprefix ) {
        $this->id = $id;
        //$this->idprefix = $idprefix;
		$this->data = $data;
	}
	 
	private function getFirstField( $name ) {
		if( !array_key_exists( $name, $this->data )) return null;
		if( !is_array( $this->data[$name] )) return null;
		if( !count( $this->data[$name] )) return null;
		return $this->data[$name][0];
	}
	
	private function getFields( $name ) {
		if( !array_key_exists( $name, $this->data )) return array();
		if( !is_array( $this->data[$name] )) return array();
		return $this->data[$name];
	}

    public function getID() {
        return $this->idprefix.'-'.$this->id; 
    }
	
	public function getOriginalID() {
		return $this->id;
	}
    	
    public function getType() {
		return "Book";
	}

    public function getEmbedded() {
		return false;
	}

    public function getSource() {
        return 'DOAB';
    }
    
	public function getLocations() {
		return array( 'DOAB:online' );
	}
	
	public function getOpenAccess() {
		return true;
	}
	
    public function getTitle() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $title = trim( $this->getFirstField('DC:TITLE') );

        return $title;
    }
    
    public function getPublisher() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return $this->getFields('DC:PUBLISHER');
    }

    public function getCodes() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $identifiers = $this->getFields( 'DC:IDENTIFIER');
		$codes = array();
		foreach( $identifiers as $ident )
			if( !preg_match( "/:\/\//", $ident ))
				$codes[] = $ident;
		return $codes;
    }
    
    public function getYear() {
        return $this->getFirstField('DC:DATE') ;
    }
    
    public function getCity() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return null; // $this->data['Country of publisher'];
    }
    
	public function getTags() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
    
        
        $this->tags = array();
        $specs = $this->getFields( 'SETSPEC' );
		foreach( $specs as $spec ) {
			if( substr( $spec, 0, 10 ) == 'publisher_' ) continue;
			$spec = str_replace( '_', ' ', $spec );
			$this->tags[] = 'subject:doab/'.md5( trim( $spec) ).'/'.trim( $spec );
		}
        $this->tags = array_unique( $this->tags );
        $this->cluster = array();
        foreach( $this->tags as $tag ) {
            if( substr( $tag, 0, strlen( 'index:keyword' )) == 'index:keyword'
                  || substr( $tag, 0, strlen( 'subject:doab' )) == 'subject:doab') {
                $ts = explode( '/', $tag );
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
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        
        return array();
    }
    
    public function getAuthors() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $authors = array();
		foreach( $this->getFields( 'DC:CREATOR') as $author ) {
			$as = explode( ',', $author );
			if( count( $as ) != 2 ) $authors[] = $author;
			else $authors[] = trim( $as[1] ).' '.trim( $as[0] );
		}
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
        $licenses = $this->getFields( 'DC:RIGHT');
		if( !count( $licenses )) $licenses = array( 'unknown' );
		return $licenses;
    }
    
    public function getURLs() {
        $identifiers = $this->getFields( 'DC:IDENTIFIER');
		$urls = array();
		foreach( $identifiers as $ident )
			if( preg_match( "/:\/\//", $ident ))
				$urls[] = $ident;
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

        return $this->getFirstField( 'DC:DESCRIPTION' );    
    }
   public function getContent() { return null; }
   
    public function getMetaACL() { return array( 'global/guest' ); }
    public function getContentACL() { return array(); }
    public function getPreviewACL() { return array(); }
    public function getLanguages() { return array(); }
    public function getIssues() { return array(); }
	
	public function getCategories() {
		$categories = parent::getCategories();
		$categories[] = 'openaccess!!DOAB';
		return $categories;
	}	
	public function getCatalogs() { return array( $this->getSource() ); }
	
}

?>