<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class Errors {
  
  public $all = [];

  
  public function __construct() {
    return $this->all();
  }



  /**
   * Return all current errors
   */
  public function all() {
    return $this->all;
  }



  /**
   * Check Error key exist
   * @param string $key Error key to check if exist
   */
  public function has($key='') {
    if ($key == '') {
      return count($this->all) > 0 ? true : false;
    } elseif (empty($this->all) || !isset($this->all)) {
      return false;
    } else {
      return array_key_exists($key, $this->all);
    }
  }



  /**
   * Get One Error by key
   * @param string $key Key of the required error
   */
  public function get(string $key='') {
    if ($key !== '') {
      if (array_key_exists($key, $this->all)) {
        return $this->all[$key];
      } else {
        if (DEBUG) {
          return ERR::invalidArrayKey($key);
        }
      }
    } else {
      return $this->all;
    }
  }



  /**
   * Set New Error with key value
   * @param string $key key of the Error
   * @param string|array|object|bool $value the error message and/or any additional info
   */
  public function set(string $key, $value) : void {
    $this->all[$key] = $value;
  }
  
}