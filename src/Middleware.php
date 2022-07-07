<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\app\RegisterMiddleware;

class Middleware {
  
  public static function execute($middleware = null, $coreData=null){
    if($middleware == null){
      $mv = RegisterMiddleware::autoMiddleware();
      foreach ($mv as $k => $v) {
        $callBackMv[0] = new $v($coreData);
        $callBackMv[1] = 'handle';
        call_user_func( $callBackMv );
      }
    }
    else{
      if(array_key_exists($middleware, RegisterMiddleware::routeMiddleware())){
        $mv = RegisterMiddleware::routeMiddleware();
        $callBackMv[0] = new $mv[$middleware]($coreData);
        $callBackMv[1] = 'handle';
        return call_user_func( $callBackMv );
      }
      else{
        Err::invalidMiddleware($middleware);
      }
    }
  }
  
}