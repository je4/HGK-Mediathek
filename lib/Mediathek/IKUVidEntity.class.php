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

class IKUVidEntity implements SOLRSource {
    private static $videotable = 'source_ikuvid';
    private $json = null;
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
        
        $this->id = str_pad($id, 9, '0', STR_PAD_LEFT );;
        $this->idprefix = $idprefix;
        
        $sql = "SELECT * FROM `".self::$videotable."` WHERE `Archiv-Nr` = ".$this->db->qstr( $id );
        $this->data = $this->db->GetRow( $sql );
        
    }
    
    public function getID() {
        return $this->idprefix.$this->id; 
    }
    
    public function getSource() {
        return 'IKUVid';
    }

	public function getOpenAccess() {
		return false;
	}

	public function getLocations() {
		return array( 'E75:Mediathek' );
	}
    
    public function getTitle() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $title = trim( $this->data['Titel1'] );
        if( strlen( trim( $this->data['Titel2'] )))
            $title .= '. '.trim( $this->data['Titel2'] );

        return $title;
    }
    
    public function getPublisher() {
        return null;
    }
    
    public function getYear() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $year = intval( substr( $this->data['Produktionsjahr'], 0, 4 ));
        return $year ? $year : null;
    }
    
    public function getCity() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return null;
    }
    
	public function getTags() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        
        $this->tags = array();
        if( strlen(trim( $this->data['Medium'])))
            $this->tags[] = 'index:medium:ikuvid/'.md5( trim( $this->data['Medium']) ).'/'.trim( $this->data['Medium']);
        if( strlen(trim( $this->data['Techn Daten'])))
            $this->tags[] = 'index:tech:ikuvid/'.md5( trim( $this->data['Techn Daten']) ).'/'.trim( $this->data['Techn Daten']);
        if( strlen(trim( $this->data['Kategorie'])))
            $this->tags[] = 'index:category:ikuvid/'.md5( trim( $this->data['Kategorie']) ).'/'.trim( $this->data['Kategorie']);
        if( strlen(trim( $this->data['Stichwort'])))
            $this->tags[] = 'index:keyword:ikuvid/'.md5( trim( $this->data['Stichwort']) ).'/'.trim( $this->data['Stichwort']);
        
        $this->tags = array_unique( $this->tags );
        $this->cluster = array();
        foreach( $this->tags as $tag ) {
            if( substr( $tag, 0, strlen( 'index:category' )) == 'index:category'
               || substr( $tag, 0, strlen( 'index:keyword' )) == 'index:keyword' ) {
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
        if( $this->authors == null ) {
            $this->authors = array();
            if( strlen(trim( $this->data['Autor Regie'])))
                 $this->authors[] = trim( $this->data['Autor Regie']);
        }
        return $this->authors;        
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
        return json_encode( $this->data );
    }
    
    public function getOnline() {
        return true;
    }

   public function getAbstract() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $bem = trim( $this->data['Bemerkungen']);
        return strlen( $bem ) ? $bem : null;    
    }
   public function getContent() { return null; }    
   public function getCodes() { return array(); }    
}

?>