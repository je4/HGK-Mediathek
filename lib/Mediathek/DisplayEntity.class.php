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
 * @subpackage  DisplayEntity
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

abstract class DisplayEntity {
    protected $data = null;
    protected $doc = null;
	protected $urlparams = null;
	protected $db = null;
	protected $highlight = null;
    public function __construct( $doc, $urlparams, $db, $highlightedDoc  ) {
        $this->doc = $doc;
		$this->urlparams = $urlparams;
		$this->db = null;
        $this->data = gzdecode( base64_decode( $doc->metagz ));
 //       echo "<!--\n".$this->data."\n-->\n";
		$this->highlight = $highlightedDoc;
    }
	
	public function getHeading() {
		$html = '';
		
        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
		                    <h2 class="small-heading">Mediathek der Künste</h2>

					<div class="container-fluid" style="margin-top: 0px; padding: 0px 20px 20px 20px;">
<?php
        $html .= ob_get_contents();
        ob_end_clean();
		return $html;
	}
	
	public abstract function getSchema( );
    public abstract function detailView();
    public abstract function desktopList();
}