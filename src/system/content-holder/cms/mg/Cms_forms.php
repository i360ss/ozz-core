<?php
// Run: [ php ozz -h migration ] to more info
use Ozz\Core\system\migration\Schema;

class Cms_forms {
  
  public function up(){
    Schema::createTable('cms_forms', [
      'id'          => ['int', 'ai', 'primary'],
      'name'        => ['str:255'],
      'content'     => ['bigtxt', 'nn'],
      'user_id'     => ['int'],
      'ip'          => ['str:100', 'nn'],
      'user_agent'  => ['txt'],
      'geo_info'    => ['txt'],
      'status'      => ['int'],
      'created'     => ['int'],
      'updated'     => ['int'],
      'update_info' => ['txt'],
    ]);
  }
  
  public function down(){
    Schema::dropTable('cms_forms');
  }
}