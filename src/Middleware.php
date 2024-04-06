<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Request;
use Ozz\Core\Response;

class Middleware {

  public static function execute($middleware = null, $coreData=null){
    $request = Request::getInstance();
    $response = Response::getInstance();

    require __DIR__.SPC_BACK['core'].'/app/RegisterMiddleware.php';

    if($middleware == null){
      $mv = $auto_middleware;
      foreach ($mv as $k => $v) {
        if(class_exists($v)){
          $callBackMv[0] = new $v($coreData);
          $callBackMv[1] = 'handle';
          call_user_func_array($callBackMv, [$request, $response]);
        } else {
          return Err::custom([
            'msg' => "Middleware Class [$v] Not found",
            'info' => 'You have to create the middleware before calling it',
            'note' => "Run [ php ozz c:middleware $v]",
          ]);
        }
      }
    } else {
      if(array_key_exists($middleware, $route_middleware)){
        $callBackMv[0] = new $route_middleware[$middleware]($coreData);
        $callBackMv[1] = 'handle';
        return call_user_func_array($callBackMv, [$request, $response]);
      } else {
        return Err::invalidMiddleware($middleware);
      }
    }
  }

}