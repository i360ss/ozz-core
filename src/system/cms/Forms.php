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
use Ozz\Core\Medoo;

trait Forms {

  /**
   * Form tracking - Create new entry
   * @param string $form Form name
   * @param object $request Request object
   */
  protected function create_form_entry($form, $request) {
    $this->tracking_validation($form, $request->input()); // Validate form

    // Get form and client info
    $entry = $request->input();
    $entry['__user_info'] = [
      'ip' => $request->ip(),
      'agent' => json_encode($request->userAgent()),
      'name' => $form // Form name
    ];

    // Store entry
    $saved = $this->create_form_entry_in_action($entry);

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
    if(isset($this->cms_forms[$form]['callback']) && $this->cms_forms[$form]['callback'] !== false){
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
  protected function create_form_entry_in_action($entry) {
    $userInfo = $entry['__user_info'];
    $name = $entry['f'];
    unset($entry['__user_info'], $entry['f'], $entry['csrf_token'], $entry['submit']);

    // Handle files
    $org_form = $this->cms_forms[$userInfo['name']];
    foreach ($entry as $key => $value) {
      foreach ($org_form['fields'] as $field) {
        if ($field['name'] == $key && $field['type'] == 'file') {
          if ($value['tmp_name'] !== '') {
            $file_settings = isset($field['settings']) ? $field['settings'] : []; // Upload file settings
            $validation = $file_settings['validation'] ?? $file_settings['validate'] ?? null; // max size and allowed formats
            $uploads = File::upload($value, $file_settings, $validation);
            foreach ($uploads as $upload) {
              if ($upload['error']) {
                set_error($key, $upload['message']);
                return back();
              }
              unset($upload['error'], $upload['message']);
              $entry[$key] = $upload;
            }
          } else {
            $entry[$key] = '';
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
   * Update Form entry
   * @param string $form Form name
   * @param int $id Entry ID
   * @param array $request Request object
   * @param boolean $check_user Check entry owner and current user ID
   */
  protected function update_form_entry($form, $id, $request, $check_user=true) {
    $this->tracking_validation($form, $request->input()); // Validate form

    $current_entry = get_entry($id);
    $entry = $request->input();
    $entry['id'] = !is_numeric($entry['id']) ? dec_base64($entry['id']) : $entry['id'];
    unset($entry['f'], $entry['csrf_token'], $entry['submit']);

    // Handle files
    $org_form = $this->cms_forms[$form];
    foreach ($entry as $key => $value) {
      foreach ($org_form['fields'] as $field) {
        if ($field['name'] == $key && $field['type'] == 'file') {
          if ($value['tmp_name'] !== '') {
            $file_settings = isset($field['settings']) ? $field['settings'] : []; // Upload file settings
            $validation = $file_settings['validation'] ?? $file_settings['validate'] ?? null; // max size and allowed formats
            $uploads = File::upload($value, $file_settings, $validation);
            foreach ($uploads as $upload) {
              if ($upload['error']) {
                set_error($key, $upload['message']);
                return back();
              }
              unset($upload['error'], $upload['message']);
              $entry[$key] = $upload;
            }
          } else {
            $entry[$key] = $current_entry['fields'][$key];
          }
        }
      }
    }

    $where = ['id' => $id];
    if ($check_user) {
      if (!is_logged_in()) {
        return false;
      }
      $where = array_merge($where, ['user_id' => auth_id()]);
    }

    // Updated from (IP, user agent and Geo information)
    $update_info = [
      'ip' => $request->ip(),
      'agent' => json_encode($request->userAgent()),
    ];
    $fields = [
      'content' => json_encode($entry),
      'updated' => time(),
      'update_info' => json_encode($update_info)
    ];
    $updated = $this->DB()->update('cms_forms', $fields, $where);

    if ($updated) {
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
    if(isset($this->cms_forms[$form]['callback']) && $this->cms_forms[$form]['callback'] !== false){
      $this_entry = get_entry($id);
      return call_user_func_array($this->cms_forms[$form]['callback'], [$this_entry, $request]);
    } else {
      return back();
    }
  }


  /**
   * Common tracking form validation
   * @param string $form Decoded form name
   * @param object $form_data
   */
  private function tracking_validation($form, $form_data) {
    if(!isset($this->cms_forms[$form])) {
      return render_error_page(404);
    }

    set_flash('form_data', $form_data);

    // Form validation
    $validationRules = [];
    foreach ($this->cms_forms[$form]['fields'] as $field) {
      if (isset($field['validate'])) {
        $validationRules[$field['name']] = $field['validate'];
      }
    }
    $validation = Validate::check($form_data, $validationRules);
    if(!$validation->pass){
      return back();
    }
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
  protected function get_form_entries(string $form, array $where = [], array $options = []): array {
    $defaultOptions = [
      'page' => 1,
      'per_page' => 10,
      'order_by' => 'id',
      'order_dir' => 'DESC'
    ];

    $options = array_merge($defaultOptions, $options);
    $defaultFields = ['name', 'user_id', 'id', 'ip', 'user_agent', 'status', 'created', 'updated', 'update_info', 'fields'];

    $conditions = ["name = '{$form}'"];

    // Handle filtering conditions
    foreach ($where as $key => $value) {
      $escapedValue = addslashes($value); // Prevent SQL injection
      if (in_array($key, $defaultFields)) {
        $conditions[] = "{$key} = '{$escapedValue}'";
      } else {
        $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(content, '$.$key')) = '{$escapedValue}'";
      }
    }

    // Build WHERE clause
    $whereSql = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

    // Validate ORDER BY field
    $orderBy = in_array($options['order_by'], $defaultFields) ? $options['order_by'] : 'id';
    $orderDir = strtoupper($options['order_dir']) === 'ASC' ? 'ASC' : 'DESC';

    // LIMIT and OFFSET
    $offset = ($options['page'] - 1) * $options['per_page'];

    // Build raw SQL query (WITHOUT SELECT)
    $query = Medoo::raw("
      {$whereSql}
      ORDER BY {$orderBy} {$orderDir}
      LIMIT {$offset}, {$options['per_page']}
    ");

    // Execute query with Medoo
    $entries = $this->DB()->select('cms_forms', '*', $query);

    // Process and return results
    return array_map(function ($entry) {
      $entry['fields'] = json_decode($entry['content'], true);
      $entry['fields']['created'] = ozz_format_date($entry['created']);
      unset($entry['content']);
      return $entry;
    }, $entries);
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
   * @param int $id Entry ID
   * @param string $new_status
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