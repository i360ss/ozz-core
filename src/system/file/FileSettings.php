<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\file;

use Ozz\Core\Err;

trait FileSettings {

  /**
   * Upload each image
   * @param string $img Image
   * @param string $tmp Temp name
   * @param string $dir Directory
   * @param int $qlt Quality
   * @param array $copies Copies settings
   */
  private static function uploadEachImage($img, $tmp=null, $dir=null, $qlt=null, $copies=false){
    $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
    switch ($ext) {
      case 'gif':
        $im = @imagecreatefromgif($tmp);
        !$copies ? $image = @imagegif($im, $dir, $qlt) : false;
        break;
      case 'jpg':
      case 'jpeg':
        $im = @imagecreatefromjpeg($tmp);
        !$copies ? $image = @imagejpeg($im, $dir, $qlt) : false;
        break;
      case 'png':
        $im = @imagecreatefrompng($tmp);
        !$copies ? $image = @imagepng($im, $dir) : false;
        break;
      case 'bmp':
        $im = @imagecreatefrombmp($tmp);
        !$copies ? $image = @imagebmp($im, $dir, $qlt) : false;
        break;
      case 'webp':
        $im = @imagecreatefromwebp($tmp);
        !$copies ? $image = @imagewebp($im, $dir, $qlt) : false;
        break;
      case 'svg':
        $svgContent = file_get_contents($tmp);

        // SVG Sanitization - Set allowed element
        $sanitizedSVG = false;
        $conf = CMS_CONFIG ? CMS_CONFIG : CONFIG;
        if($conf['SANITIZE_SVG'] === true) {
          $wildcard = $conf['SANITIZE_SVG_ALLOWED_ELEMENTS'] ? $conf['SANITIZE_SVG_ALLOWED_ELEMENTS'] : [];
          $sanitizedSVG = esx_svg($svgContent, $wildcard);
        }

        $dom = new \DOMDocument();
        if($sanitizedSVG){
          $dom->loadXML($sanitizedSVG);
        } else {
          $dom->loadXML($svgContent);
        }
        $im = file_put_contents($dir, $dom->saveXML()) !== false ? $img : false;
        !$copies ? $image = $img : false;
        break;
    }

    if (isset($copies) && $copies === true) {
      return isset($im) ? $im : false;
    } else {
      return isset($image) ? true : false; // imagedestroy($im);
    }
  }

  /**
   * Set Up File Name
   * @param $setts Settings to get rename options
   * @param $name Current name
   */
  private static function setName($setts, $name){
    if(isset($setts['rename']) && $setts['rename'] !== ''){
      $newName = ($setts['rename']=='rand' || $setts['rename'] == 'random')
        ? bin2hex(random_bytes(8))
        : $setts['rename'];
      return (isset($setts['prefix'])) 
        ? $setts['prefix'].$newName.'.'.pathinfo($name, PATHINFO_EXTENSION)
        : $newName.'.'.pathinfo($name, PATHINFO_EXTENSION);
    } elseif(isset($setts['prefix'])) {
      return $setts['prefix'].$name;
    } else{
      return $name;
    }
  }

  /**
   * Image Manipulation and Upload Settings
   * @param int $ky Image key for multiple image upload
   */
  private static function imageSettings($ky=null){
    $img = self::$thisFiles;
    $imgName = isset($ky) ? $img['name'][$ky] : $img['name'];
    $imgTmp = isset($ky) ? $img['tmp_name'][$ky] : $img['tmp_name'];
    $ext = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
    $finalOut['image']['url'] = false;
    $finalOut['image']['error'] = false;
    $finalOut['copies'] = null;

    // Upload Original Image 
    if(isset(self::$settings['ignore_source']) && self::$settings['ignore_source'] === true){
      // No required Parameters provided
      if(!isset(self::$settings['copies'])){
        $finalOut['image']['error'] = self::$errors->error('file_error');
        DEBUG ? Err::paramsRequiredForUploadSettings('File::upload() settings') : false;
      }
    } else {
      $name = self::setName(self::$settings, $imgName);
      if(isset(self::$settings['mkdir']) && self::$settings['mkdir'] === true){
        !is_dir(self::$moveTo) ? mkdir(self::$moveTo, 0777, true) : false; // Make directory if not exist
      } else {
        if(!is_dir(self::$moveTo)){
          if (DEBUG) {
            return  Err::notDir(self::$moveTo);
          } else {
            $finalOut['image']['error'] = self::$errors->error('file_error');
          }
        }
      }

      $dir = self::$moveTo.basename($name);
      $quality = isset(self::$settings['quality']) && self::$settings['quality'] !=='' ? self::$settings['quality'] : -1;

      if(file_exists($dir)){
        $finalOut['image']['error'] = self::$errors->error('file_already_exist');
      } else {
        // Make Image and Upload
        if(self::uploadEachImage($name, $imgTmp, $dir, $quality)){
          $finalOut['image']['url'] = self::$uploadedTo . $name;
        } else {
          $finalOut['image']['url'] = null;
        }
      }
    }

    // Create and Upload Copies
    if(isset(self::$settings['copies']) && !empty(self::$settings['copies'])){
      foreach (self::$settings['copies'] as $key => $copy) {
        $finalOut['copies']['url'][$key] = null;
        $finalOut['copies']['error'] = false;

        // Manipulate image
        $gdImage = self::uploadEachImage($imgName, $imgTmp, null, null, true); // GDimage
        list($origWidth, $origHeight, $type) = getimagesize($imgTmp);

        // Image Resizing
        $newWidth = $origWidth;
        $newHeight = $origHeight;

        if(isset($copy['width']) && $copy['width'] !== ''){
          $ratio = $copy['width'] / $origWidth;
          $newWidth = $copy['width'];
          $newHeight = $origHeight * $ratio;
        }

        if(isset($copy['height']) && $copy['height'] !== ''){
          if($newHeight > $copy['height']){
            $ratio = $copy['height'] / $origHeight;
            $newHeight = $copy['height'];
            $newWidth = $origWidth * $ratio;
          }
        }

        // Image Crop Properties
        $dstX = 0;
        $dstY = 0;
        $srcX = 0;
        $srcY = 0;

        if(isset($copy['crop']) && !empty($copy['crop'])){
          $dstX = $copy['crop']['dx'] ? $copy['crop']['dx'] : 0;
          $dstY = $copy['crop']['dy'] ? $copy['crop']['dy'] : 0;
          $srcX = $copy['crop']['sx'] ? $copy['crop']['sx'] : 0;
          $srcY = $copy['crop']['sy'] ? $copy['crop']['sy'] : 0;
        }

        // Rename the Copy (with prefix)
        $nameSize = '-'.round($newWidth).'x'.round($newHeight).'.';
        $prifix = isset(self::$settings['prefix']) ? self::$settings['prefix'] : '';

        if((isset($copy['rename']) &&  $copy['rename'] !== '')){
          $newName = ($copy['rename']=='rand' || $copy['rename'] == 'random')
            ? bin2hex(random_bytes(8))
            : $copy['rename'];
          $fileName = $prifix.$newName.$nameSize.pathinfo($imgName, PATHINFO_EXTENSION);
        } else {
          $fileName = $prifix.pathinfo($imgName, PATHINFO_FILENAME).$nameSize.pathinfo($imgName, PATHINFO_EXTENSION);
        }

        // Final Copy DIR + NAME
        $copyDir = isset($copy['dir']) ? UPLOAD_TO.$copy['dir'] : UPLOAD_TO;
        $copyDirWithName = $copyDir.$fileName;

        // Make Copy
        if($gdImage){
          $newCopy = imagecreatetruecolor(intval($newWidth), intval($newHeight));
          imagecopyresampled($newCopy, $gdImage, $dstX, $dstY, $srcX, $srcY, intval($newWidth), intval($newHeight), $origWidth, $origHeight);
          $qlt = $copy['quality'] ?? 100;

          !is_dir($copyDir) ? mkdir($copyDir, 0777, true) : false; // Make directory if not exist

          switch ($ext) {
            case 'gif':
              $finalCopy = @imagegif($newCopy, $copyDirWithName, $qlt);
              break;
            case 'jpg':
            case 'jpeg':
              $finalCopy = @imagejpeg($newCopy, $copyDirWithName, $qlt);
              break;
            case 'png':
              $finalCopy = @imagepng($newCopy, $copyDirWithName);
              break;
            case 'bmp':
              $finalCopy = @imagebmp($newCopy, $copyDirWithName, $qlt);
              break;
            case 'webp':
              $finalCopy = @imagewebp($newCopy, $copyDirWithName, $qlt);
              break;
          }
        }

        // Copies Response
        if($finalCopy){
          $imgurl = isset($copy['dir']) ? $copy['dir'].$fileName : $fileName;
          $finalOut['copies']['url'][$key] = '/uploads/'.$imgurl;
        }
        else{
          $finalOut['copies']['error'] = 1;
        }
      }
    }

    return $finalOut;
  }

  /**
   * Document/Font settings and upload
   * Valid Settings (Rename, Prefix, mkdir)
   * @param int $ky File key for multiple file upload
   */
  private static function commonSettings($ky=null){
    $doc = self::$thisFiles;
    $docName = isset($ky) ? $doc['name'][$ky] : $doc['name'];
    $docTmp = isset($ky) ? $doc['tmp_name'][$ky] : $doc['tmp_name'];

    $docFinalName = self::setName(self::$settings, $docName);

    $response = [
      'error' => 1,
      'message' => self::$errors->error('file_error'),
      'uploaded' => null
    ];

    if(is_dir(self::$moveTo)){
      if(file_exists(self::$moveTo.$docFinalName)){
        $response['message'] = self::$errors->error('file_already_exist');
      } elseif (move_uploaded_file($docTmp, self::$moveTo.$docFinalName)) {
        $response = [
          'error' => 0,
          'message' => self::$errors->message('file_upload_success'),
          'uploaded' => self::$uploadedTo.$docFinalName
        ];
      } else {
        $response['message'] = self::$errors->error('file_error');
      }
    } elseif (isset(self::$settings['mkdir']) && self::$settings['mkdir'] === true){
      mkdir(self::$moveTo, 0777, true); // Make directory if not exist

      if (move_uploaded_file($docTmp, self::$moveTo.$docFinalName)) {
        $response = [
          'error' => 0,
          'message' => self::$errors->message('file_upload_success'),
          'uploaded' => self::$uploadedTo.$docFinalName
        ];
      } else {
        $response['message'] = self::$errors->error('file_error');
      }
    } else {
      if(DEBUG){
        return  Err::notDir(self::$moveTo);
      } else {
        $response['message'] = self::$errors->error('file_error');
      }
    }

    return $response;
  }

}