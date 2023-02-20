<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Email;
use Ozz\Core\Request;
use Ozz\Core\Response;
use Ozz\Core\Router;

class Auth extends Model {


  use \Ozz\Core\DB;
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
  private static $active_key_field;
  private static $auth_errors = [
    'login_success'           => 'success',
    'signup_success'          => 'success',
    'invalid_username'        => 'invalid_username',
    'invalid_password'        => 'invalid_password',
    'unverified_account'      => 'unverified_account',
    'registration_failed'     => 'registration_failed',
    'email_already_exist'     => 'email_already_exist',
    'username_already_exist'  => 'username_already_exist',
    'account_locked_throttle' => 'account_locked_throttle',
    'account_locked'          => 'account_locked',
    'account_suspended'       => 'account_suspended',
    'throttle_error'          => 'throttle_error',
  ];



  /**
   * Initialize settings
   */
  private static function init(){
    self::$db               = (new static)->DB();
    self::$id_field         = AUTH_CORE_FIELDS['id_field'];
    self::$username_field   = AUTH_CORE_FIELDS['username_field'];
    self::$email_field      = AUTH_CORE_FIELDS['email_field'];
    self::$first_name_field = AUTH_CORE_FIELDS['first_name_field'];
    self::$last_name_field  = AUTH_CORE_FIELDS['last_name_field'];
    self::$password_field   = AUTH_CORE_FIELDS['password_field'];
    self::$status_field     = AUTH_CORE_FIELDS['status_field'];
    self::$role_field       = AUTH_CORE_FIELDS['role_field'];
    self::$active_key_field = AUTH_CORE_FIELDS['activation_key_field'];
    
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
    if(!empty($invalid_fields = array_diff(array_keys($user_data), AUTH_ALLOWED_FIELDS))){
      $error_fields = implode(', ', $invalid_fields);
      set_error('error', trans_e('registration_failed'));
      return DEBUG
        ? Err::custom([
          'msg' => "Invalid table fields provided on [Auth::register()] method",
          'info' => 'These fields are not configured on app/config/auth-config.php <strong>['.$error_fields.']</strong>',
          'note' => "You must define the allowed fields of the Users table on app/config/auth-config.php before using it with user registration",
        ])
        : false;
    }

    $user_count_email = self::$db->count(AUTH_USERS_TABLE, [self::$email_field => $user_data[self::$email_field]]);
    $user_count_username = self::$db->count(AUTH_USERS_TABLE, [self::$username_field => $user_data[self::$username_field]]);

    if($user_count_email == 0 && $user_count_username == 0){
      // Temp Password (For instance Login)
      $temp_password = $user_data[self::$password_field];

      // Hash password
      $user_data[self::$password_field] = password_hash($user_data[self::$password_field], PASSWORD_DEFAULT);

      // Registration time
      if(!array_key_exists('registered_at', $user_data)){
        $user_data['registered_at'] = time();
      }

      // Create account
      if(self::$db->insert(AUTH_USERS_TABLE, $user_data)){
        set_error('success', trans('signup_success'));

        // Get this user's ID
        $get_this_user = self::$db->select(AUTH_USERS_TABLE, [self::$id_field], [
          'OR' => [
            self::$email_field => $user_data[self::$email_field],
            self::$username_field => $user_data[self::$username_field],
          ]
        ]);

        self::logThrottle([
          'user_id' => $get_this_user[0][self::$id_field],
          'status'  => 'success',
          'type'    => 'signup',
        ]);

        // Send Verification mail if enabled
        if(AUTH_SEND_VERIFICATION_MAIL === true){
          $full_name = isset($user_data[self::$first_name_field]) && isset($user_data[self::$last_name_field]) 
            ? $user_data[self::$first_name_field].' '.$user_data[self::$last_name_field]
            : $user_data[self::$username_field];

          self::sendVerificationMail([
            'email' => $user_data[self::$email_field],
            'name'  => $full_name,
            'url'   => BASE_URL.AUTH_EMAIL_VERIFY_PATH.$user_data[self::$active_key_field]
          ]);

          remove_error('success');
          set_error('success', trans('signup_success_pending_verification'));
        }

        // Activate and Login to account if enabled
        if(AUTH_ACTIVATE_AND_LOGIN_ONCE_SIGNUP === true && AUTH_SEND_VERIFICATION_MAIL === false){
          self::activateAccount($user_data[self::$email_field]);
          self::login($user_data[self::$email_field], $temp_password);

          return Router::redirect(AUTH_USER_ROLES[$_SESSION['logged_user_role']]['landing_page']);
        }

        return $return_status ? self::$auth_errors['signup_success'] : true;
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
   * Send verification Email
   * @param array $args Email parameters (email and url values are must)
   */
  public static function sendVerificationMail($args=[]){
    self::init();

    $defaults = [
      'app_name'     => APP_NAME,
      'subject'      => trans('email_verification_subject'),
      'title'        => trans('email_verification_title'),
      'button_label' => trans('email_verification_button_label'),
      'template'     => AUTH_EMAIL_TEMPLATES['account-verification'],
      'email'        => false,
      'url'          => false,
      'name'         => false,
      'attachments'  => false,
      'images'       => false,
    ];

    $args = array_merge($defaults, $args);

    $mandatory_args = ['email', 'url'];
    $has_error = 0;
    foreach ($mandatory_args as $arg) {
      if ($args[$arg] === false) {
        Err::custom([
          'msg' => ucfirst($arg) . " not provided",
          'info' => 'For send mails using [Auth::sendVerificationMail()] method, [email] and [url] values are mandatory',
          'note' => "Provide the email and verification URL to send the verification mail",
        ]);
        $has_error++;
      }
    }

    if($has_error == 0){
      $alt_mail = trans('email_verification_title') ?? 'Email Verification - '.$args['app_name'];
      $alt_mail .= 'Please verify your email address.';
      $alt_mail .= '<a href="'.$args['url'].'" target="_blank">'.$args['url'].'</a>';

      return self::sendMail($args, $alt_mail);
    } else {
      return false;
    }
  }



  /**
   * Verify Account (Email)
   * @param string $token Verification token
   * @param string $return_status Return meaningful status or boolean
   */
  public static function verifyEmail(string $token, $return_status=false){
    self::init();

    $status = $return_status ? 'error' : false;
    $rows = self::$db->count(AUTH_USERS_TABLE, [self::$status_field => 'pending', self::$active_key_field => $token]);

    if($rows > 0){
      if(self::$db->update(AUTH_USERS_TABLE, [
        self::$status_field => 'active', 'email_verified_at' => time(),
        self::$active_key_field => 0
      ], [
        self::$active_key_field => $token])){
        $status = $return_status ? 'success' : true; // Account Activated
      }
    } else {
      $disabled_rows = self::$db->count(AUTH_USERS_TABLE, [
        self::$status_field => 'disabled',
        self::$active_key_field => $token
      ]);
      if($disabled_rows > 0){
        $status = $return_status ? 'disabled' : false;
      } else {
        $status = $return_status ? 'invalid' : false;
      }
    }

    return $status;
  }



  /**
   * Send New Login alert email (If Browser/Device changed)
   * @param array $args Email parameters
   */
  public static function sendNewLoginAlert($args=[]){
    self::init();

    if(AUTH_NEW_LOGIN_ALERT === true && self::isNewLogin()){
      $info = self::isNewLogin();
      $changed = $info['changes'];

      if(in_array('device', $changed) || in_array('os', $changed) || in_array('os_version', $changed)){
        $what = 'Device';
      } elseif(in_array('ip', $changed)){
        $what = 'IP Address';
      } elseif(in_array('browser', $changed)){
        $what = 'Browser';
      }

      $info_msg = "<br><br>";
      $info_msg .= '<p><strong>IP Address: </strong>'.$info['ip'].'</p>';
      $info_msg .= '<p><strong>Device: </strong>'.$info['device'].'</p>';
      $info_msg .= '<p><strong>os: </strong>'.$info['os'].' | '.$info['os_version'].'</p>';
      $info_msg .= '<p><strong>Browser: </strong>'.$info['browser'].'</p>';
      $info_msg .= '<p><strong>When: </strong>'.$info['date_time'].'</p>';
      $info_msg .= "<br><br>";

      $title = trans('new_login_alert_mail_title', ['what' => $what]);
      $message = trans('new_login_alert_mail_message', ['what' => $what, 'info' => $info_msg]);

      $defaults = [
        'app_name'     => APP_NAME,
        'subject'      => $title,
        'title'        => $title,
        'message'      => $message,
        'template'     => AUTH_EMAIL_TEMPLATES['new-login-alert'],
        'email'        => $_SESSION['logged_user_email'],
        'name'         => $_SESSION['logged_username'],
        'attachments'  => false,
        'images'       => false,
      ];

      $args = array_merge($defaults, $args);
      $alt_mail = trans('new_login_alert_mail_title') ?? 'Login from a new IP address - '.$args['app_name'];

      return self::sendMail($args, $alt_mail);
    }
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
    list($new_login_mail_data, $redirect_path, $query) = false;
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
    $user = self::$db->select(AUTH_USERS_TABLE, AUTH_ALLOWED_FIELDS, $main_query);

    if(count($user) === 1){
      $user = $user[0];
      $throttle_data['user_id'] = $user[self::$id_field]; // User ID for Throttle log
      $throttle_data['type']    = 'login';

      // Check if Max attempts exceeded (Throttle)
      if(self::isAttemptsExceeded($user[self::$id_field])){
        $wait_time = (AUTH_THROTTLE_DELAY_TIME <= 60) 
          ? AUTH_THROTTLE_DELAY_TIME.' seconds' 
          : (AUTH_THROTTLE_DELAY_TIME / 60).' minutes';

        set_error('error', trans_e('account_locked_throttle', ['time' => $wait_time]));
        self::throttleAction($user[self::$id_field]);

        return $return_status ? self::$auth_errors['throttle_error'] : false;
      } else {
        if(password_verify($password, $user[self::$password_field])){
          // User logged in
          $redirect_to = (isset($redirect_path) && $redirect_path !== '') ? $redirect_path : AUTH_USER_ROLES[$user[self::$role_field]]['landing_page'];
          $_SESSION['logged_user_id']         = $user[self::$id_field];
          $_SESSION['logged_username']        = $user[self::$username_field];
          $_SESSION['logged_user_email']      = $user[self::$email_field];
          $_SESSION['logged_user_first_name'] = $user[self::$first_name_field];
          $_SESSION['logged_user_last_name']  = $user[self::$last_name_field];
          $_SESSION['logged_user_status']     = $user[self::$status_field];
          $_SESSION['logged_user_role']       = $user[self::$role_field];

          set_error('success', trans('login_success'));

          // Send New Login alert (IP/Device/OS/Browser) if changed
          $new_login_mail_data['email'] = $user[self::$email_field];
          self::sendNewLoginAlert($new_login_mail_data);

          // Log throttle as success login
          $throttle_data['status'] = 'success';
          self::logThrottle($throttle_data);

          // Remove failed login attempts
          self::removeFailedAttempts($user[self::$id_field]);

          return $return_status ? self::$auth_errors['login_success'] : Router::redirect($redirect_to);
        } else {
          set_error('error', trans_e('invalid_password'));
          $throttle_data['status'] = 'failed';
          self::logThrottle($throttle_data);

          return $return_status ? self::$auth_errors['invalid_password'] : false;
        }
      }
    } else {
      // Check if user locked
      $locked_user = self::$db->select(AUTH_USERS_TABLE, self::$id_field, [
        'OR' => [
          self::$username_field => $email,
          self::$email_field => $email,
        ],
        self::$status_field => 'locked'
      ]);

      if(count($locked_user) === 1){
        // Check if temporary locked (Throttle)
        $is_temp_lock = self::$db->get(AUTH_THROTTLE_TABLE, ['id', 'user_id', 'timestamp'], [
          'user_id'   => $locked_user[0],
          'status'    => 'locked',
          'type'      => 'throttle_error',
          'is_active' => true,
          'ORDER'     => ['id' => 'DESC']
        ]);

        if(!is_null($is_temp_lock)){
          // Locked (Throttling)
          $unlock_time = $is_temp_lock['timestamp'] + AUTH_THROTTLE_DELAY_TIME;

          if($unlock_time <= time()){
            // Disable lock on users_log and Unlock account
            self::$db->update(AUTH_THROTTLE_TABLE, ['is_active' => false], ['id' => $is_temp_lock['id']]);
            self::activateAccount($is_temp_lock['user_id']);
            self::removeFailedAttempts($is_temp_lock['user_id']);

            return self::login($email, $password, $return_status, $args);
          } else {
            $remaining_time = $unlock_time - time();
            $remaining_time_string = (AUTH_THROTTLE_DELAY_TIME <= 60 || $remaining_time <= 60)
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
        $unverified_user = self::$db->count(AUTH_USERS_TABLE, self::$id_field, [
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
      ];

      return (isset($key) && $key !== false) ? $user[$key] : $user;
    } else {
      return false;
    }
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

    $to = $redirect ? $redirect : AUTH_LOGIN_PATH;
    clear_error();

    return Router::redirect($to);
  }



  /**
   * Throttle Action
   * @param int $user_id User ID
   */
  public static function throttleAction($user_id){
    if(AUTH_THROTTLE === true){
      $throttle_data = [
        'user_id'   => $user_id,
        'status'    => 'locked',
        'type'      => 'throttle_error',
        'is_active' => true,
      ];

      self::lockAccount($user_id);
      self::logThrottle($throttle_data);
    }
  }



  /**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 * @param string $email The email address
	 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
	 * @param string $default Default image set to use [ 404 | mp | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 */
  public static function getGravatar($email=false, $s=80, $d='identicon', $r='g') {
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email ? $email : $_SESSION['logged_user_email'])));
    $url .= "?s=$s&d=$d&r=$r";

    return $url;
  }



    /**
   * Add Avatar/Profile picture
   */
  public static function addAvatar($email){
    
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

    $last_log = self::$db->select(AUTH_THROTTLE_TABLE, ['user_ip', 'user_agent_all'], [
      'user_id' => $_SESSION['logged_user_id'],
      'status' => 'success',
      'type' => ['login', 'signup'],
      'ORDER' => ['id' => 'DESC'],
      'LIMIT' => [0, 100]
    ]);

    $known_agents = [];
    foreach ($last_log as $key => $data) {
      $ag = json_decode($data['user_agent_all'], true);
      $known_agents['ip'][$key] = $data['user_ip'];
      $known_agents['device'][$key] = $ag['device'];
      $known_agents['os'][$key] = $ag['os'];
      $known_agents['os_version'][$key] = $ag['os_version'];
      $known_agents['browser'][$key] = $ag['browser'];
    }

    $current_agent = $request->user_agent();
    $current_agent['ip'] = $request->ip();
    $current_agent['date_time'] = date("M d, Y | H:i:s");
    $new_entries = [];
    $keys = ['ip', 'device', 'os', 'browser', 'os_version'];

    foreach ($keys as $key) {
      if (!is_null($current_agent[$key]) && !in_array($current_agent[$key], $known_agents[$key])) {
        $new_entries[] = $key;
      }
    }

    $current_agent['changes'] = $new_entries;

    return !empty($current_agent['changes']) ? $current_agent : false;
  }



  /**
   * Send password reset mail
   * @param int|string $user_id User Id
   * @param array $additional_mail_data Additional mail data to password reset mail
   */
  public static function sendPasswordResetMail($user_id, $additional_mail_data=false){
    $user_data = self::$db->get(AUTH_USERS_TABLE, AUTH_ALLOWED_FIELDS, [ self::$id_field => $user_id ]);
    $user_data[self::$username_field];
    $user_data[self::$email_field];

    dd($user_data);
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
    // 1. Verify current password
    // 2. Change to New password
    // 3. Send Password reset alert
  }



  /**
   * Change Email
   * @param int $user_id User ID
   * @param string $current_email
   * @param string $new_email
   */
  public static function changeEmail($user_id, $current_email, $new_email){
    // 0. change account status to (pending)
    // 1. Send verification mail to new email
    // 2. Verify new Email account
    // 3. Change the Email
    // 4. Send Email change alert to old email
    // 5. Send welcome mail to new Email
  }



  /**
   * Change Status
   * @param string|int $user_id User ID to change the status
   * @param string $new_status new status
   * @param array $where Extra arguments
   */
  public static function changeStatus($user_id, $new_status, $where=[]){
    self::init();

    $where_args = !empty($where)
      ? array_merge([self::$id_field => $user_id ], $where) 
      : [self::$id_field => $user_id ];

    $changed = self::$db->update( AUTH_USERS_TABLE, [ self::$status_field => $new_status ], $where_args );

    return $changed ? true : false;
  }



  /**
   * Change Role
   * @param string|int $user_id User ID to change the role
   * @param string $new_role new role
   * @param array $where Extra arguments
   */
  public static function changeRole($user_id, $new_role, $where=[]){
    self::init();

    $where_args = !empty($where)
      ? array_merge([self::$id_field => $user_id ], $where) 
      : [self::$id_field => $user_id ];

    $changed = self::$db->update(AUTH_USERS_TABLE, [ self::$role_field => $new_role ], $where_args );

    return $changed ? true : false;
  }



  /**
   * Get all available User Role
   */
  public static function roles(){
    self::init();

    return array_keys(AUTH_USER_ROLES);
  }



  /**
   * Check User role
   * @param string $role Role to check against
   */
  public static function isRole($role){
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
    if(self::isLoggedIn() && $id_or_email === false){
      return $_SESSION['logged_user_role'];
    } else {
      $role = self::$db->get(AUTH_USERS_TABLE, self::$role_field, [
        'OR' => [
          self::$email_field => $id_or_email,
          self::$id_field => $id_or_email,
        ]
      ]);

      return !is_null($role) ? $role : false;
    }
  }



  /**
   * Check User status
   * @param string $status Status to check against
   */
  public static function isStatus($status){
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

    $locked = self::$db->update(AUTH_USERS_TABLE, [
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

    $unlock = self::$db->update(AUTH_USERS_TABLE, [
      self::$status_field => 'active',
      self::$active_key_field => 0 
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

    $disabled = self::$db->update(AUTH_USERS_TABLE, [
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

    $deleted = self::$db->delete(AUTH_USERS_TABLE, $where_args);

    return $deleted ? true : false;
  }

}
