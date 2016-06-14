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

class IKUVidDisplay extends DisplayEntity {
	private $metadata;
	
    public function __construct( $doc, $urlparams, $db ) {
        parent::__construct( $doc, $urlparams, $db );
		
		$this->metadata = (array) json_decode( $this->data );
    }

	public function detailView() {
        global $config, $googleservice, $googleclient;
        $html = '';

		return $html;
	}
	    
    public function desktopList() {
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
        <tr>
            <td rowspan="2" class="list" style="text-align: right; width: 25%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                    <?php echo htmlspecialchars( $this->doc->author[0] ); ?>
                </a>
            </td>
            <td class="list" style="width: 5%;"><i class="fa fa-file-video-o"></i></td>
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
                    <?php if( strlen( $this->metadata['Autor Regie'] )) echo 'Autor/Regie: '.htmlspecialchars( $this->metadata['Autor Regie'] )."<br />\n"; ?>
                    <?php if( strlen( $this->metadata['Land'] )) echo 'Land: '.htmlspecialchars( $this->metadata['Land'] )."<br />\n"; ?>
                    <?php if( strlen( $this->metadata['Produktionsjahr'] )) echo 'Jahr: '.htmlspecialchars( $this->metadata['Produktionsjahr'] )."<br />\n"; ?>
                    <?php if( strlen( $this->metadata['Dauer'] )) echo 'Dauer: '.htmlspecialchars( $this->metadata['Dauer'] )."<br />\n"; ?>
					<!--<i class="fa fa-external-link" aria-hidden="true"></i> <a href="http://www.imdb.com/find?ref_=nv_sr_fn&q=<?php echo urlencode(trim($this->metadata['Titel1'] )); ?>&s=all" target="_blank"><img src="img/imdb.png"></a><br /> -->
					ID: <?php echo $this->doc->id; ?>

                </div>
            </td>
        </tr>
<?php
        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
    }
}