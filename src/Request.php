<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Request extends Router {

  private $full_request;
  private $method;


  function __construct(){
    $this->method = self::method();

    $this->full_request = [
      'server' => $_SERVER['SERVER_NAME'],
      'root' => $_SERVER['DOCUMENT_ROOT'],
      'port' => $_SERVER['SERVER_PORT'] ?? null,
      'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? null,
      'method' => $this->method,
      'host' => $_SERVER['HTTP_HOST'] ?? null,
      'request_time' => date( 'M d, Y - H:i:s', $_SERVER['REQUEST_TIME']),
      'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
      'header' => $_SERVER['HTTP_ACCEPT'] ?? null,
      'cookie' => $_SERVER['HTTP_COOKIE'] ?? null,
      'cache' => $_SERVER['HTTP_CACHE_CONTROL'] ?? null,
      'lang' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null,
      'encoding' => $_SERVER['HTTP_ACCEPT_ENCODING'] ?? null,
      'path' => self::path(),
      'url' => $_SERVER['REQUEST_URI'] ?? null,
      'url_parts' => self::url_part(),
      'url_params' => self::param() !== [] ? self::param() : null,
      'query' => self::query(),
      'input' => self::input(),
      'files' => $_FILES,
    ];
  }



  /**
   * Return all values of Request object as array
   */
  public function all(){
    return $this->full_request;
  }



  /**
   * Returns Request path
   */
  public static function path(){
    if(isset($_SERVER['REQUEST_URI'])){
      $path = Sanitize::url($_SERVER['REQUEST_URI']);
      $pos = strpos($path, '?');
      if(!$pos){
        return $path;
      }
      return substr($path, 0, $pos);
    } else {
      return '/';
    }
  }



  /**
   * Input data sent via request (Form data and Query string)
   * 
   * @param string|array|int $key key of the input value 
   * @param boolean $evil ignore sanitization if this is true
   * @return array|string|int|bool Returns sanitized input data
   */
  public static function input($key=false, $evil=false){
    $output = $_FILE;

    if($evil === true){
      $output = $_REQUEST;
    } else {
      foreach ($_REQUEST as $k => $v) {
        (is_array($v))
          ? $output[$k] = Sanitize::array($v)
          : $output[$k] = is_json($v) ? $v : Sanitize::specialChar($v);
      }
    }

    return $key ? $output[$key] : $output; // Sanitized Data
  }



  /**
   * Returns URL query strings array or string
   * 
   * @param string|int $key
   * @param boolean $evil ignore sanitization if this is true
   * @return array|string|int|bool
   */
  public static function query($key=null, $evil=false){
    if (isset($_SERVER['QUERY_STRING'])) {
      parse_str($_SERVER['QUERY_STRING'], $query_string);

      if($evil === true){
        return (isset($key) && $key !== null)
          ? $query_string[$key]
          : $query_string;
      } else {
        return (isset($key) && $key !== null)
          ? filter_var($query_string[$key], FILTER_SANITIZE_URL)
          : Sanitize::array($query_string, 'url');
      }

    } else {
      return false;
    }
  }



  /**
   * URL Parameters (Defined parameter keys and values on Route)
   * 
   * @param string $key the key of URL parameter
   * @return array|string|int
   */
  public static function param($key=''){
    $path = self::path() ?? false;
    $method = self::method();
    if(isset(Router::$ValidRoutes[$method][$path]['urlParam'])){
      $data = $key !== '' 
      ? Router::$ValidRoutes[$method][$path]['urlParam'][$key]
      : Router::$ValidRoutes[$method][$path]['urlParam'];
    }
    else{
      $data = null;
    }
    return $data !== [] ? $data : null;
  }



  /**
   * URL part separated by ( / )
   * 
   * @param int $q index of part
   */
  public static function url_part($q=''){
    $URL = self::path() ?? false;
    if(isset($URL)){
      $parts = explode('/', $URL);
      if($q==''){
        return $parts;
      }
      elseif(count($parts) > $q){
        return $parts[$q];
      }
    } else {
      return false;
    }
  }



  /**
   * Returns Files sent over the request
   * 
   * @param $key the key for specific file
   */
  public static function file($key=null){
    return isset($key) 
    ? $_FILES[$key]
    : $_FILES;
  }



  /**
   * Returns the Request method (get, post, ect...)
   */
  public static function method(){
    return strtolower($_SERVER['REQUEST_METHOD']);
  }



  /**
   * Check the request method
   * 
   * @param string $method (get, post, ect...)
   */
  public static function is_method($method){
    return self::method() === strtolower($method);
  }



  /**
   * Get Client IP address
   */
  public static function ip() {
    $ip = $_SERVER['HTTP_CLIENT_IP'] 
      ?? $_SERVER["HTTP_CF_CONNECTING_IP"]
      ?? $_SERVER['HTTP_X_FORWARDED'] 
      ?? $_SERVER['HTTP_X_FORWARDED_FOR'] 
      ?? $_SERVER['HTTP_FORWARDED'] 
      ?? $_SERVER['HTTP_FORWARDED_FOR'] 
      ?? $_SERVER['REMOTE_ADDR'] 
      ?? '0.0.0.0';

    return $ip;
  }



  /**
   * Get client information
   * 
   * javascript http://www.geoplugin.net/javascript.gp
   * @param string $key
   */
  public static function clientInfo($key=''){
    $info = [];
    $userIp = self::ip();
    $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$userIp"));
    unset($geo['geoplugin_credit']);

    foreach ($geo as $k => $val) {
      $nk = str_replace('geoplugin_', '', $k);
      $info[$nk] = $val;
    }

    if ($key=='') {
      return $info;
    } elseif (isset($info[$key])) {
      return $info[$key];
    } else {
      return DEBUG 
      ? Err::custom([
        "msg" => "Invalid key provided for client information",
        "info" => "Please refer to the available client information below",
        "note" => implode(', ', array_keys($info))
      ])
      : false;
    }
  }



  /**
   * Log Request info to Debug bar
   */
  public function __destruct() {
    global $DEBUG_BAR;
    $DEBUG_BAR->set('ozz_request', $this->full_request);
  }
}