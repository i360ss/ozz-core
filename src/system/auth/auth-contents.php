<?php
/**
 * Auth Migration Generate Content
 */
$auth_migration_content = "<?php
/**
 * ".ucfirst(APP_CONFIG['auth_config']['users_table'])." Migration
 */
use Ozz\Core\system\migration\Schema;

class ".ucfirst(APP_CONFIG['auth_config']['users_table'])." {

  public function up(){
    Schema::createTable('".APP_CONFIG['auth_config']['users_table']."', [
      'user_id'           => ['int', 'ai', 'primary'],
      'username'          => ['str:150'],
      'first_name'        => ['str:150'],
      'last_name'         => ['str:150'],
      'email'             => ['str:150', 'unique'],
      'password'          => ['txt'],
      'role'              => ['str:20'],
      'status'            => ['str:20'],
      'activation_key'    => ['txt'],
      'avatar'            => ['txt'],
      'registered_at'     => ['datetime'],
      'email_verified_at' => ['datetime'],
      'last_change_at'    => ['datetime'],
    ]);
  }

  public function down(){
    Schema::dropTable('".APP_CONFIG['auth_config']['users_table']."');
  }
}";



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
    \$data = [];
    \$form_data = \$request->input();
    set_flash('form_data', \$form_data);

    \$validate = Validate::check(\$form_data, [
      'first_name' => 'req|max:55|txt',
      'last_name' => 'req|max:55|txt',
      'username' => 'req|max:55|txt',
      'email' => 'req|email|max:60',
      'password' => 'req|strong_password',
      'password_confirmation' => 'req|match:{password}'
    ]);

    if(\$validate->pass){
      \$user_data = [
        'username'       => \$form_data['email'],
        'first_name'     => \$form_data['first_name'],
        'last_name'      => \$form_data['last_name'],
        'email'          => \$form_data['email'],
        'password'       => \$form_data['password'],
        'role'           => 'admin',
        'status'         => 'pending',
        'activation_key' => sha1(random_str(36).time().\$form_data['email']),
      ];

      if(Auth::createUser(\$user_data)){
        \$verification_mail = Auth::sendVerificationMail(\$user_data['email'], [
          'name'         => \$user_data['first_name'],
          'url'          => BASE_URL.'verify-account/'.\$user_data['activation_key'],
          'app_name'     => APP_NAME,
          'button_label' => trans('email_verification_button_label'),
          'attachments'  => false,
          'images'       => false,
        ]);

        if(!\$verification_mail){
          \$data['email_not_sent'] = true;
        }
      }
    }

    view('auth/sign-up', \$data);
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

    \$validate = Validate::check(\$form_data, [
      'email' => 'req|email|max:60',
      'password' => 'req|password',
    ]);

    if(\$validate->pass){
      Auth::login(\$form_data['email'], \$form_data['password']);
    }

    view('auth/login');
  }

}";



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
Router::get('/login', fn() => view('auth/login'));

Router::get('/logout', fn() => Ozz\Core\Auth::logout());

Router::get('/sign-up', fn() => view('auth/sign-up'));

Router::get('/forgot-password', fn() => view('auth/forgot-password'));

Router::get('/verify-account/{token}', [App\controller\AuthController::class, 'verifyUserAccount']);

Router::get('/reset-password/{token}', fn() => view('auth/reset-password'));

Router::post('/sign-up', [App\controller\AuthController::class, 'registerUser']);

Router::post('/login', [App\controller\AuthController::class, 'loginUser']);
";