<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 *
 * File based session handler
 * - AES-256-GCM (authenticated) encryption of session data
 * - exclusive per-session file locking (same behaviour as PHP's native "files" handler)
 * - session id validation (no path traversal)
 * - lazy_write support + strict mode support (validateId / updateTimestamp)
 */

namespace Ozz\Core\system\session;

class FileBasedSessionHandler implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface {

  private const CIPHER  = 'aes-256-gcm';
  private const IV_LEN  = 12;
  private const TAG_LEN = 16;

  private string $savePath;
  private string $key;           // 32 byte binary key derived from the secret
  private $fp = null;            // locked file handle of the current request
  private ?string $currentId = null;

  public function __construct(string $savePath, string $secret){
    $this->savePath = rtrim($savePath, '/\\');
    // Always hand OpenSSL a real 32 byte key, whatever the length of the configured secret
    $this->key = hash('sha256', $secret, true);

    if(!is_dir($this->savePath)){
      mkdir($this->savePath, 0700, true);
    }
  }

  public function open(string $path, string $name): bool {
    return is_dir($this->savePath) && is_writable($this->savePath);
  }

  public function close(): bool {
    $this->releaseLock();
    return true;
  }

  /**
   * Read session file (takes an exclusive lock until close/destroy)
   */
  public function read(string $id): string|false {
    if(!$this->lock($id)){
      return false;
    }

    $raw = stream_get_contents($this->fp);
    if($raw === false || $raw === ''){
      return '';
    }

    return $this->decrypt($raw);
  }

  /**
   * Write session file
   */
  public function write(string $id, string $data): bool {
    if($this->fp === null || $this->currentId !== $id){
      if(!$this->lock($id)){
        return false;
      }
    }

    $payload = $this->encrypt($data);
    if($payload === false){
      return false;
    }

    rewind($this->fp);
    ftruncate($this->fp, 0);
    $written = fwrite($this->fp, $payload);
    fflush($this->fp);

    return $written === strlen($payload);
  }

  /**
   * Destroy session
   */
  public function destroy(string $id): bool {
    $file = $this->getFilename($id);
    if($file === null){
      return false;
    }

    if($this->currentId === $id){
      $this->releaseLock();
    }

    if(is_file($file)){
      @unlink($file);
    }

    return true;
  }

  /**
   * Session handler GC - returns number of deleted sessions
   */
  public function gc(int $max_lifetime): int|false {
    $deleted = 0;
    $now = time();

    foreach (glob($this->savePath . '/' . CONFIG['SESSION_PREFIX'] . '*') ?: [] as $file) {
      if(is_file($file) && filemtime($file) + $max_lifetime < $now && @unlink($file)){
        $deleted++;
      }
    }

    return $deleted;
  }

  /**
   * Strict mode: only accept ids that already exist on the server
   */
  public function validateId(string $id): bool {
    $file = $this->getFilename($id);
    return $file !== null && is_file($file);
  }

  /**
   * lazy_write: data did not change, only bump the timestamp
   */
  public function updateTimestamp(string $id, string $data): bool {
    $file = $this->getFilename($id);
    return $file !== null && is_file($file) && touch($file);
  }

  /**
   * Open + exclusively lock the session file
   */
  private function lock(string $id): bool {
    $this->releaseLock();

    $file = $this->getFilename($id);
    if($file === null){
      return false;
    }

    $isNew = !file_exists($file);
    $fp = @fopen($file, 'c+');
    if($fp === false){
      return false;
    }

    if(!flock($fp, LOCK_EX)){
      fclose($fp);
      return false;
    }

    if($isNew){
      @chmod($file, 0600);
    }

    $this->fp = $fp;
    $this->currentId = $id;
    return true;
  }

  private function releaseLock(): void {
    if(is_resource($this->fp)){
      flock($this->fp, LOCK_UN);
      fclose($this->fp);
    }
    $this->fp = null;
    $this->currentId = null;
  }

  /**
   * Encrypt session data -> iv . tag . ciphertext (binary)
   */
  private function encrypt(string $data): string|false {
    $iv = random_bytes(self::IV_LEN);
    $cipher = openssl_encrypt($data, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv, $tag, '', self::TAG_LEN);

    if($cipher === false){
      return false;
    }

    return $iv . $tag . $cipher;
  }

  /**
   * Decrypt data - returns '' (empty session) for corrupted/tampered files
   */
  private function decrypt(string $payload): string {
    if(strlen($payload) < self::IV_LEN + self::TAG_LEN){
      return '';
    }

    $iv     = substr($payload, 0, self::IV_LEN);
    $tag    = substr($payload, self::IV_LEN, self::TAG_LEN);
    $cipher = substr($payload, self::IV_LEN + self::TAG_LEN);

    $plain = openssl_decrypt($cipher, self::CIPHER, $this->key, OPENSSL_RAW_DATA, $iv, $tag);

    return $plain === false ? '' : $plain;
  }

  /**
   * Get session file name (null for invalid ids)
   */
  private function getFilename(string $id): ?string {
    if(preg_match('/^[A-Za-z0-9,-]{16,256}$/', $id) !== 1){
      return null;
    }

    return $this->savePath . '/' . CONFIG['SESSION_PREFIX'] . $id;
  }

}