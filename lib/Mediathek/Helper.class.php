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

use \Passbook\Pass\Field;
use \Passbook\Pass\Location;
use \Passbook\Pass\Image;
use \Passbook\PassFactory;
use \Passbook\Pass\Barcode;
use \Passbook\Pass\Structure;
use \Passbook\Type\StoreCard;

class Helper {
  
	static function createPass($id, $serial ) {
		global $db, $session, $config;
		$sql = "SELECT * FROM wallet.card WHERE pass=".$id." AND serial=".$db->qstr( $serial );
		$card = $db->GetRow( $sql );
		$sql = "SELECT * FROM wallet.pass WHERE id=".$id;
		$pass = $db->GetRow( $sql );
		
		if( !$card || !$pass ) return false;
		if( $pass['check'] && !$card['valid'] ) return false;

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
			$back1 = new Field( 'Name', $card['name'] );
			$back1->setLabel( 'Name' );
			$structure->addBackField( $back1 );
			$back2 = new Field( 'Cardnumber', $card['barcode'] );
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

			// Add logo image
			$logo = new Image($pass['logo'], 'logo');
			$p->addImage($logo);

			// Add strip image
			$strip = new Image($pass['strip'], 'strip');
			$p->addImage($strip);

			// Add barcode
			$barcode = new Barcode( $pass['code'], $card['barcode'] );
			$p->setBarcode($barcode);
		}

		if( $p != null ) {
			$auth = sha1(time() . $card['uniqueID'] . rand() );
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
			
			$sql = "REPLACE INTO wallet.wallet (`passid`, `serial`, `auth`, `expires`)
				VALUES (".$db->qstr( $pass['passid'] ).",
						".$serial.",
						".$db->qstr( $auth ).",
						".$db->qstr( $expirationDate->format( 'Y-m-d H:i:s' ) )." )";
			$db->Execute( $sql );

			if( file_exists( $passFile ))
				return $passFile;
		}
		return false;
	}
	
    static function buildSOLRQuery( $qstr ) {
        global $helper;

        $qstr = preg_replace( '/([a-zA-Z]+):"/', '"\1:', $qstr );
        if( !preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $qstr, $matches)) return array( $qstr );
        $global = array();
        $specific = array();
        foreach( $matches[0] as $m ) {
            $word = trim($m, " \"\t\n\r\0\x0B");
            $ex = explode(':', $word );
            if( count($ex) > 1 ) {
                $specific[ trim( array_shift( $ex ))] = trim( implode( ':', $ex ));
            }
            else {
                $global[] = trim( $ex[0] );
            }
        }
        $qstr = '';
        
        foreach( $specific as $key=>$word ) {
            $fields = array();
            switch( strtolower( $key )) {
				case 'publisher':
                case 'author':
                    $fields[] = 'author';
                    $fields[] = 'publisher';
                    break;
                case 'title':
                case 'source':
                case 'location':
                case 'signature':
                    $fields[] = $key;
                break;
				case 'kiste':
					$word = 'E75:Kiste:'.$word;
                    $fields[] = 'location';
					break;
            }
            if( count($fields) == 0 ) continue;
            $first = true;
            $qstr .= '(';
            foreach( $fields as $field ) {
                if( !$first )
                    $qstr .= ' OR ';
                $qstr .= $field.':'.$helper->escapePhrase( $word );
            }
            $qstr .= ')';
        }
        
        if( count( $global ) > 0 ) {
            if( strlen( $qstr ) > 0 )
                $qstr .= ' AND ';
            $qstr .= '(';
            
            $qstr .= ' (';
            $first = true;
            foreach( $global as $word ) {
                if( $first == false ) {
                    $qstr .= ' OR ';
                }
                $qstr .= 'title:'.$helper->escapePhrase( $word ).'^10';
                $first = false;
            }
            $qstr .= ' )';
            $qstr .= ' OR (';
            $first = true;
            foreach( $global as $word ) {
                if( !$first ) {
                    $qstr .= ' OR ';
                }
                $qstr .= 'author:'.$helper->escapePhrase( $word ).'^10';
                $first = false;
            }
            $qstr .= ' )';
            $qstr .= ' OR (';
            $first = true;
            foreach( $global as $word ) {
                if( !$first ) {
                    $qstr .= ' AND ';
                }
                $qstr .= 'content:'.$helper->escapePhrase( $word ).'^10';
                $first = false;
            }
            $qstr .= ' )';
            $qstr .= ' OR (';
            $first = true;
            foreach( $global as $word ) {
                if( !$first ) {
                    $qstr .= ' AND ';
                }
                $qstr .= 'abstract:'.$helper->escapePhrase( $word ).'^10';
                $first = false;
            }
            $qstr .= ' )';
            $qstr .= ' OR (';
            $first = true;
            foreach( $global as $word ) {
                if( !$first ) {
                    $qstr .= ' OR ';
                }
                $qstr .= 'signature:'.$helper->escapePhrase( $word ).'^10';
                $first = false;
            }
            $qstr .= ' )';
            
            $qstr .= ')';
            
        }
        return $qstr;
    }
}

?>