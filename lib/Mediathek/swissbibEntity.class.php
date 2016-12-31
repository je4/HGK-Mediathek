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

class swissbibEntity extends SOLRSource {
	private $node;
	private $data = null;
	
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
    private $online = null;
	private $codes = null;
	private $db = null;
	private $sourceids = null;
    
	
	static function find3( $data, $ind2 ) {
		if( $ind2 == null ) {
			$ret = array();
			foreach( $data as $d ) {
				$ret = array_merge( $d, $ret );
			}
			return $ret;
		}
		else {
			if( !array_key_exists( $ind2, $data )) return array();
			return ( $data[$ind2] );
		}
	}

	static function find2( $data, $ind1, $ind2 ) {
//		echo "find2( ".print_r( $data, true ).", $ind1, $ind2 )\n";
		if( $ind1 == null ) {
			$ret = array();
			foreach( $data as $d ) {
				$r = self::find3( $d, $ind2 );
				$ret = array_merge( $r, $ret );
			}
			return $ret;
		}
		else {
			if( !array_key_exists( $ind1, $data )) return array();
			return ( self::find3( $data[$ind1], $ind2 ));
		}
	}

	function findField( $tag, $ind1=null, $ind2=null ) {
//		echo "findField( $tag, $ind1, $ind2 )\n";
		if( !array_key_exists( $tag, $this->data )) return array();
		return self::find2( $this->data[$tag], $ind1, $ind2 );
	}
    
	function getAll( $tag, $ind1, $ind2, $code ) {
//		echo "getAll( $tag, $ind1, $ind2, $code )\n";
		$arr = array();
		$result = $this->findField( $tag, $ind1, $ind2 );
//		print_r( $result );
		foreach( $result as $r ) {
			if( array_key_exists( $code, $r )) 
				$arr = array_merge( $r[$code], $arr );
		}
//		print_r( $arr );
		return $arr;		
	}

	function getOne( $tag, $ind1, $ind2, $code ) {
		$arr = $this->getAll( $tag, $ind1, $ind2, $code );
		if( count( $arr ) == 0 ) return null;
		return $arr[0];
	}
	
    function __construct() {
    }

    private function reset() {
		$this->data = null;
		
		$this->xml = null;
		$this->id = null;
		$this->idprefix = null;
		$this->node = null;
		$this->data = null;
		$this->barcode = null;
		$this->signature = null;
		$this->loans = null;
		$this->authors = null;
		$this->tags = null;
		$this->cluster = null;
		$this->licenses = null;
		$this->urls = null;
		$this->signatures = null;
		$this->online = null;
		$this->codes = null;
		$this->sourceids = null;
		
    }

	function loadNode( $id, $node, $idprefix ) {
		$this->reset();
        $this->id = $id;
        $this->idprefix = $idprefix;
		$this->node = $node;
		$this->data = array();
		
		if( $this->node == null ) throw new \Exception( "no entity loaded" );
        $xpath = new \DOMXPath($this->node->ownerDocument);
		$xpath->registerNamespace( 'mx', 'http://www.loc.gov/MARC21/slim' );
        $query = '/record/metadata/mx:record/mx:datafield';
        $entries = $xpath->query( $query );
		foreach( $entries as $entry ) {
			$tag = $entry->getAttribute( 'tag' );
			$ind1 = $entry->getAttribute( 'ind1' );
			$ind2 = $entry->getAttribute( 'ind2' );
			if( !array_key_exists( $tag, $this->data )) $this->data[$tag] = array();
			if( !array_key_exists( $ind1, $this->data[$tag] )) $this->data[$tag][$ind1] = array();
			if( !array_key_exists( $ind2, $this->data[$tag][$ind1] )) $this->data[$tag][$ind1][$ind2] = array();
			$elem = array();
			foreach( $entry->childNodes as $child ) {
				if( !(is_a( $child, 'DOMElement' ) && $child->tagName == 'mx:subfield' )) continue;
				$code = $child->getAttribute( 'code' );
				if( !array_key_exists( $code, $elem )) $elem[$code] = array();
				$elem[$code][] = $child->textContent;
			}
			$this->data[$tag][$ind1][$ind2][] = $elem;
		}
		//var_dump( $this->data );
	}
	
    public function getID() {
        return $this->idprefix.'-'.$this->id; 
    }
    
    public function getOriginalID() {
        return $this->id; 
    }
	
    public function getSource() {
        return 'swissbib';
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
		return $locations;
	}
	
	public function getGeneralNote() {
		return null;
		
		$generalnote = $this->getAll( '245', null, null, 'a' );
		return $generalnote;
	}
	
    public function getTitle() {
		$title = trim( implode( ' / ', $r= $this->getAll( '245', null, null, 'a' )).' '.implode( ' / ', $r= $this->getAll( '245', null, null, 'b' )), '/:-., ' );
		return $title;
    }
    
    public function getPublisher() {
		$publishers = array();
		$t = $this->getAll( '260', ' ', null, 'b' );
		if( is_array( $t )) foreach( $t as $p ) $publishers[] = trim( $p, '/:-., ' ); 
		return $publishers;
    }
    
    public function getYear() {
		$year = trim( $r = $this->getOne( '260', null, null, 'c' ), '/:-., ' );
		return $year;	
    }
    
    public function getCity() {
		$city = trim( $this->getOne( '260', ' ', null, 'a' ), '/:-., ' );
		return $city;
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
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        if( $this->tags ==  null ) {
			$this->tags = array();
			$h = array();
			foreach( $fields as $btype=>$flds ) {
				$h[$btype] = array();
				foreach( $flds as $type=>$field ) {
					if( !array_key_exists( $field, $this->data )) continue;
					foreach( $this->data[$field] as $ind1=>$val1 ) {
						foreach( $val1 as $ind2=>$val2 ) {
							foreach( $val2 as $codes ) {
								$val = array_key_exists( 'a', $codes ) ? $codes['a'] : null;
								$idn = null;
								if( array_key_exists( '0', $codes )) $idn = $codes['0'];
								elseif( array_key_exists( '1', $codes )) $idn = $codes['1'];
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
									case '6': // Biblioth�que de l'Universit� Laval - R�pertoire de vedettes-mati�re
										$src = 'bulrvm';
									break; 
									case '7':
										//if( array_key_exists( '2', $subfields )) $src = $subfields['2'];
									break;
									default:
										$src = 'unknown';
									break;
								}
								if( !$val || !$src ) continue;
								$vs = implode( '-', $val );
								if( !$idn ) $idn = md5( $vs );
								$this->tags[] = "{$btype}:{$type}:{$src}/{$idn}/{$vs}";
							}
						}
					}
				}
			}
			$this->tags = array_unique( $this->tags );
			$this->cluster = array();
			foreach( $this->tags as $tag ) {
				//echo $tag."---\n";
				if( preg_match( '/^(subject|index)[^\/]+\/[^\/]+\/(.*)$/', $tag, $matches )) {
					$this->cluster[] = $matches[2];
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
        if( $this->signatures == null ) {
        	$this->signatures = array();
        	//print_r( $this->data );
        	$rs = $this->findField( '949' );
        	foreach( $rs as $r ) {
        		$lib = $sig = $verbund = null;
				foreach( $r as $code=>$val ) {
					switch( $code ) {
						case 'F':
							$lib = implode( '/', $val );
							break;
						case 'j':
							$sig = implode( '/', $val );
							break;
						case 'B':
							$verbund = implode( '/', $val );
							break;
					}
					if( $sig != null ) $this->signatures[] = "signature:{$verbund}:{$lib}:{$sig}";
					elseif( $this->getOnline()) $this->signatures[] = "signature:{$verbund}:{$lib}:online";
					
					$this->signatures = array_unique( $this->signatures );
				}
        	}

        	if( $verbund == 'NEBIS' && $lib == 'E75' ) {
				$sql = "SELECT DISTINCT Strichcode AS code, `Zweigstelle` as lib FROM ARC WHERE `Systemnummer`=".intval( $this->id );
				$rs = $this->db->Execute( $sql );
				foreach( $rs as $row ) {
					$this->signatures[] = "barcode:{$verbund}:{$lib}:{$row['code']}";
				}
				$rs->Close();
        	}
        }
        return $this->signatures;
        
    }
    
    public function getAuthors() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        if( $this->authors == null ) {
			$this->authors = array();
			
			foreach( array( '100', '700') as $fld ) {
				$tmp = $this->findField( $fld, '1', ' ' );
				$lang = null;
				$val1 = null;
				$val2 = null;
				$val3 = null;
				foreach( $tmp as $t )  {
					foreach( $t as $code=>$val ) {
						switch( strtolower( $code )) {
							case 'a':
								$val1 = implode( ';', $val );
								break;
							case 'd':
								$val3 = implode( ';', $val );
								break;
							case '1':
							case '8':
								$val2 = implode( ';', $val );
								break;
							case '2':
								$type = implode( ';', $val );
								break;
							case '9':
								$lang = implode( ';', $val );
								break;
						}
						
					}
				}
				
				if( $val1 == null ) continue;
				$result = ($val3 != null ? "{$val3}, ": '' )."{$val1}";
				$this->authors[] = $result;			
			}
			if( count($this->authors ) == 0 ) {
				$this->authors = array_merge( $this->authors, $this->getAll( '245', null, null, 'c' ));
			}
        }
        return $this->authors;        
    }
    
    public function getLoans() {
    	return array();
    	
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
    	return array();
    	
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
    
    public function getLicenses() {
        if( $this->licenses == null ) {
            $this->licenses = array( 'restricted' );
        }
        return $this->licenses;
    }
    
    public function getURLs() {
        if( $this->urls == null ) {
        	$this->urls = array();
			$urls = $this->findField( '856' );
			$val = null;
			$note = null;
			foreach( $urls as $url ) {
				foreach( $url as $code=>$v ) {
					switch( $code ) {
						case 'u':
							$val = implode( ';', $v );
							break;
						case 'z':
							$note = implode( ';', $v );
							break;
					}
				}
				if( $val == null ) continue;
				$type = 'unknown';
				if( preg_match( "/SFX/", $note )) {
					$type = 'SFX';
				}
				$this->urls[] = "{$type}:{$val}";
				//$this->online = true;
			}
	    }
        return $this->urls;
    }
    
    public function getSys() {
        return $this->id;
    }
    
    public function getMeta() {
        return ( $this->node );
    }
    
    public function getOnline() {
    	if( $this->online === null ) {
    		$this->online = false;
    		$phys = $this->getAll( '300', null, null, 'a' );
    		foreach( $phys as $p ) {
    			if( strpos( strtolower( $p ), 'online') !== false )
    				$this->online = true;
    		}
    	}
        return $this->online;
    }

    public function getCodes() {

    	$this->codes = array();
//    	var_dump( $this->data );
    	 
        foreach( array( '020', '022' ) as $tag ) {
	        $flds = $this->findField( $tag, ' ', ' ' );
	        $val = null;
	        $type = null;
	        foreach( $flds as $fld ) {
	        	   foreach( $fld as $code=>$v ) {
		        	// var_dump( $v );
		        	switch( $code ) {
		        		case 'a':
		        			$val = implode( ';', $v );
		        			break;
		        		case '2':
		        			$type = implode( ';', $v );
		        			break;
			       	}
        	   }
	        }
	        if( $val === null ) continue;
	         if( preg_match( '/$([^\(\)]+)\((.*)\)/', $val, $matches )) {
	        	$val = trim( $matches[1] );
	        	$type = trim( $matches[2] );
	        }
	        if( $type == null ) $type = ($tag == '020' ? 'ISBN': 'ISSN' );
	        if( $type != null ) $type = strtoupper( $type );
	        $this->codes[] = "{$type}:{$val}";
	   }
	   $this->codes = array_unique( $this->codes );
	   return $this->codes;
    }

	public function getCategories() {
		$categories = parent::getCategories();
		foreach( $this->getSignatures() as $sig ) {
			$s = explode( ':', $sig );
			if( count( $s ) >= 4 ) {
				$categories[] = $s[0].'!!'.$s[1].'!!'.$s[2];
			}
		}
		return $categories;
	}	
	
    public function getAbstract() { return null; }
    public function getContent() { return null; }
    public function getMetaACL() { return array( 'global/guest' ); }
    public function getContentACL() { return array(); }
    public function getPreviewACL() { return array(); }
	public function getLanguages() { return array(); }
	public function getIssues()  { return array(); }
	
	public function getSourceIDs() {
		if( $this->sourceids == null ) {
			$this->sourceids = array();
			$flds = $this->findField( '035' );
			// var_dump( $this->data );
			foreach( $flds as $fld ) {
				foreach( $fld as $code=>$v ) {
					// var_dump( $v );
					switch( $code ) {
						case 'a':
							$this->sourceids[] = implode( ';', $v );
							break;
					}
				}
			}
		}
		return $this->sourceids;
	}
	
	public function getSourceID( $lib ) {
		foreach( $this->getSourceIDs() as $id ) {
			if( preg_match( "/^\({$lib}\)(.*)$/", $id, $matches )) {
				return $matches[1];
			}
		}
		return null;
	}
}
?>