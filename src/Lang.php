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

    if(file_exists(APP_LANG_PATH.'errors.php')){
      $this->errors = include APP_LANG_PATH.'errors.php';
    } else {
      return DEBUG
        ? Err::custom([
          'msg' => "errors.php Not found in current language directory ( app/lang/".APP_LANG."/ )",
          'info' => 'You need to create error message files for each language that your application using',
          'note' => "You can just copy <strong>app/lang/en/errors.php</strong> to <strong>app/lang/".APP_LANG."/</strong> and translate content",
        ])
        : false;
    }

    if(file_exists(APP_LANG_PATH.'messages.php')){
      $this->messages = include APP_LANG_PATH.'messages.php';
    } else {
      return DEBUG
        ? Err::custom([
          'msg' => "messages.php Not found in current language directory ( app/lang/".APP_LANG."/ )",
          'info' => 'You need to create message content files for each language that your application using',
          'note' => "You can just copy <strong>app/lang/en/messages.php</strong> to <strong>app/lang/".APP_LANG."/</strong> and translate content",
        ])
        : false;
    }
  }



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
