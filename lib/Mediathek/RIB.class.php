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
    private $sys;

    public function __construct( $baseurl ) {
        $this->baseurl = $baseurl;
        $this->data = null;
        $this->sys = null;
    }

    public function hasData() {
        return isset( $this->data['holdings']);
    }

    public function load( $sys ) {
      $this->sys = $sys;
        $url = $this->baseurl.'/search/ebi01_prod'.$sys.'?holdings=true&lang=de_DE';
        $rd = file_get_contents( $url );
        $this->data = json_decode( $rd, true );
        echo "<!-- RIB";
        echo json_encode( $this->data, JSON_PRETTY_PRINT );
        echo  "ENDRIB -->";
    }

    public function getAvailability( $signature ) {
      if( !isset( $this->data['holdings'])) return null;
      foreach( $this->data['holdings']['items'] as $item ) {
          if( $item['ilsapiid'] == 'EBI01'.$this->sys ) {
            if( isset( $item['status'] )) return $item['status'];
          }
          break;
      }
      return null;

      if( !isset( $this->data['delivery'])) return null;
      foreach( $this->data['delivery']['holding'] as $holding ) {
          if( $holding['ilsApiId'] == 'EBI01'.$this->sys && $holding['libraryCode'] == 'E75' ) {
            if( isset( $holding['availabilityStatus'] )) return $holding['availabilityStatus'];
          }
          break;
      }
        return null;
    }

    public function getThumb() {
      if( !isset( $this->data['delivery'])) return null;
      foreach( $this->data['delivery']['link'] as $link ) {
          if( $link['displayLabel'] == 'thumbnail' ) {
            return $link['linkURL'];
          }
          break;
      }
      return null;
  }

    public function getStatus() {
        if( !isset( $this->data['holdings'])) return null;
        foreach( $this->data['holdings']['items'] as $item ) {
            if( $item['ilsapiid'] == 'EBI01'.$this->sys ) {
              if( isset( $item['z30-item-status'] )) return $item['z30-item-status'];
            }
            break;
        }
        return null;
    }
}

?>
