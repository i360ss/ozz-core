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
  private $csp;   // CSP

  use \Ozz\Core\TokenHandler;

  public function __construct() {
    define('SPC_BACK', [
      'core' => '/../../../../',
      'core_1' => '/../../../../../',
      'core_2' => '/../../../../../../',
    ]);

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

    // Content security policy configuration
    $csp_nonce = self::hashKey('csp-nonce');
    defined('CSP_NONCE') || define('CSP_NONCE', $csp_nonce);

    $this->csp = parse_ini_file(__DIR__.SPC_BACK['core'].'csp.ini', true); // Get CSP Values
    if($this->csp['CSP']['USE_CSP'] == 1){
      $csp = $this->csp['CSP'];
      header("Content-Security-Policy: base-uri ".$csp['base-uri']."; default-src ".$csp['default-src']."; style-src ".$csp['style-src']." 'nonce-".$csp_nonce."'; font-src ".$csp['font-src']."; script-src ".$csp['script-src']." 'nonce-" . $csp_nonce . "'; img-src ".$csp['img-src']."; connect-src ".$csp['connect-src']."; object-src ".$csp['object-src']."; media-src ".$csp['media-src']."; child-src ".$csp['child-src']."; form-action ".$csp['form-action']."; frame-ancestors ".$csp['frame-ancestors']."; worker-src ".$csp['worker-src']."; ");
    }

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
    $this_host = $_SERVER['HTTP_HOST'];
    $valid_domains = explode(' ', $this->env['app']['APP_URLS']);

    if(in_array($this_host, $valid_domains)) {
      if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on'){
        $this->SSL = 'http://';
        define('HAS_SSL', false);
      } else {
        $this->SSL = 'https://';
        define('HAS_SSL', true);
      }

      defined('APP_URL') || define('APP_URL', $this_host.'/');
      defined('BASE_URL') || define('BASE_URL', $this->SSL.APP_URL);
    } else{
      http_response_code(401);
      exit("Unauthorized");
    }

    // Root directory
    defined('ROOT') || define('ROOT', $_SERVER['DOCUMENT_ROOT'] . DS);

    // App directory
    defined('APP_DIR') || define('APP_DIR', __DIR__.SPC_BACK['core'].'app/');

    // Cache directory
    defined('CACHE_DIR') || define('CACHE_DIR', __DIR__.SPC_BACK['core'].'storage/cache/');

    // View directory
    defined('VIEW') || define('VIEW', APP_DIR.'view/');

    // Assets directory
    defined('ASSETS') || define('ASSETS', BASE_URL . "assets".DS);

    // Upload directory, Inside public directory (for internal use)
    defined('UPLOAD_TO') || define('UPLOAD_TO', '../'.$this->env['app']['UPLOAD_DIR']);

    // Upload directory, point to URL
    defined('UPLOAD_DIR_PUBLIC') || define('UPLOAD_DIR_PUBLIC', $this->env['app']['UPLOAD_DIR_PUBLIC']);

    // CSS directory, all CSS files should be here
    defined('CSS') || define('CSS', ASSETS . 'css'.DS);

    // Javascript directory, all JS files should be here
    defined('JS') || define('JS', ASSETS . 'js'.DS);

    // Debug mode, defined in env.ini
    defined('DEBUG') || define('DEBUG', $this->env['app']['DEBUG'] == 1 ? true : false);

    // Enable/Disable Debug bar, defined in env.ini
    defined('SHOW_DEBUG_BAR') || define('SHOW_DEBUG_BAR', $this->env['app']['SHOW_DEBUG_BAR'] == 1 ? true : false);

    // Debug Email template output, defined in env.ini
    defined('DEBUG_EMAIL_TEMP') || define('DEBUG_EMAIL_TEMP', $this->env['app']['DEBUG_EMAIL_TEMP'] == 1 ? true : false);

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

}
