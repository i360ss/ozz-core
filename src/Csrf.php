<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Cookie;

if(defined('OZZ_FUNC') === false){
  require 'system/ozz-func.php';
}

class Csrf {

  use \Ozz\Core\TokenHandler;

  /**
   * Generate new CSRF Token
   */
  public static function generateToken(){
    return self::hashKey('csrf-token');
  }

  /**
   * Set new token
   */
  public static function setToken($token){
    unset($_SESSION['csrf_token']);
    $cookieLife = CONFIG['CSRF_COOKIE_LIFETIME'] > 0 ? time() + CONFIG['CSRF_COOKIE_LIFETIME'] : 0;
    Cookie::set('csrf_token', $token, [
      'expires' => $cookieLife,
      'path' => CONFIG['COOKIE_PATH'],
      'domain' => CONFIG['COOKIE_DOMAIN'],
      'secure' => CONFIG['COOKIE_SECURE'],
      'httponly' => CONFIG['COOKIE_HTTP_ONLY'],
      'samesite' => CONFIG['COOKIE_SAMESITE'],
    ]);
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_expire'] = $cookieLife; // Token expire time
  }

  /**
   * Validate CSRF Token
   * @param boolean $fresh Refresh token (Default: true)
   */
  public static function validateToken($token=null){
    $csrfToken = $token;

    if (empty($csrfToken)) {
      if (!empty($_POST['csrf'])) {
        $csrfToken = $_POST['csrf'];
      } elseif (!empty($_POST['csrf_token'])) {
        $csrfToken = $_POST['csrf_token'];
      } elseif (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'];
      } elseif (isset($_SERVER['HTTP_X_XSRF_TOKEN'])) {
        $csrfToken = $_SERVER['HTTP_X_XSRF_TOKEN'];
      } elseif (Cookie::has('csrf_token')) {
        $csrfToken = Cookie::get('csrf_token');
      }
    }

    if (empty($csrfToken)) {
      set_error('csrf_error', trans_e('invalid_token', ['type' => 'CSRF']));
      return render_error_page(401, 'Unauthorized');
    }

    // Validate Token
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $csrfToken)) {
      // Token is valid
      self::refreshToken();
      return true;
    } elseif (isset($_SESSION['csrf_token_expire']) && $_SESSION['csrf_token_expire'] !== 0 && time() > $_SESSION['csrf_token_expire']) {
      // Token has expired
      self::refreshToken();
    }

    set_error('csrf_error', trans_e('invalid_token', ['type' => 'CSRF']));
    return render_error_page(401, 'Unauthorized');
  }

  /**
   * Refresh Token
   */
  public static function refreshToken(){
    $newToken = self::hashKey('csrf-token');
    self::setToken($newToken);
  }

  /**
   * Get current token
   */
  public static function getToken(){
    return isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : null;
  }

  /**
   * Get Token field (HTML input field)
   */
  public static function getTokenField(){
    if(isset($_SESSION['csrf_token'])){
      return '<input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'">';
    } else {
      self::refreshToken();
      return '<input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'">';
    }
  }

}