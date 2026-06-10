<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Session;
use Ozz\Core\Err;

class Lang {

  private $messages;
  private $errors;
  public $lang;

  public function __construct(){
    $this->lang = Session::has('app_lang') 
      ? Session::get('app_lang') 
      : Session::set('app_lang', env('app', 'APP_LANG'));

    if(file_exists(LANG_DIR.'errors.php')){
      $this->errors = include LANG_DIR.'errors.php';
    } else {
      $this->errors = include CORE_DIR.trim(CONFIG['APP_PATHS']['lang'], DS).DS.'en'.DS.'errors.php';
    }

    if(file_exists(LANG_DIR.'messages.php')){
      $this->messages = include LANG_DIR.'messages.php';
    } else {
      $this->messages = include CORE_DIR.trim(CONFIG['APP_PATHS']['lang'], DS).DS.'en'.DS.'messages.php';
    }
  }

  /**
   * Change language
   * @param string $lang
   */
  public function switch($lang){
    if(Session::get('app_lang') !== $lang){
      Session::set('app_lang', $lang);
    }
  }

  /**
   * return the required translated error message
   * @param string $key the key of the required error
   * @param string|array $param arguments to replace the string with
   */
  public function error($key, $param=false){
    $res = isset($this->errors[$key]) ? $this->errors[$key] : 'Something went wrong!';
    return $this->setOutput($res, $param);
  }

  /**
   * return the required translated message
   * @param string $key the key of the required string
   * @param string|array $param arguments to replace the string with
   */
  public function message($key, $param=false){
    $res = isset($this->messages[$key]) ? $this->messages[$key] : 'Something went wrong!';
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
          if(is_array($v)){
            $v = implode(', ', $v);
          }
          $res = str_replace(":$k", esc_x($v), $res);
        }
      } elseif(is_string($param)) {
        $res = str_replace('::', esc_x($param), $res);
      }
    }

    return $res;
  }

}
