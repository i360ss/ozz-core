<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 */

namespace Ozz\Core;

use Ozz\Core\Help;
use Ozz\Core\system\SubHelp;

class ExceptionHandler {

  private $config;

  public function __construct(){
    if(CONFIG['OZZ_EXCEPTION_HANDLER'] === false){
      return false;
    }

    $this->config = parse_ini_file(__DIR__.'/../../../../'.'env.ini', true);

    if($this->config['app']['DEBUG']){
      error_reporting(E_ALL | E_DEPRECATED);

      // Enable error log and error display
      if(CONFIG['ERROR_LOG'] === true){
        ini_set('display_errors', 1);
        ini_set('log_errors', 1);
        ini_set('error_log', __DIR__.'/../../../../storage/log/error_log.log');
      }

      // Handle exceptions
      set_exception_handler(function($exception){
        self::handler($exception);
      });

      // Handle Warnings
      set_error_handler(function($type, $errstr, $err_file, $err_line) {
        if(in_array($type, [E_NOTICE, E_WARNING, E_DEPRECATED])){
          throw new \ErrorException($errstr, 0, $type, $err_file, $err_line);
        }
      });

      register_shutdown_function(function(){
        $f_error = error_get_last();
        if(isset($f_error) && count($f_error) > 0){
          // Temporary fatal error handler
          $fatal_error = "<div class='ozz_errorOutput' style='padding:10px 20px; max-width: 900px; margin: 3px auto; border: 1px solid #FF5968; background: #FF5968;'><code><strong><h3 style='color: #fff;'>Error: ".$f_error['message']."</code></strong></h3></div>
          <div style='padding:10px 20px; max-width: 900px; margin: 3px auto; color: #fff; font-size: 14px; line-height: 1.7; border: 1px solid #666EE8; background:#666EE8;'><code>File: ".$f_error['file']." : ".$f_error['line']."</code></div>";

          echo $fatal_error;
        }
      });
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
      $errType = $exception->getSeverity();
      switch ($errType) {
        case E_ERROR:
          $severity = 'Fatal Error';
          break;
        case E_WARNING:
          $severity = 'Warning';
          break;
        case E_PARSE:
          $severity = 'Parse Error';
          break;
        case E_NOTICE:
          $severity = 'Notice';
          break;
        case E_CORE_ERROR:
          $severity = 'Core Error';
          break;
        case E_CORE_WARNING:
          $severity = 'Core Warning';
          break;
        case E_COMPILE_ERROR:
          $severity = 'Compile Error';
          break;
        case E_COMPILE_WARNING:
          $severity = 'Compile Warning';
          break;
        case E_USER_ERROR:
          $severity = 'User Error';
          break;
        case E_USER_WARNING:
          $severity = 'User Warning';
          break;
        case E_USER_NOTICE:
          $severity = 'User Notice';
          break;
        case E_STRICT:
          $severity = 'Strict Standards';
          break;
        case E_RECOVERABLE_ERROR:
          $severity = 'Recoverable Fatal Error';
          break;
        case E_DEPRECATED:
          $severity = 'Deprecated';
          break;
        case E_USER_DEPRECATED:
          $severity = 'User Deprecated';
          break;
        default:
          $severity = 'Unknown Error Type';
      }
    } else {
      $severity = 'Exception';
    }

    $modified_exception .= '<div class="ozz-exception-heading">';
    $modified_exception .= '<pre class="label">'.$severity.'</pre>';
    $modified_exception .= '<pre class="title">'.$exception->getMessage().'</pre>';
    $modified_exception .= '<pre class="file">File: '.$exception->getFile().' : '.$exception->getLine().'</pre>';
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
    $start = max($primary_line - 15, 0);
    $end = min($primary_line + 15, count($lines) - 1);
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
          $start = max($t_line - 15, 0);
          $end = min($t_line + 15, count($t_lines) - 1);
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

    global $DEBUG_BAR;
    $DEBUG_BAR->show();

    echo $style.$modified_exception.$script;
  }

}