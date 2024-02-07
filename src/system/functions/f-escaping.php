<?php
use Ozz\Core\Sanitize;

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

/**
 * Escape HTML
 * @param string $str
 */
function esc($str){
  if (0 === strlen($str)) { return ''; }
  if (!preg_match('/[&<>"\']/', $str)) { return $str; }
  if (isset($ozz_htmlSpecialChar[CONFIG['CHARSET']])) {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, CONFIG['CHARSET']);
  } elseif (isset($ozz_htmlSpecialChar[strtoupper(CONFIG['CHARSET'])])) {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, CONFIG['CHARSET']);
  } else {
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
  if (0 === strlen($str)) { return ''; }
  if (!preg_match('/[&<>"\']/', $str)) { return $str; }

  $search = array(
    '@<script[^>]*?>.*?</script>@si',
    '@<[\/\!]*?[^<>]*?>@si',
    '@<style[^>]*?>.*?</style>@siU',
    '@<![\s\S]*?--[ \t\n\r]*>@'
  );
  $rtn = preg_replace($search, '', $str);

  return $rtn;
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
  if (0 === strlen($str)) { return ''; }
  if (!preg_match('/[&<>"\']/', $str)) { return $str; }

  $str = htmlspecialchars($str, ENT_COMPAT, CONFIG['CHARSET']);
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
  $str = htmlspecialchars($str, ENT_QUOTES, CONFIG['CHARSET']);
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
function esc_url(string $url) {
  if ($url === '') {
    return $url;
  }

  $url = preg_replace('/(?:\.\.\/)+/', '', $url);
  $url = preg_replace('/\.{2,}/', '', $url);
  $url = preg_replace('/\.\//', '', $url);
  $url = str_replace(' ', '%20', ltrim($url));
  $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\[\]\\x80-\\xff]|i', '', $url);
  $url = str_replace(';//', '://', $url);
  $url = str_replace(':/','://', trim(preg_replace('/\/+/', '/', $url), '/'));
  $url = preg_replace("/\/+/", "/", $url);
  $url = filter_var($url, FILTER_SANITIZE_URL);

  return $url;
}

// Direct echo
function _esc_url(string $url) {
  echo esc_url($url);
}

// Sanitize SVG
function esx_svg($svg, $allowed_elms=[]) {
  return Sanitize::svg($svg, $allowed_elms);
}

function _esx_svg($svg, $allowed_elms=[]) {
  echo Sanitize::svg($svg, $allowed_elms);
}

/**
 * Encode HTML
 * @param string $str direct HTML
 * 
 * @return string encoded HTML as string
 */
function html_encode($str, $flag=false){
  return Sanitize::htmlEncode($str, $flag);
}

/**
 * Decode encoded HTML
 * @param string $str encoded HTML
 * 
 * @return HTML
 */
function html_decode($str, $flag=false){
  return Sanitize::htmlDecode($str, $flag);
}