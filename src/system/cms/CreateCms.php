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

    // Create directories if not exist
    if(!file_exists($app_dir.'controller/admin/')){
      mkdir($app_dir.'controller/admin/', 0777, true);
    }
    if(!file_exists($app_dir.'view/admin/')){
      mkdir($app_dir.'view/admin/', 0777, true);
    }
    if(!file_exists($app_dir.'view/base/')){
      mkdir($app_dir.'view/base/', 0777, true);
    }
    if(!file_exists($assets_dir)){
      mkdir($assets_dir, 0777, true);
    }
    if(!file_exists($assets_dir)){
      mkdir($assets_dir, 0777, true);
    }
    if(!file_exists($assets_dir.'css/')){
      mkdir($assets_dir.'css/', 0777, true);
    }
    if(!file_exists($assets_dir.'js/')){
      mkdir($assets_dir.'js/', 0777, true);
    }

    // Controller
    if(copy(__DIR__.'/../content-holder/cms/c/CMS_AdminController.php', $app_dir.'controller/admin/CMS_AdminController.php')){
      $utils->console_return("Controller created [ CMS_AdminController ]", 'green');
    } else {
      $utils->console_return("Error on creating Controller [ CMS_AdminController ]", 'red');
    }

    // View files
    $view_from = __DIR__.'/../content-holder/cms/v/admin/';
    $view_to = $app_dir.'view/admin/';
    $view_files = array_filter(glob("$view_from*"), "is_file");
    foreach ($view_files as $f) {
      if(copy($f, $view_to . basename($f))){
        $utils->console_return("View file created [ app/view/admin/".basename($f)." ]", 'green');
      } else {
        $utils->console_return("Error on creating view file [ app/view/admin/".basename($f)." ]", 'red');
      }
    }

    // Base layout
    if(copy(__DIR__.'/../content-holder/cms/v/base/admin.phtml', $app_dir.'view/base/admin.phtml')){
      $utils->console_return("Admin base layout created [ app/view/base/admin.phtml ]", 'green');
    } else {
      $utils->console_return("Error on creating admin base layout [ app/view/base/admin.phtml ]", 'red');
    }

    // Migrations
    $mig_from = __DIR__.'/../content-holder/cms/mg/';
    $mig_files = array_filter(glob("$mig_from*"), "is_file");
    foreach ($mig_files as $f) {
      if(copy($f, $mig_dir . 'mg_'.date('d_m_Y_').basename($f))){
        $utils->console_return("Migration file created [ database/migration/".basename($f)." ]", 'green');
      } else {
        $utils->console_return("Error on creating migration file [ database/migration/".basename($f)." ]", 'red');
      }
    }

    // Assets (CSS, SCSS)
    $asset_css_from = __DIR__.'/../content-holder/cms/as/admin/css/';
    $assets_css_to = $assets_dir.'css/';
    $asset_css_files = array_filter(glob("$asset_css_from*"), "is_file");
    foreach ($asset_css_files as $f) {
      if(copy($f, $assets_css_to . basename($f))){
        $utils->console_return("Asset created [ assets/admin/css/".basename($f)." ]", 'green');
      } else {
        $utils->console_return("Error on creating asset file [ assets/admin/css/".basename($f)." ]", 'red');
      }
    }

    // Assets (JS)
    $asset_from = __DIR__.'/../content-holder/cms/as/admin/js/';
    $assets_to = $assets_dir.'js/';
    $asset_files = array_filter(glob("$asset_from*"), "is_file");
    foreach ($asset_files as $f) {
      if(copy($f, $assets_to . basename($f))){
        $utils->console_return("Asset created [ assets/admin/js/".basename($f)." ]", 'green');
      } else {
        $utils->console_return("Error on creating asset file [ assets/admin/js/".basename($f)." ]", 'red');
      }
    }

    // CMS Config
    if(!file_exists($app_dir.'cms-config.php')){
      if(copy(__DIR__.'/../content-holder/cms/cms-config.php', $app_dir.'cms-config.php')){
        $utils->console_return("CMS Config file created [ app/cms-config.php ]", 'green');
      } else {
        $utils->console_return("Error on creating CMS Config file [ app/cms-config.php ]", 'red');
      }
    }

    // Copy CMS Routes
    require __DIR__.'/../content-holder/cms/routes.php';
    if(file_put_contents($app_dir.'Route.php', $route_content, FILE_APPEND | LOCK_EX)){
      $utils->console_return("CMS routes added to Route file", 'green');
    }
  }

}
(new CreateCms)->index($com);