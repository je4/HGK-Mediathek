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
    $sql = "SELECT MAX(version) FROM zotero.collection WHERE librarid={$id}";
    $lastversion = intval($this->db->GetOne( $sql ));
    $uri = $this->apiurl.'/groups/'.$id."/collections?since={$lastversion}";
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
        'numItems'=>$c['meta']['numItems'],
        'numCollections'=>$c['meta']['numCollections'],
        'data'=>null,
      );
      $this->db->Replace( 'zotero.collections', $replace, array( '`key`', 'libraryid' ), $autoquote=true );
      $this->db->UpdateBlob( 'zotero.collections', 'data', gzencode( $response ), "`key`=".$this->db->qstr( $c['key'] )." AND libraryid={$id}" );
    }
  }

  function syncItems( $groupid ) {
    $this->syncGroup( $groupid );
    $this->syncCollection( $groupid );

    $this->log( "Syncing items of group: {$groupid}" );

    $sql = "SELECT numItems FROM zotero.groups WHERE id={$groupid}";
    $numItems = intval( $this->db->getOne( $sql ));
    $start = 0;
    $limit = 100;
    while( $start < $numItems ) {
      $uri = "{$this->apiurl}/groups/{$groupid}/items?limit={$limit}&start={$start}";
      $response = Request::get( $uri )
        ->addHeader( 'Authorization', 'Bearer '.$this->apikey )
        ->send();

      $items = json_decode( $response, true );
      foreach( $items as $i ) {
        @$this->log( "   {$groupid}:{$i['key']} {$i['data']['itemType']} - {$i['data']['title']}" );
        $replace = array(
          '`key`'=>$i['key'],
          'libraryid'=>$groupid,
          'version'=>$i['version'],
          'parentKey'=>array_key_exists( 'parentItem', $i['data'] ) ? $i['data']['parentItem'] : null,
          'itemType'=>$i['data']['itemType'],
          'linkMode'=>array_key_exists( 'linkMode', $i['data'] ) ? $i['data']['linkMode'] : null,
          'title'=>array_key_exists( 'title', $i['data'] ) ? $i['data']['title'] : null,
          'dateModified'=>$i['data']['dateModified'],
          'dateAdded'=>$i['data']['dateAdded'],
          'data'=>null,
        );
        $this->db->Replace( 'zotero.items', $replace, array( '`key`', 'libraryid' ), $autoquote = true );
        $this->db->UpdateBlob( 'zotero.items', 'data', gzencode( $response ), "`key`=".$this->db->qstr( $i['key'] )." AND libraryid={$groupid}" );

        $sql = "DELETE FROM zotero.creators2items WHERE itemKey=".$this->db->qstr( $i['key'] );
        $this->db->Execute( $sql );

        if( @is_array( $i['data']['creators'] )) foreach( $i['data']['creators'] as $c ) {
          $sql = "SELECT id FROM zotero.creators
            WHERE ".(array_key_exists( 'lastName', $c ) ? "lastName LIKE ".$this->db->qstr( $c['lastName'] )." AND firstName LIKE ".$this->db->qstr( $c['firstName'] ) : "name LIKE ".$this->db->qstr( $c['name'] ));
          $id = intval( $this->db->getOne( $sql ));
          if( !$id ) {
            if( array_key_exists( 'lastName', $c )) $sql = "INSERT INTO zotero.creators ( lastName, firstName ) VALUES (".$this->db->qstr( $c['lastName'] ).", ".$this->db->qstr( $c['firstName'] ).")";
            else $sql = "INSERT INTO zotero.creators ( name ) VALUES (".$this->db->qstr( $c['name'] ).")";
            $this->db->Execute( $sql );
            $id = $this->db->insert_Id();
          }
          $sql = "INSERT INTO zotero.creators2items( itemKey, libraryId, role, creatorsId ) VALUES(
            ".$this->db->qstr( $i['key'] )."
            , {$i['library']['id']}
            , ".$this->db->qstr( $c['creatorType'] )."
            , {$id}
          );";
          $this->db->Execute( $sql );
        }
        $sql = "DELETE FROM zotero.items2collections WHERE itemKey=".$this->db->qstr( $i['key'] );
        $this->db->Execute( $sql );
        if( @is_array( $i['data']['collections'] )) foreach( $i['data']['collections'] as $k ) {
          $sql = "INSERT INTO zotero.items2collections( itemKey, libraryId, collectionsKey ) VALUES(
              ".$this->db->qstr( $i['key'] )."
              , {$i['library']['id']}
            , ".$this->db->qstr( $k )."
          );";
          $this->db->Execute( $sql );
        }

        if( array_key_exists( 'enclosure', $i['links'])) {
          $enc = $i['links']['enclosure'];
          if( !is_dir( "{$this->contentpath}/enclosure/{$groupid}" )) mkdir( "{$this->contentpath}/enclosure/{$groupid}" );
          $filename_rel = "enclosure/{$groupid}/{$i['key']}";
          $filename = "{$this->contentpath}/{$filename_rel}";
          $uri = $enc['href'];
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
            if( $enc['type'] == 'application/pdf' ) {
              $cmd = 'pdftotext '.escapeshellarg( $filename ).' '.escapeshellarg( $filename.'.txt' );
              $this->log( "      Extracting fulltext: $filename_rel" );
              $fulltext = shell_exec( $cmd );
            }
            $replace = array(
              '`key`'=>$i['key'],
              'libraryid'=>$groupid,
              'type'=>array_key_exists( 'type', $enc ) ? $enc['type'] : null,
              'href'=>array_key_exists( 'href', $enc ) ? $enc['href'] : null,
              'title'=>array_key_exists( 'title', $enc ) ? $enc['title'] : null,
              'length'=>array_key_exists( 'length', $enc ) ? $enc['length'] : null,
              'filename'=>$filename_rel,
              '`fulltext`'=>null,
            );
            $this->db->Replace( 'zotero.enclosures', $replace, array( '`key`', 'libraryid' ), $autoquote = true );
            $this->db->UpdateBlob( 'zotero.enclosures', '`fulltext`', gzencode( file_get_contents( $filename.'.txt' ) ), "`key`=".$this->db->qstr( $i['key'] )." AND libraryid={$groupid}" );
          }
        }
      }
      $start += $limit;
    }
  }
}
