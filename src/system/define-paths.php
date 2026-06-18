<?php
/**
 * Define filesystem path constants from CONFIG['APP_PATHS'].
 */
function ozz_define_paths(): void {
  defined('DS') || define('DS', '/');

  $paths = CONFIG['APP_PATHS'];

  defined('PUBLIC_DIR') || define('PUBLIC_DIR', BASE_DIR . trim($paths['public'], DS) . DS);
  defined('APP_DIR') || define('APP_DIR', BASE_DIR . trim($paths['app'], DS) . DS);
  defined('STORAGE_DIR') || define('STORAGE_DIR', BASE_DIR . trim($paths['storage'], DS) . DS);
  defined('CACHE_DIR') || define('CACHE_DIR', BASE_DIR . trim($paths['cache'], DS) . DS);
  defined('DB_DIR') || define('DB_DIR', BASE_DIR . trim($paths['database'], DS) . DS);
  defined('MIGRATION_DIR') || define('MIGRATION_DIR', BASE_DIR . trim($paths['migration'], DS) . DS);
  defined('SQLITE_DIR') || define('SQLITE_DIR', BASE_DIR . trim($paths['sqlite'], DS) . DS);
  defined('CMS_DIR') || define('CMS_DIR', BASE_DIR . trim($paths['cms'], DS) . DS);
  defined('LOG_DIR') || define('LOG_DIR', BASE_DIR . trim($paths['log'], DS) . DS);
  defined('SESSION_DIR') || define('SESSION_DIR', BASE_DIR . trim($paths['session'], DS) . DS);
  defined('SYSTEM_DIR') || define('SYSTEM_DIR', BASE_DIR . trim($paths['system'], DS) . DS);
  defined('VIEW') || define('VIEW', BASE_DIR . trim($paths['view'], DS) . DS);
  defined('UPLOAD_DIR') || define('UPLOAD_DIR', BASE_DIR . trim($paths['upload_dir'], DS) . DS);
  defined('UPLOAD_DIR_PUBLIC') || define('UPLOAD_DIR_PUBLIC', rtrim($paths['upload_dir_public'], DS) . DS);
  defined('ASSETS_DIR') || define('ASSETS_DIR', PUBLIC_DIR . trim($paths['assets'], DS) . DS);

  if (locale()) {
    defined('LANG_DIR') || define('LANG_DIR', BASE_DIR . trim($paths['lang'], DS) . DS . locale() . DS);
  }
}
