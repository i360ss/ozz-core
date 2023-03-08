<?php

namespace Ozz\Core\system\file;

use Ozz\Core\Err;

trait FileValidation {

  /**
   * Validate File Min and Max size
   * @param int $file_size Actual file size to validate against
   * @param string $max_min Allowed Minimum or maximum file size (with unit [K, M])
   * @param string $typ Validation type (min, max)
   */
  public static function validateFileSize($file_size, $max_min, $typ){
    $unit = substr($max_min, -1);
		$check_size = (int)substr($max_min, 0, -1);

    switch ($unit) {
      case 'K':
      case 'k':
        $check_size *= 1024; // If KB
        break;

      case 'M':
      case 'm':
        $check_size *= 1024;
        $check_size *= 1024; // IF MB
        break;

      default:
        $check_size *= 1024; // Default is KB
        break;
    }

    if($typ == 'max'){
      return $file_size > $check_size ? false : true;
    } else {
      return $file_size < $check_size ? false : true;
    }
  }

  /**
   * Validate File Format
   * @param object|array File
   */
  public static function validateFileFormat($file, $extensions, $key=null){
    $tmp = !is_null($key) ? $file['tmp_name'][$key] : $file['tmp_name'];
    $name = !is_null($key) ? $file['name'][$key] : $file['name'];

    $valid_exts = array_map('trim', explode('/', strtolower($extensions)));
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    if (in_array($ext, $valid_exts)) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime = finfo_file($finfo, $tmp);
      finfo_close($finfo);

      require 'mime_types.php';

      if(isset($MIME_TYPES[$ext])){
        return in_array($mime, $MIME_TYPES[$ext]);
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  /**
   * Check if the file is an Image
   * @param array $file File to check
   */
  public static function isImage($file, $key=null){
    $tmp = !is_null($key) ? $file['tmp_name'][$key] : $file['tmp_name'];
    $name = !is_null($key) ? $file['name'][$key] : $file['name'];

    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmp);
    finfo_close($finfo);

    require 'image_mime_types.php';

    if(isset($IMAGE_MIME_TYPES[$ext])){
      return in_array($mime, $IMAGE_MIME_TYPES[$ext]);
    } else {
      return false;
    }
  }

}