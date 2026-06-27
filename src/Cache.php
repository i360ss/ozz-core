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
  private $cms_config_cache_file;

  public function __construct(){
    $this->page_cache_file = CACHE_DIR.'page/'.md5($_SERVER['REQUEST_URI']);
    $this->cms_config_cache_file = CACHE_DIR.'cms/cache.config.php';
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
   * @param array|object|string $val Cache value
   */
  public function store($type, $val){
    switch ($type) {
      case 'page':
        if (!$this->isPageAllowedToCache()) {
          return false; 
        }
        return $this->createPageCache($val);
        break;
      case 'cms_config':
        return $this->createCmsConfigCache($val);
        break;
    }
  }

  /**
   * Return stored cache if available
   * @param string $type Cache type (page, array, object etc.)
   */
  public function get($type='page'){
    if ($type == 'page') {
      // Page Cache
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
    elseif ($type == 'cms_config') {
      // CMS Config Cache
      if (!file_exists($this->cms_config_cache_file)) {
        $this->createCmsConfigCache();
      }
      return require $this->cms_config_cache_file;
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
  private function createPageCache($content){
    if(false !== ($f = @fopen($this->page_cache_file, 'w'))) {
      fwrite($f, "<!-- OZZ PAGE CACHED @ ".date('M d, Y h:m:s a', time())." /-->\n".$content);
      fclose($f);
    }
  }

  /**
   * CMS configurations cache
   * @param array Compiled CMS configuration
   */
  private function createCmsConfigCache($content=false) {
    if (!$content) {
      $content = require CMS_DIR.'cms-config.php';
    }

    $dir = dirname($this->cms_config_cache_file);
    if (!is_dir($dir)) {
      mkdir($dir, 0755, true);
    }

    $php = "<?php\n";
    $php .= "// OZZ CMS CONFIG CACHE\n";
    $php .= "// Generated: " . date('Y-m-d H:i:s') . "\n";
    $php .= "return " . var_export($content, true) . ";\n";

    file_put_contents($this->cms_config_cache_file, $php, LOCK_EX);
  }

}