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

class DesktopResult extends SOLRResult {
    private $html = "";
	private $urlparams = null;
	private $schema;
    
    public function __construct( $resultSet, int $startDoc, int $pageSize, $db, $urlparams ) {
            $this->urlparams = $urlparams;
			$this->schema = array("@context"=>"http://schema.org",
				"@type"=>array( "ItemList", "SearchResultsPage"),
				"publisher"=> array( '@type'=>'Organization', 'name'=>'Mediathek der Künste Basel' ),
				"numberOfItems"=>count( $resultSet ),
				"itemListElement"=> array(),
			);
            parent::__construct($resultSet, $startDoc, $pageSize, $db);
    }
 
    public function addDocument( $doc, $highlightedDoc ){
        $class = '\\Mediathek\\'.$doc->source.'Display';

        $output = new $class($doc, $this->urlparams, $this->db, $highlightedDoc );
        $this->html .= $output->desktopList();
        $this->schema['itemListElement'][] = array( "@type"=>"ListItem",
						"item"=>$output->getSchema(),
				);
        return;
    }
    
    public function getResult() {
       $this->html  = '<table class="table" style="table-layout: fixed;">'."\n".$this->html."</table>\n";
	   $this->html .= '<script type="application/ld+json">'."\n".json_encode( $this->schema )."\n</script>\n";
       return $this->html;
    }
}