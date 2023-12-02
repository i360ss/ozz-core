<?php
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
// Short hand
function c_log($val=null) {
  return console_log($val);
}