<?php
/**
 * Auth initial route content
 */
$content = "
// Auth Routes
use App\controller\\".AUTH_CONTROLLER.";

Router::get(AUTH_EMAIL_VERIFY_PATH.'/{token}', [".AUTH_CONTROLLER."::class, 'verifyUserAccount']);

Router::get(AUTH_RESET_PASSWORD_PATH.'/{token}', [".AUTH_CONTROLLER."::class, 'resetPassword']);

Router::get(AUTH_LOGOUT_PATH, fn() => Auth::logout());

Router::getGroup(['auth'], null, [
  AUTH_LOGIN_PATH    => fn() => view('auth/login'),
  AUTH_SIGNUP_PATH   => fn() => view('auth/sign-up'),
  AUTH_FORGOT_PASSWORD_PATH => fn() => view('auth/forgot-password'),
  '/dashboard'       => fn() => view('auth/dashboard', Auth::info())
]);

Router::postGroup(['auth'], [
  AUTH_LOGIN_PATH           => [".AUTH_CONTROLLER."::class, 'loginUser'],
  AUTH_SIGNUP_PATH          => [".AUTH_CONTROLLER."::class, 'registerUser'],
  AUTH_FORGOT_PASSWORD_PATH => [".AUTH_CONTROLLER."::class, 'passwordResetRequest'],
  AUTH_RESET_PASSWORD_PATH  => [".AUTH_CONTROLLER."::class, 'updateNewPassword'],
]);

";

?>