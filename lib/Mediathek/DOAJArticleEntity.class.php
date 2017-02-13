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

class DOAJArticleEntity extends SOLRSource {
    private static $table = 'source_doajoai';
    private $idprefix = 'doajarticle';
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

    public function loadFromDoc( $doc) {
    	$this->data = ( array )json_decode( gzdecode( base64_decode( $doc->metagz )));
    	$this->id = $doc->originalid;
    }
    
    function loadFromDatabase( string $id, string $idprefix ) {
        $this->reset();
        
        $this->id = $id;
        //$this->idprefix = $idprefix;
        
        $sql = "SELECT data FROM `".self::$table."` WHERE `identifier` = ".$this->db->qstr( $id );
        $this->data = ( array )json_decode( $this->db->GetOne( $sql ));
    }
    
    function loadFromArray( string $id, array $data, string $idprefix ) {
        $this->id = $id;
        //$this->idprefix = $idprefix;
		$this->data = $data;
	}
	
	
    public function getID() {
        if( substr( $this->id, 0, strlen( 'oai:doaj.org/article:')) == 'oai:doaj.org/article:' )
            return $this->idprefix.'-'.substr( $this->id, strlen( 'oai:doaj.org/article:'));
        return $this->idprefix.'-'.$this->id; 
    }
    
    public function getSource() {
        return 'DOAJArticle';
    }
    
    public function getOpenAccess() {
        return true;
    }
    
    public function getType() {
		return "Article";
	}

	public function getEmbedded() {
		return false;
	}

    public function getTitle() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $title = @trim( $this->data['DC:TITLE'][0] );

        return $title;
    }
    
    public function getPublisher() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $pub = $this->data['DC:PUBLISHER'];

        return $pub;
    }

    public function getCodes() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $codes = array();
		foreach( $this->data['DC:IDENTIFIER'] as $ident ) {
			if( preg_match( "/[0-9]+-[0-9]+/", $ident ))
				$codes[] = 'ISSN:'.trim( $ident );
			elseif( preg_match( "/[0-9]{2}.[0-9]{4}\//", $ident ))
				$codes[] = 'DOI:'.trim( $ident );
		}
        return $codes;
    }
    
    public function getYear() {
		if( !isset( $this->data['DC:DATE'] )) return null;
		$ds = $this->data['DC:DATE'];
		if( !count($ds )) return null;
		$d = new \DateTime( $ds[0] );
		return intval( $d->format( 'Y' ));
    }
    
    public function getCity() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return null;
    }
    
	public function getTags() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
    
        
        $this->tags = array();
    
        $kws = $this->data['DC:SUBJECT'];

        foreach( $kws as $kw ) {
            $this->tags[] = 'subject:hierarchical:doaj/'.md5( trim( $kw) ).'/'.trim( $kw );
        }
        
        $this->tags = array_unique( $this->tags );
        $this->cluster = array();
        foreach( $this->tags as $tag ) {
            if( substr( $tag, 0, strlen( 'index:keyword' )) == 'index:keyword'
                  || substr( $tag, 0, strlen( 'subject:hierarchical' )) == 'subject:hierarchical') {
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
		if( !@is_array( $this->data['DC:CREATOR'] )) return array();
		$authors = array();
		foreach( $this->data['DC:CREATOR'] as $a ) {
			if( strpos( $a, ',' ) === false ) {
				$as = explode( ' ', $a );
				if( count( $as ) > 1 ) {
					$a = array_pop( $as );
					$a .= ', '.implode( ' ', $as );
				}
			}
			$authors[] = $a;
		}
		//print_r( $authors );
        return $authors;
    }
    
    public function getLoans() {
        return array();
    }
    
	public function getLocations() {
		return array( 'online' );
	}
   
	
	public function getOriginalID() {
		return $this->id;
	}
         
    public function getBarcode() {
        return null;
    }
    
    public function getSignature() {
        return null;
    }
    
    public function getLicenses() {
            if( !isset( $this->data['DC:RIGHTS'] )) return array( 'open access' );
            return $this->data['DC:RIGHTS'];
    }
    
    public function getURLs() {
        if( $this->urls == null ) {
            $this->urls = array();
			foreach( $this->data['DC:IDENTIFIER'] as $ident ) {
				if( preg_match( '/https?:\/\//', $ident ))
					$this->urls[] = 'identifier:'.trim( $ident );
			}
			foreach( $this->data['DC:RELATION'] as $ident ) {
				$this->urls[] = 'relation:'.trim( $ident );
			}
            
        }
        return $this->urls;
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

        return @trim( $this->data['DC:DESCRIPTION'][0] );    
    }
   public function getContent() { return null; }
   
    public function getMetaACL() { return array( 'global/guest' ); }
    public function getContentACL() {
        // todo: check, whether ppl are in fhnw network???
        return array(  );
    }
    public function getPreviewACL() { return array( 'location/fhnw' ); }
   
   	public function getLanguages() {
        if( isset( $this->data['DC:LANGUAGE']))
            return $this->data['DC:LANGUAGE'];
        return array();
    }
    
	public function getIssues() {
        if( isset( $this->data['DC:SOURCE']))
            return $this->data['DC:SOURCE'];
        return array();
    }

	public function getCategories() {
		$categories = parent::getCategories();
		$categories[] = 'openaccess!!DOAJArticle';
		return $categories;
	}	
	public function getCatalogs() { return array( $this->getSource(), 'openaccess_journals' ); }
	
}

?>