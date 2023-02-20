<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;


use Ozz\Core\system\SubHelp;

class DebugBar {

  protected $debug_info = [];
  

  /**
   * Construct the instance
   */
  public function __construct() {
    $this->debug_info['ozz_message'] = [];
    $this->debug_info['ozz_request'] = [];
  }



  /**
   * Log debug information to display on debug bar
   * @param string $key key of the data
   * @param string|array|object|int The debug info
   */
  public function set($key, $value) {
    if(DEBUG){
      $this->debug_info[$key] = $value;
    }
  }



  /**
   * Get Debug Information
   * @param string $key the key to return value (optional)
   * @return array|string|int ill return the debug info
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
    $request = Request::getInstance();
    $subHelp = new SubHelp();
    $this->set('ozz_request', $request->all());
    $subHelp->renderDebugBar($this->get());
  }

}