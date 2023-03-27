<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 */

namespace Ozz\Core\system\session;

class FileBasedSessionHandler implements \SessionHandlerInterface {
  private $savePath;
  private $key;

  public function __construct($savePath, $key){
    $this->savePath = $savePath;
    $this->key = $key;
  }

  #[\ReturnTypeWillChange]
  public function open($savePath, $sessionName){
    // Delete expired session files
    foreach (glob($this->savePath . '/*') as $file) {
      if(filemtime($file) + SESSION_LIFETIME < time()){
        unlink($file);
      }
    }

    return true;
  }

  #[\ReturnTypeWillChange]
  public function close(){
    return true;
  }

  /**
   * Read session file
   */
  #[\ReturnTypeWillChange]
  public function read($sessionId){
    $filename = $this->getFilename($sessionId);
    if (!file_exists($filename)) {
      return '';
    }

    $encryptedData = file_get_contents($filename);
    return $this->decrypt($encryptedData);
  }

  /**
   * Write session file
   */
  #[\ReturnTypeWillChange]
  public function write($sessionId, $data){
    $filename = $this->getFilename($sessionId);
    $encryptedData = $this->encrypt($data);
    return file_put_contents($filename, $encryptedData) !== false;
  }

  /**
   * Destroy session
   */
  #[\ReturnTypeWillChange]
  public function destroy($sessionId): int {
    $filename = $this->getFilename($sessionId);
    if (file_exists($filename)) {
      unlink($filename);
    }

    return true;
  }

  /**
   * Session handler GC
   */
  #[\ReturnTypeWillChange]
  public function gc($maxLifetime): int|false {
    foreach (glob($this->savePath . '/*') as $filename) {
      if (filemtime($filename) + $maxLifetime < time() && file_exists($filename)) {
        unlink($filename);
      }
    }

    return true;
  }

  /**
   * Encrypt session data
   */
  private function encrypt($data){
    $iv = random_bytes(16);
    $cipher = 'AES-256-CBC';
    $encryptedData = openssl_encrypt($data, $cipher, $this->key, 0, $iv);
    return base64_encode($encryptedData . '::' . $iv);
  }
  
  /**
   * Decrypt data
   */
  private function decrypt($encryptedData){
    list($encryptedData, $iv) = explode('::', base64_decode($encryptedData), 2);
    $cipher = 'AES-256-CBC';
    $decryptedData = openssl_decrypt($encryptedData, $cipher, $this->key, 0, $iv);
    return $decryptedData;
  }

  /**
   * Get session file name
   */
  private function getFilename($sessionId){
    return $this->savePath . '/'.SESSION_PREFIX. $sessionId;
  }

}

?>