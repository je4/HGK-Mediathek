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

	private static $mapbib = null;

	private $record = null;
	private $infunction = false;
    private $xml = null;
    private static $idprefix = 'swissbib';
    private $barcode = null;
    private $locations = null;
    private $libCodes = null;
    private $loans = null;
    private $authors = null;
    private $tags = null;
    private $cluster = null;
    private $licenses = null;
    private $urls = null;
    private $urls_notes = null;
    private $signatures = null;
		private $libs = null;
		private $libsigcode = null;
    private $online = null;
    private $openaccess = null;
	private $codes = null;
	protected $db = null;
	private $sourceids = null;
	private static $typeList = array(
			'BK01'=>'Article',
			'CL01'=>'Collection',
			'CL02'=>'Collection',
			'BK02'=>'Book',
			'BK03'=>'Manuscript',
			'CR01'=>'Ressource',
			'CR02'=>'Ressource',
			'CR03'=>'Ressource',
			'MU01'=>'Notes',
			'MU02'=>'Notes-Manuscript',
			'MU03'=>'Sound',
			'MU04'=>'Music',
			'MP01'=>'Map',
			'MP02'=>'Map-Manuscript',
			'CF01'=>'File',
			'VM01'=>'Projectable',
			'VM02'=>'2D',
			'VM03'=>'Kit',
			'VM04'=>'3D',
			'MX01'=>'Materials',
			'CL05'=>'unknown',
	);

	private static $bibs = array(
			'E44'=>array('FHNW-Bib'),
			'E50'=>array('FHNW-Bib'),
			'E60'=>array('FHNW-Bib'),
			'E48'=>array('FHNW-Bib'),
			'E75'=>array('FHNW-Bib', 'Kunst-Bib Region Basel/Aargau', 'Kunsthochschul-Bibs' ),
			'N01'=>array('FHNW-Bib'),
			'N02'=>array('FHNW-Bib'),
			'N03'=>array('FHNW-Bib'),
			'E39'=>array('FHNW-Bib'),
			'N10'=>array('FHNW-Bib'),
			'A332'=>array('FHNW-Bib', 'Kunsthochschul-Bibs'),
			'A298'=>array('Kunst-Bib Region Basel/Aargau'),
			'A214'=>array('Kunst-Bib Region Basel/Aargau'),
			'E65'=>array('Kunsthochschul-Bibs'),
			'B420'=>array('Kunsthochschul-Bibs'),
			'RE11023'=>array('Kunsthochschul-Bibs'),
			'RE31006'=>array('Kunsthochschul-Bibs'),
			'LUHGK'=>array('Kunsthochschul-Bibs'),
			'RE61078'=>array('Kunsthochschul-Bibs'),
			'LOASP'=>array('Kunsthochschul-Bibs'),
			'E34'=>array('Kunsthochschul-Bibs'),
	);

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
//		echo "find2( data, $ind1, $ind2 )\n";
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

	function __construct( \ADOConnection $db ) {
		global $config;

		$this->db = $db;
		if( swissbibEntity::$mapbib == null ) {
			swissbibEntity::$mapbib = json_decode( file_get_contents( $config['mapbib'] ), true );
		}
	}

	public function loadFromDoc( $doc) {
		$this->reset();
		$xml = \Normalizer::normalize(( string )gzdecode( base64_decode( $doc->metagz )));
		$this->loadNode( $doc->originalid, new OAIPMHRecord( $xml ), null );
	}


    public function reset() {
		parent::reset();
		$this->infunction = false;
		$this->record = null;
		$this->xml = null;
		$this->node = null;
		$this->barcode = null;
		$this->locations = null;
		$this->libCodes = null;
		$this->loans = null;
		$this->authors = null;
		$this->tags = null;
		$this->cluster = null;
		$this->licenses = null;
		$this->urls = null;
		$this->signatures = null;
		$this->libCodes = null;
		$this->libs = null;
		$this->online = null;
		$this->openaccess = null;
		$this->codes = null;
		$this->sourceids = null;
		$this->libsigcode = null;
    }

	function loadNode( $id, $record, $idprefix ) {
		$this->reset();
        $this->id = $id;
        // $this->idprefix = $idprefix;
        $this->record = $record;

        $metadata = $record->getMetadata();
        $marc = null;
        foreach( $metadata->childNodes as $child ) {
        	if( is_a( $child, 'DOMElement' ) && $child->tagName == 'mx:record' ) {
        		$marc = $child;
        	}
        }

		$this->data = array();

		if( $marc == null ) throw new \Exception( "no entity loaded" );
        $xpath = new \DOMXPath( $marc->ownerDocument );
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
				$elem[$code][] = \Normalizer::normalize($child->textContent);
			}
			$this->data[$tag][$ind1][$ind2][] = $elem;
		}
		//var_dump( $this->data );
	}

	public function getData() {
		return $this->data;
	}

	public function getXML() {
		return $this->record->getXML();
	}

    public function getID() {
        return self::$idprefix.'-'.$this->id;
    }

    public function getOriginalID() {
        return $this->id;
    }

    public function getSource() {
        return 'swissbib';
    }

    public function getType() {
/*
			foreach( $this->getCatalogs() as $cat ) {
				if( $cat == 'FHNWeMedien' ) return 'emedia';
			}
*/
		$code = $this->getOne( '898', null, null, 'a' );
		$primary = substr( $code, 0, 4 );
		$tertiary = substr( $code, 6, 2 );
		if( $tertiary == '53' ) {
				$this->online = true;
			}
		if( array_key_exists( $primary, swissbibEntity::$typeList )) {
			$type = swissbibEntity::$typeList[$primary];
			switch( strtolower( $type )) {
				case 'book':
					if( $this->getOnline()) return $this->getOpenAccess() ? 'OABook':'eMedia';
					return 'book';
				break;
				case 'journal':
					if( $this->getOnline()) return $this->getOpenAccess() ? 'OAJournal':'eJournal';
					return 'journal';
				break;
			}
		}
		foreach( $this->getCodes() as $code ) {
			$x = explode( ':', $code );
			switch( strtolower( $x[0] )) {
				case 'issn':
					return 'journal';
				break;
				case 'isbn':
					if( $this->getOnline() ) return $this->getOpenAccess() ? 'eOpenAccess':'eMedia';
					return 'book';
				break;
			}
		}
		return 'unknown';
	}

    public function getEmbedded() {
		return false;
	}


	public function getOpenAccess() {
		$licenses = $this->getLicenses();
		if( $this->openaccess !== null ) return $this->openaccess;
		$this->openaccess = false;
		foreach( $licenses as $lic ) {
			switch( strtolower( $lic )) {
				case 'open access':
					$this->openaccess = true;
					break;
			}
		}
		return $this->openaccess;
	}

	public function getLocations() {
		if( $this->locations == null ) $this->getSignatures();
		return $this->locations;
	}

	public function getPhysicalDescription() {
		$generalnote = $this->getAll( '300', null, null, 'a' );
		return $generalnote;
	}

	public function getGeneralNote() {
		return array();

		$generalnote = $this->getAll( '245', null, null, 'a' );
		return $generalnote;
	}

    public function getTitle() {
    	$title = trim( implode( ' / ', $r= $this->getAll( '245', null, null, 'a' ))
				.' '.implode( ' / ', $r= $this->getAll( '245', null, null, 'b' )), '/:-., ' );
    	if( strlen( $title ) == 0 ) {
    		$title .= trim( implode( ' / ', $r= $this->getAll( '490', null, null, 'a' ))
    				.' '.implode( ' / ', $r= $this->getAll( '490', null, null, 'v' )));
    	}
		return trim( $title );
    }

    public function getPublisher() {
		$publishers = array();
		foreach( array( '260', '264' ) as $fld ) {
			$t = $this->getAll( $fld, null, null, 'b' );
			if( is_array( $t )) foreach( $t as $p ) $publishers[] = trim( $p, '/:-., ' );
		}
		return $publishers;
    }

    public function getYear() {
		$year = null;
		foreach( array( '260', '264' ) as $fld ) {
			$t = trim( $this->getOne( $fld, ' ', null, 'c' ), '[]/:-., ' );
			if( strlen( $t )) $year = $t;
		}
		return $year;
		}

    public function getCity() {
    	$city = null;
    	foreach( array( '260', '264' ) as $fld ) {
    		$t = trim( $this->getOne( $fld, ' ', null, 'a' ), '/:-., ' );
    		if( strlen( $t )) $city = $t;
    	}
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
													'localsubjectaccess'=>'691',

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
								$val = array_key_exists( 'a', $codes ) ? implode( '/', $codes['a'] ) : null;
								$idn = null;
								if( array_key_exists( '0', $codes )) $idn = implode( '/', $codes['0'] );
								elseif( array_key_exists( '1', $codes )) $idn = implode( '/', $codes['1'] );
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
										if( array_key_exists( '2', $codes )) $src = implode( '/', $codes['2'] );
									break;
									default:
										$src = 'unknown';
									break;
								}
								if( !$val || !$src ) continue;
								$val = trim( $val, '/:-., ' );
								if( !$idn ) $idn = md5( $val );
								$this->tags[] = "{$btype}:{$type}:{$src}/{$idn}/{$val}";
							}
						}
					}
				}
			}

			$fld909_7 = $this->findField('909', ' ', '7');
			foreach( $fld909_7 as $sub ) {
				if( !array_key_exists( 'a', $sub )) continue;
				if( !is_array( $sub['a'])) continue;
				if( count( $sub['a']) == 0) continue;
				if( preg_match( '/([A-Z0-9]{3})-([0-9]{4})([0-9]{2})([0-9]{2})/', $sub['a'][0], $matches )) {
					try {
						$d = GregorianToJD(intval($matches[3]), intval($matches[4]), intval($matches[2]));
						$now = UnixToJD();
						$diff = $now - $d;
						if( $diff <= 31 ) $this->tags[] = "acquisition:31:{$matches[1]}";
						if( $diff <= 100 ) $this->tags[] = "acquisition:100:{$matches[1]}";
					}
					catch( \Exception $ex ) {
						echo $ex."\n";
					}
				}
			}

			$this->tags = array_unique( $this->tags );
			$this->cluster = array();
			foreach( $this->tags as $tag ) {
				//echo $tag."---\n";
				if( preg_match( '/^(subject:topicalterm|subject:localsubjectaccess:gnd|index:genreform)[^\/]+\/[^\/]+\/(.*)$/', $tag, $matches )) {
					$this->cluster[] = $matches[2];
				}
				if( preg_match( '/^(acquisition):([0-9]+):([A-Z0-9]{3})$/', $tag, $matches )) {
					$this->cluster[] = "{$matches[1]}{$matches[2]}_{$matches[3]}";
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
        if( $this->signatures !== null ) return $this->signatures;
					if( $this->infunction ) {
						debug_print_backtrace();
						die();
					}
					$this->infunction = true;
        	$this->signatures = array();
        	$this->locations = array();
        	$this->libCodes = array();
					$this->libsigcode = array();
					$this->libs = array();
        	//print_r( $this->data );
        	foreach( array( '949', '852' ) as $fld ) {
	        	$rs = $this->findField( $fld );
	        	foreach( $rs as $r ) {
		        		$lib = null;
		        		$sig = null;
		        		$verbund = null;
		        		$bar = null;
		        		$full = null;
								$loc = null;
								foreach( $r as $code=>$val ) {
									//continue;
									switch( $code ) {
										case 'O':
											$full = trim( implode( '/', $val ));
											break;
										case 'b':
											$lib = trim( implode( '/', $val ));
											break;
										case 'j':
											$sig = trim( implode( '/', $val ));
											break;
										case 'B':
											$verbund = trim( implode( '/', $val ));
											break;
										case 'p':
											$bar = trim( implode( '/', $val ));
										break;
										case 'c':
											$loc = trim( implode( '/', $val ));
										break;
										default:
											break;
									}
								}
								if( $verbund != null && $lib != null ) {
									if( $loc ) $this->locations[] = "{$verbund}:{$lib}:{$loc}";
									if( $sig != null ) $this->signatures[] = "signature:{$verbund}:{$lib}:{$sig}";
									//if( $this->getOnline()) $this->signatures[] = "signature:{$verbund}:{$lib}:online";
									if( $bar != null ) {
										$this->signatures[] = "barcode:{$verbund}:{$lib}:{$bar}";
										$sql = "SELECT marker FROM location WHERE verbund=".$this->db->qstr( $verbund ).
											" AND library=".$this->db->qstr( $lib ).
											" AND itemid=".$this->db->qstr( $bar );
										$kiste = $this->db->GetOne( $sql );
										if( $kiste ) {
											$this->locations[] = "{$verbund}:{$lib}:{$kiste}";
										}

									}
									$this->libsigcode[] = array(
										  'verbund'=>$verbund
								 		, 'lib'=>$lib
										, 'sig'=>$sig
									  , 'bar'=>$bar
									);
								}
								if( $lib != null ) $this->libCodes[] = $lib;
								if( $lib != null && $full != null ) $this->libs[$lib] = $full;
							}
        	}
        $this->libCodes = array_unique( $this->libCodes );
        return $this->signatures;
    }

		public function getLibSigCodes() {
			if( $this->libsigcode == null ) {
				$this->getSignatures();
			}
			return $this->libsigcode;
		}

		public function getSigCode( $verbund, $lib ) {
			if( $this->libsigcode == null ) {
				$this->getSignatures();
			}
			$result = array();
			foreach( $this->libsigcode as $lsc ) {
				if( $lsc['verbund'] == $verbund && $lsc['lib'] == $lib ) {
					$result[$lsc['bar']] = $lsc['sig'];
				}
			}
			return $result;
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
				$val4 = null;
				foreach( $tmp as $t )  {
					foreach( $t as $code=>$val ) {
						switch( $code ) {
							case '0':
								break;
							case 'a':
								$val1 = implode( ';', $val );
									break;
							case 'd':
								$val3 = implode( ';', $val );
								break;
							case 'D':
								$val4 = implode( ';', $val );
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
					if( $val1 == null ) continue;
					$result = trim( $val1.($val4 != null ? ", {$val4}": '' )); //.($val3 != null ? "({$val3})":"");
					if( preg_match( '/^\[(.*)\]$/', $result, $matches )) {
						foreach( explode( ',', $matches[1] ) as $author ) {
							$this->authors[] = trim( $author );
						}
					}
					else {
						$this->authors[] = $result;
					}
				}

			}
			if( count($this->authors ) == 0 ) {
				$as = $this->getAll( '245', null, null, 'c' );
				foreach( $as as $result ) {
					if( preg_match( '/^\[(.*)\]$/', $result, $matches )) {
						foreach( explode( ',', $matches[1] ) as $author ) {
							$this->authors[] = trim( $author );
						}
					}
					else {
						$this->authors[] = $result;
					}
				}
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
                //$this->signature = $row['sig'];
            }
        }
        return $this->barcode;
    }

    public function getLicenses() {
        if( $this->licenses !== null ) return array_unique( $this->licenses );

        	$this->licenses = array();
        	foreach( $this->getAll( '506', '0', null, 'a' ) as $lic ){
        		$this->licenses[] = trim( $lic, '/:-., ' );
					 }
	      		foreach( $this->getAll( '540', null, null, 'a' ) as $lic ) {
	      			if( preg_match( '/Open Access/i', $lic )) {
	      				$this->openaccess = true;
	      				if( preg_match( '/(http?:\/\/[^ \)]+)/i', $lic, $matches )) {
	      					$this->licenses[] = trim( $matches[1], '/:-., ' );
	      				}
	      				else $this->licenses[] = 'open access';
	      			}
        		}
						foreach( $this->getAll( '856', null, null, 'z' ) as $lic ) {
        			if( preg_match( '/Online via e-rara\.ch/i', $lic )) {
        				$this->openaccess = true;
								$this->licenses[] = 'https://creativecommons.org/licenses/by-sa/4.0/';
        			}
        		}
						foreach( $this->getAll( '909', null, 4, 'f' ) as $dgoa ) {
        			if( preg_match( '/Open ?Access/i', $dgoa )) {
        				$this->openaccess = true;
								$this->licenses[] = $dgoa;
        			}
        		}

        		foreach( $this->getURLs() as $url ) {
        			if( strncmp( 'eperiodica:', $url, 11 ) == 0 ) {
        				$this->licenses[] = 'http://www.e-periodica.ch/digbib/about3';
        				$this->openaccess = true;
        			}
        		}

        		if( count( $this->licenses ) == 0 ) $this->licenses = array( 'restricted' );

        return array_unique( $this->licenses );
    }

    public function getURLs() {
        if( $this->urls == null ) {
        	$this->urls = array();
        	$this->urls_notes = array();
        	 foreach( array( '856', '950', '956') as $fld ) {
				$urls = $this->findField( $fld );
				foreach( $urls as $url ) {
					$val = null;
					$note = null;
					$type = 'unknown';
					$org = null;
					foreach( $url as $code=>$v ) {
						switch( $code ) {
							case 'u':
								$val = implode( ';', $v );
								break;
							case 'z':
							case 'y':
								$note = implode( ';', $v );
								break;
							case '3':
								$type = strtolower( implode( ';', $v ));
								break;
							case 'P':
								$org = strtolower( implode( ';', $v ));
								break;
						}
					}
					if( $val == null ) continue;
					if( $org != null && $org != '856' ) continue;

					if( preg_match( "/SFX/", $note )) {
						$type = 'sfx';
					}
					if( preg_match( "/Cover/", $note )) {
						$type = 'cover';
					}
					if( preg_match( "/DOI/", $note ) || preg_match( "/doi.org/", $val )) {
						$type = 'doi';
					}
					if( preg_match( "/Inhalt/", $note )) {
						$type = 'toc';
					}
					if( preg_match( "/Onlinezugriff via nanoo/", $note )) {
						$type = 'nanoo';
					}
					if( preg_match( "/Online via e-rara\.ch/", $note )) {
						$type = 'erara';
					}
					if( preg_match( '/\/\/www\.e-periodica\.ch/i', $val )) {
						$type = 'eperiodica';
					}
					$this->urls[] = "{$type}:{$val}";

					$this->urls_notes[$val] = array( 'type'=>$type, 'url'=>$val, 'note'=>$note );

					//$this->online = true;
				}
        	}
	    }
        return array_unique( $this->urls );
    }

    public function getURLsNotes() {
    	if( $this->urls_notes == null ) {
    		$this->getURLs();
    	}
    	return $this->urls_notes;
    }

    public function getSys() {
        return $this->id;
    }

    public function getMeta() {
        return $this->record->getXML();
    }

    public function getOnline() {
    		//$this->getType();
//				var_dump( $this->online );
				if( $this->online !== null ) return $this->online;

    		$carriers = $this->getAll( '338', null, null, 'a' );
    		foreach( $carriers as $carrier ) {
					$this->online |= preg_match( '/online(-| )ress?ource/i', $carrier );
    		}
				$extents = $this->getAll( '300', null, null, 'a' );
				foreach( $extents as $extent ) {
					$this->online |= preg_match( '/online(-| )ress?ource/i', $extent );
    		}
				foreach( $this->getLocations() as $location ) {
					list( $verbund, $lib, $loc ) = explode( ':', $location );
					if( array_key_exists( $lib, swissbibEntity::$bibs )) {
						foreach( swissbibEntity::$bibs[$lib] as $cat ) {
							if( $cat == 'FHNW-Bib'
								&& (in_array( $loc, array( 'E39OF', 'E44OF', 'E50OF', 'E60OF', 'E75OF' ))
							)) {
								$this->online = true;
							}
						}
					}
				}
        return $this->online;
    }

    public function getCodes() {

    	$this->codes = array();
//    	var_dump( $this->data );

        foreach( array( '020', '022' ) as $tag ) {
	        $flds = $this->findField( $tag, null, ' ' );
					// print_r( $flds );
	        foreach( $flds as $l=>$fld ) {
						$val = null;
						$type = null;
	        	  foreach( $fld as $code=>$v ) {
		        	switch( $code ) {
								case 'a':
		        			$val = implode( ';', $v );
		        		break;
								case 'z':
								case '2':
								case 'q':
	        				$type = implode( ';', $v );
	        			break;
			       	}
        	   }
	        if( $val === null ) continue;
	         if( $type == null ) {
						 if( preg_match( '/^([^\(\)]+)\(([^()]*)\)/', $val, $matches )) {
			        	$val = trim( $matches[1] );
			        	$type = trim( $matches[2] );
		        }
					}
					if( preg_match( '/(ebook|online|pdf|epub|e-?isbn)/i', $type )) $type = 'EISBN';
					elseif( preg_match( '/(print)/i', $type )) $type = 'ISBN';

	        if( $type == null ) {
						$type = 'ISBN';
						if( preg_match( '/^[0-9]{4}-?[0-9]{4}$/', $val )) $type = 'ISSN';
					}
	        if( $type != null ) $type = strtoupper( $type );
	        $this->codes[] = "{$type}:{$val}";
				}
	   }
/*
		// 0248 Nicht spezifizierte Art der Standardnummer oder des -codes
		 $flds = $this->getAll( '024', '8', ' ', 'a' );
		 foreach( $flds as $fld ) {
			 $this->codes[] = 'unkown:'.$fld;
		 }
*/

	   $this->codes = array_unique( $this->codes );
	   return $this->codes;
    }

	public function getCategories() {
		static $pattern = null;
		if( $pattern == null ) {
			$pattern = array();
			$sql = "SELECT signature, original, institute, field1, field2, field3, area FROM curate_signature2category ORDER BY LENGTH(signature) DESC, signature, area";
			$rs = $this->db->Execute( $sql );
			foreach( $rs as $row ) {
				$pattern[] = array( 'signature'=>$row['signature'], 'institute'=>$row['institute'], 'field1'=>$row['field1'], 'field2'=>$row['field2'], 'field3'=>$row['field3'], 'area'=>$row['area'], 'institute'=>$row['institute'] );
			}
			$rs->Close();
		}
		$categories = parent::getCategories();
		$regal = array();
		foreach( $this->getLocations() as $loc ) {
			if( preg_match( '/NEBIS:E75:([A-Z])_[0-9]{3}_[ab]/i', $loc, $matches )) {
				$regal[] = $matches[1];
			}
		}

		$found = false;
		foreach( $this->getSignatures() as $sig ) {
			$s = explode( ':', $sig );
			if( count( $s ) >= 4 ) {
				if( $s[0] == 'barcode' ) continue;
				$categories[] = $s[0].'!!'.$s[1].'!!'.$s[2];

				if( preg_match( '/^signature:NEBIS:E75:/i', $sig )) {
					// create automated categories based on signatures
					foreach( $pattern as $cat ) {
						$reg = "/^".preg_quote( $cat['signature'], '/' )."/i";
//					echo "[{$reg}]\n";
						if( preg_match( $reg, $s[3] )) {
//							echo "{$reg} \n";
							if( strlen( $cat['field1'] )) $categories[] = $cat['field1'];
							if( strlen( $cat['field2'] )) $categories[] = $cat['field2'];
							if( strlen( $cat['field3'] )) $categories[] = $cat['field3'];
							if( strlen( $cat['institute'] )) $categories[] = $cat['institute'];
							if( strlen( $cat['area'] )) $categories[] = $cat['area'];
							$categories[] = 'siggroup!!'.$cat['signature'];
							if( @strlen( $cat['institute'] )) $categories[] = $cat['institute'];
							$found = true;
							foreach( $regal as $r ) {
								if( strlen( $cat['field1'] )) $categories[] = $cat['field1'] .'!!Regal '.trim( $r );
								if( strlen( $cat['field2'] )) $categories[] = $cat['field2'] .'!!Regal '.trim( $r );
								if( strlen( $cat['field3'] )) $categories[] = $cat['field3'] .'!!Regal '.trim( $r );
								//$categories[] = $cat['area'] .'!!Regal '.trim( $r );
							}
						}
						if( $found ) break;
					}
				}
			}
		}
		if( !$found ) $categories[] = 'area!!unknown';

		return $categories;
	}

    public function getAbstract() {
    	return trim( implode( '; ', $this->getAll( '520', null, null, 'a' )).' '.implode( '; ', $this->getAll( '500', null, null, 'a' )));
    }
    public function getContent() { return null; }
    public function getMetaACL() { return array( 'global/guest' ); }
    public function getContentACL() { return array(); }
    public function getPreviewACL() { return array(); }
	public function getLanguages() { return array(); }

	public function getIssues()  {
		$issues = $this->getAll( '250', null, null, 'a' );
		return $issues;
	}

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

	public function getLibCodes() {
		if( $this->libCodes === null ) $this->getSignatures();
		return $this->libCodes;
	}

	public function getLibNames() {
		if( $this->libs === null ) $this->getSignatures();
		return $this->libs;
	}

	public function getCatalogs() {
		$libcodes = $this->getLibCodes();
		$catalogs = array($this->getSource());
		$categories = $this->getCategories();
		foreach( $libcodes as $lib ) {
			if( array_key_exists( $lib, swissbibEntity::$bibs )) {
				foreach( swissbibEntity::$bibs[$lib] as $cat ) {
					$catalogs[] = $cat;
				}
			}
		}
		foreach( $categories as $cat ) {
			if( strncmp( $cat, 'signature!!NATIONALLICENCE!!', 28 ) == 0 ) {
				$catalogs[] = 'NATIONALLICENCE';
			}
		}
		if( array_search( 'E75', $libcodes) !== false ) {
			$catalogs[] = 'HGK';
		} elseif( array_search( 'FHNW-Bib', $catalogs ) !== false && $this->getOnline()) {
			$catalogs[] = 'HGK';
		}

		switch( $this->getType()) {
			case 'OABook':
				$catalogs[] = 'OABook';
			break;
			case 'OAJournal':
				$catalogs[] = 'OAJournal';
			break;
		}

		foreach( $this->getLicenses() as $lic ) {
			switch( strtolower( $lic )) {
				case 'open access':
					if( $this->getType() == 'book') $catalogs[] = 'openaccess_books';
					elseif( $this->getType() == 'journal') $catalogs[] = 'openaccess_journals';
					else $catalogs[] = 'openaccess';
				break;
			}
		}

		foreach( $this->getURLs() as $url ) {
			if( strncmp( 'nanoo:', $url, 6 ) == 0 ) {
				$catalogs[] = 'nanoo';
			}
			if( strncmp( 'erara:', $url, 6 ) == 0 ) {
				$catalogs[] = 'erara';
			}
			if( strncmp( 'eperiodica:', $url, 11 ) == 0 ) {
					$catalogs[] = 'eperiodica';
			}

		}

		foreach( $this->getLocations() as $location ) {
			list( $verbund, $lib, $loc ) = explode( ':', $location );
			if( $verbund == 'NEBIS'
					&& array_search( 'FHNW-Bib', $catalogs ) !== false
					&& (in_array( $loc, array( 'E39OF', 'E39OS', 'E44OF', 'E44OS', 'E50OF', 'E50OS', 'E60EL', 'E60OF',  'E60OS', 'E60OP', 'E75OF' ))
							   || $this->getOnline()
							)
				) {
					$catalogs[] = 'FHNWeMedien';
					$catalogs[] = 'HGK';
			}
		}

		return array_unique( $catalogs );
	}

	public function getCoverImg() {
		foreach( $this->getURLs() as $u ) {
			$us = explode( ':', $u );
			if( substr( $us[1], 0, 4 ) == 'http' ) {
				$url = substr( $u, strlen( $us[0])+1 );
				if( $us[0] == 'cover')
					return $url;
			}
		}
		return null;
	}

	function getBibs() {
		$bibs = array();
		foreach( $this->getSignatures() as $sig ) {
			if( preg_match( '/^signature:([^:]+):([^:]+):(.+)$/', $sig, $matches )) {
				$gcode = $matches[1];
				$bcode = $matches[2];
				foreach( swissbibEntity::$mapbib['data'] as $d ) {
					if( $d['group']['code'] == $gcode ) {
						foreach( $d['institutions'] as $i ) {
							if( $i['bib_code'] == $bcode ) {
								$bibs[] = $i['name']['de'];
								break;
							}
						}
						break;
					}
				}
			}
		}
		return $bibs;
	}
}
?>
