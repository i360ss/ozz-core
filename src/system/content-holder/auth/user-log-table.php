<?php
/**
 * Login attempts migration content
 */
$content = "<?php
/**
 * ".ucfirst(CONFIG['AUTH_LOG_TABLE'])." migration
 */
use Ozz\Core\system\migration\Schema;

class ".ucfirst(CONFIG['AUTH_LOG_TABLE'])." {
  
  public function up(){
    Schema::createTable('".CONFIG['AUTH_LOG_TABLE']."', [
      'id'             => ['bigint', 'ai', 'primary', 'nn'],
      'user_id'        => ['bigint', 'nn'],
      'user_ip'        => ['str:255', 'nn'],
      'type'           => ['str:50', 'nn'],
      'status'         => ['str:50', 'nn'],
      'is_active'      => ['bool', 'default:0', 'nn'],
      'user_agent'     => ['txt', 'nn'],
      'timestamp'      => ['int', 'nn'],
    ]);
  }
  
  public function down(){
    Schema::dropTable('".CONFIG['AUTH_LOG_TABLE']."');
  }
}
";