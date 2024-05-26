<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Model {

  use DB;

  public function all() {
    return $this->DB()->select($this->table, '*');
  }

  public function find($id) {
    return $this->DB()->get($this->table, '*', ['id' => $id]);
  }

  public function create($data) {
    return $this->DB()->insert($this->table, $data);
  }

  public function edit($id, $data) {
    return $this->DB()->update($this->table, $data, ['id' => $id]);
  }

  public function remove($id) {
    return $this->DB()->delete($this->table, ['id' => $id]);
  }

  public function where($column, $value) {
    return $this->DB()->select($this->table, '*', [$column => $value]);
  }

  public function raw_query($sql) {
    return $this->DB()->query($sql)->fetchAll();
  }

}
