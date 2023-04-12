<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 */

namespace Ozz\Core;

if(defined('OZZ_FUNC') === false){
  require 'system/ozz-func.php';
}

use Ozz\Core\system\session\FileBasedSessionHandler;

class Session {

  /**
   * Initialize Application Session
   */
  public static function init() {
    // Initialize session driver
    if(session_status() == PHP_SESSION_NONE){
      if(strtolower(CONFIG['SESSION_DRIVER']) == 'file'){
        // File based session
        $sessionHandler = new FileBasedSessionHandler(__DIR__.SPC_BACK['core'].CONFIG['SESSION_DIRECTORY'], CONFIG['SESSION_SECRET_KEY']);

        // Configure session options as needed
        session_set_save_handler(
          [$sessionHandler, 'open'],
          [$sessionHandler, 'close'],
          [$sessionHandler, 'read'],
          [$sessionHandler, 'write'],
          [$sessionHandler, 'destroy'],
          [$sessionHandler, 'gc']
        );
      }

      if(CONFIG['SESSION_COOKIE_NAME'] !== ''){
        session_name(CONFIG['SESSION_COOKIE_NAME']);
      }

      session_set_cookie_params(CONFIG['SESSION_LIFETIME'], CONFIG['SESSION_PATH'], CONFIG['SESSION_DOMAIN'], CONFIG['SESSION_SECURE_COOKIE'], CONFIG['SESSION_HTTP_ONLY']);
      session_start(); // Start session
    }

    if(!isset($_SESSION['SESSION_INIT_TIME'])){
      $_SESSION['SESSION_INIT_TIME'] = time();
    } elseif (time() - $_SESSION['SESSION_INIT_TIME'] > CONFIG['SESSION_LIFETIME']){
      session_regenerate_id();
      $_SESSION['SESSION_INIT_TIME'] = time();
    }
  }

  /**
   * Set New session value
   * @param string $k Key of the session
   * @param string|array $v session value to store
   * @param bool $force overwrite existing value (default: true)
   */
  public static function set($k, $v, $force=true) {
    if(!isset($_SESSION)){
      return false;
    }

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
    if(!isset($_SESSION)){
      return false;
    }

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
    if(!isset($_SESSION)){
      return false;
    }

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
   * Set Session only if not set already
   * @param string $k Key of the session
   * @param string|array|object|int|bool $v session value to store
   * @param bool $force overwrite existing value (default: true)
   */
  public static function setIfNot($k, $v, $force=true) {
    !self::has($k) ? self::set($k, $v, $force) : true;
  }

  /**
   * Check if session exist
   * @param string $k session key
   * @return bool
   */
  public static function has($k=null) {
    if(!isset($_SESSION)){
      return false;
    }

    if (isset($k)) {
      return array_key_exists($k, $_SESSION) ? true : false;
    } else {
      return DEBUG 
        ? Err::custom([
          'msg' => 'Key not provided for <strong>Session::has()</strong>',
          'info' => '[ Session::has() ] method required a valid key parameter to check the value existence',
          'note' => 'Try dump( Session::get() ) to see all available session keys and values',
        ])
        : false;
    }
  }

  /**
   * Session Flash
   * Store session for only one request and unset
   * @param string $k Session key
   * @param string|array|object|int $v Session value
   * @param bool $force overwrite existing value (default: true)
   */
  public static function flash(string $k, $v, $is_error=false) {
    if(!isset($_SESSION)){
      return false;
    }

    if($is_error){
      $_SESSION['__error'][$k] = $v;
    } else {
      $_SESSION['__flash'][$k] = $v;
    }
  }

  /**
   * Clear Session variable
   */
  public static function clear() {
    $_SESSION = false;
    session_unset();
  }

  /**
   * Destroy Session variable
   */
  public static function destroy() {
    $_SESSION = false;
    session_unset();
    session_destroy();
  }

}