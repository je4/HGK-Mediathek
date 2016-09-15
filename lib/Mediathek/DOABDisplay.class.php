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

class DOABDisplay extends DisplayEntity {
    private $metadata;
    
    public function __construct( $doc, $urlparams, $db, $highlightedDoc ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );
        
        $this->metadata = (array) json_decode( $this->data );
    }
    
	public function detailView() {
        global $config, $googleservice, $googleclient;
        $html = '';

		return $html;
	}
	
    public function desktopList() {
		$html = '';
		$lineid = preg_replace( "/[^a-zA-Z0-9]/", "_", $this->doc->id );
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
        <tr>
            <td rowspan="2" class="list" style="text-align: right; width: 25%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                    <?php echo htmlspecialchars( implode( '; ', $this->doc->author )); ?>
                </a>
            </td>
            <td class="list" style="width: 5%;"><i class="fa fa-book" aria-hidden="true"></i></td>
            <td class="list" style="width: 70%;">
                <a class="entity" href="#coll_<?php echo $lineid; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                    <?php echo htmlspecialchars( $this->doc->title ); ?>
                </a>        
            </td>
        </tr>
        <tr>
            <td colspan="1" class="detail">
            </td>
            <td class="detail">
                <div class="collapse" id="coll_<?php echo $lineid; ?>">
                    <?php if( @count( $this->metadata['DC:PUBLISHER'] )) echo 'Verlag: '.htmlspecialchars( implode( '; ', $this->metadata['DC:PUBLISHER'] ))."<br />\n"; ?>
                    <?php if( @count( $this->metadata['DC:DATE'] )) echo 'Jahr: '.htmlspecialchars( implode( '; ', $this->metadata['DC:DATE'] ))."<br />\n"; ?>
                    <?php if( @count( $this->metadata['DC:LANGUAGE'] )) echo 'Sprache: '.htmlspecialchars( implode( '; ', $this->metadata['DC:LANGUAGE'] ))."<br />\n"; ?>
                    <?php if( @count( $this->metadata['DC:RIGHT'] )) echo 'Lizenz: '.htmlspecialchars( implode( '; ', $this->metadata['DC:RIGHT'] ))."<br />\n"; ?>
                    <?php if( isset( $this->doc->code )) echo htmlspecialchars( implode( "<br />\n", $this->doc->code ))."<br />\n"; ?>
                    <?php if( isset( $this->doc->url )) foreach( $this->doc->url as $url ) echo 'URL: <i class="fa fa-external-link" aria-hidden="true"></i><a href="redir.php?id='.urlencode( $this->doc->id ).'&url='.urlencode($url).'" target="_blank">'.htmlspecialchars( $url )."</a><br />\n"; ?>
					
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