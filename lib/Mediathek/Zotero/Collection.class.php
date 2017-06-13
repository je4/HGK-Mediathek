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

class Collection {
  var $parent = null;

  function __construct( $data ) {
    $this->data = $data;
    if( array_key_exists( 'parent', $this->data )) {
      $p = new Collection( $this->data['parent']);
      $this->setParent( $p );
    }
  }

  function __toString() {
    $str = $this->getGroupId().':'.$this->getKey().' - '.$this->getName()."\n";
    foreach( $this->children as $child ) {
      $str .= '   '.$child;
    }
    return $str;
  }

  public function getParentKey() {
    return $this->data['data']['parentCollection'] ? $this->data['data']['parentCollection'] : null;
  }

  public function setParent( $parent ) {
    $this->parent = $parent;
  }

  public function getData() {
      if( $this->parent ) $this->data['parent'] = $this->parent->getData();
      return $this->data;
  }

  public function getGroupId() {
    return $this->data['library']['id'];
  }

  public function getKey() {
    return $this->data['key'];
  }

  public function getName() {
    return array_key_exists( 'name', $this->data['data'] ) ? $this->data['data']['name'] : '';
  }

  public function getFullName() {
    $pname = '';
    if( $this->parent ) $pname = $this->parent->getFullName().':';
    return $pname.$this->getName();
  }

  public function getNames() {
    $names = array();
    if( $this->parent ) $names = $this->parent->getNames();
    $names[] = $this->getName();
    return $names;
  }

  public function getNumItems() {
    return array_key_exists( 'numItems', $this->data['meta'] ) ? $this->data['meta']['numItems'] : 0;
  }

  public function getNumCollections() {
    if( !array_key_exists( 'numCollections', $this->data['meta'])) return 0;
    return intval( $this->data['meta']['numCollections'] );
  }

}
