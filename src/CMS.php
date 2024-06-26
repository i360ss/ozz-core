<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core;

class CMS {

  use \Ozz\Core\DB;
  use \Ozz\Core\system\cms\Utilities;
  use \Ozz\Core\system\cms\Posts;
  use \Ozz\Core\system\cms\Blocks;
  use \Ozz\Core\system\cms\Settings;
  use \Ozz\Core\system\cms\Taxonomy;
  use \Ozz\Core\system\cms\Forms;

  public $data=[];
  public $cms_config;
  public $cms_post_types;
  public $post_config=[];
  public $cms_blocks;
  public $cms_taxonomies;
  public $cms_tabs;
  public $cms_forms;
  public $cms_media;
  public $cms_user_meta;
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
    $this->cms_config = require __DIR__.SPC_BACK['core'].'cms/cms-config.php';
    $this->cms_config['languages'] = array_merge(['en' => 'English'], isset($this->cms_config['languages']) ? $this->cms_config['languages'] : []);
    $this->cms_config['media'] = array_merge($this->media_default, isset($this->cms_config['media']) ? $this->cms_config['media'] : []);
    $this->cms_media = $this->cms_config['media'];
    $this->cms_user_meta = $this->cms_config['user_meta'];

    // Modify post types
    $this->cms_post_types = $this->modify_cms_post_types($this->cms_config['post_types']);

    // All post types
    $this->cms_config['post_types'] = $this->cms_post_types;

    // Modify Block
    $this->cms_blocks = $this->modify_cms_blocks($this->cms_config['blocks']);

    // Taxonomies
    $this->cms_taxonomies = $this->get_taxonomies();

    // Post Tabs
    $this->cms_tabs = isset($this->post_config['tabs']) ? $this->post_config['tabs'] : [];

    // Forms
    $this->cms_forms = require __DIR__.SPC_BACK['core'].'cms/cms-forms.php';
    // Modify forms
    foreach ($this->cms_forms as $k => $fm) {
      !isset($fm['entry-status']) ? $this->cms_forms[$k]['entry-status'] = [
        1 => 'None',
        2 => 'Draft',
        3 => 'Spam',
      ] : false;
    }

    // if post type available
    if(isset($request->urlParam()['post_type'])){
      $this->post_type = $request->urlParam('post_type');
      $this->post_config = $this->cms_post_types[$this->post_type];

      // set Post validation rules (Including tab fields)
      $tab_fields = [];
      if (!empty($this->cms_tabs)) {
        foreach ($this->cms_tabs as $key => $tab) {
          foreach ($tab['fields'] as $field) {
            $tab_fields[] = $field;
          }
        }
      }

      $all_fields = array_merge($this->post_config['form']['fields'], $tab_fields);
      $post_only_validation_fields = ozz_i_get_nested_validations( array_merge($this->post_default_fields, $all_fields) );
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
      'taxonomies' => $this->cms_taxonomies,
      'post_types' => $this->cms_post_types,
      'forms' => $this->cms_forms,
      'media' => $this->cms_media,
      'user_meta' => $this->cms_user_meta,
      'js_data' => [],
      'notify' => $this->get_new_items_notification()
    ]);

    // update Config with CMS config
    defined('CMS_CONFIG') || define('CMS_CONFIG', array_merge(CONFIG, $this->cms_config['CONFIG'] ?? []));
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
      if(strpos($key, '__') !== false){
        Err::custom([
          'msg' => 'Post names should not contain multiple underscores one after another ( __ )',
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
      $block['name'] = preg_replace('/_+/', '_', $block['name']);
      $block['form']['csrf'] = false;
      $block['form']['id'] = $block['name'];
      $block['form']['field_options']['wrapper'] = '<div class="block-editor-field">##</div>';

      $block_prefix = 'block__'.$block['name'].'__';

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
        if(isset($value['type'])) {
          // Media element Field
          if(in_array($value['type'], ['files', 'media'])){
            $button_label = isset($form['fields'][$key]['button_label']) ? $form['fields'][$key]['button_label'] : 'Select File';

            // Select multiple items
            $multiple = '';
            if(isset($value['multiple']) && $value['multiple'] === true){
              $multiple = 'data-multiple="true"';
            }

            // Embed Value DOM
            $form['fields'][$key] = array_merge($value, [
              'type' => 'hidden',
              'id' => $value['name'],
            ]);

            $form['fields'][$key]['wrapper'] = '<div class="ozz-fm__media-selector">##
              <span class="button small media-selector-trigger" id="trigger_'.random_str(5).'"
                data-field-name="'.$value['name'].'" '.$multiple.'>'.$button_label.
              '</span><div class="ozz-fm__media-embed-wrapper"></div></div>';
          }

          // Multi selection field
          if(in_array($value['type'], ['multi-select', 'multiselect'])){
            $valuesDOM = '';
            foreach ($value['options'] as $ky => $option) {
              $valuesDOM .= '<li data-value="'.$ky.'">'.$option.'</li>';
            }

            $form['fields'][$key]['wrapper'] = '<div class="ozz-fm__multiselect">##<ul>'.$valuesDOM.'</ul>
            <div class="ozz-fm__multiselect--selected"></div></div>';
            $form['fields'][$key]['type'] = 'hidden';
          }
        }
      }
    }

    return $form;
  }


  /**
   * Filter Form data and organize as an array (Block and Post content)
   * @param array $form_data
   */
  protected function cms_filter_form_data($form_data) {
    // Block Data
    $block_data = array_filter($form_data, function($key) {
      return preg_match('/^'.preg_quote('i-').'\d+__block__/', $key);
    }, ARRAY_FILTER_USE_KEY);

    // Post Content Data
    $post_data = array_diff_key($form_data, $block_data);

    return [
      'block' => $this->cms_organize_block_content($block_data),
      'post' => $this->cms_organize_post_content($post_data)
    ];
  }

}