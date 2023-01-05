<?php
use Ozz\Core\system\cli\CliUtils;

if(APP_ENV !== 'local'){
  exit('Unauthorized');
}

$GLOBALS['utils'] = new CliUtils;
global $utils;


// Print error on console
function ozz_console_error($message) {
  global $utils;
  $utils->console_return($message, 'white', 'red', true, true);
}

// Print warning on console
function ozz_console_warn($message) {
  global $utils;
  $utils->console_return($message, 'white', 'yellow', true, true);
}

// Print success on console
function ozz_console_success($message) {
  global $utils;
  $utils->console_return($message, 'white', 'green', true, true);
}



/**
 * Validate class/file Name to create
 */
function valid_file_name($name) {
  if(strpos($name, './') || !preg_match('/^[a-z0-9.\\/\-_]+$/i', $name) || is_numeric($name[0])){
    return false;
  }
  else{
    $classParts = explode('/', $name);
    $className = end($classParts);

    if(!preg_match('/^[a-z_]+$/i', $className[0])) {
      return false;
    }
    else{
      return true;
    }
  }
}



/**
 * All CLI scripts here
 */
$com = [];
foreach ($arg as $k => $v) { $com['r'.$k] = $v; }
extract($com);

$class = false;

if(count($com) == 1){
  // No Commands given
  if($r0 == 'ozz'){
    $class = "cli/CliHelp";
  }
}
elseif(count($com) == 2){
  # --------------------------------------
  // Single Argument 
  # --------------------------------------
  switch ($r1) {
    case '--help':
    case '-h':
      $class = "cli/CliHelp";
      break;

    case 'serve':
    case 'start':
      $class = "cli/Serve";
      break;

    case 'c:auth':
    case 'make:auth':
    case 'create:auth':
      $class = "auth/CreateAuth";
      break;

    case 'migrate':
    case 'migrate:run':
    case 'migrate:clear':
    case 'migrate:reset':
      $class = "migration/Migrate";
      break;

    default:
      ozz_console_error('Command Not Found! Please check below for help');
      $class = "cli/CliHelp";
      break;
  }
}
elseif(count($com) == 3){
  # --------------------------------------
  // 2 Arguments
  # --------------------------------------
  switch ($r1) {
    
    // Create files
    case 'c:mvc':
    case 'create:mvc':
    case 'make:mvc':
    
    case 'c:middleware':
    case 'create:middleware':
    case 'make:middleware':
    case 'c:md':

    case 'create:controller':
    case 'create:view':
    case 'create:model':

    case 'make:controller':
    case 'make:view':
    case 'make:model':

    case 'c:controller':
    case 'c:view':
    case 'c:model':

    case 'c:et':
    case 'c:email-view':
    case 'c:email-temp':
    case 'c:email-template':
    case 'make:email-template':
    case 'make:email-temp':
    case 'create:email-template':
    case 'create:email-temp':

    case 'c:layout':
    case 'c:lay':
    case 'c:base-layout':

    case 'c:component':
    case 'c:comp':
    case 'c:compo':
      
    case 'c:c':
    case 'c:v':
    case 'c:m':
    
    case 'c:mv':
    case 'c:vm':

    case 'c:mc':
    case 'c:cm':

    case 'c:vc':
    case 'c:cv':
      $class = "cli/CliCreate";
      break;

    // Create Migration
    case 'c:migration':
    case 'c:mig':
    case 'make:mig':
    case 'create:migration':
    case 'create:mig':
    case 'make:migration':
    
    case 'u:migration': // Update Table
    case 'u:mig':
    case 'update:migration':
    case 'update:mig':
      $class = "migration/CreateMigration";
      break;

    // Delete or Create one table
    case 'migrate':
    case 'migrate:up':
    case 'migrate:drop':
      $class = "migration/Migrate";
      break;

    default:
      ozz_console_error('Command Not Found! Please check below for help');
      $class = "cli/CliHelp";
      break;
  }
}

// Load action class
if($class){
  require __DIR__ . '/'.$class.'.php';
  exit;
}
else{
  ozz_console_error('Command Not Found!');
  $utils->console_return('Run [ php ozz -h ] to see commands', 'light_cyan');
  exit;
}