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

namespace Mediathek;

use \Httpful\Request;
use \Mediathek\Zotero\Library;
use \Mediathek\Zotero\Item;
use \Mediathek\Zotero\Collection;

class Zotero {
  var $db;
  var $apikey;
  var $apiurl;
  var $contentpath;
  var $out;

  function __construct( $db, $apiurl, $apikey, $contentpath, $out = null  ) {
    $this->db = $db;
    $this->apiurl = $apiurl;
    $this->apikey = $apikey;
    $this->contentpath = $contentpath;
    $this->out = $out;
  }

  private function log( $str ) {
    if( $this->out == null ) return;
    fwrite( $this->out, $str."\n" );
  }

  function syncGroup( $id ) {
    $this->log( "Syncing group: {$id}" );
    $uri = $this->apiurl.'/groups/'.$id;
    $response = Request::get( $uri )
      ->addHeader( 'Authorization', 'Bearer '.$this->apikey )
      ->send();
  //    echo( $response );
    $g = json_decode( $response, true );

    $this->log( "   {$g['data']['name']}");
    $replace = array(
      'id'=>$g['id'],
      'version'=>$g['version'],
      'name'=>$g['data']['name'],
      'type'=>$g['data']['type'],
      'numItems'=>$g['meta']['numItems'],
      'dateAdded'=>$g['meta']['created'],
      'dateModified'=>$g['meta']['lastModified'],
      'data'=>null,
    );
    $this->db->Replace( 'zotero.groups', $replace, array( 'id' ), $autoquote=true );
    $this->db->UpdateBlob( 'zotero.groups', 'data', gzencode( $response ), "id={$id}" );

  }

  function syncCollection( $id ) {
    $this->log( "Syncing collections of group: {$id}" );
    $sql = "SELECT MAX(version) FROM zotero.collections WHERE libraryid={$id}";
    $lastversion = intval($this->db->GetOne( $sql ));

    $start = 0;
    $limit = 100;
    do {
      $uri = $this->apiurl.'/groups/'.$id."/collections?limit={$limit}&start={$start}&since={$lastversion}";
      $response = Request::get( $uri )
        ->addHeader( 'Authorization', 'Bearer '.$this->apikey )
        ->send();
    //    echo( $response );
      $cs = json_decode( $response, true );
      foreach( $cs as $c ) {
        $this->log( "   {$c['key']}:{$c['data']['name']}");

        $replace = array(
          '`key`'=>$c['key'],
          'libraryid'=>$id,
          'version'=>$c['version'],
          'name'=>$c['data']['name'],
          'parentCollection'=>$c['data']['parentCollection'],
          'numItems'=>$c['meta']['numItems'],
          'numCollections'=>$c['meta']['numCollections'],
          'data'=>null,
        );
        $this->db->Replace( 'zotero.collections', $replace, array( '`key`', 'libraryid' ), $autoquote=true );
        $this->db->UpdateBlob( 'zotero.collections', 'data', gzencode( json_encode( $c ) ), "`key`=".$this->db->qstr( $c['key'] )." AND libraryid={$id}" );
      }
      $start += $limit;
    } while( count( $cs ) == 100 );
  }

  function syncItem( $id, $item, $trash ) {
    @$this->log( "   {$id}:{$item['key']} ".($trash?"[TRASH] ":"")."{$item['data']['itemType']} - {$item['data']['title']}" );
    $replace = array(
      '`key`'=>$item['key'],
      'libraryid'=>$id,
      'version'=>$item['version'],
      'parentKey'=>array_key_exists( 'parentItem', $item['data'] ) ? $item['data']['parentItem'] : null,
      'itemType'=>$item['data']['itemType'],
      'linkMode'=>array_key_exists( 'linkMode', $item['data'] ) ? $item['data']['linkMode'] : null,
      'title'=>array_key_exists( 'title', $item['data'] ) ? $item['data']['title'] : null,
      'trash'=>$trash,
      'dateModified'=>$item['data']['dateModified'],
      'dateAdded'=>$item['data']['dateAdded'],
      'data'=>null,
    );
    $this->db->Replace( 'zotero.items', $replace, array( '`key`', 'libraryid' ), $autoquote = true );
    $this->db->UpdateBlob( 'zotero.items', 'data', gzencode( json_encode( $item )), "`key`=".$this->db->qstr( $item['key'] )." AND libraryid={$id}" );

    $sql = "DELETE FROM zotero.creators2items WHERE itemKey=".$this->db->qstr( $item['key'] );
    $this->db->Execute( $sql );

    if( @is_array( $item['data']['creators'] )) foreach( $item['data']['creators'] as $c ) {
      $sql = "SELECT id FROM zotero.creators
        WHERE ".(array_key_exists( 'lastName', $c ) ? "lastName LIKE ".$this->db->qstr( $c['lastName'] )." AND firstName LIKE ".$this->db->qstr( $c['firstName'] ) : "name LIKE ".$this->db->qstr( $c['name'] ));
      $cid = intval( $this->db->getOne( $sql ));
      if( !$cid ) {
        if( array_key_exists( 'lastName', $c )) $sql = "INSERT INTO zotero.creators ( lastName, firstName ) VALUES (".$this->db->qstr( $c['lastName'] ).", ".$this->db->qstr( $c['firstName'] ).")";
        else $sql = "INSERT INTO zotero.creators ( name ) VALUES (".$this->db->qstr( $c['name'] ).")";
        $this->db->Execute( $sql );
        $cid = $this->db->insert_Id();
      }
      $sql = "INSERT INTO zotero.creators2items( itemKey, libraryId, role, creatorsId ) VALUES(
        ".$this->db->qstr( $item['key'] )."
        , {$item['library']['id']}
        , ".$this->db->qstr( $c['creatorType'] )."
        , {$cid}
      );";
      $this->db->Execute( $sql );
    }
    $sql = "DELETE FROM zotero.items2collections WHERE itemKey=".$this->db->qstr( $item['key'] );
    $this->db->Execute( $sql );
    if( @is_array( $item['data']['collections'] )) foreach( $item['data']['collections'] as $k ) {
      $sql = "INSERT INTO zotero.items2collections( itemKey, libraryId, collectionsKey ) VALUES(
          ".$this->db->qstr( $item['key'] )."
          , {$item['library']['id']}
        , ".$this->db->qstr( $k )."
      );";
      $this->db->Execute( $sql );
    }

    if( array_key_exists( 'enclosure', $item['links'])) {
      $enc = $item['links']['enclosure'];
      if( !is_dir( "{$this->contentpath}/enclosure/{$id}" )) mkdir( "{$this->contentpath}/enclosure/{$id}" );
      $filename_rel = "enclosure/{$id}/{$item['key']}";
      $filename = "{$this->contentpath}/{$filename_rel}";
      $uri = $enc['href'];
      if( !file_exists( $filename )) {
        $this->log( "      Downloading file: {$uri}" );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer '.$this->apikey ));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        /*
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $response = curl_exec ($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        print_r( $header );
        */
        $body = curl_exec ($ch);
        file_put_contents( $filename,  $body );
        if( filesize( $filename )) {
          $fulltext = null;
          $pages = null;
          switch( $enc['type'] ) {
            case 'application/pdf':
              $cmd = 'pdftotext '.escapeshellarg( $filename ).' '.escapeshellarg( $filename.'.txt' );
              $this->log( "      Extracting fulltext (pdf): $filename_rel" );
              shell_exec( $cmd );
              /*
              $dir = $filename.'_pages';
              if( !is_dir( $dir )) mkdir( $dir );
              $cmd = 'convert '.escapeshellarg( $filename ).' -resize x800 '.escapeshellarg( $dir.'/'.$item['key'].'-%03d.jpg' );
              $this->log( "      Generating pages (pdf): $filename_rel" );
              shell_exec( $cmd );
              */
              $cmd = 'pdfinfo '.escapeshellarg( $filename );
              $this->log( "      Counting pages (pdf): $filename_rel" );
              $ret = shell_exec( $cmd );
              if( preg_match( '/Pages:\s*(\d+)/i', $ret, $matches )) {
                $pages = intval( $matches[1] );
              }
              break;
              case 'text/html':
                $text = file_get_contents( $filename );
                $text = preg_replace(
                    array(
                      // Remove invisible content
                        '@<head[^>]*?>.*?</head>@siu',
                        '@<style[^>]*?>.*?</style>@siu',
                        '@<script[^>]*?.*?</script>@siu',
                        '@<object[^>]*?.*?</object>@siu',
                        '@<embed[^>]*?.*?</embed>@siu',
                        '@<applet[^>]*?.*?</applet>@siu',
                        '@<noframes[^>]*?.*?</noframes>@siu',
                        '@<noscript[^>]*?.*?</noscript>@siu',
                        '@<noembed[^>]*?.*?</noembed>@siu',
                      // Add line breaks before and after blocks
                        '@</?((address)|(blockquote)|(center)|(del))@iu',
                        '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
                        '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
                        '@</?((table)|(th)|(td)|(caption))@iu',
                        '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
                        '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
                        '@</?((frameset)|(frame)|(iframe))@iu',
                    ),
                    array(
                        ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
                        "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
                        "\n\$0", "\n\$0",
                    ),
                    $text );
                $text = strip_tags( $text );
                file_put_contents( $filename.'.txt', $text);
                $this->log( "      Extracting fulltext (html): $filename_rel" );
                break;
              case 'text/plain':
                file_put_contents( $filename.'.txt', file_get_contents( $filename ));
                $this->log( "      Extracting fulltext (text): $filename_rel" );
                break;
          }
          $replace = array(
            '`key`'=>$item['key'],
            'libraryid'=>$id,
            'type'=>array_key_exists( 'type', $enc ) ? $enc['type'] : null,
            'href'=>array_key_exists( 'href', $enc ) ? $enc['href'] : null,
            'title'=>array_key_exists( 'title', $enc ) ? $enc['title'] : null,
            'pages'=>$pages,
            'length'=>array_key_exists( 'length', $enc ) ? $enc['length'] : null,
            'filename'=>$filename_rel,
            '`fulltext`'=> null,
          );
          $this->db->Replace( 'zotero.enclosures', $replace, array( '`key`', 'libraryid' ), $autoquote = true );
          $this->db->UpdateBlob( 'zotero.enclosures', '`fulltext`', file_exists( $filename.'.txt' ) ? gzencode( file_get_contents( $filename.'.txt' )) : null, "`key`=".$this->db->qstr( $item['key'] )." AND libraryid={$id}" );
        }
      }
    }
  }

  function syncItems( $id ) {
    $this->syncGroup( $id );
    $this->syncCollection( $id );
    $library = $this->loadLibrary($id);

    $this->log( "Syncing items of group: {$id}" );

    $start = 0;
    $limit = 100;
    $sql = "SELECT MAX(version) FROM zotero.items WHERE trash=false AND libraryid={$id}";
    $lastversion = intval($this->db->GetOne( $sql ));

    do {
      $uri = "{$this->apiurl}/groups/{$id}/items?limit={$limit}&start={$start}&since={$lastversion}";
      $response = Request::get( $uri )
        ->addHeader( 'Authorization', 'Bearer '.$this->apikey )
        ->send();

      $items = json_decode( $response, true );
      foreach( $items as $i ) {
        $i['group'] = $library->getData();
        $this->syncItem( $id, $i, false );
      }
      $start += $limit;
    } while( count( $items ) == 100 );

    $start = 0;
    $limit = 100;
    $sql = "SELECT MAX(version) FROM zotero.items WHERE libraryid={$id} AND trash=true";
    $lastversion = intval($this->db->GetOne( $sql ));

    do {
      $uri = "{$this->apiurl}/groups/{$id}/items/trash?limit={$limit}&start={$start}&since={$lastversion}";
      $response = Request::get( $uri )
        ->addHeader( 'Authorization', 'Bearer '.$this->apikey )
        ->send();

      $items = json_decode( $response, true );
      foreach( $items as $i ) {
        $i['group'] = $library->getData();
        $this->syncItem( $id, $i, true );
      }
      $start += $limit;
    } while( count( $items ) == 100 );
  }

  public function loadLibrary($groupid) {
    $sql = "SELECT data FROM zotero.groups WHERE id={$groupid}";
    $json = gzdecode($this->db->GetOne( $sql ));
    $data = json_decode( $json );
    return new Library( $data );
  }

  public function loadCollections($groupid) {
    $collections = array();
    $sql = "SELECT data FROM zotero.collections WHERE libraryid={$groupid}";
    $rs = $this->db->Execute( $sql );
    foreach( $rs as $row ) {
      $data = json_decode( gzdecode( $row['data']), true );
      $coll = new Collection( $data );
      $collections[$coll->getKey()] = $coll;
    }
    $rs->Close();
    foreach( $collections as $coll ) {
      $pKey = $coll->getParentKey();
      if( $pKey ) {
        if( !array_key_exists( $pKey, $collections )) {
          print_r( array_keys( $collections ));
          die( "Collection {$pKey} not found");
        }
        $coll->setParent( $collections[$pKey]);
      }
    }
    return $collections;
  }

  public function loadChildren( $groupid, $itemkey=null ) {
    $collections = $this->loadCollections($groupid);
//    $group = $this->loadGroup($groupid);

    $sql = "SELECT data, trash FROM zotero.items WHERE libraryid={$groupid} AND parentKey".($itemkey ? '='.$this->db->qstr( $itemkey ) : ' IS NULL');
    if( $itemkey ) $sql .= " AND trash=false";
    //echo "{$sql}\n";
    $rs = $this->db->Execute( $sql );
    foreach( $rs as $row ) {
      $data = json_decode( gzdecode( $row['data']), true );
      $trash = $row['trash'];
      $pages = 0;
      $sql = "SELECT pages, `fulltext` FROM zotero.enclosures WHERE libraryid={$groupid} AND `key`=".$this->db->qstr( $data['key'] );
      $row2 = $this->db->getRow( $sql );
      if( $row2 ) {
        $pages = intval( $row2['pages']);
        if( strlen( $row2['fulltext'])) {
          $data['fulltext'] = gzdecode( $row2['fulltext'] );
        }
      }
      $data['data']['pages'] = $pages;
//      $data['group'] = $group->getData();

      $item = new Item( $data, $trash );
      if( $item->numChildren()) {
        foreach( $this->loadChildren( $groupid, $item->getKey()) as $child ) {
          $item->addChild( $child );
        }
      }
      foreach( $item->getCollectionKeys() as $key ) {
        if( array_key_exists( $key, $collections )) {
          $item->addCollection( $collections[$key] );
        }
      }
      yield $item;
    }
    $rs->Close();
  }


}
