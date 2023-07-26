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

  public $data;
  public $cms_config;
  public $cms_post_types;
  public $cms_post_content_fields = [];
  public $post_config=[];
  public $cms_blocks;
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
      'label' => 'Title',
      'validate' => 'req',
      'class' => 'default-post-title'
    ],
    [
      'name' => 'slug',
      'type' => 'text',
      'label' => 'Slug',
      'validate' => 'req',
      'class' => 'default-post-slug'
    ]
  ];

  public function __construct() {
    $request = Request::getInstance();

    $this->cms_config = require __DIR__.SPC_BACK['core'].'app/cms-config.php';
    $this->cms_config['languages'] = array_merge(['en' => 'English'], isset($this->cms_config['languages'])
      ? $this->cms_config['languages']
      : []);
    $this->cms_post_types = $this->cms_config['post_types'];

    // Config post types to prevent some unwanted errors (This will allow to use 'form' or 'fields' directly)
    foreach ($this->cms_config['post_types'] as $key => $type) {
      if(isset($type['fields'])){
        $this->cms_post_types[$key]['form']['fields'] = $type['fields'];
        unset($this->cms_post_types[$key]['fields']);
      } elseif(isset($type['form'])){
        $this->cms_post_types[$key]['form'] = array_merge(['fields' => []], $type['form']);
      } else {
        $this->cms_post_types[$key]['form'] = ['fields' => []];
      }

      if(!isset($type['label'])){
        $this->cms_post_types[$key]['label'] = ucfirst($key);
      }
      if(!isset($type['note'])){
        $this->cms_post_types[$key]['note'] = '';
      }
    }

    // Modify Block
    foreach ($this->cms_config['blocks'] as $key => $block) {
      $block['form']['csrf'] = false;
      $block['form']['id'] = $block['name'];
      $block['form']['field_options']['wrapper'] = '<div class="block-editor-field">##</div>';

      foreach ($block['form']['fields'] as $ky => $field) {
        $new_name = 'block___'.$block['name'].'___'.$field['name'];
        $block['form']['fields'][$ky]['name'] = $new_name;
        isset($field['validate']) ? $this->post_validate[$new_name] = $field['validate'] : false;
      }

      $this->cms_blocks[$key] = $block;
    }

    $this->data['blocks'] = $this->cms_blocks;
    $this->data['languages'] = $this->cms_config['languages'];
    $this->data['post_types'] = $this->cms_post_types;

    // if post type available
    if(isset($request->urlParam()['post_type'])){
      $this->post_type = $request->urlParam('post_type');
      $this->post_config = $this->cms_post_types[$this->post_type];
      $this->cms_post_content_fields = $this->post_config['form']['fields'];
      $this->data['post_type'] = $this->post_type;
      $this->data['post_config'] = $this->post_config;

      // set Post validation rules
      foreach (array_merge($this->post_default_fields, $this->cms_post_content_fields) as $value) {
        isset($value['validate']) ? $this->post_validate[$value['name']] = $value['validate'] : false;
      }

      // Merge labels
      if(isset($this->post_config['labels'])){
        $this->post_labels = array_merge($this->post_labels, $this->post_config['labels']);
      }
    }
  }


  /**
   * Modify CMS specific items on Form Builder
   * @param array $form The form
   * @return array Modified form
   */
  public function cms_related_form_modifies($form) {
    if(isset($form['fields'])){
      // Change file fields into Media Manager opening trigger
      foreach ($form['fields'] as $key => $value) {
        if(isset($value['type']) && in_array($value['type'], ['file', 'files', 'media'])){
          $form['fields'][$key]['type'] = 'html';
          $form['fields'][$key]['html'] = '<div class="ozz-form__media-selector">
          <span class="button mini" data-fieldName="'.$value['name'].'">Select File</span>
          <input type="hidden" name="'.$value['name'].'" id="'.$value['name'].'" />
          </div>';
        }
      }
    }

    return $form;
  }

}