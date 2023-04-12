<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\AfterRequest;
use Ozz\Core\Err;

class Response {

  private static $instance;
  private $content;
  private $status_code;
  private $headers = [];

  /**
   * Single Instance of Response
   */
  public static function getInstance() {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Get Response properties
   * @param string $key property name (optional)
   */
  public function get($key=false){
    $props = [
      'status_code' => $this->status_code,
      'headers' => $this->headers,
      'content' => $this->content,
    ];

    return $key ? $props[$key] : $props;
  }

  /**
   * Check header exist
   * @param string Header key
   */
  public function hasHeader($key=false){
    if(isset($key)){
      return isset($this->headers[$key]) && !empty($this->headers[$key]);
    } else {
      return !empty($this->headers);
    }
  }

  /**
   * Update Response Header
   * @param string $key Header key
   * @param string $val Header Value
   */
  public function setHeader(string $key, string $val){
    $this->headers[$key] = $val;
  }

  /**
   * Set Response status code
   * @param int $status_code HTTP response status code
   */
  public function setStatusCode(int $status_code){
    $this->status_code = $status_code;
  }

  /**
   * Set Response Content
   * @param $content response content
   */
  public function setContent($content){
    $this->content = $content;
  }

  /**
   * Send Response to client
   */
  public function send(){
    $show_debug_bar = false;
    $page_cache = false;

    !is_null($this->status_code)
      ? http_response_code($this->status_code)
      : false;

    if(isset($this->headers) && !empty($this->headers)){
      foreach ($this->headers as $key => $header) {
        header("$key: $header");

        // Only for page view Response
        if($key == 'Content-Type' && in_array($header, ['text/html', 'text/html; charset='.CONFIG['CHARSET'], 'text/plain'])){
          $show_debug_bar = true;
          $page_cache = true;
        }
      }
    } else {
      // Default header
      header('Content-Type', 'text/html; charset='.CONFIG['CHARSET']);
    }

    // Render Exceptions
    if(DEBUG){
      $exceptions = Err::getInstance();
      if(isset($exceptions::$exception_doms) && !empty($exceptions::$exception_doms) && is_array($exceptions::$exception_doms)){
        foreach ($exceptions::$exception_doms as $key => $exception) {
          echo $exception;
        }
      }
    }

    // Render final Content
    echo $this->content;

    // Store page cache for this page
    $http_error_codes = [400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 421, 422, 423, 424, 425, 426, 428, 429, 431, 451, 500, 501, 502, 503, 504, 505, 506, 507, 508, 510, 511];

    if(!in_array($this->status_code, $http_error_codes) && CONFIG['PAGE_CACHE_LIFETIME'] && $page_cache === true){
      $request = Request::getInstance();
      (new Cache)->store('page', $request->url(), $this->content);
    }

    // Show debug bar
    if(DEBUG && SHOW_DEBUG_BAR && $show_debug_bar){
      global $DEBUG_BAR;
      $DEBUG_BAR->show();
    }

    $afterRequest = new AfterRequest;
    $afterRequest->run();
  }

}