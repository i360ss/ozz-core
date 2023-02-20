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
        return sha1(random_str(36).time().$string);
        break;
    }
  }



  /**
   * Log Throttles
   * @param array $throttle_data Throttle information to be logged
   */
  private static function logThrottle($throttle_data){
    $request = Request::getInstance();

    $throttle_data['user_ip']        = $request->ip();
    $throttle_data['user_agent']     = $request->user_agent(true);
    $throttle_data['user_agent_all'] = json_encode($request->user_agent());
    $throttle_data['timestamp']      = time();

    self::$db->insert(AUTH_THROTTLE_TABLE, $throttle_data);
  }



  /**
   * Check if Login attempts exceeded
   * @param int $user_id User ID
   */
  private static function isAttemptsExceeded($user_id){
    if(AUTH_THROTTLE === true){
      $request = Request::getInstance();
      $time_ago = time() - AUTH_THROTTLE_TIME;

      $failed_attempts = self::$db->count(AUTH_THROTTLE_TABLE, ['timestamp'], [
        'user_id'       => $user_id,
        'status'        => 'failed',
        'type'          => 'login',
        'user_ip'       => $request->ip(),
        'timestamp[>=]' => $time_ago,
      ]);

      return $failed_attempts >= AUTH_THROTTLE_MAX_ATTEMPTS;
    } else {
      return false;
    }
  }



  /**
   * Remove Failed Attempts of a user
   * @param int $user_id User ID
   */
  private static function removeFailedAttempts($user_id){
    $delete_failed_attempts = self::$db->delete(AUTH_THROTTLE_TABLE, [
      'user_id'  => $user_id,
      'status'   => 'failed',
      'type'     => 'login',
    ]);

    return $delete_failed_attempts ? true : false;
  }



  /**
   * Send All Auth mails
   * @param array $args All email parameters
   * @param string $alt_mail Text version of the mail
   */
  private static function sendMail($args, $alt_mail){
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


}