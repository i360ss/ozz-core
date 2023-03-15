<?php
/**
 * Auth Controller generative content
 */
$controllerName = substr(AUTH_CONTROLLER, -4) == '.php' ? substr(AUTH_CONTROLLER, 0, -4) : AUTH_CONTROLLER;
$content = "<?php
namespace App\controller;

use Ozz\Core\Controller;
use Ozz\Core\Request;
use Ozz\Core\Router;
use Ozz\Core\Auth;
use Ozz\Core\Validate;

class ".ucfirst($controllerName)." extends Controller {

  /**
   * Register / Create new user account
   */
  public function registerUser(Request \$request){
    \$form_data = \$request->input();
    set_flash('form_data', \$form_data);

    \$validation = Validate::check(\$form_data, [
      'first_name'            => 'req | max:55 | txt',
      'last_name'             => 'req | max:55 | txt',
      'username'              => 'req | max:55 | txt',
      'email'                 => 'req | email | max:60',
      'password'              => 'req | strong_password',
      'password_confirmation' => 'req | match:{password}'
    ]);

    if(\$validation->pass){
      extract(\$form_data);
      \$user_data = [
        'first_name' => \$first_name,
        'last_name'  => \$last_name,
        'email'      => \$email,
        'password'   => \$password,
      ];

      Auth::register(\$user_data, false);
    }

    return view('auth/sign-up');
  }



  /**
   * Verify user account
   */
  public function verifyUserAccount(Request \$request){
    \$data['status'] = Auth::verifyEmail(\$request->urlParam('token'));

    return view('auth/verify-account', \$data);
  }



  /**
   * Login User
   */
  public function loginUser(Request \$request){
    \$form_data = \$request->input();
    set_flash('form_data', \$form_data);
    extract(\$form_data);

    \$validation = Validate::check(\$form_data, [
      'email'     => 'req | email | max:60',
      'password'  => 'req | password',
    ]);

    \$validation->pass ? Auth::login(\$email, \$password) : false;

    return Router::redirect('".AUTH_LOGIN_PATH."');
  }



  /**
   * Password reset request
   */
  public function passwordResetRequest(Request \$request){
    \$form_data = \$request->input();
    set_flash('form_data', \$form_data);

    \$validation = Validate::check(\$form_data, [
      'email' => 'req | email'
    ]);

    \$validation->pass ? Auth::passwordResetAttempt(\$form_data['email']) : false;

    return Router::redirect('".AUTH_FORGOT_PASSWORD_PATH."');
  }



  /**
   * Reset password
   */
  public function resetPassword(Request \$request){
    \$data['status'] = Auth::validateResetToken(\$request->urlParam('token'));
    \$data['token'] = \$request->urlParam('token');

    return view('auth/reset-password', \$data);
  }



  /**
   * Update new password
   */
  public function updateNewPassword(Request \$request){
    \$token = \$request->input('token');
    \$data['token'] = \$token;
    \$data['status'] = Auth::validateResetToken(\$token);

    \$validate = Validate::check(\$request->input(), [
      'password' => 'req | strong_password',
      'confirm_password' => 'req | match:{password}',
    ]);

    if(\$validate->pass){
      if(\$data['status'] == 'valid_token'){
        if(Auth::changePasswordByToken(\$token, \$request->input('password'))){
          \$data['status'] = 'success';
        }
      } else {
        set_error('error', trans_e('invalid_reset_token'));
      }
    }

    return view('auth/reset-password', \$data);
  }



}
";