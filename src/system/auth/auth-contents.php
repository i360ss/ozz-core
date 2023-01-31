<?php
/**
 * Auth Migration Generate Content
 */
$auth_migration_content = "<?php
/**
 * ".ucfirst(AUTH_USERS_TABLE)." Migration
 */
use Ozz\Core\system\migration\Schema;

class ".ucfirst(AUTH_USERS_TABLE)." {

  public function up(){
    Schema::createTable('".AUTH_USERS_TABLE."', [
      'user_id'           => ['int', 'ai', 'primary'],
      'username'          => ['str:150'],
      'first_name'        => ['str:150'],
      'last_name'         => ['str:150'],
      'email'             => ['str:150', 'unique'],
      'password'          => ['str:255'],
      'role'              => ['str:20'],
      'status'            => ['str:20'],
      'activation_key'    => ['txt'],
      'avatar'            => ['txt'],
      'registered_at'     => ['int'],
      'email_verified_at' => ['int'],
      'last_change_at'    => ['int', 'default:CURRENT_TIMESTAMP'],
    ]);
  }

  public function down(){
    Schema::dropTable('".AUTH_USERS_TABLE."');
  }
}";



/**
 * Login attempts migration
 */
$login_attempts_migration_content = "<?php
/**
 * ".ucfirst(AUTH_THROTTLE_TABLE)." migration
 */
use Ozz\Core\system\migration\Schema;

class ".ucfirst(AUTH_THROTTLE_TABLE)." {
  
  public function up(){
    Schema::createTable('".AUTH_THROTTLE_TABLE."', [
      'id'             => ['int', 'ai', 'primary'],
      'user_id'        => ['int'],
      'user_ip'        => ['str:255'],
      'type'           => ['str:50'],
      'status'         => ['str:20'],
      'user_agent'     => ['txt'],
      'user_agent_all' => ['txt'],
      'timestamp'      => ['int'],
    ]);
  }
  
  public function down(){
    Schema::dropTable('".AUTH_THROTTLE_TABLE."');
  }
}
";


/**
 * Auth Controller Generate Content
 */
$auth_controller_content = "<?php
namespace App\controller;

use Ozz\Core\Controller;
use Ozz\Core\Request;
use Ozz\Core\Auth;
use Ozz\Core\Validate;

class AuthController extends Controller {


  /**
   * Register / Create new user account
   */
  public function registerUser(Request \$request){
    \$form_data = \$request->input();
    set_flash('form_data', \$form_data);

    \$validate = Validate::check(\$form_data, [
      'first_name'            => 'req | max:55 | txt',
      'last_name'             => 'req | max:55 | txt',
      'username'              => 'req | max:55 | txt',
      'email'                 => 'req | email | max:60',
      'password'              => 'req | strong_password',
      'password_confirmation' => 'req | match:{password}'
    ]);

    if(\$validate->pass){
      extract(\$form_data);
      /**
       * Array keys of \$user_data should be same as DB fields
       * And should be configured on [app/config.php]
       */
      \$user_data = [
        'username'       => explode('@', \$email)[0],
        'first_name'     => \$first_name,
        'last_name'      => \$last_name,
        'email'          => \$email,
        'password'       => \$password,
        'role'           => 'admin',
        'status'         => 'pending',
        'activation_key' => sha1(random_str(36).time().\$email),
      ];

      if(Auth::register(\$user_data, false)){
        remove_flash('form_data');
        \$mail = Auth::sendVerificationMail([
          'email' => \$email,
          'name'  => \$first_name.' '.\$last_name,
          'url'   => BASE_URL.'verify-account/'.\$user_data['activation_key']
        ]);
      }
    }

    view('auth/sign-up');
  }



  /**
   * Verify user account
   */
  public function verifyUserAccount(Request \$request){
    \$data['status'] = Auth::verifyEmail(\$request->param('token'));

    view('auth/verify-account', \$data);
  }



  /**
   * Login User
   */
  public function loginUser(Request \$request){
    \$form_data = \$request->input();
    set_flash('form_data', \$form_data);
    extract(\$form_data);

    \$validate = Validate::check(\$form_data, [
      'email'     => 'req | email | max:60',
      'password'  => 'req | password',
    ]);

    if(\$validate->pass){
      Auth::login(\$email, \$password, false);
    }

    view('auth/login');
  }

}
";



/**
 * Signup View
 */
$signup_content = "<?php
/**
 * View Name: sign-up
 * Path: view/sign-up
 * 
 * @param array \$data arguments passed from controller
 * 
 */

\$value = has_flash('form_data') ? (object) get_flash('form_data') : false;

?>
{{ title        = \"Sign up | <?=APP_NAME?>\" }}
{{ keywords     = \"signup\" }}
{{ description  = \"Sign Up\" }}
{{ bodyClass    = \"auth\" }}

{{ content }}
  <section class=\"component auth-comp sign-up center\">
    <div class=\"container\">
      <h1 class=\"component__heading-1\">Sign Up</h1>
      <form action=\"<?=BASE_URL?>sign-up\" method=\"post\" class=\"form\">

        <?=CSRF_FIELD?>

        <div class=\"col2\">
          <div class=\"form-item\">
            <input type=\"text\" name=\"first_name\" placeholder=\"First Name\" value=\"<?=\$value->first_name ?? '' ?>\" class=\"<?=has_error('first_name') ? 'error' : false ?>\">
            <?=show_error('first_name', '<span class=\"error-label\">##</span>')?>
          </div>

          <div class=\"form-item\">
            <input type=\"text\" name=\"last_name\" placeholder=\"Last Name\" value=\"<?=\$value->last_name ?? '' ?>\" class=\"<?=has_error('last_name') ? 'error' : false ?>\">
            <?=show_error('last_name', '<span class=\"error-label\">##</span>')?>
          </div>
        </div>

        <div class=\"form-item\">
          <input type=\"email\" name=\"email\" placeholder=\"Email Address\" value=\"<?=\$value->email ?? '' ?>\" class=\"<?=has_error('email') ? 'error' : false ?>\">
          <?=show_error('email', '<span class=\"error-label\">##</span>')?>
          <?=show_error('username', '<span class=\"error-label\">##</span>')?>
        </div>

        <div class=\"form-item\">
          <input type=\"password\" name=\"password\" placeholder=\"Password\" autocomplete=\"new-password\" class=\"<?=has_error('password') ? 'error' : false ?>\">
          <?=show_error('password', '<span class=\"error-label\">##</span>')?>
        </div>

        <div class=\"form-item\">
          <input type=\"password\" name=\"password_confirmation\" placeholder=\"Confirm Password\" autocomplete=\"new-password\" class=\"<?=has_error('password_confirmation') ? 'error' : false ?>\">
          <?=show_error('password_confirmation', '<span class=\"error-label\">##</span>')?>
        </div>

        <div class=\"form-error\">
          <?=show_error('success', '<p class=\"message success\">##</p>'); ?>
        </div>

        <input type=\"submit\" value=\"Sign Up\">
      </form>
    </div>
  </section>

  <div class=\"off-bottom-link\">
    <p>Already have an account? <a href=\"<?=BASE_URL?>/login\" class=\"link\">Login</a></p>
  </div>
{{ content-end }}
";



/**
 * Login View
 */
$login_content = "<?php
/**
 * View Name: login
 * Path: view/login
 * 
 * @param array \$data arguments passed from controller
 * 
 */

\$value = has_flash('form_data') ? (object) get_flash('form_data') : false;

?>
{{ title        = \"Login | <?=APP_NAME?>\" }}
{{ keywords     = \"login\" }}
{{ description  = \"Login\" }}
{{ bodyClass    = \"auth\" }}

{{ content }}
  <section class=\"component auth-comp log-in center\">
    <div class=\"container\">
      <h1 class=\"component__heading-1\">Login</h1>
      <form action=\"<?=BASE_URL?>login\" method=\"post\" class=\"form\">

        <?=CSRF_FIELD?>

        <div class=\"form-item\">
          <input type=\"email\" name=\"email\" placeholder=\"Email\" value=\"<?=\$value->email ?? '' ?>\" class=\"<?=has_error('email') ? 'error' : false ?>\">
          <?=show_error('email', '<span class=\"error-label\">##</span>')?>
        </div>

        <div class=\"form-item\">
          <input type=\"password\" name=\"password\" placeholder=\"Password\" autocomplete=\"new-password\" value=\"<?=\$value->password ?? '' ?>\" class=\"<?=has_error('password') ? 'error' : false ?>\">
          <?=show_error('password', '<span class=\"error-label\">##</span>')?>
        </div>

        <div class=\"form-error\">
          <?=show_error('success', '<p class=\"message success\">##</p>'); ?>
          <?=show_error('error', '<p class=\"message danger\">##</p>'); ?>
        </div>

        <input type=\"submit\" value=\"Login\">
      </form>
    </div>
  </section>

  <div class=\"off-bottom-link\">
    <a href=\"<?=BASE_URL?>sign-up\" class=\"link\">Create an account</a> 
    &nbsp;| &nbsp;
    <a href=\"<?=BASE_URL?>forgot-password\" class=\"link\">Forgot Password?</a>
  </div>
{{ content-end }}
";



/**
 * Forgot Password View
 */
$forgot_pass_content = "<?php
/**
 * View Name: forgot-password
 * Path: view/auth/forgot-password
 * 
 * @param array \$data arguments passed from controller
 * 
 */
?>
{{ title        = \"Forgot Password | <?=APP_NAME?>\" }}
{{ bodyClass    = \"auth\" }}

{{ content }}
  <section class=\"component auth-comp forgot-password center\">
    <div class=\"container\">
      <h1 class=\"component__heading-1\">Forgot Password</h1>
      <p>Enter the email. We'll send you a link to reset your password</p>
      <form action=\"<?=BASE_URL?>forgot-password\" method=\"post\" class=\"form\">

        <?=CSRF_FIELD?>

        <div class=\"form-item\">
          <input type=\"email\" name=\"email\" placeholder=\"Email Address\">
        </div>

        <div class=\"form-error\">
          <?php show_errors('<p>##</p>'); ?>
        </div>

        <input type=\"submit\" value=\"Submit\">
      </form>
    </div>

  </section>

  <div class=\"off-bottom-link\">
    <a href=\"<?=BASE_URL?>/login\" class=\"link\">Back to Login</a>
  </div>
{{ content-end }}
";



/**
 * Reset Password View
 */
$reset_pass_content = "<?php
/**
 * View Name: reset-password
 * Path: view/auth/reset-password
 * 
 * @param array \$data arguments passed from controller
 * 
 */
?>
{{ title        = \"Reset Password | <?=APP_NAME?>\" }}
{{ bodyClass    = \"auth\" }}

{{ content }}
  <section class=\"component auth-comp reset-password center\">
    <div class=\"container\">
      <h1 class=\"component__heading-1\">Reset Password</h1>
      <form action=\"<?=BASE_URL?>reset-password\" method=\"post\" class=\"form\">

        <?=CSRF_FIELD?>

        <div class=\"form-item\">
          <input type=\"password\" name=\"password\" placeholder=\"New Password\" autocomplete=\"new-password\">
        </div>

        <div class=\"form-item\">
          <input type=\"password\" name=\"confirm_password\" placeholder=\"Confirm New Password\" autocomplete=\"new-password\">
        </div>

        <div class=\"form-error\">
          <?php show_errors('<p>##</p>'); ?>
        </div>

        <input type=\"submit\" value=\"Reset Password\">
      </form>
    </div>
  </section>
{{ content-end }}
";



/**
 * Verify Account/Email View
 */
$verify_account_content = "<?php
/**
 * View Name: verify-account
 * Path: view/auth/verify-account
 * 
 * @param array \$data arguments passed from controller
 * 
 */
?>
{{ title        = \"Verify Email Address | <?=APP_NAME?>\" }}
{{ bodyClass    = \"auth\" }}

{{ content }}
  <section class=\"component auth-comp verify-account center\">
    <div class=\"container\">
      <?php if(\$data['status'] == 'success') : ?>
        <h1 class=\"c-green\">Congratulations!</h1>
        <hr class=\"hr1\">
        <p>Your account verified successfully</p>
        <a href=\"<?=BASE_URL?>login\" class=\"button\">Login to your account</a>
      <?php elseif(\$data['status'] == 'disabled') : ?>
        <h1 class=\"c-orange\">Account is Disabled</h1>
        <hr class=\"hr1\">
        <p>Your account is disabled. Please contact support team for more information.</p>
      <?php elseif(\$data['status'] == 'invalid') : ?>
        <h1 CLASS=\"c-red\">Invalid Link</h1>
        <hr class=\"hr1\">
        <p>The verification link is invalid</p>
        <a href=\"<?=BASE_URL?>login\" class=\"button\">Go to Login page</a>
      <?php else : ?>
        <h1 CLASS=\"c-red\">Error</h1>
        <hr class=\"hr1\">
        <p>Something went wrong. There is an error on verifying your account!</p>
        <a href=\"<?=BASE_URL?>login\" class=\"button\">Go to Login page</a>
      <?php endif; ?>
    </div>
  </section>
{{ content-end }}
";



/**
 * Route Content
 */
$router_content = "

// Auth Routes
Router::get('".AUTH_LOGIN_PATH."', fn() => view('auth/login'));

Router::get('".AUTH_LOGOUT_PATH."', fn() => Ozz\Core\Auth::logout());

Router::get('".AUTH_SIGNUP_PATH."', fn() => view('auth/sign-up'));

Router::get('/forgot-password', fn() => view('auth/forgot-password'));

Router::get('/verify-account/{token}', [App\controller\AuthController::class, 'verifyUserAccount']);

Router::get('/reset-password/{token}', fn() => view('auth/reset-password'));

Router::post('".AUTH_LOGIN_PATH."', [App\controller\AuthController::class, 'loginUser']);

Router::post('".AUTH_SIGNUP_PATH."', [App\controller\AuthController::class, 'registerUser']);
";



/**
 * Account verification Email Template Content
 */
$account_verification_mail = "<!DOCTYPE html>
<html lang=\"en\">
<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width,initial-scale=1\">
  <meta name=\"x-apple-disable-message-reformatting\">
  <title>{{ title }}</title>
  <style>
    body {
      font-family: sans-serif;
      margin: 0;
      padding: 0;
      background: #eeeeee;
    }
    * {
      text-align: center;
    }
    .container {
      max-width: 80%;
      margin: 0 auto;
      background: #ffffff;
    }
    a {
      outline: none;
      text-decoration: none;
    }
    .button {
      display: inline-block;
      padding: 10px;
      background: #39B54A;
      color: #fff;
      border-radius: 5px;
    }
    .button:hover {
      background: #20862E;
    }
    em {
      opacity: 0.6;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class=\"container\">
    <h1>{{ title }}</h1>
    <hr>
    <p><strong>Hi {{ name }},</strong></p>
    <p>
      Welcome to {{ app_name }}<br>
      Please verify your email address to activate your account.<br><br>
      Click the link below to verify<br><br>
    </p>

    <a href=\"{{ url }}\" target=\"_blank\" class=\"button\">
      <span>{{ button_label }}</span>
    </a><br><br>

    <p>
      <em>If you didn't create an account on {{ app_name }}, Just ignore this email.</em>
    </p>
  </div>
</body>
</html>
";



/**
 * Password Reset Mail template
 */
$password_reset_mail = "<!DOCTYPE html>
<html lang=\"en\">
<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width,initial-scale=1\">
  <meta name=\"x-apple-disable-message-reformatting\">
  <title></title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
  </style>
</head>
<body>
  <h1>Password Reset Request</h1>
  <p>Hello {{ name }},</p>
  <p>{{ message }}</p>
</body>
</html>
";



/**
 * Reset Password Mail template (Throttle-Reset)
 */
$throttle_reset_mail = "<!DOCTYPE html>
<html lang=\"en\">
<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width,initial-scale=1\">
  <meta name=\"x-apple-disable-message-reformatting\">
  <title></title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
  </style>
</head>
<body>
  <h1>Please Reset Your Password</h1>
  <p>Hello {{ name }},</p>
  <P>We found too many wrong login attempts to your account. Please reset your password for protect your account.</p>
  <p>{{ message }}</p>
</body>
</html>
";



/**
 * New Login Security Alert Mail
 */
$new_login_security_alert_mail = "<!DOCTYPE html>
<html lang=\"en\">
<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width,initial-scale=1\">
  <meta name=\"x-apple-disable-message-reformatting\">
  <title></title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
  </style>
</head>
<body>
  <h1>New login from a different IP address</h1>
  <p>Hello {{ name }},</p>
  <p>{{ message }}</p>
</body>
</html>
";
