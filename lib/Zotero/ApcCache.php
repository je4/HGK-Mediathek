<?php
namespace Zotero;
/**
 * APC backed cache implementing interface required for Library caching
 * 
 * @package libZotero
 */
class ApcCache
{
    public $prefix = 'LibZotero';
    
    public function __construct(){
        if(!extension_loaded('apc')){
            if(!extension_loaded('apcu')){
                throw new \Zotero\Exception('APC not loaded');
            }
        }
    }
    
    public function add($key, $val, $ttl=0){
        return \apc_add($key, $val, $ttl);
    }
    
    public function store($key, $val, $ttl=0){
        return \apc_store($key, $val, $ttl);
    }
    
    public function delete($key){
        return \apc_delete($key);
    }
    
    public function fetch($key, &$success){
        return \apc_fetch($key, $success);
    }
    
    public function exists($keys){
        return \apc_exists($keys);
    }
}

