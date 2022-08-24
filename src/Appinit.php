<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Session;

class Appinit {
  
  private $SSL;      // Check (http or https)
  private $config;   // Config App
  private $csp;      // CSP

  
  public function __construct() {

    define('SPC_BACK', [
      'core' => '/../../../../',
      'core_1' => '/../../../../../',
      'core_2' => '/../../../../../../',
    ]);


    /**
     * Get content from env.ini and assign to $this->config
     */
    $this->config = parse_ini_file(__DIR__.SPC_BACK['core'].'env.ini', true);
    

    /**
     * App session start
     */
    Session::init($this->config);
    

    /**
     * Content security policy configuration
     */
    $csp_unique_hash = substr(base64_encode(sha1( mt_rand() )), 0, 20);
    $csp_nonce = base64_encode($csp_unique_hash);
    defined('CSP_NONCE') || define('CSP_NONCE', $csp_nonce);
    
    $this->csp = parse_ini_file(__DIR__.SPC_BACK['core'].'csp.ini', true); // Get CSP Values
    if($this->csp['CSP']['USE_CSP'] == 1){
      $csp = $this->csp['CSP'];
      header("Content-Security-Policy: base-uri ".$csp['base-uri']."; default-src ".$csp['default-src']."; style-src ".$csp['style-src']." 'nonce-".$csp_nonce."'; font-src ".$csp['font-src']."; script-src ".$csp['script-src']." 'nonce-" . $csp_nonce . "'; img-src ".$csp['img-src']."; connect-src ".$csp['connect-src']."; object-src ".$csp['object-src']."; media-src ".$csp['media-src']."; child-src ".$csp['child-src']."; report-uri ".$csp['report-uri']."; form-action ".$csp['form-action']."; frame-ancestors ".$csp['frame-ancestors']."; worker-src ".$csp['worker-src']."; ");
    }
    

    /**
     * Create Commen CSRF Token (For Outside Users)
     */
    if(empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token'])){
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }


    /**
     * CSRF Token
     */
    defined('CSRF_TOKEN') || define('CSRF_TOKEN', $_SESSION['csrf_token']);


    /**
     * Input Field with CSRF token
     */
    define('CSRF_FIELD', '<input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'">');
    

    /**
     * Directory separator
     */
    defined('DS') || define('DS', '/');


    /**
     * App envirenment (local, dev, prod)
     */
    defined('APP_ENV') || define('APP_ENV', $this->config['app']['APP_ENV']);


    /**
     * The Name of the app defined in env.ini
     */
    defined('APP_NAME') || define('APP_NAME', $this->config['app']['APP_NAME']);


    /**
     * App Version defined in env.ini
     */
    defined('APP_VERSION') || define('APP_VERSION', $this->config['app']['APP_VERSION']);


    /**
     * App Language defined in env.ini
     */
    defined('APP_LANG') || define('APP_LANG', $this->config['app']['APP_LANG']);
    

    /**
     * Set Base URL
     */
    $SVR = $_SERVER['SERVER_NAME'];
    if( $SVR == $this->config['app']['APP_URL'] || $SVR == 'www.'.$this->config['app']['APP_URL']) {
      $this->SSL = (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') 
      ? 'http://' 
      : 'https://';
      
      $baseURL = (APP_ENV == 'local') 
      ? $this->SSL.$_SERVER['SERVER_NAME'].':'.$this->config['app']['LOCAL_PORT'].'/'
      : $this->SSL.$_SERVER['SERVER_NAME'].'/';
      
      defined('BASE_URL') || define('BASE_URL', $baseURL);
    }
    else{
      Help::statusCode(401);
      exit("Not autherized");
    }


    /**
     * Root directory
     */
    defined('ROOT') || define('ROOT', $_SERVER['DOCUMENT_ROOT'] . DS);


    /**
     * "View directory" where all view files will live
     */
    defined('VIEW') || define('VIEW', __DIR__.SPC_BACK['core'].'app/view/');


    /**
     * Assets directory
     */
    defined('ASSETS') || define('ASSETS', BASE_URL . "assets".DS);


    /**
     * Upload directory, Inside public directory (for internal use)
     */
    defined('UPLOAD_TO') || define('UPLOAD_TO', '../'.$this->config['app']['UPLOAD_DIR']);


    /**
     * Upload directory, point to URL
     */
    defined('UPLOADS') || define('UPLOADS', BASE_URL.$this->config['app']['UPLOAD_DIR']);


    /**
     * CSS directory, all CSS files should be here
     */
    defined('CSS') || define('CSS', ASSETS . 'css'.DS);


    /**
     * Javascript directory, all JS files should be here
     */
    defined('JS') || define('JS', ASSETS . 'js'.DS);


    /**
     * Debug mode, defined in env.ini
     */
    defined('DEBUG') || define('DEBUG', $this->config['app']['DEBUG'] == 1 ? true : false);


    /**
     * Enable/Disable Debug bar, defined in env.ini
     */
    defined('SHOW_DEBUG_BAR') || define('SHOW_DEBUG_BAR', $this->config['app']['SHOW_DEBUG_BAR'] == 1 ? true : false);


    /**
     * Minify HTML, defined in env.ini
     */
    defined('MINIFY_HTML') || define('MINIFY_HTML', $this->config['app']['MINIFY_HTML'] == 1 ? true : false);
  }
  
  
  
  /**
   * Run Application
   */
  public function run(){
    /**
     * Load ozz functions
     */
    require "system/ozz-func.php";

    /**
     * Prepare and output
     */
    $out = Router::resolve();
    if(MINIFY_HTML){
      $search = array('/(\n|^)(\x20+|\t)/', '/(\n|^)\/\/(.*?)(\n|$)/', '/\n/', '/\<\!--.*?-->/', '/(\x20+|\t)/', '/\>\s+\</', '/(\"|\')\s+\>/', '/=\s+(\"|\')/');
      $replace = array("\n", "\n", " ", "", " ", "><", "$1>", "=$1");
      $out = preg_replace($search,$replace,$out);
      echo $out;
    }
    else{
      echo $out;
    }

    $afterReq = new AfterRequest;
    $afterReq->run();
  }
  
}
