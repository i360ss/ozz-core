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
    $app_dir = __DIR__.SPC_BACK['core_2'].'app/';
    $cms_dir = __DIR__.SPC_BACK['core_2'].'cms/';
    $mig_dir = __DIR__.SPC_BACK['core_2'].'database/migration/';
    $assets_dir = __DIR__.SPC_BACK['core_2'].env('app', 'PUBLIC_DIR').'/src/admin/';
    $cms_hold_dir = __DIR__.'/../content-holder/cms/';

    // Create CMS directory
    if (!is_dir($cms_dir)) {
      if (!mkdir($cms_dir, 0777, true)) {
        $utils->console_return("Error on creating directory [ $cms_hold_dir ]", 'red');
        die();
      } else {
        $utils->console_return("CMS directory created [ ".$cms_hold_dir." ]", 'green');
      }
    }

    // Controllers
    $this->copy_directory($cms_hold_dir.'c/', $cms_dir.'controller/');

    // View files
    $this->copy_directory($cms_hold_dir.'v/', $cms_dir.'view/');

    // Assets files
    $this->copy_directory($cms_hold_dir.'as/', $assets_dir);

    // Migrations
    $mig_from = $cms_hold_dir.'mg/';
    $mig_files = array_filter(glob("$mig_from*"), "is_file");
    foreach ($mig_files as $f) {
      if(copy($f, $mig_dir . 'mg_'.date('d_m_Y_').basename($f))){
        $utils->console_return("Migration file created [ database/migration/".basename($f)." ]", 'green');
      } else {
        $utils->console_return("Error on creating migration file [ database/migration/".basename($f)." ]", 'red');
      }
    }

    // CMS Config
    if(!file_exists($cms_dir.'cms-config.php')){
      if(copy($cms_hold_dir.'cms-config.php', $cms_dir.'cms-config.php')){
        $utils->console_return("CMS Config file created [ app/cms-config.php ]", 'green');
      } else {
        $utils->console_return("Error on creating CMS Config file [ app/cms-config.php ]", 'red');
      }
    }

    // CMS Routes
    if(!file_exists($cms_dir.'cms-route.php')){
      if(copy($cms_hold_dir.'cms-route.php', $cms_dir.'cms-route.php')){
        $utils->console_return("CMS Route file created [ cms/cms-route.php ]", 'green');
      } else {
        $utils->console_return("Error on creating CMS Routes file [ cms/cms-route.php ]", 'red');
      }
    }

    // CMS Forms (config)
    if(!file_exists($cms_dir.'cms-forms.php')){
      if(copy($cms_hold_dir.'cms-forms.php', $cms_dir.'cms-forms.php')){
        $utils->console_return("CMS Forms file created [ cms/cms-forms.php ]", 'green');
      } else {
        $utils->console_return("Error on creating CMS Forms file [ cms/cms-forms.php ]", 'red');
      }
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