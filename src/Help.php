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