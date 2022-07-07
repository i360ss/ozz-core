<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\core;

class Request extends Router {
  
  private $fullRequest;
  private $method;
  
  
  function __construct(){
    $this->method = Help::getMethod();
    
    $this->fullRequest = [
      'server' => $_SERVER['SERVER_NAME'],
      'root' => $_SERVER['DOCUMENT_ROOT'],
      'port' => $_SERVER['SERVER_PORT'] ?? null,
      'protocol' => $_SERVER['SERVER_PROTOCOL'] ?? null,
      'method' => Help::getMethod() ?? null,
      'path' => $this->path(),
      'host' => $_SERVER['HTTP_HOST'] ?? null,
      'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
      'header' => $_SERVER['HTTP_ACCEPT'] ?? null,
      'cookie' => $_SERVER['HTTP_COOKIE'] ?? null,
      'cache' => $_SERVER['HTTP_CACHE_CONTROL'] ?? null,
      'lang' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null,
      'encoding' => $_SERVER['HTTP_ACCEPT_ENCODING'] ?? null,
      'requestTime' => [
        'time' => $_SERVER['REQUEST_TIME'],
        'floatTime' => $_SERVER['REQUEST_TIME_FLOAT']
      ],
      'url' => $_SERVER['REQUEST_URI'] ?? null,
      'urlParts' => Help::urlPart(),
      'queryString' => $_SERVER['QUERY_STRING'] ?? null,
      'query' => Help::urlParam() !== [] ? Help::urlParam() : null,
      'urlParam' => $this->urlParam() !== [] ? $this->urlParam() : null,
      'input' => Help::formData() !== [] ? Help::formData() : null,
      'files' => $_FILES,
    ];
  }
  
  
  
  # ---------------------------------------
  // All Request values
  # ---------------------------------------
  public function all(){
    return $this->fullRequest;
  }
  
  
  
  # ---------------------------------------
  // Input Values
  # ---------------------------------------
  public function input($param=''){
    return Help::formData($param) !== [] ? Help::formData($param) : null;
  }
  
  
  
  # ---------------------------------------
  // Url Query string values
  # ---------------------------------------
  public function query($param=''){
    return Help::urlParam($param) !== [] ? Help::urlParam($param) : null;
  }
  
  
  
  # ---------------------------------------
  // URL Parameters Values (SEO Friendly)
  # ---------------------------------------
  public function urlParam($param=''){
    if(isset(Router::$ValidRoutes[$this->method][$this->path()]['urlParam'])){
      $data = $param 
      ? Router::$ValidRoutes[$this->method][$this->path()]['urlParam'][$param]
      : Router::$ValidRoutes[$this->method][$this->path()]['urlParam'];
    }
    else{
      $data = null;
    }
    return $data !== [] ? $data : null;
  }
  
  
  
  # ---------------------------------------
  // Seperated URL Parts
  # ---------------------------------------
  public function urlPart($param=''){
    return Help::urlPart($param);
  }
  
  
  
  # ---------------------------------------
  // Files
  # ---------------------------------------
  public function files($param=''){
    return $_FILES;
  }
  
  
  
  // Other Commen Methods
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
    return Help::getMethod() ?? null;
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
  
  public function url($typ=''){
    return $_SERVER['REQUEST_URI'] ?? null;
  }
  
}