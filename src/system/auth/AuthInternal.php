<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\auth;

use Ozz\Core\Request;
use Ozz\Core\Medoo;
use Ozz\Core\Email;

trait AuthInternal {

  /**
   * Generate common auth hashes
   * @param string $type hash $type
   * @param string $string string to include to hash
   */
  public static function hashKey($type='activation', $string=''){
    switch ($type) {
      case 'activation':
        return sha1(random_str(36, 'A0').time().$string);
        break;

      case 'password-reset':
        return rtrim(strtr(base64_encode(random_str(32, 'A0').time().$string), '+/', '-_'), '=');
        break;

      case 'password-hash':
        return password_hash($string, PASSWORD_DEFAULT);
        break;
    }
  }

  /**
   * User activity log
   * @param array $log_data Information to be logged
   */
  public static function addUserLog($log_data){
    $request = Request::getInstance();

    $log_data['user_ip']    = $request->ip();
    $log_data['user_agent'] = json_encode($request->user_agent());
    $log_data['timestamp']  = time();

    self::$db->insert(AUTH_LOG_TABLE, $log_data);
  }

  /**
   * Login Throttle Action
   * @param int $user_id User ID
   */
  public static function loginThrottleAction($user_id){
    self::init();

    if(AUTH_LOGIN_THROTTLE['ENABLE'] === true){
      $throttle_data = [
        'user_id'   => $user_id,
        'status'    => 'locked',
        'type'      => 'throttle_error',
        'is_active' => true,
      ];

      self::lockAccount($user_id);
      self::addUserLog($throttle_data);
    }
  }

  /**
   * Check if Login attempts exceeded
   * @param int $user_id User ID
   */
  public static function isLoginAttemptsExceeded($user_id){
    if(defined('AUTH_LOGIN_THROTTLE') && AUTH_LOGIN_THROTTLE['ENABLE'] === true){
      self::init();

      $request = Request::getInstance();

      $failed_attempts = self::$db->count(AUTH_LOG_TABLE, [
        'user_id'       => $user_id,
        'status'        => 'failed',
        'type'          => 'login',
        'is_active'     => 1,
        'user_ip'       => $request->ip(),
        'timestamp[>=]' => time() - AUTH_LOGIN_THROTTLE['PERIOD'],
      ]);

      return $failed_attempts >= AUTH_LOGIN_THROTTLE['MAX_ATTEMPTS'];
    } else {
      return false;
    }
  }

  /**
   * Check if Password reset attempts exceeded
   * @param int $user_id User ID
   */
  public static function isResetAttemptsExceeded($user_id){
    self::init();

    if(defined('AUTH_PASSWORD_RESET_THROTTLE') && AUTH_PASSWORD_RESET_THROTTLE['ENABLE'] === true){
      $count_requests = self::$db->count( AUTH_META_TABLE, [
        'user_id'       => $user_id,
        'meta_key'      => 'password_reset_token',
        'timestamp[>=]' => time() - AUTH_PASSWORD_RESET_THROTTLE['PERIOD']
      ]);

      return $count_requests >= AUTH_PASSWORD_RESET_THROTTLE['MAX_ATTEMPTS'];
    } else {
      return false;
    }
  }

  /**
   * Check if password change attempts exceeded
   * @param int $user_id User ID
   */
  public static function isPasswordChangeAttemptsExceeded($user_id){
    self::init();

    if(defined('AUTH_PASSWORD_RESET_THROTTLE') && AUTH_PASSWORD_RESET_THROTTLE['ENABLE'] === true){
      $count_changes = self::$db->count( AUTH_LOG_TABLE, [
        'user_id'       => $user_id,
        'type'          => 'password_change',
        'timestamp[>=]' => time() - AUTH_PASSWORD_RESET_THROTTLE['PERIOD']
      ]);

      return $count_changes >= AUTH_PASSWORD_RESET_THROTTLE['MAX_ATTEMPTS'];
    } else {
      return false;
    }
  }

  /**
   * Validate password reset token
   * @param string $token Password reset token
   * @param boolean $return_status Return a meaningful error message
   */
  public static function validateResetToken($token, $return_status=true){
    self::init();

    $meta_data = self::$db->get(AUTH_META_TABLE, '*', [
      'meta_key' => 'password_reset_token',
      'meta_value' => $token
    ]);

    if(isset($meta_data) && is_array($meta_data)){
      $user = self::getUser($meta_data['user_id']);
      $decoded_token = base64_decode($token);

      if(str_contains($decoded_token, $user[self::$email_field])){
        if($meta_data['timestamp'] >= time() - PASSWORD_RESET_LINK_EXPIRE_IN){
          // Reset token validated
          return $return_status ? self::$auth_errors['valid_token'] : true;
        } else {
          // Token expired
          set_error('error', trans_e('expired_reset_token'));
          return $return_status ? self::$auth_errors['expired_token'] : false;
        }
      }
    }

    set_error('error', trans_e('invalid_reset_token'));
    return $return_status ? self::$auth_errors['invalid_token'] : false;
  }

  /**
   * Disable Failed Attempts of a user
   * @param int $user_id User ID
   */
  public static function disableFailedAttempts($user_id){
    self::init();

    $delete_failed_attempts = self::$db->update(AUTH_LOG_TABLE, ['is_active' => 0], [
      'user_id'   => $user_id,
      'status'    => 'failed',
      'type'      => 'login',
      'is_active' => 1
    ]);

    return $delete_failed_attempts ? true : false;
  }

  /**
   * Send All Auth mails
   * @param array $args All email parameters
   * @param string $alt_mail Text version of the mail
   */
  private static function sendMail($args, $alt_mail=''){
    $mail = Email::send([
      'to'        => $args['email'],
      'title'     => $args['title'],
      'subject'   => $args['subject'],
      'template'  => $args['template'],
      'data'      => $args,
      'alt'       => $alt_mail,
      'files'     => $args['attachments'],
      'img'       => $args['images'],
    ]);

    return (boolean) $mail;
  }

  /**
   * Create a User Meta
   * @param int $user_id User ID
   * @param string $meta_key Meta Key
   * @param string|int|bool|object|array|json $meta_value
   */
  public static function addUserMeta($user_id, $meta_key, $meta_value){
    $create = self::$db->insert(AUTH_META_TABLE, [
      'user_id' => $user_id,
      'meta_key' => $meta_key,
      'meta_value' => $meta_value,
      'timestamp' => time()
    ]);

    return $create ? true : false;
  }

  /**
   * Delete user meta
   * @param array $where Where arguments
   */
  public static function deleteUserMeta($where){
    $deleted = self::$db->delete(AUTH_META_TABLE, $where);

    return $deleted ? true : false;
  }

}