<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
* OZZ DEFINED FUNCTIONS
*/

// Check if functions already declared
if(!function_exists('ozz_func_loaded')) {

  /**
   * Get .env values
   * @param string $key the key of .env value
   */
  function env($key=null, $key2=null){
    $env = parse_ini_file(__DIR__.SPC_BACK['core_1'].'env.ini', true);
    if($key !== null && $key2 !== null){
      return $env[$key][$key2];
    } elseif($key !== null){
      return $env[$key];
    } else {
      return $env;
    }
  }

  require __DIR__.'/functions/f-utils.php'; // Utilities
  require __DIR__.'/functions/f-escaping.php'; // Escaping
  require __DIR__.'/functions/f-content-manipulation.php'; // Var, Array, JSON, String, ect Manipulation
  require __DIR__.'/functions/f-router.php'; // Router
  require __DIR__.'/functions/f-dumper.php'; // Dumper
  require __DIR__.'/functions/f-flash.php'; // Flash session
  require __DIR__.'/functions/f-translation.php'; // Translation
  require __DIR__.'/functions/f-errors.php'; // Errors
  require __DIR__.'/functions/f-cache.php'; // Cache
  require __DIR__.'/functions/f-auth.php'; // Auth
  require __DIR__.'/functions/f-debug-bar.php'; // Debug bar
  require __DIR__.'/functions/f-dom-support.php'; // Dom support


  function ozz_func_loaded() {
    return true;
  }
}