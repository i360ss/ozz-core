<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use PDO;
use Ozz\Core\Medoo;

abstract class Model {

  private $DBconfig;
  private $db_host;
  private $db_user;
  private $db_pass;
  private $db_base;
  private $db_prefix;
  private $connect;
  protected $DB;
  protected $table_name;
  
  
  /**
   * Make Database Connection
   */
  public function __construct(){
    $this->DBconfig = parse_ini_file(__DIR__.SPC_BACK['core'].'env.ini', true);
    $this->db_host = $this->DBconfig['mysql']['DB_HOST'];
    $this->db_user = $this->DBconfig['mysql']['DB_USERNAME'];
    $this->db_pass = $this->DBconfig['mysql']['DB_PASSWORD'];
    $this->db_port = $this->DBconfig['mysql']['DB_PORT'];
    $this->db_prefix = $this->DBconfig['mysql']['DB_PREFIX'];

    // Return Primary DB Connection
    $this->DB = $this->DBconfig['db']['PRIMARY_DB'] == 'mysql' 
    ? $this->mysql() 
    : $this->sqlite();

    $this->table_name = $this->getTable();
  }



  /**
   * Make MySQL Connection
   * @param string $dbName Database name (Leave empty for get from env.ini)
   */
  protected function mysql($dbName=null){
    $this->db_base = $dbName !== null ? $dbName : $this->DBconfig['mysql']['DB_NAME'];
    if($this->db_host !=="" && $this->db_user !=="" && $this->db_base !==""){
      $this->connect = new Medoo([
        'database_type' => 'mysql',
        'database_name' => $this->db_base,
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
        'prefix' => $this->db_prefix !== '' ? $this->db_prefix : false,
      ]);
      
      return $this->connect;
    }
  }



  /**
   * Make SqLite Connection
   * @param string $dbName sqLite DB Name (Leave empty for get from env.ini)
   */
  protected function sqlite($dbName=null){
    $db = $dbName !== null ? $dbName : $this->DBconfig['sqlite']['DB_NAME'];
    $prefix = $this->DBconfig['sqlite']['DB_PREFIX'] !== '' ? $this->DBconfig['sqlite']['DB_PREFIX'] : false;
    $sqliteDB = new Medoo([
      'database_type' => 'sqlite',
      'database_file' => __DIR__ . '/../database/sqlite/'.$db,
      'logging' => DEBUG ? true : false,
      'prefix' => $prefix,
    ]);

    return $sqliteDB;
  }



  /**
   * Set table name
   * Use defined name from called model if defined
   * or use the called model name as the table name
   */
  public function getTable() {
    $this_model = get_sub_classes(Model::class);
    $table_from_class = explode('\\', end($this_model));
    return $this->table ?? to_snakecase(end($table_from_class));
  }



  /**
   * Get All values from the table
   */
  public function all() {
    return $this->DB->select($this->table_name, '*',);
  }



  /**
   * Log SQL queries to debug bar info
   */
  public function __destruct() {
    global $DEBUG_BAR;
    $DEBUG_BAR->set('ozz_sql_queries', $this->DB->log());
  }

}