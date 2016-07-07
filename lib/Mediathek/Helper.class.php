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

class Helper {
  
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