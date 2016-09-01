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
    
    static $done = array(
3010,
3014,
3015,
3017,
3020,
3021,
3057,
3058,
3063,
3065,
3067,
3070,
3072,
3073,
3075,
3079,
3081,
3088,
3089,
3090,
3092,
3098,
3100,
3101,
3103,
3104,
3106,
3112,
3114,
3127,
3128,
3131,
3132,
3133,
3134,
3135,
3137,
3141,
3145,
3151,
3153,
3154,
3160,
3161,
3164,
3171,
3175,
3178,
3184,
3185,
3186,
3187,
3189,
3201,
3205,
3217,
3222,
3223,
3225,
3228,
3237,
3243,
3246,
6002,
6003,
6008,
6009,
6010,
6014,
6018,
6024,
6025,
6026,
6027,
6028,
6029,
6038,
6039,
6042,
6047,
6050,
6054,
6057,
6058,
6072,
6076,
6083,
6088,
6089,
6090,
6091,
6094,
6097,
6098,
6102,
6106,
6107,
6110,
6113,
6114,
6116,
6119,
6120,
6122,
6127,
6128,
6132,
6138,
6142,
6150,
6152,
6153,
6158,
6159,
6165,
6170,
6172,
6174,
6175,
6179,
6182,
6185,
6193,
6197,
6200,
6202,
6203,
6205,
6208,
6211,
6212,
6216,
6218,
6219,
6223,
6229,
6235,
6244,
6247,
6248,
6251,
6253,
6259,
6261,
6263,
6266,
6267,
6268,
6270,
6271,
6273,
6274,
6275,
6280,
6282,
6286,
6287,
6289,
6294,
6296,
6299,
6300,
6307,
6310,
6316,
6323,
6326,
6330,
6333,
6335,
6337,
6341,
6343,
6346,
6348,
6351,
6360,
6364,
6368,
6370,
6407,
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
        
        $sql = "SELECT * FROM `".self::$videotable."` WHERE `Archiv-Nr` = ".$this->db->qstr( $id );
        $this->data = $this->db->GetRow( $sql );
        
    }
    
    public function getID() {
        return $this->idprefix.str_pad($this->id, 9, '0', STR_PAD_LEFT ); 
    }
	
	public function getOriginalID() {
		return $this->id;
	}
    
    public function getSource() {
        return 'IKUVid';
    }
	
    public function getType() {
		return "MovingImage";
	}

	
	public function getEmbedded() {
		return array_search($this->id, IKUVidEntity::$done ) !== false;
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
		return array_search($this->id, IKUVidEntity::$done ) !== false;
    }

   public function getAbstract() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $bem = trim( $this->data['Bemerkungen']);
        return strlen( $bem ) ? $bem : null;    
    }
   public function getContent() { return null; }    
   public function getCodes() { return array(); }
   
    public function getMetaACL() { return array( 'global/guest' ); }
    public function getContentACL() { return array( 'certificate/mediathek', 'fhnw/video' ); }
    public function getPreviewACL() { return array( 'location/fhnw' ); }

}

?>