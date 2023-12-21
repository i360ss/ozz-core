<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Session;
use Ozz\Core\Err;

class Errors {

  /**
   * Return all current errors
   */
  public static function all() {
    return (isset($_SESSION['__error']) && !empty($_SESSION['__error'])) 
      ? $_SESSION['__error'] 
      : false;
  }

  /**
   * Check Error key exist
   * @param string $key Error key to check if exist
   */
  public static function has(string $key='') {
    if ( Session::has('__error') ) {
      $all = Session::get('__error');
      if ($key == '') {
        return is_array($all) && count($all) > 0 ? true : false;
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
    if ( Session::has('__error') ) {
      $all = Session::get('__error');
      if ($key !== '') {
        return isset($all[$key]) ? $all[$key] : '';
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
    Session::flash($key, $value, true);
  }

  /**
   * Remove Error
   * @param string $key error key to remove
   * @param bool Optional (Return new errors if this is true)
   * @return bool|array
   */
  public static function remove(string $key, $return=false) {
    if ( Session::has('__error') ) {
      $all = Session::get('__error');
      unset($all[$key]);
      Session::set('__error', $all);
      return $return ? $all : true;
    } else {
      return false;
    }
  }

  /**
   * Clear All Errors
   */
  public static function clear() {
    if (Session::has('__error')) {
      Session::remove('__error');
    }
  }

}