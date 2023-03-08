<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system;

use Ozz\Core\Help;

class SubHelp {

  /**
   * Ozz custom dumper
   */
  public static function varDump($data, $label='', $return = false, $noLine=false){
    $debug = debug_backtrace();
    $callingFile = $debug[0]['file'];
    $callingFileLine = $debug[0]['line'];
    
    ob_start();
    var_dump($data);
    $c = ob_get_contents();
    ob_end_clean();
    
    $c = preg_replace("/\r\n|\r/", "\n", $c);
    $c = str_replace("]=>\n", '] = ', $c);
    $c = preg_replace('/= {2,}/', '= ', $c);
    $c = preg_replace("/\[\"(.*?)\"\] = /i", "[$1] = ", $c);
    $c = preg_replace('/  /', "    ", $c);
    $c = preg_replace("/\"\"(.*?)\"/i", "\"$1\"", $c);
    $c = preg_replace("/(int|float)\(([0-9\.]+)\)/i", "$1() <span class=\"number\">$2</span>", $c);
    
    // Syntax Highlighting of Strings. This seems cryptic, but it will also allow non-terminated strings to get parsed.
    $c = preg_replace("/(\[[\w ]+\] = string\([0-9]+\) )\"(.*?)/sim", "$1<span class=\"string\">\"", $c);
    $c = preg_replace("/(\"\n{1,})( {0,}\})/sim", "$1</span>$2", $c);
    $c = preg_replace("/(\"\n{1,})( {0,}\[)/sim", "$1</span>$2", $c);
    $c = preg_replace("/(string\([0-9]+\) )\"(.*?)\"\n/sim", "$1<span class=\"string\">\"$2\"</span>\n", $c);
    
    $regex = array(
      // Numbers
      'numbers' => array('/(^|] = )(array|float|int|string|resource|object\(.*\)|\&amp;object\(.*\))\(([0-9\.]+)\)/i', '$1$2(<span class="number">$3</span>)'),
      // Keywords
      'null' => array('/(^|] = )(null)/i', '$1<span class="keyword">$2</span>'),
      'bool' => array('/(bool)\((true|false)\)/i', '$1(<span class="keyword">$2</span>)'),
      // Types
      'types' => array('/(of type )\((.*)\)/i', '$1(<span class="type">$2</span>)'),
      // Objects
      'object' => array('/(object|\&amp;object)\(([\w]+)\)/i', '$1(<span class="object">$2</span>)'),
      // Function
      'function' => array('/(^|] = )(array|string|int|float|bool|resource|object|\&amp;object)\(/i', '$1<span class="function">$2</span>('),
    );
    
    foreach ($regex as $x) {
      $c = preg_replace($x[0], $x[1], $c);
    }
    
    $style = '
    /* outside div - it will float and match the screen */
    .dumpr {
      margin: 0;
      padding: 5px;
      background-color: #fbfbfb;
    }
    /* font size and family */
    .dumpr pre {
      color: #000000;
      font-size: 9pt;
      font-family: monospace;
      line-height: 1.1rem;
      margin: 0px;
      padding: 0;
    }
    /* inside div */
    .dumpr div {
      background-color: #fcfcfc;
      border: 1px solid #d9d9d9;
      width: calc(100% - 20px);
      padding: 10px;
    }
    /* syntax highlighting */
    .dumpr span.string {color: #c40000;}
    .dumpr span.number {color: #ff0000;}
    .dumpr span.keyword {color: #007200;}
    .dumpr span.function {color: #0000c4;}
    .dumpr span.object {color: #ac00ac;}
    .dumpr span.type {color: #0072c4;}
    ';
    
    $style = preg_replace("/ {2,}/", "", $style);
    $style = preg_replace("/\t|\r\n|\r|\n/", "", $style);
    $style = preg_replace("/\/\*.*?\*\//i", '', $style);
    $style = str_replace('}', '} ', $style);
    $style = str_replace(' {', '{', $style);
    $style = trim($style);
      
    $c = trim($c);
    $c = preg_replace("/\n<\/span>/", "</span>", $c);
    
    if ($label == ''){
      $line1 = '';
    } else {
      $line1 = "<strong>$label</strong> \n";
    }
    
    $lineInfo = $noLine ? '' : "$line1 $callingFile : $callingFileLine \n";

    $out = "\n<!-- Dumpr Begin -->\n".
    "<style type=\"text/css\" nonce=".CSP_NONCE.">".$style."</style>".
    "<div class=\"dumpr\">
      <div>$lineInfo $c</div>
    </div>".
    "\n<!-- Dumpr End -->\n";
    if($return) {
      return $out;
    } else {
      echo $out;
    }
  }



  /**
   * JSON Pretty Dumper
   * @param string JSON string to be dumped
   * @param int $id ID of the dump DOM
   * @return html DOM
   */
  public static function jsonDumper($id, $jsonString, $bg_padd=true) {
    include 'core_vendor/pretty-json/pretty-json.php';
    $styl = $bg_padd ? 'style="background: #fff;padding: 20px;"' : '';

    return '<style nonce="'.CSP_NONCE.'">'.$pretty_json_style.'</style><script type="text/javascript" nonce="'.CSP_NONCE.'">'.$pretty_json_script.'</script><div '.$styl.'>
      <button id="ozz_debugbar__collapseBtn_'.$id.'" class="ozz_debugbar__collapse_btn active">Collapse</button>
      <div id="ozz_debugbar__jsonDump_'.$id.'"></div>
      <script type="text/javascript" nonce="'.CSP_NONCE.'">
        var ozz_debug_jsondump'.$id.' = new JSONViewer();
        document.querySelector("#ozz_debugbar__jsonDump_'.$id.'").appendChild(ozz_debug_jsondump'.$id.'.getContainer());
        ozz_debug_jsondump'.$id.'.showJSON('.$jsonString.');

        var collapseBtn = document.getElementById("ozz_debugbar__collapseBtn_'.$id.'");
        collapseBtn.addEventListener("click", function(e) {
          e.preventDefault();
          if (collapseBtn.classList.contains("active")) {
            ozz_debug_jsondump'.$id.'.showJSON('.$jsonString.');
            collapseBtn.classList.remove("active");
            this.innerHTML="Collapse";
          } else {
            ozz_debug_jsondump'.$id.'.showJSON('.$jsonString.', null, 0);
            collapseBtn.classList.add("active")
            this.innerHTML="Expand";
          }
        });
      </script>
    </div>';
  }



  /**
   * Highlight SQL syntax
   * @param string $string the sql string
   * @return html wrapped html
   */
  public static function sqlDumper($string, $inlineStyle=true) {
    $sqlKeyords = [
      'ADD',
      'EXTERNAL',
      'PROCEDURE',
      'ALL',
      'FETCH',
      'PUBLIC',
      'ALTER',
      'FILE',
      'RAISERROR',
      'AND',
      'FILLFACTOR',
      'READ',
      'ANY',
      'FOREIGN',
      'READTEXT',
      'RECONFIGURE',
      'ASC',
      'FREETEXT',
      'REFERENCES',
      'AUTHORIZATION',
      'FREETEXTTABLE',
      'REPLICATION',
      'BACKUP',
      'FROM',
      'RESTORE',
      'BEGIN',
      'FULL',
      'RESTRICT',
      'BETWEEN',
      'FUNCTION',
      'RETURN',
      'BREAK',
      'GOTO',
      'REVERT',
      'BROWSE',
      'GRANT',
      'REVOKE',
      'BULK',
      'GROUP',
      'RIGHT',
      'BY',
      'HAVING',
      'ROLLBACK',
      'CASCADE',
      'HOLDLOCK',
      'ROWCOUNT',
      'CASE',
      'IDENTITY',
      'ROWGUIDCOL',
      'CHECK',
      'IDENTITY_INSERT',
      'RULE',
      'CHECKPOINT',
      'IDENTITYCOL',
      'SAVE',
      'CLOSE',
      'IF',
      'SCHEMA',
      'CLUSTERED',
      'SECURITYAUDIT',
      'COALESCE',
      'INDEX',
      'SELECT',
      'COLLATE',
      'INNER',
      'SEMANTICKEYPHRASETABLE',
      'COLUMN',
      'INSERT',
      'SEMANTICSIMILARITYDETAILSTABLE',
      'COMMIT',
      'INTERSECT',
      'SEMANTICSIMILARITYTABLE',
      'COMPUTE',
      'INTO',
      'SESSION_USER',
      'CONSTRAINT',
      'CONTAINS',
      'JOIN',
      'SETUSER',
      'CONTAINSTABLE',
      'SHUTDOWN',
      'CONTINUE',
      'KILL',
      'SOME',
      'CONVERT',
      'LEFT',
      'STATISTICS',
      'CREATE',
      'LIKE',
      'SYSTEM_USER',
      'CROSS',
      'LINENO',
      'TABLE',
      'CURRENT',
      'LOAD',
      'TABLESAMPLE',
      'CURRENT_DATE',
      'MERGE',
      'TEXTSIZE',
      'CURRENT_TIME',
      'NATIONAL',
      'THEN',
      'CURRENT_TIMESTAMP',
      'NOCHECK',
      'CURRENT_USER',
      'NONCLUSTERED',
      'CURSOR',
      'NOT',
      'TRAN',
      'DATABASE',
      'NULL',
      'TRANSACTION',
      'DBCC',
      'NULLIF',
      'TRIGGER',
      'DEALLOCATE',
      'TRUNCATE',
      'DECLARE',
      'TRY_CONVERT',
      'DEFAULT',
      'OFFSETS',
      'TSEQUAL',
      'DELETE',
      'UNION',
      'DENY',
      'OPEN',
      'UNIQUE',
      'DESC',
      'OPENDATASOURCE',
      'UNPIVOT',
      'DISK',
      'OPENQUERY',
      'UPDATE',
      'DISTINCT',
      'OPENROWSET',
      'UPDATETEXT',
      'DISTRIBUTED',
      'OPENXML',
      'DOUBLE',
      'OPTION',
      'USER',
      'DROP',
      'VALUES',
      'DUMP',
      'ORDER',
      'VARYING',
      'ELSE',
      'OUTER',
      'VIEW',
      'OVER',
      'WAITFOR',
      'ERRLVL',
      'PERCENT',
      'WHEN',
      'ESCAPE',
      'PIVOT',
      'WHERE',
      'EXCEPT',
      'PLAN',
      'WHILE',
      'EXEC',
      'PRECISION',
      'WITH',
      'EXECUTE',
      'PRIMARY',
      'WITHIN GROUP',
      'EXISTS',
      'PRINT',
      'WRITETEXT',
      'EXIT',
      'PROC',
      'IN',
      'AS',
      'FOR',
      'IS',
      'END',
      'OR',
      'USE',
      'ON',
      'OFF',
      'OF',
      'TOP',
      'TO',
      'KEY',
      'SET',
    ];

    $pattern = '/\b(' . implode ('|', $sqlKeyords) . ')/i';
    preg_match_all($pattern, $string, $matches);
    foreach ($matches[0] as $val) {
      $string = str_ireplace("$val ", "<span class='high-txt'>$val</span> ", $string);
    }

    if ($inlineStyle) {
      $style = "<div class='ozz-debug-sqldumper'><style nonce='".CSP_NONCE."'>.ozz-debug-sqldumper {color:#18171B;} .ozz-debug-sqldumper span.high-txt { color: #2e86de; }</style>";
      return $style.$string.'</div>';
    } else {
      return $string;
    }
  }



  /**
   * PHP code highlight
   */
  public static function phpHighlight($code) {
    // Replace special characters with HTML entities
    $code = htmlspecialchars($code, ENT_NOQUOTES);
  
    // Define the patterns to match
    $patterns = array(
      // Keywords and function names
      '/\b(abstract|and|array|as|break|callable|case|catch|class|clone|const|continue|declare|default|die|do|echo|else|elseif|empty|enddeclare|endfor|endforeach|endif|endswitch|endwhile|eval|exit|extends|final|finally|for|foreach|function|global|goto|if|implements|include|include_once|instanceof|insteadof|interface|isset|list|namespace|new|or|print|private|protected|public|require|return|static|switch|throw|trait|try|unset|use|var|while|xor)\b/',
      // Variables and const
      '/(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/',
      // Numbers
      '/\b([0-9]+\.?[0-9]*|\.[0-9]+)\b/',
      // Strings
      '/(\'[^\']*\'|"[^"]*")/',
       // Single line comments
      '/\/\/(.*)/',
       // Multi-line comments
      '/\/\*(.*?)\*\//s',
       // Hash comments
      '/\#(.*)/',
    );


    // Define the replacements
    $replacements = array(
      '<ch-purple>$0</ch-purple>',
      '<ch-blue>$0</ch-blue>',
      '<ch-green>$0</ch-green>',
      '<ch-red>$0</ch-red>',
      '<ch-gray>$0</ch-gray>',
      '<ch-gray>$0</ch-gray>',
      '<ch-gray>$0</ch-gray>',
    );
  
    // Apply the highlighting
    $code = preg_replace($patterns, $replacements, $code);
  
    // Return the highlighted code
    return $code;
  }



  /**
   * Get Set and Render Debug Bar
   */
  public function renderDebugBar($data) { 

    // Get and set SQL temporary debug log
    $sql_temp_log_content = file_get_contents(__DIR__.SPC_BACK['core_1'].'storage/log/sql_debug.log');
    $sql_temp_log_arr = array_filter(explode('<####>', $sql_temp_log_content));
    $final_sql_log = [];

    foreach ($sql_temp_log_arr as $k => $statement) {
      if(!empty(explode('<###>', $statement))){
        $final_sql_log[$k] = explode('<###>', $statement);
      }
    }
    $data['ozz_sql_queries'] = $final_sql_log;
    ?>

    <div class="ozz__debugbar">
    <!-- Ozz Debug Bar Styles -->
    <style nonce="<?=CSP_NONCE?>"><?= Help::minifyCSS(file_get_contents(__DIR__.'/assets/css/debugbar.css')); ?></style>

    <!-- Ozz Debug Bar -->
    <div class="ozz-fw-debug-bar">
      <div class="ozz-fw-debug-bar__nav wrapper">
        <button class="ozz-fw-debug-bar__nav item" data-item="console">Console <span class="count"><?= count($data['ozz_message']) ?></span></button>
        <button class="ozz-fw-debug-bar__nav item" data-item="request">Request</button>
        <button class="ozz-fw-debug-bar__nav item" data-item="queries">Queries <span class="count"><?= count($data['ozz_sql_queries']) ?></span></button>
        <button class="ozz-fw-debug-bar__nav item" data-item="view">View</button>
        <button class="ozz-fw-debug-bar__nav item" data-item="controller">Controller</button>
        <button class="ozz-fw-debug-bar__nav item" data-item="session">Session <span class="count"></span></button>

        <div class="ozz-fw-debug-bar__nav bar-items"></div>
      </div>

      <div class="ozz-fw-debug-bar__body">
        <div class="ozz-fw-debug-bar__body tab-body console">
          <?php if (count($data['ozz_message']) < 1) : ?>
            <pre class="ozz-fw-debug-bar__empty">No Console logs</pre>
          <?php else: ?>
            <?php foreach ($data['ozz_message'] as $key => $value) : ?>
              <?php $class = isset($value['args'][1]) ? $value['args'][1] : ''; ?>
              <?php 
              if (is_array($value['args'][0]) || is_object($value['args'][0])) { ?>
                <pre class="ozz-fw-debug-bar-tab__message <?=$class?>">
                  <span class="ozz-fw-debug-bar-array"><?php self::varDump($value['args'][0], '', false, true); ?></span>
                  <span style="color: var(--ozz-dark2)"><?=$value['file'].' | ln: '.$value['line']?></span>
                </pre>
              <?php } elseif (is_json($value['args'][0])) { ?>
                <pre class="ozz-fw-debug-bar-tab__message <?=$class?>">
                  <div><?= self::jsonDumper($key, $value['args'][0], false)?></div>
                  <span style="color: var(--ozz-dark2)"><?=$value['file'].' | ln: '.$value['line']?></span>
                </pre>
              <?php
              } else { ?>
                <pre class="ozz-fw-debug-bar-tab__message <?=$class?>">
                  <span><?=$value['args'][0]?></span>
                  <span style="color: var(--ozz-dark2)"><?=$value['file'].' | ln: '.$value['line']?></span>
                </pre>
              <?php } ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>


        <div class="ozz-fw-debug-bar__body tab-body request">
          <?php if (count($data['ozz_request']) < 1) : ?>
            <pre class="ozz-fw-debug-bar__empty">Error</pre>
          <?php else: ?>
            <?php foreach ($data['ozz_request'] as $key => $value) : ?>
              <div class="ozz-fw-debug-bar-tab__message-request">
                <span class="label"><?=ucfirst($key)?></span>
                <span>
                <?php
                  if (is_array($value)) {
                    foreach ($value as $k => $v) {
                      if(is_string($v) || is_bool($v)){
                        echo "<b>$k</b> : $v <br>";
                      } else {
                        self::varDump($v, false, true);
                      }
                    }
                  } else {
                    echo $value;
                  }
                ?>
                </span>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>


        <div class="ozz-fw-debug-bar__body tab-body queries">
          <?php if (isset($data['ozz_sql_queries']) && count($data['ozz_sql_queries']) < 1) : ?>
            <pre class="ozz-fw-debug-bar__empty">No Queries</pre>
          <?php else: ?>
            <?php foreach ($data['ozz_sql_queries'] as $key => $value) : ?>
              <div class="ozz-fw-debug-bar-tab__message-queries">
                <span><?=self::sqlDumper($value[1], false)?></span>
                <span><?=number_format($value[0]*1000, 3)?> ms</span>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>


        <div class="ozz-fw-debug-bar__body tab-body view">
          <?php if (!isset($data['ozz_view']) || count($data['ozz_view']) < 1) : ?>
            <pre class="ozz-fw-debug-bar__empty">No View Files</pre>
          <?php elseif (isset($data['ozz_view'])) : ?>
            <?php $view = $data['ozz_view']; ?>

            <div class="ozz__dbg_view-comp-wrapper">
              <div class="ozz__dbg_view-info">
                <div class="ozz-fw-debug-bar-tab__message-view">
                  <span><strong>View File:</strong> <?=isset($view['view_file']) ? $view['view_file'] : '<em style="color: #a00">Not Found</em>'; ?></span><br><br>
                  <span><strong>Base Layout:</strong> <?=isset($view['base_file']) ? $view['base_file'] : '<em style="color: #a00">Not Found</em>';?></span><br><br>
                  <span class="label">View Data: </span><br>
                  <?php if (is_array($view['view_data']) || is_object($view['view_data'])) {?>
                    <span class="ozz-fw-debug-bar-array"><?php self::varDump($view['view_data'], '', false, true)?></span>
                  <?php } elseif (is_json($view['view_data'])) { ?>
                    <div><?= self::jsonDumper('view_data', $view['view_data'], false)?></div>
                  <?php } else { ?>
                    <span><?= is_string($view['view_data']) ? $view['view_data'] : false; ?></span>
                  <?php } ?>
                </div>
              </div>


              <div class="ozz__dbg_component-info">
                <div class="ozz-fw-debug-bar-tab__message-view">
                  <span class="label">Components</span><br><br>
                  <?php
                  if (isset($view['components']) || has_flash('ozz_components')) {

                    $view['components'] = !isset($view['components']) ? [] : $view['components'];

                    // Merge Components info from flash and default
                    $view_components = has_flash('ozz_components') ? array_merge(get_flash('ozz_components'), $view['components']) : $view['components'];

                    // remove component info flash
                    remove_flash('ozz_components');

                    foreach ($view_components as $key => $value) {
                      echo '<details class="ozz-fw-debug-bar-tab__message-view-component">';
                      echo '<summary><span class="green">'.$value['file'].'</span></summary>';

                      if (is_json($value['args'])) {
                        echo '<br><div><pre>'.json_encode($value['args'], JSON_PRETTY_PRINT).'</pre></div>';
                      } elseif (is_array($value['args']) || is_object($value['args'])) {
                        echo '<div>'.self::varDump($value['args'], '', false, true).'</div><br>';
                      } else {
                        echo '<br><div>'.$value['args'].'</div>';
                      }
                      echo '</details>';
                    }
                  }
                  ?>
                </div>
              </div>
            </div>

          <?php endif; ?>
        </div>


        <div class="ozz-fw-debug-bar__body tab-body controller">
          <?php
          if (isset($data['ozz_controller'])) { 
            if (count($data['ozz_controller']) < 1) {
              echo '<pre class="ozz-fw-debug-bar__empty">No Controller</pre>';
            } else {
              $ctl = $data['ozz_controller']; ?>
              <div class="ozz-fw-debug-bar-tab__message-controller">
                <span class="label">Controller:</span>
                <span><?=$ctl['controller']?></span>
              </div>

              <div class="ozz-fw-debug-bar-tab__message-controller">
                <span class="label">Method:</span>
                <span><?=$ctl['method']?></span>
              </div>
            <?php
            }
          } else {
            echo '<pre class="ozz-fw-debug-bar__empty">No Controller</pre>';
          } ?>
        </div>


        <div class="ozz-fw-debug-bar__body tab-body session">
          <?php if (isset($_SESSION) && !empty($_SESSION)) { ?>
            <?php foreach ($_SESSION as $key => $value) { ?>
              <div class="ozz-fw-debug-bar-tab__message-view">
              <span class="label"><?=$key?></span>
              <?php if (is_array($value) || is_object($value)) { ?>
                <span class="ozz-fw-debug-bar-array"><?=self::varDump($value, '', false, true)?></span>
              <?php } elseif (is_json($value)) { ?>
                <span><?= self::jsonDumper('jid_'.rand(), $value, false)?></span>
              <?php } else { ?>
                <span><?=$value?></span>
              <?php } ?>
              </div>
            <?php } ?>
          <?php } ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Ozz Debug Bar Script -->
    <script type="text/javascript" nonce="<?=CSP_NONCE?>">
    (function() {
      var ozzDebugBar__container = document.querySelector('.ozz-fw-debug-bar');
      var ozzDebugBar__nav_item = document.querySelectorAll('.ozz-fw-debug-bar__nav.item');
      var ozzDebugBar__tab_bodies = document.querySelectorAll('.ozz-fw-debug-bar__body.tab-body');
      var ozzDebugBar__tempTabName = '';

      ozzDebugBar__nav_item.forEach(el => {
        el.addEventListener('click', function() {
          var tabName = this.getAttribute('data-item');
          ozzDebugBar__container.classList.toggle('open');

          // Active current menu item
          ozzDebugBar__nav_item.forEach(item => {
            item.classList.remove('active');
          });
          this.classList.add('active');

          ozzDebugBar__tab_bodies.forEach(tab => {
            tab.classList.remove('active');
          });

          // Activate current tab
          document.querySelector('.ozz-fw-debug-bar__body.tab-body.'+tabName).classList.add('active');

          if (tabName !== ozzDebugBar__tempTabName) {
            ozzDebugBar__container.classList.add('open');
          }

          ozzDebugBar__tempTabName = tabName;
        });
      });
    })()
    </script>
    </div>
    <?php
  }

}