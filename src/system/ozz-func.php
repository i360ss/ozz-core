<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*
* OZZ DEFINED FUNCTIONS
*/

// Used Classes
use Ozz\Core\Errors;
use Ozz\Core\Router;
use Ozz\Core\Help;
use Ozz\Core\system\SubHelp;

# ----------------------------------------------------
// Ozz escaping functions
# ----------------------------------------------------
// HTML Charsets
$ozz_htmlSpecialChar = [
  'ISO-8859-1' => true, 
  'ISO8859-1' => true,
  'ISO-8859-15' => true,
  'ISO8859-15' => true,
  'UTF8' => true,
  'UTF-8' => true,
  'utf-8' => true,
  'utf8' => true,
  'CP866' => true,
  'IBM866' => true,
  '866' => true,
  'CP1251' => true,
  'WINDOWS-1251' => true,
  'WIN-1251' => true,
  '1251' => true,
  'CP1252' => true,
  'WINDOWS-1252' => true,
  '1252' => true,
  'KOI8-R' => true,
  'KOI8-RU' => true,
  'KOI8R' => true,
  'BIG5' => true,
  '950' => true,
  'GB2312' => true,
  '936' => true,
  'BIG5-HKSCS' => true,
  'SHIFT_JIS' => true,
  'SJIS' => true,
  '932' => true,
  'EUC-JP' => true,
  'EUCJP' => true,
  'ISO8859-5' => true,
  'ISO-8859-5' => true,
  'MACROMAN' => true,
];



/**
 * Escape HTML
 * @param string $str
 */
function esc($str){
  if (0 === strlen($str)) {
    return '';
  }

  if (!preg_match('/[&<>"\']/', $str)) {
    return $str;
  }

  if (isset($ozz_htmlSpecialChar[CHARSET])) {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, CHARSET);
  }
  elseif (isset($ozz_htmlSpecialChar[strtoupper(CHARSET)])) {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, CHARSET);
  }
  else {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  }
}

// Direct echo
function _esc($str) {
  echo esc($str);
}



/**
 * Escape HTML (Sanitize)
 * This will remove all the HTML entities and return plain text
 * @param string $str
 */
function esc_x($str){
  if (0 === strlen($str)) {
    return '';
  }

  if (!preg_match('/[&<>"\']/', $str)) {
    return $str;
  }

  return filter_var($str, FILTER_SANITIZE_STRING);
}

// Direct echo
function _esc_x($str) {
  echo esc_x($str);
}

function _esx($str) {
  echo esc_x($str);
}



/**
 * Escape Javascript
 * @param string $str
 */
function esc_js($str) {
  if (0 === strlen($str)) {
    return '';
  }

  if (!preg_match('/[&<>"\']/', $str)) {
    return $str;
  }

  $str = htmlspecialchars($str, ENT_COMPAT, CHARSET);
  $str = preg_replace('/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes($str));
  $str = str_replace("\r", '', $str);
  $str = str_replace("\n", '\\n', addslashes($str));
  return $str;
}

// Direct echo
function _esc_js($str) {
  echo esc_js($str);
}



/**
 * Escape CSS
 * @param string $str
 */
function esc_css($str) {
  return preg_replace("/[^A-Za-z0-9.!?]/", '', $str);
}

// Direct echo
function _esc_css($str) {
  echo esc_css($str);
}



/**
 * Escape attributes
 * @param string $str
 */
function esc_attr($str) {
  $str = htmlspecialchars($str, ENT_QUOTES);
  return $str;
}

// Direct echo
function _esc_attr($str) {
  echo esc_attr($str);
}



/**
 * Escape textarea
 * @param string $str
 */
function esc_textarea($str) {
  $str = htmlspecialchars($str, ENT_QUOTES, CHARSET);
  return $str;
}

// Direct echo
function _esc_textarea($str) {
  echo esc_textarea($str);
}



/**
 * Escape URL
 * @param string $url
 */
function esc_url($url) {
  if ($url === '') {
    return $url;
  }

  $url = str_replace(' ', '%20', ltrim($url));
  $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url);
  $url = str_replace(';//', '://', $url);
  $url = str_replace(':/','://', trim(preg_replace('/\/+/', '/', $url), '/'));
  $url = filter_var($url, FILTER_SANITIZE_URL);

  return $url;
}

// Direct echo
function _esc_url($url) {
  echo esc_url($url);
}



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
  }
  else {
    return false;
  }
}

// Direct echo (Returns a string each value is seperated by a comma (,))
function _str_between_all($str, $start, $end) {
  if(is_array(str_between_all($str, $start, $end))){
    echo implode(', ', str_between_all($str, $start, $end));
  }
  else {
    return false;
  }
}


/**
 * Camel case to snake case
 * This will convert camelcase to snake case
 * @param string $str string to convert
 */
function to_snakecase($str) {
  return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
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
function isJSON($str){
  return is_string($str) && is_array(json_decode($str, true)) ? true : false;
}


/**
 * Pretty Dumper for JSON
 * @param string The json string to dump
 * @return html dumped DOM
 */
function json_dump($str) {
  $id = 'jd_'.rand(0, 10);
  return SubHelp::jsonDumper($id, $str);
}


/**
 * Pretty SQL Dumper
 * @param string SQL string to highlight with colors
 */
function sql_dumper($str) {
  return SubHelp::sqlDumper($str);
}


# ----------------------------------------------------
// Short functions for existnig Object methods
# ----------------------------------------------------
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
 * Short way to render view with Data
 * @param string $view // View file to be rendered
 * @param array $data // Data array to be accessible on defined view
 */
function view($view, $data=[]) {
  return Router::view($view, $data);
}



/**
 * Internal var dump of ozz
 * @param $arg content to dumped
 */
function ozz_dump($arg) {
  return Help::dump($arg); 
}

function _dump($arg) {
  return ozz_dump($arg);
}



# ----------------------------------------------------
// Short functions for Error handling
# ----------------------------------------------------
/**
 * Returns all current errors
 */
function errors() {
  return Errors::all();
}

/**
 * Returns Boolean
 */
function has_error($key='') {
  return Errors::has($key);
}

/**
 * Return only required error
 */
function get_error($key='') {
  return Errors::get($key);
}

/**
 * Set new error
 */
function set_error($k, $v) {
  return Errors::set($k, $v);
}

/**
 * Remove Error
 */
function remove_error($k) {
  return Errors::remove($k);
}

/**
 * Clear All Errors
 */
function clear_error() {
  return Errors::clear();
}



# ----------------------------------------------------
// Short functions for Debug Bar
# ----------------------------------------------------
/**
 * Console log information to Debug bar
 * @param string|array|int|object $value the debug log value
 */
function console_log($value=null) : void {
  if (DEBUG) {
    global $DEBUG_BAR;
    $line_info = debug_backtrace();
    $new_log = $DEBUG_BAR->get('ozz_message');
    array_push($new_log, $line_info[0]);
    $DEBUG_BAR->set('ozz_message', $new_log);
  }
}