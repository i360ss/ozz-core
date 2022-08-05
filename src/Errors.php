<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Session;

class Errors {
  
  /**
   * Return all current errors
   */
  public static function all() {
    return (isset($_SESSION['ozz__flash']) && !empty($_SESSION['ozz__flash'])) 
      ? $_SESSION['ozz__flash'] 
      : false;
  }



  /**
   * Check Error key exist
   * @param string $key Error key to check if exist
   */
  public static function has(string $key='') {
    if ( Session::has('ozz__flash') ) {
      $all = Session::get('ozz__flash');
      if ($key == '') {
        return count($all) > 0 ? true : false;
      } elseif (empty($all) || !isset($all)) {
        return false;
      } else {
        return array_key_exists($key, $all);
      }
    } else {
      return false;
    }
  }



  /**
   * Get One Error by key
   * @param string $key Key of the required error
   */
  public static function get(string $key='') {
    if ( Session::has('ozz__flash') ) {
      $all = Session::get('ozz__flash');
      if ($key !== '') {
        if (array_key_exists($key, $all)) {
          return $all[$key];
        } else {
          if (DEBUG) {
            return ERR::invalidArrayKey($key);
          } else {
            return false;
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
    Session::flash($key, $value);
  }



  /**
   * Remove Error
   * @param string $key error key to remove
   * @param bool Optional (Return new errors if this is true)
   * @return bool|array
   */
  public static function remove(string $key, $return=false) {
    if ( Session::has('ozz__flash') ) {
      $all = Session::get('ozz__flash');
      unset($all[$key]);
      Session::set('ozz__flash', $all);
      return $return ? $all : true;
    } else {
      return false;
    }
  }



  /**
   * Clear All Errors
   */
  public static function clear() {
    if (Session::has('ozz__flash')) {
      Session::remove('ozz__flash');
    }
  }
  
}