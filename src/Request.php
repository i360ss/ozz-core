<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Request extends Router {

  private $all;
  private static $instance;

  private function __construct() {
    $this->all = [
      'server'            => $this->server(),
      'root'              => $this->root(),
      'port'              => $this->port(),
      'protocol'          => $this->protocol(),
      'method'            => $this->method(),
      'host'              => $this->host(),
      'time'              => $this->time(),
      'ip'                => $this->ip(),
      'user_agent_string' => $this->user_agent(true),
      'user_agent'        => $this->user_agent(),
      'headers'           => $this->headers(),
      'cookies'           => $this->cookies(),
      'cache'             => $this->cache(),
      'lang'              => $this->lang(),
      'encoding'          => $this->encoding(),
      'path'              => $this->path(),
      'url'               => $this->url(),
      'url_parts'         => $this->url_part(),
      'url_params'        => $this->url_param() !== [] ? $this->url_param() : null,
      'query'             => $this->query(),
      'input'             => $this->input(),
      'files'             => $this->files(),
      'secure'            => $this->is_secure(),
    ];
  }

  /**
   * Single Request instance
   */
  public static function getInstance() {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Return all values of Request object as array
   * @param string $key Specific key to return specific value
   */
  public function all($key=''){
    return !empty($key) 
      ? $this->all[$key] 
      : $this->all;
  }

  /**
   * Return the Request server
   */
  public function server(){
    return $_SERVER['SERVER_NAME'];
  }

  /**
   * Return the Request URL
   */
  public function url(){
    return $_SERVER['REQUEST_URI'] ?? null;
  }

  /**
   * Return the Request Port
   */
  public function port(){
    return $_SERVER['SERVER_PORT'] ?? null;
  }

  /**
   * Return the Request Protocol
   */
  public function protocol(){
    return $_SERVER['SERVER_PROTOCOL'] ?? null;
  }

  /**
   * Return the Root
   */
  public function root(){
    return $_SERVER['DOCUMENT_ROOT'];
  }

  /**
   * Return the Root
   */
  public function host(){
    return $_SERVER['HTTP_HOST'] ?? null;
  }

  /**
   * Request Time
   */
  public function time(){
    return date( 'M d, Y - H:i:s', $_SERVER['REQUEST_TIME']);
  }

  /**
   * Request Cookies
   */
  public function cookies(){
    return $_SERVER['HTTP_COOKIE'] ?? null;
  }

  /**
   * Request Cookies
   * @param string $key Cookie key
   */
  public function cookie($key=false){
    return $key !== false ? $_SERVER['HTTP_COOKIE'][$key] : $_SERVER['HTTP_COOKIE'];
  }

  /**
   * Request Cache control
   */
  public function cache(){
    return $_SERVER['HTTP_CACHE_CONTROL'] ?? null;
  }

  /**
   * Request Language
   */
  public function lang(){
    return $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null;
  }

  /**
   * Request Encoding
   */
  public function encoding(){
    return $_SERVER['HTTP_ACCEPT_ENCODING'] ?? null;
  }

  /**
   * Check if HTTPS or not
   */
  public function is_secure(){
    return isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on';
  }

  /**
   * Check if HTTPS or not
   */
  public function is_ajax(){
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
  }

  /**
   * Check request is JSON
   */
  public function is_json(){
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

    return $contentType === "application/json";
  }

  /**
   * HTTP Headers
   * @return array Array of headers
   */
  public function headers(){
    $headers = [];
    foreach ($_SERVER as $k => $value) {
      if(strpos($k, 'HTTP_') === 0){
        $headers[substr($k, 5)] = $value;
      } elseif(strpos($k, 'REDIRECT_') === 0){
        $headers[substr($k, 9)] = $value;
      }
    }

    if(function_exists('getallheaders')){
      $headers = array_merge($headers, getallheaders());
    }

    return $headers;
  }

  /**
   * Return HTTP Header value of provided key
   * @param string $key Key for select specific header value
   */
  public function header($key=false){
    return $key ? $this->headers()[$key] : $this->headers();
  }

  /**
   * Returns Request path
   */
  public function path(){
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
   * @param string|array|int $key key of the input value 
   * @param boolean $evil ignore sanitization if this is true
   * @return array|string|int|bool Returns sanitized input data
   */
  public function input($key=false, $evil=false){
    $output = $_FILES;
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
   * @param string|int $key
   * @param boolean $evil ignore sanitization if this is true
   * @return array|string|int|bool
   */
  public function query($key=null, $evil=false){
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
   * @param string $key the key of URL parameter
   * @return array|string|int
   */
  public function url_param($key=''){
    $path = $this->path() ?? false;
    $method = $this->method();
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
   * @param int $q index of part
   */
  public function url_part($q=''){
    $URL = $this->path() ?? false;
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
   * Another way to call url_part()
   * @param int $q index of segment
   */
  public function segment($q=''){
    return $this->url_part($q);
  }

  /**
   * Returns All Files sent over the request
   */
  public function files(){
    return $_FILES;
  }

  /**
   * Returns Files sent over the request
   * @param $key the key for specific file
   */
  public function file($key=null){
    return isset($key) ? $_FILES[$key] : $_FILES;
  }

  /**
   * Returns the Request method (get, post, ect...)
   */
  public function method(){
    return strtolower($_SERVER['REQUEST_METHOD']);
  }

  /**
   * Check the request method
   * @param string $method (get, post, ect...)
   */
  public function is_method($method){
    return $this->method() === strtolower($method);
  }

  /**
   * User agent info (Separated)
   * @param boolean Return only the user-agent string if (true)
   */
  public function user_agent($string=false){

    if($string === true){
      return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $user_agent_info = [
      'all'             => $user_agent,
      'device'          => null,
      'os'              => null,
      'device_name'     => null,
      'os_version'      => null,
      'browser'         => null,
      'browser_version' => null,
    ];

    $device_list = [
      'Computer' => 'Windows|Mac|Linux|UNIX|BeOS|FreeBSD|OpenBSD|NetBSD|SunOS|Solaris|IRIX|HP-UX|AIX|OSF1|IOS',
      'Mobile' => 'Android|iPhone|iPad|iPod|BlackBerry|Windows Phone|SymbianOS|S60|Series60|Series40|Opera Mini|Opera Mobi|Nokia|SonyEricsson|Samsung|LG|HTC|Sony|Asus|Micromax|Palm|Vertu|Pantech|Fly|iMobile|SimValley|Ubuntu Touch|Windows CE|WindowsCE|Smartphone|Armv|Spice|Bird|ZTE|Alcatel|Lenovo|SonyEricsson|Ericsson|Bada|Meizu|Xolo|Lava|iOne|Celkon|Gionee|Vivo|Nexus|OnePlus|Yu|Acer|Xiaomi|OPPO|vivo|Coolpad|Wiko|Generic Smartphone',
      'Tablet' => 'iPad|Android|Windows Tablet|Kindle|PlayBook|Samsung Tablet|Galaxy Tab|Nexus 7|Nexus 10|Asus Tablet|Transformer|Lenovo|Acer|HP|Toshiba|Sony|Sony Tablet|Galaxy|Galaxy Tab|Xoom|Dell|Motorola|LG|Asus|Nook|Fonepad|Ainol|Nabi|Nexus|Sony Xperia Tablet|Iconia|IdeaTab|ThinkPad Tablet|Yoga Tablet|Zenpad|Xiaomi|Surface Pro|NuVision|Venue|Nexus 9|Nexus 7|Surface|Pixel C|Lenovo Yoga Tablet 2|Lenovo Yoga Tablet|Lenovo IdeaPad|Lenovo Miix|Lenovo ThinkPad',
    ];

    $os_list = [
      'Windows' => 'Windows NT|Windows NT 10.0|Windows NT 6.2|Windows NT 6.1|Windows NT 6.0|Windows NT 5.1|Windows NT 5.0|Windows 2000|Windows NT 4.0|Windows 98|Windows 95|Windows CE|Windows Phone|Windows',
      'Mac' => 'Mac OS X|Macintosh|Mac OS X 10.10|Mac OS X 10.9|Mac OS X 10.8|Mac OS X 10.7|Mac OS X 10.6|Mac OS X 10.5|Mac OS X 10.4|Mac OS X 10.3|Mac OS X 10.2|Mac OS X 10.1|Mac OS X 10.0',
      'Linux' => 'Linux|Red Hat|Fedora|Debian|Ubuntu|FreeBSD|OpenBSD|NetBSD|SunOS|Solaris|IRIX|HP-UX|AIX|OSF1|IOS',
      'UNIX' => 'UNIX',
      'BeOS' => 'BeOS',
      'iOS' => 'iPhone|iPad|iPod',
      'Android' => 'Android',
      'BlackBerry' => 'BlackBerry|BB10|RIM Tablet OS',
      'Symbian' => 'SymbianOS|S60|Series60|Series40',
      'Palm' => 'PalmOS',
      'Chrome OS' => 'CrOS'
    ];

    $browser_list = [
      'Chrome' => 'Chrome',
      'Firefox' => 'Firefox',
      'Safari' => 'Safari',
      'Opera' => 'Opera',
      'Edge' => 'Edge',
      'MSIE' => 'MSIE|IEMobile|MSIEMobile',
      'BlackBerry' => 'BlackBerry|BB10|RIM Tablet OS',
      'UC Browser' => 'UCBrowser',
      'Opera Mini' => 'Opera Mini',
      'Opera Mobi' => 'Opera Mobi',
      'Nokia' => 'Nokia',
      'SonyEricsson' => 'SonyEricsson',
      'Samsung' => 'Samsung',
      'LG' => 'LG',
      'HTC' => 'HTC',
      'Sony' => 'Sony',
      'Asus' => 'Asus',
      'Micromax' => 'Micromax',
      'Palm' => 'Palm',
      'Vertu' => 'Vertu',
      'Pantech' => 'Pantech',
      'Fly' => 'Fly',
      'iMobile' => 'iMobile',
      'SimValley' => 'SimValley',
      'Ubuntu Touch' => 'Ubuntu Touch',
      'Windows CE' => 'Windows CE',
      'WindowsCE' => 'WindowsCE',
      'Smartphone' => 'Smartphone',
      'Armv' => 'Armv',
      'Spice' => 'Spice',
      'Bird' => 'Bird',
      'ZTE' => 'ZTE',
      'Alcatel' => 'Alcatel',
      'Lenovo' => 'Lenovo',
      'SonyEricsson' => 'SonyEricsson',
      'Ericsson' => 'Ericsson',
      'Bada' => 'Bada',
      'Meizu' => 'Meizu',
      'Xolo' => 'Xolo',
      'Lava' => 'Lava',
      'iOne' => 'iOne',
      'Celkon' => 'Celkon',
      'Gionee' => 'Gionee',
      'Vivo' => 'Vivo',
      'Nexus' => 'Nexus',
      'OnePlus' => 'OnePlus',
      'Yu' => 'Yu',
      'Acer' => 'Acer',
      'Xiaomi' => 'Xiaomi',
      'OPPO' => 'OPPO',
      'Coolpad' => 'Coolpad',
      'Wiko' => 'Wiko',
      'Generic Smartphone' => 'Generic Smartphone'
    ];

    // check device
    foreach ($device_list as $device => $regex) {
      if(preg_match("/$regex/i", $user_agent, $match)){
        $user_agent_info['device'] = $device;
        $user_agent_info['device_name'] = $match[0];
        break;
      }
    }

    // check os
    foreach ($os_list as $os => $regex) {
      if(preg_match("/$regex/i", $user_agent, $base_matches)){
        $user_agent_info['os'] = $os;
        $user_agent_info['os_version'] = $base_matches[0];
        if(is_null($user_agent_info['os_version'])){
          if(preg_match("/$os (.*);/i", $user_agent, $matches)){
            $user_agent_info['os_version'] = $matches[1];
          }
        }
        break;
      }
    }

    // check browser
    foreach ($browser_list as $browser => $regex) {
      if(preg_match("/$regex/i", $user_agent)){
        $user_agent_info['browser'] = $browser;
        if(preg_match("/$browser\/(.*);/i", $user_agent, $matches)){
          $user_agent_info['browser_version'] = $matches[1];
        }
        break;
      }
    }

    return $user_agent_info;
  }

  /**
   * Get Client IP address
   */
  public function ip() {
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
   * javascript http://www.geoplugin.net/javascript.gp
   * @param string $key
   */
  public function client_info($key=''){
    $info = [];
    $userIp = $this->ip();
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

}
