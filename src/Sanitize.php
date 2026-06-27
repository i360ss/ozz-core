<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Sanitize {

  public static function string(string $value): string {
    return trim($value);
  }

  public static function number(int|float|string $value): int|float|null {
    $value = trim((string) $value);
    return is_numeric($value) ? $value + 0 : null;
  }

  public static function phone(string $value): string{
    $value = trim($value);
    if (str_starts_with($value, '+')) {
      return '+' . preg_replace('/\D/', '', substr($value, 1));
    }
    return preg_replace('/\D/', '', $value);
  }

  public static function htmlEncode(string $value): string {
    return htmlspecialchars( $value, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8', false );
  }

  public static function htmlDecode(string $value): string {
    return htmlspecialchars_decode( $value, ENT_QUOTES );
  }

  public static function sanitizeEach($value, ?string $type = null) {
    $value = is_scalar($value) ? (string)$value : $value;
    return match ($type) {
      'string' => self::string($value),
      'number' => self::number($value),
      'phone' => self::phone($value),
      'htmlEncode' => self::htmlEncode($value),
      'htmlDecode' => self::htmlDecode($value),
      default => self::string($value),
    };
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
  public static function array(array $arr, ?string $sanType = null): array {
    foreach ($arr as $k => $v) {
      (is_array($v)) ? $arr[$k] = self::array($v) : $arr[$k] = self::sanitizeEach($v, $sanType);
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
    if(is_callable($v, true) || is_object($v)){
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