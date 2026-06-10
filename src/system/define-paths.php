<?php
/**
 * Define filesystem path constants from CONFIG['APP_PATHS'].
 * @param string $baseDir Project root directory (absolute path with trailing logic handled via BASE_DIR)
 */
function ozz_define_paths(string $baseDir): void {
  defined('DS') || define('DS', '/');

  defined('BASE_DIR') || define('BASE_DIR', $baseDir);

  $paths = CONFIG['APP_PATHS'];

  defined('PUBLIC_DIR') || define('PUBLIC_DIR', BASE_DIR . trim($paths['public'], DS) . DS);

  if (isset($_SERVER['DOCUMENT_ROOT'])) {
    defined('ROOT') || define('ROOT', $_SERVER['DOCUMENT_ROOT'] . DS);
  }

  $core_dir = trim($paths['core'], DS);
  defined('CORE_DIR') || define('CORE_DIR', BASE_DIR . (!empty($core_dir) ? $core_dir . DS : ''));

  defined('APP_DIR') || define('APP_DIR', CORE_DIR . trim($paths['app'], DS) . DS);
  defined('STORAGE_DIR') || define('STORAGE_DIR', CORE_DIR . trim($paths['storage'], DS) . DS);
  defined('CACHE_DIR') || define('CACHE_DIR', CORE_DIR . trim($paths['cache'], DS) . DS);
  defined('DB_DIR') || define('DB_DIR', CORE_DIR . trim($paths['database'], DS) . DS);
  defined('MIGRATION_DIR') || define('MIGRATION_DIR', CORE_DIR . trim($paths['migration'], DS) . DS);
  defined('SQLITE_DIR') || define('SQLITE_DIR', CORE_DIR . trim($paths['sqlite'], DS) . DS);
  defined('CMS_DIR') || define('CMS_DIR', CORE_DIR . trim($paths['cms'], DS) . DS);
  defined('LOG_DIR') || define('LOG_DIR', CORE_DIR . trim($paths['log'], DS) . DS);
  defined('SESSION_DIR') || define('SESSION_DIR', CORE_DIR . trim($paths['session'], DS) . DS);
  defined('SYSTEM_DIR') || define('SYSTEM_DIR', CORE_DIR . trim($paths['system'], DS) . DS);
  defined('VIEW') || define('VIEW', CORE_DIR . trim($paths['view'], DS) . DS);
  defined('UPLOAD_DIR') || define('UPLOAD_DIR', BASE_DIR . trim($paths['upload_dir'], DS) . DS);
  defined('UPLOAD_DIR_PUBLIC') || define('UPLOAD_DIR_PUBLIC', rtrim($paths['upload_dir_public'], DS) . DS);
  defined('ASSETS_DIR') || define('ASSETS_DIR', PUBLIC_DIR . trim($paths['assets'], DS) . DS);

  if (defined('BASE_URL')) {
    defined('ASSETS') || define('ASSETS', BASE_URL . trim($paths['assets'], DS) . DS);
  }

  if (defined('APP_LANG')) {
    defined('LANG_DIR') || define('LANG_DIR', CORE_DIR . trim($paths['lang'], DS) . DS . APP_LANG . DS);
  }
}
