<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use PDO;
use Ozz\Core\Medoo;

trait DB {

  private static array $sharedConnections = [];
  private $DBconfig;
  private $db_type;
  private $db_name;
  private $db_port;
  private $db_host;
  private $db_user;
  private $db_pass;
  private $db_prefix;
  private $db_options;
  private $connect = null;
  private $current_connection;

  /**
   * Make MySQL Connection
   */
  protected function mysql() : void {
    if ($this->db_host !== "" && $this->db_user !== "" && $this->db_name !== "") {
      $rawOptions  = $this->db_options['DB_OPTIONS']  ?? ['ATTR_CASE' => 'CASE_NATURAL'];
      $rawCommands = $this->db_options['DB_COMMANDS'] ?? ['SET SQL_MODE=ANSI_QUOTES'];
      try {
        $this->connect = new Medoo([
          'database_type' => 'mysql',
          'database_name' => $this->db_name,
          'server'        => $this->db_host,
          'username'      => $this->db_user,
          'password'      => $this->db_pass,
          'port'          => $this->db_port ?: 3306,
          'charset'       => $this->db_options['DB_CHARSET'] ?? 'utf8mb4',
          'collation'     => $this->db_options['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
          'option'        => $this->resolvePdoOptions($rawOptions),
          'command'       => $rawCommands,
          'logging'       => DEBUG,
          'prefix'        => $this->db_prefix ?? false,
        ]);
      } catch (\PDOException $e) {
        $this->handleConnectionError($e, 'MySQL');
      }
    }
  }

  /**
   * Make SqLite Connection
   */
  protected function sqlite() : void {
    try {
      $this->connect = new Medoo([
        'type' => 'sqlite',
        'database' => BASE_DIR . ltrim($this->db_name, '/'),
        'logging' => DEBUG,
        'prefix' => $this->db_prefix ?? false,
      ]);
    } catch (\PDOException $e) {
      $this->handleConnectionError($e, 'SQLite');
    }
  }

    /**
   * The Database connection method
   * This will look for a new DB connection first, if it is not available on env.ini it will look for another DB on same server
   * @param string $db Database key to DB credentials (provided on env.ini) or Database name on same connection
   * @return object Database connection
   */
  public function DB($db = null) {
    $this->DBconfig = env();

    $connection = $this->DBconfig['app']['PRIMARY_DB'];
    $dbName = false;

    if ($db !== null && isset($this->DBconfig[$db])) {
      $connection = $db;
    } elseif (null !== $db) {
      $dbName = $db;
    }

    $this->db_name = $dbName ?: $this->DBconfig[$connection]['DB_NAME'];
    $this->db_type = $this->DBconfig[$connection]['DB_TYPE'] ?? 'mysql';
    $this->db_prefix = $this->DBconfig[$connection]['DB_PREFIX'] ?: false;

    if ($this->db_type !== 'sqlite') {
      $this->db_host = $this->DBconfig[$connection]['DB_HOST'];
      $this->db_user = $this->DBconfig[$connection]['DB_USERNAME'];
      $this->db_pass = $this->DBconfig[$connection]['DB_PASSWORD'];
      $this->db_port = $this->DBconfig[$connection]['DB_PORT'];
      $this->db_options = $this->DBconfig[$connection];

      $signature = $this->db_type.$this->db_name.$this->db_host.$this->db_user.$this->db_pass.$this->db_port.$this->db_prefix;
    } else {
      $signature = $this->db_type.$this->db_name.$this->db_prefix;
    }

    if (isset(self::$sharedConnections[$signature])) {
      $this->connect = self::$sharedConnections[$signature];
      return $this->connect;
    }

    switch ($this->db_type) {
      case 'mysql':   $this->mysql();  break;
      case 'sqlite':  $this->sqlite(); break;
      default:        $this->mysql();  break;
    }

    self::$sharedConnections[$signature] = $this->connect;
    return $this->connect;
  }

  private function resolvePdoOptions(array $rawOptions): array {
    $resolved = [];
    foreach ($rawOptions as $key => $value) {
      $constKey = defined("PDO::{$key}") ? constant("PDO::{$key}") : null;
      if ($constKey === null) continue;

      if (is_string($value) && defined("PDO::{$value}")) {
        $resolved[$constKey] = constant("PDO::{$value}");
      } else {
        $resolved[$constKey] = $value;
      }
    }
    return $resolved;
  }

  private function handleConnectionError(\PDOException $e, string $context): void {
    // Always log the real error server-side, regardless of DEBUG
    error_log("[{$context}] DB connection failed: " . $e->getMessage());

    if (defined('DEBUG') && DEBUG) {
      throw new \RuntimeException(
        "{$context} connection failed: " . $e->getMessage(),
        (int) $e->getCode(),
        $e
      );
    }

    throw new \RuntimeException('Something went wrong. Please try again later.', 0);
  }

}