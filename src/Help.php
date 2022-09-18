<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\system\SubHelp;

class Help extends AppInit {


  /**
   * Get Path
   */
  public static function getPath(){
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    $position = strpos($path, '?');
    if(!$position){
      return $path;
    }
    return substr($path, 0, $position);
  }



  /**
   * Get HTTP request Methods
   */
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



  /**
   * Sanitize Submitted Form Data
   * @param string|array|int $param 
   */
  public static function formData($param=false){
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

    return $param ? $output[$param] : $output; // Sanitized Data
  }



  /**
   * URL query part
   * @param int $q
   */
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



  /**
   * Get URL param by key
   * @param string|int $q
   */
  public static function urlParam($q=''){
    if(isset($_GET[$q])){
      return filter_var($_GET[$q], FILTER_SANITIZE_URL);
    }
    elseif($q == ''){
      return Sanitize::array($_GET);
    }
  }



  /**
   * Encode or Decode String (Base 64 encryption)
   * @param string $e
   */
  public static function enc_base64($e){
    return strtr(base64_encode($e), '+/=', '-_,');
  }
  public static function dec_base64($e){
    return base64_decode(strtr($e, '-_,', '+/='));
  }



  /**
   * Get Geo IP information
   * javascript http://www.geoplugin.net/javascript.gp
   * @param string $key
   */
  public static function client($key=''){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $userIp = $_SERVER['HTTP_CLIENT_IP'];
    } elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $userIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $userIp = $_SERVER['REMOTE_ADDR'];
    }
    
    $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$userIp"));
    
    $clientInfo = array(
      'ip'              => $geo['geoplugin_request'],
      'country'         => $geo['geoplugin_countryName'],
      'country_code'    => $geo['geoplugin_countryCode'],
      'city'            => $geo['geoplugin_city'],
      'region'          => $geo['geoplugin_regionName'],
      'timezone'        => $geo['geoplugin_timezone'],
      'currency'        => $geo['geoplugin_currencyCode'],
      'exchange'        => $geo['geoplugin_currencyConverter'], // 1 USD = this
      'currency_symbol' => $geo['geoplugin_currencySymbol'],
    );

    return $key=='' ? $clientInfo : $clientInfo[$key];
  }



  /**
   * Minify HTML
   * @param HTML $html
   */
  public static function minifyHTML($html) {
    $find = array('/(\n|^)(\x20+|\t)/', '/(\n|^)\/\/(.*?)(\n|$)/', '/\n/', '/\<\!--.*?-->/', '/(\x20+|\t)/', '/\>\s+\</', '/(\"|\')\s+\>/', '/=\s+(\"|\')/');
    $replace = array("\n", "\n", " ", "", " ", "><", "$1>", "=$1");
    $html = preg_replace($find,$replace,$html);

    return $html;
  }



    /**
   * Minify CSS
   * @param CSS $css
   */
  public static function minifyCSS($css) {
    $find = array('/\s*(\w)\s*{\s*/','/\s*(\S*:)(\s*)([^;]*)(\s|\n)*;(\n|\s)*/','/\n/','/\s*}\s*/');
    $replace = array('$1{ ','$1$3;',"",'} ');
    $css = preg_replace($find,$replace,$css);

    return $css;
  }



  /**
   * Set HTTP status code
   */
  public static function statusCode($code){
    return http_response_code($code);
  }



  /**
   * Ozz built-in dumper
   * @param array|string|object|int|bool $data
   * @param string $label
   * @param bool $return
   */
  public static function dump($data, $label='', $return = false){
    if(DEBUG){
      return SubHelp::varDump($data, $label, $return);
    }
  }



} // Helper END