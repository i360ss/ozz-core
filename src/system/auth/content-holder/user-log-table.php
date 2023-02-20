<?php
/**
 * Login attempts migration content
 */
$content = "<?php
/**
 * ".ucfirst(AUTH_THROTTLE_TABLE)." migration
 */
use Ozz\Core\system\migration\Schema;

class ".ucfirst(AUTH_THROTTLE_TABLE)." {
  
  public function up(){
    Schema::createTable('".AUTH_THROTTLE_TABLE."', [
      'id'             => ['int', 'ai', 'primary'],
      'user_id'        => ['int'],
      'user_ip'        => ['str:255'],
      'type'           => ['str:50'],
      'status'         => ['str:50'],
      'is_active'      => ['bool', 'default:0'],
      'user_agent'     => ['txt'],
      'user_agent_all' => ['txt'],
      'timestamp'      => ['int'],
    ]);
  }
  
  public function down(){
    Schema::dropTable('".AUTH_THROTTLE_TABLE."');
  }
}
";