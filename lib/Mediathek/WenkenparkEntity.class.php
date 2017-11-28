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

class WenkenparkEntity extends SOLRSource {
    private static $videotable = 'source_wenkenpark';
    private $json = null;
    private $idprefix = 'vww';
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

    function loadFromDatabase( $id, string $idprefix, $media ) {
        global $ADODB_FETCH_MODE;

        $this->reset();

        $this->id = $id;
        //$this->idprefix = $idprefix;

        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $sql = "SELECT * FROM `".self::$videotable."` WHERE `Publikationsnummer` = ".$this->db->qstr( $id );
        $this->data = $this->db->GetRow( $sql );
        $this->data['media'] = $media;

    }

    public function getData() {
      return $this->data;
    }

    public function getID() {
        return $this->idprefix.'-'.str_pad($this->id, 4, '0', STR_PAD_LEFT );
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
		return true;
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
        //$this->tags[] = 'index:keyword:wenkenpark/'.md5( trim( 'Wenkenpark' )).'/Wenkenpark';
        if( strlen(trim( $this->data['Videowoche Jahr'])))
            $this->tags[] = 'index:keyword:wenkenpark/'.md5( trim( $this->data['Videowoche Jahr']) ).'/Videowoche im Wenkenpark '.trim( $this->data['Videowoche Jahr']);
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
                 $this->authors[] = trim( $this->data['AutorInN1']).', '.trim( $this->data['AutorinVN1']);
            if( strlen(trim( $this->data['AutorinVN2'])))
                 $this->authors[] = trim( trim( $this->data['AutorInN2']).', '.$this->data['AutorinVN2']);
            if( strlen(trim( $this->data['AutorinVN3'])))
                 $this->authors[] = trim( $this->data['AutorInN3']).', '.trim( $this->data['AutorinVN3']);
            if( strlen(trim( $this->data['KonzeptDrehbuchVor1'])))
                 $this->authors[] = trim( $this->data['KonzeptDrehbuchNach1']).', '.trim( $this->data['KonzeptDrehbuchVor1']);
            if( strlen(trim( $this->data['KonzeptDrehbuchVor2'])))
                 $this->authors[] = trim( $this->data['KonzeptDrehbuchNach2']).', '.trim( $this->data['KonzeptDrehbuchVor2']);
/*
            if( strlen(trim( $this->data['KAMERA'])))
                 $this->authors[] = trim( $this->data['KAMERA']);
            if( strlen(trim( $this->data['Schnitt'])))
                 $this->authors[] = trim( $this->data['Schnitt']);
            if( strlen(trim( $this->data['MUSIK'])))
                 $this->authors[] = trim( $this->data['MUSIK']);
            if( strlen(trim( $this->data['Ton'])))
                 $this->authors[] = trim( $this->data['Ton']);
*/
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
		return true;
    }

   public function getAbstract() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $bem = trim( $this->data['KURZBESCHRIEB']);
        return strlen( $bem ) ? $bem : null;
    }
   public function getContent() { return null; }
   public function getCodes() { return array(); }

    public function getMetaACL() { return array( 'global/guest' ); }
    public function getContentACL() {
      switch( $this->data['Rechte Internet'] ) {
        case 'open':
          return array( 'global/guest' );
          break;
        case 'station':
          return array( 'certificate/mediathek', 'global/admin' );
          break;
        case 'intern':
          return array( 'location/fhnw' );
          break;
        default:
          return array( 'global/admin' );
      }
      //return array( 'certificate/mediathek', 'fhnw/video' );
    }
    public function getPreviewACL() { return array( 'global/guest' );
    }
	public function getLanguages() { return array(); }
	public function getIssues()  { return array(); }
	public function getCatalogs() { return array( $this->getSource() ); }

}

?>
