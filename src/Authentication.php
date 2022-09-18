<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Authentication extends Model {
  
  protected static $validUserRoles = []; // This will return all the registered user roles
  protected static $response = [];
  
  
  # ----------------------------------------------------
  // Login Attempt Authentication
  # ----------------------------------------------------
  protected static function AuthenticateLoginAttempt($thisRole, $userTable, $checkDataFrom){
    
    if(array_key_exists($thisRole, self::$validUserRoles)){
      require '../app/Error.php';
      // Full Final Response Array of Login Attempt
      self::$response = [
        'status' => false,
        'success' => '',
        'warning' => '',
        'error'=> 0,
        'redirect' => ''
      ];
        
      $login_credentials = Help::formData(); // Get Login Credentials
      $validData = Validate::validateForm($login_credentials); // Validate Login Credentials
          
      extract($validData);
          
      foreach ($username as $k => $v) {
        if(!$v){
          if($k !== 'value'){
            self::$response['error'] = USERNAME_ERR[$k];
            break;
          }
        }
      }
          
      foreach ($password as $k => $v) {
        if(!$v){
          if($k !== 'value'){
            self::$response['error'] = PASSWORD_ERR[$k];
            break;
          }
        }
      }
    
  
      # ------------------------------------
      // Check Auth Credentials
      # ------------------------------------ 
      if(self::$response['error'] == 0){
        // Connect to Database
        $DB = new Model();
        switch (self::$db_type) {
          case 'mysql':
            $DB = $DB->mysql();
            break;
            
          case 'sqlite':
            $DB = $DB->sqlite();
            break;
        }
    
        // Fetch User
        $availableUser = $DB->select(
          $userTable,
          [ $checkDataFrom[0], $checkDataFrom[1], 'id' ],
          [ $checkDataFrom[0] => $username['value'], "AND" => $checkDataFrom['arg'] ]
        );
      
        // Check User existance
        if(count($availableUser) > 0){
          if(password_verify($password['value'], $availableUser[0][$checkDataFrom[1]])){
            # ------------------------------------
            // Loged In Successfuly
            # ------------------------------------
            $_SESSION["userRole"] = $thisRole;
            $_SESSION['userId'] = $availableUser[0]['id'];
            $_SESSION['loggedUser'] = $username['value'];
            $_SESSION['loggedDate'] = date('d-m-Y');
            $_SESSION['loggedTime'] = date('h:m:s');
            
            // Remember Me
            // if(isset($remember) && $remember === true){
              
            //     $client = Help::client();
            //     dump($client);
            //     exit;
            
            //     setcookie();
            // }
            
            self::$response = [
              'status' => true,
              'success' => 'Login Success',
              'warning' => '',
              'error'=> 0,
              'redirect' => self::$validUserRoles[$thisRole]['userLandingPage']
            ];
          }
          else{
            # ------------------------------------
            // Invalid Pasword
            # ------------------------------------
            self::$response['error'] = PASSWORD_ERR['invalid'];
          }
        }
        else{
          # ------------------------------------
          // Invalid Username or Email
          # ------------------------------------
          self::$response['error'] = USERNAME_ERR['invalid'];
        }
        // Response
        return self::$response;
      }
      else{
        // Validation errors
        return self::$response;
      }
    }
    else{
      // Invalid User Role
      return Err::userRoleNotFound($thisRole);
    }
  }
          
          
          
  # ----------------------------------------------------
  // Password Reset Authentication
  # ----------------------------------------------------
  protected static function ValidateUserForResetPassword(){
    // Validate user credentials to reset password
    // Return True or False with a data object
  }


}