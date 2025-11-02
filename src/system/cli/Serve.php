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
    $url = explode(' ', $conf['APP_URLS'])[0];

    // Print header info
    $utils->console_return('', '', '', 1);
    $utils->console_return('  Ozz Dev server started 🚀', 'green', '', 1);
    $utils->console_return('-----------------------------', 'green', '', 1);
    $utils->console_return('- PHP Server: http://' . $url, 'brown', '', 1);
    $utils->console_return('- Press Ctrl + C to stop the server', 'brown', false, 2);

    // Detect OS
    $uname = strtolower(php_uname());
    $os = (strpos($uname, "darwin") !== false)
      ? 'osx'
      : ((strpos($uname, "win") !== false) ? 'win32' : 'linux');

    // Open browser automatically
    $end = $os === 'win32' ? '' : '&';
    $cmd1 = sprintf(
      "%s http://%s %s",
      $os === 'win32' ? 'start ""' : ($os === 'osx' ? 'open' : 'xdg-open'),
      $url,
      $end
    );
    @pclose(@popen($cmd1, "r"));

    // Build PHP dev server command
    $php_cmd = 'php -S ' . $url . ' -t ' . $conf['PUBLIC_DIR'];

    // Run server depending on OS
    if ($os === 'win32') {
      passthru($php_cmd);
    } else {
      $hasScript = trim(shell_exec('command -v script'));

      if (!empty($hasScript)) {
        passthru('script -q -c "' . $php_cmd . '" /dev/null');
      } else {
        passthru($php_cmd);
      }
    }

    die;
  }
}

(new Serve)->index($com);
