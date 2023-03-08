<?php
namespace Ozz\Core\system;

if(APP_ENV !== 'local'){
  exit('Unauthorized');
}

trait Ozz_CLI_Connection {

  private $ozz_log_conn;
  private $ozz_cli_DB_Conn;
  private $config;

  /**
   * Get Config data
   */
  private function init() {
    $this->config = env();
  }

  /**
   * Mysql Connection to CLI usage
   * @return ozz_cli_DB_Conn Mysql connection for CLI
   */
  private function mysql() {
    $this->init();
    $primary_db = $this->config['app']['PRIMARY_DB'];
    extract($this->config[$primary_db]);
    $this->ozz_cli_DB_Conn = new \PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USERNAME, $DB_PASSWORD);
    $this->ozz_cli_DB_Conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $this->ozz_cli_DB_Conn;
  }

  /**
   * Framework Log Sqlite Connection to CLI usage (internal)
   * @return ozz_log_conn Log sqlite connection
   */
  private function log_conn() {
    $this->init();
    $this->ozz_log_conn = new \PDO("sqlite:" . $this->config['app_log']['DB_NAME']);
    $this->ozz_log_conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $this->ozz_log_conn;
  }
}