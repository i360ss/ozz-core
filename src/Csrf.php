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
    $_SESSION['csrf_token'] = $token;
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
      }
    }

    if (empty($csrfToken)) {
      set_error('csrf_error', trans_e('invalid_token', ['type' => 'CSRF']));
      throw new Exception("CSRF token missing");
    }

    // Validate Token
    if (hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
      return true; // Token is valid
    }

    set_error('csrf_error', trans_e('invalid_token', ['type' => 'CSRF']));
    throw new Exception("CSRF token invalid");
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
  public static function getToken() {
    if (!isset($_SESSION['csrf_token'])) {
      self::refreshToken();
    }
    return $_SESSION['csrf_token'];
  }

  /**
   * Get Token field (HTML input field)
   */
  public static function getTokenField(){
    return '<input type="hidden" name="csrf_token" value="'.htmlspecialchars(self::getToken(), ENT_QUOTES, 'UTF-8').'">';
  }

}