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

class MarcEntity implements SOLRSource {
    private static $marctable = 'nebis_grab';
    private $xml = null;
    private $id = null;
    private $idprefix = null;
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
	private $codes = null;
	private $db = null;
    
    
    function __construct( $db ) {
		$this->db = $db;
    }

    private function reset() {
		$this->xml = null;
		$this->id = null;
		$this->idprefix = null;
		$this->barcode = null;
		$this->signature = null;
		$this->loans = null;
		$this->authors = null;
		$this->tags = null;
		$this->cluster = null;
		$this->licenses = null;
		$this->urls = null;
		$this->signatures = null;
		$this->online = false;
		$this->codes = null;
    }
    
	function loadFromXML( $xml, $id, $idprefix ) {
		$this->xml = new \DOMDocument( '1.0', 'UTF-8' );
		$this->xml->LoadXML( $xml );
        $id = str_pad($id, 9, '0', STR_PAD_LEFT );
        $this->id = $id;
        $this->idprefix = $idprefix;
	}
	
    function loadFromDatabase( $db, string $id, string $idprefix ) {
        $this->reset();
        
        $id = str_pad($id, 9, '0', STR_PAD_LEFT );
        $this->id = $id;
        $this->idprefix = $idprefix;
        $this->xml = new \DOMDocument( '1.0', 'UTF-8' );
        $rec = $this->xml->createElement( 'record' );
        
        $sql = "SELECT * FROM `".self::$marctable."` WHERE `sys` = ".$db->qstr( $id );
        $rs = $db->Execute( $sql );
        foreach( $rs as $row ) {            
            $tag = str_pad( $row['tag'], 5, ' ', STR_PAD_RIGHT );
            $value = $row['value'];
            $parts = explode( '|', $value );
            
            $el = $this->xml->createElement('datafield');
            $attr = $this->xml->createAttribute( 'tag' );
            $attr->value = substr( $tag, 0, 3 );
            $el->appendChild( $attr);
            $attr = $this->xml->createAttribute( 'ind1' );
            $attr->value = $tag[3];
            $el->appendChild( $attr);
            $attr = $this->xml->createAttribute( 'ind2' );
            $attr->value = $tag[4];
            $el->appendChild( $attr);
            
//            echo $value."\n";
            if( false === strpos( $value, '|' )) {
                $txt = $this->xml->createTextNode( trim( $value ));
                $el->appendChild( $txt );
            }
            else {
                foreach( $parts as $sub ) {
                    $sub = trim( $sub );
                    if( !strlen( $sub )) continue;
//                    echo "  ".$sub."\n";
                    $el2 = $this->xml->createElement( 'subfield' );
                    $attr = $this->xml->createAttribute( 'code' );
                    $attr->value = $sub[0];
                    $el2->appendChild( $attr);
                    $txt = $this->xml->createTextNode( trim( substr( $sub, 1 )));
                    $el2->appendChild( $txt );
                    $el->appendChild( $el2 );
                }
            }
            $rec->appendChild( $el );
        }
        $rs->Close();
        $this->xml->appendChild( $rec );
    }
    
    public function getID() {
        return $this->idprefix.$this->id; 
    }
    
    public function getOriginalID() {
        return $this->id; 
    }
	
    public function getSource() {
        return 'NEBIS';
    }

    public function getType() {
		foreach( $this->getCodes() as $code ) {
			if( strncmp( $code, 'ISSN', 4) == 0)
				return 'Journal';
		}
		return "Book";
	}

    public function getEmbedded() {
		return false;
	}

    
	public function getOpenAccess() {
		return false;
	}
	
	public function getLocations() {
		$locations = array();
		$sql = "SELECT DISTINCT marker FROM inventory2 i, ARC a WHERE i.itemid=a.Strichcode AND a.`Systemnummer`=".intval($this->id );
		$rs = $this->db->Execute( $sql );
		foreach( $rs as $row ) {
			$locations[] = 'E75:Kiste:'.trim( $row['marker'] );
		}
		$rs->Close();
		return $locations;
	}
	
	public function getGeneralNote() {
		$xpath = new \DOMXPath($this->xml);
        $generalnote = array();
        // 245 a
        $query = '/record/datafield[@tag="245" and @ind1=" "]/subfield[@code="a"]';
        $entries = $xpath->query( $query );
        if( $entries->length > 0 ) {
            $generalnote[] = $entries->item(0)->nodeValue;
        }
		return $generalnote;
	}
	
    public function getTitle() {
        if( $this->xml == null ) throw new \Exception( "no entity loaded" );
        $xpath = new \DOMXPath($this->xml);
        $title = "";
        // 245 a
        $query = '/record/datafield[@tag="245"]/subfield[@code="a"]';
        $entries = $xpath->query( $query );
        if( $entries->length > 0 ) {
            $title .= $entries->item(0)->nodeValue;
        }
        $query = '/record/datafield[@tag="245"]/subfield[@code="b"]';
        $entries = $xpath->query( $query );
        if( $entries->length > 0 ) {
            $title .= '. '.$entries->item(0)->nodeValue;
        }
/*		
        $query = '/record/datafield[@tag="245" and @ind1=" "]/subfield[@code="c"]';
        $entries = $xpath->query( $query );
        if( $entries->length > 0 ) {
           $title .= '. '.$entries->item(0)->nodeValue;
        }
*/
		if( strlen( $title ) == 0 ) {
			$query = '/record/datafield[@tag="490"]/subfield[@code="a"]';
			$entries = $xpath->query( $query );
			if( $entries->length > 0 ) {
				$title .= $entries->item(0)->nodeValue;
			}
			$query = '/record/datafield[@tag="490"]/subfield[@code="v"]';
			$entries = $xpath->query( $query );
			if( $entries->length > 0 ) {
				$title .= '. '.$entries->item(0)->nodeValue;
			}
			
		}
        return $title;
    }
    
    public function getPublisher() {
        if( $this->xml == null ) throw new \Exception( "no entity loaded" );
        $xpath = new \DOMXPath($this->xml);
        $query = '/record/datafield[@tag="260" and @ind1=" "]/subfield[@code="b"]';
        $entries = $xpath->query( $query );
        if( $entries->length > 0 ) {
            return $entries->item(0)->nodeValue;
        }
        return null;
    }
    
    public function getYear() {
        if( $this->xml == null ) throw new \Exception( "no entity loaded" );
        $xpath = new \DOMXPath($this->xml);
        $query = '/record/datafield[@tag="260" and @ind1=" "]/subfield[@code="c"]';
        $entries = $xpath->query( $query );
        if( $entries->length > 0 ) {
            return $entries->item(0)->nodeValue;
        }
        return null;
    }
    
    public function getCity() {
        if( $this->xml == null ) throw new \Exception( "no entity loaded" );
        $xpath = new \DOMXPath($this->xml);
        $query = '/record/datafield[@tag="260" and @ind1=" "]/subfield[@code="a"]';
        $entries = $xpath->query( $query );
        if( $entries->length > 0 ) {
            return $entries->item(0)->nodeValue;
        }
        return null;
    }
    
	public function getTags() {
		static $fields = array( 'subject' => array( 'personalname'=>'600',
													'corporatename'=>'610',
													'meetingname'=>'611',
													'uniformtitle'=>'630',
													'chronologicalterm'=>'648',
													'topicalterm'=>'650',
													'geographicname'=>'651',
													'facetettopicalterm'=>'654',
													'hierarchicalplacename'=>'662',
													'localsubjectaccess'=>'69X',
													),
								'index' => array( 'uncontrolled'=>'653',
													'genreform'=>'655',
													'occupation'=>'656',
													'function'=>'657',
													'curriculumobjective'=>'658',
													),
								);
        if( $this->xml == null ) throw new \Exception( "no entity loaded" );

        if( $this->tags ==  null ) {
			$this->tags = array();
			$h = array();
            $xpath = new \DOMXPath($this->xml);
			foreach( $fields as $btype=>$flds ) {
				$h[$btype] = array();
				foreach( $flds as $type=>$field ) {
					$h[$btype][$type] = array();
					$query = '/record/datafield[(@tag="'.$field.'")]';
					$entries = $xpath->query( $query );
					foreach( $entries as $entry ) {
						$ind1 = $entry->getAttribute( 'ind1' );
						$ind2 = $entry->getAttribute( 'ind2' );
						$subfields = array();
						if( $entry->hasChildNodes()) {
							foreach( $entry->childNodes as $subfield ) {
								if( $subfield->tagName != 'subfield') continue;
								$subfields[$subfield->getAttribute( 'code' )] = $subfield->nodeValue;
							}
						}
						$val = isset( $subfields['a'] ) ? $subfields['a'] : null;
						$idn = null;
						if( isset( $subfields['0'] )) $idn = $subfields['0'];
						elseif( isset( $subfields['1'] )) $idn = $subfields['1'];
						$lang = 'xxx';
						
						$src = null;
						// Thesaurus
						switch( $ind2 ) {
							case '0': // Library of Congress - Subject Headings
								$src = 'lcsh';
							break;
							case '1': // LC subject headings for children's literature
								$src = 'lccsh';
							break;
							case '2': // National Library of Medicine - Medical Subject Headings
								$src = 'nlmmesh';
							break;
							case '3': // LC National Agricultural Library name authority file
								$src = 'lcnalnaf';
							break;
							case '4': // not specified
								$src = 'unknown';
							break;
							case '5': // Library and Archives Canada - Canadian Subject Headings
								$src = 'laccsh';
							break;
							case '6': // Bibliothèque de l'Université Laval - Répertoire de vedettes-matière
								$src = 'bulrvm';
							break; 
							case '7':
								if( array_key_exists( '2', $subfields )) $src = $subfields['2'];
							break;
							default:
								$src = 'unknown';
							break;
						}
						
						if( !$val || !$src ) continue;
						if( !$idn ) $idn = md5( $val );
						$this->tags[] = "{$btype}:{$type}:{$src}/{$idn}/{$val}";
					}
				}
			}
			$this->tags = array_unique( $this->tags );
			$this->cluster = array();
			foreach( $this->tags as $tag ) {
				if( substr( $tag, 0, strlen( 'subject' )) == 'subject' ) {
					$ts = explode( '/', $tag );
					$this->cluster[] = $ts[count( $ts )-1];
				}
			}
			$this->cluster = array_unique( $this->cluster );
		}
        return $this->tags;
	}
	
    
    public function getCluster() {
        if( $this->cluster == null) $this->getTags();
        return $this->cluster;
    }
    
    public function getSignatures() {
        if( $this->xml == null ) throw new \Exception( "no entity loaded" );
        if( $this->signatures == null ) {
            $xpath = new \DOMXPath($this->xml);
            $this->signatures = array();
            // PST4        
            $query = '/record/datafield[(@tag="LOC" and @ind1="4")]'; //  and @ind2=" ")]';
            $entries = $xpath->query( $query );
            foreach( $entries as $entry ) {
                $lib = null;
                $sig = null;
                $online = null;
                if( $entry->hasChildNodes()) {
                    foreach( $entry->childNodes as $subfield ) {
                        if( $subfield->tagName != 'subfield') continue;
                        switch( $subfield->getAttribute( 'code' )) {
                            case 'b':
                                $lib = $subfield->nodeValue;
                                break;
                            case 'j':
                                $sig = $subfield->nodeValue;
                                break;
                            case '3':
                                //$online = $subfield->nodeValue;
                                break;
                        }
                    }
                    if( $sig != null && array_search( "nebis:{$lib}:{$sig}", $this->signatures ) === false ) $this->signatures[] = "nebis:{$lib}:{$sig}";
                    elseif( $online != null && array_search( "nebis:{$lib}:{$online}", $this->signatures ) === false  ) $this->signatures[] = "nebis:{$lib}:{$online}";
                }
                
            }
			if( count( $this->signatures ) == 0 ) {
				$sql = "SELECT DISTINCT `Signatur` AS sig, `Zweigstelle` as lib FROM ARC WHERE `Systemnummer`=".intval( $this->id );
				$rs = $this->db->Execute( $sql );
				foreach( $rs as $row ) {
					if( strlen( $row['sig']))
						$this->signatures[] = "nebis:{$row['lib']}:{$row['sig']}";
				}
				$rs->Close();
			}
			$sql = "SELECT DISTINCT Strichcode AS code, `Zweigstelle` as lib FROM ARC WHERE `Systemnummer`=".intval( $this->id );
			$rs = $this->db->Execute( $sql );
			foreach( $rs as $row ) {
				$this->signatures[] = "barcode:{$row['lib']}:{$row['code']}";
			}
			$rs->Close();
        }
        return $this->signatures;
    }
    
    public function getAuthors() {
        if( $this->xml == null ) throw new \Exception( "no entity loaded" );
        if( $this->authors == null ) {
            $xpath = new \DOMXPath($this->xml);
            $this->authors = array();
            // 60017        
            $query = '/record/datafield[(@tag="700" and (@ind1="1" or @ind1=" ") and @ind2=" ") or (@tag="100" and @ind1=" " and @ind2=" ")]';
            $entries = $xpath->query( $query );
            foreach( $entries as $entry ) {
                $lang = null;
                
                $val1 = null;
                $val2 = null;
                $val3 = null;
    
                if( $entry->hasChildNodes()) {
                    foreach( $entry->childNodes as $subfield ) {
                        if( $subfield->tagName != 'subfield') continue;
                        switch( $subfield->getAttribute( 'code' )) {
                            case 'a':
                                $val1 = $subfield->nodeValue;
                                break;
                            case 'd':
                                $val3 = $subfield->nodeValue;
                                break;
                            case '1':
                            case '8':
                                $val2 = $subfield->nodeValue;
                                break;
                            case '2':
                                $type = $subfield->nodeValue;
                                break;
                            case '9':
                                $lang = $subfield->nodeValue;
                                break;
                        }
                    }
                }
                if( $val1 == null ) continue;
                $result = "{$val1}";
/*                
                if( $val3 != null )
                   $result .= "/{$val3}";
                if( $val2 != null )
                    $result .= "/{$val2}";
*/
                $this->authors[] = $result;
            }
        }
		if( count($this->authors ) == 0 ) {
	        $query = '/record/datafield[@tag="245" and @ind1=" "]/subfield[@code="c"]';
			$entries = $xpath->query( $query );
			if( $entries->length > 0 ) {
			   $this->authors[] = $entries->item(0)->nodeValue;
			}
		}
        return $this->authors;        
    }
    
    public function getLoans() {
        if( $this->loans == null ) {
            $this->loans = array();
            $sql = "SELECT DISTINCT `Benutzer-ID` AS uid FROM ARC AS a, loans AS l WHERE a.Strichcode=l.Strichcode AND a.`Systemnummer`=".$this->db->qstr( ltrim( $this->id, '0' ));
//            echo $sql."\n";
            $rs = $this->db->Execute( $sql );
            foreach( $rs as $row ) {
                $this->loans[] = $row['uid'];
            }
            $rs->Close();
        }
        return $this->loans;
    }
    
    public function getBarcode() {
        if( $this->barcode == null ) {
            $sql = "SELECT DISTINCT `Signatur` AS sig, `Strichcode` AS bc FROM `ARC` WHERE `Systemnummer`=".$this->db->qstr( ltrim( $id, '0' ));
            $row = $db->getRow( $sql );
            if( $row ) {
                $this->barcode = $row['bc'];
                $this->signature = $row['sig'];
            }
        }
        return $this->barcode;
    }
    
    public function getSignature() {
        if( $this->signature == null ) {
            $sql = "SELECT DISTINCT `Erste Signatur (Beschr.)` AS sig, `Strichcode` AS bc FROM `ARC` WHERE `Satznummer Titel`=".$this->db->qstr( ltrim( $id, '0' ));
            $row = $db->getRow( $sql );
            if( $row ) {
                $this->barcode = $row['bc'];
                $this->signature = $row['sig'];
            }
        }
        return $this->signature;
    }
    
    public function getLicenses() {
        if( $this->licenses == null ) {
            $this->licenses = array( 'restricted' );
        }
        return $this->licenses;
    }
    
    public function getURLs() {
        if( $this->urls == null ) {
           $xpath = new \DOMXPath($this->xml);
            $this->urls = array();
            // 60017        
            $query = '/record/datafield[(@tag="856")]';
            $entries = $xpath->query( $query );
            foreach( $entries as $entry ) {
                $url = null;
                $note = null;
    
                if( $entry->hasChildNodes()) {
                    foreach( $entry->childNodes as $subfield ) {
                        if( $subfield->tagName != 'subfield') continue;
                        switch( $subfield->getAttribute( 'code' )) {
                            case 'u':
                                $url = $subfield->nodeValue;
                                break;
                            case 'z':
                                $note = $subfield->nodeValue;
                                break;
                        }
                    }
                }
                if( $url == null ) continue;
                $type = 'unknown';
                if( preg_match( "/SFX/", $note ))
                    $type = 'SFX';
                $this->urls[] = "{$type}:{$url}";
                $this->online = true;
            }
        }
        return $this->urls;
    }
    
    public function getSys() {
        return $this->id;
    }
    
    public function getMeta() {
        return $this->xml->saveXML();
    }
    
    public function getOnline() {
        return $this->online;
    }

    public function getCodes() {
		if( $this->codes != null ) return $this->codes;
        $codes = array();
        $xpath = new \DOMXPath($this->xml);
         // 60017        
         $query = '/record/datafield[(@tag="020" or @tag="022")]';
         $entries = $xpath->query( $query );
         foreach( $entries as $entry ) {
             $code = null;
 
             if( $entry->hasChildNodes()) {
                 foreach( $entry->childNodes as $subfield ) {
                     if( $subfield->tagName != 'subfield') continue;
                     switch( $subfield->getAttribute( 'code' )) {
                         case 'a':
                             $code = $subfield->nodeValue;
                             break;
                     }
                 }
             }
             if( $code == null ) continue;
             $codes[] = ($entry->getAttribute('tag') == '020' ? 'ISBN:' : 'ISSN:' ).$code; 
         }
		 $this->codes = $codes;
         return $codes;
    }

    public function getAbstract() { return null; }
    public function getContent() { return null; }
    public function getMetaACL() { return array( 'global/guest' ); }
    public function getContentACL() { return array(); }
    public function getPreviewACL() { return array(); }}

?>