<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Session;

class Language {

  public $lang;


  public function __construct(){
    $this->lang = Session::has('app_language') 
      ? Session::get('app_language') 
      : Session::set('app_language', 'en');
  }



  public static function switch($lang){
    if(Session::get('app_language') !== $lang){
      Session::set('app_language', $lang);
    }
  }

}
