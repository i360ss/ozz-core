<?php
use Ozz\Core\Session;

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