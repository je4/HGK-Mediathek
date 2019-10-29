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

class EZBDisplay extends DisplayEntity {
    
    public function __construct( $doc, $urlparams, $db, $highlightedDoc ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );

        $this->db = $db;
    }
    
    public function getSchema() {
		$schema = array();
		$schema['@context'] = 'http://schema.org';
		$schema['@type'] = array('Periodical');
		$schema['@id'] = $this->doc->id;
		$schema['name'] = $this->doc->title;
		if( isset( $this->doc->author_ss ) && count( $this->doc->author_ss )) {
			$schema['author'] = array();
			foreach( $this->doc->author_ss as $author ) {
				$schema['author'][] = array( '@type' => 'Person', 'name' => $author );
			}
		}
		
		if( $this->doc->publisher && count( $this->doc->publisher ))
			foreach( $this->doc->publisher as $publisher ) {
				$schema['publisher'][] = array( '@type' => 'Organization', 'legalName' => $publisher );
			}
		$schema['url'] = array( 'https://mediathek.hgk.fhnw.ch/detail.php?id='.urlencode( $this->doc->id ));		
		if( is_array( $this->doc->url )) foreach( $this->doc->url as $u ) {
			$us = explode( ':', $u );
			if( substr( $us[1], 0, 4 ) == 'http' ) {
				$url = substr( $u, strlen( $us[0])+1 );
				$schema['url'][]= $url;
			}
		}
		if( $this->doc->cluster_ss )
			$schema['keywords'] = implode( '; ', $this->doc->cluster_ss );
		
		$schema['license'] = implode( '; ', $this->doc->license );
		
		return $schema;
	}
  
	public function detailView() {
		
        global $config, $solrclient, $db, $urlparams, $pagesize;
		return '';
	}
	
    public function desktopList() {
        global $config, $googleservice, $googleclient;
        $html = '';
		
		$entity = new EZBEntity($this->db);
		$entity->loadFromArray( (array)json_decode( $this->data ), $this->doc->source );

		$t = strtolower( $this->doc->type );
		$icon = array_key_exists( $t, $config['icon'] ) ? $config['icon'][$t] : $config['icon']['default'];
		
		
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
        <tr>
            <td rowspan="2" class="list" style="text-align: right; width: 25%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
					<?php echo htmlspecialchars( implode( '; ', $entity->getPublisher())  ); ?>
                </a>
            </td>
            <td class="list" style="width: 5%;"><i class="<?php echo $icon; ?>"></i></td>
            <td class="list" style="width: 70%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                    <?php echo htmlspecialchars( $entity->getTitle() ); ?>
                </a>        
            </td>
        </tr>
        <tr>
            <td colspan="1" class="detail">
            </td>
            <td class="detail">
                <div class="collapse" id="coll_<?php echo $this->doc->id; ?>">
				
<?php			
				$codes = $entity->getCodes();
				foreach( $codes as $code ) 
					echo htmlspecialchars( $code )."<br />\n";
				
				if( is_array( $this->doc->url )) foreach( $this->doc->url as $u ) {
					$us = explode( ':', $u );
					if( substr( $us[1], 0, 4 ) == 'http' ) {
						$url = substr( $u, strlen( $us[0])+1 );
						echo ($us[0] == 'unknown' ? '' : $us[0].':')."<i class=\"fa fa-external-link\" aria-hidden=\"true\"></i><a href=\"".$url."\" target=\"blank\">{$url}</a><br />\n";
					}
				}
				
				echo "ID: ".$this->doc->id."<br />\n";
?>
                </div>
            </td>
        </tr>
<?php
        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
    }
}