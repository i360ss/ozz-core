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

  private function __construct() {}

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
    $this->all = [
      'server'            => $this->server(),
      'root'              => $this->root(),
      'port'              => $this->port(),
      'protocol'          => $this->protocol(),
      'method'            => $this->method(),
      'host'              => $this->host(),
      'time'              => $this->time(),
      'ip'                => $this->ip(),
      'user_agent'        => $this->header('user-agent'),
      'headers'           => $this->headers(),
      'cookies'           => $this->cookies(),
      'cache'             => $this->cache(),
      'lang'              => $this->lang(),
      'encoding'          => $this->encoding(),
      'path'              => $this->path(),
      'url'               => $this->url(),
      'url_parts'         => $this->urlPart(),
      'url_params'        => $this->urlParam(),
      'postData'          => $this->postData(),
      'query'             => $this->query(),
      'input'             => $this->input(),
      'content'           => $this->content(),
      'files'             => $this->files(),
      'secure'            => $this->isSecure(),
    ];

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
    return $_SERVER['REQUEST_TIME'];
  }

  /**
   * Request Cookies
   */
  public function cookies(){
    return $_COOKIE;
  }

  /**
   * Request Cookies
   * @param string $key Cookie key
   */
  public function cookie($key=false){
    if ($key === false) {
      return $_COOKIE;
    }
    return $_COOKIE[$key] ?? null;
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
  public function isSecure(){
    return isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on';
  }

  /**
   * Check if HTTPS or not
   */
  public function isAjax(){
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
  }

  /**
   * Check request is JSON
   */
  public function isJson(){
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

    return str_contains($contentType, 'application/json');
  }

  /**
   * HTTP Headers
   * @return array Array of headers
   */
  public function headers(): array {
    if (function_exists('getallheaders')) {
      return array_change_key_case(getallheaders(), CASE_LOWER);
    }

    $headers = [];

    foreach ($_SERVER as $k => $v) {
      if (str_starts_with($k, 'HTTP_')) {
        $name = strtolower(str_replace('_', '-', substr($k, 5)));
        $headers[$name] = $v;
      }
    }

    return $headers;
  }

  /**
   * Return HTTP Header value of provided key
   * @param string $key Key for select specific header value
   */
  public function header(?string $key = null) {
    $headers = $this->headers();
    if ($key === null) {
      return $headers;
    }
    $key = strtolower($key);
    return $headers[$key] ?? null;
  }

  /**
   * Returns Request path
   */
  public function path(){
    if(isset($_SERVER['REQUEST_URI'])){
      $path = $_SERVER['REQUEST_URI'] ?? '/';
      $pos = is_string($path) ? strpos($path, '?') : false;
      if(!$pos){
        return $path;
      }
      return substr($path, 0, $pos);
    } else {
      return '/';
    }
  }

  /**
   * Get POST data only
   * @param string|int|false $key  Specific key to retrieve, or false for all
   * @param mixed $default Default value if key not found
   * @return mixed Single value, full array, or default
   */
  public function postData(string|int|false $key = false, mixed $default = null): mixed {
    if ($key === false) {
      return $_POST;
    }

    return $_POST[$key] ?? $default;
  }

  /**
   * Get merged GET + POST data
   * (POST overrides GET on duplicate keys)
   * @param string|int|false $key Specific key to retrieve, or false for all
   * @param mixed $default Default value if key not found
   * @return mixed Single value, full array, or default
   */
  public function input(string|int|false $key = false, mixed $default = null): mixed {
    $data = array_merge($_GET, $_POST);
    if ($key === false) {
      return $data;
    }

    return $data[$key] ?? $default;
  }

  /**
   * Get JSON-decoded request body
   * @param bool $assoc Return as associative array if true
   * @return mixed Decoded JSON or null if invalid/empty
   */
  public function content($key = false, bool $assoc = true): mixed {
    $raw = $this->raw();

    if (!$raw) {
      return null;
    }

    $data = json_decode($raw, $assoc);
    $d = (json_last_error() === JSON_ERROR_NONE) ? $data : null;

    if ($key === false) {
      return $d;
    }

    return $d[$key];
  }

  /**
   * Get GET query parameters only
   * @param string|int|false $key Specific key to retrieve, or false for all
   * @param mixed $default Default value if key not found
   * @return mixed Single value, full array, or default
   */
  public function query(string|int|false $key = false, mixed $default = null): mixed {
    if ($key === false) {
      return $_GET;
    }

    return $_GET[$key] ?? $default;
  }

  /**
   * Returns All Files sent over the request
   */
  public function files(){
    return $_FILES;
  }

  /**
   * Returns Files sent over the request
   * @param string|int|false $key Specific file key, or false for all files
   * @return mixed Single file array, full files array, or null
   */
  public function file(string|int|false $key = false): mixed {
    if ($key === false) {
      return $_FILES;
    }

    return $_FILES[$key] ?? null;
  }

  /**
   * Get raw request body (unparsed)
   * @return string|null Raw body string or null if empty
   */
  public function raw(): ?string {
    static $raw = null;
    if ($raw === null) {
      $raw = file_get_contents('php://input');
    }

    return $raw ?: null;
  }

  /**
   * URL Parameters (Defined parameter keys and values on Route)
   * @param string $key the key of URL parameter
   * @return array|string|int
   */
  public function urlParam($key=''){
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
  public function urlPart($q=''){
    $URL = $this->path() ?? false;
    if(isset($URL)){
      $parts = explode('/', $URL);
      if ($q=='') {
        return $parts;
      } elseif (count($parts) > $q) {
        return $parts[$q];
      }
    } else {
      return false;
    }
  }

  /**
   * Another way to call urlPart()
   * @param int $q index of segment
   */
  public function segment($q=''){
    return $this->urlPart($q);
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
  public function isMethod($method){
    return $this->method() === strtolower($method);
  }

  /**
   * Get Client IP address
   * 
   * @param array $trustedProxies List of proxy IPs you control
   * @return string
   */
  public function ip(array $trustedProxies = []): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    // If behind trusted proxy, allow forwarded headers
    if (in_array($ip, $trustedProxies, true)) {
      if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP) ?: $ip;
      }
      if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $forwardedIps = array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
        foreach (array_reverse($forwardedIps) as $fwdIp) {
          if (filter_var($fwdIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return $fwdIp;
          }
        }
      }
    }

    return $ip;
  }

}
