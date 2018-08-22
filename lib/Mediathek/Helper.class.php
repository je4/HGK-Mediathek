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
 * @subpackage  NEBISDisplay
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

include( dirname(__DIR__).'/php-barcode.php' );

use \Passbook\Pass\Field;
use \Passbook\Pass\Location;
use \Passbook\Pass\Image;
use \Passbook\PassFactory;
use \Passbook\Pass\Barcode;
use \Passbook\Pass\Structure;
use \Passbook\Type\StoreCard;

class Helper {

	static function isbn13( $code ) {
		$code = str_replace( '-', '', $code );
		if( preg_match( '/^[0-9]{13}$/', $code )) return $code;
		if( strlen( $code ) == 10 ) {
			$i13 = '978'.substr( $code, 0, 9 );
		}
		elseif( strlen( $code ) == 13 ) {
			$i13 = substr( $code, 0, 12 );
		}
		else {
			return $code;
		}
		$sum = 0;
		for( $i = 0; $i < 12; $i++ ) {
				$sum += intval( $i13{$i} )*($i%2==1 ? 3 : 1);
		}
		$p = (10 - ($sum%10))%10;
		$i13 .= $p;
		return $i13;
	}

	static function clearCluster( $cluster ) {
		$nc = array();
		foreach( $cluster as $c ) {
			if( preg_match( '/Online[ -]Ress?ource/', $c )) continue;
			$nc[] = $c;
		}
		return $nc;
	}

	static function createPass($id, $serial ) {
		global $db, $session, $config;
		$sql = "SELECT * FROM wallet.card WHERE pass=".$id." AND serial=".$db->qstr( $serial );
		$card = $db->GetRow( $sql );
		$sql = "SELECT * FROM wallet.pass WHERE id=".$id;
		$pass = $db->GetRow( $sql );

		if( !$card || !$pass ) return false;
		if( $pass['check'] && !$card['valid'] ) return false;

		$barcodeFile = $config['tmpprefix'].$card['barcode'].'.png';
		$im     = imagecreatetruecolor(375, 30);
		$black  = ImageColorAllocate($im,0x00,0x00,0x00);
		$white  = ImageColorAllocate($im,0xff,0xff,0xff);
		imagefilledrectangle($im, 0, 0, 375, 30, $white);
		$data = \Barcode::gd($im, $black, 375/2, 18, 0, "code39", $card['barcode'], 2, 25);
		imagepng( $im, $barcodeFile );

		$stripfile = $config['tmpprefix'].$card['barcode'].'_strip.png';
		$cmd = "composite -gravity South ".escapeshellarg($barcodeFile)." ".escapeshellarg( $pass['strip'] )." ".escapeshellarg( $stripfile );
		$result = shell_exec( $cmd );

		$passFile = "{$pass['folder']}/{$serial}.pkpass";
		if( file_exists( $passFile ))
			return $passFile;

		if( $pass['type'] == 'storeCard' ) {
			$p = new StoreCard($serial, $pass['description'] );
			$p->setBackgroundColor('rgb(255, 255, 255)');
			$p->setLogoText($pass['logotext']);

			if( $pass['latitude'] && $pass['longitude'] ) {
				$location = new Location( ( double )$pass['latitude'] , ( double )$pass['longitude'] );
				if( $pass['altitude'] ) $location->setAltitude( ( double )$pass['altitude'] );
				$p->addLocation( $location );
			}

			$expirationDate = (new \DateTime())->add( new \DateInterval($pass['expiry']) );
			$p->setExpirationDate( $expirationDate );

			// Create pass structure
			$structure = new Structure();

			// Add primary field
			$primary = new Field('Title', $pass['title']);
			$structure->addPrimaryField($primary);

			// Add secondary field
			$secondary = new Field('Name', "{$card['name']}");
			$secondary->setLabel( 'Name' );
			$structure->addSecondaryField($secondary);

			$secondary2 = new Field('Cardnumber', $card['barcode']);
			$secondary2->setLabel( 'Kartennummer' );
			$structure->addSecondaryField($secondary2);

			// Add back field
			$back = new Field( 'Library', $pass['library'] );
			$back->setLabel( 'Bibliothek' );
			$structure->addBackField( $back );
			$back1 = new Field( 'Ownername', $card['name'] );
			$back1->setLabel( 'Name' );
			$structure->addBackField( $back1 );
			$back2 = new Field( 'CardnumberBack', $card['barcode'] );
			$back2->setLabel( 'Kartennummer' );
			$structure->addBackField( $back2 );
			$back3 = new Field( 'Email', $card['email'] );
			$back3->setLabel( 'Emailadresse' );
			if( strlen( $card['email2'])) {
				$back3 = new Field( 'Email2', $card['email2'] );
				$back3->setLabel( 'Emailadresse 2' );
			}
			$structure->addBackField( $back3 );
			$back4 = new Field( 'Valid', $expirationDate->format( 'd.m.Y H:i:s' ) );
			$back4->setLabel( 'Gültig bis' );
			$structure->addBackField( $back4 );
			$back2 = new Field( 'Data', "Seriennummer: ".$serial
			."\nAAI UniqueID: ".$card['uniqueID']
			."\nAAI Organisation: ".$session->shibHomeOrganization()
			."\n\nhttps://mediathek.hgk.fhnw.ch/ausweis.php"
			);
			$back2->setLabel( 'Informationen' );
			$structure->addBackField( $back2 );
			$back2 = new Field( 'presented', 'Proudly presented by Mediathek HGK' );
			$structure->addBackField( $back2 );
			$back2 = new Field( 'Programming', 'info-age GmbH, Basel' );
			$back2->setLabel( 'Programmierung' );
			$structure->addBackField( $back2 );

			// Set pass structure
			$p->setStructure($structure);

			// Add icon image
			$icon = new Image($pass['icon'], 'icon');
			$p->addImage($icon);
			$icon2 = new Image($pass['icon2'], 'icon@2x');
			$p->addImage($icon2);

			// Add logo image
			$logo = new Image($pass['logo'], 'logo');
			$p->addImage($logo);
			$logo2 = new Image($pass['logo2'], 'logo@2x');
			$p->addImage($logo2);

			// Add strip image
			$strip = new Image(/* $pass['strip'] */ $stripfile, 'strip');
			$p->addImage($strip);

			// Add barcode
			$barcode = new Barcode( $pass['code'], $card['barcode'] );
			$p->setBarcode($barcode);
		}

		if( $p != null ) {
			$sql = "SELECT auth FROM wallet.wallet WHERE passid=".$db->qstr( $pass['passid'] )." AND serial=".$serial;
			$auth = $db->GetOne( $sql );
			if( $auth === null ) {
				$auth = sha1(time() . $card['uniqueID'] . rand() );

				$sql = "INSERT INTO wallet.wallet (`passid`, `serial`, `auth`, `expires`)
					VALUES (".$db->qstr( $pass['passid'] ).",
							".$serial.",
							".$db->qstr( $auth ).",
							".$db->qstr( $expirationDate->format( 'Y-m-d H:i:s' ) )." )";
				$db->Execute( $sql );
			}
			$p->setAuthenticationToken( $auth );

			$p->setWebServiceURL( $pass['webservice'] );

			// Create pass factory instance
			$factory = new PassFactory($pass['passid']
									, $pass['teamid']
									, $pass['teamname']
									, $pass['certfile']
									, $pass['certpwd']
									, $config['wallet']['applecert']);
			$factory->setOutputPath( $pass['folder'] );
			$factory->package($p);

			$sql = "UPDATE wallet.card SET issuedate=NOW(), expirydate=".$db->qstr( $expirationDate->format( 'Y-m-d H:i:s' ) )." WHERE pass=".$id." AND serial=".$serial;
			$db->Execute( $sql );


			if( file_exists( $passFile ))
				return $passFile;
		}
		return false;
	}

		static function addField( &$fields, $field, $word ) {
			if( !@is_array( $fields[$field] )) $fields[$field] = [];
			$fields[$field][] = $word;
		}

    static function buildSOLRQuery( $qstr ) {
        global $helper;
				//echo "{$qstr}\n";
        $qstr = preg_replace( '/([a-zA-Z]+):"/', '"\1:', $qstr );
        if( !preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $qstr, $matches)) return array( $qstr );
        //print_r( $matches );
        $global = array();
        $specific = array();
        foreach( $matches[0] as $m ) {
            $word = trim($m, " \"\t\n\r\0\x0B");
            if( preg_match( '/([^:]+):(.*)$/', $word, $matches2 )) {
							if( !@is_array( $specific[$matches2[1]])) $specific[ $matches2[1] ] = [];
              $specific[ $matches2[1] ][] = trim( $matches2[2] );
            }
            else {
                $global[] = $word;
            }
        }
        $qstr = '';
        foreach( $specific as $key=>$words ) {
					$fields = array();
					foreach( $words as $word ) {
							//echo "{$key} -> {$word}\n";
	            switch( strtolower( $key )) {
					        case 'publisher':
	                case 'author':
											self::addField($fields, 'author', $word );
											self::addField($fields, 'publisher', $word );
//	                    $fields[] = 'author';
//	                    $fields[] = 'publisher';
	                    break;
	                case 'title':
	                case 'source':
	                case 'location':
									case 'signature':
									case 'issue':
									case 'city':
									case 'category':
										self::addField($fields, $key, $word );
										//$fields[] = $key;
									break;
									case 'isbn':
									case 'eisbn':
										$key = strtoupper( $key );
										$word = $key.':'.Helper::isbn13( preg_replace( '/[^0-9Xx]/', '', $word ));
										//$fields[] = 'code';
										self::addField($fields, 'code', $word );
									break;
					        case 'kiste':
						           $word = 'NEBIS:E75:Kiste:'.$word;
	                     //$fields[] = 'location';
											 self::addField($fields, 'location', $word );
						      break;
	            }
						}
            if( count($fields) == 0 ) continue;
						//print_r( $fields );
            $first = true;
            $qstr .= '(';
            foreach( $fields as $field=>$words ) {
                if( !$first )
                    $qstr .= ' OR ';
                $first = false;
                $qstr .= '(';
								$first2 = true;
								foreach( $words as $word ) {
										if( !$first2 ) $qstr .= ' AND ';
										$first2 = false;
										$qstr .= $field.':'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapePhrase( $word )));
								}
								$qstr .=')';
            }
            $qstr .= ')';
        }
        //echo $helper->escapeTerm( $word )." - ".htmlentities($qstr);
        if( count( $global ) > 0 ) {
            $all = implode( ' ', $global );
            $multiple = count( $global ) > 1;
            if( strlen( $qstr ) > 0 )
                $qstr .= ' AND ';
            $qstr .= '(';

            $qstr .= ' (';
            $first = true;
            foreach( $global as $word ) {
                if( $first == false ) {
                    $qstr .= ' OR ';
                }
                $qstr .= 'title:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapePhrase( $word ))).'^10';
                $first = false;
            }
            if( $multiple ) $qstr .= ' OR title:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapePhrase( $all ))).'^20';

            $qstr .= ' )';
            // ----------------------
            $qstr .= ' OR (';
            $first = true;
            foreach( $global as $word ) {
            	if( !$first ) {
            		$qstr .= ' OR ';
            	}
            	$qstr .= 'author:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapeTerm( $word ))).'^10';
            	$first = false;
            }
            if( $multiple ) $qstr .= ' OR author:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapeTerm( $all ))).'^20';
            $qstr .= ' )';
            // ----------------------
            $qstr .= ' OR (';
            $first = true;
            foreach( $global as $word ) {
            	if( !$first ) {
            		$qstr .= ' OR ';
            	}
            	$qstr .= 'publisher:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapeTerm( $word ))).'^8';
            	$first = false;
            }
            if( $multiple ) $qstr .= ' OR publisher:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapeTerm( $all ))).'^18';
            $qstr .= ' )';
            // ----------------------
            $qstr .= ' OR (';
            $first = true;
            foreach( $global as $word ) {
                if( !$first ) {
                    $qstr .= ' AND ';
                }
                $qstr .= 'content:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapeTerm( $word.'*' ))).'^6';
                $first = false;
            }
            if( $multiple ) $qstr .= ' OR content:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapeTerm( $all.'*' ))).'^12';
            $qstr .= ' )';
            // ----------------------
            $qstr .= ' OR (';
            $first = true;
            foreach( $global as $word ) {
                if( !$first ) {
                    $qstr .= ' AND ';
                }
                $qstr .= 'abstract:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapeTerm( $word.'*' ))).'^8';
                $first = false;
            }
            if( $multiple ) $qstr .= ' OR abstract:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapeTerm( $all.'*' ))).'^15';
            $qstr .= ' )';
            // ----------------------
            $qstr .= ' OR (';
            $qstr .= ' signature:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapePhrase( $all ))).'^25';
            $qstr .= ' )';
            // ----------------------
	        if( preg_match( '/^[0-9Xx-]+$/', $all)) {
	            $qstr .= ' OR (';
	            $qstr .= ' code:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapePhrase( 'ISBN:'.$all ))).'^25';
	            $qstr .= ' OR code:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapePhrase( 'ISSN:'.$all ))).'^25';
	            $qstr .= ' OR code:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapePhrase( 'ISBN:'.str_replace( '-', '', $all )))).'^25';
	            $qstr .= ' OR code:'.str_replace( '\*', '*', str_replace( '\?', '?', $helper->escapePhrase( 'ISSN:'.str_replace( '-', '', $all )))).'^25';
	             $qstr .= ' )';
            }

            $qstr .= ')';

        }
        return $qstr;
    }

    static function writeQuery( $md5, $qobj, $json ) {
    	global $db;

    	$sql = "SELECT COUNT(*) FROM web_query WHERE queryid=".$db->qstr( $md5 );
    	$num = intval( $db->GetOne( $sql ));
    	if( $num > 0 ) return;

    	$sql = "INSERT INTO web_query VALUES( ".$db->qstr( $md5 ).", ".$db->qstr( $qobj['query'] ). ", ".$db->qstr( $qobj['area'] ). ", ".$db->qstr( $json ).")";
    	$db->Execute( $sql );
    }

    static function readQuery( $md5 ) {
    	global $db, $config;

    	if( strlen( $md5 ) > 0 ) {
	    	$sql = "SELECT json FROM web_query WHERE queryid=".$db->qstr( $md5 );
	    	$json = $db->GetOne( $sql );
	    	if( strlen( $json )) return json_decode( $json, true );
    	}

    	$qobj = array();
    	$qobj['facets'] = array();
    	$qobj['filter'] = array();
    	$qobj['area'] = '';
    	$qobj['facets']['catalog'] = $config['defaultcatalog'];
    	$qobj['query'] = '*';
    	$query = json_encode( $qobj );
    	$q = md5($query);
    	Helper::writeQuery( $q, $qobj, $query );
    	return $qobj;
    }

}

?>
