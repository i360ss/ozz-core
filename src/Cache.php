<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Cache {

  private $cache_file;

  public function __construct(){
    $this->cache_file = CACHE_DIR.'page/'.md5($_SERVER['REQUEST_URI']);
  }

  /**
   * Store Cache
   * @param string $type Cache type (page, array, object etc.)
   * @param string $key Key for access the cache
   * @param array|object|string Cache value
   */
  public function store($type, $key, $val){
    switch ($type) {
      case 'page':
        return $this->pageCache($val);
        break;
    }
  }

  /**
   * Return stored cache if available
   * @param string $type Cache type (page, array, object etc.)
   * @param string $key Key for access the cache
   */
  public function get($type, $key){
    if(!file_exists($this->cache_file)) return false;
    if(filemtime($this->cache_file) < time() - PAGE_CACHE_TIME){
      unlink($this->cache_file);
      return false;
    }

    return readfile($this->cache_file);
  }

  /**
   * Purge cache
   * @param string $type Cache type to clear
   * @param string $key Cache key (Optional)
   */
  public function purge($type, $key=false){
    // Clear all page caches
    if($type == 'page'){
      $cache_files = glob(CACHE_DIR.'page/*'); 
      foreach($cache_files as $file) {
        $fp = explode('/', $file);
        if(end($fp) !== '.gitkeep'){
          if(is_file($file)) unlink($file);
        }
      }
    }
  }

  /**
   * Page Cache
   * @param string|HTML Full page content to cache
   */
  private function pageCache($content){
    if(false !== ($f = @fopen($this->cache_file, 'w'))) {
      fwrite($f, "<!-- OZZ PAGE CACHED @ ".date('M d, Y h:m:s a', time())." /-->\n".$content);
      fclose($f);
    }
  }

}