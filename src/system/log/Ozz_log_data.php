<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\core\system\log;

use Ozz\core\system\Ozz_CLI_Connection;

trait Ozz_log_data {

  use Ozz_CLI_Connection;

  /**
   * Log Migrations to internal sqlite
   * 
   * @param array $migration_data Table name as key and Generated SQL as value
   * @param string $type Type of operation (create/update/delete)
   */
  private function log_migrations($migration_data, $type) {
    $this->init();
    
    $sql = "INSERT INTO ozz_migrations(mg_type, mg_sql, mg_created) VALUES(:mg_type, :mg_data, datetime('now', 'localtime'))";
    $run = $this->log_conn()->prepare($sql);
    $run->execute([
      ':mg_type' => $type,
      ':mg_data' => json_encode($migration_data),
    ]);
  }
}