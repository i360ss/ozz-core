<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

trait Security {
  
  #---------------------------------------------------------------------
  // Check the request AJAX or not (Allow only Ajax requests)
  #---------------------------------------------------------------------
  protected function isAjax(){
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'){
      Request::statusCode(405);
      exit('Not an AJAX request'); // Not a Ajax request
    }
  }
  
  
  
  #---------------------------------------------------------------------
  // Check the request method
  #---------------------------------------------------------------------
  protected function isMethod($method){
    if(strtolower($_SERVER['REQUEST_METHOD']) !== strtolower($method)) {
      Request::statusCode(405);
      exit('Invalid Request Method');
    }
  }
  
  
  
  #---------------------------------------------------------------------
  // Check CSRF token validation
  #---------------------------------------------------------------------
  protected function csrf($fresh = false){
    $token = '';
    if (isset($_POST['csrf']) && !empty($_POST['csrf'])) {
      $csrfToken = $_POST['csrf'];
    }
    elseif (isset($_POST['csrf_token']) && !empty($_POST['csrf_token'])) {
      $csrfToken = $_POST['csrf_token'];
    }
    elseif(!empty($_SERVER["HTTP_X_CSRF_TOKEN"]) || !empty($_SERVER["HTTP_X_XSRF_TOKEN"]) ){
      $csrfToken = $_SERVER["HTTP_X_CSRF_TOKEN"];
    }
    else{
      Request::statusCode(401);
      exit('Token not found');
    }
    
    // Check Token
    if($csrfToken !== ""){
      if (hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        // Remove token and Generate new CSRF token
        if($fresh){
          unset($_SESSION["csrf_token"]);
          $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
      }
      else{
        Request::statusCode(401);
        exit('Invalid Token');
      }
    }
  }
  
  
  
  #---------------------------------------------------------------------
  // Check All Secrity checks
  #---------------------------------------------------------------------
  protected function allSecurity($method = 'post'){
    self::isAjax();
    self::csrf();
    self::isMethod($method);
  }
  
  
  
} // Security class END