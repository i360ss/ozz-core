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
    $config_file = $this->env['app']['CONFIG_FILE'] ?? 'app/config.php';
    $devConfig = include __DIR__.SPC_BACK['core'].$config_file;
    $defConfig = require __DIR__.'/system/default-config.php';
    define('CONFIG', array_merge($defConfig, $devConfig));

    // Auth paths
    defined('AUTH_PATHS') || define('AUTH_PATHS', CONFIG['AUTH_PATHS']);

    // Directory separator
    defined('DS') || define('DS', '/');

    // Framework Directories
    // Base directory
    defined('BASE_DIR') || define('BASE_DIR', __DIR__.SPC_BACK['core']);

    // Public directory
    defined('PUBLIC_DIR') || define('PUBLIC_DIR', BASE_DIR . trim(CONFIG['APP_PATHS']['public'], DS) . DS);

    // Root directory
    defined('ROOT') || define('ROOT', $_SERVER['DOCUMENT_ROOT'] . DS);

    // Core directory
    $core_dir = trim(CONFIG['APP_PATHS']['core'], DS);
    defined('CORE_DIR') || define('CORE_DIR', BASE_DIR . (!empty($core_dir) ? $core_dir . DS : ''));

    // App directory
    defined('APP_DIR') || define('APP_DIR', CORE_DIR . trim(CONFIG['APP_PATHS']['app'], DS) . DS);

    // Storage directory
    defined('STORAGE_DIR') || define('STORAGE_DIR', CORE_DIR . trim(CONFIG['APP_PATHS']['storage'], DS). DS);

    // Cache directory
    defined('CACHE_DIR') || define('CACHE_DIR', CORE_DIR . trim(CONFIG['APP_PATHS']['cache'], DS) . DS);

    // Database directory
    defined('DB_DIR') || define('DB_DIR', CORE_DIR . trim(CONFIG['APP_PATHS']['database'], DS) . DS);

    // Migration directory
    defined('MIGRATION_DIR') || define('MIGRATION_DIR', CORE_DIR . trim(CONFIG['APP_PATHS']['migration'], DS) . DS);

    // SQLite directory
    defined('SQLITE_DIR') || define('SQLITE_DIR', CORE_DIR . trim(CONFIG['APP_PATHS']['sqlite'], DS) . DS);

    // CMS directory
    defined('CMS_DIR') || define('CMS_DIR', CORE_DIR . trim(CONFIG['APP_PATHS']['cms'], DS) . DS);

    // Log directory
    defined('LOG_DIR') || define('LOG_DIR', CORE_DIR . trim(CONFIG['APP_PATHS']['log'], DS) . DS);

    // Session directory
    defined('SESSION_DIR') || define('SESSION_DIR', CORE_DIR . trim(CONFIG['APP_PATHS']['session'], DS) . DS);

    // System directory
    defined('SYSTEM_DIR') || define('SYSTEM_DIR', CORE_DIR . trim(CONFIG['APP_PATHS']['system'], DS) . DS);

    // View directory
    defined('VIEW') || define('VIEW', CORE_DIR . trim(CONFIG['APP_PATHS']['view'], DS) . DS);

    // Upload directory
    defined('UPLOAD_DIR') || define('UPLOAD_DIR', BASE_DIR . trim(CONFIG['APP_PATHS']['upload_dir'], DS) . DS);

    // Public Upload directory, point to URL
    defined('UPLOAD_DIR_PUBLIC') || define('UPLOAD_DIR_PUBLIC', rtrim(CONFIG['APP_PATHS']['upload_dir_public'], DS) . DS);

    // Initialize session
    Session::init();

    // Set default timezone
    date_default_timezone_set(@date_default_timezone_get());

    // Content security policy configuration
    $csp_nonce = self::hashKey('csp-nonce');
    defined('CSP_NONCE') || define('CSP_NONCE', $csp_nonce);

    // App environment (local, dev, prod)
    defined('APP_ENV') || define('APP_ENV', $this->env['app']['APP_ENV']);

    // The Name of the app defined in env.ini
    defined('APP_NAME') || define('APP_NAME', $this->env['app']['APP_NAME']);

    // App Version defined in env.ini
    defined('APP_VERSION') || define('APP_VERSION', $this->env['app']['APP_VERSION']);

    // CMS Admin path
    defined('ADMIN_PATH') || define('ADMIN_PATH', $this->env['cms']['ADMIN_PATH'] ?? '/admin');

    // App current language
    if(!Session::has('app_lang')){
      Session::set('app_lang', $this->env['app']['APP_LANG']);
    }
    defined('APP_LANG') || define('APP_LANG', Session::get('app_lang'));

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

    // Assets directory
    defined('ASSETS_DIR') || define('ASSETS_DIR', PUBLIC_DIR . trim(CONFIG['APP_PATHS']['assets'], DS) . DS);

    // Assets URL
    defined('ASSETS') || define('ASSETS', BASE_URL . trim(CONFIG['APP_PATHS']['assets'], DS) . DS);

    // Debug constants
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

    // App current language directory
    defined('LANG_DIR') || define('LANG_DIR', CORE_DIR . trim(CONFIG['APP_PATHS']['lang'], DS) . DS . APP_LANG . DS);
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
