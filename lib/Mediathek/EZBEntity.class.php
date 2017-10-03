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

class EZBEntity extends SOLRSource {
    private $json = null;
    private $idprefix = 'ezb';
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
    
    function loadFromArray( array $row, string $idprefix ) {
        $this->reset();
        
        $this->data = $row;
        
        $this->id = $this->data['EZB-Id'];
        // $this->idprefix = $idprefix;
        
        //echo $this->id."\n";
        
        //var_dump( $this->data );
    }
    
    public function getID() {
        return $this->idprefix.str_pad($this->id, 9, '0', STR_PAD_LEFT ); 
    }
	
	public function getOriginalID() {
		return $this->id;
	}
    
    public function getSource() {
        return 'EZB';
    }
	
    public function getType() {
		return "EJournal";
	}

	
	public function getEmbedded() {
		return false;
	}

	public function getOpenAccess() {
		return false;
	}

	public function getLocations() {
		return array( 'online' );
	}
    
    public function getTitle() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $title = trim( $this->data['Titel'] );
        return $title;
    }
    
    public function getPublisher() {
        return array( $this->data['Verlag'] );
    }
    
    public function getYear() {
        return null;
    }
    
    public function getCity() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return null;
    }
    
	public function getTags() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        
        $this->tags = array();
        $this->cluster = array();
		
		
        foreach( explode( ';', preg_replace( '/(?<! u|-)[\.,]/', ';',$this->data['Fach'])) as $fach ) {
            $this->tags[] = 'index:medium:ezb/'.md5( trim( $fach ) ).'/'.trim( $fach );
            $this->cluster[] = trim( $fach );
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
            $this->licenses = array( 'restricted' );
        }
        return $this->licenses;
    }
    
    public function getURLs() {
        $urls = array();
        if( strlen( $this->data['bibliographische URL(s)']))
            foreach( explode( '***', $this->data['bibliographische URL(s)']) as $url )
                $urls[] = 'bibl:'.$url;
        if( strlen( $this->data['FrontdoorURL']))
            foreach( explode( '***', $this->data['FrontdoorURL']) as $url )
                $urls[] = 'frontdoor:'.$url;
        if( strlen( $this->data['Link zur Zeitschrift']))
            foreach( explode( '***', $this->data['Link zur Zeitschrift']) as $url )
                $urls[] = 'journal:'.$url;
        
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
        return null;
    }
    
   public function getContent() { return null; }    
   public function getCodes() {
        $codes = array();
        if( strlen( $this->data['E-ISSN'])) $codes[] = 'EISSN:'.$this->data['E-ISSN'];
        if( strlen( $this->data['P-ISSN'])) $codes[] = 'ISSN:'.$this->data['P-ISSN'];
        
        return $codes;
    }
   
    public function getMetaACL() { return array( 'global/guest' ); }
    public function getContentACL() {
        // todo: check, whether ppl are in fhnw network???
        return array(  );
    }
    public function getPreviewACL() { return array( 'location/fhnw' ); }

	public function getLanguages() { return array(); }
	public function getIssues()  { return array(); }
	public function getCatalogs() { return array( $this->getSource(), 'HGK' ); }
	
}

?>