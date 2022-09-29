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

  private $DBconfig;
  private $db_type;
  private $db_host;
  private $db_user;
  private $db_pass;
  private $db_base;
  private $db_prefix;
  private $connect = null;
  private $current_connection;


  /**
   * Make MySQL Connection
   */
  protected function mysql() : void {
    if($this->db_host !=="" && $this->db_user !=="" && $this->db_name !==""){
      $this->connect = new Medoo([
        'database_type' => 'mysql',
        'database_name' => $this->db_name,
        'server' => $this->db_host,
        'username' => $this->db_user,
        'password' => $this->db_pass,
        'port' => $this->db_port ? $this->db_port : 3306,
        'option' => [
          PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
          'SET SQL_MODE=ANSI_QUOTES'
        ],
        'logging' => DEBUG ? true : false,
        'prefix' => isset($this->db_prefix) ? $this->db_prefix : false,
      ]);
    }
  }



  /**
   * Make SqLite Connection
   */
  protected function sqlite() : void {
    $this->connect = new Medoo([
      'type' => 'sqlite',
      'database' => __DIR__ .SPC_BACK['core'].'/database/sqlite/'.$this->db_name,
      'logging' => DEBUG ? true : false,
      'prefix' => isset($this->db_prefix) ? $this->db_prefix : false,
    ]);
  }



    /**
   * The Database connection method
   * This will look for a new DB connection first, if it is not available on env.ini it will look for another DB on same server
   * @param string $db Database key to DB credentials (provided on env.ini) or Database name on same connection
   * @return object Database connection
   */
  public function DB($db=null) {
    $this->DBconfig = env();
    
    $connection = $this->DBconfig['app']['PRIMARY_DB'];
    $dbName = false;
    
    if ($db !== null && isset($this->DBconfig[$db])) {
      $connection = $db; // New connection
    } elseif (null !== $db) {
      $dbName = $db; // New Database on same connection
    }

    $this->db_name = $dbName ? $dbName : $this->DBconfig[$connection]['DB_NAME'];
    $this->db_type = isset($this->DBconfig[$connection]['DB_TYPE']) ? $this->DBconfig[$connection]['DB_TYPE'] : 'mysql';
    $this->db_prefix = $this->DBconfig[$connection]['DB_PREFIX'] ? $this->DBconfig[$connection]['DB_PREFIX'] : false;

    if ($this->db_type !== 'sqlite') {
      $this->db_host = $this->DBconfig[$connection]['DB_HOST'];
      $this->db_user = $this->DBconfig[$connection]['DB_USERNAME'];
      $this->db_pass = $this->DBconfig[$connection]['DB_PASSWORD'];
      $this->db_port = $this->DBconfig[$connection]['DB_PORT'];

      $temp_conn = $this->db_type.$this->db_name.$this->db_host.$this->db_user.$this->db_pass.$this->db_port.$this->db_prefix;
    } else {
      $temp_conn = $this->db_type.$this->db_name.$this->db_prefix;
    }

    if ($this->current_connection == $temp_conn && $this->connect !== null) {
      return $this->connect;
    } else {
      $this->current_connection = $temp_conn;

      switch ($this->db_type) {
        case 'mysql':
          $this->mysql();
          break;
  
        case 'sqlite':
          $this->sqlite();
          break;
        
        default:
          $this->mysql();
          break;
      }
  
      return $this->connect;
    }
  }



  /**
   * Destructor
   */
  public function __destruct() {
    if ($this->connect !== null) {
      global $DEBUG_BAR;
      $DEBUG_BAR->set('ozz_sql_queries', $this->connect->log());
    }
  }
  
}