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

class eartnetDisplay extends DisplayEntity {

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
        global $config, $googleservice, $googleclient, $pagesize;
        $html = '';

		$entity = new eartnetEntity($this->db);
    $data = json_decode( $this->data, true );
		$entity->loadFromArray( $data, $this->doc->source );

		$t = strtolower( $this->doc->type );
		$icon = array_key_exists( $t, $config['icon'] ) ? $config['icon'][$t] : $config['icon']['default'];


        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
        <tr>
            <td rowspan="2" class="list" style="text-align: right; width: 25%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
					<?php
          $authors = $entity->getAuthors();
          echo htmlspecialchars( $authors[0] );
          ?>
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
                  <table border="0">
<?php
        foreach( $data as $row ) {
          $dblink = "http://service.european-art.net/redirect?db={$row['person_id_datenbank']}&p={$row['person_id_datensatz']}";
          //print_r( $row );
          // str_replace( '[id]', $row['person_id_datensatz'], $row['datenbank_aufruf']);
          echo "<tr><td><a href=\"{$dblink}\"><img alt=\"{$row['institution_name']}\" src=\"http://www.european-art.net/media_system/logo/{$row['datenbank_logo']}\" /></a></td><td><a href=\"{$dblink}\">{$row['person_vorname']} {$row['person_nachname']}</a></td></tr>\n";
        }
// http://www.european-art.net/eingang_besucher/index.cfm?token_lastname=bolf&token_firstname=josef


        $link = "http://www.european-art.net/eingang_besucher/index.cfm?token_lastname={$data[0]['person_nachname']}&token_firstname={$data[0]['person_vorname']}";
        echo "<tr><td><a href=\"{$link}\"><img src=\"img/europeanartnet.png\" alt=\"european-art.net\" style=\"max-width: 120px; max-height=30px\" /></a></td><td><a href=\"{$link}\">{$data[0]['person_vorname']} {$data[0]['person_nachname']}</a></td></tr>\n";
?>
                </table>
				ID: <?php echo $this->doc->id; ?><br />

                </div>
            </td>
        </tr>
<?php
        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
    }
}
