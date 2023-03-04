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


  # ---------------------------------------
  // Upload Files
  # ---------------------------------------
  /**
   * @param string $typ file Type (image, document, font, audio, video)
   * @param $files Single/Multiple files to Upload (By default it will get from $_FILES)
   * @param string $to Upload to this directory
   * @param array $settings File upload settings
   */
  public static function upload(string $typ, $files=null, $to=null, $settings=false, $customExts=null){

    self::$errors = new Lang;

    // Validator
    self::$validator = DEFAULT_FILE_VALIDATION;
    
    // Invalid File type defined in function call
    if(!array_key_exists($typ, self::$validator)){
      Err::invalidFileTypeDefinedToUpload($typ);
      exit;
    }

    // Valid File Formats
    $validFormats = self::$validator[$typ][1]; // Allowed file formats
    self::$maxSize = self::$validator[$typ][0]; // Max upload size

    if(isset($customExts) && is_array($customExts)){
      $validFormats = $customExts[1];
      self::$maxSize = $customExts[0];
    }
    elseif(isset($customExts) && is_string($customExts)){
      $validFormats = $customExts;
    }
    
    self::$formats = array_map('trim', explode('|', $validFormats));

    // Upload Directory
    self::$moveTo = $to !== null ? UPLOAD_TO.$to : UPLOAD_TO;
    self::$moveTo = preg_replace("/\/+/", "/", self::$moveTo);

    self::$uploadedTo = $to !== null ? UPLOAD_DIR_PUBLIC.$to : UPLOAD_DIR_PUBLIC;
    self::$uploadedTo = preg_replace("/\/+/", "/", self::$uploadedTo);

    // Current File(s)
    $ReqFiles = $files !== null ? $files : $_FILES;
    self::$thisFiles = $ReqFiles[array_key_first($ReqFiles)];

    if(is_string(self::$thisFiles['name']) && self::$thisFiles['name'] == ''){
      return [
        'error' => 1,
        'message' => self::$errors->error('file_required'),
        'uploaded' => null,
        'no_file' => true,
      ];
    } elseif (is_array(self::$thisFiles['name'])) {
      if(!isset(self::$thisFiles['name'][0]) || empty(self::$thisFiles['name'][0]) || self::$thisFiles['name'][0] == ''){
        return [
          'error' => 1,
          'message' => self::$errors->error('file_required'),
          'uploaded' => null,
          'no_file' => true,
        ];
      }
    }

    // Settings before upload
    self::$settings = $settings ? $settings : false;

    // Call Correct Upload Method
    switch ($typ) {
      case 'image':
        return self::uploadImage();
        break;
        
      case 'document':
        return self::uploadDocument();
        break;

      case 'audio':
        return self::uploadAudio();
        break;

      case 'video':
        return self::uploadVideo();
        break;

      case 'font':
        return self::uploadFont();
        break;
    }
  }



  # ---------------------------------------
  // Validate file size
  # ---------------------------------------
  /**
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



  # ---------------------------------------
  // Validate file format
  # ---------------------------------------
  private static function validateFileFormat($file=false, $tmp=false){
    $checkFile = $file ? $file : self::$thisFiles['name'];
    $tempFile = $tmp ? $tmp : self::$thisFiles['tmp_name'];

    $ext = strtolower(pathinfo($checkFile, PATHINFO_EXTENSION));

    if (in_array($ext, self::$formats)) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime = finfo_file($finfo, $tempFile);
      finfo_close($finfo);

      require __DIR__.'/system/file/mime_types.php';

      if(isset($MIME_TYPES[$ext])){
        return in_array($mime, $MIME_TYPES[$ext]);
      }
      else {
        return false;
      }
    }
    else{
      return false;
    }
  }



  # ---------------------------------------
  // Upload Image
  # ---------------------------------------
  private static function uploadImage(){
    if(is_array(self::$thisFiles['name'])){
      // Validate and Upload Multiple Images
      foreach (self::$thisFiles['name'] as $k => $val) {
        if(self::validateFileFormat($val, self::$thisFiles['tmp_name'][$k])){
          if(self::validateFileSize('image', self::$thisFiles['size'][$k])){
            if(self::$thisFiles['error'][$k] == 0 && self::$settings){

              // Manipulate Image (Settings)
              $finalIMG = self::imageSettings($k);

              if(isset($finalIMG['image']) && $finalIMG['image']['error'] === false){
                $response['error'][$k] = 0;
                $response['message'][$k] = self::$errors->message('image_upload_success');
                $response['uploaded'][$k] = $finalIMG['image']['url'];

                if($finalIMG['copies'] !== null && $finalIMG['copies']['error'] === false){
                  $response['copies'][$k] = $finalIMG['copies']['url'];
                }
              }
              elseif(isset($finalIMG['copies']) && $finalIMG['copies']['error'] === false){
                $response['error'][$k] = 1;
                $response['message'][$k] = $finalIMG['image']['error'];
                $response['uploaded'][$k] = $finalIMG['image']['url'];
                $response['copies'][$k] = $finalIMG['copies']['url'];
              }
              else{
                $response['error'][$k] = 1;
                $response['message'][$k] = isset($finalIMG['image']) ? $finalIMG['image']['error'] : null;
                $response['uploaded'][$k] = null;
              }              
            }
            elseif(self::$thisFiles['error'][$k] == 0){
              if(is_dir(self::$moveTo)){
                if(file_exists(self::$moveTo.basename($val))){
                  $response['error'][$k] = 1;
                  $response['message'][$k] = self::$errors->error('file_already_exist');
                  $response['uploaded'][$k] = null;
                }
                elseif (move_uploaded_file(self::$thisFiles['tmp_name'][$k], self::$moveTo.basename($val))) {
                  $response['error'][$k] = 0;
                  $response['message'][$k] = self::$errors->message('image_upload_success');
                  $response['uploaded'][$k] = self::$uploadedTo.basename($val);
                }
                else{
                  $response['error'][$k] = 1;
                  $response['message'][$k] = self::$errors->error('file_error');
                  $response['uploaded'][$k] = null;
                }
              }
              else{
                if(DEBUG){
                  return Err::notDir(self::$moveTo);
                }
                else{
                  $response['error'][$k] = 1;
                  $response['message'][$k] = self::$errors->error('file_error');
                  $response['uploaded'][$k] = null;
                }
              }
            }
            else{
              $response['error'][$k] = 1;
              $response['message'][$k] = self::$errors->error('file_error');
              $response['uploaded'][$k] = null;
            }
          }
          else{
            $response['error'][$k] = 1;
            $response['message'][$k] = self::$errors->error('file_too_large');
            $response['uploaded'][$k] = null;
          }
        }
        else{
          $response['error'][$k] = 1;
          $response['message'][$k] = self::$errors->error('file_invalid_format');
          $response['uploaded'][$k] = null;
        }
      } // End of loop
    }
    else {
      // Validate and Upload 1 image
      $response = [
        'error' => 1,
        'message' => self::$errors->error('file_error'),
        'uploaded' => null
      ];

      if(self::validateFileFormat()){
        if(self::validateFileSize('image', self::$thisFiles['size'])){
          if(self::$thisFiles['error'] == 0 && self::$settings){

            // One Image & Copies (Settings)
            $finalIMG = self::imageSettings(null);

            if(isset($finalIMG['image']) && $finalIMG['image']['error'] === false){
              $response = [
                'error' => 0,
                'message' => self::$errors->message('image_upload_success'),
                'uploaded' => $finalIMG['image']['url']
              ];

              if(isset($finalIMG['copies']) && $finalIMG['copies']['error'] === false){
                $response['copies'] = $finalIMG['copies']['url'];
              }
            }
            elseif(isset($finalIMG['copies']) && $finalIMG['copies']['error'] === false){
              $response['message'] = isset($finalIMG['image']) ? $finalIMG['image']['error'] : null;
              $response['copies'] = $finalIMG['copies']['url'];
            }
            else {
              $response = [
                'error' => 1,
                'message' => isset($finalIMG['image']) ? $finalIMG['image']['error'] : null,
              ];
            }
          }
          elseif(self::$thisFiles['error'] == 0){
            if(is_dir(self::$moveTo)){
              if(file_exists(self::$moveTo.basename(self::$thisFiles['name']))){
                $response['message'] = self::$errors->error('file_already_exist');
              }
              elseif (move_uploaded_file(self::$thisFiles['tmp_name'], self::$moveTo.basename(self::$thisFiles['name']))) {
                $response = [
                  'error' => 0,
                  'message' => self::$errors->message('image_upload_success'),
                  'uploaded' => self::$uploadedTo.basename(self::$thisFiles['name'])
                ];
              }
              else {
                $response['message'] = self::$errors->error('file_error');
              } 
            }
            else{
              DEBUG 
              ? Err::notDir(self::$moveTo) 
              : $response['message'] = self::$errors->error('file_error');
            }
          }
          else{
            $response['message'] = self::$errors->error('file_error');
          }
        }
        else {
          $response['message'] = self::$errors->error('file_too_large');
        }
      }
      else{
        $response['message'] = self::$errors->error('file_invalid_format');
      }
    }
    
    return $response;
  }



  # ---------------------------------------
  // Upload Document
  # ---------------------------------------
  private static function uploadDocument(){
    if(is_array(self::$thisFiles['name'])){
      // Validate and Upload Multiple Documents
      foreach (self::$thisFiles['name'] as $k => $val) {
        if(self::validateFileFormat($val, self::$thisFiles['tmp_name'][$k])){
          if(self::validateFileSize('document', self::$thisFiles['size'][$k])){
            if(self::$thisFiles['error'][$k] == 0 && self::$settings){
              $finalOut = self::commonSettings($k);
              $response['error'][$k] = $finalOut['error'];
              $response['message'][$k] = $finalOut['message'];
              $response['uploaded'][$k] = $finalOut['uploaded'];
            }
            else{
              $response['error'][$k] = 1;
              $response['message'][$k] = self::$errors->error('file_error');
              $response['uploaded'][$k] = null;

              if(is_dir(self::$moveTo)){
                if(file_exists(self::$moveTo.basename($val))){
                  $response['message'][$k] = self::$errors->error('file_already_exist');
                }
                elseif (move_uploaded_file(self::$thisFiles['tmp_name'][$k], self::$moveTo.basename($val))) {
                  $response['error'][$k] = 0;
                  $response['message'][$k] = self::$errors->message('file_upload_success');
                  $response['uploaded'][$k] = self::$uploadedTo.basename($val);
                }
              }
              else{
                DEBUG 
                ? Err::notDir(self::$moveTo) 
                : $response['message'][$k] = self::$errors->error('file_error');
              }
            }
          }
        }
      }
    }
    else{
      // Validate and Upload Single Documents
      $response = [
        'error' => 1,
        'message' => self::$errors->error('file_error'),
        'uploaded' => null,
      ];
      
      if(self::$thisFiles['error'] == 0){
        if(self::validateFileFormat()){
          if(self::validateFileSize('document', self::$thisFiles['size'])){
            if(self::$settings && !empty(self::$settings)){
              // With settings 
              $response = self::commonSettings();
            }
            else{
              // Without settings
              if(is_dir(self::$moveTo)){
                if(file_exists(self::$moveTo.basename(self::$thisFiles['name']))){
                  $response['message'] = self::$errors->error('file_already_exist');
                  $response['uploaded'] = null;
                }
                elseif (move_uploaded_file(self::$thisFiles['tmp_name'], self::$moveTo.basename(self::$thisFiles['name']))) {
                  $response = [
                    'error' => 0,
                    'message' => self::$errors->message('file_upload_success'),
                    'uploaded' => self::$uploadedTo.basename(self::$thisFiles['name'])
                  ];
                }
                else {
                  $response['message'] = self::$errors->error('file_error');
                }
              }
              else{
                DEBUG 
                ? Err::notDir(self::$moveTo) 
                : $response['message'] = self::$errors->error('file_error');
              }
            }
          }
          else{
            $response['message'] = self::$errors->error('file_too_large');
          }
        }
        else{
          $response['message'] = self::$errors->error('file_invalid_format');
        }
      }
    }

    return $response;
  }



  # ---------------------------------------
  // Upload Font
  # ---------------------------------------
  private static function uploadFont(){
    if(is_array(self::$thisFiles['name'])){
      // Validate and Upload Multiple Fonts
      foreach (self::$thisFiles['name'] as $k => $val) {
        if(self::validateFileFormat($val, self::$thisFiles['tmp_name'][$k])){
          if(self::validateFileSize('font', self::$thisFiles['size'][$k])){
            if(self::$thisFiles['error'][$k] == 0 && self::$settings){
              $finalOut = self::commonSettings($k);
              $response['error'][$k] = $finalOut['error'];
              $response['message'][$k] = $finalOut['message'];
              $response['uploaded'][$k] = $finalOut['uploaded'];
            }
            else{
              $response['error'][$k] = 1;
              $response['message'][$k] = self::$errors->error('file_error');
              $response['uploaded'][$k] = null;

              if(is_dir(self::$moveTo)){
                if(file_exists(self::$moveTo.basename($val))){
                  $response['message'][$k] = self::$errors->error('file_already_exist');
                }
                elseif (move_uploaded_file(self::$thisFiles['tmp_name'][$k], self::$moveTo.basename($val))) {
                  $response['error'][$k] = 0;
                  $response['message'][$k] = self::$errors->message('file_upload_success');
                  $response['uploaded'][$k] = self::$uploadedTo.basename($val);
                }
              }
              else{
                DEBUG 
                ? Err::notDir(self::$moveTo) 
                : $response['message'][$k] = self::$errors->error('file_error');
              }
            }
          }
        }
      }
    }
    else{
      // Validate and Upload Single Font
      $response = [
        'error' => 1,
        'message' => self::$errors->error('file_error'),
        'uploaded' => null,
      ];
      
      if(self::$thisFiles['error'] == 0){
        if(self::validateFileFormat()){
          if(self::validateFileSize('font', self::$thisFiles['size'])){
            if(self::$settings && !empty(self::$settings)){
              // With settings 
              $response = self::commonSettings();
            }
            else{
              // Without settings
              if(is_dir(self::$moveTo)){
                if(file_exists(self::$moveTo.basename(self::$thisFiles['name']))){
                  $response['message'] = self::$errors->error('file_already_exist');
                }
                elseif (move_uploaded_file(self::$thisFiles['tmp_name'], self::$moveTo.basename(self::$thisFiles['name']))) {
                  $response = [
                    'error' => 0,
                    'message' => self::$errors->message('file_upload_success'),
                    'uploaded' => self::$uploadedTo.basename(self::$thisFiles['name'])
                  ];
                }
                else {
                  $response['message'] = self::$errors->error('file_error');
                }
              }
              else{
                DEBUG 
                ? Err::notDir(self::$moveTo) 
                : $response['message'] = self::$errors->error('file_error');
              }
            }
          }
          else{
            $response['message'] = self::$errors->error('file_too_large');
          }
        }
        else{
          $response['message'] = self::$errors->error('file_invalid_format');
        }
      }
    }

    return $response;
  }

}