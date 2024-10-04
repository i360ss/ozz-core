<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Mail;
use Ozz\Core\Request;
use Ozz\Core\Response;
use Ozz\Core\Router;
use Ozz\Core\Csrf;

class Auth extends Model {
  
  use \Ozz\Core\DB;
  use \Ozz\Core\TokenHandler;
  use \Ozz\Core\system\auth\AuthInternal;

  private static $db;
  private static $id_field;
  private static $password_field;
  private static $username_field;
  private static $first_name_field;
  private static $last_name_field;
  private static $email_field;
  private static $status_field;
  private static $role_field;
  private static $avatar_field;
  private static $active_key_field;
  private static $auth_errors = [
    'success'                 => 'success',
    'error'                   => 'error',
    'meta_log_error'          => 'meta_log_error',
    'valid_token'             => 'valid_token',
    'invalid_username'        => 'invalid_username',
    'invalid_password'        => 'invalid_password',
    'unverified_account'      => 'unverified_account',
    'registration_failed'     => 'registration_failed',
    'email_already_exist'     => 'email_already_exist',
    'email_error'             => 'email_error',
    'username_already_exist'  => 'username_already_exist',
    'account_locked_throttle' => 'account_locked_throttle',
    'account_locked'          => 'account_locked',
    'account_suspended'       => 'account_suspended',
    'throttle_error'          => 'throttle_error',
    'password_reset_error'    => 'password_reset_error',
    'invalid_token'           => 'invalid_token',
    'expired_token'           => 'expired_token',
  ];

  /**
   * Initialize settings
   */
  private static function init(){
    self::$db               = (new static)->DB();
    self::$id_field         = CONFIG['AUTH_CORE_FIELDS']['ID_FIELD'];
    self::$username_field   = CONFIG['AUTH_CORE_FIELDS']['USERNAME_FIELD'];
    self::$email_field      = CONFIG['AUTH_CORE_FIELDS']['EMAIL_FIELD'];
    self::$first_name_field = CONFIG['AUTH_CORE_FIELDS']['FIRST_NAME_FIELD'];
    self::$last_name_field  = CONFIG['AUTH_CORE_FIELDS']['LAST_NAME_FIELD'];
    self::$password_field   = CONFIG['AUTH_CORE_FIELDS']['PASSWORD_FIELD'];
    self::$status_field     = CONFIG['AUTH_CORE_FIELDS']['STATUS_FIELD'];
    self::$role_field       = CONFIG['AUTH_CORE_FIELDS']['ROLE_FIELD'];
    self::$avatar_field     = CONFIG['AUTH_CORE_FIELDS']['AVATAR_FIELD'];
    self::$active_key_field = CONFIG['AUTH_CORE_FIELDS']['ACTIVATION_KEY_FIELD'];

    defined('AUTH_PASSWORD_RESET_THROTTLE') || define('AUTH_PASSWORD_RESET_THROTTLE', CONFIG['AUTH_PASSWORD_RESET_THROTTLE']);
    defined('AUTH_LOGIN_THROTTLE') || define('AUTH_LOGIN_THROTTLE', CONFIG['AUTH_LOGIN_THROTTLE']);
    defined('AUTH_EMAIL_CHANGE_THROTTLE') || define('AUTH_EMAIL_CHANGE_THROTTLE', CONFIG['AUTH_EMAIL_CHANGE_THROTTLE']);
    defined('AUTH_USER_ROLES') || define('AUTH_USER_ROLES', CONFIG['AUTH_USER_ROLES']);
    defined('AUTH_EMAIL_TEMPLATES') || define('AUTH_EMAIL_TEMPLATES', CONFIG['AUTH_EMAIL_TEMPLATES']);
  }

  /**
   * Create/Register New User
   * @param array $user_data User Data (email, username, password, first_name, last_name, role, status, avatar)
   * @param boolean $return_status Return meaningful error string or boolean (default is Boolean, only true if success)
   */
  public static function register(array $user_data, $return_status=true){
    self::init();

    $user_data[self::$username_field]   = isset($user_data[self::$username_field]) ? $user_data[self::$username_field] : explode('@', $user_data[self::$email_field])[0];
    $user_data[self::$role_field]       = isset($user_data[self::$role_field]) ? $user_data[self::$role_field] : self::roles()[0];
    $user_data[self::$status_field]     = isset($user_data[self::$status_field]) ? $user_data[self::$status_field] : $user_data[self::$status_field] = 'pending';
    $user_data[self::$active_key_field] = isset($user_data[self::$active_key_field]) ? $user_data[self::$active_key_field] : self::hashKey('activation');

    // Check if table fields are configured
    if(!empty($invalid_fields = array_diff(array_keys($user_data), CONFIG['AUTH_ALLOWED_FIELDS']))){
      $error_fields = implode(', ', $invalid_fields);
      set_error('error', trans_e('registration_failed'));
      return DEBUG
        ? Err::custom([
          'msg' => "Invalid table fields provided on [Auth::register()] method",
          'info' => 'These fields are not configured on app/config/auth.php <strong>['.$error_fields.']</strong>',
          'note' => "You must define the allowed fields of the Users table on app/config/auth.php before using it with user registration",
        ])
        : false;
    }

    $user_count_email = self::$db->count(CONFIG['AUTH_USERS_TABLE'], [self::$email_field => $user_data[self::$email_field]]);
    $user_count_username = self::$db->count(CONFIG['AUTH_USERS_TABLE'], [self::$username_field => $user_data[self::$username_field]]);

    if($user_count_email == 0 && $user_count_username == 0){
      // Temp Password (For instance Login)
      $temp_password = $user_data[self::$password_field];

      // Hash password
      $user_data[self::$password_field] = self::hashKey('password-hash', $user_data[self::$password_field]);

      // Registration time
      if(!array_key_exists('registered_at', $user_data)){
        $user_data['registered_at'] = time();
      }

      // Create account
      if(self::$db->insert(CONFIG['AUTH_USERS_TABLE'], $user_data)){
        set_error('success', trans('signup_success'));

        // Get this user's ID
        $get_this_user = self::$db->select(CONFIG['AUTH_USERS_TABLE'], [self::$id_field], [
          'OR' => [
            self::$email_field => $user_data[self::$email_field],
            self::$username_field => $user_data[self::$username_field],
          ]
        ]);

        self::addUserLog([
          'user_id' => $get_this_user[0][self::$id_field],
          'status'  => 'success',
          'type'    => 'signup',
        ]);

        // Send Verification mail if enabled
        if(CONFIG['AUTH_SEND_VERIFICATION_MAIL'] === true && CONFIG['AUTH_ACTIVATE_AND_LOGIN_ONCE_SIGNUP'] === false){
          $full_name = isset($user_data[self::$first_name_field]) && isset($user_data[self::$last_name_field]) 
            ? $user_data[self::$first_name_field].' '.$user_data[self::$last_name_field]
            : $user_data[self::$username_field];

          $mail_args = [
            'name' => $full_name,
            'verify_link' => clear_multi_slashes(AUTH_PATHS['verify_account'].'/').$user_data[self::$active_key_field]
          ];

          return self::notify('account-verification', $user_data[self::$email_field], $mail_args);
        } elseif(CONFIG['AUTH_ACTIVATE_AND_LOGIN_ONCE_SIGNUP'] === true){
          // Activate and Login to account if enabled
          self::activateAccount($user_data[self::$email_field]);
          self::login($user_data[self::$email_field], $temp_password);

          return Router::redirect(AUTH_USER_ROLES[$_SESSION['logged_user_role']]['landing_page']);
        }

        return $return_status ? self::$auth_errors['success'] : true;
      } else {
        set_error('error', trans_e('registration_failed'));
        return $return_status ? self::$auth_errors['registration_failed'] : false;
      }
    } elseif($user_count_email > 0){
      set_error('email', trans_e('email_already_exist'));
      return $return_status ? self::$auth_errors['email_already_exist'] : false;
    } else {
      set_error('username', trans_e('username_already_exist'));
      return $return_status ? self::$auth_errors['username_already_exist'] : false;
    }
  }

  /**
   * Verify User Account
   * @param string $token Verification token
   * @param string $return_status Return meaningful status or boolean
   */
  public static function verifyAccount(string $token, $return_status=true){
    self::init();

    $status = $return_status ? 'error' : false;
    $rows = self::$db->count(CONFIG['AUTH_USERS_TABLE'], [self::$status_field => 'pending', self::$active_key_field => $token]);

    if($rows > 0){
      if(self::$db->update(CONFIG['AUTH_USERS_TABLE'], [
        self::$status_field => 'active', 'email_verified_at' => time(),
        self::$active_key_field => ''
      ], [
        self::$active_key_field => $token])){
        $status = $return_status ? self::$auth_errors['success'] : true; // Account Activated
      }
    } else {
      $disabled_rows = self::$db->count(CONFIG['AUTH_USERS_TABLE'], [
        self::$status_field => 'disabled',
        self::$active_key_field => $token
      ]);
      if($disabled_rows > 0){
        $status = $return_status ? self::$auth_errors['account_suspended'] : false;
      } else {
        $status = $return_status ? self::$auth_errors['invalid_token'] : false;
      }
    }

    return $status;
  }

  /**
   * Login User
   * @param string $email Email/Username
   * @param string $password Password
   * @param boolean $return_status Return a meaningful error text if true / return true or false if this is false
   * @param array $args Additional arguments
   */
  public static function login(string $email, string $password, $return_status=true, $args=[]){
    // Redirect to landing page if already logged in
    if(self::isLoggedIn()){
      return Router::redirect(AUTH_USER_ROLES[$_SESSION['logged_user_role']]['landing_page']);
    }

    self::init();

    // Optional Arguments
    list($redirect_path, $query) = false;
    if(!empty($args)){
      extract($args, EXTR_IF_EXISTS);
    }

    $main_query = [
      'OR' => [
        self::$username_field => $email,
        self::$email_field => $email,
      ],
      self::$status_field => 'active'
    ];

    $main_query = $query ? array_merge($main_query, $query) : $main_query;
    $user = self::$db->select(CONFIG['AUTH_USERS_TABLE'], CONFIG['AUTH_ALLOWED_FIELDS'], $main_query);

    if(count($user) === 1){
      $user = $user[0];
      $throttle_data['user_id'] = $user[self::$id_field]; // User ID for Throttle log
      $throttle_data['type']    = 'login';

      // Check if Max attempts exceeded (Throttle)
      if(self::isLoginAttemptsExceeded($user[self::$id_field])){
        $wait_time = (AUTH_LOGIN_THROTTLE['DELAY_TIME'] <= 60) 
          ? AUTH_LOGIN_THROTTLE['DELAY_TIME'].' seconds' 
          : (AUTH_LOGIN_THROTTLE['DELAY_TIME'] / 60).' minutes';

        set_error('error', trans_e('account_locked_throttle', ['time' => $wait_time]));
        self::loginThrottleAction($user[self::$id_field]);

        return $return_status ? self::$auth_errors['throttle_error'] : false;
      } else {
        if(password_verify($password, $user[self::$password_field])){
          // User logged in
          session_regenerate_id();
          $redirect_to = (isset($redirect_path) && $redirect_path !== '') ? $redirect_path : AUTH_USER_ROLES[$user[self::$role_field]]['landing_page'];
          $_SESSION['logged_user_id']         = $user[self::$id_field];
          $_SESSION['logged_username']        = $user[self::$username_field];
          $_SESSION['logged_user_email']      = $user[self::$email_field];
          $_SESSION['logged_user_first_name'] = $user[self::$first_name_field];
          $_SESSION['logged_user_last_name']  = $user[self::$last_name_field];
          $_SESSION['logged_user_status']     = $user[self::$status_field];
          $_SESSION['logged_user_role']       = $user[self::$role_field];
          $_SESSION['logged_user_avatar']     = $user[self::$avatar_field];

          set_error('success', trans('login_success'));

          // Send New Login alert (IP/Device/OS/Browser) if changed
          if(CONFIG['AUTH_NEW_LOGIN_ALERT'] === true && self::isNewLogin()){
            $info = self::isNewLogin();
            $changed = $info['info'];

            if(in_array('device', $changed) || in_array('os', $changed) || in_array('os_version', $changed)){
              $what = 'Device';
            } elseif(in_array('ip', $changed)){
              $what = 'IP Address';
            } elseif(in_array('browser', $changed)){
              $what = 'Browser';
            }

            $info_dom = '<p><strong>IP Address: </strong>'.$info['ip'].'</p>';
            $info_dom .= '<p><strong>Device: </strong>'.$info['device'].'</p>';
            $info_dom .= '<p><strong>os: </strong>'.$info['os'].' | '.$info['os_version'].'</p>';
            $info_dom .= '<p><strong>Browser: </strong>'.$info['browser'].'</p>';
            $info_dom .= '<p><strong>Time: </strong>'.$info['date_time'].'</p>';

            $full_name = !empty($user[self::$first_name_field]) 
              ? $user[self::$first_name_field].' '.$user[self::$last_name_field] 
              : $user[self::$username_field];

            $new_login_alert_args = [
              'name' => $full_name,
              'what' => $what,
              'info_dom' => $info_dom,
              'info' => $info,
            ];
          }

          // Log throttle as success login
          $throttle_data['status'] = 'success';
          self::addUserLog($throttle_data);

          // Disable failed login attempts
          self::disableFailedAttempts($user[self::$id_field]);

          // Email Notification if new login
          isset($new_login_alert_args) ?  self::notify('new-login-alert', $user[self::$email_field], $new_login_alert_args) : false;

          return $return_status ? self::$auth_errors['success'] : Router::redirect($redirect_to);
        } else {
          set_error('error', trans_e('invalid_password'));
          $throttle_data['status'] = 'failed';
          $throttle_data['is_active'] = 1;
          self::addUserLog($throttle_data);

          return $return_status ? self::$auth_errors['invalid_password'] : false;
        }
      }
    } else {
      // Check if user locked
      $locked_user = self::$db->select(CONFIG['AUTH_USERS_TABLE'], self::$id_field, [
        'OR' => [
          self::$username_field => $email,
          self::$email_field => $email,
        ],
        self::$status_field => 'locked'
      ]);

      if(count($locked_user) === 1){
        // Check if temporary locked (Throttle)
        $is_temp_lock = self::$db->get(CONFIG['AUTH_LOG_TABLE'], ['id', 'user_id', 'timestamp'], [
          'user_id'   => $locked_user[0],
          'status'    => 'locked',
          'type'      => 'throttle_error',
          'is_active' => true,
          'ORDER'     => ['id' => 'DESC']
        ]);

        if(!is_null($is_temp_lock)){
          // Locked (Throttling)
          $unlock_time = $is_temp_lock['timestamp'] + AUTH_LOGIN_THROTTLE['DELAY_TIME'];

          if($unlock_time <= time()){
            // Disable lock on users_log and Unlock account
            self::$db->update(CONFIG['AUTH_LOG_TABLE'], ['is_active' => false], ['id' => $is_temp_lock['id']]);
            self::activateAccount($is_temp_lock['user_id']);
            self::disableFailedAttempts($is_temp_lock['user_id']);

            return self::login($email, $password, $return_status, $args);
          } else {
            $remaining_time = $unlock_time - time();
            $remaining_time_string = (AUTH_LOGIN_THROTTLE['DELAY_TIME'] <= 60 || $remaining_time <= 60)
              ? round($remaining_time).' seconds' 
              : round($remaining_time / 60).' minutes';

            set_error('error', trans_e('account_locked_throttle', ['time' => $remaining_time_string]));

            return $return_status ? self::$auth_errors['account_locked_throttle'] : false;
          }
        } else {
          // Locked (Other reasons)
          set_error('error', trans_e('account_locked'));
          return $return_status ? self::$auth_errors['account_locked'] : false;
        }
      } else {
        // Check if email not verified
        $unverified_user = self::$db->count(CONFIG['AUTH_USERS_TABLE'], self::$id_field, [
          'OR' => [
            self::$username_field => $email,
            self::$email_field => $email,
          ],
          self::$status_field => 'pending'
        ]);

        if($unverified_user === 1){
          set_error('error', trans_e('unverified_account'));
          return $return_status ? self::$auth_errors['unverified_account'] : false;
        } else {
          set_error('error', trans_e('invalid_username'));
          return $return_status ? self::$auth_errors['invalid_username'] : false;
        }
      }
    }
  }

  /**
   * Is Logged in
   */
  public static function isLoggedIn(){
    self::init();

    if(isset($_SESSION['logged_username']) && !empty($_SESSION['logged_username']) && isset($_SESSION['logged_user_id']) && !empty($_SESSION['logged_user_id'])){
      return true;
    } else {
      return false;
    }
  }

  /**
   * Get Logged in user info
   * @param string $key Key for get specific user value (Optional)
   */
  public static function info($key=false){
    self::init();

    if(self::isLoggedIn()){
      $user = [
        'id'         => $_SESSION['logged_user_id'],
        'username'   => $_SESSION['logged_username'],
        'email'      => $_SESSION['logged_user_email'],
        'name'       => $_SESSION['logged_user_first_name'].' '.$_SESSION['logged_user_last_name'],
        'first_name' => $_SESSION['logged_user_first_name'],
        'last_name'  => $_SESSION['logged_user_last_name'],
        'status'     => $_SESSION['logged_user_status'],
        'role'       => $_SESSION['logged_user_role'],
        'avatar'     => $_SESSION['logged_user_avatar'],
      ];

      if($user['name'] == ' '){
        $user['name'] = $_SESSION['logged_username'];
      }

      return (isset($key) && $key !== false) ? $user[$key] : $user;
    } else {
      return false;
    }
  }

  /**
   * Restore User info in session
   * @return void
   */
  public static function restoreInfo() : void {
    $user = self::$db->select(CONFIG['AUTH_USERS_TABLE'], CONFIG['AUTH_ALLOWED_FIELDS'], [self::$id_field => self::id()]);
    if(count($user) == 1){
      $user = $user[0];
      $_SESSION['logged_user_id']         = $user[self::$id_field];
      $_SESSION['logged_username']        = $user[self::$username_field];
      $_SESSION['logged_user_email']      = $user[self::$email_field];
      $_SESSION['logged_user_first_name'] = $user[self::$first_name_field];
      $_SESSION['logged_user_last_name']  = $user[self::$last_name_field];
      $_SESSION['logged_user_status']     = $user[self::$status_field];
      $_SESSION['logged_user_role']       = $user[self::$role_field];
      $_SESSION['logged_user_avatar']     = $user[self::$avatar_field];
    }
  }

  /**
   * Logged in user ID
   */
    public static function id(){
      return self::info('id');
    }

  /**
   * Logout User
   * @param string $redirect path to redirect after Logout
   */
  public static function logout($redirect=false){
    self::init();

    unset($_SESSION['logged_user_id']);
    unset($_SESSION['logged_username']);
    unset($_SESSION['logged_user_email']);
    unset($_SESSION['logged_user_status']);
    unset($_SESSION['logged_user_role']);
    unset($_SESSION['logged_user_avatar']);
    unset($_SESSION['logged_user_first_name']);
    unset($_SESSION['logged_user_last_name']);

    // Re generate session ID
    session_regenerate_id();

    // Re generate csrf token
    Csrf::refreshToken();

    $to = $redirect ? $redirect : AUTH_PATHS['login'];
    clear_error();

    return Router::redirect($to);
  }

  /**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 * @param string $email The email address
	 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
	 * @param string $default Default image set to use [ 404 | mp | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 */
  public static function getGravatar($email=false, $s=80, $d='mp', $r='g') {
    $url = 'https://www.gravatar.com/avatar/';
    if($email == false && (!isset($_SESSION['logged_user_email']) || empty($_SESSION['logged_user_email']))){
      $url .= md5(strtolower(random_str(5, 'a')));
    } else {
      $url .= md5(strtolower(trim($email ? $email : $_SESSION['logged_user_email'])));
    }
    $url .= "?s=$s&d=$d&r=$r";

    return $url;
  }

  /**
   * Get specific user by email, username, or user ID
   * @param string|int $email_username_id
   * @param string $key Return specific field value
   */
  public static function getUser($email_username_id=false, $key=false){
    self::init();

    if($email_username_id === false && isset($_SESSION['logged_user_id'])){
      $email_username_id = $_SESSION['logged_user_id'];
    }

    $user_data = self::$db->get(CONFIG['AUTH_USERS_TABLE'], '*', [ 
      'OR' => [
        self::$id_field => $email_username_id,
        self::$email_field => $email_username_id,
        self::$username_field => $email_username_id
      ]
    ]);

    return $key ? $user_data[$key] : $user_data;
  }

  /**
   * Get only authenticated User
   * @param string $key Specific field
   */
  public static function user($key=false){
    if(self::isLoggedIn()){
      return self::getUser($_SESSION['logged_user_id'], $key);
    } else {
      return false;
    }
  }

  /**
   * Get All users
   * @param array fields to get
   */
  public static function allUsers($what='*') {
    $user_data = self::$db->select(CONFIG['AUTH_USERS_TABLE'], $what);

    return $user_data;
  }

  /**
   * If logged in from a new browser or Device/IP
   * @return array|boolean Changed items (ip, device, browser, os) / return (false) if not a new login
   */
  public static function isNewLogin(){
    $request = Request::getInstance();

    if(!self::isLoggedIn()){
      return false;
    }

    self::init();

    $last_log = self::$db->select(CONFIG['AUTH_LOG_TABLE'], ['user_ip', 'user_agent'], [
      'user_id' => $_SESSION['logged_user_id'],
      'status' => 'success',
      'type' => ['login', 'signup'],
      'ORDER' => ['id' => 'DESC'],
      'LIMIT' => [0, 500]
    ]);

    if(empty($last_log)){
      return false;
    }

    $known_agents = [];
    foreach ($last_log as $key => $data) {
      $ag = json_decode($data['user_agent'], true);
      $known_agents['ip'][$key]         = $data['user_ip'];
      $known_agents['device'][$key]     = $ag['device'];
      $known_agents['os'][$key]         = $ag['os'];
      $known_agents['os_version'][$key] = $ag['os_version'];
      $known_agents['browser'][$key]    = $ag['browser'];
    }

    $current_agent              = $request->userAgent();
    $current_agent['ip']        = $request->ip();
    $current_agent['date_time'] = date("M d, Y | H:i:s");
    $new_entries                = [];
    $keys                       = ['ip', 'device', 'os', 'browser', 'os_version'];

    foreach ($keys as $key) {
      if (!is_null($current_agent[$key]) && !in_array($current_agent[$key], $known_agents[$key])) {
        $new_entries[] = $key;
      }
    }

    $current_agent['info'] = $new_entries;

    return !empty($current_agent['info']) ? $current_agent : false;
  }

  /**
   * Send password reset mail
   * @param string $email_username Email or Username
   * @param array $additional_mail_data Additional mail data to password reset mail
   */
  public static function passwordResetAttempt($email_username, $return_status=true, $additional_mail_data=[]){
    self::init();

    $user = self::getUser($email_username);

    if(isset($user)){
      $token = self::hashKey('password-reset', $email_username);
      $reset_link = clear_multi_slashes(AUTH_PATHS['reset_password'].'/'.$token);

      // Full name or Username
      $name = isset($user[self::$first_name_field]) && $user[self::$first_name_field] !== ''
        ? $user[self::$first_name_field].' '.$user[self::$last_name_field] 
        : $user[self::$username_field];

      // Primary mail arguments for password reset
      $args = [
        'reset_link' => $reset_link,
        'name'       => $name,
      ];
      $args = array_merge($args, $additional_mail_data);

      // Throttle Check
      if(self::isResetAttemptsExceeded($user[self::$id_field])){
        $last_attempt = self::$db->get(CONFIG['AUTH_META_TABLE'], 'timestamp', [
          'user_id'   => $user[self::$id_field],
          'meta_key'  => 'password_reset_token',
          'ORDER'     => ['id' => 'DESC']
        ]);

        $unlock_time = $last_attempt + AUTH_PASSWORD_RESET_THROTTLE['DELAY_TIME'];
        if($unlock_time <= time()){
          // Store token and send reset mail
          if(self::addUserMeta($user[self::$id_field], 'password_reset_token', $token)){
            return self::notify('password-reset', $user[self::$email_field], $args);
          } else {
            return $return_status ? self::$auth_errors['password_reset_error'] : false;
          }
        } else {
          $remaining_time = $unlock_time - time();
          $remaining_time_string = (AUTH_PASSWORD_RESET_THROTTLE['DELAY_TIME'] <= 60 || $remaining_time <= 60)
            ? round($remaining_time).' seconds' 
            : round($remaining_time / 60).' minutes';

          set_error('error', trans_e('password_reset_throttle', ['time' => $remaining_time_string]));

          return $return_status ? self::$auth_errors['throttle_error'] : false;
        }
      } else {
        // Store token and send reset mail
        if(self::addUserMeta($user[self::$id_field], 'password_reset_token', $token)){
          return self::notify('password-reset', $user[self::$email_field], $args);
        } else {
          return $return_status ? self::$auth_errors['password_reset_error'] : false;
        }
      }
    } else {
      set_error('error', trans_e('invalid_username'));

      return $return_status ? self::$auth_errors['invalid_username'] : false;
    }
  }

  /**
   * Common Email Notification
   * @param string $type Notification type
   * @param string $email recipient email
   * @param array $args All arguments to be passed to email template
   */
  public static function notify($type, $email, $args){
    $success = false;
    $error = false;

    switch ($type) {
      case 'account-verification':
        $defaults = [
          'title' => trans('email_verification_mail_title'),
          'subject' => trans('email_verification_mail_subject'),
          'template' => AUTH_EMAIL_TEMPLATES['account-verification'],
        ];
        $success = trans('email_verification_mail_sent', ['email' => $email]);
        $error = trans_e('email_verification_mail_error');
        $alt_fallback = trans('email_verification_mail_title').'<br> <a href="'.$args['verify_link'].'"></a>';
        break;

      case 'email-change-verification':
        $defaults = [
          'title' => trans('email_verification_mail_title'),
          'subject' => trans('email_verification_mail_subject'),
          'template' => AUTH_EMAIL_TEMPLATES['email-change-verification'],
        ];
        $success = trans('email_verification_mail_sent', ['email' => $email]);
        $error = trans_e('email_verification_mail_error');
        $alt_fallback = trans('email_verification_mail_title').'<br> <a href="'.$args['verify_link'].'"></a>';
        break;

      case 'email-changed-alert':
        $defaults = [
          'title' => trans('email_changed_alert_mail_title', ['new_email' => $email]),
          'subject' => trans('email_changed_alert_mail_subject'),
          'template' => AUTH_EMAIL_TEMPLATES['email-changed-alert'],
        ];
        $alt_fallback = trans('email_changed_alert_mail_title').'<br> Your email address changed to '.$email;
        break;

      case 'new-login-alert':
        $defaults = [
          'title' => trans('new_login_alert_mail_title'),
          'subject' => trans('new_login_alert_mail_subject'),
          'template' => AUTH_EMAIL_TEMPLATES['new-login-alert'],
        ];
        $alt_fallback = trans('new_login_alert_mail_title').'<br> Login from New '.$args['what'].'<br>Info: <br>'.$args['info_dom'];
        break;

      case 'password-reset':
        $defaults = [
          'title' => trans('password_reset_mail_title'),
          'subject' => trans('password_reset_mail_subject'),
          'template' => AUTH_EMAIL_TEMPLATES['password-reset-request'],
        ];
        $success = trans('password_reset_link_sent', ['email' => $email]);
        $error = trans_e('password_reset_mail_error');
        $alt_fallback = trans('password_reset_mail_title').'<br> Open the link below to reset your password<br> '.$args['reset_link'];
        break;

      case 'password-changed-alert':
        $defaults = [
          'title' => trans('password_changed_mail_title'),
          'subject' => trans('password_changed_mail_subject'),
          'template' => AUTH_EMAIL_TEMPLATES['password-changed-alert'],
        ];
        $alt_fallback = trans('password_changed_mail_title').'<br> Your password was changed. If it wasn\'t you, please contact us immediately.';
        break;
    }

    $defaults['app_name'] = APP_NAME;
    $defaults['attachments'] = false;
    $defaults['images'] = false;
    $defaults['email'] = $email;

    $arg = array_merge($defaults, $args);
    $alt_mail = isset($arg['alt']) ? $arg['alt'] : $alt_fallback;

    if(self::sendMail($arg, $alt_mail)){
      $success ? set_error('success', $success) : false;
      return true;
    } else {
      $error ? set_error('error', $error) : false;
      return false;
    }
  }

  /**
   * Verify/Confirm current password
   * @param string $current_password Current password to verify
   * @param string|int $user_id_email user ID, Email or username
   */
  public static function verifyPassword($current_password){
    if($user = self::user()){
      if(password_verify($current_password, $user[self::$password_field])){
        return true;
      }
    }

    return false;
  }

  /**
   * Change password by reset token
   * @param string $token Password reset token
   * @param string $new_password New password
   */
  public static function changePasswordByToken($token, $new_password){
    self::init();

    $user_id = self::$db->get(CONFIG['AUTH_META_TABLE'], 'user_id', [
      'meta_key'     => 'password_reset_token',
      'meta_value'   => $token,
      'timestamp[>]' => time() - (CONFIG['PASSWORD_RESET_LINK_LIFETIME'] + 1),
      'ORDER'        => ['id' => 'DESC']
    ]);

    if(isset($user_id) && self::deleteUserMeta(['user_id' => $user_id, 'meta_key' => 'password_reset_token'])){
      return self::changePassword($user_id, $new_password);
    }

    return false;
  }

  /**
   * Change Password
   * @param int $user_id
   * @param string $new_password New password
   */
  public static function changePassword($user_id, $new_password){
    self::init();

    // Check password change throttle
    if(self::isPasswordChangeAttemptsExceeded($user_id)){
      $last_attempt = self::$db->get(CONFIG['AUTH_LOG_TABLE'], 'timestamp', [
        'user_id' => $user_id,
        'type'    => 'password_change',
        'ORDER'   => ['id' => 'DESC']
      ]);

      $unlock_time = $last_attempt + AUTH_PASSWORD_RESET_THROTTLE['DELAY_TIME'];
      if($unlock_time > time()){
        $remaining_time = $unlock_time - time();
        $remaining_time_string = (AUTH_PASSWORD_RESET_THROTTLE['DELAY_TIME'] <= 60 || $remaining_time <= 60)
          ? round($remaining_time).' seconds' 
          : round($remaining_time / 60).' minutes';

        set_error('error', trans_e('password_reset_throttle', ['time' => $remaining_time_string]));

        return false;
      }
    }

    $request = Request::getInstance();
    $pass = self::hashKey('password-hash', $new_password);
    $changed = self::$db->update(CONFIG['AUTH_USERS_TABLE'], [ self::$password_field => $pass ], [
      self::$id_field => $user_id
    ]);

    if($changed){
      // Log password change
      self::addUserLog(['user_id' => $user_id, 'type' => 'password_change', 'status' => 'success']);

      set_error('success', trans('password_changed'));

      if(CONFIG['AUTH_PASSWORD_CHANGED_ALERT'] === true){
        $user = self::getUser($user_id);
        $name = isset($user[self::$first_name_field]) && $user[self::$first_name_field] !== ''
          ? $user[self::$first_name_field].' '.$user[self::$last_name_field] 
          : $user[self::$username_field];

        $info = $request->userAgent();
        $info['ip'] = $request->ip();

        $info_dom = '<p><strong>IP Address: </strong>'.$info['ip'].'</p>';
        $info_dom .= '<p><strong>Device: </strong>'.$info['device'].'</p>';
        $info_dom .= '<p><strong>os: </strong>'.$info['os'].' | '.$info['os_version'].'</p>';
        $info_dom .= '<p><strong>Browser: </strong>'.$info['browser'].'</p>';
        $info_dom .= '<p><strong>Time: </strong>'.date("M d, Y | H:i:s").'</p>';

        $args = [
          'name' => $name,
          'time' => time(),
          'info' => $info,
          'info_dom' => $info_dom,
        ];

        self::notify('password-changed-alert', $user[self::$email_field], $args);
      }

      if(CONFIG['AUTH_LOGOUT_ON_PASSWORD_CHANGE'] === true && self::isLoggedIn()){
        self::logout();
      }

      return true;
    } else {
      set_error('error', trans_e('password_change_error'));

      return false;
    }
  }

  /**
   * Email change request
   * @param string $new_email New email address
   */
  public static function emailChangeRequest($new_email, $return_status=true){
    if(!self::isLoggedIn()){
      return render_error_page(401, 'Unauthorized');
    }

    $user_id = self::info('id');
    $verification_token = self::hashKey('verification');
    $meta_val = json_encode(['email' => $new_email, 'token' => $verification_token]);

    // Email change throttle check
    if(self::isEmailChangeAttemptsExceeded($user_id)){
      $last_attempt = self::$db->get(CONFIG['AUTH_META_TABLE'], 'timestamp', [
        'user_id'  => $user_id,
        'meta_key' => 'email_change_verification',
        'ORDER'    => ['id' => 'DESC']
      ]);

      $unlock_time = $last_attempt + AUTH_EMAIL_CHANGE_THROTTLE['DELAY_TIME'];
      if($unlock_time >= time()){
        $remaining_time = $unlock_time - time();
        $remaining_time_string = (AUTH_EMAIL_CHANGE_THROTTLE['DELAY_TIME'] <= 60 || $remaining_time <= 60)
          ? round($remaining_time).' seconds' 
          : round($remaining_time / 60).' minutes';

        set_error('error', trans_e('email_change_throttle', ['time' => $remaining_time_string]));
        return $return_status ? self::$auth_errors['throttle_error'] : false;
      }
    }

    if(self::addUserMeta($user_id, 'email_change_verification', $meta_val)){
      $args = [
        'name' => self::info('name'),
        'current_email' => self::info('email'),
        'new_email' => $new_email,
        'verify_link' => clear_multi_slashes(AUTH_PATHS['verify_email'].'/').$verification_token,
      ];

      if(self::notify('email-change-verification', $new_email, $args)){
        set_error('success', trans('email_verification_mail_sent'));
        return $return_status ? self::$auth_errors['success'] : true;
      }

      set_error('error', trans_e('error'));
      return $return_status ? self::$auth_errors['email_error'] : false;
    }

    set_error('error', trans_e('error'));
    return $return_status ? self::$auth_errors['meta_log_error'] : false;
  }

  /**
   * Verify and change email address
   * @param string $token Verification token
   * @param boolean $return_status
   * @param boolean $notify Send email notification
   */
  public static function verifyAndChangeEmail($token, $notify=true, $return_status=true,){
    self::init();

    $meta = self::getUserMeta([
      'meta_key' => 'email_change_verification',
      'timestamp[>=]' => time() - CONFIG['EMAIL_VERIFICATION_LINK_LIFETIME'],
    ]);

    list($user_id, $new_email) = false;
    foreach ($meta as $k => $v) {
      $tk = json_decode($v['meta_value'])->token;
      if(hash_equals($tk, $token)){
        $user_id = $v['user_id'];
        $new_email = json_decode($v['meta_value'])->email;
        break;
      }
    }

    if($user_id && $new_email){
      self::changeEmail($new_email, $user_id, [], false);
      self::deleteUserMeta(['user_id' => $user_id, 'meta_key' => 'email_change_verification']);
      if($notify){
        $mail_args = [
          'new_email' => $new_email,
          'name' => strtok($new_email, '@'),
        ];
        self::notify('email-changed-alert', $new_email, $mail_args);
      }

      set_error('success', trans('email_change_success'));
      return $return_status ? self::$auth_errors['success'] : true;
    }

    set_error('error', trans_e('invalid_token', ['type' => 'email reset']));
    return $return_status ? self::$auth_errors['invalid_token'] : false;
  }

  /**
   * Change Email
   * @param string $user_id
   * @param string $new_email
   * @param boolean $authenticated_only allow only authenticated user (default: true)
   * @param array $where Additional where arguments
   * @param boolean $notify Send email notification
   */
  public static function changeEmail($new_email, $user_id=null, $where=[], $authenticated_only=true,){
    self::init();

    $changed = self::update([ self::$email_field => $new_email ], $user_id, $where, $authenticated_only);
    if($changed){
      self::isLoggedIn() ? $_SESSION['logged_user_email'] = $new_email : false;
      return true;
    }

    return false;
  }

  /**
   * Change Status
   * @param string $new_status new status
   * @param int $user_id User ID to change the status
   * @param array $where Extra arguments
   */
  public static function changeStatus($new_status, $user_id=null, $where=[]){
    self::init();

    return self::update([ self::$status_field => $new_status ], $user_id, $where);
  }

  /**
   * Change Role
   * @param string $new_role new role
   * @param int $user_id User ID to change the role
   * @param array $where Extra arguments
   */
  public static function changeRole($new_role, $user_id=null, $where=[]){
    self::init();

    return self::update([ self::$role_field => $new_role ], $user_id, $where);
  }

  /**
   * Update any User info
   * @param array $what The parameters to be updated
   * @param integer $user_id User ID
   * @param array $where Extra arguments
   * @param bool $authenticated_only Allow only logged-in users to make these updates
   */
  public static function update($what, $user_id=null, $where=[], $authenticated_only=true) {
    if($authenticated_only && !self::isLoggedIn()){
      return render_error_page(401, 'Unauthorized');
    }

    if(is_null($user_id)){
      $user_id = self::id();
    }

    $where_args = !empty($where)
      ? array_merge([self::$id_field => $user_id ], $where) 
      : [self::$id_field => $user_id ];

    $changed = self::$db->update(CONFIG['AUTH_USERS_TABLE'], $what, $where_args );

    // Restore User Info
    if($changed){
      self::restoreInfo();
      return true;
    }

    return false;
  }

  /**
   * Get all available User Role
   */
  public static function roles(){
    self::init();

    return array_keys(AUTH_USER_ROLES);
  }

  /**
   * Get Current user landing page
   * @param string $role
   */
  public static function getLandingPage($role=false){
    return $role ? AUTH_USER_ROLES[$role]['landing_page'] : AUTH_USER_ROLES[Auth::getRole()]['landing_page'];
  }

  /**
   * Check User role
   * @param string $role Role to check against
   */
  public static function isRole($role){
    self::init();

    if(self::isLoggedIn()){
      return $_SESSION['logged_user_role'] == $role;
    } else {
      return false;
    }
  }

  /**
   * Get a user's role by email or user ID
   * @param string $id_or_email User ID or User email
   */
  public static function getRole($id_or_email=false){
    self::init();

    if(self::isLoggedIn() && $id_or_email === false){
      return $_SESSION['logged_user_role'];
    } elseif($id_or_email !== false) {
      $role = self::$db->get(CONFIG['AUTH_USERS_TABLE'], self::$role_field, [
        'OR' => [
          self::$email_field => $id_or_email,
          self::$id_field => $id_or_email,
        ]
      ]);

      return !is_null($role) ? $role : false;
    } else {
      return false;
    }
  }

  /**
   * Check User status
   * @param string $status Status to check against
   */
  public static function isStatus($status){
    self::init();

    if(self::isLoggedIn()){
      return $_SESSION['logged_user_status'] == $status;
    } else {
      return false;
    }
  }

  /**
   * Lock User Account
   * @param string|int $user_id User ID to lock
   * @param array $where Extra arguments
   */
  public static function lockAccount($user_id, $where=[]){
    self::init();

    $where_args = !empty($where)
      ? array_merge([self::$id_field => $user_id ], $where) 
      : [self::$id_field => $user_id ];

    $locked = self::$db->update(CONFIG['AUTH_USERS_TABLE'], [
      self::$status_field => 'locked',
      self::$active_key_field => self::hashKey('activation', $user_id)
    ], $where_args );

    return $locked ? true : false;
  }

    /**
   * Activate/Unlock User Account
   * @param string|int $id_or_email User ID or email to activate
   * @param array $where Extra arguments
   */
  public static function activateAccount($id_or_email, $where=[]){
    self::init();

    $initial_where = [
      'OR' => [
        self::$id_field => $id_or_email,
        self::$email_field => $id_or_email
      ]
    ];
    $where_args = !empty($where)
      ? array_merge($initial_where, $where) 
      : $initial_where;

    $unlock = self::$db->update(CONFIG['AUTH_USERS_TABLE'], [
      self::$status_field => 'active',
      self::$active_key_field => ''
    ], $where_args );

    return $unlock ? true : false;
  }

  /**
   * Disable User Account
   * @param string|int $id_or_email User ID or Email to disable
   * @param array $where Extra arguments
   */
  public static function disableAccount($id_or_email, $where=[]){
    self::init();

    $initial_where = [
      'OR' => [
        self::$id_field => $id_or_email,
        self::$email_field => $id_or_email
      ]
    ];
    $where_args = !empty($where)
      ? array_merge($initial_where, $where) 
      : $initial_where;

    $disabled = self::$db->update(CONFIG['AUTH_USERS_TABLE'], [
      self::$status_field => 'disable',
      self::$active_key_field => self::hashKey('activation', $user_id)
    ], $where_args );

    return $disabled ? true : false;
  }

  /**
   * Delete User Account
   *@param string|int $id_or_email User ID or Email to delete
   * @param array $where Extra arguments
   */
  public static function deleteAccount($id_or_email, $where=[]){
    self::init();

    $initial_where = [
      'OR' => [
        self::$id_field => $id_or_email,
        self::$email_field => $id_or_email
      ]
    ];
    $where_args = !empty($where)
      ? array_merge($initial_where, $where) 
      : $initial_where;

    $deleted = self::$db->delete(CONFIG['AUTH_USERS_TABLE'], $where_args);

    return $deleted ? true : false;
  }

  /**
   * Check if Auth created via ozz command
   */
  public static function isCreated() {
    return file_exists(APP_DIR.'controller/'.CONFIG['AUTH_CONTROLLER'].'.php');
  }

}
