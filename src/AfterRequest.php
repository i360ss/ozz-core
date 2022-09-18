<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Session;

class AfterRequest {

  private $flash_keys;


  /**
   * Run this after each request
   */
  public function run() {
    $this->session_flash_out();
  }  



  /**
   * Execute this after all request
   */
  private function session_flash_out() {
    if ( isset($_SESSION['ozz__flash']) && !empty($_SESSION['ozz__flash']) ) {
      $this->flash_keys = array_keys( Session::get('ozz__flash') );
      
      foreach ($this->flash_keys as $key) {
        Session::remove($key);
      }
      
      // Clear flash session log
      $_SESSION['ozz__flash'] = false;
    }
  }
}
