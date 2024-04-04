<?php
namespace App\middleware;

use Ozz\Core\Auth;

class AdminAccessMiddleware {

  public function handle(){
    if (Auth::info('role') !== 'admin') {
      return redirect('/404');
    }
  }
}