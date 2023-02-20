<?php
use Ozz\Core\Errors;

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

/**
 * Show Error / Warning / Success Message
 * @param string $wrapper HTML wrapper for each error ( ## ) will be the placeholder
 * @param boolean $all show only first error if false, Show all errors if true (default -> true)
 */
function show_errors($wrapper=null, $all=true) {
  if(Errors::has()){
    if((has_error('warning'))){
      $err_w = is_array(get_error('warning')) ? array_values(get_error('warning'))[0] : get_error('warning');
      echo is_null($wrapper) 
        ? '<span class="message warning">'.$err_w.'</span>' 
        : str_replace('##', '<span class="message warning">'.$err_w.'</span>', $wrapper);
    }

    if(has_error('info')) {
      $err_i = is_array(get_error('info')) ? array_values(get_error('info'))[0] : get_error('info');
      echo is_null($wrapper) 
        ? '<span class="message info">'.$err_i.'</span>' 
        : str_replace('##', '<span class="message info">'.$err_i.'</span>', $wrapper);
    }

    if(has_error('success')){
      $err_s = is_array(get_error('success')) ? array_values(get_error('success'))[0] : get_error('success');
      echo is_null($wrapper)
        ? '<span class="message success">'.$err_s.'</span>' 
        : str_replace('##', '<span class="message success">'.$err_s.'</span>', $wrapper);
    }

    $errs = [];
    foreach (get_error() as $key => $err_msg) {
      $err_msg = is_array($err_msg) ? array_values($err_msg)[0] : $err_msg;

      if(!in_array($key, ['success', 'warning', 'info'])){
        if($all === true){
          echo is_null($wrapper) 
            ? '<span class="message danger">'.$err_msg.'</span>' 
            : str_replace('##', '<span class="message danger">'.$err_msg.'</span>', $wrapper);
        } else {
          $errs[] = $err_msg;
        }
      }
    }

    if(!empty($errs)){
      echo is_null($wrapper)
        ? '<span class="message danger">'.$errs[0].'</span>'
        : str_replace('##', '<span class="message danger">'.$errs[0].'</span>', $wrapper);
    }
  }
}

/**
 * Show single error by key
 * @param string $key Error key
 * @param string $wrapper HTML wrapper for each error ( ## ) will be the placeholder
 */
function show_error(string $key, $wrapper=null) {
  if(Errors::has($key)){
    $err = is_array(get_error($key)) ? get_error($key)[0] : get_error($key);
    echo is_null($wrapper) ? $err : str_replace('##', $err, $wrapper);
  }
}