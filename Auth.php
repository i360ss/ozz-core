<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\core;

class Auth extends Authentication {
  
  # ----------------------------------------------------
  // Register User All Roles of application
  # ----------------------------------------------------
  public static function registerUserRole($userRole, $args=[], $land=""){
    self::$validUserRoles[$userRole] = $args;
    // self::$validUserRoles[$userRole]['userLandingPage'] = $land;
    $land == "" ? self::$validUserRoles[$userRole]['userLandingPage'] = 'home' : $land;
  }
  
  
  
  # ----------------------------------------------------
  // User Login Attempt
  # ----------------------------------------------------
  protected static function login($thisRole, $userTable, $checkDataFrom){
    if(self::isLoggedIn($thisRole)){
      Router::redirect(self::$validUserRoles[$thisRole]['userLandingPage']);
    }
    else{
      return Authentication::AuthenticateLoginAttempt($thisRole, $userTable, $checkDataFrom);
    }
  }
  
  
  
  # ----------------------------------------------------
  // Password Reset Attempt
  # ----------------------------------------------------
  protected static function resetAttempt($thisRole, $userTable, $checkDataFrom){
    return Authentication::ValidateUserForResetPassword();
  }
  
  
  
  # ----------------------------------------------------
  // Check if user logged in (User role must be provided to check)
  # ----------------------------------------------------
  public static function isLoggedIn($role){
    if(array_key_exists($role, self::$validUserRoles)):
      return (isset($_SESSION["userRole"]) && $_SESSION["userRole"] == $role) ? true : false;
    else:
      return false;
    endif;
  }
  
  
  
  # ----------------------------------------------------
  // Get Loged in User Info
  # ----------------------------------------------------
  public static function loggedUser($role){
    $userInfo = [];
    if(isset($_SESSION["userRole"]) && $_SESSION["userRole"] == $role){
      if(isset($_SESSION['loggedUser']) && !empty($_SESSION['loggedUser'])){
        $userInfo = [
          'role' => $_SESSION["userRole"],
          'userId' => $_SESSION["userId"],
          'username' => $_SESSION['loggedUser'],
          'loggedDate' => $_SESSION['loggedDate'],
          'loggedTime' => $_SESSION['loggedTime']
        ];
      }
      else{
        return false;
      }
    }
    else{
      return false;
    }
    
    return $userInfo;
  }
  
  
  
  # ----------------------------------------------------
  // Log Out
  # ----------------------------------------------------
  public static function logout($goTo='/login'){
    unset($_SESSION["userRole"]);
    unset($_SESSION['userId']);
    unset($_SESSION['loggedUser']);
    unset($_SESSION['loggedDate']);
    unset($_SESSION['loggedTime']);
    header("Location:$goto");
  }
  
  
}