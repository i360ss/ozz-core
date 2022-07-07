<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 */

namespace Ozz\Core\system\cli;

use Ozz\Core\system\cli\CliUtils;

class CliHelp {

  public function index(){
    
    $utils = new CliUtils;
    extract($utils->styles);

    print("$green
    
         |||||         |||||||||||       |||||||||||
       |||   |||       |/     |||        |/     |||
      |||     |||           |||               |||
      |||     |||         |||               |||
       |||   |||        |||     /|        |||     /|
         |||||         |||||||||||       |||||||||||
     
    $gray---------------------------------------------------
    $cyan A   M I C R O   F R A M E W O R K   F O R   P H P
    $gray---------------------------------------------------

    ".$br);
    
print("
All available commands 
----------------------
".$br);
    
    print($yellow."Usage".$br);
    print($cyan." php ozz [option] [argument]".$br);
    
    print($br);

    print($br.$yellow."General".$br);
    print($green." -h | --help                 ".$wt."Display this help message ".$br.$br);

    // Local server
    print($yellow."Server ".$br);
    print($green." serve                       ".$wt."Start ozz development server ".$br.$br);

    // migration commands here
    print($yellow."Migration Options ".$br);
    print($green." migrate                     ".$wt."Run Migration (will execute all available migrations)".$br);
    print($green." migrate:drop                ".$wt."Delete one specific table (provide table name) ".$br);
    print($green." migrate:clear               ".$wt."Drop All Tables ".$br);
    print($green." migrate:reset               ".$wt."Reset All Migrations ".$br.$br);

    // create commands here
    print($yellow."Create Options ".$br);
    print($green." c:mvc                       ".$wt."Create a new model view & controller ".$br);
    print($green." c:controller                ".$wt."Create a new controller ".$br);
    print($green." c:model                     ".$wt."Create a new model ".$br);
    print($green." c:view                      ".$wt."Create a new view ".$br);
    print($green." c:layout                    ".$wt."Create a new base layout ".$br);
    print($green." c:component                 ".$wt."Create a new view component ".$br);
    print($green." c:middleware                ".$wt."Create a new middleware ".$br);
    print($green." c:email_template            ".$wt."Create a new email template ".$br);
    print($green." c:migration                 ".$wt."Create a new migration file ".$br);
    print($green." u:migration                 ".$wt."Create a new update migration file (For ALTER a table) ".$br.$br);

    // Cache commands
    print($yellow."Cache Options ".$br);
    print($green." cache:cache                 ".$wt."Store the application cache ".$br);
    print($green." cache:clear                 ".$wt."Clear the application cache ".$br.$br);

    // Route commands
    print($yellow."Route Options ".$br);
    print($green." route:cache                 ".$wt."Create route cache file ".$br);
    print($green." route:clear                 ".$wt."Remove route cache file ".$br.$br);

    // View commands
    print($yellow."View Options ".$br);
    print($green." view:cache                  ".$wt."Create page cache (compile all web output) ".$br);
    print($green." view:clear                  ".$wt."Clear all compiled page cache ".$br.$br);

    // List commands
    print($yellow."List Info ".$br);
    print($green." list:schema                 ".$wt."Show the given table's schema ".$br);
    print($green." list:route                  ".$wt."List down all registered route information ".$br.$br);

  }
}
(new CliHelp)->index($com);