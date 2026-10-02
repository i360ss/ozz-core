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

use Ozz\Core\system\session\SessionDriver;
use Ozz\Core\system\session\FileBasedSessionHandler;

class Session {
  // Is a session currently active?
  private static function active(): bool {
    return session_status() === PHP_SESSION_ACTIVE;
  }

  /**
   * Initialize Application Session
   */
  public static function init() {
    if(session_status() === PHP_SESSION_NONE){
      $driver = strtolower(CONFIG['SESSION_DRIVER']);
      if($driver === 'file'){
        // File based session (encrypted + locked)
        $session_path = BASE_DIR . ltrim(CONFIG['APP_PATHS']['session'], '/');
        $sessionHandler = new FileBasedSessionHandler(
          $session_path,
          self::resolveSessionKey($session_path)
        );

        // true = write and close the session on shutdown
        session_set_save_handler($sessionHandler, true);
      } else {
        // 'redis' or 'memcached' configure PHP's native handler;
        // anything else uses PHP's default
        SessionDriver::register($driver);
      }

      if(CONFIG['SESSION_COOKIE_NAME'] !== ''){
        session_name(CONFIG['SESSION_COOKIE_NAME']);
      }

      ini_set('session.use_strict_mode', '1');   // reject unknown session ids (anti fixation)
      ini_set('session.use_only_cookies', '1');  // never accept ids from URL
      ini_set('session.use_trans_sid', '0');     // never put ids in URLs

      // Make sure expired sessions actually get cleaned up
      ini_set('session.gc_maxlifetime', (string) CONFIG['SESSION_LIFETIME']);
      ini_set('session.gc_probability', '1');
      ini_set('session.gc_divisor', '100');

      // Apply cookie params with SameSite support
      session_set_cookie_params([
        'lifetime' => CONFIG['COOKIE_LIFETIME'],
        'path' => CONFIG['COOKIE_PATH'],
        'domain' => CONFIG['COOKIE_DOMAIN'],
        'secure' => CONFIG['COOKIE_SECURE'],
        'httponly' => CONFIG['COOKIE_HTTP_ONLY'],
        'samesite' => CONFIG['COOKIE_SAMESITE'],
      ]);

      session_start();
    }

    if(!isset($_SESSION['SESSION_INIT_TIME'])){
      $_SESSION['SESSION_INIT_TIME'] = time();
    } elseif (time() - $_SESSION['SESSION_INIT_TIME'] > CONFIG['SESSION_LIFETIME']){
      session_regenerate_id(true);
      Csrf::refreshToken();
      $_SESSION['SESSION_INIT_TIME'] = time();
    }
  }

  /**
   * Use the configured key, or auto-generate and persist one per install
   */
  private static function resolveSessionKey(string $dir): string {
    $key = CONFIG['SESSION_SECRET_KEY'] ?? '';
    if (strlen($key) >= 32) {
      return $key;
    }

    if (!is_dir($dir)) {
      @mkdir($dir, 0700, true);
    }

    $keyFile = rtrim($dir, '/\\') . '/.session_key';
    $fp = @fopen($keyFile, 'c+');
    if ($fp === false) {
      throw new \RuntimeException('Cannot read or create the session key file: ' . $keyFile);
    }

    // The lock stops two simultaneous first requests from generating different keys
    flock($fp, LOCK_EX);
    $stored = trim((string) stream_get_contents($fp));

    if (strlen($stored) < 32) {
      $stored = bin2hex(random_bytes(32));
      ftruncate($fp, 0);
      rewind($fp);
      fwrite($fp, $stored);
      fflush($fp);
      @chmod($keyFile, 0600);
    }

    flock($fp, LOCK_UN);
    fclose($fp);

    return $stored;
  }

  /**
   * Set New session value
   * @param string $k Key of the session
   * @param string|array $v session value to store
   * @param bool $force overwrite existing value (default: true)
   * @return bool
   */
  public static function set($k, $v, $force=true) {
    if(!self::active()){
      return false;
    }

    if ($force || !array_key_exists($k, $_SESSION)) {
      $_SESSION[$k] = $v;
    }

    return true;
  }

  /**
   * Get Session Value by key
   * Get all if key not provided
   * @param string $k session key
   */
  public static function get($k=null) {
    if(!self::active()){
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
    if(!self::active()){
      return false;
    }

    if (isset($k)) {
      if (is_array($k)) {
        foreach ($k as $key) {
          unset($_SESSION[$key]);
        }
        return true;
      }
      elseif (array_key_exists($k, $_SESSION)) {
        unset($_SESSION[$k]);
        return true;
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
    return !self::has($k) ? self::set($k, $v, $force) : true;
  }

  /**
   * Check if session exist
   * @param string $k session key
   * @return bool
   */
  public static function has($k=null) {
    if(!self::active()){
      return false;
    }

    if (isset($k)) {
      return array_key_exists($k, $_SESSION);
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
   * @param bool $is_error store in the error bag instead of the flash bag
   */
  public static function flash(string $k, $v, $is_error=false) {
    if(!self::active()){
      return false;
    }

    if($is_error){
      $_SESSION['__error'][$k] = $v;
    } else {
      $_SESSION['__flash'][$k] = $v;
    }

    return true;
  }

  /**
   * Clear Session variables (session itself stays alive)
   */
  public static function clear() {
    if(!self::active()){
      return false;
    }

    $_SESSION = [];
    session_unset();
    return true;
  }

  /**
   * Destroy the session completely (data, server file and cookie)
   */
  public static function destroy() {
    if(!self::active()){
      return false;
    }

    $_SESSION = [];
    session_unset();

    // Expire the session cookie in the browser too
    if(ini_get('session.use_cookies') && !headers_sent()){
      $p = session_get_cookie_params();
      setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $p['path'],
        'domain' => $p['domain'],
        'secure' => $p['secure'],
        'httponly' => $p['httponly'],
        'samesite' => $p['samesite'] ?: 'Lax',
      ]);
    }

    return session_destroy();
  }

}