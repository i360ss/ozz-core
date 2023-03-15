<?php
use Ozz\Core\Router;
use Ozz\Core\Err;

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