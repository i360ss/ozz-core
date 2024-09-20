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

trait Forms {

  /**
   * Form tracking
   * @param object $request
   */
  protected function track_form($request) {
    $form = dec_base64($request->query('f'));
    if(!isset($this->cms_forms[$form])) {
      return render_error_page(404, 'Page Not Found');
    }

    set_flash('form_data', $request->input());

    // Form validation
    $validationRules = [];
    foreach ($this->cms_forms[$form]['fields'] as $field) {
      if (isset($field['validate'])) {
        $validationRules[$field['name']] = $field['validate'];
      }
    }
    $validation = Validate::check($request->input(), $validationRules);
    if(!$validation->pass){
      return back();
    }

    // Get form and client info
    $entry = $request->input();
    $entry['__user_info'] = [
      'ip' => $request->ip(),
      'agent' => json_encode($request->userAgent()),
      'geo' => json_encode($request->clientInfo()),
      'name' => $form // Form name
    ];

    // Store entry
    $saved = $this->save_form_entry($entry);

    if ($saved) {
      remove_flash('form_data');
      if (isset($this->cms_forms[$form]['success_message'])) {
        set_error('success', $this->cms_forms[$form]['success_message']);
      }
    } else {
      if (isset($this->cms_forms[$form]['error_message'])) {
        set_error('error', $this->cms_forms[$form]['error_message']);
      }
      return back();
    }

    // Return callback
    if(isset($this->cms_forms[$form]['callback'])){
      $this_entry = get_entry($saved);
      return call_user_func_array($this->cms_forms[$form]['callback'], [$this_entry, $request]);
    } else {
      return back();
    }
  }


  /**
   * Save form data
   * @param array $entry Tracked form entry
   */
  protected function save_form_entry($entry) {
    $userInfo = $entry['__user_info'];
    $name = $entry['f'];
    unset($entry['__user_info'], $entry['f'], $entry['csrf_token'], $entry['submit']);

    // Handle files
    $org_form = $this->cms_forms[$userInfo['name']];
    foreach ($entry as $key => $value) {
      foreach ($org_form['fields'] as $field) {
        if ($field['name'] == $key && $field['type'] == 'file') {
          $file_settings = isset($field['settings']) ? $field['settings'] : []; // Upload file settings
          $uploads = File::upload($value, $file_settings);
          foreach ($uploads as $upload) {
            if ($upload['error']) {
              set_error('error', $upload['message']);
              set_error($key, $upload['message']);
              return back();
            }
            unset($upload['error'], $upload['message']);
            $entry[$key] = $upload;
          }
        }
      }
    }

    $created = $this->DB()->insert('cms_forms', [
      'name' => $userInfo['name'], // Form name
      'content' => json_encode($entry),
      'ip' => $userInfo['ip'],
      'user_id' => auth_id() ?? null,
      'user_agent' => $userInfo['agent'],
      'geo_info' => $userInfo['geo'],
      'status' => 1,
      'created' => time()
    ]);

    // Log for notification
    ozz_log_save('ozz_notification', [
      'type' => 'form_entry',
      'key' => $userInfo['name'],
      'item_id' => $this->DB()->id()
    ]);

    return $created ? $this->DB()->id() : false;
  }


  /**
   * Count total form entries
   * @param array $where Conditions
   */
  protected function count_form_entries($where=[]) {
    if(!empty($where)){
      $count = $this->DB()->count('cms_forms', $where);
    } else {
      $count = $this->DB()->count('cms_forms');
    }

    return $count;
  }


  /**
   * Get form entries
   * @param string $form Form name
   * @param array $where
   * @param int $page Page number
   * @param int $per_page Items per page
   */
  protected function get_form_entries($form, $where=[], $page=1, $per_page=10) {
    $whr = array_merge([
      'ORDER' => ['id' => 'DESC'],
      'LIMIT' => [$page-1, $per_page],
      'name' => $form
    ], $where);

    $entries = $this->DB()->select('cms_forms', '*', $whr);
    foreach ($entries as $key => $entry) {
      $entries[$key]['fields'] = json_decode($entry['content'], true);
      $entries[$key]['fields']['created'] = ozz_format_date($entry['created']);
      unset($entries[$key]['content']);
    }

    return $entries;
  }


  /**
   * Get Single form entry
   * @param integer $id Entry ID
   */
  protected function get_form_entry($id) {
    $entry = $this->DB()->get('cms_forms', '*', ['id' => $id]);
    $entry['fields'] = json_decode($entry['content'], true);

    // Get set user if available
    if ( isset($entry['user_id']) && !empty($entry['user_id']) ) {
      $entry['user'] = Auth::getUser( $entry['user_id'] );
      unset($entry['user']['password']);
      unset($entry['user']['activation_key']);
    } else {
      $entry['user'] = false;
    }

    unset($entry['content']);

    return $entry;
  }


  /**
   * Update Form entry status
   * @param $id Entry ID
   * @param $new_status
   */
  protected function update_form_entry_status($id, $new_status) {
    $update = $this->DB()->update('cms_forms', ['status' => $new_status], ['id' => $id]);

    return $update ? true : false;
  }


  /**
   * Delete Form entry
   * @param $id Entry ID
   */
  protected function delete_form_entry($id) {
    $delete = $this->DB()->delete('cms_forms', ['id' => $id]);

    return $delete ? true : false;
  }

}