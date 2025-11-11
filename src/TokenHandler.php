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

trait TokenHandler {

  /**
   * Generate common auth hashes
   * @param string $type hash $type
   * @param string $string string to include to hash
   */
  private static function hashKey($type='activation', $string=''){
    switch ($type) {
      case 'activation':
      case 'verification':
        return bin2hex(random_bytes(32));
        break;

      case 'password-reset':
        return rtrim(strtr(base64_encode(random_bytes(48)), '+/', '-_'), '=');
        break;

      case 'password-hash':
        return password_hash($string, PASSWORD_DEFAULT);
        break;

      case 'csrf-token':
        return bin2hex(random_bytes(32).random_str(24, 'A0'));
        break;

      case 'csp-nonce':
        return base64_encode(substr(base64_encode(sha1( mt_rand() )), 0, 20));
        break;
    }
  }


}