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

class to_performEntity extends SOLRSource {
    private $idprefix = null;

	static $persons = array( 'Author', 'Creator', 'Artist', );
    
   
    function __construct( \ADOConnection $db = null ) {
        
    }

    public function reset() {
    	parent::reset();
        $this->idprefix = null;
    }
    
    function loadFromArray( string $id, array $row, string $idprefix ) {
        $this->reset();
        
        $this->data = $row;
        
        $this->id = $this->data['Id'];
        $this->idprefix = $idprefix;
    }
    
    public function getID() {
        return $this->idprefix.str_pad($this->id, 9, '0', STR_PAD_LEFT ); 
    }
	
	public function getOriginalID() {
		return $this->id;
	}
    
    public function getSource() {
        return 'to_perform';
    }
	
    public function getType() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
		if( @strlen( $this->data['Vermittlung'] )) return $this->data['Vermittlung'];
		else return 'unknown';
	}

	
	public function getEmbedded() {
		return true;
	}

	public function getOpenAccess() {
		return false;
	}

	public function getLocations() {
		return array( 'online' );
	}
    
    public function getTitle() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
		if( array_key_exists( 'Title', $this->data )) return $this->data['Title'];
		if( array_key_exists( 'Document Name', $this->data )) return $this->data['Document Name'];
		
		else return 'unknown';
    }
    
    public function getPublisher() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
		if( @strlen( $this->data['Verleger'] )) return array( $this->data['Verleger'] );
		else return array( 'Hochschule für Musik Basel' );
    }
    
    public function getYear() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
		if( @preg_match( '/^({0-9]{2})\.({0-9]{2})\.({0-9]{4})$/', $this->data['Description'], $matches )) {
			return intval( $matches[3] );
		}
        return null;
    }
    
    public function getCity() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return 'Basel';
    }
    
	public function getTags() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        
        $this->tags = array();
        $this->cluster = array();
		
		if( false ) {
			foreach( explode( ';', preg_replace( '/(?<! u|-)[\.,]/', ';',$this->data['Fach'])) as $fach ) {
				$this->tags[] = 'index:medium:ezb/'.md5( trim( $fach ) ).'/'.trim( $fach );
				$this->cluster[] = trim( $fach );
			}
        }
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
		$persons = array();
		foreach( to_performEntity::$persons as $p ) {
			if( array_key_exists( $p, $this->data )) {
				$persons[] = $this->data[$p];
			}
		}
        return $persons;        
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
        $urls = array();
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
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
		if( array_key_exists( 'Description', $this->data )) 
			return $this->data['Description'];
        return null;    
	}
    
   public function getContent() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
		if( array_key_exists( 'Full Text', $this->data )) 
			return $this->data['Full Text'];
        return null;    
   }    

   public function getCodes() {
        $codes = array();
        
        return $codes;
    }
   
    public function getMetaACL() { return array( 'musik/to_perform', 'global/admin' ); }
    public function getContentACL() {
        return array( 'location/fhnw' );
    }
    public function getPreviewACL() { 
		return array( 'location/fhnw' ); 
	}

	public function getLanguages() { return array(); }
	public function getIssues()  { return array(); }
	
	public function getCategories() {
		$categories = parent::getCategories();
		if( preg_match( '/Projekte:to_perform:(.*)$/', $this->data['Categories'], $matches )) {
			$categories[] = 'fhnw!!hsm!!fue!!prj!!to_perform!!'.str_replace( ':', '!!', $matches[1] );
		}
		else $categories[] = 'fhnw!!hsm!!fue!!prj!!to_perform';
		return $categories;
	}

	public function getCatalogs() { return array( $this->getSource() ); }
}

?>