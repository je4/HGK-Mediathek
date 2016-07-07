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
3002, 
3005, 
3007, 
3008, 
3010, 
3014, 
3015, 
3016, 
3017, 
3020, 
3021, 
3023, 
3024, 
3027, 
3050, 
3052, 
3056, 
3057, 
3058, 
3060, 
3061, 
3062, 
3063, 
3065, 
3066, 
3067, 
3068, 
3069, 
3070, 
3071, 
3072, 
3073, 
3074, 
3075, 
3077, 
3079, 
3081, 
3082, 
3083, 
3085, 
3088, 
3089, 
3090, 
3091, 
3092, 
3093, 
3097, 
3098, 
3099, 
3100, 
3101, 
3103, 
3104, 
3106, 
3108, 
3109, 
3110, 
3112, 
3114, 
3117, 
3118, 
3119, 
3124, 
3125, 
3127, 
3128, 
3129, 
3131, 
3132, 
3133, 
3134, 
3135, 
3136, 
3137, 
'3150_Dx', 
3151, 
3154, 
3159, 
3160, 
3161, 
3162, 
3163, 
3164, 
3168, 
3169, 
3170, 
3171, 
3172, 
3173, 
3174, 
3175, 
3176, 
3178, 
3179, 
3181, 
3184, 
3185, 
3186, 
3187, 
3188, 
3189, 
3191, 
3192, 
3193, 
3194, 
3195, 
3197, 
3198, 
3200, 
3201, 
3202, 
3205, 
3206, 
3211, 
3212, 
3213, 
3215, 
3216, 
3217, 
3218, 
3219, 
3222, 
3223, 
3225, 
6002, 
6003, 
6010, 
6024, 
6027, 
6036, 
6037, 
6038, 
6039, 
6040, 
6041, 
6042, 
6046, 
6047, 
6049, 
6050, 
6053, 
6054, 
6055, 
6056, 
6057, 
6058, 
6064, 
6069, 
6072, 
6073, 
6076, 
6080, 
6081, 
6083, 
6084, 
6085, 
6087, 
6088, 
6089, 
6090, 
6091, 
6092, 
6093, 
6094, 
6098, 
6107, 
6110, 
6113, 
6116, 
6119, 
6120, 
6138, 
6156, 
6158, 
6159, 
6277, 
6286, 
6407, 
6408, 
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
    public function getContentACL() { return array( 'certificate/mediathek' ); }
    public function getPreviewACL() { return array( 'location/fhnw' ); }

}

?>