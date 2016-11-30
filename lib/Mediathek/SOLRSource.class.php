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
 * @subpackage  SOLRSource
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

abstract class SOLRSource {
    abstract public function getID();
	abstract public function getOriginalID();
    abstract public function getSource();
    abstract public function getType();
	abstract public function getOpenAccess();
	abstract public function getLocations();
    abstract public function getTitle();    
    abstract public function getCodes();
    abstract public function getAbstract();
    abstract public function getContent();
    abstract public function getTags();    
    abstract public function getSignatures();    
    abstract public function getAuthors();    
    abstract public function getLoans();    
    abstract public function getBarcode();    
    abstract public function getLicenses();    
    abstract public function getURLs();    
    abstract public function getSys();
    abstract public function getPublisher();
    abstract public function getYear();
    abstract public function getCity();
    abstract public function getCluster();
    abstract public function getMeta();
    abstract public function getOnline();
	abstract public function getEmbedded();
    abstract public function getMetaACL();
    abstract public function getContentACL();
    abstract public function getPreviewACL();
	abstract public function getLanguages();
	abstract public function getIssues();
	public function getCategories() {
		$r = new \ReflectionMethod(get_called_class(), 'getSource');
		return array( 'source::'.$r->invoke($this) );
	}

}

?>