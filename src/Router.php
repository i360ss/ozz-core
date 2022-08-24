<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Request;
use Ozz\Core\Sanitize;

class Router extends Appinit {
  
  protected static $ValidRoutes=[];
  protected static $template; // Base Template for view
  
  # ----------------------------------
  # GET Route Group
  # ----------------------------------
  public static function getGroup($middleware, $template, $callback){
    foreach ($callback as $k => $v) {
      self::get($k, $v, $template, $middleware);
    }
  }
  
  
  
  # ----------------------------------
  # POST Route Group
  # ----------------------------------
  public static function postGroup($middleware, $callback, $template=null){
    foreach ($callback as $k => $v) {
      self::post($k, $v, $middleware, $template);
    }
  }
  
  
  
  # ----------------------------------
  # GET Method Route
  # ----------------------------------
  public static function get($route, $callBack, $baseTemplate=null, $middlewares=null){
    $finalPath = self::finalizeRoutePath($route);
    $finalRoute = $finalPath['route'];
    self::$ValidRoutes['get'][$finalRoute]['urlParam'] = $finalPath['data'];
    self::$ValidRoutes['get'][$finalRoute]['middlewares'] = $middlewares;
    self::$ValidRoutes['get'][$finalRoute]['temp'] = $baseTemplate;
    self::$ValidRoutes['get'][$finalRoute]['callback'] = $callBack;
  }
  
  
  
  # ----------------------------------
  # POST Method Route
  # ----------------------------------
  public static function post($route, $callBack, $middlewares=null, $baseTemplate=null){
    self::$ValidRoutes['post'][$route]['callback'] = $callBack;
    self::$ValidRoutes['post'][$route]['middlewares'] = $middlewares;
    self::$ValidRoutes['post'][$route]['temp'] = $baseTemplate;
  }
  
  
  
  # ----------------------------------
  # Finalize Get Method URL Parameter setting
  # ----------------------------------
  private static function finalizeRoutePath($route){
    
    $routeData['route'] = $route;
    $routeData['data'] = [];
    
    if(preg_match("~\{\s*(.*?)\s*\}~",  $route)){
      $urlPlaceholders = [];
      preg_match_all("~\{\s*(.*?)\s*\}~",  $route, $urlPlaceholders);
      
      $realUrlVals['innerRoute'] = explode('/', $route);
      $realUrlVals['url'] = Help::urlPart();
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
  
  
  
  # ----------------------------------
  # Resolve
  # ----------------------------------
  protected static function resolve(){
    
    $path = Help::getPath();
    $method = Help::getMethod();

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
      Help::statusCode('404');
      return self::view('404', [], 'base/layout');
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
    
    // Get base template for this GET Method request
    self::$template = self::$ValidRoutes[$method][$path]['temp'] 
    ? self::$ValidRoutes[$method][$path]['temp']
    : false;
    
    // Render View
    if(is_string($callback)){
      return self::view($callback);
    }
    // Load Class
    if(is_array($callback)){
      $callback[0] = new $callback[0];
    }
    return call_user_func($callback, new Request); // Execute
  }
  
  
  
  # ----------------------------------
  # Render View
  # ----------------------------------
  public static function view($vv, $data=[], $basetemp=''){
    new Request;
    return Templating::render($vv, $data, $basetemp, self::$template);
  }  
  
  

  # ----------------------------------
  # Header Redirect
  # ----------------------------------
  public static function redirect($to, $status=301){
    Help::statusCode($status);
    header("Location: $to");
    exit;
  }
  
}