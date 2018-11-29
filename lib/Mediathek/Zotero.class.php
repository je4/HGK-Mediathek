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

  static function mimeFromURL( $url ) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, trim($url, "'") );
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $ret = curl_exec($ch);
    $code = intval( curl_getinfo( $ch, CURLINFO_RESPONSE_CODE ));
    $mimetype = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    if( $code == 404 || $code == 500 ) {
      throw( new \Exception( "Response Error {$code}" ));
    }
    echo "mimetype: {$mimetype} - {$url}\n";
    return $mimetype;
  }

  static function dataFromURL( $url ) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, trim($url, "'") );
    curl_setopt($ch, CURLOPT_HEADER, 0);
//    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $ret = curl_exec($ch);
    $code = intval( curl_getinfo( $ch, CURLINFO_RESPONSE_CODE ));
    curl_close($ch);
    if( $code == 404 || $code == 500 ) {
      throw( new \Exception( "Response Error {$code}" ));
    }
    return $ret;
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
    global $config;

    @$this->log( "   {$id}:{$item['key']} ".($trash?"[TRASH] ":"")."{$item['data']['itemType']} - {$item['data']['title']}" );

    if( array_key_exists( 'linkMode', $item['data'] )) {
      $linkMode = $item['data']['linkMode'];
      if( $linkMode == 'linked_url' ) {
        try {
          $url = $item['data']['url'];
          foreach( $config['http2regexp'] as $pattern=>$replace ) {
            $url = preg_replace( $pattern, $replace, $url );
          }
          // check for nfs file
          if( preg_match( "#file://[^/]+({$config['nfsdata']}.+)$#", $url, $matches )) {
            // get collection id
            $collectionname = "zotero_{$item['library']['id']}";
            $signature = "{$item['library']['id']}.{$item['key']}";
            $sql = "SELECT collectionid FROM mediaserver.collection WHERE name=".$this->db->qstr( $collectionname );
            $collectionid = intval( $this->db->getOne( $sql ));
            if( $collectionid == 0 ) die( 'library has no collection: '.$sql );
            $sql = "INSERT INTO mediaserver.master( collectionid, signature, urn ) VALUES( {$collectionid}, ".$this->db->qstr( $signature ).", ".$this->db->qstr( $url ).")";
            echo "{$sql}\n";
            try {
              $this->db->Execute( $sql );
            }
            catch( \Exception $e ) {
              echo $e->getMessage()."\n";
            }
            $url = "{$config['mediaserver']['baseurl']}{$collectionname}/{$signature}";
            $item['data']['url'] = "mediaserver:{$collectionname}/{$signature}";
            $metaurl = $url.'/metadata';
            if( isset( $config['mediaserver']['key'] )) {
              $metaurl .= '?token='.jwt_encode(
                    ['sub'=>"{$config['mediaserver']['sub_prefix']}{$collectionname}/{$signature}/metadata", 'exp'=>time()+1000],
                    $config['mediaserver']['key']
                  );
            }
            echo "loading metadata from {$metaurl}\n";
            try {
              $meta = $this->dataFromURL( $metaurl );
              $metaarr = json_decode( $meta, true );
              $item['data']['media'] = array( 'metadata'=>$metaarr );
              $mimetype = array_key_exists( 'mimetype', $metaarr ) ? $metaarr['mimetype'] : 'application/octet-stream';
              //var_dump( $metaarr );
            }
            catch( \Exception $ex ) {
              echo( $ex->getMessage());
            }
            //die();
          }
          // prüfen, ob es sich um einen indexer-link handelt...
          elseif( preg_match( '/^http:\/\/hdl.handle.net\/20.500.11806\/mediathek\/inventory\/[^\/]+\/([0-9.]+)$/', $url, $matches )) {
						$url = "{$config['mediaserver']['baseurl']}zotero_{$item['library']['id']}/indexer{$matches[1]}";
            $item['data']['url'] = "mediaserver:zotero_{$item['library']['id']}/indexer{$matches[1]}";
            $metaurl = $url.'/metadata';
            if( isset( $config['mediaserver']['key'] )) {
              $metaurl .= '?token='.jwt_encode(
                    ['sub'=>"{$config['mediaserver']['sub_prefix']}zotero_{$item['library']['id']}/indexer{$matches[1]}/metadata", 'exp'=>time()+1000],
                    $config['mediaserver']['key']
                  );
            }
            echo "loading metadata from {$metaurl}\n";
            try {
              $meta = $this->dataFromURL( $metaurl );
              $metaarr = json_decode( $meta, true );
              $item['data']['media'] = array( 'metadata'=>$metaarr );
              $mimetype = $metaarr['mimetype'];
            }
            catch( \Exception $ex ) {
              echo( $ex->getMessage());
            }
            //print_r( $item );
            //die("hdl link done");
					}
          // prüfen auf Grenzgang Link
          elseif( preg_match( '/^http:\/\/(https?\/\/)?media:(.+)$/', $url, $matches )) {
            $collection = 'zotero_2250437';
            $signature = $matches[2];
						$url = "{$config['mediaserver']['baseurl']}{$collection}/{$signature}";
            $item['data']['url'] = "mediaserver:{$collection}/{$signature}";
            $metaurl = $url.'/metadata';
            if( isset( $config['mediaserver']['key'] )) {
              $metaurl .= '?token='.jwt_encode(
                    ['sub'=>"{$config['mediaserver']['sub_prefix']}{$collection}/{$signature}/metadata", 'exp'=>time()+1000],
                    $config['mediaserver']['key']
                  );
            }
            echo "loading metadata from {$metaurl}\n";
            try {
              $meta = $this->dataFromURL( $metaurl );
              $metaarr = json_decode( $meta, true );
              $item['data']['media'] = array( 'metadata'=>$metaarr );
              $mimetype = $metaarr['mimetype'];
            }
            catch( \Exception $ex ) {
              echo( $ex->getMessage());
            }
            //print_r( $item );
            //die("hdl link done");
					}
          elseif( preg_match( '/^http:\/\/media\/(.+)$/', $url, $matches )) {
            $coll_sig = $matches[1];
						$url = "{$config['mediaserver']['baseurl']}{$coll_sig}";
            $item['data']['url'] = "mediaserver:{$coll_sig}";
            $metaurl = $url.'/metadata';
            if( isset( $config['mediaserver']['key'] )) {
              $metaurl .= '?token='.jwt_encode(
                    ['sub'=>"{$config['mediaserver']['sub_prefix']}{$coll_sig}/metadata", 'exp'=>time()+1000],
                    $config['mediaserver']['key']
                  );
            }
            echo "loading metadata from {$metaurl}\n";
            try {
              $meta = $this->dataFromURL( $metaurl );
              $metaarr = json_decode( $meta, true );
              $item['data']['media'] = array( 'metadata'=>$metaarr );
              $mimetype = $metaarr['mimetype'];
            }
            catch( \Exception $ex ) {
              echo( $ex->getMessage());
            }
            //print_r( $item );
            //die("hdl link done");
					}
          else {
            $mimetype = $this->mimeFromURL( $url );
            $item['data']['urlMimetype'] = $mimetype;
            $item['data']['nameImageserver'] = "zotero/zotero-{$id}-{$item['key']}";
          }
          $sql = "REPLACE INTO dirty_image_server.image( name, mimetype, sourcetype, source )
            VALUES( ".$this->db->qstr( "zotero-{$id}-{$item['key']}" ).", ".$this->db->qstr( $mimetype ).", 'remote', ".$this->db->qstr($item['data']['url'])." )";
          //$this->db->Execute( $sql );
        }
        catch( \Exception $e ) {
          echo "{$e}\n";
        }
      }
    }
//    print_r( $item['data'] );

if( array_key_exists( 'enclosure', $item['links'])) {
  $enc = $item['links']['enclosure'];
  if( !is_dir( "{$this->contentpath}/enclosure/{$id}" )) {
    echo "mkdir {$this->contentpath}\n";
    mkdir( "{$this->contentpath}/enclosure/{$id}" );
    chmod( "{$this->contentpath}/enclosure/{$id}", 0777 );
    //die();
  }
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
      chmod( $filename, 0666 );
    }
  }
  if( filesize( $filename )) {
    $collectionname = "zotero_{$item['library']['id']}";
    $signature = "{$item['library']['id']}.{$item['key']}_enclosure";
    $sql = "SELECT collectionid FROM mediaserver.collection WHERE name=".$this->db->qstr( $collectionname );
    $collectionid = intval( $this->db->getOne( $sql ));
    if( $collectionid == 0 ) die( 'library has no collection: '.$sql );
    $sql = "INSERT INTO mediaserver.master( collectionid, signature, urn ) VALUES( {$collectionid}, ".$this->db->qstr( $signature ).", ".$this->db->qstr( "file://ba14ns21403.fhnw.ch{$filename}" ).")";
    echo "{$sql}\n";
    try {
      $this->db->Execute( $sql );
    }
    catch( \Exception $e ) {
      echo $e->getMessage()."\n";
    }
    $url = "{$config['mediaserver']['baseurl']}{$collectionname}/{$signature}";
    $item['data']['url'] = "mediaserver:{$collectionname}/{$signature}";
    $metaurl = $url.'/metadata';
    if( isset( $config['mediaserver']['key'] )) {
      $metaurl .= '?token='.jwt_encode(
            ['sub'=>"{$config['mediaserver']['sub_prefix']}{$collectionname}/{$signature}/metadata", 'exp'=>time()+1000],
            $config['mediaserver']['key']
          );
    }
    echo "loading metadata from {$metaurl}\n";
    try {
      $meta = $this->dataFromURL( $metaurl );
      $metaarr = json_decode( $meta, true );
      $item['data']['media'] = array( 'metadata'=>$metaarr );
      $mimetype = $metaarr['mimetype'];
      //var_dump( $metaarr );
    }
    catch( \Exception $ex ) {
      echo( $ex->getMessage());
    }
  }
}


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
    //if( $item['key'] == 'ISH7TPLH') die( 'ISH7TPLH' );
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
    //$lastversion = 0;
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
    $data = json_decode( $json, true );
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
    $library = $this->loadLibrary( $groupid );

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
      $data['group'] = $library->getData();

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
