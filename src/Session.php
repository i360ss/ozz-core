<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 */

namespace Ozz\Core;

class Session {

  /**
   * Set New session value
   * @param string $k Key of the session
   * @param string|array $v session value to store
   * @param bool $force overwrite existing value (default: true)
   */
  public static function set($k, $v, $force=true) {
    if ($force) {
      $_SESSION[$k] = $v;
    } else {
      !array_key_exists($k, $_SESSION) ? $_SESSION[$k] = $v : false;
    }
  }



  /**
   * Get Session Value by key
   * Get all if key not provided
   * @param string $k session key
   */
  public static function get($k=null) {
    if (isset($k)) {
      if (array_key_exists($k, $_SESSION)) {
        return $_SESSION[$k];
      } else {
        return DEBUG 
        ? Err::custom([
          'msg' => 'Invalid Array key provided to [ Session::get() ] method',
          'info' => '[ '.$k.' ] is not available in current session',
          'note' => 'You can check all available session values by dump(Session::get()) without any parameters',
        ])
        : false;
      }
      
    } else {
      return $_SESSION;
    }
  }



  /**
   * Unset/Remove session by key
   * @param string|array $k session key/keys
   */
  public static function remove($k=null) {
    if (isset($k)) {
      if (is_array($k)) {
        foreach ($k as $key) {
          if (array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
          }
        }
      }
      elseif (isset($_SESSION[$k])) {
        if (array_key_exists($k, $_SESSION)) {
          unset($_SESSION[$k]);
        }
      } else {
        return DEBUG 
        ? Err::custom([
          'msg' => 'Invalid Array key provided to [ Session::remove() ] method',
          'info' => '[ '.$k.' ] is not available in current session',
          'note' => 'You can check all available session values by dump(Session::get())',
        ])
        : false;
      }
    } else {
      return DEBUG 
        ? Err::custom([
          'msg' => 'Key not provided for <strong>Session::remove()</strong>',
          'info' => '[ Session::remove() ] method required a valid key parameter to remove the value',
          'note' => 'If you want to clear all sessions you can use <strong>Session::clear()</strong>',
        ])
        : false;
    }
  }



  /**
   * Check if session exist
   * @param string $k session key
   * @return bool
   */
  public static function has($k=null) {
    if (isset($k)) {
      return array_key_exists($k, $_SESSION) ? true : false;
    } else {
      return DEBUG 
        ? Err::custom([
          'msg' => 'Key not provided for <strong>Session::has()</strong>',
          'info' => '[ Session::has() ] method required a valid key parameter to check the value existance',
          'note' => 'Try dump( Session::get() ) to see all available session keys and values',
        ])
        : false;
    }
  }



  /**
   * Clear Session variable
   */
  public static function clear() {
    $_SESSION = [];
  }



  /**
   * Destroy Session variable
   */
  public static function destroy() {
    $_SESSION = [];
    session_destroy();
  }

}