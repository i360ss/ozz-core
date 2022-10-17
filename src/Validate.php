<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 */

namespace Ozz\Core;

use Ozz\Core\Language;

class Validate {

  /**
   * Validate multiple items with rules
   * @param array|string $input The values to be validated
   * @param array|string $checkup $input[key] as key and rules as value (Rules separated by "|" )
   */
  public static function check($input, $checkup){
    $validity = [];
    $validatedData = [];
    if(is_array($input) && is_array($checkup)){
      foreach ($checkup as $ky => $val) {
        if(explode('|', $val) > 0){
          $rules = explode('|', $val);
          foreach ($rules as $rule) {
            $validity[] = self::checkRule($input[$ky], $rule, $ky); // If multiple rules provided
            $validatedData[$ky] = $input[$ky];
          }
        } else {
          $validity[] = self::checkRule($input[$ky], $val, $ky); // If only one rule provided per one key
          $validatedData[$ky] = $input[$ky];
        }
      }
    } elseif(is_array($input) && is_string($checkup)){
      $rules = (explode('|', $checkup) > 0) ? explode('|', $checkup) : $checkup;
      foreach ($input as $ky => $val) {
        if(is_array($rules)){
          foreach ($rules as $rule) {
            $validity[] = self::checkRule($val, $rule, $ky); // Multiple rule multiple values
            $validatedData[$ky] = $val;
          }
        } else {
          $validity[] = self::checkRule($val, $rules, $ky); // One rule multiple values
          $validatedData[$ky] = $val;
        }
      }
    } elseif(is_string($input) && is_string($checkup)){
      $rules = (explode('|', $checkup) > 0) ? explode('|', $checkup) : $checkup;
      if(is_array($rules)){
        foreach ($rules as $rule) {
          $validity[] = self::checkRule($input, $rule);
          $validatedData[count($validatedData)+1] = $input;
        }
      } else {
        $validity[] = self::checkRule($input, $rules);
        $validatedData[count($validatedData)+1] = $input;
      }
    } else {
      return DEBUG 
        ? Err::custom([
          'msg' => 'Invalid parameters provided to [Validate::check()]',
          'info' => 'Valid arguments:<br><br> Validate::check(array, array)<br>Validate::check(array, string)<br>Validate::check(string, string)
          <br><span style="color: var(--ozz-error);">Validate::check(string/boolean, array) // Wrong attempt</span>',
        ])
        : false;
    }

    return (object) [
      'pass' => in_array(false, $validity) ? false : true,
      'errors' => get_error(),
      'data' => $validatedData,
    ];
  }



  /**
   * Check each rule
   * @param string $val The value to be checked
   * @param string $rule Single rule
   */
  private static function checkRule($val, $rule, $valueKey=null) {
    $valueKey = ($valueKey == null && has_error()) ? count(get_error())+1 : $valueKey;
    $rule = preg_match_all("/([^,= ]+):([^,= ]+)/", $rule, $r) ? array_combine($r[1], $r[2]) : $rule;
    if(is_array($rule)){
      foreach ($rule as $k => $v) {
        switch ($k) {
          case 'max':
            return self::maxLength($val, $v, $valueKey);
            break;

          case 'min':
            return self::minLength($val, $v, $valueKey);
            break;

          case 'match':
            return self::matchValues($val, $v, $valueKey);
            break;

          default:
            return self::invalidRule($rule);
            break;
        }
      }
    } else {
      switch ($rule) {
        case 'boolean':
        case 'bool':
          return self::boolean($val, $valueKey);
          break;

        case 'required':
        case 'req':
          return self::required($val, $valueKey);
          break;

        case 'txt':
        case 'text':
        case 'letters':
        case 'letter':
          return self::text($val, $valueKey);
          break;

        case 'email':
        case 'mail':
          return self::email($val, $valueKey);
          break;

        case 'number':
        case 'numbers':
        case 'numeric':
          return self::number($val, $valueKey);
          break;

        case 'string':
          return self::string($val, $valueKey);
          break;

        case 'float':
        case 'double':
          return self::float($val, $valueKey);
          break;

        case 'int':
        case 'integer':
          return self::int($val, $valueKey);
          break;

        default:
          return self::invalidRule($rule);
          break;
      }
    }
  }



  /**
   * Invalid rule provided
   * @param string $rule
   */
  private static function invalidRule($rule){
    return DEBUG 
      ? Err::custom([
        'msg' => "Invalid validation rule provided [$rule]",
      ])
      : false;
  }



  /**
   * Validation Methods
   */
  public static function boolean($v, $key=''){
    return self::response(($v === true || $v === false), $key, 'Not a boolean value (It should be either true or false)');
  }

  public static function required($v, $key=''){
    return self::response((empty($v) || $v == ''), $key, 'Required!');
  }

  public static function text($v, $key=''){
    return self::response(($v !== '' && preg_match ("/^[a-zA-z]*$/", $v)), $key, 'Invalid text');
  }

  public static function email($v, $key=''){
    return self::response((filter_var($v, FILTER_VALIDATE_EMAIL) && preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $v)), $key, 'Invalid Email address');
  }

  public static function number($v, $key=''){
    return self::response(is_numeric($v), $key, 'Invalid number or numeric string');
  }

  public static function url($v, $key=''){
    return self::response(preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $v), $key, 'Invalid URL');
  }

  public static function string($v, $key=''){
    return self::response(is_string($v), $key, 'Invalid string');
  }

  public static function float($v, $key=''){
    return self::response(is_float($v), $key, 'Invalid float');
  }

  public static function int($v, $key=''){
    return self::response(is_int($v), $key, 'Invalid integer');
  }



  /**
   * key:value options
   */
  public static function maxLength($v, $ln, $key=''){
    return self::response((strlen($v) <= $ln), $key, 'Exceeded maximum characters');
  }
  
  public static function minLength($v, $ln, $key=''){
    return self::response((strlen($v) >= $ln), $key, 'Minimum character length is '.$ln);
  }

  public static function matchValues($v, $match, $key=''){
    return self::response($v === $match, $key, 'Values not matching');
  }



  /**
   * Internal Responser
   */
  private static function response($bool, $key, $errMsg) {
    if($bool === false){
      set_error($key, $errMsg);
    }
    return $bool;
  }

}