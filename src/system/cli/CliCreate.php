<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\cli;

class CliCreate {
  
  private $createTo = SPC_BACK['core_2'].'app/';
  
  public function index($com){
    extract($com);
    if($r1 == 'c:mvc' || $r1 == 'create:mvc' || $r1 == 'make:mvc'){
      $this->createController($r2);
      $this->createModel($r2);
      $this->createView($r2);
    }
    elseif($r1 == 'c:c' || $r1 == 'c:controller' || $r1 == 'create:controller' || $r1 == 'make:controller'){
      $this->createController($r2);
    }
    elseif($r1 == 'c:m' || $r1 == 'c:model' || $r1 == 'create:model' || $r1 == 'make:model'){
      $this->createModel($r2);
    }
    elseif($r1 == 'c:v' || $r1 == 'c:view' || $r1 == 'create:view' || $r1 == 'make:view'){
      $this->createView($r2);
    }
    elseif($r1 == 'c:vc' || $r1 == 'c:cv'){
      $this->createController($r2);
      $this->createView($r2);
    }
    elseif($r1 == 'c:mc' || $r1 == 'c:cm'){
      $this->createController($r2);
      $this->createModel($r2);
    }
    elseif($r1 == 'c:vm' || $r1 == 'c:mv'){
      $this->createModel($r2);
      $this->createView($r2);
    }
    elseif($r1 == 'c:middleware' || $r1 == 'c:md' || $r1 == 'create:middleware' || $r1 == 'make:middleware'){
      $this->createMiddleware($r2);
    }
    elseif($r1 == 'c:et' || $r1 == 'c:email-temp' || $r1 == 'c:email-template' || $r1 == 'create:email-template' || $r1 == 'create:email-temp' || $r1 == 'make:email-template' || $r1 == 'make:email-temp' || $r1 == 'c:email-view'){
      $this->createEmailTemplate($r2);
    }
    elseif($r1 == 'c:layout' || $r1 == 'c:lay' || $r1 == 'c:base-layout'){
      $this->createLayout($r2);
    }
    elseif($r1 == 'c:component' || $r1 == 'c:comp' || $r1 == 'c:compo'){
      $this->createComponent($r2);
    }
    else{
      $this->createError();
    }
    
  }
  
  
  
  # ------------------------------------
  // Create Controller
  # ------------------------------------
  private function createController($name){
    if(!valid_file_name($name)){
      ozz_console_error('Invalid Controller Name');
      return false;
    }
    
    $fileName = $this->createTo.'controller/' .$name.'.php';
    $namespace = self::SetNamespace('App\controller\\'.$name);
    $nameFinal = explode("/", $name);
    
    if(!file_exists($fileName)){
      require_once __DIR__.$this->createTo.'GenerateContent.php'; // Generating Contents
      $content = Content('controller', [
        'namespace' => $namespace,
        'class' => end($nameFinal)
      ]); // Generating Contents
      
      if(self::Create('controller', $fileName, $content)){
        ozz_console_success("Controller $name Created successfully");
      }
      else{
        ozz_console_error('Controller not generated');
      }
    }
    else{
      ozz_console_warn("Controller $name already exist");
    }
    
  }
  
  
  
  # ------------------------------------
  // Create View
  # ------------------------------------
  private function createView($name){    
    if(!valid_file_name($name)){
      ozz_console_error("Invalid View file name");
      return false;
    }
    
    $fileName = $this->createTo.'view/' .$name.'.phtml';
    $nameFinal = explode("/", $name);
    
    if(!file_exists($fileName)){
      require_once __DIR__.$this->createTo.'GenerateContent.php'; // Generating Contents
      $content = Content('view', [
        'name' => end($nameFinal),
        'path' => 'view/'.$name
      ]); // Generating Contents
      
      if(self::Create('view', $fileName, $content)){
        ozz_console_success("View $name Created successfully");
      }
      else{ 
        ozz_console_error("View file not generated");
      }
    }
    else{
      ozz_console_warn("View $name already exist");
    }
  }
  
  
  
  # ------------------------------------
  // Create View
  # ------------------------------------
  private function createModel($name){
    if(!valid_file_name($name)){
      ozz_console_error("Invalid Model name");
      return false;
    }
    
    $fileName = $this->createTo.'model/' .$name.'.php';
    $namespace = self::SetNamespace('App\model\\'.$name);
    $nameFinal = explode("/", $name);
    
    if(!file_exists($fileName)){
      require_once __DIR__.$this->createTo.'GenerateContent.php'; // Generating Contents
      $content = Content('model', [
        'namespace' => $namespace,
        'class' => end($nameFinal)
      ]); // Generating Contents
      
      if(self::Create('model', $fileName, $content)){
        ozz_console_success("Model $name Created successfully");
      }
      else{
        ozz_console_error("Model not generated");
      }
    }
    else{
      ozz_console_warn("Model $name already exist");
    }
  }
  
  
  
  # ------------------------------------
  // Create View
  # ------------------------------------
  private function createMiddleware($name){    
    if(!valid_file_name($name)){
      ozz_console_error("Invalid Middleware Name");
      return false;
    }
    
    $fileName = $this->createTo.'middleware/' .$name.'.php';
    $namespace = self::SetNamespace('App\middleware\\'.$name);
    $nameFinal = explode("/", $name);
    
    if(!file_exists($fileName)){
      require_once __DIR__.$this->createTo.'GenerateContent.php'; // Generating Contents
      $content = Content('middleware', [
        'namespace' => $namespace,
        'class' => end($nameFinal)
      ]); // Generating Contents
      
      if(self::Create('middleware', $fileName, $content)){
        ozz_console_success("Middleware $name Created successfully");
      }
      else{
        ozz_console_error("Middleware not generated");
      }
    }
    else{
      ozz_console_warn("Middleware $name already exist");
    }
  }



  # ------------------------------------
  // Create Email Template
  # ------------------------------------
  private function createEmailTemplate($name){
    if(!valid_file_name($name)){
      ozz_console_error("Invalid Email Template Name");
      return false;
    }
    
    $fileName = $this->createTo.'email_template/'.$name.'.phtml';
    
    if(!file_exists($fileName)){
      require_once __DIR__.$this->createTo.'GenerateContent.php'; // Generating Contents
      $content = Content('email_template', false); // Generating Contents
      
      if(self::Create('email_template', $fileName, $content)){
        ozz_console_success("Email Template $name Created successfully");
      }
      else{
        ozz_console_error("Email Template not generated");
      }
    }
    else{
      ozz_console_warn("Email Template $name already exist");
    }
  }



  # ------------------------------------
  // Create Base Layout (View)
  # ------------------------------------
  private function createLayout($name){
    if(!valid_file_name($name)){
      ozz_console_error("Invalid Layout Name");
      return false;
    }
    
    $fileName = $this->createTo.'view/base/'.$name.'.phtml';
    
    if(!file_exists($fileName)){
      require_once __DIR__.$this->createTo.'GenerateContent.php'; // Generating Contents
      $content = Content('layout', false); // Generating Contents
      
      if(self::Create('layout', $fileName, $content)){
        ozz_console_success("Base Layout $name Created successfully");
      }
      else{
        ozz_console_error("Base Layout not generated");
      }
    }
    else{
      ozz_console_warn("Base Layout $name already exist");
    }
  }



  # ------------------------------------
  // Create Component (View)
  # ------------------------------------
  private function createComponent($name){
    if(!valid_file_name($name)){
      ozz_console_error("Invalid Component Name");
      return false;
    }
    
    $fileName = $this->createTo.'view/components/'.$name.'.phtml';
    $nameFinal = explode("/", $name);
    
    if(!file_exists($fileName)){
      require_once __DIR__.$this->createTo.'GenerateContent.php'; // Generating Contents
      $content = Content('component', [
        'path' => 'view/components/'.$name,
        'name' => end($nameFinal)
      ]); // Generating Contents
      
      if(self::Create('component', $fileName, $content)){
        ozz_console_success("Component $name Created successfully");
      }
      else{
        ozz_console_error("Component not generated");
      }
    }
    else{
      ozz_console_warn("Component $name already exist");
    }
  }
  
  
  
  # ------------------------------------
  // Create Error
  # ------------------------------------
  private function createError(){
    ozz_console_error('Error on creating');
  }
  
  
  
  # ------------------------------------
  // Create File
  # ------------------------------------
  private static function Create($typ, $fullName, $content){
    
    $DS = DIRECTORY_SEPARATOR;
    $dirName = explode("/", $fullName);
    
    $dirArr = array_slice($dirName, 0, -1);
    $dir = implode($DS, $dirArr);

    if(PHP_OS == 'WIN32' || PHP_OS == 'Windows' || PHP_OS == 'WINNT'){
      if(!is_dir(__DIR__.$DS.$dir)){
        mkdir(__DIR__.$DS.$dir, 0777, true);
      }
    } else {
      if(!file_exists(__DIR__.$DS.$dir)){
        mkdir(__DIR__.$DS.$dir, 0755, true);
      }
    }
    
    if($typ == "controller" || $typ == "model" || $typ == "middleware"){
      $onlyName = substr($fullName, strrpos($fullName, '/') + 1); // Only Name
      $onlyName = ucwords($onlyName);
      
      $onlyDir = explode('/', $fullName);
      array_pop($onlyDir);
      
      $fileFinalPath = implode($DS, $onlyDir).$DS.$onlyName;
    }
    else{
      $fileFinalPath = $fullName;
    }
    
    // Create File
    $fl = fopen(__DIR__.$fileFinalPath, 'w');
    fwrite($fl, $content."\n");
    if(fclose($fl)){
      return true;
    }
    else {
      return false;
    }
  }
  
  
  
  # ------------------------------------
  // Set Namespace for creating Class
  # ------------------------------------
  private static function SetNamespace($fullName){
    $namespace = explode("/", $fullName);
    if (count($namespace) < 2) {
      $namespace = explode("\\", $namespace[0]);
    }
    array_pop($namespace);
    $namespace = join("\\", $namespace);
    return $namespace;
  }
  
  
}
(new CliCreate)->index($com);