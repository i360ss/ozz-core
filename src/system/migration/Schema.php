<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\migration;

class Schema extends GenerateSql {
  
  // Data From All Migration files (Up Data)
  protected static $allmigrationData; // Create New Table
  protected static $allUpdateData; // Update Existing Tables
  protected static $allDropData = []; // Delete Tables
  
  

  # --------------------------------------
  // TABLE CREATING & DELETING METHODS (MIGRATIONS)
  # --------------------------------------
  // Create Table Initialize
  public static function createTable($table, $cols){
    self::$allmigrationData[$table] = $cols;
  }
  
  
  
  // Delete Table
  public static function dropTable($table){
    self::$allDropData[$table] = $table;
  }
  
  
  
  # --------------------------------------
  // TABLE UPDATING (ALTER) METHODS (MIGRATIONS)
  # --------------------------------------
  // Add Columns to Table Initialize
  public static function addColumns($table, $cols){
    self::$allUpdateData[$table]['add'] = $cols;
  }
  
  
  
  // Update Table Initialize
  public static function updateColumns($table, $cols){
    self::$allUpdateData[$table]['update'] = $cols;
  }
  
  
  
  // Drop Columns on selected Initialize
  public static function dropColumns($table, $cols){
    self::$allUpdateData[$table]['drop'] = $cols;
  }
  
  
  
  # --------------------------------------
  // This will return real final Generated SQL to (CREATE and ALTER tables)
  # --------------------------------------
  protected function getGeneratedSQL($typ){
    $sqlGEN = new GenerateSql;
    
    if($typ == 'createTable'){
      $data = self::$allmigrationData;
      $finalCreationSQL = $sqlGEN->generateCreateSql($data);
    }
    elseif($typ == 'updateTable'){
      $data = self::$allUpdateData;
      $finalCreationSQL = $sqlGEN->generateAlterSql($data);
    }
    elseif($typ == 'clearTables'){
      foreach (self::$allDropData as $table => $value) {
        $finalCreationSQL[$table] = "DROP TABLE IF EXISTS $value";
      }
    }
    
    return $finalCreationSQL;
  }
  
  
}
?>