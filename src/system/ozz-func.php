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
use Ozz\Core\Session;
use Ozz\Core\Err;
use Ozz\Core\Sanitize;
use Ozz\Core\Response;
use Ozz\Core\Cache;
use Ozz\Core\system\SubHelp;

// Check if functions already declared
if(!function_exists('ozz_func_loaded')) {

# ----------------------------------------------------
// Ozz escaping functions
# ----------------------------------------------------
// HTML Char sets
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


function ozz_func_loaded() {
  return true;
}


/**
 * Get .env values
 * @param string $key the key of .env value
 */
function env($key=null, $key2=null){
  $env = parse_ini_file(__DIR__.SPC_BACK['core_1'].'env.ini', true);
  if($key !== null && $key2 !== null){
    return $env[$key][$key2];
  } elseif($key !== null){
    return $env[$key];
  } else {
    return $env;
  }
}



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

function esx($str){
  return esc_x($str);
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


/**
 * Encode HTML
 * @param string $str direct HTML
 * 
 * @return string encoded HTML as string
 */
function html_encode($str){
  return Sanitize::htmlEncode($str);
}


/**
 * Decode encoded HTML
 * @param string $str encoded HTML
 * 
 * @return HTML
 */
function html_decode($str){
  return Sanitize::htmlDecode($str);
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

// Direct echo (Returns a string each value is separated by a comma (,))
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
 * Pretty Dumper for JSON
 * @param string The json string to dump
 * @return html dumped DOM
 */
function json_dump($str) {
  $id = 'jd_'.rand(0, 10);
  return SubHelp::jsonDumper($id, $str);
}


/**
 * Return as json
 * @param array $data Array to convert into JSON
 * @param string $flags json encoding flags
 * @param int $status_code HTTP Response status code (Default: 200)
 * @param array $custom_headers HTTP Headers
 */
function json(array $data, $flags=null, $status_code=200, array $custom_headers=null) {
  $content = !is_null($flags) ? json_encode($data, $flags) : json_encode($data);
  $headers['Content-Type'] = 'application/json; charset='.CHARSET;

  if(!is_null($custom_headers)){
    foreach ($custom_headers as $k => $v) {
      $headers[$k] = $v;
    }
  }

  $response = new Response($content, $status_code, $headers);

  return $response->send();
}


/**
 * Pretty SQL Dumper
 * @param string SQL string to highlight with colors
 */
function sql_dumper($str) {
  return SubHelp::sqlDumper($str);
}


# ----------------------------------------------------
// Short functions for existing Object methods
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
 * @param string $template // Base template
 */
function view($view, $data=[], $template='') {
  return Router::view($view, $data, $template);
}


/**
 * Short way to Redirect back
 * @param string $add Concat string after URL
 * @param int $status Redirect Status Code
 */
function back($add='', $status=301) {
  return Router::back($add, $status);
}


/**
 * Render component with parameters
 * @param string $component Component name
 * @param array|string|object $args Parameters
 */
function component($component, $args=null) {
  $dom = '';
  $component_info = has_flash('ozz_components') ? get_flash('ozz_components') : [];
  $comp_key = count($component_info);

  if(file_exists(VIEW.'components/'.$component.'.phtml')){
    if(isset($args) && is_array($args)){
      extract($args);
    }

    ob_start();
    include VIEW.'components/'.$component.'.phtml';
    $dom = ob_get_contents();
    ob_end_clean();

    // Set up to log into debug bar
    if(DEBUG){
      $component_info[$comp_key] = [
        'file' => "view/components/$component.phtml",
        'args' => $args,
      ];
    }
  }
  elseif(file_exists(VIEW.'components/'.$component.'.html')){
    $dom = file_get_contents(VIEW.'components/'.$component.'.html');
    DEBUG ? $component_info[$comp_key]['file'] = "view/components/$component.html" : false; // Log to debug bar
  }
  else {
    return DEBUG
    ? Err::componentNotFound($component)
    : false;
  }

  // Log into flash session
  set_flash('ozz_components', $component_info);

  return $dom;
}

function _component($component, $args=null) {
  echo component($component, $args);
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
// Short functions for Flash session handling
# ----------------------------------------------------
/**
 * Return all available flash session values
 */
function flash($k='') {
  $flash = Session::has('__flash') && !empty(Session::get('__flash')) ? Session::get('__flash') : false;
  if($flash && $k !== ''){
    return $flash[$k];
  } else {
    return $flash;
  }
}

/**
 * Set new flash session
 */
function set_flash($k, $v) {
  Session::flash($k, $v, false);
}

/**
 * Return Boolean
 */
function has_flash($k='') {
  $flash = Session::has('__flash') && !empty(Session::get('__flash')) ? Session::get('__flash') : false;
  if($flash === false){
    return false;
  } elseif($flash && $k !== ''){
    return isset($flash[$k]);
  } else {
    return true; // Has flash & key not provided
  }
}

/**
 * Return Boolean
 */
function get_flash($k='') {
  return flash($k);
}

/**
 * Remove single flash value (use before request end)
 */
function remove_flash($k) {
  unset($_SESSION['__flash'][$k]);
}

/**
 * Clear all flash values (use before request end)
 */
function clear_flash() {
  unset($_SESSION['__flash']);
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
function has_error($k='') {
  return Errors::has($k);
}

/**
 * Returns all current errors
 */
function error($k='') {
  return get_error($k);
}

/**
 * Set new error
 */
function set_error($k, $v) {
  return Errors::set($k, $v);
}

/**
 * Return only required error
 */
function get_error($k='') {
  return Errors::get($k) ?? false;
}

/**
 * Return only first error
 */
function first_error() {
  $errs = Errors::get();
  return !is_null($errs) ? array_values($errs)[0] : null;
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
// Application Cache
# ----------------------------------------------------
/**
 * Purge Cache
 * @param string $typ Cache type to clear
 */
function purge_cache($typ){
  return (new Cache)->purge($typ);
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


} // Function existence check END