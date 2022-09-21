<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;


class Model {

  use DB;



  function __construct() {
    $this->table = $this->get_table();
  }



  /**
   * Set table name
   * Use defined name from called model if defined
   * or use the called model name as the table name
   */
  public function get_table() {
    $this_model = get_sub_classes(Model::class);
    $table_from_class = explode('\\', end($this_model));
    return isset($this->table) ? $this->table : to_snakecase(end($table_from_class));
  }


}