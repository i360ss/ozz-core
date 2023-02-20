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

      if(Auth::register(\$user_data, false)){
        remove_flash('form_data');
      }
    }

    view('auth/sign-up');
  }



  /**
   * Verify user account
   */
  public function verifyUserAccount(Request \$request){
    \$data['status'] = Auth::verifyEmail(\$request->url_param('token'));

    view('auth/verify-account', \$data);
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

    if(\$validation->pass){
      Auth::login(\$email, \$password, false);
    }

    Router::redirect('/login');
  }

}
";