<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Auth extends Model {

  use DB;


  /**
   * Create/Register New User
   * @param array $user_data User Data (email, username, password, first_name, last_name, role, status, avatar)
   */
  public static function createUser($user_data){
    $db = (new static)->DB();
    $users = $db->select('users', '*');

    return $users;
  }



  /**
   * Send verification Email
   */
  public static function sendVerificationMail(){

  }



  /**
   * Verify User (Email)
   */
  public static function verifyEmail(){

  }



  /**
   * Verify Password
   */
  public static function verifyPassword(){

  }



  /**
   * Login User
   */
  public static function login(){

  }



  /**
   * Reset Password
   */
  public static function resetPassword(){

  }



  /**
   * Change Password
   */
  public static function changePassword(){

  }



  /**
   * Change Email
   */
  public static function changeEmail(){

  }



  /**
   * Change Status
   */
  public static function changeStatus(){

  }



  /**
   * Change Role
   */
  public static function changeRole(){

  }



  /**
   * User Information
   */
  public static function info(){

  }



  /**
   * Logout User
   */
  public static function logout(){

  }



  /**
   * Delete User Account
   */
  public static function deleteUser(){

  }

}