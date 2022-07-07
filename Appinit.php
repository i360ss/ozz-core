<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\core;

class Appinit {
  
  private $SSL;      // Check (http or https)
  private $config;   // Config App
  private $csp;      // CSP

  
  public function __construct() {
    
    $this->config = parse_ini_file(__DIR__.'/../../env.ini', true);
    
    // # App session Setup
    if (session_status() == PHP_SESSION_NONE) {
      session_set_cookie_params(time()+600, '/', '', false, true);
      session_start();
    }
    
    
    // # Content security policy configuration
    $unique = substr(base64_encode(sha1( mt_rand() )), 0, 20);
    $nonce = base64_encode($unique);
    defined('CSP_NONCE') || define('CSP_NONCE', $nonce);
    
    $this->csp = parse_ini_file(__DIR__.'/../../csp.ini', true); // Get CSP Values
    if($this->csp['CSP']['USE_CSP'] == 1){
      $csp = $this->csp['CSP'];
      header("Content-Security-Policy: base-uri ".$csp['base-uri']."; default-src ".$csp['default-src']."; style-src ".$csp['style-src']." 'nonce-".$nonce."'; font-src ".$csp['font-src']."; script-src ".$csp['script-src']." 'nonce-" . $nonce . "'; img-src ".$csp['img-src']."; connect-src ".$csp['connect-src']."; object-src ".$csp['object-src']."; media-src ".$csp['media-src']."; child-src ".$csp['child-src']."; report-uri ".$csp['report-uri']."; form-action ".$csp['form-action']."; frame-ancestors ".$csp['frame-ancestors']."; worker-src ".$csp['worker-src']."; ");
    }
    
    
    // # Create Commen Hash Token (For Outside Users)
    if(empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token'])){
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    define('CSRF_TOKEN', $_SESSION['csrf_token']);
    define('CSRF_FIELD', '<input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'">');
    
    
    // # Config App
    defined('DS') || define('DS', '/');
    defined('APP_ENV') || define('APP_ENV', $this->config['app']['APP_ENV']);
    defined('APP_NAME') || define('APP_NAME', $this->config['app']['APP_NAME']);
    defined('APP_VERSION') || define('APP_VERSION', $this->config['app']['APP_VERSION']);
    defined('APP_LANG') || define('APP_LANG', $this->config['app']['APP_LANG']);
    
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

    # Config Directories
    defined('ROOT') || define('ROOT', $_SERVER['DOCUMENT_ROOT'] . DS);
    defined('VIEW') || define('VIEW', __DIR__ . '/../app/view/');
    defined('ASSETS') || define('ASSETS', BASE_URL . "assets".DS);
    defined('UPLOAD_TO') || define('UPLOAD_TO', __DIR__.'/../../'.$this->config['app']['PUBLIC_DIR'].'/uploads/');
    defined('UPLOADS') || define('UPLOADS', BASE_URL.'uploads/');
    defined('CSS') || define('CSS', ASSETS . 'css'.DS);
    defined('JS') || define('JS', ASSETS . 'js'.DS);

    defined('DEBUG') || define('DEBUG', $this->config['app']['DEBUG'] == 1 ? true : false);
    defined('MINIFY_HTML') || define('MINIFY_HTML', $this->config['app']['MINIFY_HTML'] == 1 ? true : false);    
  } // Constructor End
  
  
  
  // Run Application
  public function run(){
    require "system/ozz-func.php";
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
  }
  
} // End of App init Class