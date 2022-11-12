<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\AfterRequest;

class Response {

  public $content;
  public $status_code;
  public $headers;


  public function __construct($content, $status_code, $headers){
    $this->content = $content;
    $this->status_code = $status_code;
    $this->headers = $headers;
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
   * Send Response to client
   */
  public function send(){
    $show_debug_bar = false;
    $page_cache = false;

    foreach ($this->headers as $key => $header) {
      header("$key: $header");

      // Only for page view Response
      if($key == 'Content-Type' && in_array($header, ['text/html', 'text/html; charset='.CHARSET, 'text/plain'])){
        $show_debug_bar = true;
        $page_cache = true;
      }
    }

    http_response_code($this->status_code);
    echo $this->content;

    // Store page cache for this page
    if($this->status_code !== 404 && PAGE_CACHE_TIME !== '0' && $page_cache === true){
      $request = new Request;
      (new Cache)->store('page', $request->all()['url'], $this->content);
    }

    // Show debug bar
    if(DEBUG && SHOW_DEBUG_BAR && $show_debug_bar){
      global $DEBUG_BAR;
      $DEBUG_BAR->show();
    }

    $afterReq = new AfterRequest;
    $afterReq->run();
  }

}