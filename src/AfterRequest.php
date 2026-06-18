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

  public function run() {
    $this->session_flash_out();
    $this->remove_temp_logs();

    if (isset(CONFIG['SERVER']) && CONFIG['SERVER'] === 'frankenphp' && function_exists('frankenphp_handle_request')) {
      $this->frankenphp_cleanup();
    }
  }

  /**
   * Clean flash
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
    if (defined('LOG_DIR')) {
      file_put_contents(LOG_DIR.'sql_debug.log', '');
    }
  }

  /**
   * Clean up stateful resources leaked during worker processing
   * (This is completely skipped on the PHP Built-in Server for max performance)
   */
  private function frankenphp_cleanup() {
    $_GET = [];
    $_POST = [];
    $_FILES = [];
    $_COOKIE = [];
    $_REQUEST = [];

    $protected_globals = ['GLOBALS', '_SERVER', '_ENV', '_SESSION', 'DEBUG_BAR', 'CONFIG']; 
    foreach ($GLOBALS as $key => $value) {
      if (!in_array($key, $protected_globals, true)) {
        unset($GLOBALS[$key]);
      }
    }

    // Cleanup static values
    if (function_exists('csp_nonce')) csp_nonce(true);
    if (function_exists('locale')) locale(true);
    if (function_exists('csrf_token')) csrf_token(true);
    if (function_exists('csrf_field')) csrf_field(true);
    if (function_exists('base_url')) base_url(true);
    if (function_exists('assets_url')) assets_url(true);

    // $servicesToReset = [
    //   \Ozz\Core\Request::class,
    //   \Ozz\Core\Err::class,
    //   \Ozz\Core\Response::class,
    // ];

    // foreach ($servicesToReset as $service) {
    //   if (is_subclass_of($service, \Ozz\Core\ResetableInterface::class)) {
    //     $service::resetInstance();
    //   }
    // }
  }

}