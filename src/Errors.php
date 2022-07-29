<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Session;

class Errors {
  
  public $all = [];

  
  /**
   * Return all current errors
   */
  public static function all() {
    return Session::has('errors') ? Session::get('errors') : false;
  }



  /**
   * Check Error key exist
   * @param string $key Error key to check if exist
   */
  public static function has($key='') {
    if ( Session::has('errors') ) {
      $all = Session::get('errors');
      if ($key == '') {
        return count($all) > 0 ? true : false;
      } elseif (empty($all) || !isset($all)) {
        return false;
      } else {
        return array_key_exists($key, $all);
      }
    }
  }



  /**
   * Get One Error by key
   * @param string $key Key of the required error
   */
  public static function get(string $key='') {
    if ( Session::has('errors') ) {
      $all = Session::get('errors');
      if ($key !== '') {
        if (array_key_exists($key, $all)) {
          return $all[$key];
        } else {
          if (DEBUG) {
            return ERR::invalidArrayKey($key);
          }
        }
      } else {
        return $all;
      }
    }
  }



  /**
   * Set New Error with key value
   * @param string $key key of the Error
   * @param string|array|object|bool $value the error message and/or any additional info
   */
  public static function set(string $key, $value) : void {
    if (Session::has('errors')) {
      $current_errs = Session::get('errors');
      $current_errs[$key] = $value;
      Session::set('errors', $current_errs);
    } else {
      Session::set('errors', [ $key => $value ]);
    }
  }



  /**
   * Remove Error
   * @param string $key error key to remove
   * @param bool Optional (Return new errors if this is true)
   * @return bool|array
   */
  public static function remove($key, $return=false) {
    if (Session::has('errors')) {
      $all = Session::get('errors');
      unset($all[$key]);
      Session::set('errors', $all);
      return $return ? $all : true;
    }
  }



  /**
   * Clear All Errors
   */
  public static function clear() {
    if (Session::has('errors')) {
      Session::remove('errors');
    }
  }
 
}