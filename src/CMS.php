<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class CMS {

  use \Ozz\Core\DB;
  use \Ozz\Core\system\cms\Posts;
  use \Ozz\Core\system\cms\Blocks;
  use \Ozz\Core\system\cms\Settings;

  public $cms_config;
  public $cms_post_types;
  public $post_config=[];
  public $cms_blocks;
  public $data = [];
  public $post_type = null;
  public $post_validate = [];
  public $post_labels = [
    'create_button' => 'Create',
    'update_button' => 'Save',
    'delete_button' => 'Delete',
  ];

  public $post_default_fields = [
    [
      'name' => 'title',
      'type' => 'text',
      'label' => 'Post Title',
      'class' => 'default-post-title'
    ],
    [
      'name' => 'slug',
      'type' => 'text',
      'label' => 'Post Slug',
      'class' => 'default-post-slug'
    ]
  ];

  public function __construct() {
    $request = Request::getInstance();

    $this->cms_config = require __DIR__.SPC_BACK['core'].'app/cms-config.php';
    $this->cms_post_types = $this->cms_config['post_types'];
    $this->cms_blocks = $this->cms_config['blocks'];
    $this->data['languages'] = array_merge(['en' => 'English'], $this->cms_config['languages']);
    $this->data['post_types'] = $this->cms_post_types;

    if(isset($request->urlParam()['post_type'])){
      $this->post_type = $request->urlParam('post_type');
      $this->post_config = $this->cms_post_types[$this->post_type];
      $this->data['post_type'] = $this->post_type;
      $this->data['post_config'] = $this->post_config;

      // set Post validation rules
      foreach ($this->post_config['form']['fields'] as $value) {
        isset($value['validate']) ? $this->post_validate[$value['name']] = $value['validate'] : false;
      }

      // Merge labels
      if(isset($this->post_config['labels'])){
        $this->post_labels = array_merge($this->post_labels, $this->post_config['labels']);
      }
    }
  }

}