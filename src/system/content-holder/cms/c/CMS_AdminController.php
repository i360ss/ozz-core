<?php
namespace App\controller\admin;

use Ozz\Core\Request;
use Ozz\Core\CMS;

class CMS_AdminController extends CMS {

  /**
   * This is the Ozz micro CMS Controller
   * You have access to following properties and methods to customize your CMS
   * 
   * # Properties
   * $this->data   ------------------- Data can be used inside the CMS view files
   * $this->cms_config   ------------- Complete output of app/cms-config.php
   * $this->cms_post_types  ---------- Defined post types in app/cms-config.php
   * $this->cms_blocks  -------------- Defined blocks in app/cms-config.php
   * $this->post_type  --------------- Current post type
   * $this->post_config  ------------- Current post type's settings
   * $this->post_labels  ------------- Post labels
   * 
   * # Methods (CMS specific methods)
   * $this->cms_get_posts() ---------- Returns all posts (params: post type. language) default: current post type and current language
   * $this->cms_get_post_to_edit() --- Return single post (params: post ID (post_id), where:optional (sql where), language:default(Current language))
   * $this->cms_post_form() ---------- Returns the generated form for post create/update (params: form, values) default: current post's form and empty array for values
   * $this->cms_post_count() --------- Return post count (params: By (post_type or post_status), post type default:current post type, Language default: current language )
   * $this->cms_store_post() --------- Store post (params: form_data (the data to be stored))
   * $this->cms_update_post() -------- Update post (params: post ID (id), form_data (new updated data))
   * 
   * # Methods (common methods)
   * $this->get_post() --------------- Return single post (params: Post ID (id) or post slug, SQL where args, Language) 1st param is required. Others are optional
   * $this->delete_post()  ----------- Delete post (params: post ID (id), trash (move to trash if true. else delete))
   * $this->change_post_status() ----- Change post statue (params: post ID (id), status (status to be))
   */

  /**
   * Admin dashboard
   */
  public function dashboard() {
    return view('admin/dashboard', $this->data);
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

    return view('admin/posts', $this->data);
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

    return view('admin/post_listing', $this->data);
  }


  /**
   * Render post create view
   */
  public function post_create_view() {
    $current_values = has_flash('form_data') ? get_flash('form_data') : [];
    $this->data['blocks_forms'] = $this->cms_block_forms();
    $this->data['form'] = $this->cms_post_form('create', null,  $current_values);

    return view('admin/create_post', $this->data);
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

    return view('admin/edit_post', $this->data);
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


  // =============================================
  // Blocks
  // =============================================
  public function blocks_listing() {
    return view('admin/blocks', $this->data);
  }


}