<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Cookie {

  /**
   * Set a cookie
   * @param string $name The name of the cookie
   * @param string $value The value of the cookie
   * @param int|array $expires The expiration time of the cookie in seconds (default: 0 for session cookie) or All configurations as array
   * @param string $path The path on the server in which the cookie will be available (default: '/')
   * @param string $domain The domain that the cookie is available to (default: '')
   * @param bool $secure Indicates if the cookie should only be transmitted over a secure HTTPS connection (default: false)
   * @param bool $httpOnly When true, the cookie will be made accessible only through the HTTP protocol (default: true)
   * @param string $sameSite The SameSite attribute for the cookie, controlling when it should be sent in cross-site requests (default: 'Strict')
   * @return bool True on success, false on failure to set the cookie
   */
public static function set($name, $value, $expires_or_opts = 0, $path = '/', $domain = '', $secure = false, $httpOnly = true, $sameSite = CONFIG['SESSION_SAME_SITE']) {
  $options = [
    'expires' => $expires_or_opts,
    'path' => $path,
    'domain' => $domain,
    'secure' => $secure,
    'httponly' => $httpOnly,
    'samesite' => $sameSite,
  ];

  if (is_array($expires_or_opts)) {
    $options = array_merge($options, $expires_or_opts);
  }

  return setcookie($name, $value, $options) ? true : false;
}

  /**
   * Check Cookie existence
   * @param string $name Cookie name
   * @return boolean
   */
  public static function has($name){
    return isset($_COOKIE[$name]);
  }

  /**
   * Get the value of a cookie
   * @param string $name The name of the cookie
   * @return mixed The value of the cookie, or null if the cookie is not set
   */
  public static function get($name){
    return self::has($name) ? $_COOKIE[$name] : null;
  }

  /**
   * Delete a cookie
   * @param string $name The name of the cookie
   * @return void
   */
  public static function delete($name) {
    unset($_COOKIE[$name]);
    setcookie($name, null, -1);
  }

}

