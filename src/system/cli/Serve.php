<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\cli;

use Ozz\Core\system\cli\CliUtils;

class Serve {
  public function index($com){
    $utils = new CliUtils;
    extract($utils->styles);
    
    $conf = parse_ini_file(__DIR__.SPC_BACK['core_2'].'env.ini', true);
    $url = $conf['app']['APP_URL'].':'.$conf['app']['LOCAL_PORT'];

    $utils->console_return('Ozz development server started', 'white', 'green', 1, true);
    exec( sprintf('start %s', 'http://'.$url) ); // Open on default browser
    exec('php -S '.$url.' -t '.$conf['app']['PUBLIC_DIR']);
    die;
  }
}
(new Serve)->index($com);