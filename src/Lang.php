<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Lang {

  private $messages;
  private $errors;


  public function __construct(){
    $this->errors = include APP_LANG_PATH.'errors.php';
    $this->messages = include APP_LANG_PATH.'messages.php';
  }



  /**
   * return the required translated error message
   * @param string $key the key of the required error
   * @param string|array $param arguments to replace the string with
   */
  public function error($key, $param=false){
    $res = isset($this->errors[$key]) ? $this->errors[$key] : $this->errors['invalid_array_key'];
    return $this->setOutput($res, $param);
  }



  /**
   * return the required translated message
   * @param string $key the key of the required string
   * @param string|array $param arguments to replace the string with
   */
  public function message($key, $param=false){
    $res = isset($this->messages[$key]) ? $this->messages[$key] : $this->errors['invalid_array_key'];
    return $this->setOutput($res, $param);
  }



  /**
   * Replace the messages and errors with provided arguments
   * @param string $res the translated Error/Message
   * @param string|array $param the arguments to replace with
   */
  private function setOutput($res, $param){
    if(isset($param)){
      if(is_array($param) && !empty($param)){
        foreach ($param as $k => $v) {
          $res = str_replace(":$k", esc_x($v), $res);
        }
      } elseif(is_string($param)) {
        $res = str_replace('::', esc_x($param), $res);
      }
    }
    
    return $res;
  }

}
