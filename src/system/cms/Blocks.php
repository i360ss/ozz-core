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

trait Blocks {

  /**
   * Generated Form DOM for each block
   */
  protected function cms_block_forms() {
    $block_forms = [];
    foreach ($this->cms_blocks as $block) {
      $block_forms[$block['name']] = Form::create($block['form']);
    }

    return $block_forms;
  }


  /**
   * Build block editor area (Using Block data stored in DB or Flash)
   * @param array $block_data
   */
  protected function cms_block_editor_field($block_data=[]) {
    $blocks = !empty($block_data) ? json_decode($block_data, true) : [];
    $block_dom = '';
    if(!empty($blocks)){
      // Build block forms and assign values
      foreach ($blocks as $key => $block) {
        // Get the correct block type
        $b = [];
        foreach ($this->cms_blocks as $key => $blk) {
          $blk['name'] == $block['b'] ? $b = $blk : false;
        }

        if(!empty($b)) {
          // Modify value keys
          $values = [];
          $bk = 'i-'.$block['i'].'_block_'.$block['b'].'_';
          foreach ($block['f'] as $ky => $value) {
            if(is_array($value) && is_string(key($value))){
              $values[$bk.$ky.'_'.key($value)] = $value[key($value)];
            } else {
              $values[$bk.$ky] = $value;
            }
          }

          // Modify block field names
          $new_form = $b['form'];
          $b_name = 'i-'.$block['i'].'_';
          $rps = ['repeat', 'repeater', 'repeatable'];
          foreach ($new_form['fields'] as $ky => $value) {
            $new_form['fields'][$ky]['name'] = $b_name.$value['name'];

            $is_repeatable = (
              (($new_form['fields'][$ky]['repeat'] ?? false) === true || in_array(($value['type'] ?? ''), $rps)) &&
              isset($new_form['fields'][$ky]['fields'])
            );
          }

          // Generate block's form with values
          $form = Form::create($new_form, $values);
          $form = preg_replace('/<form[^>]*>/', '', $form);
          $form = preg_replace('/<\/form>/', '', $form);

          $is_expand = isset($b['expand']) && $b['expand'] === true;
          $expanded = $is_expand ? ' active' : '';

          // Append each single block
          $note = (isset($b['note']) && $b['note'] !== '') ? "<p class=\"light-text\">{$b['note']}</p>" : '';
          $block_dom .= '<li class="pick-block '.$b['name'].' ozz-used-block" data-blockname="'.$b['name'].'" 
          data-expand="'.($is_expand ? 'true' : 'false').'">
            <div class="ozz-block-accordion-bar'.$expanded.'">
              <span class="ozz-handle"></span>
              <div><h4>'.$b['label'].'</h4>'.$note.'</div>
              <div class="ozz-block-actions">
                <span class="ozz-block-duplicate-trigger"></span>
                <span class="ozz-block-delete-trigger"></span>
              </div>
              <span class="ozz-accordion-arrow"></span>
            </div>
            <div class="ozz-accordion-body'.$expanded.'">'.$form.'</div>
          </li>';
        } else {
          $block_dom .= '<div class="missing-block"><p><strong>Block not found!</strong> <br></p><span class="light-text">This block has removed or renamed from the cms-config.php</span></div>';
        }
      }
    }

    return [
      'html' => '<div class="ozz-block-editor-head">
      <label>Block Editor</label>
      <span class="ozz-block-editor-expand-button" title="Expand Block Editor"></span></div>
      <div class="ozz-block-editor" data-blocks="'.htmlspecialchars(json_encode($this->cms_blocks), ENT_QUOTES, 'UTF-8').'">
      <div class="ozz-block-editor__block-picker">
        <div class="ozz-block-editor__block-picker-head">
          <span title="Two Columns" class="button micro light lay lay2 active" data-lay="lay2">Two Columns</span>
          <span title="One Column" class="button micro light lay lay1" data-lay="lay1">One Column</span>
        </div>
        <div class="ozz-block-editor__block-picker-content"></div>
      </div>
      <div class="ozz-block-editor__form-loader">'.$block_dom.'</div>
      </div>'
    ];
  }


  /**
   * Organize and set block content to insert
   * @param array $form_data Complete form data
   */
  protected function cms_organize_block_content($block_data) {
    $blocks = [];
    $b = ozz_i_convert_str_to_array_1($block_data);
    foreach ($b as $k => $v) {
      $i = intval(substr($k, 2));
      $b_name = array_key_first($v['block']);
      $b_values = $v['block'][$b_name];
      $blocks[$i]['i'] = $i;
      $blocks[$i]['b'] = $b_name;
      $blocks[$i]['f'] = $b_values;
    }

    return $blocks;
  }

}