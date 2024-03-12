<?php
// Run: [ php ozz -h migration ] to more info
use Ozz\Core\system\migration\Schema;

class Cms_post_terms {
  
  public function up(){
    Schema::createTable('cms_post_terms', [
      'id'          => ['bigint', 'ai', 'primary'],
      'post_id'     => ['int'],
      'taxonomy_id' => ['int'],
      'term_id'     => ['int'],
    ]);
  }
  
  public function down(){
    Schema::dropTable('cms_post_terms');
  }
}