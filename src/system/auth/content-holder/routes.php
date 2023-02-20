<?php
/**
 * Auth initial route content
 */
$content = "
// Auth Routes
Router::get('/forgot-password', fn() => view('auth/forgot-password'));

Router::get('/verify-account/{token}', [App\controller\AuthController::class, 'verifyUserAccount']);

Router::get('/reset-password/{token}', fn() => view('auth/reset-password'));

Router::get('/logout', fn() => Auth::logout());

Router::getGroup(['auth'], null, [
  '/login' => fn() => view('auth/login'),
  '/sign-up' => fn() => view('auth/sign-up'),
  '/dashboard' => fn() => view('auth/dashboard', Auth::info())
]);

Router::postGroup(['auth'], [
  '/login' => [App\controller\AuthController::class, 'loginUser'],
  '/sign-up' => [App\controller\AuthController::class, 'registerUser'],
]);
";
