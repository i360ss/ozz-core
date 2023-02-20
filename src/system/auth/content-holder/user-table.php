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
      'user_id'           => ['int', 'ai', 'primary'],
      'username'          => ['str:150'],
      'first_name'        => ['str:150'],
      'last_name'         => ['str:150'],
      'email'             => ['str:150', 'unique'],
      'password'          => ['str:255'],
      'role'              => ['str:20'],
      'status'            => ['str:20'],
      'activation_key'    => ['txt'],
      'avatar'            => ['txt'],
      'registered_at'     => ['int'],
      'email_verified_at' => ['int'],
      'last_change_at'    => ['int', 'default:CURRENT_TIMESTAMP'],
    ]);
  }

  public function down(){
    Schema::dropTable('".AUTH_USERS_TABLE."');
  }
}";