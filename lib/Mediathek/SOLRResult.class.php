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

abstract class SOLRResult {
    protected $query;
    protected $totalDoc, $startDoc, $pageSize, $numDoc;
    protected $resultSet;
    protected $db;

    public function __construct( $resultSet, int $startDoc, int $pageSize, $db ) {
        $this->resultSet = $resultSet;
        $this->totalDoc = $resultSet->getNumFound();
        $this->startDoc = $startDoc;
        $this->numDoc = $resultSet->count();
        $this->db = $db;
        
        foreach( $resultSet as $doc ) {
            $this->addDocument( $doc );
        }
    }
        
    abstract public function addDocument( $doc );
    abstract public function getResult();
    
    
}