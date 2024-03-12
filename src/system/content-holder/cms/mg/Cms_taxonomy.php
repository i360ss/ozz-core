<?php
// Run: [ php ozz -h migration ] to more info
use Ozz\Core\system\migration\Schema;

class Cms_taxonomy {
  
  public function up(){
    Schema::createTable('cms_taxonomy', [
      'id'          => ['bigint', 'ai', 'primary'],
      'lang'        => ['str:20'],
      'name'        => ['str', 'nn'],
      'slug'        => ['str', 'nn'],
      'content'     => ['bigtxt', 'nn'],
    ]);
  }
  
  public function down(){
    Schema::dropTable('cms_taxonomy');
  }
}