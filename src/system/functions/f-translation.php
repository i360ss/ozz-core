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