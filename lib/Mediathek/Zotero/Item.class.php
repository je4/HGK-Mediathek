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

class Item {
  var $parent = null;
  var $trash = false;
  var $children = array();

  function __construct( $data, $trash = false ) {
    $this->data = $data;
    $this->trash = $trash;
    if( @is_array( $this->data['children'] )) {
      foreach( $this->data['children'] as $d ) {
        $this->addChild( new Item( $d ));
      }
    }
  }

  function __toString() {
    $str = $this->getGroupId().':'.$this->getKey().' ['.$this->getType().'] - '.$this->getTitle()."\n";
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

  public function getType() {
    return $this->data['data']['itemType'];
  }

  public function getNote() {
    return $this->data['data']['note'];
  }

  public function getTitle() {
    return array_key_exists( 'title', $this->data['data'] ) ? $this->data['data']['title'] : '';
  }

  public function getPublisher() {
    return array_key_exists( 'publisher', $this->data['data'] ) ? $this->data['data']['publisher'] : null;
  }

  public function getISBN() {
    return array_key_exists( 'ISBN', $this->data['data'] ) ? $this->data['data']['ISBN'] : null;
  }

  public function getPages() {
    return array_key_exists( 'pages', $this->data['data'] ) ? $this->data['data']['pages'] : 0;
  }

  public function getCreators() {
    if( !array_key_exists( 'creators', $this->data['data'] )) return array();
    $cs = array();
    foreach( $this->data['data']['creators'] as $c ) {
      $creator = new Creator( $c );
      $cs[] = $creator;
    }

    return $cs;
  }

  public function getTags() {
    if( !array_key_exists( 'tags', $this->data['data'] )) return array();

    $tags = array();
    foreach( $this->data['data']['tags'] as $tag ) {
      if( array_key_exists( 'tag', $tag )) $tags[] = $tag['tag'];
    }
    return $tags;
  }

  public function getYear() {
    //print_r($this->data['meta']['parsedDate'] );
    return array_key_exists( 'parsedDate', $this->data['meta'] ) ? intval( $this->data['meta']['parsedDate'] ) : null;
  }

  public function getPlace() {
    return array_key_exists( 'place', $this->data['data'] ) ? $this->data['data']['place'] : null;
  }

  public function getRights() {
    return array_key_exists( 'rights', $this->data['data'] ) ? array( $this->data['data']['rights'] ) : array();
  }

  public function getLanguage() {
    return array_key_exists( 'language', $this->data['data'] ) ? $this->data['data']['language'] : null;
  }

  public function getUrl() {
    return array_key_exists( 'url', $this->data['data'] ) ? $this->data['data']['url'] : null;
  }

    public function getContentType() {
      return array_key_exists( 'contentType', $this->data['data'] ) ? $this->data['data']['contentType'] : null;
    }

    public function getFilename() {
      return array_key_exists( 'filename', $this->data['data'] ) ? $this->data['data']['filename'] : null;
    }



  public function getUrls() {
    $urls = array();
    if( $this->getUrl()) $urls[] = $this->getUrl();
    foreach( $this->children as $child ) {
      $urls = array_merge( $urls, $child->getUrls());
    }
    return $urls;
  }

  public function getAbstract() {
    $abstract = array_key_exists( 'abstractNote', $this->data['data'] ) ? $this->data['data']['abstractNote'] : '';
    if( $this->getUrl()) $urls[] = $this->getUrl();
    foreach( $this->children as $child ) {
      $abstract .= "\n".$child->getAbstract();
    }
    return trim( $abstract );
  }


  public function numChildren() {
    if( !array_key_exists( 'numChildren', $this->data['meta'])) return 0;
    return intval( $this->data['meta']['numChildren'] );
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

  public function getNotes() {
    $notes = array();
    foreach( $this->children as $child ) {
      if( $child->getType() == 'note' ) $notes[] = $child;
    }
    return $notes;
  }

  public function getImages() {
    return $this->getAttachments( array( '/image\//' ));
  }

  public function getPDFs() {
    return $this->getAttachments( array( '/application\/pdf/' ));
  }
  public function getAttachments( $types = array()) {
    $pdfs = array();
    foreach( $this->children as $child ) {
      if( $child->getType() == 'attachment' ) {
        $contentType = $child->getContentType();
        if( count( $types ) == 0 ) $pdfs[] = $child;
        else foreach( $types as $type ) {
          if( preg_match( $type, $contentType )) $pdfs[] = $child;
        }
      }
    }
    return $pdfs;
  }

}
