<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Err {

  // Error Template Container
  private static $eachContainer = "<div style='margin: 15px auto;'>";
  private static $eachContainerEnd = "</div>";
  
  // Error Message Template
  public static $errorTmp = "
  <div class='ozz_errorOutput' 
  style='padding:10px 20px; 
  max-width: 750px;
  margin: 3px auto;
  border: 1px solid #f8cdcb;
  background:#fceceb;'>
  <code><strong><h3 style='color: #722c2c;'>";
  public static $errorTmpEnd = "</code></strong></h3></div>";

  // Error Info Template
  public static $errInfo = "
  <div  style='padding:10px 20px; 
  max-width: 750px;
  margin: 3px auto;
  color: #000;
  font-size: 14px;
  border: 1px solid #ddd;
  background:#dee;'><code>";
  public static $errInfoEnd = "</code></div>";

  // Error Note Template
  public static $errNote = "
  <div style='padding:10px 20px; 
  max-width: 750px;
  margin: 3px auto;
  color: #666;
  font-size: 14px;
  border: 1px solid #ddd;
  background:#eee;'><code><em>";
  public static $errNoteEnd = "</em></code></div>";


  private static function renderErr($errData){
    extract($errData);
    if(DEBUG){
      echo self::$eachContainer;
      echo self::$errorTmp . '<strong>Error: </strong>' . $msg . self::$errorTmpEnd;
      echo self::$errInfo . '<strong>Info: </strong>' . $info . self::$errInfoEnd;
      echo self::$errNote . '<strong>Note: </strong>' . $note . self::$errNoteEnd;
      echo self::$eachContainerEnd;
    }
  }


  // Custom Exception
  public static function custom($i) {
    return self::renderErr($i);
  }


  // Commen Exceptions
  ///////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////

  // Base template file Not Found Error
  public static function baseTemplateNotFound($i){
    return self::renderErr([
      "msg" => "Base Template file not found <br> File Name: $i",
      "info" => "More information about the issue and how to fix it",
      "note" => "Additional Notes"
    ]);
  }
  
  // View file Not Found Error
  public static function viewNotFound($i){
    return self::renderErr([
      "msg" => "View file not found <br> File Name: $i",
      "info" => "More information about the issue and how to fix it",
      "note" => "Additional Notes"
    ]);
  }
  
  // Component name
  public static function componentNotFound($i){
    return self::renderErr([
      "msg" => "(".$i.") Component file Not found",
      "info" => "More information about the issue and how to fix it",
      "note" => "Additional Notes"
    ]);
  }
  
  // User Role Not Found
  public static function userRoleNotFound($i){
    return self::renderErr([
      "msg" => "User role not found <br> Role Name: $i",
      "info" => "More information about the issue and how to fix it",
      "note" => "Additional Notes"
    ]);
  }
  
  // User landing page not defined or not set in session
  public static function userLandingPageNotFound($i){
    return self::renderErr([
      "msg" => "User landing page not defined or not set in session",
      "info" => "More information about the issue and how to fix it",
      "note" => "Additional Notes"
    ]);
  }
  
  // Invalid Middleware name
  public static function invalidMiddleware($i){
    return self::renderErr([
      "msg" => "(".$i.") Middleware Not registered",
      "info" => "More information about the issue and how to fix it",
      "note" => "Additional Notes"
    ]);
  }

  // It is not a directory
  public static function notDir($i){
    return self::renderErr([
      "msg" => "[ $i ] is Not a valid directory",
      "info" => "More information about the issue and how to fix it",
      "note" => "Additional Notes"
    ]);
  }

  // Invalid Array Key
  public static function invalidArrayKey($i){
    return self::renderErr([
      "msg" => "[ $i ] is Not a valid array key",
      "info" => "More information about the issue and how to fix it",
      "note" => "Additional Notes"
    ]);
  }

  // File Handling Exceptions
  ///////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////

  // Invalid File type defined to upload
  public static function invalidFileTypeDefinedToUpload($i){
    return self::renderErr([
      "msg" => "Invalid file type defined on: File::upload()<br>1st Parameter '$i' is not a valid file type",
      "info" => "More information about the issue and how to fix it",
      "note" => "Additional Notes"
    ]);
  }

  // Upload settings parameters not provided
  public static function paramsRequiredForUploadSettings($i){
    return self::renderErr([
      "msg" => "Not provided enough parameters on [ $i ]",
      "info" => "Please provide at least one of thies params (keep_original, copies)",
      "note" => "Additional Notes"
    ]);
  }

}