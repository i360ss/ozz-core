<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

use Ozz\Core\Sanitize;

class Templating extends Appinit {
  
  protected static $cdt=[]; // Current Data to load on view
  
  # ----------------------------------
  # Render Template
  # ----------------------------------
  /**
   * @param string  $vv          View file (phtml/html)
   * @param mixed   $data        Data from controller/router to load into view
   * @param string  $basetemp    Base template defined on Router::render() method or render function
   * @param string  $basetemp_from_router   Base template defined on router
   * 
   * @return /DOM   Final view DOM to render
   * 
   * $basetemp will be used over $basetemp_from_router if it is not empty
   */
  public static function render($vv, $data, $basetemp, $basetemp_from_router){
    $regComps = [];
    $context = Sanitize::tempContext($data);
    self::$cdt = [$context, json_encode($context, JSON_FORCE_OBJECT)];

    if(file_exists(VIEW . $vv . '.phtml')){
      $viewContent = self::setView($vv)[0];
      $vars = self::setView($vv)[1];
      
      if(!isset($basetemp_from_router) && $basetemp==''){
        return $viewContent;
      }
      else{
        if($basetemp!==''){ $baselay = self::layout($basetemp); }
        else{ $baselay = self::layout($basetemp_from_router); }
        
        preg_match_all("~\{\{\s*(.*?)\s*\}\}~", $viewContent, $regComps['view']);
        preg_match_all("~\{\%\s*(.*?)\s*\%\}~", $baselay, $regComps['base']);
      
        // Set up view page content
        $viewComp = [];
        foreach ($regComps['base'][1] as $v) {
          if(in_array($v, $regComps['view'][1])){
            $viewComp[$v] = self::get_string_between($viewContent, "{{ $v }}", "{{ $v-end }}");
          }
          elseif(preg_grep("/^$v::/i", $regComps['view'][1])){
            $vvCon = explode('::', array_merge(preg_grep("/^$v::/i", $regComps['view'][1]))[0])[1];

            if(self::is_string($vvCon)){
              $viewComp[$v] = self::trim_string($vvCon);
            }
            else{
              // Get the context as array or variable
              $keys = array_map('trim', explode('.', self::trim_string($vvCon)));
              if(count($keys) > 1){
                $temp = $vars[$keys[0]];
                foreach ($keys as $i => $vl) {
                  $i !== 0 ? $temp =& $temp[$vl] : false;
                }
                $viewComp[$v] = !is_null($temp) ? $temp : false;
              } else{
                $viewComp[$v] = !is_null($vars[$keys[0]]) ? $vars[$keys[0]] : false; // Var
              }
            }
          }
          else{
            $baselay = str_replace("{% $v %}", '', $baselay);
          }
        }
        
        // Replace Blocks on layout template by view contents
        foreach ($viewComp as $k => $v) {
          $baselay = str_replace("{% $k %}", $v, $baselay);
        }
        return $baselay;
      }
    }
    else{
      return Err::viewNotFound(VIEW . $vv . '.phtml');
    }
  }



  // Main Layout Template
  private static function layout($temp){
    ob_start();
    $data = self::$cdt[0];
    $data_json = self::$cdt[1];
    $temp = ($temp == '') ? 'base/layout' : $temp;
    if(file_exists(VIEW . $temp . '.phtml')){ require VIEW . $temp.'.phtml'; }
    else{ return Err::baseTemplateNotFound(VIEW . $temp . '.phtml'); }
    $lay = ob_get_contents();
    ob_end_clean();
    return $lay;
  }



  // Set Up View Template with Components
  private static function setView($v){
    ob_start();
    $data = self::$cdt[0];
    $json_data = self::$cdt[1];
    require VIEW.$v.'.phtml';
    $vars = get_defined_vars();
    $view = ob_get_contents();
    ob_end_clean();
    
    // Clean HTML Comments on View file
    $view = preg_replace('/\<\!--.*?-->/', '', $view);

    // Get all Component Placeholders
    preg_match_all("~\{\:\s*(.*?)\s*\:\}~", $view, $comps);

    // Set All Components
    foreach ($comps[1] as $c) {
      $parts = array_map('trim', explode('|', $c));

      if(file_exists(VIEW.'components/'.$parts[0].'.phtml')){
        ob_start();
        
        // Setup arguments to component
        if(count($parts) == 2){
          unset($args, $temp);

          if(self::is_string($parts[1])){
            // Get the $args as string
            $args = self::trim_string($parts[1]);
          }
          else{
            // Get the $args as array or variable
            $keys = array_map('trim', explode('.', $parts[1]));
            if(count($keys) > 1){
              $temp = ${$keys[0]};
              foreach ($keys as $i => $v) {
                $i !== 0 ? $temp =& $temp[$v] : false;
              }
              $args = !is_null($temp) ? $temp : false;
            }
            else{
              $args = !is_null(${$keys[0]}) ? ${$keys[0]} : false;
            }
          }
        }
        else{
          $args = false;
        }

        $args && is_array($args) ? extract($args) : false; // Extract args
        include VIEW.'components/'.$parts[0].'.phtml';
        $component = ob_get_contents();
        ob_end_clean();
      }
      elseif(file_exists(VIEW.'components/'.$parts[0].'.html')){
        $component = file_get_contents(VIEW.'components/'.$parts[0].'.html'); 
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



  // Check is string (wrapped in single or double quotes)
  private static function is_string($c) {
    $c = trim($c);
    return ($c[0] == '\'') && (substr($c,-1) == '\'') || ($c[0] == '"') && (substr($c,-1) == '"');
  }
  


  // Trim string (wrapped in single or double quotes)
  private static function trim_string($a){
    $a = trim($a);
    $args = trim($a, '"');
    return trim($args, '\'');
  }



  // Return Variable / Array (Convert string to variable/array)
  private static function return_vars($keys) {
    if(count($keys) > 1){
      $temp = ${$keys[0]};
      foreach ($keys as $i => $v) {
        $i !== 0 ? $temp =& $temp[$v] : false;
      }
      $args = !is_null($temp) ? $temp : false;
    }
    else{
      $args = !is_null(${$keys[0]}) ? ${$keys[0]} : false;
    }
    return $args;
  }

    
  // Templating Option
  private static function get_string_between($str, $stt, $end){
    $str = ' ' . $str;
    $ini = strpos($str, $stt);
    if ($ini == 0) return '';
    $ini += strlen($stt);
    $len = strpos($str, $end, $ini) - $ini;
    return substr($str, $ini, $len);
  }

}