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
      ];
      $post = $empty_post;
    }

    // Content available in following languages
    $lang_availability = $this->DB()->select('cms_posts', ['lang'], ['post_id' => $post['post_id']]);
    $langs = [];
    foreach ($lang_availability as $key => $value) {
      $langs[$value['lang']] = lang_name($value['lang']);
    }
    $post['translated_to'] = $langs;

    // Get and setup post taxonomies
    if(isset($post['id'])){
      $post = array_merge($post, $this->setup_taxonomy_field_values( $post['id'] ));
    }

    // Decode HTML entities
    foreach ($post as $k => $v) {
      $ignore = ['content', 'blocks', 'translated_to', 'post_id', 'post_type', 'lang', 'post_status', 'published_at', 'created_at', 'modified_at'];
      if (!in_array($k, $ignore)) {
        if (is_array($v)) {
          array_walk_recursive($v, function (&$value) { $value = html_decode($value); });
          $post[$k] = $v;
        } else {
          $post[$k] = html_decode($v);
        }
      }
    }

    // Decode HTML entities in content JSON
    $decoded_content = is_string($post['content']) ? json_decode($post['content'], true) : $post['content'];
    array_walk_recursive($decoded_content, function (&$value) { $value = html_decode($value); });
    $post['content'] = json_encode($decoded_content);

    // Decode HTML entities in blocks JSON
    $decoded_blocks = is_string($post['blocks']) ? json_decode($post['blocks'], true) : $post['blocks'];
    array_walk_recursive($decoded_blocks, function (&$value) { $value = html_decode($value); });
    $post['blocks'] = json_encode($decoded_blocks);

    return $post;
  }


  /**
   * Store Post in DB
   * @param array $form_data Data to store
   */
  protected function cms_store_post($form_data) {
    // set taxonomy fields flash data
    if(isset($form_data['___taxonomy___']) && !empty($form_data['___taxonomy___'])){
      foreach ($form_data['___taxonomy___'] as $key => $taxonomy) {
        $form_data['___taxonomy___['.$key.']'] = json_decode( htmlspecialchars_decode($taxonomy), true );
      }
    }

    // Store form flash data
    set_flash('form_data', $form_data);
    set_error('error', 'Error on updating your post');

    if(Validate::check($form_data, $this->post_validate)->pass){
      $title = $form_data['title'];
      $slug = $form_data['slug'];
      $status = isset($form_data['post_status']) ? $form_data['post_status'] : 'draft';
      $post_id = $form_data['post_id'];
      $published_at = strtotime($form_data['published_at']);
      $taxonomies = $form_data['___taxonomy___'] ?? [];
      $author = $form_data['author'];

      // Prevent these fields from adding into content
      unset(
        $form_data['csrf_token'],
        $form_data['post_status'],
        $form_data['title'],
        $form_data['slug'],
        $form_data['submit_post'],
        $form_data['post_id'],
        $form_data['published_at'],
        $form_data['author'],
      );

      // Prevent Taxonomy fields from storing inside the content
      foreach ($form_data as $key => $value) {
        if (strpos($key, '___taxonomy___') === 0) {
          unset($form_data[$key]);
        }
      }

      // Filtered data (Block and Post content)
      $filtered_data = $this->cms_filter_form_data($form_data);
      $block_data = json_encode($filtered_data['block']);
      $post_content = json_encode($filtered_data['post']);

      // Save the post
      $post_created = $this->DB()->insert('cms_posts', [
        'lang' => APP_LANG,
        'post_type' => $this->post_type,
        'post_id' => $post_id,
        'author' => $author,
        'title' => $title,
        'slug' => $slug,
        'post_status' => $status,
        'content' => $post_content,
        'blocks' => $block_data,
        'published_at' => $published_at,
        'created_at' => time(),
        'modified_at' => time(),
      ]);

      $post_ai_id = $this->DB()->id();

      // Get and store taxonomies
      if (count($taxonomies) > 0) {
        foreach ($taxonomies as $taxonomy) {
          if ($taxonomy !== '') {
            $data = json_decode( htmlspecialchars_decode($taxonomy), true );
            if (!empty($data)) {
              $data = !isset($data['taxonomy']) ? $data[0] : $data;
              $this->link_post_term($post_ai_id, $data['taxonomy'], $data['terms']);
            }
          }
        }
      }

      if($post_created){
        remove_flash('form_data');
        remove_error('error');
        set_error('success', 'Post created successfully!');
      }
    }

    return redirect(ADMIN_PATH.'/posts/edit/'.$this->post_type.'/'.$post_id);
  }


  /**
   * Update post
   * @param int|string $post_id (id)
   * @param array $form_data Data to store
   */
  protected function cms_update_post($post_id, $form_data) {
    // set taxonomy fields flash data
    if(isset($form_data['___taxonomy___']) && !empty($form_data['___taxonomy___'])){
      foreach ($form_data['___taxonomy___'] as $key => $taxonomy) {
        $form_data['___taxonomy___['.$key.']'] = json_decode( htmlspecialchars_decode($taxonomy), true );
      }
    }

    // Store form flash data
    set_flash('form_data', $form_data);
    set_error('error', 'Error on updating your post');

    if(Validate::check($form_data, $this->post_validate)->pass){
      $title = $form_data['title'];
      $slug = $form_data['slug'];
      $status = isset($form_data['post_status']) ? $form_data['post_status'] : 'draft';
      $published_at = strtotime($form_data['published_at']);
      $taxonomies = $form_data['___taxonomy___'] ?? [];
      $author = $form_data['author'];

      // Clear post terms
      $this->clear_post_terms($post_id);

      // Get and store taxonomies
      if (count($taxonomies) > 0) {
        foreach ($taxonomies as $taxonomy) {
          if ($taxonomy !== '') {
            $data = json_decode( htmlspecialchars_decode($taxonomy), true );
            if (!empty($data)) {
              $data = !isset($data['taxonomy']) ? $data[0] : $data;
              $this->link_post_term($post_id, $data['taxonomy'], $data['terms']);
            }
          }
        }
      }

      // Prevent these fields from adding into content
      unset(
        $form_data['csrf_token'],
        $form_data['post_status'],
        $form_data['title'],
        $form_data['slug'],
        $form_data['published_at'],
        $form_data['submit_post'],
        $form_data['author']
      );

      // Prevent Taxonomy fields from storing inside the content
      foreach ($form_data as $key => $value) {
        if (strpos($key, '___taxonomy___') === 0) {
          unset($form_data[$key]);
        }
      }

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
        'author' => $author,
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
        $form['action'] = ADMIN_PATH.'/posts/create/'.$this->post_type;

        // Post ID (post_id) for translation
        $form['fields'][] = [
          'name' => 'post_id',
          'type' => 'hidden',
          'value' => isset($values['post_id']) ? $values['post_id'] : random_str(4, 0).time(),
          'wrapper' => false
        ];
      } else {
        $form['action'] = ADMIN_PATH.'/posts/update/'.$this->post_type.'/'.$values['id'];
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

    // Wrap Post Core Fields and Tabs
    $core_start = [
      'html' => '<div class="post-edit-view__widget core">',
      'wrapper' => false
    ];
    $core_end = [
      'html' => '</div>',
      'wrapper' => false
    ];

    // Setup Tabs and Tab Menu
    if (isset($this->cms_post_types[$post_type]['tabs'])) {
      $tab_menu = '<div class="post-edit-view__tab-menu"><a href="#default"><span class="button light mini default">Default</span></a>';
      foreach ($this->cms_post_types[$post_type]['tabs'] as $key => $tab) {
        $tab_menu .= '<a href="#'.$tab['slug'].'"><span class="button light mini '.$tab['slug'].'">'.$tab['label'].'</span></a>';
      }
      $tab_menu .= '</div>';

      // Wrap Tab menu and Default tab
      array_unshift($form['fields'], [
        'html' => $tab_menu.'<div id="tab_id-default" class="post-edit-view__tab default" data-tab-name="default">
          <div class="post-edit-view__tab-content">',
        'wrapper' => false
      ]);

      array_push($form['fields'], [
        'html' => '</div></div>',
        'wrapper' => false
      ]);

      // Setup Tabs
      foreach ($this->cms_post_types[$post_type]['tabs'] as $key => $tab) {
        $tab['field_options'] = [
          'wrapper' => '<div class="'.$form_class.'__field">##</div>'
        ];

        // Tab wrapper start
        array_unshift($tab['fields'], [
          'html' => '<div id="tab_id-'.$tab['slug'].'" class="post-edit-view__tab '.$key.'" data-tab-name="'.$key.'">
            <div class="post-edit-view__tab-content">',
          'wrapper' => false
        ]);

        // Tab wrapper end
        array_push($tab['fields'], [
          'html' => '</div></div>',
          'wrapper' => false
        ]);

        // Add tabs to main form fields
        $form['fields'] = array_merge($form['fields'], $tab['fields']);
      }
    }

    // Wrap Core (All Tabs / Default fields)
    array_unshift($form['fields'], $core_start);
    array_push($form['fields'], $core_end);

    // Set up sidebar
    // Authors
    $author = [
      'name' => 'author',
      'type' => 'select',
      'label' => 'Author',
      'options' => $final_users,
      'selected' => (isset($values['author']) ? $values['author'] : Auth::id()),
      'id' => 'post-author',
    ];

    // Created at
    if(isset($values['published_at']) && $values['published_at'] !== ''){
      $published_date = strpos($values['published_at'], '-') === false ? $values['published_at'] : strtotime($values['published_at']);
      $published_date = ozz_format_date($published_date, 2);
    } else {
      $published_date = ozz_format_date(time(), 2);
    }

    // Post Published at
    $published_at = [
      'name' => 'published_at',
      'type' => 'datetime-local',
      'label' => 'Published at',
      'value' => $published_date,
      'id' => 'post-created-at',
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
    ];

    if($form_type == 'create'){
      $status_checkbox = array_merge($status_checkbox, ['checked' => 'true']);
    }

    // Sidebar wrapper
    $sidebar_wrap_start = [ 'html' => '<div class="post-edit-view__sidebar">', 'wrapper' => false ];
    $sidebar_wrap_end = [ 'html' => '</div>', 'wrapper' => false ];
    $form['fields'][] = $sidebar_wrap_start; // sidebar wrap start

    // Add Additional CMS fields (On Sidebar)
    $sidebar_start = [ 'html' => '<div class="post-edit-view__widget">', 'wrapper' => false ];
    $sidebar_end = [ 'html' => '</div>', 'wrapper' => false ];

    $form['fields'] = array_merge($form['fields'], [ $sidebar_start, $status_checkbox, $published_at, $author, $sidebar_end ]);

    // List down available taxonomy fields of curren post type
    if (isset($this->post_config['taxonomies']) && is_array($this->post_config['taxonomies'])) {
      $form['fields'][] = $sidebar_start;
      $taxonomies = $this->cms_taxonomies;
      $post_id = $values['id'] ?? false;

      foreach ($this->post_config['taxonomies'] as $taxonomy) {
        if(isset($taxonomies[$taxonomy]) && !empty($taxonomies[$taxonomy])) {
          $tx = $taxonomies[$taxonomy];

          // Build taxonomy field
          $field_name = '___taxonomy___['.$taxonomy.']';
          $form['fields'][] = [
            'name' => $field_name,
            'label' => $tx['name'] ?? $tx['slug'],
            'type' => 'multiselect',
            'value' => json_encode($values[$field_name] ?? []),
            'data-taxonomy-id' => $tx['id'],
            'options' => array_combine(array_column($tx['terms'], 'id'), array_column($tx['terms'], 'name'))
          ];
        }
      }
      $form['fields'][] = $sidebar_end;
    }
    $form['fields'][] = $sidebar_wrap_end; // sidebar wrap end

    // Add Submit button
    $form['fields'][] = [
      'name' => 'submit_post',
      'type' => 'button',
      'class' => 'button small ozz-default-save-button green '.$form_type,
      'value' => $form_type == 'create' ? $this->post_labels['create_button'] : $this->post_labels['update_button']
    ];

    return [
      'data' => $form,
      'values' => $values
    ];
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
        'post_status' => $post['post_status'],
        'content' => $post['content'],
        'blocks' => $post['blocks'],
        'published_at' => $post['published_at'],
        'created_at' => time(),
        'modified_at' => time(),
      ]);

      $newPostID = $this->DB()->id();

      // Get all post terms
      $terms = [];
      $post_terms = $this->DB()->select('cms_post_terms', '*', ['post_id' => $post_id]);
      foreach ($post_terms as $k => $post_term) {
        $terms[$k] = [
          'post_id' => (int)$newPostID,
          'taxonomy_id' => $post_term['taxonomy_id'],
          'term_id' => $post_term['term_id']
        ];
      }

      // Insert post taxonomy terms
      $this->link_post_term($terms);

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
      $deleted_post = get_post($post_id, ['post_status' => 'trash']);
      if($this->DB()->delete('cms_posts', ['id' => $post_id])){
        $this->clear_post_terms($post_id); // clear taxonomy terms
        set_error('success', 'Post deleted successfully!');
      } else {
        set_error('error', 'Error on deleting your post!');
      }

      return redirect(ADMIN_PATH.'/posts/'.$deleted_post['post_type']);
    }

    return back();
  }
}