<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 */

namespace Ozz\Core;

class Validate {

  public static $validate;
  private static $submittedData;
  public static $validationErrors = [];
  

  private static function setValidationValues(){
    foreach (Validator::FormFields() as $key => $value) {
      $inner = [];
      foreach ($value as $k => $v) {
        $v = preg_match_all("/([^,= ]+):([^,= ]+)/", $v, $r) ? array_combine($r[1], $r[2]) : $v;
        $inner[] = $v;
      }
      self::$validate[$key] = $inner;
    }
  }



  # ------------------------------------------
  // Validate input values
  # ------------------------------------------
  public static function validateForm($data){
    self::setValidationValues();
    self::$submittedData = $data;

    foreach (self::$submittedData as $k => $v) {
      array_key_exists($k, self::$validate) ? $validateThis = $k : false;
      foreach (self::$validate[$validateThis] as $kk => $vv) {
        if(is_array($vv)){
          foreach ($vv as $ky => $vl) {
            switch ($ky) {
              case 'max':
                self::$validationErrors[$k]['maxLength'] = self::maxLength($v, $vl);
                break;

              case 'min':
                self::$validationErrors[$k]['minLength'] = self::minLength($v, $vl);
                break;

              case 'match':
                self::$validationErrors[$k]['match'] = self::matchPass($v, $vl);
                break;
            }
          }
        }
        else{
          switch ($vv) {
            case 'required':
            case 'req':
              self::$validationErrors[$k]['required'] = self::required($v);
              break;
            
            case 'string':
            case 'txt':
            case 'text':
              self::$validationErrors[$k]['string'] = self::string($v);
              break;

            case 'email':
            case 'mail':
              self::$validationErrors[$k]['email'] = self::email($v);
              break;

            case 'float':
              self::$validationErrors[$k]['float'] = self::float($v);
              break;

            case 'number':
            case 'int':
              self::$validationErrors[$k]['number'] = self::number($v);
              break;
          
          }
          self::$validationErrors[$k]['value'] = $v;
        }
      }
    }
    return self::$validationErrors;
  }



  # ------------------------------------------
  // Validation Methods
  # ------------------------------------------
  private static function required($v){
    return empty($v) ? false : true;
  }

  private static function string($v){
    return !empty($v) ? is_string($v) : false;
  }

  private static function email($v){
    return (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $v)) ? false : true;
  }

  private static function float($v){
    return preg_match('/([0-9]{1,})\.([0-9]{2,2})/', $v) == 0 ? false : true;
  }

  private static function number($v){
    return is_numeric($v);
  }



  # ------------------------------------------
  // key : value option
  # ------------------------------------------
  private static function maxLength($v, $lnth){
      return strlen($v) <= $lnth ? true : false;
  }
  
  private static function minLength($v, $lnth){
      return strlen($v) >= $lnth ? true : false;
  }

  private static function matchPass($v, $mtch){
      return $v == self::$submittedData[$mtch] ? true : false;
  }

}