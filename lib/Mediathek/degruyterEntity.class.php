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

class degruyterEntity extends swissbibEntity {
	private $node;

    private static $idprefix = 'degruyter';
    
    public function loadFromArray( $id, $data ) {
    	$this->reset();
    	$this->id = $id;
    	$this->data = $data;
    }
    
    public function loadFromDoc( $doc) {
    	$this->id = $doc->originalid;
    	$this->data = json_decode(( string )gzdecode( base64_decode( $doc->metagz )), true );
    }
    
	function loadNode( $id, $doc, $idprefix ) {
		$this->reset();
		$this->data = array();
		$this->id = $id;
        foreach( $doc->getElementsByTagName( 'datafield' ) as $entry ) {
        	//echo $doc->saveXML( $entry );
        	//var_dump( $entry );
        	$tag = $entry->getAttribute( 'tag' );
        	$ind1 = $entry->getAttribute( 'ind1' );
        	$ind2 = $entry->getAttribute( 'ind2' );
        	if( !array_key_exists( $tag, $this->data )) $this->data[$tag] = array();
        	if( !array_key_exists( $ind1, $this->data[$tag] )) $this->data[$tag][$ind1] = array();
        	if( !array_key_exists( $ind2, $this->data[$tag][$ind1] )) $this->data[$tag][$ind1][$ind2] = array();
        	$elem = array();
        	foreach( $entry->childNodes as $child ) {
        		if( !(is_a( $child, 'DOMElement' ) && $child->tagName == 'subfield' )) continue;
        		$code = $child->getAttribute( 'code' );
        		if( !array_key_exists( $code, $elem )) $elem[$code] = array();
        		$elem[$code][] = $child->textContent;
        	}
        	$this->data[$tag][$ind1][$ind2][] = $elem;
        
        	// now you can use $node without going insane about parsing
        }
        if( $this->id == null ) {
	        $flds = $this->findField( '020', ' ', ' ' );
	        $val = null;
	        $type = null;
	        foreach( $flds as $fld ) {
	        	foreach( $fld as $code=>$v ) {
	        		switch( $code ) {
	        			case 'a':
	        				$this->id = implode( ';', $v );
	        				break;
	        		}
	        	}
	        }
        }
	}

	public function getID() {
		return self::$idprefix.'-'.$this->id;
	}
	
	public function getSource() {
		return 'degruyter';
	}
	
	public function getMeta() {
		return json_encode( $this->data );
	}
	
	public function getXML() {
		return "";
	}
	
}
?>
