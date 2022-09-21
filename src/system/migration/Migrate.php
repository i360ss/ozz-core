<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\migration;

use \PDO;
use Ozz\Core\system\cli\CliUtils;
use Ozz\Core\system\log\Ozz_log_data;
use Ozz\Core\system\Ozz_CLI_Connection;

class Migrate extends Schema {

  use Ozz_log_data;
  use Ozz_CLI_Connection;
  
  private $mgDir = __DIR__.SPC_BACK['core_2'].'database/migration/'; // Migrations Directory
  private $vals;
  private $dbCred; // DB Cred
  private $conn; // DB Connection
  private $cli_utils; // CLI styling Utilities
  
  function __construct(){
    $this->conn = $this->mysql();
    $this->cli_utils = new CliUtils;
  }
  

  # --------------------------------------
  // Migration Request Landing
  # --------------------------------------
  public function index($com){
    $this->vals = $com;
    extract($com);

    if($r1 == "migrate" || $r1 == "migrate:run"){
      if(isset($r2) && $r2 !== ''){
        $this->migrateUp();
      }
      else{
        $this->migrateRun();
      }
    }
    elseif($r1 == "migrate:up"){
      $this->migrateUp();
    }
    elseif($r1 == "migrate:drop"){
      $this->migrateDrop();
    }
    elseif($r1 == "migrate:clear"){
      $this->migrateClear();
    }
    elseif($r1 == "migrate:reset"){
      $this->migrateReset();
    }
    else{
      console_return('red', 'Invalid command');
      exit;
    }
  }
  
  
  
  # --------------------------------------
  // Run Migration
  # --------------------------------------
  private function migrateRun(){
    extract($this->cli_utils->styles);
    
    $getAllMigrations = scandir($this->mgDir);
    unset($getAllMigrations[0], $getAllMigrations[1]);
    
    // Load all Migration files one by one
    foreach ($getAllMigrations as $k => $v) {
      if(file_exists($this->mgDir.$v) && is_file($this->mgDir.$v)){
        require_once $this->mgDir.$v;
        
        if ($v !== '.gitkeep') {
          $class = substr(substr($v, 14), 0, -4);
          $class = new $class;
          $class->up();
        }
      }
    }
    
    
    // All Generated SQLs for CREATE TABLES
    $allTblCreation_SQL = $this->getGeneratedSQL('createTable');
    
    if(isset($allTblCreation_SQL) && !empty($allTblCreation_SQL)){
      // Create Tables
      foreach ($allTblCreation_SQL as $table => $v) {
        if($this->conn->query($v)){

          // Log Migration data history
          $this->log_migrations([$table => $v], 'create');

          $this->cli_utils->console_success("Table [ $table ] created",);
        }
        else{
          $this->cli_utils->ozz_console_error("Error on creating [ $table ] table. Please check your migration file.");
        }
      }
    }
    
    
    // All Generated SQLs for UPDATE TABLES
    $allTblUpdate_SQL = $this->getGeneratedSQL('updateTable');
    
    if(isset($allTblUpdate_SQL) && !empty($allTblUpdate_SQL)){
      
      foreach ($allTblUpdate_SQL as $table => $value) {
        foreach ($value as $k => $v) {
          if(!empty($v)){
            if($this->conn->query($v)){
              
              // Log Migration data history
              $this->log_migrations([$table => $value], 'update');

              if(strpos($v, 'ADD COLUMN')){
                $workDone = 'New column added';
              }
              elseif(strpos($v, 'MODIFY COLUMN')){
                $workDone = 'Column modified';
              }
              elseif(strpos($v, 'DROP COLUMN')){
                $workDone = 'Column removed';
              }
              elseif(strpos($v, 'CHANGE')){
                $workDone = 'Column renamed';
              }
              
              $this->cli_utils->console_success("Table [ $table ] modified - $workDone");
            }
            else{
              $this->cli_utils->console_error("Error on modifying [ $table ] table. Please check your migration file.");
            }
          }
        }
      }
    }
  }
  
  
  
  # --------------------------------------
  // Clear Migration
  # --------------------------------------
  private function migrateClear(){
    
    $getAllMigrations = scandir($this->mgDir);
    unset($getAllMigrations[0], $getAllMigrations[1]);
    
    // Load all Migration files one by one
    foreach ($getAllMigrations as $k => $v) {
      if(file_exists($this->mgDir.$v) && is_file($this->mgDir.$v)){
        require_once $this->mgDir.$v;
        
        $class = substr(substr($v, 14), 0, -4);
        $class = new $class;
        method_exists($class, 'down') ? $class->down() : false;
      }
    }
    
    $clearSQL = $this->getGeneratedSQL('clearTables');
    foreach ($clearSQL as $table => $value) {
      if($this->conn->query($value)){

        // Log Migration data history
        $this->log_migrations([$table => $value], 'delete');

        $this->cli_utils->console_warn("Table [ $table ] deleted");
      }
    }
  }
  
  
  
  # --------------------------------------
  // Reset Migration
  # --------------------------------------
  private function migrateReset(){
    $this->migrateClear();
    $this->migrateRun();
  }



  # --------------------------------------
  // Create Single Table Migration
  # --------------------------------------
  private function migrateUp(){
    extract($this->vals);
    
    $getAllMigrations = scandir($this->mgDir);
    unset($getAllMigrations[0], $getAllMigrations[1]);
    $requestedFileExist = false;

    foreach ($getAllMigrations as $k => $v) {
      if(file_exists($this->mgDir.$v)){
        $className = substr(substr($v, 14), 0, -4);

        if($className == ucfirst($r2)){
          $requestedFileExist = true;
          require_once $this->mgDir.$v;
          $class = new $className;
          $class->up();

          if(strtolower(substr($className, 0, 6)) !== 'update'){
            // Create one table
            $allTblCreation_SQL = $this->getGeneratedSQL('createTable');

            if(isset($allTblCreation_SQL) && !empty($allTblCreation_SQL)){
              foreach ($allTblCreation_SQL as $table => $v) {
                if($this->conn->query($v)) {
                  
                  // Log Migration data history
                  $this->log_migrations([$table => $v], 'create');

                  $this->cli_utils->console_success("Table [ $table ] created");
                } else {
                  $this->cli_utils->console_error("Error on creating [ $table ] table. Please check your migration file.");
                }
              }
            }
          }
          else{
            // Update one table
            $allTblUpdate_SQL = $this->getGeneratedSQL('updateTable');

            if(isset($allTblUpdate_SQL) && !empty($allTblUpdate_SQL)){
              foreach ($allTblUpdate_SQL as $table => $value) {
                foreach ($value as $v) {
                  if(!empty($v)){
                    if($this->conn->query($v)){

                      // Log Migration data history
                      $this->log_migrations([$table => $value], 'update');
                      
                      if(strpos($v, 'ADD COLUMN')){
                        $workDone = 'New column added';
                      }
                      elseif(strpos($v, 'MODIFY COLUMN')){
                        $workDone = 'Column modified';
                      }
                      elseif(strpos($v, 'DROP COLUMN')){
                        $workDone = 'Column removed';
                      }
                      else{
                        $workDone = '';
                      }

                      $this->cli_utils->console_success("Table [ $table ] modified - $workDone");
                    }
                    else{
                      $this->cli_utils->console_error("Error on modifying [ $table ] table. Please check your migration file.");
                    }
                  }
                }
              }
            }
          }
        }
      }
    }

    if(!$requestedFileExist){
      $this->cli_utils->console_error("Migration file [ $r2 ] not exist");
    }
  }
  
  
  
  # --------------------------------------
  // Drop Single Table Migration
  # --------------------------------------
  private function migrateDrop(){    
    extract($this->vals);
    if($this->conn->query('DROP TABLE '.$r2.';')){
      
      // Log Migration data history
      $this->log_migrations([$r2 => 'DROP TABLE '.$r2.';'], 'delete');

      $this->cli_utils->console_success("Table [ $r2 ] Deleted");
    }
    else{
      $this->cli_utils->console_error("Error on deleting table - $r2");
    }
  }  
  
}
(new Migrate)->index($com);