<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Sanitize {

  public static function string($i){
    return htmlspecialchars($i, ENT_QUOTES);
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

  public static function htmlEncode($i, $flag=false){
    return $flag ? htmlspecialchars($i, $flag) : htmlspecialchars($i);
  }

  public static function htmlDecode($i, $flag=false){
    return $flag ? htmlspecialchars_decode($i, $flag) : htmlspecialchars_decode($i);
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
    $i = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $i);
    return filter_var($i, FILTER_SANITIZE_URL);
  }

  public static function sanitize_each($v, $typ){
    switch ($typ) {
      case 'url':
        return self::url($v);
        break;

      case 'string':
        return self::string($v);
        break;

      case 'email':
        return self::email($v);
        break;

      case 'number':
        return self::number($v);
        break;

      case 'phone':
        return self::phone($v);
        break;

      case 'htmlEncode':
        return self::htmlEncode($v);
        break;

      case 'htmlDecode':
        return self::htmlDecode($v);
        break;

      case 'encoded':
        return self::encoded($v);
        break;

      case 'specialCharFull':
        return self::specialCharFull($v);
        break;

      default:
        return self::specialChar($v);
        break;
    }
  }

  /**
   * Sanitize SVG (Clean up all bad events and elements from SVG)
   * @param string $svg
   * @param array $allowed_elements
   */
  public static function svg($svg, $allowed_elements = []) {
    // Default list of elements to remove
    $defaultElementsToRemove = array(
      'iframe', 'embed', 'object', 'applet', 'meta', 'link', 'style', 'form', 'input', 'select', 'textarea', 'button',
      'script', 'noscript', 'template', 'frameset', 'frame', 'noframes', 'blink', 'marquee', 'base', 'head', 'html',
      'body', 'frameset', 'frame', 'noframes', 'applet',
    );

    // Remove harmful elements
    $elementsToRemove = array_diff($defaultElementsToRemove, $allowed_elements);
    $pattern = '/<(' . implode('|', $elementsToRemove) . ')\b[^>]*>.*?(<\/\1>|\/>|$)/is';
    $svg = preg_replace($pattern, '', $svg);

    // Remove event attributes
    $svg = preg_replace('/\s+on\w+="[^"]*"/i', '', $svg);

    return $svg;
  }

  /**
   * @param array $arr The array to be sanitized
   * @param string $sanType Sanitization method for each array item
   */
  public static function array($arr, $sanType=false){
    foreach ($arr as $k => $v) {
      (is_array($v)) ? $arr[$k] = self::array($v) : $arr[$k] = self::sanitize_each($v, $sanType);
    }
    return $arr;
  }

  /**
   * Used to prevent template injection
   * @param array $arr
   */
  public static function templateContext($arr){
    if (is_array($arr) || is_object($arr)) {
      foreach ($arr as $k => $v) {
        (is_array($v)) ? $arr[$k] = self::templateContext($v) : $arr[$k] = self::regExps($v);
      }
    } else {
      $arr = self::regExps($arr);
    }

    return $arr;
  }

  /**
   * Clear template to prevent template injection
   */
  public static function regExps($v){
    if(is_callable($v, true)){
      return $v;
    }

    if(!is_null($v)){
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
    }

    return $v;
  }

}