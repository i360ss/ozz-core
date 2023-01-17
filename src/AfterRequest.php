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
  private $error_keys;


  /**
   * Run this after each request
   */
  public function run() {
    $this->session_flash_out();
    $this->remove_temp_logs();
  }



  /**
   * Execute this after all request
   */
  private function session_flash_out() {
    if ( isset($_SESSION['__flash']) && !empty($_SESSION['__flash']) ) {
      unset($_SESSION['__flash']);
    }

    if ( isset($_SESSION['__error']) && !empty($_SESSION['__error']) ) {
      unset($_SESSION['__error']);
    }
  }



  /**
   * Clear Temporary debug log files
   */
  private function remove_temp_logs() {
    file_put_contents(__DIR__.SPC_BACK['core'].'storage/log/sql_debug.log', '');
  }

}
