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
   * @param string $key Specific key to return specific value
   */
  public function all($key=''){
    return !empty($key) 
      ? $this->full_request[$key] 
      : $this->full_request;
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
   * User agent info (Separated)
   * @param boolean Return only the user-agent string if (true)
   */
  public static function user_agent($string=false){

    if($string === true){
      return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $user_agent_info = [
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
  public static function client_info($key=''){
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
