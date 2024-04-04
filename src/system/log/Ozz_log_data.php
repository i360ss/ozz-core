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
   * Check if log table exists
   * @param string $table
   */
  private function is_table_exist($table){
    $tblCheck = $this->log_DB()->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$table'");
    return $tblCheck->fetch() !== false;
  }

  /**
   * Log Migrations to internal sqlite
   * @param array $migration_data Table name as key and Generated SQL as value
   * @param string $type Type of operation (create/update/delete)
   */
  private function log_migrations($migration_data, $type) {
    $this->init();

    if(!$this->is_table_exist('ozz_migrations')){
      // create table
      $this->log_DB()->exec("CREATE TABLE IF NOT EXISTS ozz_migrations (
        id INTEGER PRIMARY KEY,
        mg_type TEXT,
        mg_sql TEXT,
        mg_created TEXT
      )");
    }

    // Insert migration log
    $sql = "INSERT INTO ozz_migrations(mg_type, mg_sql, mg_created) VALUES(:mg_type, :mg_data, datetime('now', 'localtime'))";
    $run = $this->log_DB()->prepare($sql);
    $run->execute([
      ':mg_type' => $type,
      ':mg_data' => json_encode($migration_data),
    ]);
  }

  /**
   * Log on SQLite (save)
   * @param string $table Table name to store
   * @param array $data Data to be stored
   */
  private function log_store($table, $data) {
    if(!$this->is_table_exist($table)){
      $this->log_DB()->exec("CREATE TABLE IF NOT EXISTS $table (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        `type` TEXT,
        `key` TEXT,
        item_id INTEGER
      )");
    }

    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), "?"));
    $values = array_values($data);

    $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $this->log_DB()->prepare($query);
    $stmt->execute($values);
  }

  /**
   * Get data from Log DB
   * @param string $table Table name to store
   * @param string $what What to get
   * @param array $conditions WHERE parameters
   */
  private function log_get($table, $what, $conditions = [], $count = false) {
    if (!$this->is_table_exist($table)) return false;

    $query = $count ? "SELECT COUNT(*) as count FROM $table" : "SELECT $what FROM $table";
    if (!empty($conditions)) {
      $placeholders = [];
      $values = [];
      foreach ($conditions as $column => $value) {
        $placeholders[] = "$column = ?";
        $values[] = $value;
      }
      $whereClause = implode(' AND ', $placeholders);
      $query .= " WHERE $whereClause";
    }

    $stm = $this->log_DB()->prepare($query);
    $stm->execute(empty($values) ? null : $values);

    if ($count) {
      $result = $stm->fetch(\PDO::FETCH_ASSOC);
      return $result['count'];
    } else {
      return $stm->fetchAll(\PDO::FETCH_ASSOC);
    }
  }

  /**
   * Delete from log DB
   * @param string $table
   * @param array $conditions WHERE parameters
   */
  private function log_delete($table, $conditions=[]) {
    if (!$this->is_table_exist($table)) return false;

    $query = "DELETE FROM $table";
    if (!empty($conditions)) {
      $placeholders = [];
      $values = [];
      foreach ($conditions as $column => $value) {
        $placeholders[] = "$column = ?";
        $values[] = $value;
      }
      $whereClause = implode(' AND ', $placeholders);
      $query .= " WHERE $whereClause";
    }
    $stm = $this->log_DB()->prepare($query);
    $stm->execute(empty($values) ? null : $values);

    return true;
  }

}