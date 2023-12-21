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

  public $data=[];
  public $cms_config;
  public $cms_post_types;
  public $post_config=[];
  public $cms_blocks;
  public $cms_media;
  public $post_type = null;
  public $post_validate = [];
  public static $instance;

  // Default Media manager Config
  public $media_default = [
    'pagination_items_per_page' => 30,
    'validation' => ['20M', 'jpg|png|jpeg|svg|webp|mp4|mp3|ogg|pdf|json']
  ];

  // Default post labels
  public $post_labels = [
    'create_button' => 'Create',
    'update_button' => 'Save',
    'delete_button' => 'Delete',
  ];

  // Default post fields
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

    // Initialize CMS Configurations
    $this->cms_config = require __DIR__.SPC_BACK['core'].'app/cms-config.php';
    $this->cms_config['languages'] = array_merge(['en' => 'English'], isset($this->cms_config['languages']) ? $this->cms_config['languages'] : []);
    $this->cms_config['media'] = array_merge($this->media_default, isset($this->cms_config['media']) ? $this->cms_config['media'] : []);
    $this->cms_media = $this->cms_config['media'];

    // Modify post types
    $this->cms_post_types = $this->modify_cms_post_types($this->cms_config['post_types']);

    // All post types
    $this->cms_config['post_types'] = $this->cms_post_types;

    // Modify Block
    $this->cms_blocks = $this->modify_cms_blocks($this->cms_config['blocks']);

    // if post type available
    if(isset($request->urlParam()['post_type'])){
      $this->post_type = $request->urlParam('post_type');
      $this->post_config = $this->cms_post_types[$this->post_type];

      // set Post validation rules
      $post_only_validation_fields = ozz_i_get_nested_validations(array_merge($this->post_default_fields, $this->post_config['form']['fields']));
      $this->post_validate = array_merge($this->post_validate, $post_only_validation_fields);

      // Merge labels
      if(isset($this->post_config['labels'])){
        $this->post_labels = array_merge($this->post_labels, $this->post_config['labels']);
      }

      // Set post settings to Data
      $this->data = array_merge($this->data, [
        'post_type' => $this->post_type,
        'post_config' => $this->post_config,
      ]);
    }

    // Set Data values
    $this->data = array_merge($this->data, [
      'languages' => $this->cms_config['languages'],
      'blocks' => $this->cms_blocks,
      'post_types' => $this->cms_post_types,
      'media' => $this->cms_media,
    ]);
  }


  /**
   * Single Request instance
   */
  public static function getInstance() {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }


  /**
   * Modify CMS post types
   * @param array $post_types
   */
  private function modify_cms_post_types($post_types) {
    foreach ($post_types as $key => $type) {
      if(isset($type['fields'])){
        $post_types[$key]['form']['fields'] = $type['fields'];
        unset($post_types[$key]['fields']);
      } elseif(isset($type['form'])){
        $post_types[$key]['form'] = array_merge(['fields' => []], $type['form']);
      } else {
        $post_types[$key]['form'] = ['fields' => []];
      }

      if(!isset($type['label'])){
        $post_types[$key]['label'] = ucfirst($key);
      }
      if(!isset($type['note'])){
        $post_types[$key]['note'] = '';
      }

      // Modify post type field names
      $post_types[$key]['form']['fields'] = ozz_i_modify_field_names($post_types[$key]['form']['fields']);

      // Post name validation
      if(strpos($key, '_') !== false){
        Err::custom([
          'msg' => 'Post names should not contain underscores ( _ )',
          'info' => 'Post type ( '.$key.' ) has underscore/s',
          'note' => 'Please replace it with a hyphen or text'
        ]);
      }
    }

    return $post_types;
  }


  /**
   * Modify CMS Blocks
   * @param array $blocks
   */
  private function modify_cms_blocks($blocks) {
    foreach ($blocks as $key => $block) {
      if(strpos($block['name'], '_') !== false){
        Err::custom([
          'msg' => 'Block names should not contain underscores ( _ )',
          'info' => 'Block ( '.$block['name'].' ) has underscore/s',
          'note' => 'Please replace it with a hyphen or text'
        ]);
      }

      $block['form']['csrf'] = false;
      $block['form']['id'] = $block['name'];
      $block['form']['field_options']['wrapper'] = '<div class="block-editor-field">##</div>';

      $block_prefix = 'block_'.$block['name'].'_';

      // Update field names
      $block['form']['fields'] = ozz_i_modify_field_names($block['form']['fields']); 
      foreach ($block['form']['fields'] as $ky => $field) {
        $new_name = $block_prefix.$field['name'];
        $block['form']['fields'][$ky]['name'] = $new_name;
      }

      // Add nested fields into validation
      $this->post_validate[] = ozz_i_get_nested_validations($block['form']['fields']);
      foreach ($this->post_validate as $k => $value) {
        if(is_array($value)){
          if(!empty($value)){
            $this->post_validate = array_merge($this->post_validate, $value);
          }
          unset($this->post_validate[$k]);
        }
      }

      $blocks[$key] = $block;
    }

    return $blocks;
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
        if(isset($value['type']) && in_array($value['type'], ['files', 'media'])){
          $field_label = isset($form['fields'][$key]['button_label']) ? $form['fields'][$key]['button_label'] : 'Select File';
          $field_value = isset($form['fields'][$key]['value']) ? $form['fields'][$key]['value'] : '';

          $form['fields'][$key]['type'] = 'html';
          $form['fields'][$key]['html'] = '<div class="ozz-fm__media-selector">
          <span class="button mini" data-fieldName="'.$value['name'].'">'.$field_label.'</span>
          <input type="hidden" name="'.$value['name'].'" id="'.$value['name'].'" value="'.$field_value.'" />
          </div>';
        }
      }
    }

    return $form;
  }


  /**
   * Filter Form data and organize as an array (Blog and Post content)
   * @param array $form_data
   */
  protected function cms_filter_form_data($form_data) {
    // Block Data
    $block_data = array_filter($form_data, function($key) {
      return preg_match('/^'.preg_quote('i-').'\d+_block_/', $key);
    }, ARRAY_FILTER_USE_KEY);

    // Post Content Data
    $post_data = array_diff_key($form_data, $block_data);

    return [
      'block' => $this->cms_organize_block_content($block_data),
      'post' => $this->cms_organize_post_content($post_data)
    ];
  }

}