<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\log;

use Ozz\Core\system\Ozz_CLI_Connection;

trait Ozz_log_data {

  use Ozz_CLI_Connection;

  /**
   * Log Migrations to internal sqlite
   * @param array $migration_data Table name as key and Generated SQL as value
   * @param string $type Type of operation (create/update/delete)
   */
  private function log_migrations($migration_data, $type) {
    $this->init();

    if(!$this->is_table_exist('ozz_migrations')){
      // create table
      $createTable = "CREATE TABLE IF NOT EXISTS ozz_migrations (
        id INTEGER PRIMARY KEY,
        mg_type TEXT,
        mg_sql TEXT,
        mg_created TEXT
      )";
      $this->log_conn()->exec($createTable);
    }

    // Insert migration log
    $sql = "INSERT INTO ozz_migrations(mg_type, mg_sql, mg_created) VALUES(:mg_type, :mg_data, datetime('now', 'localtime'))";
    $run = $this->log_conn()->prepare($sql);
    $run->execute([
      ':mg_type' => $type,
      ':mg_data' => json_encode($migration_data),
    ]);
  }

  /**
   * Check if log table exists
   * @param string $table
   */
  private function is_table_exist($table){
    $tblCheck = $this->log_conn()->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$table'");
    return $tblCheck->fetch() !== false;
  }
}