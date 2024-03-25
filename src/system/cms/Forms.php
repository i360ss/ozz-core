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
    $this->save_form_entry($entry);
    remove_flash('form_data');

    // Return callback
    if(isset($this->cms_forms[$form]['callback'])){
      return call_user_func_array($this->cms_forms[$form]['callback'], [$request]);
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

    $created = $this->DB()->insert('cms_forms', [
      'name' => $userInfo['name'], // Form name
      'content' => json_encode($entry),
      'ip' => $userInfo['ip'],
      'user_agent' => $userInfo['agent'],
      'geo_info' => $userInfo['geo'],
      'status' => 1,
      'created' => time()
    ]);

    return $created ? true : false;
  }


  /**
   * Get form entries
   * @param string $form Form name
   */
  protected function get_form_entries($form) {
    $entries = $this->DB()->select('cms_forms', '*', ['name' => $form]);
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