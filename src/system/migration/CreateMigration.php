<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\migration;

class CreateMigration {
  
  private $mgDir = __DIR__.SPC_BACK['core_2'].'database/migration/'; // Migrations Directory
  
  public function index($com){

    // CLI Input
    extract($com);
    $r2 = ucwords($r2);
    
    // Set Up Migration Type (New / Update)
    if($r1 == 'c:migration' || $r1 == 'create:migration' || $r1 == 'create:mig' || $r1 == 'c:mig' || $r1 == 'make:mig' || $r1 == 'make:migration'){
      // Default Create New Migration Generating Content
      $content = $this->newMigratorContent($r2);
      $newFile = $this->mgDir.'mg_'.date('d_m_Y_').$r2.'.php';
      $extName = '';
    }
    elseif($r1 == 'u:migration' || $r1 == 'u:mig' || $r1 == 'update:migration' || $r1 == 'update:mig'){
      // Default Update Table Migration Generating Content
      $content = $this->updateMigratorContent($r2);
      $newFile = $this->mgDir.'mg_'.date('d_m_Y_').'Update_'.$r2.'.php';
      $extName = 'Update_';
    }
    
    if(!valid_file_name($r2)){
      ozz_console_error('Invalid Migration File Name');
      return false;
    }

    // Available Files in DIR (Scan and get the clean names to check)
    $fileNamesOnly = [];
    $mg_files = scandir($this->mgDir);
    foreach ($mg_files as $k => $v) {
      $fileNamesOnly[$k] = ucwords(substr($v, 14));
    }
    
    // Crete migration file only if not already exist
    if( !in_array($extName.$r2.'.php', $fileNamesOnly) ){
      $fl = fopen($newFile, 'w');
      fwrite($fl, $content);
      if(fclose($fl)){
        ozz_console_success("Migration [ $r2 ] created successfully");
      }
      else {
        ozz_console_error(" Error: Migration [ $r2 ] not created!");
      }
    }
    else{
      ozz_console_warn(" Migration [ $r2 ] already exist!");
    }
  }
  
  
  
  # -------------------------------------------
  // Create New Migrator Default Contents
  # -------------------------------------------
  private function newMigratorContent($migrationName){
    return "<?php
// Run: [ php ozz -h migration ] to more info
use Ozz\Core\system\migration\Schema;

class $migrationName {
  
  public function up(){
    Schema::createTable('".strtolower($migrationName)."', [
      
      'id'          => ['int', 'ai', 'primary'],
      'name'        => ['str:150'],
      'email'       => ['str:150', 'unique'],
      'password'    => ['txt'],
      'status'      => ['bool'],
      'timestamp'   => ['datetime'],
      
    ]);
  }
  
  public function down(){
    Schema::dropTable('".strtolower($migrationName)."');
  }
}";
  }
  
  
  
  # -------------------------------------------
  // Update Migrator Default Contents
  # -------------------------------------------
  private function updateMigratorContent($migrationName){
return "<?php
use Ozz\Core\system\migration\Schema;

class Update_$migrationName {
  
  public function up(){
    Schema::addColumns('".strtolower($migrationName)."', [
      // 
    ]);

    
    Schema::updateColumns('".strtolower($migrationName)."', [
      // 
    ]);
    
    
    Schema::dropColumns('".strtolower($migrationName)."', [
      // 
    ]);
  }
}";
  }
  
}
(new CreateMigration)->index($com);