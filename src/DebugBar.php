<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;


class DebugBar {

  protected $debug_info;
  

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
    return $key ? $this->debug_info[$key] : $this->debug_info;
  }



  /**
   * Set and Display/Render the debug bar
   */
  public function show() {
    echo '<h4>Ozz Debug Bar</h4>';
    dump( $this->get() );
  }
  
}