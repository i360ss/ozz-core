<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Request;
use Ozz\Core\Sanitize;

class Router extends AppInit {

  protected static $ValidRoutes=[];
  protected static $template; // Base Template for view


  /**
   * GET Route Group
   * 
   * @param array|string $middleware
   * @param string $template Base layout template
   * @param array $callback route paths as keys, objects or strings as values 
   */
  public static function getGroup($middleware, $template, $callback){
    foreach ($callback as $k => $v) {
      self::get($k, $v, $template, $middleware);
    }
  }



  /**
   * POST Route Group
   * 
   * @param array|string $middleware
   * @param array $callback route paths as keys, objects or strings as values 
   * @param string $template Base layout template (optional)
   */
  public static function postGroup($middleware, $callback, $template=null){
    foreach ($callback as $k => $v) {
      self::post($k, $v, $middleware, $template);
    }
  }



  /**
   * GET Method Route
   * 
   * @param string $route
   * @param array|string|function $callBack
   * @param string $baseTemplate Base layout template
   * @param array|string $middleware
   */
  public static function get($route, $callBack, $baseTemplate=null, $middlewares=null){
    $finalPath = self::finalizeRoutePath($route);
    $finalRoute = $finalPath['route'];
    self::$ValidRoutes['get'][$finalRoute]['urlParam'] = $finalPath['data'];
    self::$ValidRoutes['get'][$finalRoute]['middlewares'] = $middlewares;
    self::$ValidRoutes['get'][$finalRoute]['temp'] = $baseTemplate;
    self::$ValidRoutes['get'][$finalRoute]['callback'] = $callBack;
  }



  /**
   * Initial route method
   * 
   * @param string $method (get, post, put, patch, delete)
   * @param string $route
   * @param array|string|function $callBack
   * @param array|string $middlewares
   * @param string $baseTemplate Base layout template (optional)
   */
  protected static function initialRoute($method, $route, $callBack, $middlewares, $baseTemplate){
    $finalPath = self::finalizeRoutePath($route);
    $finalRoute = $finalPath['route'];
    self::$ValidRoutes[$method][$finalRoute]['urlParam'] = $finalPath['data'];
    self::$ValidRoutes[$method][$finalRoute]['callback'] = $callBack;
    self::$ValidRoutes[$method][$finalRoute]['middlewares'] = $middlewares;
    self::$ValidRoutes[$method][$finalRoute]['temp'] = $baseTemplate;
  }



  /**
   * POST Method Route
   * 
   * @param string $route
   * @param array|string|function $callBack
   * @param array|string $middleware
   * @param string $baseTemplate Base layout template (optional)
   */
  public static function post($route, $callBack, $middlewares=null, $baseTemplate=null){
    return self::initialRoute('post', $route, $callBack, $middlewares, $baseTemplate);
  }



  /**
   * DELETE Method Route
   * 
   * @param string $route
   * @param array|string|function $callBack
   * @param array|string $middleware
   * @param string $baseTemplate Base layout template (optional)
   */
  public static function delete($route, $callBack, $middlewares=null, $baseTemplate=null){
    return self::initialRoute('delete', $route, $callBack, $middlewares, $baseTemplate);
  }



  /**
   * PATCH Method Route
   * 
   * @param string $route
   * @param array|string|function $callBack
   * @param array|string $middleware
   * @param string $baseTemplate Base layout template (optional)
   */
  public static function patch($route, $callBack, $middlewares=null, $baseTemplate=null){
    return self::initialRoute('patch', $route, $callBack, $middlewares, $baseTemplate);
  }



    /**
   * PUT Method Route
   * 
   * @param string $route
   * @param array|string|function $callBack
   * @param array|string $middleware
   * @param string $baseTemplate Base layout template (optional)
   */
  public static function put($route, $callBack, $middlewares=null, $baseTemplate=null){
    return self::initialRoute('put', $route, $callBack, $middlewares, $baseTemplate);
  }



  /**
   * Finalize URL Parameter setting
   * 
   * @param string $route
   */
  private static function finalizeRoutePath($route){

    $routeData['route'] = $route;
    $routeData['data'] = [];

    if(preg_match("~\{\s*(.*?)\s*\}~",  $route)){
      $urlPlaceholders = [];
      preg_match_all("~\{\s*(.*?)\s*\}~",  $route, $urlPlaceholders);

      $realUrlVals['innerRoute'] = explode('/', $route);
      $realUrlVals['url'] = Request::url_part();
      $realUrlVals['final'] = [];

      if(count($realUrlVals['innerRoute']) == count($realUrlVals['url'])){
        foreach ($realUrlVals['innerRoute'] as $k => $v) {
          if(preg_match_all("~\{\s*(.*?)\s*\}~",  $v)){
            $realUrlVals['final'][$v] = $realUrlVals['url'][$k];
          }
        }
        foreach ($realUrlVals['innerRoute'] as $key => $value) {
          if(array_key_exists($value, $realUrlVals['final'])){
            $realUrlVals['innerRoute'][$key] = $realUrlVals['final'][$value];
          }
        }

        // Final Route and URL Parameters
        $routeData['route'] = implode('/', $realUrlVals['innerRoute']); // Final Route Path
        foreach ($realUrlVals['final'] as $k => $v) {
          $k = substr($k, 1, -1);
          $routeData['data'][$k] = $v; // Final Route URL Params
        };
      }
      else{
        $routeData['route'] = '404';
      }
    }
    return $routeData;
  }



  /**
   * Resolve route
   */
  protected static function resolve(){
    global $DEBUG_BAR;

    $path = Request::getPath();
    $method = Request::method();

    // Rewrite URL
    if($path != '/'){
      if(substr($path, -1) == '/'){
        $path = preg_replace('/(\/+)/','/', substr($path, 0, -1));
        return Router::redirect($path);
      }

      if(preg_match('/(\/\/+)/', $path) > 0){
        $path = preg_replace('/(\/+)/','/',$path);
        return Router::redirect($path);
      }
    }

    $callback = self::$ValidRoutes[$method][$path]['callback'] ?? false;

    // Render 404 if callback is false
    if($callback === false){
      Request::statusCode('404');
      return self::view('404', [], 'layout');
      exit;
    }

    // Get set and execute Middlewares
    $thisMiddlewares = self::$ValidRoutes[$method][$path]['middlewares'] ?? false;
    if($thisMiddlewares && is_array($thisMiddlewares)){
      foreach ($thisMiddlewares as $mv) { Middleware::execute($mv); }
    }
    elseif($thisMiddlewares && is_string($thisMiddlewares)){
      Middleware::execute($thisMiddlewares);
    }

    // Get base template for this request
    self::$template = self::$ValidRoutes[$method][$path]['temp'] 
    ? self::$ValidRoutes[$method][$path]['temp']
    : false;

    // Render View
    if(is_string($callback)){
      return self::view($callback);
    }

    // Load Class
    if(is_array($callback)){
      if(!isset($callback[1])){
        $callback[1] = 'index';
      }

      // Check class existence
      if(class_exists($callback[0])){
        $callback[0] = new $callback[0];

        // Check method existence
        if(method_exists($callback[0], $callback[1])){
          $DEBUG_BAR->set('ozz_controller', [
            'controller' => get_class($callback[0]),
            'method' => $callback[1]
          ]);

          if(is_callable($callback)){
            return call_user_func($callback, new Request); // Execute
          }
          else {
            Err::custom([
              'msg' => "Error on class [ ".get_class($callback[0])." ]",
              'info' => "Please check the class name in your route for any spelling mistakes. If you don't have a class already, please create it first",
              'note' => "Command to create a class [ php ozz c:c className ]"
            ]);
          }
        }
        else {
          // Method not found
          $DEBUG_BAR->set('ozz_controller', [
            'controller' => get_class($callback[0]),
            'method' => $callback[1]. '<f style="color:red;"> Method not found</f>',
          ]);

          Err::custom([
            'msg' => "Method [$callback[1]] Not found in [".get_class($callback[0])."] class",
            'info' => "Please check the method name in your route for any spelling mistakes. If you don't have a [$callback[1]] method already, please create it first",
          ]);
        }
      }
      else {
        $DEBUG_BAR->set('ozz_controller', [
          'controller' => $callback[0] . '<f style="color:red;"> Class not found or invalid</f>',
          'method' => $callback[1]
        ]);

        Err::custom([
          'msg' => "Class [".is_string($callback[0]) ? $callback[0] : get_class($callback[0])."] not found",
          'info' => "Please check the class name in your route for any spelling mistakes. If you don't have a class already, please create it first",
          'note' => "Command to create a class [ php ozz c:c className ]"
        ]);
      }
    }
    else {
      return call_user_func($callback, new Request); // Execute
    }
  }



  /**
   * Render View
   * 
   * @param string $vv The viw file (without extension)
   * @param array $data Data to be passed to view
   * @param string $template Base layout template
   */
  public static function view($vv, $data=[], $template=''){
    new Request;
    return Templating::render($vv, $data, $template, self::$template);
  }  



  /**
   * Header Redirect
   * 
   * @param string $to Path/URL to redirect
   * @param int $status Redirect status code
   */
  public static function redirect($to, $status=301){
    Request::statusCode($status);
    header("Location: $to");
    exit;
  }

}