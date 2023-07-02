<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\cms;

class CreateCms {

  public function index() {
    $this->create_cms();
  }


  // Generate files
  private function create_cms() {
    global $utils;
    $app_dir = __DIR__.SPC_BACK['core_2'].'app/';
    $mig_dir = __DIR__.SPC_BACK['core_2'].'database/migration/';
    $assets_dir = __DIR__.SPC_BACK['core_2'].env('app')['PUBLIC_DIR'].'/assets/admin/';
    $cms_dir = __DIR__.'/../content-holder/cms/';

    // Controllers
    $this->copy_directory($cms_dir.'c/', $app_dir.'controller/admin/');

    // View files
    $this->copy_directory($cms_dir.'v/', $app_dir.'view/');

    // Migration files
    $this->copy_directory($cms_dir.'v/', $app_dir.'view/');

    // Assets files
    $this->copy_directory($cms_dir.'as/admin/', $assets_dir);

    // Migrations
    $mig_from = $cms_dir.'mg/';
    $mig_files = array_filter(glob("$mig_from*"), "is_file");
    foreach ($mig_files as $f) {
      if(copy($f, $mig_dir . 'mg_'.date('d_m_Y_').basename($f))){
        $utils->console_return("Migration file created [ database/migration/".basename($f)." ]", 'green');
      } else {
        $utils->console_return("Error on creating migration file [ database/migration/".basename($f)." ]", 'red');
      }
    }

    // CMS Config
    if(!file_exists($app_dir.'cms-config.php')){
      if(copy($cms_dir.'cms-config.php', $app_dir.'cms-config.php')){
        $utils->console_return("CMS Config file created [ app/cms-config.php ]", 'green');
      } else {
        $utils->console_return("Error on creating CMS Config file [ app/cms-config.php ]", 'red');
      }
    }

    // Copy CMS Routes
    require $cms_dir.'routes.php';
    if(file_put_contents($app_dir.'Route.php', $route_content, FILE_APPEND | LOCK_EX)){
      $utils->console_return("CMS routes added to Route file", 'green');
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