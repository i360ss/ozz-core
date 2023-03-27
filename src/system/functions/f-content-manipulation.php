<?php
use Ozz\Core\Response;

# ----------------------------------------------------
// Var, Array, JSON and Object manipulations
# ----------------------------------------------------
/**
 * Returns string between 2 strings
 * @param string $str   // Full string
 * @param string $start // Left side string
 * @param string $end   // Right side string
 * 
 * @return string
 */
function str_between($str, $start, $end) {
  if (preg_match("/$start(.*?)$end/", $str, $match) == 1) {
    return $match[1];
  }
  else {
    return '';
  }
}

// Direct echo
function _str_between($str, $start, $end) {
  echo str_between($str, $start, $end);
}

/**
 * Returns an array of all matching strings between 2 strings
 * @param string $str   // Full string
 * @param string $start // Left side string
 * @param string $end   // Right side string
 * 
 * @return array|bool
 */
function str_between_all($str, $start, $end) {
  if (preg_match_all("/$start(.*?)$end/", $str, $match)) {
    return $match[1];
  } else {
    return false;
  }
}

// Direct echo (Returns a string each value is separated by a comma (,))
function _str_between_all($str, $start, $end) {
  if(is_array(str_between_all($str, $start, $end))){
    echo implode(', ', str_between_all($str, $start, $end));
  } else {
    return false;
  }
}

/**
 * Camel case to snake case
 * This will convert camelCase to snake case
 * @param string $str string to convert
 */
function to_snakecase($str) {
  return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
}

/**
 * Convert string to valid slug
 * @param string $str String to convert
 * @param string $delimiter Delimiter to replace (Optional)
 */
function to_slug($str, $delimiter='-') {
  return strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv(CHARSET, 'ASCII//TRANSLIT', urldecode($str)))))), $delimiter));
}

/**
 * Find value in array by provided key
 * @param string $key Array key to find by
 * @param array $array The array to find from
 * 
 * @return array|string|bool
 */
function find_in_array($key, $array) {
  if (array_key_exists($key, $array)) {
    return $array[$key];
  } else {
    foreach ($array as $k => $val) {
      if (is_array($val)) {
        return find_in_array($key, $val);
      } else {
        return false;
      }
    }
  }
}

/**
 * Get sub-classes of a class
 * @param object $parent
 */
function get_sub_classes($parent) {
  $result = array();
  foreach (get_declared_classes() as $class) {
    is_subclass_of($class, $parent) ? $result[] = $class : false;
  }
  return $result;
}

/**
 * Search [key => value] in array by provided value
 * @param string $value Value to search by
 * @param array $array Array to search from
 * @param bool $getOnlyKey If it is true function will only return the key of the value
 * 
 * @return array|bool
 */
function search_in_array($value, $array, $getOnlyKey=false) {
  $arrKey = array_search($value, $array);
  if ($arrKey) {
    return $getOnlyKey ? $arrKey : [$arrKey => $array[$arrKey] ];
  } else {
    foreach ($array as $k => $val) {
      if (is_array($val)) {
        return search_in_array($value, $val, $getOnlyKey);
      } else {
        return false;
      }
    }
  }
}

/**
 * Check string is JSON or not
 * @param string @str
 * @return bool
 */
function is_json($str){
  return is_string($str) && is_array(json_decode($str, true)) ? true : false;
}

/**
 * Return as json
 * @param array $data Array to convert into JSON
 * @param string $flags json encoding flags
 */
function json(array $data, $flags=null) {
  $content = !is_null($flags) ? json_encode($data, $flags) : json_encode($data);
  $response = Response::getInstance();

  if(!$response->hasHeader('Content-Type')){
    $response->setHeader('Content-Type', 'application/json; charset='.CHARSET);
  }

  // Set CSRF Token to header
  if(isset($_SESSION['csrf_token'])){
    $response->setHeader('X-CSRF-Token', $_SESSION['csrf_token']);
  }

  $response->setContent($content);

  return $response->send();
}

/**
 * Simple base 64 encoding
 * @param string $str String to encrypt
 */
function enc_base64($str) {
  return strtr(base64_encode($str), '+/=', '-_,');
}

// Direct echo
function _enc_base64($str) {
  echo enc_base64($str);
}

/**
 * Simple base 64 decoding
 * @param string $str Encoded string to decode
 */
function dec_base64($str) {
  return base64_decode(strtr($str, '-_,', '+/='));
}

// Direct echo
function _dec_base64($str) {
  echo dec_base64($str);
}

/**
 * Random string generator
 * @param int { $n } the number of characters
 * @param string { $validChars } character type
 * 
 * You can define what characters should be in the output in 2nd argument
 * It can be letters / numbers / uppercase / lowercase / numbers lowercase / numbers uppercase
 * By Default this will return a random string with letters (both case) and numbers mixed
 */
function random_str($n=10, $validChars=null) {
  $randStr = '';

  switch ($validChars) {
    case 'number':
    case 'numbers':
    case 'numeric':
    case 'int':
    case 'n':
    case '0':
      $char = '0123456789';
      break;

    case 'numbers lowercase':
    case 'number lowercase':
    case 'lowercase numbers':
    case 'lowercase number':
    case 'a0':
    case '0a':
      $char = '0123456789abcdefghijklmnopqrstuvwxyz';
      break;

    case 'numbers uppercase':
    case 'number uppercase':
    case 'uppercase numbers':
    case 'uppercase number':
    case 'A0':
    case '0A':
      $char = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      break;

    case 'text':
    case 'alpha':
    case 'letter':
    case 'letters':
    case 'Aa':
    case 'aA':
      $char = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      break;

    case 'lowercase':
    case 'a':
      $char = 'abcdefghijklmnopqrstuvwxyz';
      break;
    
    case 'uppercase':
    case 'A':
      $char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      break;
    
    default:
      $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      break;
  }

  for ($i = 0; $i < $n; $i++) {
    $ind = rand(0, strlen($char) - 1);
    $randStr .= $char[$ind];
  }

  return $randStr;
}

function _random_str($n=10, $type=null) {
  echo random_str($n, $type);
}

/**
 * Replace multiple slashes by single slash
 * @param string $str string to be replaced
 */
function clear_multi_slashes($str) {
  return preg_replace("/\/+/", "/", $str);
}