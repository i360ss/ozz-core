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
   * Block editor area to inject into Form
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
          foreach ($block['f'] as $ky => $value) {
            $values['i-'.$block['i'].'___block___'.$block['b'].'___'.$ky] = $value;
          }

          // Modify block field names
          // dump($b);
          $new_form = $b['form'];
          foreach ($b['form']['fields'] as $ky => $value) {
            $new_form['fields'][$ky]['name'] = 'i-'.$block['i'].'___'.$value['name'];
          }

          // Generate block's form with values
          $form = Form::create($new_form, $values);
          $form = preg_replace('/<form[^>]*>/', '', $form);
          $form = preg_replace('/<\/form>/', '', $form);

          // Append each single block
          $block_dom .= '<li class="pick-block '.$b['name'].' ozz-used-block" data-blockname="'.$b['name'].'">
            <div class="ozz-block-accordion-bar">
              <span class="ozz-handle"></span>
              <div><h4>'.$b['label'].'</h4><p class="light-text">'.$b['note'].'</p></div>
              <div><span class="ozz-block-delete-trigger"></span></div>
              <span class="ozz-accordion-arrow"></span>
            </div>
            <div class="ozz-accordion-body">'.$form.'</div>
          </li>';
        } else {
          $block_dom .= '<div class="missing-block"><p><strong>Block not found!</strong> <br></p><span class="light-text">This block has removed or renamed from the cms-config.php</span></div>';
        }
      }
    }

    return [
      'name' => '',
      'type' => '',
      'raw_html' => '<label>Block Editor</label><div class="ozz-block-editor" data-blocks="'.htmlspecialchars(json_encode($this->cms_blocks), ENT_QUOTES, 'UTF-8').'">
      <div class="ozz-block-editor__block-picker"></div>
      <div class="ozz-block-editor__form-loader">'.$block_dom.'</div>
      </div>'
    ];
  }


  /**
   * Filter Block data and update validation rules
   */
  protected function cms_filter_block_data($form_data) {
    $block_data = array_filter($form_data, function($key) {
      return preg_match('/^'.preg_quote('i-').'\d+___block___/', $key);
    }, ARRAY_FILTER_USE_KEY);

    foreach ($block_data as $key => $value) {
      $newKey = preg_replace('/^i-\d+___/', '', $key);
      (isset($this->post_validate[$newKey]) && $this->post_validate[$newKey] !== '')
        ? $this->post_validate[$key] = $this->post_validate[$newKey] 
        : false;
    }

    return $this->cms_organize_block_content($block_data);
  }


  /**
   * Organize and set block content to insert
   * @param array $form_data Complete form data
   */
  protected function cms_organize_block_content($block_data) {
    $blocks = [];
    foreach ($block_data as $key => $value) {
      $parts = explode('___', $key);
      $ind = intval(substr($parts[0], 2));
      $blocks[$ind]['i'] = $ind;
      $blocks[$ind]['b'] = $parts[2];
      $blocks[$ind]['f'][$parts[3]] = $value;
    }

    return $blocks;
  }


}