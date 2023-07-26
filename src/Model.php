<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Model {

  use DB;

  private $table;

  function __construct() {
    $this->table = $this->getTable();
  }

  /**
   * Set table name
   * Use defined name from called model if defined
   * or use the called model name as the table name
   */
  public function getTable() {
    $this_model = get_sub_classes(Model::class);
    $table_from_class = explode('\\', end($this_model));
    return isset($this->table) ? $this->table : to_snakecase(end($table_from_class));
  }

  /**
   * A simple method for select from current table
   * @param array|string $what Items to be selected eg: '*' | 'address' | ['email', 'name'] ect.
   * @param array|string $where and all the other parameters of Medoo query
   */
  protected function get($what, $where) {
    return $this->DB()->select($this->table, $what, $where);
  }

  /**
   * A simple method for select all from current table
   */
  protected function all() {
    return $this->DB()->select($this->table, '*');
  }

}