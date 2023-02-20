<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\AfterRequest;

class Response {

  private static $instance;
  private $content;
  private $status_code;
  private $headers = [];



  private function __construct() {}



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
  public function has_header($key=false){
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
  public function set_header(string $key, string $val){
    $this->headers[$key] = $val;
  }



  /**
   * Set Response status code
   * @param int $status_code HTTP response status code
   */
  public function set_status_code(int $status_code){
    $this->status_code = $status_code;
  }



  /**
   * Set Response Content
   * @param $content response content
   */
  public function set_content($content){
    $this->content = $content;
  }



  /**
   * Send Response to client
   */
  public function send(){
    $show_debug_bar = false;
    $page_cache = false;

    http_response_code($this->status_code);

    if(isset($this->headers) && !empty($this->headers)){
      foreach ($this->headers as $key => $header) {
        header("$key: $header");

        // Only for page view Response
        if($key == 'Content-Type' && in_array($header, ['text/html', 'text/html; charset='.CHARSET, 'text/plain'])){
          $show_debug_bar = true;
          $page_cache = true;
        }
      }
    } else {
      // Default header
      header('Content-Type', 'text/html; charset='.CHARSET);
    }

    echo $this->content;

    // Store page cache for this page
    if($this->status_code !== 404 && PAGE_CACHE_TIME !== '0' && $page_cache === true){
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