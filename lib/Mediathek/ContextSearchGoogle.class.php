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
 * @subpackage  NebisImport
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

/**
 * Handling of MARC Data from Nebis
 *
 */

class ContextSearchGoogle extends ContextSearch  {
    protected $result = null;
    protected $api;
    
    public function __construct( $doc, $db, $api ) {
        parent::__construct( $doc, $db );
        $this->api = $api;
        $this->doSearch();
    }

    private function doSearch() {
        global $config;
        
        $sql = "SELECT json FROM extern_search_google WHERE id=".$this->db->qstr( $this->doc->id )." AND api=".$this->db->qstr( $this->api );
        
        $json = $this->db->GetOne( $sql );
        if( $json != null ) $this->result = json_decode( $json );
        else {
            $searchstr = '';
            if( is_array( $this->doc->code ))
                foreach( $this->doc->code as $code ) {
                    if( !strlen( $code )) continue;
                    $cs = explode( ' ', $code );
                    $searchstr .= $cs[0].' ';
                }
            $searchstr = str_replace('-', '', trim( $searchstr ));
            if( strlen( $searchstr )) {
                $json = file_get_contents( $config['google'][$this->api]['restget'].'&q='.urlencode( $searchstr ));
                if( !strlen( trim( $json ))) return;
                $this->result = json_decode( $json );
                $sql = "INSERT INTO extern_search_google VALUES( ".$this->db->qstr( $this->doc->id ).", ".$this->db->qstr( $this->api ).", {$this->result->totalItems}, ".$this->db->qstr( $json ).", NOW())";
                $this->db->Execute( $sql );
            }
        }
    }
    
    public function desktopCards() {
        if( !$this->result ) return '';
        if( $this->result->totalItems == 0 ) return '';


        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
        foreach( $this->result->items as $item ) {
            echo "<!-- ".print_r($item, true)."-->\n";
            if( !isset( $item->volumeInfo )) continue;
?>
    <div class="card" style="max-width: 300px;">
        <?php if( isset( $item->volumeInfo->imageLinks )) { ?>
      <img class="card-img-top" src="<?php echo $item->volumeInfo->imageLinks->thumbnail; ?>" alt="Card image cap">
       <?php } ?>
      <div class="card-block">
        <h4 class="card-title"><?php echo htmlentities( $item->volumeInfo->title ); ?></h4>
        <p class="card-text"><?php if( isset( $item->searchInfo )) echo ( $item->searchInfo->textSnippet ); ?></p>
        <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
      </div>
    </div>
<?php
        }
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
}