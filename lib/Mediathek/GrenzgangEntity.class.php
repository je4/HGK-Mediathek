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

class GrenzgangEntity extends SOLRSource {
    private $json = null;
    private $idprefix = 'grenzgang-';
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

    function loadFromDatabase( string $id, string $idprefix ) {
        $this->reset();

        $this->id = $id;
        //$this->idprefix = $idprefix;

        $sql = "SELECT * FROM `source_grenzgang` WHERE `ID` = ".$this->db->qstr( $id );
        $this->data = $this->db->GetRow( $sql );
        $sql = "SELECT name, value FROM source_properties WHERE id=".$this->db->qstr( $this->idprefix.$this->id );
        $rs = $this->db->Execute( $sql );
        foreach( $rs as $row ) {
          $name = $row['name'];
          $val = $row['value'];
          if( preg_match( '/([^0-9]+)\.([0-9])+$/', $name, $matches )) {
            if( !@is_array( $this->data['meta:'.$matches[1]] ))
              $this->data['meta:'.$matches[1]] = array();

            $this->data['meta:'.$matches[1]][intval( $matches[2] )] = $val;
          }
        	else $this->data['meta:'.$name] = ($name == 'tika' ? json_decode( $val, true ) : trim( $val ));
        }
        $rs->Close();

    }

    public function getID() {
        return $this->idprefix.$this->id;
    }

	public function getOriginalID() {
		return $this->id;
	}

    public function getSource() {
        return 'Grenzgang';
    }

    public function getType() {
    	$type = 'unknown';
      if( pathinfo($this->data['Dateiname'], PATHINFO_EXTENSION) == 'mp3' ) $type = 'sound';
    	elseif( array_key_exists( 'meta:mime', $this->data )) {
	    	if( preg_match( '/^video\//', $this->data['meta:mime'])) $type = 'projectable';
	    	elseif( preg_match( '/^audio\//', $this->data['meta:mime'])) $type = 'sound';
	    	elseif( preg_match( '/^image\//', $this->data['meta:mime'])) $type = '2d';
        elseif( preg_match( '/^text\//', $this->data['meta:mime'])) $type = 'ressource';
        elseif( preg_match( '/^application\/pdf/', $this->data['meta:mime'])) $type = 'ressource';
	    	elseif( preg_match( '/^application\/xml/', $this->data['meta:mime'])) $type = 'track';
    	}
    	elseif( preg_match( '/^ORDNER/', $this->data['Datentyp'])) $type = 'folder';
    	 return $type;
	}


	public function getEmbedded() {
		return true;
	}

	public function getOpenAccess() {
		return false;
	}

	public function getLocations() {
		$locations = array( 'E75:Mediathek' );
		if( array_key_exists( 'Related Work-ID', $this->data ) && intval( $this->data['Related Work-ID'])) {
			$locations[] = 'parent:grenzgang-'.intval( $this->data['Related Work-ID']);
		}
		return $locations;
	}

    public function getTitle() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );

        $title = trim( $this->data['Titel'] );
		if( !strlen( $title )) $title = trim( $this->data['Dateiname'] );

        return $title;
    }

    public function getPublisher() {
        $publisher = array();
		return $publisher;
    }

    public function getYear() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $year = intval( $this->data['Jahr'] );
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
        $value = trim( $this->data['Materialtyp (Schlagwörter 0)']);
        foreach( explode( ';', $value ) as $val ) {
        	$val = trim( $val );
        	if( strlen($val)) $this->tags[] = 'index:materialtyp:grenzgang/'.md5( $val ).'/'.$val;
        }

        $value = trim( $this->data['Medium / Format']);
        foreach( explode( ';', $value ) as $val ) {
        	$val = trim( $val );
        	if( strlen( $val )) $this->tags[] = 'index:mediumformat:grenzgang/'.md5( $val ).'/'.$val;
        }

        $value = trim( $this->data['Datum (JJJJ-MM-TT)']);
        foreach( explode( ';', $value ) as $val ) {
        	$val = trim( $val );
        	if( strlen($val)) $this->tags[] = 'index:keyword:grenzgang/'.md5( $val ).'/'.$val;
        }

        $value = trim( $this->data['Schlagwörter (2)']);
        foreach( explode( ';', $value ) as $val ) {
        	$val = trim( $val );
        	if( strlen($val)) $this->tags[] = 'index:keyword:grenzgang/'.md5( $val ).'/'.$val;
        }

        $value = trim( $this->data['Projektname']);
        foreach( explode( ';', $value ) as $val ) {
        	$val = trim( $val );
        	if( strlen($val)) $this->tags[] = 'index:projekt:grenzgang/'.md5( $val ).'/'.$val;
        }

        $value = trim( $this->data['Ort / Strecke']);
        foreach( explode( ';', $value ) as $val ) {
        	$val = trim( $val );
        	if( strlen($val)) $this->tags[] = 'index:ortstrecke:grenzgang/'.md5( $val ).'/'.$val;
        }

        $this->tags = array_unique( $this->tags );
        $this->cluster = array();
        foreach( $this->tags as $tag ) {
           if( preg_match( '/^[^\:]+:[^\:]+:[^\/]+\/[^\/]+\/(.*)$/', $tag, $matches )) {
           	$this->cluster[] = $matches[1];
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
            $as = $this->data['Autor (Nachname, Vorname'];
            foreach( explode( ';', $as ) as $a ) {
            	$a = trim( $a );
            	$this->authors[] = $a;
            }
			$this->authors = array_unique( $this->authors );
        }
        return $this->authors;
    }

    public function getLoans() {
    	return array();
    }

    public function getBarcode() {
    	return array();
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
        return null;
    }
   public function getContent() {
   		$text = @trim( $this->data['meta:fulltext'] );
   		if( strlen( $text )) return $text;
   		return null;
   }
   public function getCodes() { return array(); }

    public function getMetaACL() {
    	$access = array();
    	switch( $this->data['Berechtigung']) {
    		case 'login':
    			$access[] = 'global/user';
    			break;
   			case 'open':
   				$access[] = 'global/guest';
   				break;
   			case 'group':
   				$access[] = 'hgk/grenzgang';
   				break;
   	    }
    	return $access;
    }

    public function getContentACL() { return $this->getMetaACL(); }
    public function getPreviewACL() { return $this->getMetaACL(); }
	public function getLanguages() { return array(); }
	public function getIssues()  { return array(); }
	public function getCatalogs() { return array( $this->getSource() ); }

}

?>
