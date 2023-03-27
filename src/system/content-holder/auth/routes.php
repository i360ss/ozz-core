<?php
/**
 * Auth initial route content
 */
$content = "
// Auth Routes
use App\controller\\".AUTH_CONTROLLER.";

Router::get(AUTH_PATHS['verify_account'].'/{token}', [".AUTH_CONTROLLER."::class, 'verifyUserAccount']);

Router::get(AUTH_PATHS['reset_password'].'/{token}', [".AUTH_CONTROLLER."::class, 'resetPassword']);

Router::get(AUTH_PATHS['logout'], fn() => Auth::logout());

Router::getGroup(['auth'], null, [
  AUTH_PATHS['login']    => fn() => view('auth/login'),
  AUTH_PATHS['signup']   => fn() => view('auth/sign-up'),
  AUTH_PATHS['forgot_password'] => fn() => view('auth/forgot-password'),
  '/dashboard'       => fn() => view('auth/dashboard', Auth::info())
]);

Router::postGroup(['auth'], [
  AUTH_PATHS['login']           => [".AUTH_CONTROLLER."::class, 'loginUser'],
  AUTH_PATHS['signup']          => [".AUTH_CONTROLLER."::class, 'registerUser'],
  AUTH_PATHS['forgot_password'] => [".AUTH_CONTROLLER."::class, 'passwordResetRequest'],
  AUTH_PATHS['reset_password']  => [".AUTH_CONTROLLER."::class, 'updateNewPassword'],
]);

";

?>