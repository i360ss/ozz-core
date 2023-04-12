<?php
/**
 * User Meta migration content
 */
$content = "<?php
/**
 * ".ucfirst(CONFIG['AUTH_META_TABLE'])." migration
 */
use Ozz\Core\system\migration\Schema;

class ".ucfirst(CONFIG['AUTH_META_TABLE'])." {
  
  public function up(){
    Schema::createTable('".CONFIG['AUTH_META_TABLE']."', [
      'id'             => ['bigint', 'ai', 'primary', 'nn'],
      'user_id'        => ['bigint', 'nn'],
      'meta_key'       => ['str:255', 'nn'],
      'meta_value'     => ['txt', 'nn'],
      'timestamp'      => ['int', 'nn'],
    ]);
  }
  
  public function down(){
    Schema::dropTable('".CONFIG['AUTH_META_TABLE']."');
  }
}
";