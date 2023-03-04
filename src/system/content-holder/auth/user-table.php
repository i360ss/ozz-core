<?php
/**
 * Auth migration content
 */
$content = "<?php
/**
 * ".ucfirst(AUTH_USERS_TABLE)." Migration
 */
use Ozz\Core\system\migration\Schema;

class ".ucfirst(AUTH_USERS_TABLE)." {

  public function up(){
    Schema::createTable('".AUTH_USERS_TABLE."', [
      'user_id'           => ['bigint', 'ai', 'primary', 'nn'],
      'username'          => ['str:150', 'unique', 'nn'],
      'first_name'        => ['str:150', 'nn'],
      'last_name'         => ['str:150', 'nn'],
      'email'             => ['str:150', 'unique', 'nn'],
      'password'          => ['str:255', 'nn'],
      'role'              => ['str:20', 'nn'],
      'status'            => ['str:20', 'nn'],
      'activation_key'    => ['str:255', 'nn'],
      'avatar'            => ['txt', 'nn'],
      'registered_at'     => ['int', 'nn'],
      'email_verified_at' => ['int', 'nn'],
      'last_change_at'    => ['int', 'default:CURRENT_TIMESTAMP', 'nn'],
    ]);
  }

  public function down(){
    Schema::dropTable('".AUTH_USERS_TABLE."');
  }
}";