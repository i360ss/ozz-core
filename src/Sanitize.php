<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\core;

class Sanitize {
  
  public static function string($i){
    return filter_var($i, FILTER_SANITIZE_STRING);
  }
  
  public static function email($i){
    return filter_var($i, FILTER_SANITIZE_EMAIL);
  }
  
  public static function number($i){
    return filter_var($i, FILTER_SANITIZE_NUMBER_INT);
  }
  
  public static function phone($i){
    return filter_var($i, FILTER_SANITIZE_NUMBER_FLOAT);
  }
  
  public static function htmlEncode($i, $flag=null){
    return htmlspecialchars($i, $flag);
  }
  
  public static function htmlDecode($i, $flag=null){
    return htmlspecialchars_decode($i, $flag);
  }
  
  public static function encoded($i){
    return filter_var($i, FILTER_SANITIZE_ENCODED);
  }
  
  public static function specialChar($i){
    return filter_var($i, FILTER_SANITIZE_SPECIAL_CHARS);
  }
  
  public static function specialCharFull($i){
    return filter_var($i, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  }
  
  public static function url($i){
    return filter_var($i, FILTER_SANITIZE_URL);
  }
  
  public static function array($arr){
    foreach ($arr as $k => $v) {
      (is_array($v)) ? $arr[$k] = self::array($v) : $arr[$k] = filter_var($v, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    return $arr;
  }

  public static function tempContext($arr){
    foreach ($arr as $k => $v) {
      (is_array($v)) ? $arr[$k] = self::tempContext($v) : $arr[$k] = self::regExps($v);
    }
    return $arr;
  }

  // Clear template to prevent template injection
  public static function regExps($v){
    preg_match_all("~\{\{\s*(.*?)\s*\}\}~", $v, $clr['block_view']);
    preg_match_all("~\{\%\s*(.*?)\s*\%\}~", $v, $clr['block_base']);
    preg_match_all("~\{\:\s*(.*?)\s*\:\}~", $v, $clr['block_comp']);

    foreach ($clr as $typ) {
      foreach ($typ[1] as $vl) {
        $v = trim($vl);
        $v = str_replace("{% $vl %}", $vl, $v);
        $v = str_replace("{{ $vl }}", $vl, $v);
        $v = str_replace("{: $vl :}", $vl, $v);
      }
    }
    return $v;
  }
  
}