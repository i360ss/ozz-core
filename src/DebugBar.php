<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;


class DebugBar {

  protected $debug_info = [];
  

  /**
   * Construct the instance
   */
  public function __construct() {
    $this->debug_info['ozz_message'] = [];
    $this->debug_info['ozz_request'] = [];
    $this->debug_info['ozz_sql_queries'] = [];
  }



  /**
   * Log debug information to display on debug bar
   * @param string $key key of the data
   * @param string|array|object|int The debug info
   */
  public function set($key, $value) {
    DEBUG ? $this->debug_info[$key] = $value : false;
  }



  /**
   * Get Debug Information
   * @param string $key the key to return value (optional)
   * @return array|string|int ill return the debug infor
   */
  public function get($key=null) {
    if (isset($key) && array_key_exists($key, $this->debug_info)) {
      return $this->debug_info[$key];
    } else {
      return $this->debug_info;
    }
  }



  /**
   * Set and Display/Render the debug bar
   */
  public function show() {
    // Remove Duplicates on ozz_messages
    $msg = $this->debug_info['ozz_message'];
    if (!empty($msg) && count($msg) > 1) {
      $this->debug_info['ozz_message'] = array_chunk($msg, ceil(count($msg) / 2))[0];
    }

    // Render Debug Bar / Generate DOM
    ///////////////////////
    echo '<h4>Ozz Debug Bar</h4>';
    dump( $this->get() );
  }
  
}