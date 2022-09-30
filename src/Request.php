<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Request extends Router {
  
  private $fullRequest;
  private $method;
  
  
  function __construct(){
    $this->method = self::requestMethod();
    
    $this->fullRequest = [
      'server' => $_SERVER['SERVER_NAME'],
      'root' => $_SERVER['DOCUMENT_ROOT'],
      'port' => $_SERVER['SERVER_PORT'] ?? null,
      'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? null,
      'method' => self::requestMethod() ?? null,
      'host' => $_SERVER['HTTP_HOST'] ?? null,
      'request_time' => date( 'M d, Y - H:i:s', $_SERVER['REQUEST_TIME']),
      'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
      'header' => $_SERVER['HTTP_ACCEPT'] ?? null,
      'cookie' => $_SERVER['HTTP_COOKIE'] ?? null,
      'cache' => $_SERVER['HTTP_CACHE_CONTROL'] ?? null,
      'lang' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null,
      'encoding' => $_SERVER['HTTP_ACCEPT_ENCODING'] ?? null,
      'path' => $this->path(),
      'url' => $_SERVER['REQUEST_URI'] ?? null,
      'url_parts' => self::url_part(),
      'url_params' => $this->param() !== [] ? $this->param() : null,
      'query' => self::query(),
      'input' => self::input(),
      'files' => $_FILES,
    ];
  }
  
  
  
  # ---------------------------------------
  // All Request values
  # ---------------------------------------
  public function all(){
    return $this->fullRequest;
  }



  /**
   * Set HTTP status code
   */
  public static function statusCode($code){
    return http_response_code($code);
  }



  /**
   * Request path
   */
  public static function getPath(){
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    $position = strpos($path, '?');
    if(!$position){
      return $path;
    }
    return substr($path, 0, $position);
  }



  /**
   * Input data sent via request (Form data and Query string)
   * @param string|array|int $key key of the input value 
   * @return array|string|int|bool Returns sanitized input data
   */
  public static function input($key=false){
    $output = [];
    
    foreach ($_REQUEST as $k => $v) {
      if(is_array($v)){
        $output[$k] = Sanitize::array($v);
      }
      else{
        if((strtolower(substr($k, 0, 5)) == 'html:')) {
          $output[$k] = Sanitize::htmlEncode($v);
        } elseif((strtolower(substr($k, 0, 5)) == 'evil:')) {
          $output[$k] = $v;
        } else {
          $output[$k] = Sanitize::string($v);
        }
      }
    }
    
    return $key ? $output[$key] : $output; // Sanitized Data
  }



  /**
   * Get URL query strings
   * @param string|int $key
   */
  public static function query($key=null){
    if (isset($_SERVER['QUERY_STRING'])) {
      parse_str($_SERVER['QUERY_STRING'], $query_string);
      
      return (isset($key) && $key !== null)
      ? filter_var($query_string[$key], FILTER_SANITIZE_URL)
      : Sanitize::array($query_string);
    } else {
      return false;
    }
  }
  
  
  
  # ---------------------------------------
  // URL Parameters (SEO Friendly)
  # ---------------------------------------
  public function param($key=''){
    if(isset(Router::$ValidRoutes[$this->method][$this->path()]['urlParam'])){
      $data = $key !== '' 
      ? Router::$ValidRoutes[$this->method][$this->path()]['urlParam'][$key]
      : Router::$ValidRoutes[$this->method][$this->path()]['urlParam'];
    }
    else{
      $data = null;
    }
    return $data !== [] ? $data : null;
  }
  
  
  
  /**
   * URL part separated by ( / )
   * @param int $q index of part
   */
  public static function url_part($q=''){
    if(isset($_SERVER['REQUEST_URI'])){
      $parts = explode('/', filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL));
      if($q==''){
        return $parts;
      }
      elseif(count($parts) > $q){
        return $parts[$q];
      }
    }
  }
  
  
  
  # ---------------------------------------
  // Files
  # ---------------------------------------
  public function file($key=null){
    return isset($key) 
    ? $_FILES[$key]
    : $_FILES;
  }



  /**
   * Get HTTP request Methods
   */
  public static function requestMethod(){
    return strtolower($_SERVER['REQUEST_METHOD']);
  }

  // Check is it GET
  public static function isGet(){
    return self::getMethod() === 'get';
  }

  // Check is it POST
  public static function isPost(){
    return self::getMethod() === 'post';
  }

  // Check is it PUT
  public static function isPut(){
    return self::getMethod() === 'put';
  }

  // Check is it DELETE
  public static function isDelete(){
    return self::getMethod() === 'delete';
  }
  
  // Other Common Methods
  public function server(){
    return $_SERVER['SERVER_NAME'];
  }
  
  public function protocol(){
    return $_SERVER['SERVER_PROTOCOL'];
  }
  
  public function root(){
    return $_SERVER['DOCUMENT_ROOT'];
  }
  
  public function port(){
    return $_SERVER['SERVER_PORT'];
  }
  
  public function method(){
    return self::requestMethod() ?? null;
  }
  
  public function path(){
    return $_SERVER['PATH_INFO'] ?? null;
  }
  
  public function host(){
    return $_SERVER['HTTP_HOST'] ?? null;
  }
  
  public function userAgent(){
    return $_SERVER['HTTP_USER_AGENT'] ?? null;
  }
  
  public function header(){
    return $_SERVER['HTTP_ACCEPT'] ?? null;
  }
  
  public function cookie(){
    return $_SERVER['HTTP_COOKIE'] ?? null;
  }
  
  public function cache(){
    return $_SERVER['HTTP_CACHE_CONTROL'] ?? null;
  }
  
  public function lang(){
    return $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null;
  }
  
  public function encoding(){
    return $_SERVER['HTTP_ACCEPT_ENCODING'] ?? null;
  }
  
  public function requestTime($typ=''){
    return $typ ? $_SERVER['REQUEST_TIME_FLOAT'] : $_SERVER['REQUEST_TIME'];
  }
  
  public function url(){
    return $_SERVER['REQUEST_URI'] ?? null;
  }



    /**
   * Get Geo IP information
   * javascript http://www.geoplugin.net/javascript.gp
   * @param string $key
   */
  public static function client($key=''){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $userIp = $_SERVER['HTTP_CLIENT_IP'];
    } elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $userIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $userIp = $_SERVER['REMOTE_ADDR'];
    }
    
    $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$userIp"));
    
    $clientInfo = array(
      'ip'              => $geo['geoplugin_request'],
      'country'         => $geo['geoplugin_countryName'],
      'country_code'    => $geo['geoplugin_countryCode'],
      'city'            => $geo['geoplugin_city'],
      'region'          => $geo['geoplugin_regionName'],
      'timezone'        => $geo['geoplugin_timezone'],
      'currency'        => $geo['geoplugin_currencyCode'],
      'exchange'        => $geo['geoplugin_currencyConverter'], // 1 USD = this
      'currency_symbol' => $geo['geoplugin_currencySymbol'],
    );
    
    if ($key=='') {
      return $clientInfo;
    } elseif (isset($clientInfo[$key])) {
      return $clientInfo[$key];
    } else {
      return DEBUG 
      ? Err::custom([
        "msg" => "Invalid key provided for client information",
        "info" => "Please refer to the available client information below",
        "note" => implode(', ', array_keys($clientInfo))
      ])
      : false;
    }
  }
  
  
  
  /**
   * Log Request info to Debug bar
   */
  public function __destruct() {
    global $DEBUG_BAR;
    $DEBUG_BAR->set('ozz_request', $this->fullRequest);
  }
}