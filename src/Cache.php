<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

defined('CACHE_DIR') || define('CACHE_DIR', BASE_DIR . trim(CONFIG['APP_PATHS']['cache'], '/') . '/');

class Cache {

  private $page_cache_file;

  public function __construct(){
    $this->page_cache_file = CACHE_DIR.'page/'.md5($_SERVER['REQUEST_URI']);
  }

  /**
   * Is page allowed to cache
   */
  private function isPageAllowedToCache(){
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
      return false;
    }

    $currentUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    foreach (CONFIG['PREVENT_PAGE_CACHE'] as $pattern) {
        $pattern = trim($pattern, '/');
        $regex = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#';
        if (preg_match($regex, $currentUri)) {
          return false;
        }
    }

    return true;
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
        if (!$this->isPageAllowedToCache()) {
          return false; 
        }
        return $this->pageCache($val);
        break;
    }
  }

  /**
   * Return stored cache if available
   * @param string $type Cache type (page, array, object etc.)
   * @param string $key Key for access the cache
   */
  public function get($type='page', $key=null){
    if ($type == 'page') {
      if (!$this->isPageAllowedToCache()) {
        return false; 
      }

      if(!file_exists($this->page_cache_file)) return false;

      if(filemtime($this->page_cache_file) < time() - CONFIG['PAGE_CACHE_LIFETIME']){
        unlink($this->page_cache_file);
        return false;
      }

      return file_get_contents($this->page_cache_file);
    }
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
    if(false !== ($f = @fopen($this->page_cache_file, 'w'))) {
      fwrite($f, "<!-- OZZ PAGE CACHED @ ".date('M d, Y h:m:s a', time())." /-->\n".$content);
      fclose($f);
    }
  }

}