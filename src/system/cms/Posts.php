<?php
/**
* Ozz micro framework
* Author: Shakir
* Contact: shakeerwahid@gmail.com
*/

namespace Ozz\Core\system\cms;

use Ozz\Core\Form;
use Ozz\Core\Validate;
use Ozz\Core\Auth;
use Ozz\Core\File;

trait Posts {

  // ========================================================= //
  // CMS Specific methods
  // ========================================================= //
  /**
   * Post count (return the number of posts)
   * @param string $by Group by columns
   */
  protected function cms_post_count($by='post_types', $post_type=null, $lang=APP_LANG) {
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
  protected function cms_get_posts($where=[], $post_type=null, $lang=APP_LANG) {
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
      'cms_posts.published_at',
      'cms_posts.created_at',
      'cms_posts.modified_at',
      'cms_posts.content',
      'cms_posts.blocks',
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
  protected function cms_get_post_to_edit($post_id, $post_type=null, $where=[], $lang=APP_LANG) {
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
      'cms_posts.published_at',
      'cms_posts.created_at',
      'cms_posts.modified_at',
      'cms_posts.content',
      'cms_posts.blocks',
      'cms_posts.tags',
      'user.first_name',
      'user.last_name',
    ], $where_1);

    if (!is_null($post)) {
      $post = array_merge($post, is_array(json_decode($post['content'], true)) ? json_decode($post['content'], true) : []);
    } else {
      $where_2 = [
        'post_id' => $post_id,
        'post_type' => $post_type,
      ] + $where;
      $other_lang_post = $this->DB()->get('cms_posts', ['post_id'], $where_2);
      $other_language_post_is = isset($other_lang_post) ? (string) $other_lang_post['post_id'] : false;
      $empty_post = [
        'post_id' => $other_language_post_is,
        'post_type' => $this->post_type,
        'post_status' => 'published',
        'lang' => $lang,
        'title' => '',
        'first_name' => false,
        'last_name' => false,
        'published_at' => false,
        'created_at' => false,
        'modified_at' => false,
        'content' => [],
        'blocks' => [],
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
    set_error('error', 'Error on creating your post');

    if(Validate::check($form_data, $this->post_validate)->pass){
      $title = $form_data['title'];
      $slug = $form_data['slug'];
      $status = isset($form_data['post_status']) ? $form_data['post_status'] : 'draft';
      $post_id = $form_data['post_id'];
      $published_at = strtotime($form_data['published_at']);

      // Prevent these fields from adding into content
      unset(
        $form_data['csrf_token'],
        $form_data['post_status'],
        $form_data['title'],
        $form_data['slug'],
        $form_data['submit_post'],
        $form_data['post_id'],
        $form_data['published_at'],
      );

      // Filtered data (Block and Post content)
      $filtered_data = $this->cms_filter_form_data($form_data);
      $block_data = json_encode($filtered_data['block']);
      $post_content = json_encode($filtered_data['post']);

      // Save the post
      $post_created = $this->DB()->insert('cms_posts', [
        'lang' => APP_LANG,
        'post_type' => $this->post_type,
        'post_id' => $post_id,
        'author' => Auth::id(),
        'title' => $title,
        'slug' => $slug,
        'post_status' => $status,
        'content' => $post_content,
        'blocks' => $block_data,
        'published_at' => $published_at,
        'created_at' => time(),
        'modified_at' => time(),
      ]);

      if($post_created){
        remove_flash('form_data');
        remove_error('error');
        set_error('success', 'Post created successfully!');
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
    set_error('error', 'Error on updating your post');

    if(Validate::check($form_data, $this->post_validate)->pass){
      $title = $form_data['title'];
      $slug = $form_data['slug'];
      $status = isset($form_data['post_status']) ? $form_data['post_status'] : 'draft';
      $published_at = strtotime($form_data['published_at']);

      // Prevent these fields from adding into content
      unset(
        $form_data['csrf_token'],
        $form_data['post_status'],
        $form_data['title'],
        $form_data['slug'],
        $form_data['published_at'],
        $form_data['submit_post']
      );

      // Filtered data (Block and Post content)
      $filtered_data = $this->cms_filter_form_data($form_data);
      $block_data = json_encode($filtered_data['block']);
      $post_content = json_encode($filtered_data['post']);

      // Update the post
      $post_updated = $this->DB()->update('cms_posts', [
        'title' => $title,
        'slug' => $slug,
        'post_status' => $status,
        'content' => $post_content,
        'blocks' => $block_data,
        'published_at' => $published_at,
        'modified_at' => time(),
      ],[
        'id' => $post_id
      ]);

      if($post_updated){
        remove_flash('form_data');
        remove_error('error');
        set_error('success', 'Post updated successfully!');
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
  protected function cms_post_form($form_type='create', $post_type=null, $values=[]) {
    $post_type = is_null($post_type) ? $this->post_type : $post_type;
    $form = $this->cms_post_types[$post_type]['form'];

    // Add form class
    $form_class = 'ozz-fm';
    $form['class'] = isset($form['class']) ? $form['class'].' '.$form_class : $form_class;
    $form['class'] .= ' '.$form_type.'-fm';

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

    // If flash blocks available
    if(has_flash('form_data')){
      $flash_blocks = json_encode($this->cms_filter_form_data(get_flash('form_data'))['block']);
      $form['fields'][] = $this->cms_block_editor_field($flash_blocks);
    } else {
      $form['fields'][] = isset($values['blocks']) ? $this->cms_block_editor_field($values['blocks']) : $this->cms_block_editor_field();
    }

    // Users
    $users = Auth::allUsers(['user_id', 'first_name', 'last_name']);
    $final_users = [];
    foreach ($users as $user) {
      $final_users[$user['user_id']] = $user['first_name'].' '.$user['last_name'];
    }

    // Authors
    $author = [
      'name' => 'author',
      'type' => 'select',
      'label' => 'Author',
      'options' => $final_users,
      'selected' => (isset($values['author']) ? $values['author'] : Auth::id()),
      'id' => 'post-author',
      'wrapper_class' => 'cl cl-4',
    ];

    // Created at
    if(isset($values['published_at']) && $values['published_at'] !== ''){
      $published_date = strpos($values['published_at'], '-') === false ? $values['published_at'] : strtotime($values['published_at']);
      $published_date = ozz_format_date($published_date, 2);
    } else {
      $published_date = ozz_format_date(time(), 2);
    }

    $published_at = [
      'name' => 'published_at',
      'type' => 'datetime-local',
      'label' => 'Published At',
      'value' => $published_date,
      'id' => 'post-created-at',
      'wrapper_class' => 'cl cl-4',
    ];

    // Add Post status checkbox
    $status_checkbox = [
      'name' => 'post_status',
      'id' => 'post_status',
      'type' => 'checkbox',
      'label' => 'Publish',
      'value' => 'published',
      'class' => 'switch',
      'wrapper' => '<div class="'.$form_class.'__switch-checkbox">##</div>',
      'input_wrapper' => '<span class="'.$form_class.'__checkbox-wrapper">##</span>',
      'wrapper_class' => 'cl cl-4',
    ];

    if($form_type == 'create'){
      $status_checkbox = array_merge($status_checkbox, ['checked' => 'true']);
    }

    // Add Additional CMS fields
    $form['fields'] = array_merge($form['fields'], [ $author, $published_at, $status_checkbox ]);

    // Add Submit button
    $form['fields'][] = [
      'name' => 'submit_post',
      'type' => 'button',
      'class' => 'button small ozz-default-save-button green '.$form_type,
      'value' => $form_type == 'create' ? $this->post_labels['create_button'] : $this->post_labels['update_button']
    ];

    return Form::create($form, $values);
  }


  /**
   * Duplicate Post
   * @param string $post_id Post ID (id)
   * @param string $post_lang_id Language ID (post_id)
   * @param string $translation_to Language
   */
  public function cms_duplicate_post($post_id, $post_lang_id=null, $translation_to=null) {
    $post = $this->DB()->get('cms_posts', [
      'lang',
      'post_type',
      'slug',
      'title',
      'content',
      'blocks',
      'tags',
      'post_status',
      'published_at'
    ], [
      'id' => $post_id
    ]);

    if(!is_null($post)){
      if(isset($post_lang_id)){
        $check = $this->DB()->select('cms_posts', ['id'], [
          'lang' => isset($translation_to) ? $translation_to : $post['lang'],
          'post_id' => $post_lang_id
        ]);

        if(!is_null($check) && count($check) > 0) {
          // Already have a translation
          $lang_name = lang_name($translation_to);
          set_error('success', 'Translated version already available for '.$lang_name.'. Click <a href="/lang/'.$translation_to.'" class="link"><strong>Here</strong></a> to edit');
          return back();
        }
      }

      $post_created = $this->DB()->insert('cms_posts', [
        'lang' => isset($translation_to) ? $translation_to : $post['lang'],
        'post_type' => $post['post_type'],
        'post_id' => isset($post_lang_id) ? $post_lang_id : random_str(4, 0).time(),
        'author' => Auth::id(),
        'title' => $post['title'].' (Copy)',
        'slug' => $post['slug'].'-copy',
        'tags' => $post['tags'],
        'post_status' => $post['post_status'],
        'content' => $post['content'],
        'blocks' => $post['blocks'],
        'published_at' => $post['published_at'],
        'created_at' => time(),
        'modified_at' => time(),
      ]);

      if($post_created){
        remove_flash('form_data');
        $duplicated_lang = isset($translation_to) ? 'into '.lang_name($translation_to) : '';
        set_error('success', 'Post duplicated '.$duplicated_lang.' successfully!');
        if(isset($translation_to)) {
          switch_language($translation_to);
        }
      } else {
        set_error('error', 'Error on duplicating the post');
      }
    }

    return back();
  }


  /**
   * Organize post content
   * @param array $data Post data (except block data)
   */
  public function cms_organize_post_content($data) {
    return ozz_i_convert_str_to_array_1($data);
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
  protected function get_post($post_id_slug, $where=[], $lang=APP_LANG) {
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
      'cms_posts.published_at',
      'cms_posts.modified_at',
      'cms_posts.content',
      'cms_posts.blocks',
      'cms_posts.tags',
      'user.first_name',
      'user.last_name',
    ], $where);

    if(!is_null($post)){
      $post = array_merge($post, is_array(json_decode($post['content'], true)) ? json_decode($post['content'], true) : []);
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