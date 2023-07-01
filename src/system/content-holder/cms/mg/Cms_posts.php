<?php
// Run: [ php ozz -h migration ] to more info
use Ozz\Core\system\migration\Schema;

class Cms_posts {
  
  public function up(){
    Schema::createTable('cms_posts', [
      'id'          => ['bigint', 'ai', 'primary'],
      'post_id'     => ['bigint'],
      'lang'        => ['str:20'],
      'post_type'   => ['str:50', 'nn'],
      'author'      => ['bigint', 'nn'],
      'slug'        => ['str:200', 'nn'],
      'title'       => ['txt', 'nn'],
      'content'     => ['bigtxt', 'nn'],
      'blocks'      => ['bigtxt', 'nn'],
      'tags'        => ['txt', 'nn'],
      'post_status' => ['str:20', 'nn'],
      'created_at'  => ['str:30', 'nn'],
      'modified_at' => ['str:30', 'nn'],
    ]);
  }
  
  public function down(){
    Schema::dropTable('cms_posts');
  }
}