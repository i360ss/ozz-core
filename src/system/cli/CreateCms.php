<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\cli;

class CreateCms {

  public function index() {
    $this->create_cms();
  }


  // Generate files
  private function create_cms() {
    global $utils;
    $app_dir = APP_DIR;
    $cms_dir = CMS_DIR;
    $mig_dir = MIGRATION_DIR;
    $assets_dir = ASSETS_DIR.'admin/';
    $cms_hold_dir = __DIR__.'/../content-holder/cms/';

    // Create CMS directory
    if (!is_dir($cms_dir)) {
      if (!mkdir($cms_dir, 0777, true)) {
        $utils->console_return("Error on creating directory [ $cms_dir ]", 'red');
        die();
      } else {
        $utils->console_return("CMS directory created [ $cms_dir ]", 'green');
      }
    }

    // Controllers
    $this->copy_directory($cms_hold_dir.'c/', $cms_dir.'controller/');

    // View files
    $this->copy_directory($cms_hold_dir.'v/', $cms_dir.'view/');

    // Assets files
    $this->copy_directory($cms_hold_dir.'as/', $assets_dir);

    // Middleware files
    $this->copy_directory($cms_hold_dir.'md/', $app_dir.'middleware/');

    // Migrations
    $mig_from = $cms_hold_dir.'mg/';
    $mig_files = array_filter(glob("$mig_from*"), "is_file");
    foreach ($mig_files as $f) {
      if(copy($f, $mig_dir . 'mg_'.date('YmdHis').'_'.ucfirst(basename($f)))){
        $utils->console_return("Migration file created [ ".MIGRATION_DIR.basename($f)." ]", 'green');
      } else {
        $utils->console_return("Error on creating migration file [ ".MIGRATION_DIR.basename($f)." ]", 'red');
      }
    }

    // CMS Config
    if(!file_exists($cms_dir.'cms-config.php')){
      if(copy($cms_hold_dir.'cms-config.php', $cms_dir.'cms-config.php')){
        $utils->console_return("CMS Config file created [ ".APP_DIR."cms-config.php ]", 'green');
      } else {
        $utils->console_return("Error on creating CMS Config file [ ".APP_DIR."cms-config.php ]", 'red');
      }
    }

    // CMS Routes
    if(!file_exists($cms_dir.'cms-route.php')){
      if(copy($cms_hold_dir.'cms-route.php', $cms_dir.'cms-route.php')){
        $utils->console_return("CMS Route file created [ ".CMS_DIR."cms-route.php ]", 'green');
      } else {
        $utils->console_return("Error on creating CMS Routes file [ ".CMS_DIR."cms-route.php ]", 'red');
      }
    }

    // CMS related files that should be inside the app directory
    $this->copy_directory($cms_hold_dir.'copy-to-app-dir/v/', $app_dir.'view/');

    // Run migration
    $this->runCMSMigration(CONFIG['CMS_TABLES']['forms']);
    $this->runCMSMigration(CONFIG['CMS_TABLES']['post_terms']);
    $this->runCMSMigration(CONFIG['CMS_TABLES']['posts']);
    $this->runCMSMigration(CONFIG['CMS_TABLES']['taxonomy']);
    $this->runCMSMigration(CONFIG['CMS_TABLES']['terms']);
  }


  /**
   * Run default CMS migrations
   * @param string $name Migration Name
   */
  private function runCMSMigration($name){
    global $utils;

    $output = [];
    $returnVar = 0;
    exec("php ozz migrate $name", $output, $returnVar);
    foreach ($output as $msg) {
      $utils->console_return($msg);
    }
  }


  /**
   * Custom content file copy
   * @param string $src Source directory
   * @param string $dst Destination directory
   */
  private function copy_directory($src, $dst) {
    global $utils;
    $dir = opendir($src);
    @mkdir($dst);

    while( $file = readdir($dir) ) {
      if(($file != '.' ) && ( $file != '..' )) { 
        if(is_dir($src . '/' . $file)) {
          $this->copy_directory($src . '/' . $file, $dst . '/' . $file);
        } else {
          if(copy($src . '/' . $file, $dst . '/' . $file)){
            $utils->console_return("File created [ $file ]", 'green');
          } else {
            $utils->console_return("Error on creating file [ $file ]", 'red');
          }
        }
      }
    }
    closedir($dir);
  }

}
(new CreateCms)->index($com);