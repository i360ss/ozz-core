<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\auth;

class CreateAuth {

  private $app_dir             = __DIR__.SPC_BACK['core_2'].'app/';
  private $migration_dir       = __DIR__.SPC_BACK['core_2'].'database/migration/';
  private $controller_dir      = __DIR__.SPC_BACK['core_2'].'app/controller/';
  private $view_dir            = __DIR__.SPC_BACK['core_2'].'app/view/';
  private $middleware_dir      = __DIR__.SPC_BACK['core_2'].'app/middleware/';
  private $email_template_dir  = __DIR__.SPC_BACK['core_2'].'app/email_template/';
  private $auth_content_holder = __DIR__.'/../content-holder/auth';

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

    // Users Controller already exist
    if(file_exists($this->controller_dir.AUTH_CONTROLLER.'.php')){
      $errors[] = '[ '.AUTH_CONTROLLER.' ] already exist. Please remove or rename your existing [ '.AUTH_CONTROLLER.' ] if it is not generated by auth command.';
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
    $this->createAuthMigration('user-table', AUTH_USERS_TABLE);
    $this->createAuthMigration('user-log-table', AUTH_LOG_TABLE);
    $this->createAuthMigration('user-meta-table', AUTH_META_TABLE);
    $this->runAuthMigration(ucfirst(AUTH_USERS_TABLE));
    $this->runAuthMigration(ucfirst(AUTH_LOG_TABLE));
    $this->runAuthMigration(ucfirst(AUTH_META_TABLE));

    // Generate View files
    $this->createAuthViewFile('sign-up', AUTH_VIEWS['sign-up']);
    $this->createAuthViewFile('login', AUTH_VIEWS['login']);
    $this->createAuthViewFile('forgot-password', AUTH_VIEWS['forgot-password']);
    $this->createAuthViewFile('reset-password', AUTH_VIEWS['reset-password']);
    $this->createAuthViewFile('verify-account', AUTH_VIEWS['verify-account']);
    $this->createAuthViewFile('dashboard', AUTH_VIEWS['dashboard']);

    // Generate Email Templates
    $this->createAuthEmailTemplate('account-verification-mail', AUTH_EMAIL_TEMPLATES['account-verification']);
    $this->createAuthEmailTemplate('password-reset-mail', AUTH_EMAIL_TEMPLATES['password-reset-request']);
    $this->createAuthEmailTemplate('new-login-alert-mail', AUTH_EMAIL_TEMPLATES['new-login-alert']);
    $this->createAuthEmailTemplate('register-alert-mail', AUTH_EMAIL_TEMPLATES['register-alert']);
    $this->createAuthEmailTemplate('password-changed-alert-mail', AUTH_EMAIL_TEMPLATES['password-changed-alert']);
    $this->createAuthEmailTemplate('email-verification-mail', AUTH_EMAIL_TEMPLATES['email-change-verification']);
    $this->createAuthEmailTemplate('email-changed-alert-mail', AUTH_EMAIL_TEMPLATES['email-changed-alert']);

    // Generate Routes
    $this->createAuthRoutes();

    // Generate Middleware
    $this->createAuthMiddleware(AUTH_MIDDLEWARE_NAME);
  }

  /**
   * Create auth controller
   * @param string Controller Name
   */
  private function createAuthController($name){
    global $utils;
    require $this->auth_content_holder.'/auth-controller.php';

    $name = ucfirst($name);
    $newFile = substr($name, -4) == '.php' ? $this->controller_dir.$name : $this->controller_dir.$name.'.php';
    $controller_file = fopen($newFile, 'w');
    
    if(fwrite($controller_file, $content)){
      $utils->console_return("Controller created [ $name ]", 'green');
    } else {
      $utils->console_return("Error on creating Controller [ $name ]", 'red');
    }
    fclose($controller_file);
  }

  /**
   * Create auth migration file
   * @param string Migration Name
   */
  private function createAuthMigration($base_file, $name){
    global $utils;
    require $this->auth_content_holder.'/'.$base_file.'.php';

    $newFile = $this->migration_dir.'mg_'.date('d_m_Y_').ucfirst($name).'.php';
    $migration_file = fopen($newFile, 'w');

    if(fwrite($migration_file, $content)){
      $utils->console_return("Migration created [ $name ]", 'green');
    } else {
      $utils->console_return("Error on creating migration file [ $name ]", 'red');
    }
    fclose($migration_file);
  }

  /**
   * Run default auth migration
   * @param string $name Migration Name
   */
  private function runAuthMigration($name){
    global $utils;
    if(exec("php ozz migrate $name")){
      $utils->console_return("Table created [ $name ]", 'green');
    }
  }

  /**
   * Create Auth View Files view
   */
  private function createAuthViewFile($base_file, $name){
    global $utils;

    if (!file_exists($this->view_dir.'auth/')) {
      mkdir($this->view_dir.'auth/', 0777, true);
    }

    $from = $this->auth_content_holder.'/view/'.$base_file.'.phtml';
    $to = $this->view_dir.'auth/'.$name;

    if(copy($from, $to)){
      $utils->console_return("View file created [ $name ]", 'green');
    } else {
      $utils->console_return("Error on creating view file [ $name ]", 'red');
    }
  }

  /**
   * Create Email Template
   * @param string $type Template type
   * @param string $name Template name
   */
  private function createAuthEmailTemplate($base_file, $name){
    global $utils;

    $from = $this->auth_content_holder.'/mail-templates/'.$base_file.'.phtml';
    $to = $this->email_template_dir.$name;

    if(copy($from, $to)){
      $utils->console_return("Email template created [ $name ]", 'green');
    } else {
      $utils->console_return("Error on creating Email template [ $name ]", 'red');
    }
  }

  /**
   * Create Auth Middleware
   */
  private function createAuthMiddleware($name){
    global $utils;
    require $this->auth_content_holder.'/auth-middleware.php';

    $name = ucfirst($name);
    $newFile = substr($name, -4) == '.php' ? $this->middleware_dir.$name : $this->middleware_dir.$name.'.php';
    $middleware_file = fopen($newFile, 'w');

    if(fwrite($middleware_file, $content)){
      $utils->console_return("Middleware created [ $name ]", 'green');
    } else {
      $utils->console_return("Error on creating Middleware [ $name ]", 'red');
    }
    fclose($middleware_file);
  }

  /**
   * Create Auth Routes
   */
  private function createAuthRoutes(){
    global $utils;
    require $this->auth_content_holder.'/routes.php';

    if(file_put_contents($this->app_dir.'Route.php', $content, FILE_APPEND | LOCK_EX)){
      $utils->console_return("Auth routes added to Route file", 'green');
    }
  }

}
(new CreateAuth)->index($com);