<?php
/**
 * Auth Middleware generative content
 */
$middlewareName = substr(AUTH_MIDDLEWARE_NAME, -4) == '.php' ? substr(AUTH_MIDDLEWARE_NAME, 0, -4) : AUTH_MIDDLEWARE_NAME;
$content = "<?php
namespace App\middleware;

use Ozz\Core\Request;
use Ozz\Core\Response;
use Ozz\Core\Router;
use Ozz\Core\Auth;

class ".ucfirst($middlewareName)." {

  public function handle(Request \$request, Response \$response){
    // Pages to be denied to logged in users
    \$deniedPages = [
      AUTH_LOGIN_PATH,
      AUTH_SIGNUP_PATH,
      AUTH_FORGOT_PASSWORD_PATH,
      AUTH_RESET_PASSWORD_PATH,
    ];

    \$loggedIn = Auth::isLoggedIn();
    \$isDeniedPage = in_array(\$request->path(), \$deniedPages);

    if(\$loggedIn && \$isDeniedPage){
      return Router::redirect(Auth::getLandingPage());
    } elseif(!\$loggedIn && !\$isDeniedPage){
      return Router::redirect(AUTH_LOGIN_PATH);
    }
  }

}
";