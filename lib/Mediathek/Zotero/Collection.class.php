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
  var $trash = false;
  var $children = array();

  function __construct( $parent, $data, $trash = false ) {
    $this->data = $data;
    $this->trash = $trash;
    $this->parent = $parent;
    if( @is_array( $this->data['children'] )) {
      foreach( $this->data['children'] as $d ) {
        $this->addChild( new Item( $d ));
      }
    }
  }

  function __toString() {
    $str = $this->getGroupId().':'.$this->getKey().' - '.$this->getName()."\n";
    foreach( $this->children as $child ) {
      $str .= '   '.$child;
    }
    return $str;
  }

  public function isTrashed() {
    return $this->trash;
  }

  public function getData() {
      if( count( $this->children )) {
        $this->data['children'] = array();
        foreach( $this->children as $child ) {
          $this->data['children'][] = $child->getData();
        }
      }
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

  public function getNumItems() {
    return array_key_exists( 'numItems', $this->data['meta'] ) ? $this->data['meta']['numItems'] : 0;
  }

  public function getNumCollections() {
    if( !array_key_exists( 'numCollections', $this->data['meta'])) return 0;
    return intval( $this->data['meta']['numCollections'] );
  }

  public function addChild( $child ) {
      $this->children[] = $child;
  }

  public function getChild( $key ) {
    foreach( $this->children as $child ) {
      if( $child->getKey() == $key ) {
        return $child;
      }
    }
    return null;
  }

}
