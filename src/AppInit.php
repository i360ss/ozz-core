<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Session;
use Ozz\Core\Csrf;

class AppInit {

  private $env;
  private static $cachedEnv = null;
  private static $dependenciesChecked = false;

  use \Ozz\Core\TokenHandler;

  public function __construct() {
    defined('SPC_BACK') || define('SPC_BACK', [
      'core' => '/../../../../',
      'core_1' => '/../../../../../',
      'core_2' => '/../../../../../../',
    ]);

    // Only check dependencies once
    if (!self::$dependenciesChecked) {
      $this->dependencyCheck();
      self::$dependenciesChecked = true;
    }

    // Cache env
    if (self::$cachedEnv === null) {
      self::$cachedEnv = parse_ini_file(ENV_FILE, true);
    }
    $this->env = self::$cachedEnv;

    // Auth paths
    defined('AUTH_PATHS') || define('AUTH_PATHS', CONFIG['AUTH_PATHS']);

    // Initialize session
    Session::init();

    // Set default timezone
    date_default_timezone_set(@date_default_timezone_get());

    defined('APP_ENV') || define('APP_ENV', $this->env['app']['APP_ENV']);
    defined('APP_NAME') || define('APP_NAME', $this->env['app']['APP_NAME']);
    defined('APP_VERSION') || define('APP_VERSION', $this->env['app']['APP_VERSION']);
    defined('ADMIN_PATH') || define('ADMIN_PATH', $this->env['cms']['ADMIN_PATH'] ?? '/admin');
    defined('DEBUG') || define('DEBUG', $this->env['app']['DEBUG'] == 1 ? true : false);
    defined('SHOW_DEBUG_BAR') || define('SHOW_DEBUG_BAR', $this->env['app']['SHOW_DEBUG_BAR'] == 1 ? true : false);
    defined('DEBUG_EMAIL') || define('DEBUG_EMAIL', $this->env['app']['DEBUG_EMAIL'] == 1 ? true : false);

    // Validate host
    $this_host = $_SERVER['HTTP_HOST'] ?? '';
    $valid_domains = explode(' ', $this->env['app']['APP_URLS'] ?? '');

    if (!in_array($this_host, $valid_domains)) {
      http_response_code(401);
      echo "Unauthorized Host Access"; 
      return; 
    }

    // Create initial CSRF token
    if(empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token']) || (isset($_SESSION['csrf_token_expire']) && time() > $_SESSION['csrf_token_expire'])){
      Csrf::refreshToken();
    }

    // Define paths
    if (function_exists('frankenphp_handle_request')) {
      require_once __DIR__.'/system/define-paths.php';
    } else {
      require __DIR__.'/system/define-paths.php';
    }
    ozz_define_paths();
  }

  /**
   * Run Application
   */
  public function run(){
    if (function_exists('frankenphp_handle_request')) {
      require_once "system/ozz-func.php";
    } else {
      require "system/ozz-func.php";
    }

    return Router::resolve();
  }

  /**
   * Dependency check
   */
  private function dependencyCheck(){
    $dependency_errors = [];
    if(phpversion() < 8.0){
      $dependency_errors['PHP version error'] = 'PHP version should be 8.0 or above. Please upgrade your PHP version';
    }

    $required_extensions = [
      'hash',
      'json',
      'session',
      'PDO',
      'curl',
      'dom',
      'mbstring',
      'fileinfo',
      'mysqli',
      'pdo_mysql',
      'pdo_sqlite',
      'sqlite3',
    ];

    foreach ($required_extensions as $ext) {
      if (!in_array($ext, get_loaded_extensions())) {
        $dependency_errors['PHP extension error'] = 'PHP Extension missing ['.$ext.']';
      }
    }

    // Render dependency errors
    if(count($dependency_errors) > 0){
      $err_dom = '<div style="max-width: 800px; margin: 32px auto">';
      foreach ($dependency_errors as $error => $message) {
        $err_dom .= '<div style="padding: 10px; background: #fff;border-bottom: 1px solid #ddd;line-height: 1.6rem; font-size: 16px;">
        <pre><strong style="color: #ff4757;">'.$error.'</strong><br>'.$message.'</pre>
        </div>';
      }
      $err_dom .= '</div>';

      echo $err_dom;
      exit;
    }
  }

}
