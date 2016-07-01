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

class DOAJEntity implements SOLRSource {
    private static $table = 'source_doaj';
    private $data = null;
    private $id = null;
    private $idprefix = null;
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

    private function reset() {
        $this->id = null;
        $this->idprefix = null;
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
        $this->idprefix = $idprefix;
        
        $sql = "SELECT * FROM `".self::$table."` WHERE `Journal EISSN (online version)` = ".$this->db->qstr( $id );
        $this->data = $this->db->GetRow( $sql );
        
    }
    
    public function getID() {
        return $this->idprefix.'-'.$this->id; 
    }
	
	public function getOriginalID() {
		return $this->id;
	}
    	
    public function getType() {
		return "Journal";
	}

    public function getEmbedded() {
		return false;
	}

    public function getSource() {
        return 'DOAJ';
    }
    
	public function getLocations() {
		return array( 'DOAJ:online' );
	}
	
	public function getOpenAccess() {
		return true;
	}
	
    public function getTitle() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $title = trim( $this->data['Journal title'] );

        return $title;
    }
    
    public function getPublisher() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $pub = trim( $this->data['Publisher'] );

        return $pub;
    }

    public function getCodes() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $codes = array();
        if( strlen( trim( $this->data['Journal ISSN (print version)'])))
            $codes[] = 'ISSN:'.trim( $this->data['Journal ISSN (print version)']);
        if( strlen( trim( $this->data['Journal EISSN (online version)'])))
            $codes[] = 'EISSN:'.trim( $this->data['Journal EISSN (online version)']);
        return $codes;
    }
    
    public function getYear() {
        return null;
    }
    
    public function getCity() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return $this->data['Country of publisher'];
    }
    
	public function getTags() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
    
        
        $this->tags = array();
    
        $kws = explode( ',', $this->data['Keywords']);
   
        foreach( $kws as $kw ) {
            $this->tags[] = 'index:keyword:doaj/'.md5( trim( $kw) ).'/'.trim( $kw );
        }
        $kws = explode( '|', $this->data['Subjects']);
   
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
        return array();
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
            $this->licenses = array( );
            $l = "";
            if( strlen( trim( $this->data['Journal license'])))
                $l .= trim( $this->data['Journal license']);
            if( strlen( trim( $this->data['License attributes'])))
                $l .= ' ('.trim( $this->data['License attributes']).')';
            if( strlen( trim( $l )))
                $this->licenses[] = trim( $l );
            if( count( $this->licenses ) == 0)
                $this->licenses[] = 'restricted';
        }
        return $this->licenses;
    }
    
    public function getURLs() {
        if( $this->urls == null ) {
            $this->urls = array();
            if( strlen( trim( $this->data['Journal URL'])))
                $this->urls[] = 'journal:' . trim( $this->data['Journal URL']);
            if( strlen( trim( $this->data['APC information URL'])))
                $this->urls[] = 'apcinfo:' . trim( $this->data['APC information URL']);
            if( strlen( trim( $this->data['Archiving infomation URL'])))
                $this->urls[] = 'archinfo:' . trim( $this->data['Archiving infomation URL']);
            if( strlen( trim( $this->data['URL for license terms'])))
                $this->urls[] = 'license:' . trim( $this->data['URL for license terms']);
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

        return null;    
    }
   public function getContent() { return null; }
   
    public function getMetaACL() { return array( 'global/guest' ); }
    public function getContentACL() { return array(); }
    public function getPreviewACL() { return array(); }
}

?>