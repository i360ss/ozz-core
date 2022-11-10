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
  public $cookies; // have more work



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
    foreach ($this->headers as $key => $header) {
      header("$key: $header");

      if($key == 'Content-Type' && in_array($header, ['text/html', 'text/html; charset='.CHARSET, 'text/plain'])){
        $show_debug_bar = true;
      }
    }

    http_response_code($this->status_code);
    echo $this->content;

    if(DEBUG && SHOW_DEBUG_BAR && $show_debug_bar){
      global $DEBUG_BAR;
      $DEBUG_BAR->show();
    }

    $afterReq = new AfterRequest;
    $afterReq->run();
  }

}