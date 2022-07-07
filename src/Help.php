<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\core;

# CSS JS Minify
use MatthiasMullie\Minify;
use Ozz\core\system\SubHelp;

# ----------------------------------
// Application Helper
# ----------------------------------
class Help extends Appinit {
  
  # ----------------------------------
  # Get Path
  # ----------------------------------
  public static function getPath(){
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    $position = strpos($path, '?');
    if(!$position){
      return $path;
    }
    return substr($path, 0, $position);
  }
  
  
  
  # ----------------------------------
  # Get HTTP request Methods
  # ----------------------------------
  public static function getMethod(){
    return strtolower($_SERVER['REQUEST_METHOD']);
  }
  
  // Check is it GET
  public static function isGet(){
    return self::getMethod() === 'get';
  }
  
  // Check is it POST
  public static function isPost(){
    return self::getMethod() === 'post';
  }
  
  // Check is it PUT
  public static function isPut(){
    return self::getMethod() === 'put';
  }
  
  // Check is it DELETE
  public static function isDelete(){
    return self::getMethod() === 'delete';
  }
  
  
  
  # ----------------------------------
  # Sanitize Submited Form Data
  # ----------------------------------
  public static function formData($param=''){
    $output = [];
    if(self::isGet()){
      foreach ($_GET as $k => $v) {                
        if(is_array($v)){
          $output[$k] = Sanitize::array($v);
        }
        else{
          (strtolower(substr($k, 0, 5)) == 'html:') ? $output[$k] = Sanitize::htmlEncode($v) : $output[$k] = Sanitize::string($v);
        }
      }
    }
    if(self::isPost()){
      foreach ($_POST as $k => $v) {
        if($k == 'password'){
          $output[$k] = $v;
        }
        elseif(is_array($v)){
          $output[$k] = Sanitize::array($v);
        }
        else{
          (strtolower(substr($k, 0, 5)) == 'html:') ? $output[$k] = Sanitize::htmlEncode($v) : $output[$k] = Sanitize::string($v);
        }
      }
    }
    return $param ? $output[$param] : $output; // Filtered Data
  }
  
  
  
  # ----------------------------------
  # URL Query Part
  # ----------------------------------
  public static function urlPart($q=''){
    if(isset($_SERVER['REQUEST_URI'])){
      $query = explode('/', filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL));
      if($q==''){
        return $query;
      }
      elseif(count($query) > $q){
        return $query[$q];
      }
    }
  }
  
  
  
  # ----------------------------------
  # Get URL Param by key
  # ----------------------------------
  public static function urlParam($q=''){
    if(isset($_GET[$q])){
      return filter_var($_GET[$q], FILTER_SANITIZE_URL);
    }
    elseif($q == ''){
      return Sanitize::array($_GET);
    }
  }
  
  
  
  # ----------------------------------
  # Encode or Decode String (Base 64 encription)
  # ----------------------------------
  public static function enc_base64($e){
    return strtr(base64_encode($e), '+/=', '-_,');
  }
  public static function dec_base64($e){
    return base64_decode(strtr($e, '-_,', '+/='));
  }
  
  
  
  #-------------------------------------------------------------------------
  // Get Geo IP information
  #-------------------------------------------------------------------------
  // javascript http://www.geoplugin.net/javascript.gp
  public static function client($i=''){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])):
      $userIp = $_SERVER['HTTP_CLIENT_IP'];
    
    elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])):
      $userIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
      
    else:
      $userIp = $_SERVER['REMOTE_ADDR'];
    endif;
    
    $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$userIp"));
    
    $clientInfo = array(
      'ip' => $geo['geoplugin_request'],
      'country' => $geo['geoplugin_countryName'],
      'country_code' => $geo['geoplugin_countryCode'],
      'city' => $geo['geoplugin_city'],
      'region' => $geo['geoplugin_regionName'],
      'timezone' => $geo['geoplugin_timezone'],
      'currency' => $geo['geoplugin_currencyCode'],
      'exchange' => $geo['geoplugin_currencyConverter'], // 1 USD = this
      'currency_symbol' => $geo['geoplugin_currencySymbol'],
    );
    return $i=="" ? $clientInfo : $clientInfo[$i];
  }
    
    
    
  # -----------------------------------------------------------------------
  // HTML Minfier
  # -----------------------------------------------------------------------
  public static function HTMLMinify(){
    function minifier($code) {
      $search = array(
        '/\>[^\S ]+/s',
        '/[^\S ]+\</s',
        '/(\s)+/s',
        '/<!--(.|\s)*?-->/'
      );
      $replace = array('>', '<', '\\1');
      $code = preg_replace($search, $replace, $code);
      return $code;
    }
    ob_start("minifier"); // HTML Minifier
  }
    
    
    
  # -----------------------------------------------------------------------
  // ERROR  Set status code to response
  # -----------------------------------------------------------------------
  public static function statusCode($code){
    return http_response_code($code);
  }
    
    
    
  # -----------------------------------------------------------------------
  // ERROR  Set status code to response
  # -----------------------------------------------------------------------
  public static function dump($data, $label='', $return = false){
    if(DEBUG){
      return SubHelp::varDump($data, $label, $return);
    }
  }
    
    
} // Helper END