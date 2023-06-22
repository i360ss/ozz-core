<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\cms;

use Ozz\Core\Cms;
use Ozz\Core\Medoo;
use Ozz\Core\Form;
use Ozz\Core\Auth;
use Ozz\Core\Validate;

trait Posts {

  // ========================================================= //
  // CMS Specific methods
  // ========================================================= //
  /**
   * Post count (return the number of posts)
   * @param string $by Group by columns
   */
  public function cms_post_count($by='post_types', $post_type=null, $lang=APP_LANG) {
    $return = [];
    $post_type = is_null($post_type) ? $this->post_type : $post_type;

    if($by == 'post_types'){
      // Count all post types
      $return = [
        'total_posts' => 0,
        'posts_by_type' => [],
      ];
      $count = $this->DB()->query("SELECT post_type, COUNT(*) AS count FROM cms_posts GROUP BY post_type")->fetchAll();
      foreach ($count as $value) {
        $return['posts_by_type'][$value['post_type']] = $value['count'];
        $return['total_posts'] += $value['count'];
      }
    } elseif($by == 'post_status'){
      // Count current post type by status
      $return = [
        'all' => 0,
        'published' => 0,
        'draft' => 0,
        'trash' => 0,
      ];
      $status_count = $this->DB()->query(
        "SELECT post_status, COUNT(*) AS count
        FROM cms_posts
        WHERE post_type='$post_type' AND lang='$lang'
        GROUP BY post_status"
      )->fetchAll();

      foreach ($status_count as $value) {
        $return[$value['post_status']] = $value['count'];
      }
      $return['all'] = array_sum($return);
    }

    return $return;
  }


  /**
   * Return all Posts under provided post type
   * @param array $where SQL where arguments
   * @param string $post_type
   * @param string $lang Language code
   */
  public function cms_get_posts($where=[], $post_type=null, $lang=APP_LANG) {
    $post_type = is_null($post_type) ? $this->post_type : $post_type;
    $where = array_merge([
      'post_type' => $post_type,
      'lang' => $lang,
    ], $where);

    $return['listing'] = $this->DB()->select('cms_posts', [
      '[>]user' => ['author' => 'user_id']
    ], [
      'cms_posts.id',
      'cms_posts.post_id',
      'cms_posts.title',
      'cms_posts.post_type',
      'cms_posts.slug',
      'cms_posts.lang',
      'cms_posts.post_status',
      'cms_posts.created_at',
      'cms_posts.modified_at',
      'cms_posts.content',
      'cms_posts.tags',
      'user.first_name',
      'user.last_name',
    ], $where);
    $return['count'] = $this->cms_post_count('post_status');

    return $return;
  }


  /**
   * Return single post to edit
   * @param int|string $post_id Post ID (post_id)
   * @param array $where SQL where arguments
   * @param string $lang Language code
   */
  public function cms_get_post_to_edit($post_id, $post_type=null, $where=[], $lang=APP_LANG) {
    $post_type = is_null($post_type) ? $this->post_type : $post_type;
    $where_1 = [
      'post_id' => $post_id,
      'post_type' => $post_type,
      'lang' => $lang,
    ] + $where;

    $post = $this->DB()->get('cms_posts', [
      '[>]user' => ['author' => 'user_id']
    ], [
      'cms_posts.id',
      'cms_posts.post_id',
      'cms_posts.title',
      'cms_posts.post_type',
      'cms_posts.slug',
      'cms_posts.lang',
      'cms_posts.post_status',
      'cms_posts.created_at',
      'cms_posts.modified_at',
      'cms_posts.content',
      'cms_posts.tags',
      'user.first_name',
      'user.last_name',
    ], $where_1);

    if (!is_null($post)) {
      $post = array_merge($post, json_decode($post['content'], true));
    } else {
      $where_2 = [
        'post_id' => $post_id,
        'post_type' => $post_type,
      ] + $where;
      $other_lang_post = $this->DB()->get('cms_posts', ['post_id'], $where_2);
      $empty_post = [
        'post_id' => (string) $other_lang_post['post_id'],
        'post_type' => $this->post_type,
        'post_status' => 'published',
        'lang' => $lang,
        'title' => '',
        'first_name' => false,
        'last_name' => false,
        'created_at' => false,
        'modified_at' => false,
        'content' => [],
        'tags' => '',
      ];
      $post = $empty_post;
    }

    // Content in available following languages
    $lang_availability = $this->DB()->select('cms_posts', ['lang'], ['post_id' => $post['post_id']]);
    $langs = [];
    foreach ($lang_availability as $key => $value) {
      $langs[$value['lang']] = lang_name($value['lang']);
    }
    $post['translated_to'] = $langs;

    return $post;
  }


  /**
   * Store Post in DB
   * @param array $form_data Data to store
   */
  protected function cms_store_post($form_data) {
    set_flash('form_data', $form_data);
    $validate = Validate::check($form_data, $this->post_validate);

    if($validate->pass){
      $title = $form_data['title'];
      $slug = $form_data['slug'];
      $status = isset($form_data['post_status']) ? $form_data['post_status'] : 'draft';
      $post_id = $form_data['post_id'];
      unset($form_data['csrf_token'], $form_data['post_status'], $form_data['title'], $form_data['slug'], $form_data['submit_post'], $form_data['post_id']);

      $post_content = json_encode($form_data);
      $post_created = $this->DB()->insert('cms_posts', [
        'lang' => APP_LANG,
        'post_type' => $this->post_type,
        'post_id' => $post_id,
        'author' => Auth::id(),
        'title' => $title,
        'slug' => $slug,
        'post_status' => $status,
        'content' => $post_content,
        'created_at' => time(),
        'modified_at' => time(),
      ]);

      if($post_created){
        remove_flash('form_data');
        set_error('success', 'Post created successfully!');
      } else {
        set_error('error', 'Error on creating your post');
      }
    }

    return back();
  }


  /**
   * Update post
   * @param int|string $post_id (id)
   * @param array $form_data Data to store
   */
  protected function cms_update_post($post_id, $form_data) {
    set_flash('form_data', $form_data);
    $validate = Validate::check($form_data, $this->post_validate);

    if($validate->pass){
      $title = $form_data['title'];
      $slug = $form_data['slug'];
      $status = isset($form_data['post_status']) ? $form_data['post_status'] : 'draft';
      unset($form_data['csrf_token'], $form_data['post_status'], $form_data['title'], $form_data['slug'], $form_data['submit_post']);

      $post_content = json_encode($form_data);
      $post_updated = $this->DB()->update('cms_posts', [
        'title' => $title,
        'slug' => $slug,
        'post_status' => $status,
        'content' => $post_content,
        'modified_at' => time(),
      ],[
        'id' => $post_id
      ]);

      if($post_updated){
        remove_flash('form_data');
        set_error('success', 'Post updated successfully!');
      } else {
        set_error('error', 'Error on updating your post');
      }
    }

    return back();
  }


  /**
   * Generate and return post form to Create/Update
   * @param string $form_type create/edit
   * @param string $post_type
   * @param array $values Post values if exist
   */
  public function cms_post_form($form_type='create', $post_type=null, $values=[]) {
    $post_type = is_null($post_type) ? $this->post_type : $post_type;
    $form = $this->cms_post_types[$post_type]['form'];

    // Add form class
    $form_class = 'ozz-form';
    $form['class'] = isset($form['class']) ? $form['class'].' '.$form_class : $form_class;

    // Add default fields
    $form['fields'] = array_merge($this->post_default_fields, $form['fields']);

    // Add field wrapper
    if(!isset($form['field_options']['wrapper'])){
      $form['field_options']['wrapper'] = '<div class="'.$form_class.'__field">##</div>';
    }

    if(!isset($form['enctype'])){
      $form['enctype'] = 'multipart/form-data';
    }

    // Add form method
    if(!isset($form['method'])){
      $form['method'] = 'post';
    }

    // Add form action
    if(!isset($form['action'])){
      if($form_type == 'create'){
        $form['action'] = '/admin/posts/create/'.$this->post_type;

        // Post ID (post_id) for translation
        $form['fields'][] = [
          'name' => 'post_id',
          'type' => 'hidden',
          'value' => isset($values['post_id']) ? $values['post_id'] : random_str(4, 0).time(),
          'wrapper' => false
        ];
      } else {
        $form['action'] = '/admin/posts/update/'.$this->post_type.'/'.$values['id'];
      }
    }

    // Add Post status checkbox
    $status_checkbox = [
      'name' => 'post_status',
      'id' => 'post_status',
      'type' => 'checkbox',
      'label' => 'Publish',
      'value' => 'published',
      'class' => 'switch',
      'wrapper' => '<div class="'.$form_class.'__switch-checkbox">##</div>',
      'input_wrapper' => '<span class="'.$form_class.'__checkbox-wrapper">##</span>'
    ];

    if($form_type == 'create'){
      $status_checkbox = array_merge($status_checkbox, ['checked' => 'true']);
    }

    $form['fields'][] = $status_checkbox;

    // Add Submit button
    $form['fields'][] = [
      'name' => 'submit_post',
      'type' => 'submit',
      'class' => 'button',
      'value' => $form_type == 'create' ? $this->post_labels['create_button'] : $this->post_labels['update_button']
    ];

    return Form::create($form, $values);
  }


  // ========================================================= //
  // Common usage methods
  // ========================================================= //
  /**
   * Return single post
   * @param int|string $post_id_slug Post ID (id) or slug
   * @param array $where SQL where arguments
   * @param string $lang Language code
   */
  public function get_post($post_id_slug, $where=[], $lang=APP_LANG) {
    $where = array_merge([
      'OR' => [
        'id' => $post_id_slug,
        'slug' => $post_id_slug
      ],
      'lang' => $lang
    ], $where);

    $post = $this->DB()->get('cms_posts', [
      '[>]user' => ['author' => 'user_id']
    ], [
      'cms_posts.id',
      'cms_posts.post_id',
      'cms_posts.title',
      'cms_posts.post_type',
      'cms_posts.slug',
      'cms_posts.lang',
      'cms_posts.post_status',
      'cms_posts.created_at',
      'cms_posts.modified_at',
      'cms_posts.content',
      'cms_posts.tags',
      'user.first_name',
      'user.last_name',
    ], $where);

    if(!is_null($post)){
      $post = array_merge($post, json_decode($post['content'], true));
    }

    return $post;
  }


  /**
   * Change post status
   * @param int $post_id
   * @param string $status
   */
  protected function change_post_status($post_id, $status) {
    if($this->DB()->update('cms_posts', ['post_status' => $status], ['id' => $post_id])){
      $message = $status == 'published' ? 'Post published successfully' : 'Post moved to draft';
      set_error('success', $message);
    } else {
      set_error('error', 'Error on changing the post status');
    }

    return back();
  }


  /**
   * Delete post
   * @param int $post_id (id)
   * @param boolean $trash Move to trash if true
   */
  protected function delete_post($post_id, $trash=true) {
    if($trash){
      if($this->DB()->update('cms_posts', ['post_status' => 'trash'], ['id' => $post_id])){
        set_error('success', 'Post moved to trash');
      } else {
        set_error('error', 'Error on deleting your post!');
      }
    } else {
      if($this->DB()->delete('cms_posts', ['id' => $post_id])){
        set_error('success', 'Post deleted successfully!');
      } else {
        set_error('error', 'Error on deleting your post!');
      }
    }

    return back();
  }
}