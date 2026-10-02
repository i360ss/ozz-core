<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 *
 * Configures PHP's native Redis / Memcached session handlers from CONFIG.
 *
 * Config keys (all optional, defaults shown):
 *   SESSION_REDIS_HOST      = "127.0.0.1"    (or an absolute socket path: /var/run/redis/redis.sock)
 *   SESSION_REDIS_PORT      = 6379
 *   SESSION_REDIS_USER      = ""             (Redis 6+ ACL user, optional)
 *   SESSION_REDIS_PASSWORD  = ""
 *   SESSION_REDIS_DB        = 0
 *   SESSION_REDIS_PREFIX    = "ozz_sess_"
 *   SESSION_REDIS_TLS       = false
 *
 *   SESSION_MEMCACHED_SERVERS = "127.0.0.1:11211"   (comma separated for several servers)
 *   SESSION_MEMCACHED_PREFIX  = "ozz_sess_"
 */

namespace Ozz\Core\system\session;

class SessionDriver {

  /**
   * Configure the native handler for the given driver.
   * Returns true if a native handler was configured, false for any other driver.
   */
  public static function register(string $driver): bool {
    switch (strtolower($driver)) {
      case 'redis':
        self::redis();
        return true;
      case 'memcached':
        self::memcached();
        return true;
      default:
        return false;
    }
  }

  /**
   * Redis (requires the phpredis extension)
   */
  public static function redis(): void {
    if (!extension_loaded('redis')) {
      throw new \RuntimeException('SESSION_DRIVER=redis requires the phpredis extension (apt: php-redis, or: pecl install redis).');
    }

    $host = CONFIG['SESSION_REDIS_HOST'] ?? '127.0.0.1';
    $port = (int) (CONFIG['SESSION_REDIS_PORT'] ?? 6379);
    $user = (string) (CONFIG['SESSION_REDIS_USER'] ?? '');
    $pass = (string) (CONFIG['SESSION_REDIS_PASSWORD'] ?? '');
    $tls  = filter_var(CONFIG['SESSION_REDIS_TLS'] ?? false, FILTER_VALIDATE_BOOLEAN);

    $params = [
      'database' => (int) (CONFIG['SESSION_REDIS_DB'] ?? 0),
      'prefix'   => (string) (CONFIG['SESSION_REDIS_PREFIX'] ?? 'ozz_sess_'),
    ];

    if ($pass !== '') {
      // Redis 6+ ACL needs [user, password]; classic requirepass needs just the password
      $params['auth'] = $user !== '' ? [$user, $pass] : $pass;
    }

    $query = http_build_query($params);

    if (str_starts_with($host, '/')) {
      $path = 'unix://' . $host . '?' . $query;                              // unix socket
    } else {
      $path = ($tls ? 'tls://' : 'tcp://') . $host . ':' . $port . '?' . $query;
    }

    ini_set('session.save_handler', 'redis');
    ini_set('session.save_path', $path);

    // phpredis session locking is OFF by default - turn it on so parallel
    // requests from one user can't overwrite each other's session data
    ini_set('redis.session.locking_enabled', '1');
  }

  /**
   * Memcached (requires the memcached extension, not "memcache")
   */
  public static function memcached(): void {
    if (!extension_loaded('memcached')) {
      throw new \RuntimeException('SESSION_DRIVER=memcached requires the memcached extension (apt: php-memcached).');
    }

    $servers = (string) (CONFIG['SESSION_MEMCACHED_SERVERS'] ?? '127.0.0.1:11211');

    ini_set('session.save_handler', 'memcached');
    ini_set('session.save_path', $servers);

    ini_set('memcached.sess_prefix', (string) (CONFIG['SESSION_MEMCACHED_PREFIX'] ?? 'ozz_sess_'));
    ini_set('memcached.sess_locking', '1');            // lock sessions during a request
    ini_set('memcached.sess_consistent_hash', '1');    // keeps sessions stable if you add/remove a server
  }

}