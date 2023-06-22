<?php
// Run: [ php ozz -h migration ] to more info
use Ozz\Core\system\migration\Schema;

class Cms_terms {
  
  public function up(){
    Schema::createTable('cms_terms', [
      'id'          => ['bigint', 'ai', 'primary'],
      'lang'        => ['str:20'],
      'type'        => ['str:30', 'nn'],
      'meta_key'    => ['str:150', 'nn'],
      'meta_value'  => ['bigtxt', 'nn'],
      'created_at'  => ['str:30', 'nn'],
      'modified_at' => ['str:30', 'nn'],
    ]);
  }
  
  public function down(){
    Schema::dropTable('cms_terms');
  }
}