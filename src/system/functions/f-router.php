<?php
use Ozz\Core\Router;
use Ozz\Core\Err;
use Ozz\Core\Response;

# ----------------------------------------------------
// Short functions for Router related works
# ----------------------------------------------------
/**
 * Short way to render view with Data
 * @param string $view View file to be rendered
 * @param array $data Data array to be accessible on defined view
 * @param string $template Base template
 */
function view($view, $data=[], $template='') {
  return Router::view($view, $data, $template);
}

/**
 * Short way to Redirect back
 * @param string $add Concat string after URL
 * @param int $status HTTP Status Code
 */
function back($add='', $status=301) {
  return Router::back($add, $status);
}

/**
 * Short way to redirect anywhere
 * @param string $to Path to redirect
 * @param int $status HTTP status code
 */
function redirect($to, $status=301) {
  return Router::redirect($to, $status);
}

/**
 * Render component with parameters
 * @param string $component Component name
 * @param array|string|object $args Parameters
 * @param string $instance Instance directory (eg: app, cms)
 */
function component($component, $args = null, $instance = 'app/') {
  $dom = '';
  $dir = BASE_DIR . $instance . '/view/components/';
  $comp = $dir . $component;
  $component_info = has_flash('ozz_components') ? get_flash('ozz_components') : [];
  $comp_key = count($component_info);

  // Check for both file types in a single call
  $file_phtml = $comp . '.phtml';
  $file_html = $comp . '.html';

  if (file_exists($file_phtml) || file_exists($file_html)) {
    if (isset($args) && is_array($args)) {
      extract($args);
    }

    ob_start();
    include file_exists($file_phtml) ? $file_phtml : $file_html;
    $dom = ob_get_contents();
    ob_end_clean();

    // Set up to log into debug bar
    if (DEBUG) {
      $component_info[$comp_key] = [
        'file' => $instance . 'view/components/' . $component . (file_exists($file_phtml) ? '.phtml' : '.html'),
        'args' => $args,
      ];
    }
  } else {
    return DEBUG ? Err::componentNotFound($component) : false;
  }

  // Log into flash session (moved inside the block)
  set_flash('ozz_components', $component_info);

  return $dom;
}


function _component($component, $args=null, $instance='app') {
  echo component($component, $args, $instance);
}

/**
 * Render Error pages
 * @param int $errorCode HTTP Error code
 * @param string $message
 * @param mixed $args Additional arguments
 */
function render_error_page($errorCode=404, $message='Page not found ', $args=false, $base_layout=CONFIG['DEFAULT_ERROR_PAGE_BASE_LAYOUT']) {
  $response = Response::getInstance();
  $response->setStatusCode($errorCode);
  view('http-error', [
    'error_code' => $errorCode,
    'error_message' => $message,
    'args' => $args,
  ], $base_layout);
  exit;
}
