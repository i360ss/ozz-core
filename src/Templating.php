<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Sanitize;
use Ozz\Core\Response;
use Ozz\Core\Err;

class Templating extends AppInit {

  protected static $cdt=[]; // Current Data to load on view
  private static $debug_view = [];
  private static $view_file = false; // View File
  private static $view_dir = '';
  private static $view_outer_use = '';

  /**
   * Render Template
   * @param string  $vv                         View file (phtml/html)
   * @param mixed   $data                       Data from controller/router to load into view
   * @param array   $context                    Basic Data to load into view (by default)
   * @param string  $base_template              Base template defined on Router::render() method or view function
   * @param string  $base_template_from_router  Base template defined on router
   * 
   * @return /DOM   Final view DOM to render
   * 
   * $base_template will be used over $base_template_from_router if it is not empty
   */
  public static function render($vv, $customData, $base_template, $base_template_from_router, $context){
    global $DEBUG_BAR;

    // Find current view file
    $base_cms_viv = BASE_DIR.'cms/view/'.$vv.'.phtml';
    $base_app_viv = BASE_DIR.'app/view/'.$vv.'.phtml';

    if(file_exists($base_cms_viv) && env('app', 'ENABLE_CMS')){
      // CMS instance
      self::$view_dir = BASE_DIR.'cms/view/';
      self::$view_file = $base_cms_viv;
      self::$view_outer_use = 'cms/view/';
      $context['instance'] = 'cms/';
    } elseif(file_exists($base_app_viv)){
      // App instance
      self::$view_dir = BASE_DIR.'app/view/';
      self::$view_file = $base_app_viv;
      self::$view_outer_use = 'app/view/';
      $context['instance'] = 'app/';
    }

    $regComps = [];
    $data = Sanitize::templateContext($customData);
    $context['view'] = $vv;
    $context['query'] = $context['request']['query'];
    $context['layout'] = (isset($base_template) && $base_template !== '')
      ? $base_template
      : ($base_template_from_router ? $base_template_from_router : 'layout');

    if ($context['instance'] !== 'cms/') {
      require APP_DIR."/functions.php";
    }

    DEBUG ? self::$debug_view['view_data'] = $data : false; // Log to debug bar
    self::$cdt = [$context, $data];

    if(file_exists(self::$view_file)){
      DEBUG ? self::$debug_view['view_file'] = self::$view_outer_use."$vv.phtml" : false; // Log to debug bar

      $viewContentAll = self::setView($vv);
      $viewContent = $viewContentAll[0];
      $vars = $viewContentAll[1];

      if(!isset($base_template_from_router) && $base_template==''){
        self::render_final_view($viewContent); // Render final output
      }
      else{
        preg_match_all("~\{\{\s*(.*?)\s*\}\}~", $viewContent, $regComps['view']);

        // Get Variables from view to use on base layout
        $variables = [];
        foreach ($regComps['view'][1] as $vl) {
          $vl = explode('=', $vl);
          if(count($vl) > 1){
            $variables[trim($vl[0])] = self::is_string($vl[1]) ? self::trim_string($vl[1]) : self::return_var($vl[1], $vars);
          }
        }

        // Get Base layout content
        $base_layout = self::layout($context['layout'], $variables);

        preg_match_all("~\{\%\s*(.*?)\s*\%\}~", $base_layout, $regComps['base']);

        // Set up view page content
        $viewComp = [];
        foreach ($regComps['base'][1] as $v) {
          if(in_array($v, $regComps['view'][1])){
            $viewComp[$v] = self::get_string_between($viewContent, "{{ $v }}", "{{ $v-end }}");
          }
          elseif(preg_grep("/^$v\s*=/i", $regComps['view'][1])){
            $viewComp[$v] = $variables[$v];
          }
          else{
            $base_layout = str_replace("{% $v %}", '', $base_layout);
          }
        }

        // Replace Blocks on layout template by view contents
        foreach ($viewComp as $k => $v) {
          $base_layout = str_replace("{% $k %}", $v, $base_layout);
        }

        // Set view info to debug bar
        DEBUG ? $DEBUG_BAR->set('ozz_view', self::$debug_view) : false;

        self::render_final_view($base_layout); // Render final output
      }
    }
    else{
      Err::viewNotFound($vv);
      self::render_final_view('');
    }
  }

  /**
   * Set Response and render the final view
   * @param string|HTML final Content
   */
  private static function render_final_view($base_layout){
    $base_layout = CONFIG['MINIFY_HTML'] ? Help::minifyHTML($base_layout) : $base_layout; // Minify HTML if required
    $response = Response::getInstance();
    if(!$response->hasHeader('Content-Type')){
      $response->setHeader('Content-Type', 'text/html; charset='.CONFIG['CHARSET']);
    }
    $response->setContent($base_layout);
    $response->send();
  }

  /**
   * Main Layout Template
   * @param string $temp base template
   * @param array $vars Variables defined on view file
   */
  private static function layout($temp, $vars){
    ob_start();
    $context = self::$cdt[0];
    $data = self::$cdt[1];

    if(file_exists(self::$view_dir.'base/'. $temp . '.phtml')){
      extract($vars);
      require self::$view_dir.'base/'. $temp.'.phtml';
      DEBUG ? self::$debug_view['base_file'] = self::$view_outer_use."base/$temp.phtml" : false; // Log to debug bar
    } else{
      return Err::baseTemplateNotFound($temp);
    }

    $lay = ob_get_contents();
    ob_end_clean();
    return $lay;
  }

  /**
   * Set Up View Template with Components
   * @param string $v view file name
   */
  private static function setView($v){
    ob_start();
    $context = self::$cdt[0];
    $data = self::$cdt[1];
    extract($data); // Extract data to view
    require self::$view_dir.$v.'.phtml';
    $vars = get_defined_vars();
    $view = ob_get_contents();
    ob_end_clean();

    // Clean HTML Comments on View file
    $view = preg_replace('/\<\!--.*?-->/', '', $view);

    // Get all Component Placeholders
    preg_match_all("~\{\:\s*(.*?)\s*\:\}~", $view, $comps);

    // Set All Components
    foreach ($comps[1] as $k => $c) {
      $parts = array_map('trim', explode('|', $c));

      if(file_exists(self::$view_dir.'components/'.$parts[0].'.phtml')){
        DEBUG ? self::$debug_view['components'][$k]['file'] = self::$view_outer_use."components/$parts[0].phtml" : false; // Log to debug bar

        ob_start();

        // Setup arguments to component
        if(count($parts) == 2){
          // Clear previous component's args and extracted variables
          if(isset($args) && is_array($args)){
            foreach ($args as $k => $v) {
              unset(${$k});
            }
          }
          unset($args, $temp);

          if(self::is_string($parts[1])){
            // Get the $args as string
            $args = self::trim_string($parts[1]);
          }
          else{
            // Get the $args as array, variable or single string (without quotes)
            $keys = array_map('trim', explode('.', $parts[1]));
            if(count($keys) > 1){ // if array
              if(isset(${$keys[0]})){
                $temp = ${$keys[0]};
                foreach ($keys as $i => $vl) {
                  $i !== 0 ? $temp =& $temp[$vl] : false;
                }
                $args = !is_null($temp) ? $temp : false;
              } else {
                $args = $keys;
              }
            }
            elseif(count($keys) == 1 && !isset(${$keys[0]})) {
              if(is_numeric($keys[0])){
                $args = (int)$keys[0];
              } elseif(explode(',', $keys[0])){
                $args = array_map('trim', explode(',', $keys[0])); // return as array if args has comma (,) separations
              } else {
                $args = (string)$keys[0];
              }
            }
            else{ // if variable
              $args = !is_null(${$keys[0]}) ? ${$keys[0]} : false;
            }
          }
        }
        else{
          $args = false;
        }

        DEBUG ? self::$debug_view['components'][$k]['args'] = $args : false; // Log to debug bar

        $args && is_array($args) ? extract($args) : false; // Extract args
        include self::$view_dir.'components/'.$parts[0].'.phtml';
        $component = ob_get_contents();
        ob_end_clean();
      }
      elseif(file_exists(self::$view_dir.'components/'.$parts[0].'.html')){
        $component = file_get_contents(self::$view_dir.'components/'.$parts[0].'.html'); 
        DEBUG ? self::$debug_view['components'][$k]['file'] = self::$view_outer_use."components/$parts[0].html" : false; // Log to debug bar
      }
      else{ 
        Err::componentNotFound($parts[0]);
        $component = ''; 
      }

      $possibleComp = ["{: $c :}", "{:$c:}", "{:$c :}", "{: $c:}"];
      $view = str_replace($possibleComp, $component, $view);
    }
    return [$view, $vars];
  }

  /**
   * Check is string (wrapped in single or double quotes)
   * @param string $c string to check
   */
  private static function is_string($c) {
    $c = trim($c);
    return ($c[0] == '\'') && (substr($c,-1) == '\'') || ($c[0] == '"') && (substr($c,-1) == '"');
  }

  /**
   * Trim string (wrapped in single or double quotes)
   * @param string $a
   */
  private static function trim_string($a){
    $a = trim($a);
    $args = trim($a, '"');
    return trim($args, '\'');
  }

  /**
   * Return Variable / Array (Convert string to variable/array)
   * @param string $str
   * @param array $vars Variables
   */
  private static function return_var($str, $vars){
    $keys = array_map('trim', explode('.', $str));
    if(count($keys) > 1){
      $temp = $vars[$keys[0]];
      foreach ($keys as $i => $vl) {
        $i !== 0 ? $temp =& $temp[$vl] : false;
      }
      return !is_null($temp) ? $temp : false;
    } else{
      return !is_null($vars[$keys[0]]) ? $vars[$keys[0]] : false; // Var
    }
  }

  /**
   * Get string between
   * @param string $str full string
   * @param string $stt Start position
   * @param string $end End position
   */
  private static function get_string_between($str, $stt, $end){
    $str = ' ' . $str;
    $ini = strpos($str, $stt);
    if ($ini == 0) return '';
    $ini += strlen($stt);
    $len = strpos($str, $end, $ini) - $ini;
    return substr($str, $ini, $len);
  }

}