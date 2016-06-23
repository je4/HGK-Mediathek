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

interface SOLRSource {
    public function getID();
	public function getOriginalID();
    public function getSource();
	public function getOpenAccess();
	public function getLocations();
    public function getTitle();    
    public function getCodes();
    public function getAbstract();
    public function getContent();
    public function getTags();    
    public function getSignatures();    
    public function getAuthors();    
    public function getLoans();    
    public function getBarcode();    
    public function getSignature();    
    public function getLicenses();    
    public function getURLs();    
    public function getSys();
    public function getPublisher();
    public function getYear();
    public function getCity();
    public function getCluster();
    public function getMeta();
    public function getOnline();
	public function getEmbedded();
    public function getMetaACL();
    public function getDataACL();

}

?>