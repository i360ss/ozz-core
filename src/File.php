<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 */

namespace Ozz\Core;

use Ozz\Core\Err;
use Ozz\Core\Lang;
use Ozz\Core\system\file\FileSettings;

class File {

  use FileSettings;

  private static $thisFiles; // Current File(s)
  private static $errors;
  private static $validator;
  private static $formats;
  private static $moveTo;
  private static $uploadedTo; // Upload to directory (for outside Use)
  private static $settings; // File Settings
  private static $maxSize; // Max upload file size
  private static $response = []; // Final Response

  /**
   * Upload Files
   * @param array $files Single/Multiple files to Upload (By default it will get from $_FILES)
   * @param mixed $settings File upload settings or just upload path
   * @param mixed $customExts Validation (ext and size)
   */
  public static function upload($files=null, $settings=false, $customExts=null){
    self::$errors = new Lang;

    // Current File(s) temp
    $currentFiles = $files !== null ? $files : $_FILES;

    // media errors
    self::$response[0] = [
      'error'    => 1,
      'message'  => self::$errors->message('file_error'),
      'uploaded' => null
    ];

    // If file not exist
    if(empty($currentFiles) || !isset($currentFiles['name'])){
      set_error('error', self::$errors->error('file_error'));
      return;
    }

    // Set path
    $to = '';
    if($settings !== false){
      if(is_array($settings)) {
        if(isset($settings['path'])){
          $to = $settings['path'].'/';
        } elseif(isset($settings['dir'])){
          $to = $settings['dir'].'/';
        }
      } elseif(is_string($settings)) {
        $to = $settings; // In this case, Settings is just the path to upload
      }
    }

    // Upload Directory
    self::$moveTo = $to !== null ? UPLOAD_TO.$to.DIRECTORY_SEPARATOR : UPLOAD_TO;
    self::$moveTo = preg_replace("/\/+/", "/", self::$moveTo);
    self::$uploadedTo = $to !== null ? UPLOAD_DIR_PUBLIC.$to.'/' : UPLOAD_DIR_PUBLIC;
    self::$uploadedTo = '/'.preg_replace("/\/+/", "/", self::$uploadedTo);

    // Validator
    self::$validator = CONFIG['DEFAULT_FILE_VALIDATION'];

    // Settings before upload
    self::$settings = $settings ? $settings : false;

    // If Multiple files
    if(is_array($currentFiles['name'])){
      foreach ($currentFiles['name'] as $key => $value) {
        $singleFile = array(
          'name'      => $currentFiles['name'][$key],
          'full_path' => $currentFiles['full_path'][$key],
          'type'      => $currentFiles['type'][$key],
          'tmp_name'  => $currentFiles['tmp_name'][$key],
          'error'     => $currentFiles['error'][$key],
          'size'      => $currentFiles['size'][$key],
        );

        self::$response[$key] = self::proceedToUpload($singleFile, $customExts);
      }
    } else {
      self::$response[0] = self::proceedToUpload($currentFiles, $customExts);
    }

    // Log errors
    set_error('file_errors', self::$response);
    if(!has_error('error') && !has_error('success')){
      set_error((self::$response[0]['error'] ? 'error' : 'success'), self::$response[0]['message']);
    }

    return self::$response;
  }

  /**
   * Proceed upload process
   * @param array $file Single file to upload
   * @param mixed $customExts Custom Validation rules (ext and/or max-size)
   */
  private static function proceedToUpload($file, $customExts) {
    self::$thisFiles = $file;

    // File type
    $typ = get_file_type_to_upload($file);
    if($typ === 'unknown'){
      set_error('error', self::$errors->error('file_invalid_format'));
      return [
        'error'    => 1,
        'message'  => self::$errors->message('file_invalid_format'),
        'uploaded' => null
      ];
    }

    // Valid File Formats
    $validFormats = self::$validator[$typ][1]; // Allowed file formats
    self::$maxSize = self::$validator[$typ][0]; // Max upload size

    if(isset($customExts) && is_array($customExts)){
      $validFormats = $customExts[1];
      self::$maxSize = $customExts[0];
    } elseif(isset($customExts) && is_string($customExts)){
      $validFormats = $customExts;
    }

    self::$formats = array_map('trim', explode('|', $validFormats));

    if($file['name'] == ''){
      return [
        'error' => 1,
        'message' => self::$errors->error('file_required'),
        'uploaded' => null,
      ];
    }

    // Call Correct Upload Method
    switch ($typ) {
      case 'image':
        return self::uploadImage();

      case 'document':
      case 'font':
      case 'audio':
      case 'video':
        return self::uploadDocument();
    }
  }

  /**
   * Validate file size
   * @param $typ File type
   * @param $fileSize File size to validate
   */
  private static function validateFileSize($typ, $fileSize){
    $max = self::$maxSize; // Max size Validation
    $size_conf = substr($max, -1);
		$max_size = (int)substr($max, 0, -1);

    switch ($size_conf) {
      case 'K':
      case 'k':
        $max_size *= 1024;
        break;

      case 'M':
      case 'm':
        $max_size *= 1024;
        $max_size *= 1024;
        break;

      default:
        $max_size = 1024000;
        break;
    }
    return $fileSize > $max_size ? false : true;
  }

  /**
   * Validate file format
   */
  private static function validateFileFormat($file=false, $tmp=false){
    $checkFile = $file ? $file : self::$thisFiles['name'];
    $tempFile = $tmp ? $tmp : self::$thisFiles['tmp_name'];

    $ext = strtolower(pathinfo($checkFile, PATHINFO_EXTENSION));

    if (in_array($ext, self::$formats)) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime = finfo_file($finfo, $tempFile);
      finfo_close($finfo);

      require __DIR__.'/system/utils/mime_types.php';

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
   * Upload Image
   */
  private static function uploadImage() {
    $response = [
      'error'    => 1,
      'message'  => self::$errors->error('file_error'),
      'uploaded' => null
    ];

    if (self::$thisFiles['error'] !== 0) {
      return $response;
    }

    if (!self::validateFileFormat()) {
      $response['message'] = self::$errors->error('file_invalid_format');
      return $response;
    }

    if (!self::validateFileSize('image', self::$thisFiles['size'])) {
      $response['message'] = self::$errors->error('file_too_large');
      return $response;
    }

    // Without Settings
    if (!self::$settings) {
      if (is_dir(self::$moveTo)) {
        $ext = strtolower(pathinfo(self::$thisFiles['name'], PATHINFO_EXTENSION));
        $safeName = bin2hex(random_bytes(16)).'.'.$ext;
        $destination = self::$moveTo . $safeName;
        if (move_uploaded_file(self::$thisFiles['tmp_name'], $destination)) {
          $response = [
            'error'    => 0,
            'message'  => self::$errors->message('image_upload_success'),
            'uploaded' => self::$uploadedTo . $safeName
          ];
        } else {
          $response['message'] = self::$errors->error('file_error');
        }
      } else {
        DEBUG ? Err::notDir(self::$moveTo) : false;
      }
    } else {
      // With Settings
      $finalIMG = self::imageSettings(null);

      if (isset($finalIMG['image']) && $finalIMG['image']['error'] === false) {
        $response = [
          'error'    => 0,
          'message'  => self::$errors->message('image_upload_success'),
          'uploaded' => $finalIMG['image']['url']
        ];

        if (isset($finalIMG['copies']) && $finalIMG['copies']['error'] === false) {
          $response['copies'] = $finalIMG['copies']['url'];
        }
      } elseif (isset($finalIMG['copies']) && $finalIMG['copies']['error'] === false) {
        $response['message'] = isset($finalIMG['image']) ? $finalIMG['image']['error'] : 'error';
        $response['copies'] = $finalIMG['copies']['url'];
      } else {
        $response = [
          'error'   => 1,
          'message' => isset($finalIMG['image']) ? $finalIMG['image']['error'] : 'error',
        ];
      }
    }

    return $response;
  }

  /**
   * Upload Document
   */
  private static function uploadDocument() {
    $response = [
      'error'    => 1,
      'message'  => self::$errors->error('file_error'),
      'uploaded' => null,
    ];

    if (self::$thisFiles['error'] !== 0) {
      return $response;
    }

    if (!self::validateFileFormat()) {
      $response['message'] = self::$errors->error('file_invalid_format');
      return $response;
    }

    if (!self::validateFileSize('document', self::$thisFiles['size'])) {
      $response['message'] = self::$errors->error('file_too_large');
      return $response;
    }

    // With settings
    if (self::$settings && !empty(self::$settings)) {
      return self::commonSettings();
    }

    // Without settings
    if (!is_dir(self::$moveTo)) {
      DEBUG ? Err::notDir(self::$moveTo) : $response['message'] = self::$errors->error('file_error');
      return $response;
    }

    $ext = strtolower(pathinfo(self::$thisFiles['name'], PATHINFO_EXTENSION));
    $safeName = bin2hex(random_bytes(16)).'.'.$ext;
    $destinationPath = self::$moveTo . $safeName;

    if (move_uploaded_file(self::$thisFiles['tmp_name'], $destinationPath)) {
      $response = [
        'error'    => 0,
        'message'  => self::$errors->message('file_upload_success'),
        'uploaded' => self::$uploadedTo . $safeName,
      ];
    } else {
      $response['message'] = self::$errors->error('file_error');
    }

    return $response;
  }

  /**
   * Create File
   * @param string $dir
   * @param string $name
   */
  public static function create($dir, $name=false, $permission=0644) {
    $file_name = $name ? esc_url($name) : '';
    $final_file = clear_multi_slashes($dir.$file_name);

    if (!file_exists($final_file)) {
      if(touch($final_file)){
        chmod($final_file, $permission);
        set_error('success', trans('file_created_success'));
      } else {
        set_error('error', trans_e('file_not_created'));
      }
    } else {
      set_error('error', trans_e('file_already_exist'));
    }
  }

  /**
   * Create Directory
   * @param string $dir
   * @param string $name
   */
  public static function create_dir($dir, $name=false, $permission=0777) {
    $dir_name = $name ? esc_url($name) : '';
    $final_dir = clear_multi_slashes($dir.$dir_name);

    if (!is_dir($final_dir)) {
      mkdir($final_dir, $permission, true);
      is_dir($final_dir)
        ? set_error('success', trans('folder_created_success'))
        : set_error('error', trans_e('folder_not_created'));
    } else {
      set_error('error', trans_e('folder_already_exist'));
    }
  }

  /**
   * Delete File or Directory
   * @param string $dir
   * @param string $name
   */
  public static function delete($dir, $name=false) {
    $dir_name = $name ? esc_url($name) : '';
    $final_dir = clear_multi_slashes($dir.$dir_name);

    if (is_dir($final_dir)) {
      self::delete_dir($final_dir);
      set_error('success', trans('folder_deleted_success'));
    } elseif (is_file($final_dir)) {
      unlink($final_dir);
      set_error('success', trans('file_deleted_success'));
    } else {
      set_error('error', trans_e('file_not_deleted'));
    }
  }

  /**
   * Delete Directory
   * @param string $dir
   */
  public static function delete_dir($dir) {
    $files = glob($dir . '/*');
    if(count($files) > 0){
      foreach ($files as $file) {
        is_dir($file) ? self::delete_dir($file) : unlink($file);
      }
    }
    rmdir($dir);
  }

}