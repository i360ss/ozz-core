<?php
// Run: [ php ozz -h migration ] to more info
use Ozz\Core\system\migration\Schema;

class Cms_terms {
  
  public function up(){
    Schema::createTable('cms_terms', [
      'id'          => ['bigint', 'ai', 'primary'],
      'lang'        => ['str:20'],
      'taxonomy_id' => ['int'],
      'name'        => ['str', 'nn'],
      'slug'        => ['str', 'nn'],
    ]);
  }
  
  public function down(){
    Schema::dropTable('cms_terms');
  }
}