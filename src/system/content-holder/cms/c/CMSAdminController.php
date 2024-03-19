<?php
namespace Cms\controller;

use Ozz\Core\Request;
use Ozz\Core\CMS;
use Ozz\Core\File;
use Ozz\Core\Validate;
use Ozz\Core\Auth;

class CMSAdminController extends CMS {

  /**
   * This is the Ozz micro CMS Controller
   * You have access to following properties and methods to customize your CMS
   * --
   * # Properties
   * $this->data   ------------------- Data can be used inside the CMS view files
   * $this->cms_config   ------------- Complete output of cms/cms-config.php
   * $this->cms_post_types  ---------- Defined post types in cms/cms-config.php
   * $this->cms_blocks  -------------- Defined blocks in cms/cms-config.php
   * $this->cms_media  --------------- Defined media settings in cms/cms-config.php
   * $this->post_type  --------------- Current post type
   * $this->post_config  ------------- Current post type's settings
   * $this->post_labels  ------------- Post labels
   * --
   * # Methods (CMS specific methods)
   * $this->cms_get_posts() ---------- Returns all posts (params: post type. language) default: current post type
   *                                   and current language
   * $this->cms_get_post_to_edit() --- Return single post (params: post ID (post_id), where:optional (sql where),
   *                                   language:default(Current language))
   * $this->cms_post_form() ---------- Returns the generated form for post create/update (params: form, values)
   *                                   default: current post's form and empty array for values
   * $this->cms_post_count() --------- Return post count (params: By (post_type or post_status), post type
   *                                   default:current post type, Language default: current language )
   * $this->cms_store_post() --------- Store post (params: form_data (the data to be stored))
   * $this->cms_update_post() -------- Update post (params: post ID (id), form_data (new updated data))
   * --
   * # Methods (common methods)
   * $this->get_post() --------------- Return single post (params: Post ID (id) or post slug, SQL where args, Language)
   *                                   1st param is required. Others are optional
   * $this->delete_post()  ----------- Delete post (params: post ID (id), trash (move to trash if true. else delete))
   * $this->change_post_status() ----- Change post statue (params: post ID (id), status (status to be))
   */

  /**
   * Admin dashboard
   */
  public function dashboard() {
    return view('dashboard', $this->data);
  }


  // =============================================
  // Posts
  // =============================================
  /**
   * Post type listing
   */
  public function post_type_listing() {
    $post_count = $this->cms_post_count();
    foreach ($this->cms_post_types as $key => $value) {
      $this->data['post_data'][$key] = [
        'label'       => $value['label'],
        'post_count'  => isset($post_count['posts_by_type'][$key]) ? $post_count['posts_by_type'][$key] : 0,
        'fields'      => count($value['form']['fields']) + count($this->post_default_fields),
        'note'        => $value['note'],
      ];
    }
    $this->data['total_posts'] = $post_count['total_posts'];

    return view('post_type_listing', $this->data);
  }


  /**
   * Post listing
   */
  public function post_listing(Request $request){
    // Filter
    $filter = [];
    if(isset($request->query()['status'])){
      $filter['post_status'] = $request->query('status');
    }

    $this->data['posts'] = $this->cms_get_posts($filter);
    $this->data['post_type_label'] = $this->cms_post_types[$this->post_type]['label'];

    return view('post_listing', $this->data);
  }


  /**
   * Render post create view
   */
  public function post_create_view() {
    $current_values = has_flash('form_data') ? get_flash('form_data') : [];
    $this->data['blocks_forms'] = $this->cms_block_forms();
    $this->data['form'] = $this->cms_post_form('create', null,  $current_values);

    return view('create_post', $this->data);
  }


  /**
   * Render post edit view
   */
  public function post_edit_view(Request $request) {
    $post = $this->cms_get_post_to_edit( $request->urlParam('post_id') );
    $current_values = has_flash('form_data') ? array_merge($post, get_flash('form_data')) : $post;
    $this->data['form_data'] = $current_values;
    $this->data['blocks_forms'] = $this->cms_block_forms();

    // Create new post in current language if not already exist
    if(isset($post['id'])){
      $current_values['id'] = $post['id'];
      $this->data['form'] = $this->cms_post_form('edit', null, $current_values);
    } else {
      $this->data['form'] = $this->cms_post_form('create', null, $current_values);
    }

    return view('edit_post', $this->data);
  }


  /**
   * Create a new post
   */
  public function post_create(Request $request) {
    $this->cms_store_post($request->input());
  }


  /**
   * Update existing post
   */
  public function post_update(Request $request) {
    $this->cms_update_post($request->urlParam('post_id'), $request->input());
  }


  /**
   * Change post status
   */
  public function post_change_status(Request $request) {
    $this->change_post_status($request->input('post_id'), $request->input('status'));
  }


  /**
   * Delete a post
   */
  public function post_delete(Request $request) {
    $trash = $request->input('delete_type') == 'delete' ? false : true;
    $this->delete_post($request->input('post_id'), $trash);
  }


  /**
   * Duplicate post
   */
  public function post_duplicate(Request $request) {
    if(isset($request->input()['post_id_lang']) && isset($request->input()['language'])){
      $this->cms_duplicate_post(
        $request->input('post_id'),
        $request->input('post_id_lang'),
        $request->input('language')
      );
    } else {
      $this->cms_duplicate_post($request->input('post_id'));
    }
  }


  // =============================================
  // Blocks
  // =============================================
  public function blocks_listing() {
    return view('block_listing', $this->data);
  }


  /**
   * Single Block
   */
  public function block(Request $request) {
    $block_id = $request->urlParam('id');
    $this->data['block'] = isset($this->data['blocks'][$block_id]) ? $this->data['blocks'][$block_id] : [];

    return view('block', $this->data);
  }


  // =============================================
  // Taxonomy
  // =============================================
  public function taxonomy_listing() {
    return view('taxonomy_listing', $this->data);
  }


  /**
   * Single Taxonomy
   */
  public function taxonomy(Request $request) {
    $taxonomy = $request->urlParam('slug');
    $this->data['taxonomy'] = $this->get_taxonomy($taxonomy);

    return view('taxonomy', $this->data);
  }


  /**
   * Taxonomy Create view
   */
  public function taxonomy_create_view() {
    return view('create_taxonomy', $this->data);
  }


  /**
   * Taxonomy Edit view
   */
  public function taxonomy_edit_view(Request $request) {
    $this->data['edit_data'] = $this->get_taxonomy($request->urlParam('id'));

    return view('create_taxonomy', $this->data);
  }


  /**
   * Create New Taxonomy
   */
  public function taxonomy_create(Request $request) {
    set_flash('form_data', $request->input());
    $validation = Validate::check($request->input(), [
      'name' => 'req',
      'slug' => 'req',
    ]);

    if ($validation->pass) {
      $this->create_taxonomy($request->input());
      remove_flash('form_data');
    }

    return back();
  }


  /**
   * Update Taxonomy
   */
  public function taxonomy_update(Request $request) {
    set_flash('form_data', $request->input());
    $validation = Validate::check($request->input(), [
      'name' => 'req',
      'slug' => 'req',
    ]);

    if ($validation->pass) {
      $this->update_taxonomy($request->input(), $request->input('taxonomy_id'));
      remove_flash('form_data');
    }

    return back();
  }


  /**
   * Delete Taxonomy
   */
  public function taxonomy_delete(Request $request) {
    if($this->delete_taxonomy($request->content('taxonomyID'))){
      return json([ 'status' => 'success', 'message' => trans('deleted_success') ]);
    }
    return json([ 'status' => 'error', 'message' => trans_e('error') ]);
  }


  /**
   * Create Taxonomy Term
   */
  public function taxonomy_create_term(Request $request) {
    $validation = Validate::check($request->input(), [
      'name' => 'req | text',
      'slug' => 'req | text',
      'taxonomy_id' => 'req | number',
    ]);

    if ($validation->pass) {
      $form_data = [
        'taxonomy_id' => $request->input('taxonomy_id'),
        'name' => $request->input('name'),
        'slug' => $request->input('slug'),
      ];
      $this->create_term($form_data);
    }

    return back();
  }


  /**
   * Update Term
   */
  public function taxonomy_update_term(Request $request) {
    $data = [
      'name' => $request->content('name'),
      'slug' => $request->content('slug')
    ];

    if($this->update_term($request->content('termID'), $data)){
      return json([ 'status' => 'success', 'message' => trans('updated_success') ]);
    }

    return json([ 'status' => 'error', 'message' => trans_e('error') ]);
  }


  /**
   * Delete Term
   */
  public function taxonomy_delete_term(Request $request) {
    if($this->delete_term($request->content('termID'))){
      return json([ 'status' => 'success', 'message' => trans('deleted_success') ]);
    }
    return json([ 'status' => 'error', 'message' => trans_e('error') ]);
  }


  // =============================================
  // Settings
  // =============================================
  public function settings() {
    return view('settings', $this->data);
  }


  /**
   * Change Password
   */
  public function change_password(Request $request) {
    $form_data = $request->input();
    set_flash('form_data', $form_data);

    $validate = Validate::check($request->input(), [
      'current-pass' => 'req',
      'new-pass' => 'req | strong_password',
      'confirm-pass' => 'req | match:{new-pass}',
    ]);

    if($validate->pass){
      if(Auth::verifyPassword($request->input('current-pass'))) {
        if(Auth::changePassword(auth_info('id'), $request->input('new-pass'))){
          remove_flash('form_data');
        }
      } else {
        set_error('current-pass', trans_e('match'));
      }
    }

    return back();
  }


  /**
   * Change User Information
   */
  public function change_info(Request $request) {
    set_flash('form_data', $request->input());
    $validate = Validate::check($request->input(), [
      'first_name' => 'req',
      'last-name' => 'req',
      'email' => 'req | email',
    ]);

    if($validate->pass){
      $update_user = Auth::update([
        'first_name' => esx($request->input('first-name')),
        'last_name' => esx($request->input('last-name')),
        'email' => esx($request->input('email')),
      ]);

      if($update_user){
        clear_flash('form_data');
        set_error('success', trans('profile_update_success'));
      }
    }

    return back();
  }


  // =============================================
  // Media
  // =============================================
  public function media_manager(Request $request) {
    if(!is_dir(UPLOAD_TO.$request->query('dir', ''))){
      return redirect('/admin/media');
    }

    $media_items = $this->media_get_items(
      $request->query('dir', ''),
      $request->query('p', 1)
    );

    $this->data['media_directory_tree'] = $media_items['tree'];
    $this->data['media_items'] = $media_items['items'];

    return view('media', $this->data);
  }


  /**
   * Return media elements
   * @param string $directory
   * @param integer $page_number Pagination page number
   */
  public function media_get_items($directory, $page_number) {
    $directory = esc_url($directory);
    $items = get_directory_content(UPLOAD_TO.$directory);
    $media_data['tree'] = $directory !== '' ? explode('/', $directory) : [];

    // Media items Pagination
    $media_data['items'] = array_pagination(
      $items,
      $this->cms_media['pagination_items_per_page'],
      $page_number
    );

    // Include file info
    $modified = [];
    foreach ($media_data['items']['data'] as $key => $item) {
      if(!is_array($item)) {
        $url = clear_multi_slashes(UPLOAD_DIR_PUBLIC.$directory.'/'.$item);
        $modified[$key] = [
          'name' => esx($item),
          'dir' => $directory.'/',
          'type' => 'file',
          'size' => format_size_units(filesize($url)),
          'format' => get_file_type_by_url($url),
          'url' => esc_url($url),
          'absolute_url' => BASE_URL.$url,
          'created' => date('M d, Y | h:i a', filectime($url)),
          'modified' => date('M d, Y | h:i a', filemtime($url)),
          'access' => date('M d, Y | h:i a', fileatime($url)),
        ];
      } else {
        $key = trim($key, '/');
        $modified[$key] = [
          'name' => $key,
          'type' => 'folder',
          'url' => $directory ? $directory.'/'.$key : $key,
        ];
      }
    }
    $media_data['items']['data'] = $modified;

    return $media_data;
  }


  /**
   * Get Media items as JSON
   */
  public function media_get_items_json(Request $request) {
    if(!is_dir(UPLOAD_TO.$request->query('dir', ''))){
      exit(404);
    }

    $media_items = $this->media_get_items(
      $request->query('dir', ''),
      $request->query('p', 1)
    );

    return json($media_items);
  }


  /**
   * Media Manager Actions
   */
  public function media_action(Request $request) {
    $action = $request->query('q');
    $current_dir = esc_url($request->input('ozz_media_current_directory')).DS;
    $base_dir = UPLOAD_TO . $current_dir;

    $validation = Validate::check($request->input(), [
      'ozz_media_folder_name' => 'req|no-space|max:30',
      'ozz_media_file_name' => 'req|no-space|max:50',
      'ozz_media_upload_file' => 'req'
    ]);

    if ($validation->pass) {
      match ($action) {
        'create_folder' => File::create_dir($base_dir, $request->input('ozz_media_folder_name')),
        'create_file' => File::create($base_dir, $request->input('ozz_media_file_name')),
        'delete_file' => File::delete(clear_multi_slashes($base_dir.$request->input('ozz_media_file_name_delete'))),
        'delete_dir' => File::delete($base_dir),
        'upload_file' => File::upload(
          $request->file('ozz_media_upload_file'),
          [
            'dir' => $current_dir
          ],
          $this->cms_media['validation']
        ),
        default => render_error_page(404, 'Page Not Found')
      };
    }

    return back();
  }

}