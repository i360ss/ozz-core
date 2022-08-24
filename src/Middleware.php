<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Middleware {
  
  public static function execute($middleware = null, $coreData=null){
    require __DIR__.SPC_BACK['core'].'/app/RegisterMiddleware.php';

    if ($middleware == null) {
      $mv = $auto_middlewares;
      foreach ($mv as $k => $v) {
        if (class_exists($v)) {
          $callBackMv[0] = new $v($coreData);
          $callBackMv[1] = 'handle';
          call_user_func( $callBackMv );
        } else {
          return DEBUG 
          ? Err::custom([
            'msg' => "Middleware Class [$v] Not found",
            'info' => 'You have to create the middleware before calling it',
            'note' => "Run [ php ozz c:middleware $v]",
          ])
          : false;
        }
      }
    } else {
      if (array_key_exists($middleware, $route_middlewares)) {
        $callBackMv[0] = new $route_middlewares[$middleware]($coreData);
        $callBackMv[1] = 'handle';
        return call_user_func( $callBackMv );
      } else {
        Err::invalidMiddleware($middleware);
      }
    }
  }
  
}