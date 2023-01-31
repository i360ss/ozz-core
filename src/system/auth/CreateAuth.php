<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\auth;

class CreateAuth {

  private $app_dir            = __DIR__.SPC_BACK['core_2'].'app/';
  private $migration_dir      = __DIR__.SPC_BACK['core_2'].'database/migration/';
  private $controller_dir     = __DIR__.SPC_BACK['core_2'].'app/controller/';
  private $view_dir           = __DIR__.SPC_BACK['core_2'].'app/view/';
  private $email_template_dir = __DIR__.SPC_BACK['core_2'].'app/email_template/';


  public function index($com){
    //Create New Auth
    $this->createAuth();
  }



  /**
   * Create Auth
   */
  public function createAuth(){
    global $utils;
    $errors = [];

    // Users Migration already exist
    $migration_name = ucfirst(AUTH_USERS_TABLE);
    $find_user_migration = array_filter(scandir($this->migration_dir), function($item) {
      return $item[0] !== '.' && substr($item, -4) == '.php';
    });

    foreach ($find_user_migration as $k => $v) {
      if(file_exists($this->migration_dir.$v) && is_file($this->migration_dir.$v)){
        if(substr(substr($v, 14), 0, -4) == $migration_name){
          $errors[] = '['.$migration_name.'] migration already exist. Please remove or rename your existing ['.$migration_name.'] migration';
        }
      }
    }

    // Users Controller already exist
    if(file_exists($this->controller_dir.AUTH_CONTROLLER.'.php')){
      $errors[] = '['.AUTH_CONTROLLER.'] already exist. Please remove or rename your existing ['.AUTH_CONTROLLER.'] if it is not generated by auth command.';
    }

    // If has any errors
    if(isset($errors) && !empty($errors)){
      ozz_console_error('Can\'t Generate Auth!');
      foreach ($errors as $key => $value) {
        $utils->console_return($value, 'red', '', false, true);
      }
      exit;
    }

    // No errors (Create new auth)
    // Generate Controller
    $this->createAuthController(AUTH_CONTROLLER);

    // Generate and Run Migrations
    $this->createAuthMigration('user_table', AUTH_USERS_TABLE);
    $this->createAuthMigration('throttle_table', AUTH_THROTTLE_TABLE);
    $this->runAuthMigration($migration_name);
    $this->runAuthMigration(ucfirst(AUTH_THROTTLE_TABLE));

    // Generate View files
    $this->createAuthViewFile('sign-up', AUTH_VIEWS['sign-up']);
    $this->createAuthViewFile('login', AUTH_VIEWS['login']);
    $this->createAuthViewFile('forgot-password', AUTH_VIEWS['forgot-password']);
    $this->createAuthViewFile('reset-password', AUTH_VIEWS['reset-password']);
    $this->createAuthViewFile('verify-account', AUTH_VIEWS['verify-account']);

    // Generate Email Templates
    $this->createAuthEmailTemplate('account-verification-mail', AUTH_EMAIL_TEMPLATES['account-verification']);
    $this->createAuthEmailTemplate('password-reset-mail', AUTH_EMAIL_TEMPLATES['password-reset-request']);
    $this->createAuthEmailTemplate('throttle-reset-mail', AUTH_EMAIL_TEMPLATES['throttle-reset-request']);
    $this->createAuthEmailTemplate('new-login-alert-mail', AUTH_EMAIL_TEMPLATES['new-login-alert']);

    // Generate Routes
    $this->createAuthRoutes();

    // Generate Middleware
    // ===>
  }



  /**
   * Create auth controller
   * @param string Controller Name
   */
  private function createAuthController($name){
    global $utils;
    require 'auth-contents.php';

    $newFile = $this->controller_dir.$name.'.php';
    $controller_file = fopen($newFile, 'w');
    fwrite($controller_file, $auth_controller_content);
    
    $utils->console_return("[$name] generated", 'green');
  }



  /**
   * Create auth migration file
   * @param string Migration Name
   */
  private function createAuthMigration($type, $name){
    global $utils;
    require 'auth-contents.php';

    $newFile = $this->migration_dir.'mg_'.date('d_m_Y_').ucfirst($name).'.php';
    $migration_file = fopen($newFile, 'w');

    switch ($type) {
      case 'user_table':
        fwrite($migration_file, $auth_migration_content);
        break;

      case 'throttle_table':
        fwrite($migration_file, $login_attempts_migration_content);
        break;
    }

    $utils->console_return("[$name] migration file generated", 'green');
  }



  /**
   * Run default auth migration
   * @param string $name Migration Name
   */
  private function runAuthMigration($name){
    global $utils;
    if(exec("php ozz migrate $name")){
      $utils->console_return("[$name] table created", 'green');
    }
  }



  /**
   * Create Auth View Files view
   */
  private function createAuthViewFile($type, $name){
    global $utils;
    require 'auth-contents.php';

    if (!file_exists($this->view_dir.'auth/')) {
      mkdir($this->view_dir.'auth/', 0777, true);
    }

    $newFile = $this->view_dir.'auth/'.$name;
    $view_file = fopen($newFile, 'w');

    switch ($type) {
      case 'sign-up':
        fwrite($view_file, $signup_content);
        break;

      case 'login':
        fwrite($view_file, $login_content);
        break;

      case 'forgot-password':
        fwrite($view_file, $forgot_pass_content);
        break;

      case 'reset-password':
        fwrite($view_file, $reset_pass_content);
        break;

      case 'verify-account':
        fwrite($view_file, $verify_account_content);
        break;
    }

    $utils->console_return("[$name] View file created", 'green');
  }



  /**
   * Create Email Template
   * @param string $type Template type
   * @param string $name Template name
   */
  private function createAuthEmailTemplate($type, $name){
    global $utils;
    require 'auth-contents.php';

    $newFile = $this->email_template_dir.$name;
    $email_template = fopen($newFile, 'w');

    switch ($type) {
      case 'account-verification-mail':
        fwrite($email_template, $account_verification_mail);
        break;

      case 'password-reset-mail':
        fwrite($email_template, $password_reset_mail);
        break;

      case 'throttle-reset-mail':
        fwrite($email_template, $throttle_reset_mail);
        break;

      case 'new-login-alert-mail':
        fwrite($email_template, $new_login_security_alert_mail);
        break;
    }

    $utils->console_return("[$name] Email template created", 'green');
  }



  /**
   * Create Auth Routes
   */
  private function createAuthRoutes(){
    global $utils;
    require 'auth-contents.php';

    if(file_put_contents($this->app_dir.'Route.php', $router_content, FILE_APPEND | LOCK_EX)){
      $utils->console_return("Auth routes added to Route file", 'green');
    }
  }


}
(new CreateAuth)->index($com);