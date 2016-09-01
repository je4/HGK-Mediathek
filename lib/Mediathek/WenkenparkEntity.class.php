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

class WenkenparkEntity implements SOLRSource {
    private static $videotable = 'source_wenkenpark';
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
    
    static $done = array(
8402,
8404,
8601,
8633,
8801,
8811,
);


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
        
        $sql = "SELECT * FROM `".self::$videotable."` WHERE `KATALOGNUMMER` = ".$this->db->qstr( $id );
        $this->data = $this->db->GetRow( $sql );
        
    }
    
    public function getID() {
        return $this->idprefix.str_pad($this->id, 9, '0', STR_PAD_LEFT ); 
    }
	
	public function getOriginalID() {
		return $this->id;
	}
    
    public function getSource() {
        return 'Wenkenpark';
    }
	
    public function getType() {
		return "MovingImage";
	}

	
	public function getEmbedded() {
		return array_search(intval( $this->data['Publikationsnummer'] ), WenkenparkEntity::$done ) !== false;
	}

	public function getOpenAccess() {
		return false;
	}

	public function getLocations() {
		return array( 'E75:Mediathek' );
	}
    
    public function getTitle() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $title = trim( $this->data['TITEL'] );

        return $title;
    }
    
    public function getPublisher() {
        $publisher = array();
		if( @strlen( $this->data['Produktion'] )) $publisher[] = $this->data['Produktion'];
		if( @strlen( $this->data['Koproduktion'] )) $publisher[] = $this->data['Koproduktion'];
		return $publisher;
    }
    
    public function getYear() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $year = intval( substr( $this->data['Produktionsjahr'], 0, 4 ));
        return $year ? $year : null;
    }
    
    public function getCity() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return 'Basel';
    }
    
	public function getTags() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        
        $this->tags = array();
        $this->tags[] = 'index:keyword:wenkenpark/'.md5( trim( 'Wenkenpark' )).'/Wenkenpark';
        if( strlen(trim( $this->data['Videowoche Jahr'])))
            $this->tags[] = 'index:keyword:wenkenpark/'.md5( trim( $this->data['Videowoche Jahr']) ).'/'.trim( $this->data['Videowoche Jahr']);
        if( strlen(trim( $this->data['Ursprungsformat'])))
            $this->tags[] = 'index:medium:wenkenpark/'.md5( trim( $this->data['Ursprungsformat']) ).'/'.trim( $this->data['Ursprungsformat']);
        if( strlen(trim( $this->data['Sprache'])))
            $this->tags[] = 'index:language:wenkenpark/'.md5( trim( $this->data['Sprache']) ).'/'.trim( $this->data['Sprache']);
        
		$tech = array();
		if( strlen(trim( $this->data['TV System']))) $tech[] = trim( $this->data['TV System']);
		if( strlen(trim( $this->data['Farbe sw']))) $tech[] = trim( $this->data['Farbe sw']);
		if( strlen(trim( $this->data['Tonart']))) $tech[] = trim( $this->data['Tonart']);
		if( count( $tech )) $this->tags[] = 'index:tech:wenkenpark/'.md5( trim( implode( "\n", $tech ))).'/'.trim( implode( "\n", $tech ));
        
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
        
        return array( $this->data['Publikationsnummer'] );
    }
    
    public function getAuthors() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        if( $this->authors == null ) {
            $this->authors = array();
            if( strlen(trim( $this->data['AutorinVN1'])))
                 $this->authors[] = trim( $this->data['AutorinVN1']).' '.trim( $this->data['AutorInN1']);
            if( strlen(trim( $this->data['AutorinVN2'])))
                 $this->authors[] = trim( $this->data['AutorinVN2']).' '.trim( $this->data['AutorInN2']);
            if( strlen(trim( $this->data['AutorinVN3'])))
                 $this->authors[] = trim( $this->data['AutorinVN3']).' '.trim( $this->data['AutorInN3']);
            if( strlen(trim( $this->data['KonzeptDrehbuchVor1'])))
                 $this->authors[] = trim( $this->data['KonzeptDrehbuchVor1']).' '.trim( $this->data['KonzeptDrehbuchNach1']);
            if( strlen(trim( $this->data['KonzeptDrehbuchVor2'])))
                 $this->authors[] = trim( $this->data['KonzeptDrehbuchVor2']).' '.trim( $this->data['KonzeptDrehbuchNach2']);
            if( strlen(trim( $this->data['KAMERA'])))
                 $this->authors[] = trim( $this->data['KAMERA']);
            if( strlen(trim( $this->data['Schnitt'])))
                 $this->authors[] = trim( $this->data['Schnitt']);
            if( strlen(trim( $this->data['MUSIK'])))
                 $this->authors[] = trim( $this->data['MUSIK']);
            if( strlen(trim( $this->data['Ton'])))
                 $this->authors[] = trim( $this->data['Ton']);
			$this->authors = array_unique( $this->authors );
        }
        return $this->authors;        
    }
    
    public function getLoans() {
        return array();
    }
    
    public function getBarcode() {
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
//		echo "Online: ".$this->data['Publikationsnummer']."\n";
//		print_r( WenkenparkEntity::$done );
		$online = array_search(intval( $this->data['Publikationsnummer'] ), WenkenparkEntity::$done ) !== false;
		echo $online."\n";
		return $online;
    }

   public function getAbstract() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $bem = trim( $this->data['KURZBESCHRIEB']);
        return strlen( $bem ) ? $bem : null;    
    }
   public function getContent() { return null; }    
   public function getCodes() { return array(); }
   
    public function getMetaACL() { return array( 'global/guest' ); }
    public function getContentACL() { return array( 'certificate/mediathek', 'fhnw/video' ); }
    public function getPreviewACL() { return array( 'location/fhnw' ); }

}

?>