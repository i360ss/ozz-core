<?php
use Ozz\Core\Lang;

# ----------------------------------------------------
// Short functions for Translated string output
# ----------------------------------------------------
/**
 * Output Translated string messages
 */
function trans($key, $param=false) {
  $lang = new Lang;
  return $lang->message($key, $param) ;
}

/**
 * Output Translated string errors
 */
function trans_e($key, $param=false) {
  $lang = new Lang;
  return $lang->error($key, $param) ;
}

/**
 * Language switcher (Change language)
 * @param string New language code
 */
function switch_language($new_lang) {
  $lang = new Lang;
  $lang->switch($new_lang);
}

/**
 * Language name from code
 * @param string $lang_code Language code
 */
function lang_name($lang_code) {
  $langs = require __DIR__.'/../utils/language-codes.php';
  return isset($langs[$lang_code]) ? $langs[$lang_code] : 'Invalid language code';
}