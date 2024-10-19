<?php
namespace App\middleware;

use Ozz\Core\Auth;

class AdminAccessMiddleware {

  public function handle(){
    if (!is_role('admin')) {
      return render_error_page(404);
    }
  }
}