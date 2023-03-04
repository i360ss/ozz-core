<?php
/**
 * User Meta migration content
 */
$content = "<?php
/**
 * ".ucfirst(AUTH_META_TABLE)." migration
 */
use Ozz\Core\system\migration\Schema;

class ".ucfirst(AUTH_META_TABLE)." {
  
  public function up(){
    Schema::createTable('".AUTH_META_TABLE."', [
      'id'             => ['bigint', 'ai', 'primary', 'nn'],
      'user_id'        => ['bigint', 'nn'],
      'meta_key'       => ['str:255', 'nn'],
      'meta_value'     => ['txt', 'nn'],
      'timestamp'      => ['int', 'nn'],
    ]);
  }
  
  public function down(){
    Schema::dropTable('".AUTH_META_TABLE."');
  }
}
";