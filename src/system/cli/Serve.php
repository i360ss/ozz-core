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
    
    $conf = env('app');
    $url = $conf['APP_URL'].':'.$conf['LOCAL_PORT'];

    $utils->console_return('Ozz development server started', 'white', 'green', 1, true);
    $utils->console_return('Press Ctrl+C to stop the server', 'brown', false, 2);

    $uname = strtolower(php_uname());
    $os = (strpos($uname, "darwin") !== false) ? 'osx' : ((strpos($uname, "win") !== false) ? 'win32' : 'linux');
    $end = $os == 'win32' ? '' : '&';
    $cmd1 = sprintf("%s http://$url $end", $os == 'win32' ? 'start ""' : ($os == 'osx' ? 'open' : 'xdg-open'));
    pclose(popen($cmd1, "r"));

    exec('php -S '.$url.' -t '.$conf['PUBLIC_DIR']); // Start Dev Server
    die;
  }
}
(new Serve)->index($com);