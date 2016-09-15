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

class DOAJArticleDisplay extends DisplayEntity {
    private $metadata;
	private $entity;
	
    public function __construct( $doc, $urlparams, $db, $highlightedDoc ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );
        
        $this->metadata = (array) json_decode( $this->data );
		$this->entity = new DOAJArticleEntity( $db );
		$this->entity->loadFromArray( $this->doc->originalid, $this->metadata, 'doajarticle');
    }
    
	public function detailView() {
        global $config, $googleservice, $googleclient;
        $html = '';

		return $html;
	}
	
    public function desktopList() {
		$html = '';
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
        <tr>
            <td rowspan="2" class="list" style="text-align: right; width: 25%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                    <?php echo htmlspecialchars( count( $this->entity->getAuthors() ) ? $this->entity->getAuthors()[0] : "" ); ?>
                </a>
            </td>
            <td class="list" style="width: 5%;"><i class="fa fa-newspaper-o" aria-hidden="true"></i></td>
            <td class="list" style="width: 70%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                    <?php echo htmlspecialchars( $this->doc->title ); ?>
                </a>        
            </td>
        </tr>
        <tr>
            <td colspan="1" class="detail">
            </td>
            <td class="detail">
                <div class="collapse" id="coll_<?php echo $this->doc->id; ?>">
					<?php if( count( $this->entity->getAuthors()) > 1 ) for( $i = 1; $i < count( $this->entity->getAuthors()); $i++ ) echo htmlspecialchars( $this->entity->getAuthors()[$i] )." / "; ?><br />
					<?php if ($this->highlight) { ?>
					<p style="background-color: #f0f0f0; margin-left: +20px; border-top: 1px solid black; border-bottom: 1px solid black;"><i>
					
<?php
						foreach ($this->highlight as $field => $highlight) {
							echo '(...) '.strip_tags( implode(' (...) ', $highlight), '<b></b>') . ' (...)' . '<br/>';
						}
?>
					</i></p>					
<?php
					}
?>
					<?php if( count( $this->entity->getIssues() )) echo "Issue: ".htmlentities( implode( '; ', $this->entity->getIssues()))."<br />\n"; ?>
					<?php if( count( $this->entity->getLanguages() )) echo "Language(s): ".htmlentities( implode( '; ', $this->entity->getLanguages()))."<br />\n"; ?>
					<?php if( count( $this->entity->getLanguages() )) echo "Publisher: ".htmlentities( implode( '; ', $this->entity->getPublisher()))."<br />\n"; ?>
					
					ID: <?php echo $this->doc->id; ?>
					<p>
					<!-- <a href="detail.php?<?php echo "id=".urlencode( $this->doc->id ); foreach( $this->urlparams as $key=>$val ) echo '&'.$key.'='.urlencode($val); ?>">Detail</a><br />
					-->
                    
                </div>
            </td>
        </tr>
<?php
        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
    }
}