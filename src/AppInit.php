<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Session;
use Ozz\Core\Cookie;
use Ozz\Core\Csrf;

class AppInit {

  private $SSL;   // Check (http or https)
  private $env;   // Env App

  use \Ozz\Core\TokenHandler;

  public function __construct() {
    define('SPC_BACK', [
      'core' => '/../../../../',
      'core_1' => '/../../../../../',
      'core_2' => '/../../../../../../',
    ]);

    // Dependency check
    $this->dependencyCheck();

    // Get content from env.ini and assign to $this->env
    $this->env = parse_ini_file(__DIR__.SPC_BACK['core'].'env.ini', true);

    // App configurations
    $devConfig = include __DIR__.SPC_BACK['core'].'app/config.php';
    $defConfig = require __DIR__.'/system/default-config.php';
    define('CONFIG', array_merge($defConfig, $devConfig));

    // More Reused
    defined('AUTH_PATHS') || define('AUTH_PATHS', CONFIG['AUTH_PATHS']);

    // Initialize session
    Session::init();

    // Set default timezone
    date_default_timezone_set(@date_default_timezone_get());

    // Content security policy configuration
    $csp_nonce = self::hashKey('csp-nonce');
    defined('CSP_NONCE') || define('CSP_NONCE', $csp_nonce);

    // Directory separator
    defined('DS') || define('DS', '/');

    // App environment (local, dev, prod)
    defined('APP_ENV') || define('APP_ENV', $this->env['app']['APP_ENV']);

    // The Name of the app defined in env.ini
    defined('APP_NAME') || define('APP_NAME', $this->env['app']['APP_NAME']);

    // App Version defined in env.ini
    defined('APP_VERSION') || define('APP_VERSION', $this->env['app']['APP_VERSION']);

    // App current language
    if(!Session::has('app_lang')){
      Session::set('app_lang', $this->env['app']['APP_LANG']);
    }
    defined('APP_LANG') || define('APP_LANG', Session::get('app_lang'));

    // App current language path
    defined('APP_LANG_PATH') || define('APP_LANG_PATH', __DIR__.SPC_BACK['core'].'app/lang/'.APP_LANG.'/');

    // Set Base URL
    $this_host = $_SERVER['HTTP_HOST'] ?? '';
    $valid_domains = explode(' ', $this->env['app']['APP_URLS'] ?? '');

    if (in_array($this_host, $valid_domains)) {
      // Check for SSL
      $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

      $this->SSL = $is_secure ? 'https://' : 'http://';
      define('HAS_SSL', $is_secure);

      // Define APP_URL and BASE_URL
      defined('APP_URL') || define('APP_URL', rtrim($this_host, '/') . '/');
      defined('BASE_URL') || define('BASE_URL', $this->SSL . APP_URL);
    } else {
      http_response_code(401);
      exit("Unauthorized");
    }

    // Root directory
    defined('ROOT') || define('ROOT', $_SERVER['DOCUMENT_ROOT'] . DS);

    // Base directory
    defined('BASE_DIR') || define('BASE_DIR', __DIR__.SPC_BACK['core']);

    // App directory
    defined('APP_DIR') || define('APP_DIR', __DIR__.SPC_BACK['core'].'app/');

    // Storage directory
    defined('STORAGE_DIR') || define('STORAGE_DIR', __DIR__.SPC_BACK['core'].'storage/');

    // Cache directory
    defined('CACHE_DIR') || define('CACHE_DIR', __DIR__.SPC_BACK['core'].'storage/cache/');

    // View directory
    defined('VIEW') || define('VIEW', APP_DIR.'view/');

    // Assets directory
    defined('ASSETS') || define('ASSETS', BASE_URL . "assets".DS);

    // Upload directory, Inside public directory (for internal use)
    defined('UPLOAD_TO') || define('UPLOAD_TO', __DIR__.SPC_BACK['core'].$this->env['app']['UPLOAD_DIR']);

    // Upload directory, point to URL
    defined('UPLOAD_DIR_PUBLIC') || define('UPLOAD_DIR_PUBLIC', $this->env['app']['UPLOAD_DIR_PUBLIC']);

    // Debug mode, defined in env.ini
    defined('DEBUG') || define('DEBUG', $this->env['app']['DEBUG'] == 1 ? true : false);

    // Enable/Disable Debug bar, defined in env.ini
    defined('SHOW_DEBUG_BAR') || define('SHOW_DEBUG_BAR', $this->env['app']['SHOW_DEBUG_BAR'] == 1 ? true : false);

    // Debug Email template output, defined in env.ini
    defined('DEBUG_EMAIL') || define('DEBUG_EMAIL', $this->env['app']['DEBUG_EMAIL'] == 1 ? true : false);

    // Create initial CSRF token
    if(empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token']) || (isset($_SESSION['csrf_token_expire']) && time() > $_SESSION['csrf_token_expire'])){
      Csrf::refreshToken();
    }

    // CSRF Token
    defined('CSRF_TOKEN') || define('CSRF_TOKEN', Csrf::getToken());

    // Input Field with CSRF token
    defined('CSRF_FIELD') || define('CSRF_FIELD', Csrf::getTokenField());
  }

  /**
   * Run Application
   */
  public function run(){
    // Load ozz functions
    require "system/ozz-func.php";

    // Resolve Route
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
