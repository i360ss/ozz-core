<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Email;

class Auth extends Model {

  use DB;

  private static $db;
  private static $users_fields;
  private static $users_table;
  private static $id_field;
  private static $password_field;
  private static $username_field;
  private static $email_field;
  private static $status_field;
  private static $role_field;
  private static $email_template;
  private static $user_roles;
  private static $login_path;


  /**
   * Initialize settings
   */
  private static function init(){
    self::$db             = (new static)->DB();
    self::$users_table    = APP_CONFIG['auth_config']['users_table'];
    self::$id_field       = APP_CONFIG['auth_config']['id_field'];
    self::$users_fields   = APP_CONFIG['auth_config']['allowed_fields'];
    self::$username_field = APP_CONFIG['auth_config']['username_field'];
    self::$email_field    = APP_CONFIG['auth_config']['email_field'];
    self::$password_field = APP_CONFIG['auth_config']['password_field'];
    self::$status_field   = APP_CONFIG['auth_config']['status_field'];
    self::$role_field     = APP_CONFIG['auth_config']['role_field'];
    self::$email_template = APP_CONFIG['auth_config']['email_template'];
    self::$user_roles     = APP_CONFIG['auth_config']['user_roles'];
    self::$login_path     = APP_CONFIG['auth_config']['login_path'];
  }



  /**
   * Create/Register New User
   * @param array $user_data User Data (email, username, password, first_name, last_name, role, status, avatar)
   * @param string $u_table Users table on database
   */
  public static function createUser(array $user_data, $users_table=false){
    self::init();

    // Check if table fields are configured
    if(!empty($invalid_fields = array_diff(array_keys($user_data), self::$users_fields))){
      $error_fields = implode(', ', $invalid_fields);
      set_error('error', trans_e('registration_failed'));
      return DEBUG
        ? Err::custom([
          'msg' => "Invalid table fields provided on [Auth::createUser()] method",
          'info' => 'These fields are not configured on app/config.php <strong>['.$error_fields.']</strong>',
          'note' => "You must define the allowed fields of the Users table on app/config.php before using it with user registration",
        ])
        : false;
    }

    $table = $users_table ? $users_table : self::$users_table;
    $user_count = self::$db->count($table, [self::$email_field => $user_data[self::$email_field]]);

    if($user_count == 0){
      // Password encryption
      $pass_hash = password_hash($user_data[self::$password_field], PASSWORD_DEFAULT);
      $user_data[self::$password_field] = $pass_hash;

      // Registration time
      if(!array_key_exists('registered_at', $user_data)){
        $user_data['registered_at'] = date("Y-m-d H:i:s");
      }

      // Create account
      if(self::$db->insert($table, $user_data)){
        remove_flash('form_data');
        set_error('success', trans('signup_success'));
        return true;
      } else {
        set_error('error', trans_e('registration_failed'));
        return false;
      }
    } else {
      set_error('email', trans_e('email_already_exist'));
      return false;
    }
  }



  /**
   * Send verification Email
   * @param string $email Recipient email address
   * @param array $args Email parameters
   */
  public static function sendVerificationMail($email, $args=[]){
    self::init();

    $alt_mail = trans('email_verification_title') ?? 'Email Verification - '.$args['app_name'];
    $alt_mail .= 'Please verify your email address.';
    $alt_mail .= '<a href="'.$args['url'].'" target="_blank" class="button">'.$args['url'].'</a>';

    $mail = Email::send([
      'data'      => $args,
      'to'        => $email,
      'title'     => isset($args['title']) ? $args['title'] : trans('email_verification_title'),
      'subject'   => isset($args['subject']) ? $args['subject'] : trans('email_verification_subject'),
      'template'  => self::$email_template,
      'alt'       => $alt_mail,
      'files'     => $args['attachments'] ?? false,
      'img'       => $args['images'] ?? false,
    ]);

    return (bool) $mail;
  }



  /**
   * Verify Account (Email)
   * @param string $token Verification token
   * @param string $users_table Users table (Optional)
   * @return string This will return a status
   */
  public static function verifyEmail(string $token, $users_table=false){
    self::init();

    $status = 'error';
    $table = $users_table ? $users_table : self::$users_table;
    $rows = self::$db->count($table, [self::$status_field => 'pending', 'activation_key' => $token]);

    if($rows > 0){
      if(self::$db->update($table, [self::$status_field => 'active', 'email_verified_at' => date("Y-m-d H:i:s"), 'activation_key' => 0], ['activation_key' => $token])){
        $status = 'success'; // Account Activated
      }
    } else {
      $disabled_rows = self::$db->count($table, [self::$status_field => 'disabled', 'activation_key' => $token]);
      if($disabled_rows > 0){
        $status = 'disabled'; 
      } else {
        $status = 'invalid';
      }
    }

    return $status;
  }



  /**
   * Login User
   * @param string $email Email/Username
   * @param string $password Password
   * @param string $users_table Users table (Optional)
   */
  public static function login(string $email, string $password, $users_table=false, $redirect_path=false){
    self::init();

    $table = $users_table ? $users_table : self::$users_table;
    $user = self::$db->select($table, self::$users_fields, [
      'OR' => [
        self::$username_field => $email,
        self::$email_field => $email,
      ],
      self::$status_field => 'active'
    ]);

    if(count($user) === 1){
      $user = $user[0];
      if(password_verify($password, $user[self::$password_field])){
        // User logged in
        $redirect_to = $redirect_path ? $redirect_path : self::$user_roles[$user[self::$role_field]]['landing_page'];
        $_SESSION['logged_user']        = $user[self::$username_field];
        $_SESSION['logged_user_id']     = $user[self::$id_field];
        $_SESSION['logged_user_status'] = $user[self::$status_field];
        $_SESSION['logged_user_role']   = $user[self::$role_field];

        return Router::redirect($redirect_to);
      } else {
        set_error('error', trans_e('invalid_password'));
      }
    } else {
      set_error('error', trans_e('invalid_username'));
    }
  }



  /**
   * Logout User
   * @param string $redirect path to redirect after Logout
   */
  public static function logout($redirect=false){
    self::init();

    unset(
      $_SESSION['logged_user'],
      $_SESSION['logged_user_id'],
      $_SESSION['logged_user_status'],
      $_SESSION['logged_user_role']
    );

    $to = $redirect ? $redirect : self::$login_path;

    return Router::redirect($to);
  }



  /**
   * Verify Password (for reset)
   */
  public static function verifyPassword(){

  }




  /**
   * Reset Password
   */
  public static function resetPassword(){

  }



  /**
   * Change Password
   */
  public static function changePassword(){

  }



  /**
   * Change Email
   */
  public static function changeEmail(){

  }



  /**
   * Change Status
   */
  public static function changeStatus(){

  }



  /**
   * Change Role
   */
  public static function changeRole(){

  }



  /**
   * User Information
   * @param string $key Key for get specific user information
   */
  public static function info($key=false){

  }



  /**
   * Delete User Account
   * @param int $user_id User ID to delete
   * @param string $status Account status
   */
  public static function deleteUser(int $user_id, string $status='pending'){

  }

}