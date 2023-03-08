<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\cli;

class CliUtils {

  public $styles;
  public $console_colors;

  public function __construct() {
    // Console Colors to direct print
    $this->styles = [
      "endc" => "\e[0m",
      "red" => "\e[91m",
      "yellow" => "\e[1;33m",
      "green" => "\e[32m",
      "blue" => "\e[34m",
      "cyan" => "\e[36m",
      "lightCyan" => "\e[96m",
      "gray" => "\e[1;30m",
      "wt" => "\e[1;37m",
      "br" => "\e[0m\n",
    ];

    // Console Colors
    $this->console_colors = [
      'color' => array(
        'black'         => '0;30',
        'dark_gray'     => '1;30',
        'blue'          => '0;34',
        'light_blue'    => '1;34',
        'green'         => '0;32',
        'light_green'   => '1;32',
        'cyan'          => '0;36',
        'light_cyan'    => '1;36',
        'red'           => '0;31',
        'light_red'     => '1;31',
        'purple'        => '0;35',
        'light_purple'  => '1;35',
        'brown'         => '0;33',
        'yellow'        => '1;33',
        'light_gray'    => '0;37',
        'white'         => '1;37',
      ),
      'background' => array(
        'black'       => '40',
        'red'         => '41',
        'green'       => '42',
        'yellow'      => '43',
        'blue'        => '44',
        'magenta'     => '45',
        'cyan'        => '46',
        'light_gray'  => '47',
      )
    ];
  }

  /**
   * Color console outputs
   * @param string $string String to be styled
   * @param string $foreground_color Text color
   * @param string $background_color Background color
   */
  public function color_string($string, $foreground_color=null, $background_color=null) {
    $colored_string = '';

    if (isset($this->console_colors['color'][$foreground_color])) {
      $colored_string .= "\033[" . $this->console_colors['color'][$foreground_color] . "m";
    }
    if (isset($this->console_colors['background'][$background_color])) {
      $colored_string .= "\033[" . $this->console_colors['background'][$background_color] . "m";
    }

    $colored_string .=  $string . "\033[0m";
    return $colored_string;
  }

  /**
   * Console Return
   * This method will return the final output to console when executing ozz commands 
   * @param string $message The Message/Output string
   * @param string $color Text color
   * @param string $background Background color
   * @param bool|number $brk Line breaks after $message
   * @param bool $bigbox show output with a padding (Highlight more)
   */
  public function console_return($message, $color=null, $background=null, $brk=true, $bigbox=false) {
    extract($this->styles);

    // Set Line break
    $break = '';
    if (!$brk) {
      $break = false;
    } elseif (is_numeric($brk)) {
      for ($i=0; $i < $brk; $i++) { 
        $break .= $br;
      }
    } elseif ($background == 'red') {
      $break = $br.$br;
    } else {
      $break = $br;
    }

    // Final Output
    if($bigbox) {
      $spaces = '';
      for ($i=0; $i < strlen($message); $i++) { 
        $spaces .= ' ';  
      }

      print($this->color_string('  '.$spaces.'  ', $background, $background) . $br);
      print($this->color_string('  '.$message.'  ', $color, $background) . $br);
      print($this->color_string('  '.$spaces.'  ', $background, $background) . $br.$br);
    } else {
      if(!$break){
        print($this->color_string($message, $color, $background));
      } else{
        print($this->color_string($message, $color, $background) . $break);
      }
    }
  }

  /**
   * Return error to Console
   * Just red text
   */
  public function console_error($message) {
    $this->console_return($message, 'red', null, true);
  }

  /**
   * Return warning to Console
   * Just yellow text
   */
  public function console_warn($message) {
    $this->console_return($message, 'yellow', null, true);
  }

  /**
   * Return success to Console
   * Just green text
   */
  public function console_success($message) {
    $this->console_return($message, 'green', null, true);
  }

  /**
   * Return defined colored response to Console
   */
  public function console_colored($message, $color) {
    $this->console_return($message, $color, null, true);
  }

}