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

class RIB {
    private $baseurl;
    private $data;
    
    public function __construct( $baseurl ) {
        $this->baseurl = $baseurl;
        $this->data = null;
    }
    
    public function load( $sys ) {
        $url = $this->baseurl.'/documents?q=ebi01_prod'.$sys.'&aleph_items=true&searchfield=rid';
        $rd = file_get_contents( $url );
        $this->data = (array)json_decode( $rd );
//        echo "\n<!--\n".print_r( $this->data, true )."\n-->\n";
    }
    
    public function getAvailability( $signature ) {
        if( !isset( $this->data['result'])) return null;
        foreach( $this->data['result']->document as $doc ) {
            if( isset( $doc->availability->itemList ) && is_array( $doc->availability->itemList )) {
                foreach( $doc->availability->itemList as $item ) {
                    $item = (array)$item;
                    if( $item['z30-call-no'] == $signature ) return $item;
                }
            }
        }
        return null;
    }
}

?>