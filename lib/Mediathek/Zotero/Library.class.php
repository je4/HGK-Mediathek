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
 * @subpackage  Zotero
 * @author      Juergen Enge (juergen@info-age.net)
 * @copyright   (C) 2016 Academy of Art and Design FHNW
 * @license     http://www.gnu.org/licenses/gpl-3.0
 * @link        http://mediathek.fhnw.ch
 *
 */

/**
 * @namespace
 */

namespace Mediathek\Zotero;

class Library {

  function __construct( $data ) {
    $this->data = $data;
  }

  function __toString() {
    return '#'.$this->getId().' '.$this->getName();
  }

  public function getData() {
      return $this->data;
  }

  public function getId() {
    return $this->data['id'];
  }

  public function getName() {
    return array_key_exists( 'name', $this->data['data'] ) ? $this->data['data']['name'] : '';
  }

  public function getDescription() {
    return array_key_exists( 'name', $this->data['data'] ) ? $this->data['data']['description'] : '';
  }

  public function getNumItems() {
    return array_key_exists( 'numItems', $this->data['meta'] ) ? $this->data['meta']['numItems'] : 0;
  }
}