<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system;

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
      // Numberrs
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
      padding-top: 5px;
      padding-bottom: 7px;
      padding-left: 9px;
      padding-right: 9px;
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
    $c = preg_replace("/\n<\/span>/", "</span>\n", $c);
    
    if ($label == ''){
      $line1 = '';
    } else {
      $line1 = "<strong>$label</strong> \n";
    }
    
    $lineInfo = $noLine ? '' : "$line1 $callingFile : $callingFileLine \n";

    $out = "\n<!-- Dumpr Begin -->\n".
    "<style type=\"text/css\" nonce=".CSP_NONCE.">".$style."</style>\n".
    "<div class=\"dumpr\">
    <div><pre>$lineInfo $c\n</pre></div></div><div style=\"clear:both;\">&nbsp;</div>".
    "\n<!-- Dumpr End -->\n";
    if($return) {
      return $out;
    } else {
      echo $out;
    }
  }



  /**
   * Highlight SQL syntax
   * @param string $string the sql string
   * @return html wrapped html
   */
  public function sqlHighlight($string) {
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
      'FOR',
      'READTEXT',
      'AS',
      'FOREIGN',
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
      'IN',
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
      'IS',
      'SET',
      'CONTAINS',
      'JOIN',
      'SETUSER',
      'CONTAINSTABLE',
      'KEY',
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
      'TO',
      'CURRENT_USER',
      'NONCLUSTERED',
      'TOP',
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
      'OF',
      'TRUNCATE',
      'DECLARE',
      'OFF',
      'TRY_CONVERT',
      'DEFAULT',
      'OFFSETS',
      'TSEQUAL',
      'DELETE',
      'ON',
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
      'USE',
      'DOUBLE',
      'OPTION',
      'USER',
      'DROP',
      'OR',
      'VALUES',
      'DUMP',
      'ORDER',
      'VARYING',
      'ELSE',
      'OUTER',
      'VIEW',
      'END',
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
    ];

    $pattern = '/\b(' . implode ('|', $sqlKeyords) . ')/i';
    preg_match_all($pattern, $string, $matches);
    foreach ($matches[1] as $val) {
      $string = str_replace($val, "<span class='high-txt'>$val</span>", $string);
    }

    return $string;
  }



  /**
   * Get Set and Render Debug Bar
   */
  public function renderDebugBar($data) {
    dump($data); echo '<br><br><br><br><br><br><br>';
    ?>
    <!-- // Ozz Debug Bar Styles -->
    <style nonce="<?=CSP_NONCE?>">
      :root {
        --ozz-white: #ffffff;
        --ozz-green: #88bf3d;
        --ozz-light1: #f1f2f6;
        --ozz-light2: #dfe4ea;
        --ozz-light3: #ced6e0;
        --ozz-light4: #a4b0be;
        --ozz-dark1: #2f3542;
        --ozz-dark2: #747d8c;

        --ozz-warn: #ffa502;
        --ozz-error: #ff4757;
        --ozz-info: #2e86de;
      }

      .ozz-fw-debug-bar {
        background: var(--ozz-white);
        bottom: 0 !important;
        box-sizing: border-box;
        height: 25px;
        left: 0;
        margin: 0;
        padding: 0;
        position: fixed !important;
        right: 0;
        width: 100% !important;
        z-index: 99999 !important;
      }

      .ozz-fw-debug-bar.open {
        height: 350px;
      }
      
      /** Tab Nav */
      .ozz-fw-debug-bar__nav {
        height: 23px;
        background: var(--ozz-white);
        border-top: 2px solid var(--ozz-green);
        border-bottom: 1px solid var(--ozz-light1);
        padding: 0;
      }

      .ozz-fw-debug-bar__nav.item {
        background: var(--ozz-white);
        border-radius: 0 !important;
        border: none;
        color: var(--ozz-dark1);
        cursor: pointer;
        font-family: sans-serif;
        font-size: 12px;
        font-weight: 500;
        margin: 0;
        padding: 6px 10px;
        outline: none;
      }

      .ozz-fw-debug-bar__nav.item:hover {
        background: var(--ozz-light1);
      }

      .ozz-fw-debug-bar__nav.item.active {
        color: var(--ozz-light1);
        background: var(--ozz-green);
      }

      .ozz-fw-debug-bar__nav.item .count {
        color: var(--ozz-green);
        font-weight: 600;
      }

      .ozz-fw-debug-bar__nav.item.active .count {
        color: var(--ozz-white);
      }

      .ozz-fw-debug-bar__nav.bar-items {
        background: green;
        float: right;
      }

      /** Tab Body */
      .ozz-fw-debug-bar__body {
        overflow-x: auto;
        max-height: 320px;
      }

      .ozz-fw-debug-bar__body.tab-body {
        display: none;
        margin: 0;
        padding: 0;
      }

      .ozz-fw-debug-bar__body.tab-body.active {
        display: block;
      }

      .ozz-fw-debug-bar-tab__empty {
        color: var(--ozz-light4);
        padding: 0 10px;
      }

      .ozz-fw-debug-bar-tab__message {
        border-bottom: 1px solid var(--ozz-light2);
        display: grid;
        font-size: 13px;
        grid-template-columns: 1fr auto;
        margin: 0;
        padding: 5px 5px 5px 24px;
        position:relative;
      }

      .ozz-fw-debug-bar-tab__message span {
        margin: 0;
        padding: 0;
      }

      .ozz-fw-debug-bar-tab__message.array {
        padding-left: 0;
      }

      .ozz-fw-debug-bar-tab__message.array span {
        line-height: 0;
      }

      .ozz-fw-debug-bar-tab__message.array span:nth-child(2) {
        line-height: 13px;
      }

      /** Used on Queries tab */
      .ozz-fw-debug-bar-tab__message-queries {
        border-bottom: 1px solid var(--ozz-light2);
        font-size: 13px;
        margin: 0;
        padding: 10px 5px 10px 24px;
        position:relative;
        color: var(--ozz-dark1);
      }

      .ozz-fw-debug-bar-tab__message-queries span.high-txt {
        color: var(--ozz-info);
      }

      /** Used on Request Tab */
      .ozz-fw-debug-bar-tab__message-request {
        border-bottom: 1px solid var(--ozz-light2);
        font-size: 13px;
        margin: 0;
        padding: 5px 5px 5px 24px;
        position:relative;
        display: grid;
        grid-template-columns: 130px 1fr;
        color: var(--ozz-dark1);
      }

      .ozz-fw-debug-bar-tab__message-request span {
        text-align: left !important;
        float: left !important;
      }

      .ozz-fw-debug-bar-tab__message-request span {
        color: var(--ozz-dark);
        font-size: 14px;
      }
      .ozz-fw-debug-bar-tab__message-request span.label {
        font-weight: 600;
      }

      .ozz-fw-debug-bar-tab__message:hover, 
      .ozz-fw-debug-bar-tab__message-queries:hover,
      .ozz-fw-debug-bar-tab__message-request:hover {
        background: var(--ozz-light1);
      }

      .ozz-fw-debug-bar-tab__message .dumpr {
        margin: 0 !important;
        padding: 0 !important;
        line-height: 0 !important;
      }
      .ozz-fw-debug-bar-tab__message .dumpr,
      .ozz-fw-debug-bar-tab__message .dumpr * {
        background: transparent !important;
      }
      .ozz-fw-debug-bar-tab__message .dumpr div {
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
      }

      .ozz-fw-debug-bar-tab__message.w { color: var(--ozz-warn); }
      .ozz-fw-debug-bar-tab__message.e { color: var(--ozz-error); }
      .ozz-fw-debug-bar-tab__message.i { color: var(--ozz-info); }

      .ozz-fw-debug-bar-tab__message.w::before,
      .ozz-fw-debug-bar-tab__message.e::before,
      .ozz-fw-debug-bar-tab__message.i::before {
        content: ''; 
        position: absolute;
        width: 6px;
        height: 6px;
        border-radius: 6px;
        top: 12px;
        left: 12px;
      }

      .ozz-fw-debug-bar-tab__message.w::before { background: var(--ozz-warn); }
      .ozz-fw-debug-bar-tab__message.e::before { background: var(--ozz-error); }
      .ozz-fw-debug-bar-tab__message.i::before { background: var(--ozz-info); }
    </style>

    <!-- // Ozz Debug Bar -->
    <div class="ozz-fw-debug-bar">
      <div class="ozz-fw-debug-bar__nav">
        <button class="ozz-fw-debug-bar__nav item" data-item="console">Console <span class="count"><?= count($data['ozz_message']) ?></span></button>
        <button class="ozz-fw-debug-bar__nav item" data-item="request">Request</button>
        <button class="ozz-fw-debug-bar__nav item" data-item="queries">Queries <span class="count"><?= count($data['ozz_sql_queries']) ?></span></button>
        <button class="ozz-fw-debug-bar__nav item" data-item="view">View</button>
        <button class="ozz-fw-debug-bar__nav item" data-item="controller">Controller</button>
        <button class="ozz-fw-debug-bar__nav item" data-item="session">Session <span class="count"></span></button>

        <div class="ozz-fw-debug-bar__nav bar-items">

        </div>
      </div>

      <div class="ozz-fw-debug-bar__body">
        <div class="ozz-fw-debug-bar__body tab-body console">
          <?php if (count($data['ozz_message']) < 1) : ?>
            <pre class="ozz-fw-debug-bar-tab__empty">No Console logs</pre>
          <?php else: ?>
            <?php foreach ($data['ozz_message'] as $key => $value) : ?>
              <?php $class = isset($value['args'][1]) ? $value['args'][1] : ''; ?>
              <?php 
              if (is_array($value['args'][0])) { ?>
                <pre class="ozz-fw-debug-bar-tab__message array <?=$class?>">
                  <span><?php $this->varDump($value['args'][0], '', false, true); ?></span>
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
            <pre class="ozz-fw-debug-bar-tab__empty">Error</pre>
          <?php else: ?>
            <?php foreach ($data['ozz_request'] as $key => $value) : ?>
              <div class="ozz-fw-debug-bar-tab__message-request">
                <span class="label"><?=ucfirst($key)?></span>
                <span>
                <?php
                  if (is_array($value)) {
                    foreach ($value as $k => $v) {
                      echo "<b>$k</b> : $v <br>";
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


        <div class="ozz-fw-debug-bar__body tab-body Queries">
          <?php if (count($data['ozz_sql_queries']) < 1) : ?>
            <pre class="ozz-fw-debug-bar-tab__empty">No Queries</pre>
          <?php else: ?>
            <?php foreach ($data['ozz_sql_queries'] as $key => $value) : ?>
              <pre class="ozz-fw-debug-bar-tab__message-queries"><?=$this->sqlHighlight($value)?></pre>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>


        <div class="ozz-fw-debug-bar__body tab-body view">
        view
        </div>


        <div class="ozz-fw-debug-bar__body tab-body controller">
        controller
        </div>


        <div class="ozz-fw-debug-bar__body tab-body session">
          session
        </div>
      </div>
    </div>

    <!-- // Ozz Debug Bar Script -->
    <script type="text/javascript" nonce="<?=CSP_NONCE?>">
      var ozzdebugbar__container = document.querySelector('.ozz-fw-debug-bar');
      var ozzdebugbar__nav_item = document.querySelectorAll('.ozz-fw-debug-bar__nav.item');
      var ozzdebugbar__tab_bodies = document.querySelectorAll('.ozz-fw-debug-bar__body.tab-body');
      var ozzdebugbar__tempTabName = '';

      ozzdebugbar__nav_item.forEach(el => {
        el.addEventListener('click', function() {
          var tabName = this.getAttribute('data-item');
          ozzdebugbar__container.classList.toggle('open');

          // Active current menu item
          ozzdebugbar__nav_item.forEach(item => {
            item.classList.remove('active');
          });
          this.classList.add('active');

          ozzdebugbar__tab_bodies.forEach(tab => {
            tab.classList.remove('active');
          });

          // Activate current tab
          document.querySelector('.ozz-fw-debug-bar__body.tab-body.'+tabName).classList.add('active');

          if (tabName !== ozzdebugbar__tempTabName) {
            ozzdebugbar__container.classList.add('open');
          }

          ozzdebugbar__tempTabName = tabName;
        });
      });
    </script>
    <?php
  }
}