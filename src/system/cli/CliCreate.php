<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\cli;

class CliCreate {

  private $createTo = SPC_BACK['core_2'].'app/';
  private $fallback_content = __DIR__.'/../content-holder/fallback_codegen.php';

  public function index($com){
    extract($com);

    if( in_array($r1, ['c:mvc', 'create:mvc', 'make:mvc']) ) {
      $this->createController($r2);
      $this->createModel($r2);
      $this->createView($r2);
    } elseif( in_array($r1, ['c:c', 'c:controller', 'create:controller', 'make:controller']) ) {
      $this->createController($r2);
    } elseif( in_array($r1, ['c:m', 'c:model', 'create:model', 'make:model']) ) {
      $this->createModel($r2);
    } elseif( in_array($r1, ['c:v', 'c:view', 'create:view', 'make:view']) ) {
      $this->createView($r2);
    } elseif( in_array($r1, ['c:vc', 'c:cv']) ) {
      $this->createController($r2);
      $this->createView($r2);
    } elseif( in_array($r1, ['c:mc', 'c:cm']) ) {
      $this->createController($r2);
      $this->createModel($r2);
    } elseif( in_array($r1, ['c:vm', 'c:mv']) ) {
      $this->createModel($r2);
      $this->createView($r2);
    } elseif( in_array($r1, ['c:middleware', 'c:md', 'create:middleware', 'make:middleware']) ) {
      $this->createMiddleware($r2);
    } elseif( in_array($r1, ['c:et', 'c:mail', 'c:email-temp', 'c:email-template', 'create:email-template', 'create:email-temp', 'make:email-template', 'make:email-temp', 'c:email-view']) ) {
      $this->createEmailTemplate($r2);
    } elseif( in_array($r1, ['c:layout', 'c:lay', 'c:base-layout']) ) {
      $this->createLayout($r2);
    } elseif( in_array($r1, ['c:component', 'c:comp', 'c:compo']) ) {
      $this->createComponent($r2);
    } else {
      ozz_console_error('Error on creating');
    }
  }

  /**
   * Create Controller
   */
  private function createController($name){
    if(!validate_file_name($name)){
      ozz_console_error('Invalid Controller Name');
      return false;
    }

    $fileName = $this->createTo.'controller/' .$name.'.php';
    $fileName_check = $this->createTo.'controller/' .ucfirst($name).'.php';
    $namespace = self::SetNamespace('App\controller\\'.$name);
    $nameFinal = explode("/", $name);
    $file_data = [
      'namespace' => $namespace,
      'class' => end($nameFinal)
    ];

    return $this->common_create('controller', $fileName_check, $fileName, $name, $file_data);
  }

  /**
   * Create View
   */
  private function createView($name){
    if(!validate_file_name($name)){
      ozz_console_error("Invalid View file name");
      return false;
    }

    $fileName = $this->createTo.'view/' .$name.'.phtml';
    $nameFinal = explode("/", $name);
    $file_data = [
      'name' => end($nameFinal),
      'path' => 'view/'.$name
    ];

    return $this->common_create('view', $fileName, $fileName, $name, $file_data);
  }

  /**
   * Create Model
   */
  private function createModel($name){
    if(!validate_file_name($name)){
      ozz_console_error("Invalid Model name");
      return false;
    }

    $fileName = $this->createTo.'model/' .$name.'.php';
    $fileName_check = $this->createTo.'model/' .ucfirst($name).'.php';
    $namespace = self::SetNamespace('App\model\\'.$name);
    $nameFinal = explode("/", $name);
    $file_data = [
      'namespace' => $namespace,
      'class' => end($nameFinal)
    ];

    return $this->common_create('model', $fileName_check, $fileName, $name, $file_data);
  }

  /**
   * Create Middleware
   */
  private function createMiddleware($name){    
    if(!validate_file_name($name)){
      ozz_console_error("Invalid Middleware Name");
      return false;
    }

    $fileName = $this->createTo.'middleware/' .$name.'.php';
    $fileName_check = $this->createTo.'middleware/' .ucfirst($name).'.php';
    $namespace = self::SetNamespace('App\middleware\\'.$name);
    $nameFinal = explode("/", $name);
    $file_data = [
      'namespace' => $namespace,
      'class' => end($nameFinal)
    ];

    return $this->common_create('middleware', $fileName_check, $fileName, $name, $file_data);
  }

  /**
   * Create Email Template
   */
  private function createEmailTemplate($name){
    if(!validate_file_name($name)){
      ozz_console_error("Invalid Email Template Name");
      return false;
    }

    $fileName = $this->createTo.'mail/'.$name.'.phtml';
    $nameFinal = explode("/", $name);
    $file_data = [
      'name' => end($nameFinal),
      'path' => 'mail/'.$name
    ];

    return $this->common_create('email_template', $fileName, $fileName, $name, $file_data);
  }

  /**
   * Create Base Layout (View)
   */
  private function createLayout($name){
    if(!validate_file_name($name)){
      ozz_console_error("Invalid Layout Name");
      return false;
    }

    $fileName = $this->createTo.'view/base/'.$name.'.phtml';
    $nameFinal = explode("/", $name);
    $file_data = [
      'name' => end($nameFinal),
      'path' => 'view/'.$name
    ];

    return $this->common_create('layout', $fileName, $fileName, $name, $file_data);
  }

  /**
   * Create Component (View)
   */
  private function createComponent($name){
    if(!validate_file_name($name)){
      ozz_console_error("Invalid Component Name");
      return false;
    }

    $fileName = $this->createTo.'view/components/'.$name.'.phtml';
    $nameFinal = explode("/", $name);
    $file_data = [
      'name' => end($nameFinal),
      'path' => 'view/components/'.$name
    ];

    return $this->common_create('component', $fileName, $fileName, $name, $file_data);
  }

  /**
   * Create File
   * @param string $typ File type
   * @param string $fullName File full name with path
   * @param DOM $content Content to be added to the file
   * @param string $name file name only
   */
  private static function Create($typ, $fullName, $content, $name){
    $DS = DIRECTORY_SEPARATOR;
    $dirName = explode("/", $fullName);
    $dirArr = array_slice($dirName, 0, -1);
    $dir = implode($DS, $dirArr);

    if(PHP_OS_FAMILY === 'Windows' && !is_dir(__DIR__.$dir)){
      mkdir(__DIR__.$dir.$DS, 0777, true);
    } elseif(!file_exists(__DIR__.$dir)) {
      mkdir(__DIR__.$dir.$DS, 0755, true);
    }

    if($typ == "controller" || $typ == "model" || $typ == "middleware"){
      $onlyName = substr($fullName, strrpos($fullName, '/') + 1); // Only Name
      $onlyName = ucwords($onlyName);

      $onlyDir = explode('/', $fullName);
      array_pop($onlyDir);

      $fileFinalPath = implode($DS, $onlyDir).$DS.$onlyName;
    } else {
      $fileFinalPath = $fullName;
    }

    // Create File
    $fl = fopen(__DIR__.$fileFinalPath, 'w');
    fwrite($fl, $content);
    if(fclose($fl)){
      return ozz_console_success("$typ $name Created successfully");
    } else {
      return ozz_console_error("$typ not generated");
    }
  }

  /**
   * Set Namespace for creating Class
   */
  private static function SetNamespace($fullName){
    $namespace = explode("/", $fullName);
    if (count($namespace) < 2) {
      $namespace = explode("\\", $namespace[0]);
    }
    array_pop($namespace);
    $namespace = join("\\", $namespace);
    return $namespace;
  }

  /**
   * Common pre-create
   */
  private function common_create($typ, $fileName_check, $fileName, $name, $file_data){
    if(!file_exists(__DIR__.$fileName_check)){

      file_exists(__DIR__.$this->createTo.'codegen.php') 
        ? require_once __DIR__.$this->createTo.'codegen.php' 
        : false;

      if(is_callable('ozz_code_gen_'.$typ)){
        $content = call_user_func('ozz_code_gen_'.$typ, $file_data);
      } else {
        require_once $this->fallback_content;
        $content = Content($typ, $file_data);
      }

      return self::Create($typ, $fileName, $content, $name);
    } else{
      return ozz_console_warn("Controller $name already exist");
    }
  }

}
(new CliCreate)->index($com);