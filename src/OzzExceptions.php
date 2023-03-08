<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 */

namespace Ozz\Core;

use Ozz\Core\Help;
use Ozz\Core\system\SubHelp;

class OzzExceptions {

  private $config;

  public function __construct(){
    $this->config = parse_ini_file(__DIR__.'/../../../../'.'env.ini', true);

    if($this->config['app']['DEBUG']){
      // Handle exceptions
      set_exception_handler(function($exception){
        self::handler($exception);
      });

      // Handle Warnings
      set_error_handler(function($errno, $errstr, $err_file, $err_line) {
        if($errno == E_NOTICE || $errno == E_WARNING){
          throw new \ErrorException($errstr, 0, $errno, $err_file, $err_line);
        }
      });
    } else {
      return false;
    }
  }

  /**
   * Default Ozz exception handler
   */
  public static function handler($exception) {    
    // Print the context to the screen
    $style = '<style nonce="'.CSP_NONCE.'">'.Help::minifyCSS(file_get_contents(__DIR__.'/system/assets/css/exceptions.css')).'</style>';
    $script = '<script type="text/javascript" nonce="'.CSP_NONCE.'">'.file_get_contents(__DIR__.'/system/assets/js/exceptions.js').'</script>';
    $modified_exception = '<div class="ozz-exceptions"><div class="ozz-exceptions-container">';

    // Exception Heading
    if(method_exists($exception, 'getSeverity')){
      if($exception->getSeverity() == E_WARNING){
        $severity = 'Warning';
      } elseif($exception->getSeverity() == E_NOTICE){
        $severity = 'Notice';
      }
    } else {
      $severity = 'Exception';
    }

    $modified_exception .= '<div class="ozz-exception-heading">';
    $modified_exception .= '<pre class="label">'.$severity.'</pre>';
    $modified_exception .= '<pre class="title">'.$exception->getMessage().'</pre>';
    $modified_exception .= '<pre class="file">File: '.$exception->getFile().'</pre>';
    $modified_exception .= '<pre class="line">Line: '.$exception->getLine().'</pre>';
    $modified_exception .= '</pre></div>';

    $modified_exception .= '<div class="trace-code-wrapper">';

    // Trace
    $modified_exception .= '<div class="trace-menu">';

    // Primary exception link
    $modified_exception .= '<div class="single-trace primary-exception active" data-menu-key="0">';
    $modified_exception .= '<div><strong>'.$exception->getFile().'</strong> : '.$exception->getLine().'</div>';
    $modified_exception .= '</div>';

    if(method_exists($exception, 'getTrace')){
      foreach ($exception->getTrace() as $key => $value) {
        $this_line = isset($value['line']) ? ' : '.$value['line'] : '';
        $modified_exception .= '<div class="single-trace" data-menu-key="'.($key+1).'">';
        $modified_exception .= isset($value['file']) ? '<div><strong>'.$value['file'].'</strong>'.$this_line.'</div>' : '';
        $modified_exception .= isset($value['function']) ? '<div><em>Fn: '.$value['function'].'</em></div>' : '';
        $modified_exception .= isset($value['class']) ? '<div><em>Class: '.$value['class'].'</em></div>' : '';
        $modified_exception .= '</div>';
      }
    }
    $modified_exception .= '</div>';

    // Code Highlights
    $modified_exception .= '<div class="code-highlight"><pre>';

    // Primary exception code highlight
    $primary_line = $exception->getLine();
    $lines = file($exception->getFile());
    $start = max($primary_line - 25, 0);
    $end = min($primary_line + 25, count($lines) - 1);
    $context = array_slice($lines, $start, $end - $start + 1);

    $modified_exception .= '<div class="single-code-snippet active code-snippet-0">'; // Code highlight class end
    foreach($context as $i => $text){
      $ln = $start + $i + 1;
      if($ln == $primary_line){
        $modified_exception .= '<div class="code-highlight__line code-highlight__line--active"><span class="line-no">'.$ln.' </span>';
        $modified_exception .= '<code>'.SubHelp::phpHighlight($text).'</code>';
        $modified_exception .= '</div>';
      } else {
        $modified_exception .= '<div class="code-highlight__line"><span class="line-no">'.$ln.' </span>';
        $modified_exception .= '<code>'.SubHelp::phpHighlight($text).'</code>';
        $modified_exception .= '</div>';
      }
    }
    $modified_exception .= '</div>'; // Primary Code highlight class end

    // Trace code highlight
    $modified_exception .= '<div class="trace-highlight">'; // Trace code highlight wrapper start
    if(method_exists($exception, 'getTrace')){
      foreach ($exception->getTrace() as $key => $value) {
        $t_file = isset($value['file']) ? $value['file'] : false;
        $t_line = isset($value['line']) ? $value['line'] : false;

        if($t_file && $t_line){
          $t_lines = file($t_file);
          $start = max($t_line - 25, 0);
          $end = min($t_line + 25, count($t_lines) - 1);
          $t_context = array_slice($t_lines, $start, $end - $start + 1);

          $modified_exception .= '<div class="single-code-snippet trace-single-highlight code-snippet-'.($key+1).'">'; // Trace single code highlight start
          $modified_exception .= '<div class="trace-snippet-head"><em>File: '.$t_file.' : '.$t_line.'</em></div>'; // Single trace header start

          foreach($t_context as $i => $text){
            $ln = $start + $i + 1;
            if($ln == $t_line){
              $modified_exception .= '<div class="code-highlight__line code-highlight__line--active"><span class="line-no">'.$ln.' </span>';
              $modified_exception .= '<code><strong>'.SubHelp::phpHighlight($text).'</strong></code>';
              $modified_exception .= '</div>';
            } else {
              $modified_exception .= '<div class="code-highlight__line"><span class="line-no">'.$ln.' </span>';
              $modified_exception .= '<code>'.SubHelp::phpHighlight($text).'</code>';
              $modified_exception .= '</div>';
            }
          }
          $modified_exception .= '</div>'; // Trace single code highlight end
        }
      }
    }
    $modified_exception .= '</div>'; // Trace Code wrapper class end
    $modified_exception .= '</pre></div>'; // Code highlight class end
    $modified_exception .= '</div>'; // Trace code highlight wrapper end
    $modified_exception .= '</div></div>'; // Parent and container classed end

    echo $style.$modified_exception.$script;
  }

}