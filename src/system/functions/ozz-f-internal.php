<?php
// Ozz Internal functions

/**
 * Convert Block input into structured array
 * @param array $arr
 */
function ozz_i_convert_str_to_array_1($arr) {
  $output = [];
  foreach ($arr as $key => $values) {
    $keys = explode('__', $key);
    $c = &$output;
    foreach ($keys as $nk) {
      (!isset($c[$nk])) ? $c[$nk] = [] : false;
      $c = &$c[$nk];
    }
    $c = $values;
  }

  return $output;
}

/**
 * Extract nested field names
 * @param array $arr
 * @param string $parent_name
 */
function ozz_i_extract_nested_names($arr, $parent_name = '') {
  $lastItems = [];
  foreach ($arr as $item) {
    $itemName = $parent_name . ($parent_name ? '__' : '') . $item['name'];
    if (isset($item['fields']) && is_array($item['fields'])) {
      $nestedLastItems = ozz_i_extract_nested_names($item['fields']);
      if (!empty($nestedLastItems)) {
        $lastItems = array_merge($lastItems, $nestedLastItems);
      }
    } else {
      $lastItems[] = $itemName;
    }
  }

  return $lastItems;
}

/**
 * Get Validation rules of nested fields
 * @param array $arr
 * @param string $prefix
 */
function ozz_i_get_nested_validations($arr, $prefix='__') {
  $res = [];
  foreach ($arr as $item) {
    $fieldName = $item['name'];
    if (isset($item['fields']) && is_array($item['fields'])) {
      $nestedPrefix = $prefix . $fieldName . '__';
      $nestedValidations = ozz_i_get_nested_validations($item['fields'], $nestedPrefix);
      $res = array_merge($res, $nestedValidations);
    } else {
      $fullFieldName = ltrim($prefix . $fieldName, '__');
      isset($item['validate']) ? $res[$fullFieldName] = $item['validate'] : false;
    }
  }

  return $res;
}

/**
 * Modify all field names (including nested)
 * @param array $fields
 */
function ozz_i_modify_field_names(&$fields) {
  foreach ($fields as &$item) {
    if(isset($item['name'])){
      $item['name'] = preg_replace('/_+/', '_', $item['name']);
    }

    if(isset($item['fields']) && is_array($item['fields'])){
      ozz_i_modify_field_names($item['fields']);
    }
  }

  return $fields;
}

/**
 * Access Sqlite Log DB
 * @param array $table
 * @param array $data
 */
class ozz_i_log_class {
  use \Ozz\Core\system\log\Ozz_log_data;

  public function store($table, $data) {
    return $this->log_store($table, $data);
  }

  public function get($table, $what, $where, $count) {
    return $this->log_get($table, $what, $where, $count);
  }

  public function delete($table, $where) {
    return $this->log_delete($table, $where);
  }
}

// Store in log DB
function ozz_log_save($table, $data) {
  $log = new ozz_i_log_class();
  return $log->store($table, $data);
}

// Get from log DB
function ozz_log_get($table, $what, $where=[], $count=false) {
  $log = new ozz_i_log_class();
  return $log->get($table, $what, $where, $count);
}

// Delete from log DB
function ozz_log_delete($table, $where=[]) {
  $log = new ozz_i_log_class();
  return $log->delete($table, $where);
}

