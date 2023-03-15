<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 */

namespace Ozz\Core;

use Ozz\Core\Lang;
use Ozz\Core\Errors;

class Validate {

  use \Ozz\Core\system\file\FileValidation;

  private static $lang;
  private static $current_input;

  /**
   * Validate multiple items with rules
   * @param array|string $input The values to be validated
   * @param array|string $checkup $input[key] as key and rules as value (Rules separated by "|" )
   */
  public static function check($input, $checkup){
    self::$lang = new Lang;
    self::$current_input = $input;
    $validity = [];
    $validatedData = [];
    if(is_array($input) && is_array($checkup)){
      foreach ($checkup as $ky => $val) {
        if(explode('|', $val) > 0){
          $rules = array_reverse(array_map('trim', explode('|', $val)));
          foreach ($rules as $rule) {
            if(isset($input[$ky])){
              $validity[] = self::checkRule($input[$ky], $rule, $ky); // If multiple rules provided
              $validatedData[$ky] = isset($input[$ky]) ? $input[$ky] : false;
            }
          }
        } else {
          $validity[] = self::checkRule($input[$ky], $val, $ky); // If only one rule provided per one key
          $validatedData[$ky] = $input[$ky];
        }
      }
    } elseif(is_array($input) && is_string($checkup)){
      $rules = (explode('|', $checkup) > 0) ? array_reverse(explode('|', $checkup)) : $checkup;
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
      $rules = (explode('|', $checkup) > 0) ? array_reverse(explode('|', $checkup)) : $checkup;
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
      'errors' => Errors::get(),
      'data' => $validatedData,
    ];
  }

  /**
   * Check each rule
   * @param string $val The value to be checked
   * @param string $rule Single rule
   */
  private static function checkRule($val, $rule, $valueKey=null) {
    $valueKey = ($valueKey == null && Errors::has()) ? count(Errors::get())+1 : $valueKey;
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
            if($v[0] == '{' && $v[-1] == '}'){
              $matchKey = substr($v, 1, -1);
              $v2 = self::$current_input[$matchKey];
            } else {
              $matchKey = false;
              $v2 = $v;
            }
            return self::matchValues($val, $v2, $valueKey, $matchKey);
            break;

          case 'strongPassword':
          case 'strong_password':
          case 'strongPass':
          case 'strong_pass':
            return self::password($val, true, $valueKey, $v);
            break;

          case 'format':
          case 'formats':
          case 'max_size':
          case 'min_size':
          case 'max_files':
          case 'max_res':
          case 'min_res':
            return self::file($val, $k, $v, $valueKey);
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

        case 'alphanum':
        case 'alphaNum':
        case 'alpha_num':  
        case 'alphanumeric':
        case 'alphaNumeric':
          return self::alphaNum($val, $valueKey);
          break;

        case 'clean':
        case 'safe':
        case 'safe_text':
          return self::safeText($val, $valueKey);
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

        case 'num':
        case 'number':
        case 'numbers':
        case 'numeric':
          return self::number($val, $valueKey);
          break;

        case 'string':
          return self::string($val, $valueKey);
          break;

        case 'url':
        case 'URL':
          return self::url($val, $valueKey);
          break;

        case 'float':
        case 'double':
          return self::float($val, $valueKey);
          break;

        case 'int':
        case 'integer':
          return self::int($val, $valueKey);
          break;

        case 'password':
        case 'pass':
          return self::password($val, false, $valueKey);
          break;

        case 'strongPassword':
        case 'strong_password':
        case 'strongPass':
        case 'strong_pass':
          return self::password($val, true, $valueKey);
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
  public static function required($v, $key=''){
    // If File
    if(is_array($v) && isset($v['name'])){
      return self::response($v['name'] !== '', $key, self::$lang->error('file_required', ['field' => $key, 'value' => 'file']));
    }
    return self::response(!empty($v), $key, self::$lang->error('required', ['field' => $key, 'value' => $v]));
  }

  public static function boolean($v, $key=''){
    return $v!==''
      ? self::response(($v === true || $v === false), $key, self::$lang->error('boolean', ['field' => $key, 'value' => $v]))
      : true;
  }

  public static function alphaNum($v, $key=''){
    return $v!==''
      ? self::response((preg_match ("/[^0-9a-zA-Z]/", $v)), $key, self::$lang->error('alpha_num', ['field' => $key, 'value' => $v]))
      : true;
  }

  public static function safeText($v, $key=''){
    return $v!==''
      ? self::response((!preg_match ("/<[^<]+>/", $v)), $key, self::$lang->error('safe_text', ['field' => $key, 'value' => $v]))
      : true;
  }

  public static function text($v, $key=''){
    return $v!==''
      ? self::response((preg_match ("/^[a-zA-z]*$/", $v)), $key, self::$lang->error('text', ['field' => $key, 'value' => $v]))
      : true;
  }

  public static function email($v, $key=''){
    return $v!==''
      ? self::response((filter_var($v, FILTER_VALIDATE_EMAIL) && preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $v)), $key, self::$lang->error('email', ['field' => $key, 'value' => $v]))
      : true;
  }

  public static function phone($v, $key=''){
    return $v!==''
      ? self::response(preg_match('/^[0-9]{10}+$/', $v), $key, self::$lang->error('phone', ['field' => $key, 'value' => $v]))
      : true;
    
  }

  public static function number($v, $key=''){
    return $v!==''
      ? self::response(is_numeric($v), $key, self::$lang->error('number', ['field' => $key, 'value' => $v]))
      : true;
  }

  public static function url($v, $key=''){
    return $v!==''
      ? self::response(filter_var($v, FILTER_VALIDATE_URL), $key, self::$lang->error('url', ['field' => $key, 'value' => $v]))
      : true;
  }

  public static function string($v, $key=''){
    return $v!==''
      ? self::response(is_string($v), $key, self::$lang->error('string', ['field' => $key, 'value' => $v]))
      : true;
  }

  public static function float($v, $key=''){
    return $v!==''
      ? self::response(is_float($v), $key, self::$lang->error('float', ['field' => $key, 'value' => $v]))
      : true;
  }

  public static function int($v, $key=''){
    return $v!==''
      ? self::response(is_int($v), $key, self::$lang->error('integer', ['field' => $key, 'value' => $v]))
      : true;
  }

  public static function password($v, $type, $key='', $minLength=6){
    if($type === true){
      // Strong Password (Check all)
      $password_errors = [];
      if($v == trim($v) && strpos($v, ' ')){
        $password_errors[] = self::$lang->error('normal_password', ['field' => $key, 'value' => $v]);
      }
      if(!preg_match('@[0-9]@', $v)){
        $password_errors[] = self::$lang->error('strong_password_numbers', ['field' => $key, 'value' => $v]);
      }
      if(!preg_match('@[A-Z]@', $v)){
        $password_errors[] = self::$lang->error('strong_password_uppercase', ['field' => $key, 'value' => $v]);
      }
      if(!preg_match('@[a-z]@', $v)){
        $password_errors[] = self::$lang->error('strong_password_lowercase', ['field' => $key, 'value' => $v]);
      }
      if(!preg_match('@[^\w]@', $v)){
        $password_errors[] = self::$lang->error('strong_password_special_character', ['field' => $key, 'value' => $v]);
      }
      if(strlen($v) < $minLength){
        $password_errors[] = self::$lang->error('strong_password_character_length', ['field' => $key, 'value' => $v, 'min' => $minLength]);
      }
      // Log all password errors
      if(count($password_errors) > 0){
        return self::response(false, $key, $password_errors);
      } else {
        return self::response(true, $key, $password_errors);
      }

    } else {
      // Normal Password (Only check for spaces)
      return self::response(($v == trim($v) && strpos($v, ' ') == false), $key, self::$lang->error('normal_password', ['field' => $key, 'value' => $v]));
    }
  }

  /**
   * key:value options
   */
  public static function maxLength($v, $ln, $key=''){
    return self::response((strlen($v) <= $ln), $key, self::$lang->error('max', ['field' => $key, 'value' => $v, 'max' => $ln]));
  }

  public static function minLength($v, $ln, $key=''){
    return self::response((strlen($v) >= $ln), $key, self::$lang->error('min', ['field' => $key, 'value' => $v, 'min' => $ln]));
  }

  public static function matchValues($v, $match, $key='', $matchKey=false){
    return self::response($v === $match, $key, self::$lang->error('match', ['field' => $key, 'value' => $v, 'key' => $matchKey, 'match' => $match]));
  }

  /**
   * File Validation
   * @param array $file The file to be validated
   * @param string $rule Each (file Validation rule)
   * @param string $v Rule argument
   * @param string $input key
   */
  public static function file($file, $rule, $v, $key){
    if(isset($file['tmp_name'])) {
      // If Multiple Files
      // ===================================
      if(isset($file['name']) && is_array($file['name'])){
        // Maximum files per upload
        if ($rule == 'max_files'){
          return self::response(
            (count($file['name']) <= $v), $key, self::$lang->error('file_max_count', ['field' => $key, 'value' => 'file'])
          );
        }

        $multi_file_errors = [];

        foreach ($file['tmp_name'] as $ky => $val) {
          $tmp = $file['tmp_name'][$ky];
          $name = $file['name'][$ky];
          $size = $file['size'][$ky];
          $type = $file['type'][$ky];
          $error = $file['error'][$ky];

          if ($rule == 'min_res'){
            if(self::isImage($file, $ky)){
              list($origWidth, $origHeight, $type) = getimagesize($tmp);
              list($max_width, $max_height) = explode('x', strtolower($v));
              settype($origWidth, 'integer');
              settype($origHeight, 'integer');
              settype($max_width, 'integer');
              settype($max_height, 'integer');
              $state = ($origWidth >= $max_width && $origHeight >= $max_height);
              if($state === false){
                $multi_file_errors[$ky] = self::$lang->error('image_low_res', ['field' => $key, 'value' => 'file']);
              }
            }
          }

          if ($rule == 'max_res'){
            if(self::isImage($file)){
              list($origWidth, $origHeight, $type) = getimagesize($tmp);
              list($max_width, $max_height) = explode('x', strtolower($v));
              settype($origWidth, 'integer');
              settype($origHeight, 'integer');
              settype($max_width, 'integer');
              settype($max_height, 'integer');
              $state = ($origWidth <= $max_width && $origHeight <= $max_height);
              if($state === false){
                $multi_file_errors[$ky] = self::$lang->error('image_high_res', ['field' => $key, 'value' => 'file']);
              }
            }
          }

          // File Min Size (MB/KB)
          if ($rule == 'min_size'){
            if(!self::validateFileSize($size, $v, 'min')){
              $multi_file_errors[$ky] = self::$lang->error('file_too_small', ['field' => $key, 'value' => 'file']);
            }
          }

          // File Max Size (MB/KB)
          if ($rule == 'max_size'){
            if(!self::validateFileSize($size, $v, 'max')){
              $multi_file_errors[$ky] = self::$lang->error('file_too_large', ['field' => $key, 'value' => 'file']);
            }
          }

          // File Format/MIME type
          if($rule == 'formats' || $rule == 'format'){
            if(self::validateFileFormat($file, $v, $ky) === false){
              $multi_file_errors[$ky] = self::$lang->error('file_invalid_format', ['field' => $key, 'value' => 'file']);
            }
          }

          return self::response(
            empty($multi_file_errors), $key, $multi_file_errors
          );
        }
      } else {
        // If Single File 
        // ===================================
        // Image Min Resolution
        if ($rule == 'min_res'){
          if(self::isImage($file)){
            list($origWidth, $origHeight, $type) = getimagesize($file['tmp_name']);
            list($max_width, $max_height) = explode('x', strtolower($v));
            settype($origWidth, 'integer');
            settype($origHeight, 'integer');
            settype($max_width, 'integer');
            settype($max_height, 'integer');
            $state = ($origWidth >= $max_width && $origHeight >= $max_height);

            return self::response(
              $state, $key, self::$lang->error('image_low_res', ['field' => $key, 'value' => 'file'])
            );
          }
        }

        // Image Max Resolution
        if ($rule == 'max_res'){
          if(self::isImage($file)){
            list($origWidth, $origHeight, $type) = getimagesize($file['tmp_name']);
            list($max_width, $max_height) = explode('x', strtolower($v));
            settype($origWidth, 'integer');
            settype($origHeight, 'integer');
            settype($max_width, 'integer');
            settype($max_height, 'integer');
            $state = ($origWidth <= $max_width && $origHeight <= $max_height);

            return self::response(
              $state, $key, self::$lang->error('image_high_res', ['field' => $key, 'value' => 'file'])
            );
          }
        }

        // File Min Size (MB/KB)
        if ($rule == 'min_size'){
          return self::response(
            self::validateFileSize($file['size'], $v, 'min'), $key, self::$lang->error('file_too_small', ['field' => $key, 'value' => 'file'])
          );
        }

        // File Max Size (MB/KB)
        if ($rule == 'max_size'){
          return self::response(
            self::validateFileSize($file['size'], $v, 'max'), $key, self::$lang->error('file_too_large', ['field' => $key, 'value' => 'file'])
          );
        }

        // File Format/MIME type
        if($rule == 'formats' || $rule == 'format'){
          return self::response(
            self::validateFileFormat($file, $v), $key, self::$lang->error('file_invalid_format', ['field' => $key, 'value' => 'file'])
          );
        }
      }
    } else {
      Err::custom([
        'msg' => "Invalid Validation rule provided for [ $key ]",
        'info' => "Rule: [ $rule ], Field: [ $key ]",
        'note' => 'Above rule will work only for file input type',
      ]);
    }
  }

  /**
   * Internal Responser
   */
  private static function response($bool, $key, $errMsg) {
    if($bool === false){
      Errors::set($key, $errMsg);
    }
    return $bool;
  }

}