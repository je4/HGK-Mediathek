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
 * @subpackage  SOLRResult
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

class OAIPMHRecord {
	protected $header;
	protected $metadata = null;
	protected $identifier, $datestampm, $status;
	protected $dom;
	
	function __construct( $xml ) {
		$this->dom = new \DOMDocument( '1.0', 'UTF-8' );
		$this->dom->LoadXML( $xml );

		$tmpLst = $this->dom->getElementsByTagName( 'header' );
		if( $tmpLst->length != 1 ) throw new \Exception( "invalid xml" );
		$this->header = $tmpLst->item(0);
		
		$tmpLst = $this->header->getElementsByTagName( 'identifier' );
		if( $tmpLst->length != 1 ) throw new \Exception( "invalid xml" );
		$this->identifier = $tmpLst->item(0)->nodeValue;
    	
		$tmpLst = $this->header->getElementsByTagName( 'datestamp' );
		if( $tmpLst->length != 1 ) throw new \Exception( "invalid xml" );
		$this->datestamp = $tmpLst->item(0)->nodeValue;
    	
		$this->status = $this->header->getAttribute( 'status' );

		if( !$this->isDeleted()) {			
			$tmpLst = $this->dom->getElementsByTagName( 'metadata' );
			if( $tmpLst->length != 1 ) throw new \Exception( "invalid xml" );
			$this->metadata = $tmpLst->item(0);
		}
	}
	
	public function isDeleted() {
		return $this->status == 'deleted';
	}
	
	public function getIdentifier() {
		return $this->identifier;
	}

	public function getDatestamp() {
		return $this->datestamp;
	}
	
	
	public function getMetadata() {
		return $this->metadata;
	}
}