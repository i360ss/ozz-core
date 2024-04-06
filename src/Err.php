<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Err {

  public static $instance;

  // Exception dom to render on response
  public static $exception_doms = [];

  // Error Template Container
  private static $eachContainer = "<div style='margin: 15px auto;position: relative;z-index: 99999;background: #fff; padding: 10px 0;'>";
  private static $eachContainerEnd = "</div>";
  
  // Error Message Template
  public static $errorTmp = "
  <div class='ozz_errorOutput' 
  style='padding:10px 20px; 
  max-width: 900px;
  margin: 3px auto;
  border: 1px solid #FF5968;
  background: #FF5968;'>
  <code><strong><h3 style='color: #fff;'>";
  public static $errorTmpEnd = "</code></strong></h3></div>";

  // Error Info Template
  public static $errInfo = "
  <div style='padding:10px 20px; 
  max-width: 900px;
  margin: 3px auto;
  color: #fff;
  font-size: 14px;
  line-height: 1.7;
  border: 1px solid #666EE8;
  background:#666EE8;'><code>";
  public static $errInfoEnd = "</code></div>";

  // Error Note Template
  public static $errNote = "
  <div style='padding:10px 20px; 
  max-width: 900px;
  margin: 3px auto;
  color: #666;
  font-size: 14px;
  line-height: 1.7;
  border: 1px solid #f1f2f6;
  background:#f1f2f6;'><code><em>";
  public static $errNoteEnd = "</em></code></div>";

  /**
   * Single Err instance
   */
  public static function getInstance() {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  /**
   * Wrap and log errors to render
   */
  private static function renderErr($errData){
    if(DEBUG){
      extract($errData);

      $this_dom = self::$eachContainer;
      $this_dom .= (isset($msg) && $msg !== false) ? self::$errorTmp . '<strong>Error: </strong>' . $msg . self::$errorTmpEnd : '';
      $this_dom .= (isset($info) && $info !== false) ? self::$errInfo . '<strong>Info: </strong>' . $info . self::$errInfoEnd : '';
      $this_dom .= (isset($note) && $note !== false) ? self::$errNote . '<strong>Note: </strong>' . $note . self::$errNoteEnd : '';
      $this_dom .= self::$eachContainerEnd;

      self::$exception_doms[] = $this_dom;
    } else {
      exit('Error: Something went wrong');
    }
  }

  // Custom Exception
  public static function custom($i) {
    return DEBUG ? self::renderErr($i) : exit('Error: Something went wrong');
  }

  // Base template file Not Found Error
  public static function baseTemplateNotFound($i){
    return self::renderErr([
      "msg" => "Base Template file (app/view/base/".$i.".phtml) not found",
      "info" => "Please create your base template first. Run [ php ozz c:lay ".$i." ]",
    ]);
  }

  // View file Not Found Error
  public static function viewNotFound($i){
    return self::renderErr([
      "msg" => "View file (app/view/".$i.".phtml ) not found",
      "info" => "Please create your view file [ php ozz c:v ".$i." ]",
      "note" => "View directory should contain only PHTML files"
    ]);
  }

  // Component name
  public static function componentNotFound($i){
    return self::renderErr([
      "msg" => "Component (view/components/".$i.") Not found",
      "info" => "Please create your component first. Run [ php ozz c:comp ".$i." ]",
      "note" => "Your component must be a PHTML or HTML file"
    ]);
  }

  // Invalid Middleware name
  public static function invalidMiddleware($i){
    return self::renderErr([
      "msg" => "Middleware (app/middleware/".$i.") Not registered",
      "info" => "First create your middleware and register it on [ app/RegisterMiddleware.php ]",
      "note" => "To create a middleware just run [ php ozz c:md ".$i." ]"
    ]);
  }

  // It is not a directory
  public static function notDir($i){
    return self::renderErr([
      "msg" => "[ $i ] is Not a valid directory",
      "info" => "Please re-check directory name or create the directory",
    ]);
  }

  // Invalid Array Key
  public static function invalidArrayKey($i){
    return self::renderErr([
      "msg" => "[ $i ] is Not a valid array key",
      "info" => "Please check the array key for any typo",
    ]);
  }

  // Invalid File type defined to upload
  public static function invalidFileTypeDefinedToUpload($i){
    return self::renderErr([
      "msg" => "Invalid file type defined on: File::upload()<br>1st Parameter '$i' is not a valid file type",
      "info" => "Please refer the documentation for more info",
    ]);
  }

  // Upload settings parameters not provided
  public static function paramsRequiredForUploadSettings($i){
    return self::renderErr([
      "msg" => "Not provided enough parameters on [ $i ]",
      "info" => "Please provide at least one of this params (keep_original, copies)",
    ]);
  }

}