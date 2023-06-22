<?php
// Run: [ php ozz -h migration ] to more info
use Ozz\Core\system\migration\Schema;

class Cms_blocks {
  
  public function up(){
    Schema::createTable('cms_blocks', [
      'id'          => ['bigint', 'ai', 'primary'],
      'lang'        => ['str:20'],
      'author'      => ['bigint', 'nn'],
      'slug'        => ['str:200', 'nn'],
      'title'       => ['txt', 'nn'],
      'content'     => ['bigtxt', 'nn'],
      'block_status'=> ['str:20', 'nn'],
      'created_at'  => ['str:30', 'nn'],
      'modified_at' => ['str:30', 'nn'],
    ]);
  }
  
  public function down(){
    Schema::dropTable('cms_blocks');
  }
}