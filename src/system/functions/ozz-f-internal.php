<?php
// Ozz Internal functions

/**
 * Convert Block input into structured array
 * @param array $arr
 */
function ozz_i_convert_str_to_array_1($arr) {
  $output = [];
  foreach ($arr as $key => $values) {
    $keys = explode('_', $key);
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
    $itemName = $parent_name . ($parent_name ? '_' : '') . $item['name'];
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
function ozz_i_get_nested_validations($arr, $prefix='_') {
  $res = [];
  foreach ($arr as $item) {
    $fieldName = $item['name'];
    if (isset($item['fields']) && is_array($item['fields'])) {
      $nestedPrefix = $prefix . $fieldName . '_';
      $nestedValidations = ozz_i_get_nested_validations($item['fields'], $nestedPrefix);
      $res = array_merge($res, $nestedValidations);
    } else {
      $fullFieldName = ltrim($prefix . $fieldName, '_');
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
      $item['name'] = str_replace('_', '-', $item['name']);
    }

    if(isset($item['fields']) && is_array($item['fields'])){
      ozz_i_modify_field_names($item['fields']);
    }
  }

  return $fields;
}