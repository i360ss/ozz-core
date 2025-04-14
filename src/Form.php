<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 */

namespace Ozz\Core;

use Ozz\Core\Request;
use Ozz\Core\CMS;

class Form {

  public static $input_types = [
    "text",
    "password",
    "submit",
    "reset",
    "radio",
    "checkbox",
    "file",
    "image",
    "hidden",
    "date",
    "datetime-local",
    "month",
    "week",
    "time",
    "color",
    "range",
    "search",
    "tel",
    "email",
    "url",
    "number",
  ];

  public static $tag_types = [
    "textarea",
    "button",
    "progress",
    "meter",
  ];

  public static $tag_option_types = [
    "select",
    "datalist",
  ];

  public static $custom_field_types = [
    "rich-text"
  ];

  private static $initial_form;

  /**
   * Start a form
   * @param array $args for arguments
   * @return HTML form Starting DOM
   */
  public static function start($args=[]){
    // Set up form attributes
    $action     = isset($args['action']) ? $args['action'] : '/';
    $method     = isset($args['method']) ? $args['method'] : 'get';
    $form_class = isset($args['class']) ? " class=\"{$args['class']}\"" : '';
    $name       = isset($args['name']) ? " name=\"{$args['name']}\"" : '';
    $id         = isset($args['id']) ? " id=\"{$args['id']}\"" : '';
    $enctype    = isset($args['enctype']) ? " enctype=\"{$args['enctype']}\"" : '';

    $formAttributes = ' action="'.$action.'"';
    $formAttributes .= ' method="'.$method.'"';
    $formAttributes .= $name;
    $formAttributes .= $id;
    $formAttributes .= $form_class;
    $formAttributes .= $enctype;

    if(isset($args['attr']) && !empty($args['attr'])){
      foreach ($args['attr'] as $key => $value) {
        $formAttributes .= " {$key}=\"{$value}\"";
      }
    }

    // Start form HTML
    $form = '<form'.$formAttributes.'>'."\n";

    // CSRF
    $csrf = CSRF_FIELD."\n";
    if(isset($args['csrf']) && $args['csrf'] === false){
      $csrf = '';
    }
    $form .= $csrf;

    return $form;
  }

  /**
   * End Form
   */
  public static function end(){
    return "</form>\n";
  }

  /**
   * Create a complete form using given information
   * @param array $args
   * @param array $values Assign values to fields (field_name as key and value as value)
   */
  public static function create($args, $values=[]){
    $request = Request::getInstance();
    self::$initial_form = $args;

    if(env('app', 'ENABLE_CMS')) {
      $cms = CMS::getInstance();
      $args = $cms->cms_related_form_modifies($args);

      // Add CMS Specific Input fields
      (!in_array('media', self::$input_types)) ? array_push(self::$input_types, 'media') : false;
    }

    // Start form
    $form = self::start($args);

    if(isset($args['fields'])){
      $form .= self::generateFields($args, $values);
    }

    // Close form
    $form .= self::end();

    return $form;
  }

  /**
   * Generate Form Fields
   * @param array $fields
   * @param array $values
   */
  public static function generateFields($base_fields, $values=[], $prefix='') {
    $html = '';
    $fields = isset($base_fields['fields']) ? $base_fields['fields'] : $base_fields;
    $is_cms = env('app', 'ENABLE_CMS');

    foreach ($fields as $field) {
      $name = isset($field['name']) ? $field['name'] : '';
      $type = isset($field['type']) ? $field['type'] : false;
      $label = isset($field['label']) ? $field['label'] : '';

      // Add the prefix for the current field
      $f_name = $prefix . ($is_cms ? $name : preg_replace('/_+/', '_', $name));
      $is_single_repeatable = isset($field['repeat']) && $field['repeat'] === true;

      // Update name if single repeatable field, radio or checkbox
      if($is_single_repeatable || (in_array($type, ['checkbox', 'radio']) && isset($field['options']))){
        $f_name .= '[]';
      }

      $field['name'] = $f_name;

      // Get the value for the current field
      $f_value = (!empty($values) && isset($values[$name])) ? $values[$name] : '';

      // Check and add field errors
      $has_error = isset($field['name']) && has_error( rtrim($field['name'], '[]') );

      // Add global options for fields
      $global_options = isset($base_fields['field_options'])
        ? $base_fields['field_options']
        : (isset(self::$initial_form['field_options']) ? self::$initial_form['field_options'] : false);

      if($global_options){
        // Global field classes
        if(isset($global_options['class'])){
          if(isset($field['class'])){
            $field['class'] .= ' '.$global_options['class'];
          } else {
            $field['class'] = $global_options['class'];
          }
        }

        // Global label classes
        if(isset($global_options['label_class'])){
          if(isset($field['label_class'])){
            $field['label_class'] .= ' '.$global_options['label_class'];
          } else {
            $field['label_class'] = $global_options['label_class'];
          }
        }
      }

      // Add error class if has error
      if($has_error){
        $field['class'] = isset($field['class']) ? $field['class'].' error' : 'error';
      }

      // Assign Value to field
      if($f_value !== ''){
        if(in_array($type, ['radio', 'checkbox'])){
          // Assign value for checkbox and radio (Single value)
          if(isset($field['value']) && is_string($f_value)){
            ($field['value'] == $f_value) ? $field['checked'] = 'true' : false;
          }

          // Multiple selection
          if(isset($field['options']) && is_array($field['options']) && is_array($f_value)){
            $field['value'] = $f_value;
          }

        } elseif(in_array($type, ['select', 'datalist'])){
          // Assign values to Selections
          $field['selected'] = $values[$name];
        } elseif(!isset($field['value'])){
          // Assign values to other fields
          $field['value'] = $values[$name];
        }

        // Show/Embed file after the field (Image, video, audio, doc, ect)
        if(isset($field['view_file']) && $field['view_file'] === true){
          $uploaded_file_DOM = embed_files_to_dom($values[$name]);

          if(isset($field['after'])){
            $field['after'] .= $uploaded_file_DOM;
          } else {
            $field['after'] = $uploaded_file_DOM;
          }
        }
      }

      if(in_array($type, ['repeat', 'repeater', 'repeatable'])){
        if(env('app', 'ENABLE_CMS')) {
          $cms = CMS::getInstance();
          $repeaterFields = $cms->cms_related_form_modifies($field);
        } else {
          $repeaterFields = $field['fields'];
        }

        $repeater_label = isset($field['repeat_label']) ? $field['repeat_label'] : '+ Add New';
        $max_repeat = isset($field['max_repeat']) ? 'data-ozz-repeat-max="'.$field['max_repeat'].'"' : '';
        $repeaterID = random_str(12);

        $wrapper_class = isset($field['wrapper_class']) ? ' '.$field['wrapper_class'] : ''; // Repeater wrapper class
        $html .= '
        <fieldset id="rpt-'.$repeaterID.'" class="ozz-fm__repeat'.$wrapper_class.'" data-ozz-repeat="true" '.$max_repeat.' data-rpt="'.$field['name'].'">
          <div class="ozz-fm__repeat-top">
            <legend class="ozz-fm__repeat-label">'.(isset($field['label']) ? $field['label'] : 'Untitled').'</legend>
            <span class="field_note">'.(isset($field['note']) ? $field['note'] : '').'</span>
          </div>
          <div id="rptw-'.$repeaterID.'" class="ozz-fm__repeat-wrapper">';

        // Get Parent/post level repeater fields
        $ptn = array_flip(preg_grep('/^' . $name . '__\d+__/', array_keys($values)));
        $parent_repeater = ozz_i_convert_str_to_array_1(array_intersect_key($values, $ptn));

        // Common code for creating a repeated field block
        $createRepeatedFieldBlock = function ($i, $repeaterValue, $prefix) use ($repeaterFields, &$html) {
          $html .= '<div id="rptf-' . random_str(18) . '" class="ozz-fm__repeat-fields">';
          $html .= '<div class="ozz-fm__repeat-head">';
          $html .= '<span class="ozz-fm__repeat-number">' . ((int)$i + 1) . '</span>';
          $html .= '<span class="ozz-fm__repeat-remove button micro danger">Delete</span>';
          $html .= '</div>';
          $html .= '<div class="ozz-fm__repeat-body">';
          $html .= self::generateFields($repeaterFields, $repeaterValue, $prefix . $i . '__');
          $html .= '</div></div>';
        };

        // Parent/Post level repeater
        if (!empty($parent_repeater[$name])) {
          foreach ($parent_repeater[$name] as $i => $repeaterValue) {
            $createRepeatedFieldBlock($i, $repeaterValue, $name . '__');
          }
        }
        // Block repeaters
        elseif (!empty($f_value)) {
          foreach ($f_value as $i => $repeaterValue) {
            $createRepeatedFieldBlock($i, $repeaterValue, $f_name . '__');
          }
        }
        // Default
        else {
          $createRepeatedFieldBlock(0, $values, $f_name . '__');
        }

        $html .= '</div>
          <span class="ozz-fm__repeat-add button mini">'.$repeater_label.'</span>
        </fieldset>';

      } else {
        // Generate Field HTML output
        $eachInputDOM = self::input($type, $field, true);
        $thisField = $eachInputDOM['field'];
        $thisLabel = $eachInputDOM['label'];
        $thisNote = $eachInputDOM['note'];

        // Wrap input only (Internal)
        if(isset($field['input_wrapper'])){
          $thisField = str_replace('##', "\n$thisField", $field['input_wrapper']);
        }

        // Set field error if available
        if($has_error && is_string($f_name) && !empty($f_name)){
          $error_str = error(rtrim($f_name, '[]'));
          if(is_array($error_str)){
            $error_msg = '';
            foreach ($error_str as $err) {
              $error_msg .= isset($field['field_error_wrapper']) 
                ? str_replace('##', $err, $field['field_error_wrapper'])
                : '<span class="field-error">'.$err.'</span><br>';
            }
          } else {
            $error_msg = isset($field['field_error_wrapper']) 
              ? str_replace('##', $error_str, $field['field_error_wrapper'])
              : '<span class="field-error">'.$error_str.'</span>';
          }

          $thisField .= $error_msg;
        }

        if(isset($field['html'])){
          $thisField .= $field['html'];
        }

        // Single repeatable field
        if($is_single_repeatable){
          $repeater_label = isset($field['repeat_label']) ? $field['repeat_label'] : '+ Add New';
          $max_repeat = isset($field['max_repeat']) ? 'data-ozz-repeat-max="'.$field['max_repeat'].'"' : '';
          $s_repeaterID = random_str(12);

          // Update 'value' by 'selected' attribute
          isset($field['selected']) ? $field['value'] = $field['selected'] : false;

          if (isset($field['value']) && is_array($field['value'])) {
            // Single repeater with values
            $thisField = '
            <div id="rpt-'.$s_repeaterID.'" class="ozz-fm__repeat single" data-ozz-repeat="true" '.$max_repeat.' data-rpt="'.$field['name'].'">
            <div id="rptw-'.$s_repeaterID.'" class="ozz-fm__repeat-wrapper">';

            foreach ($field['value'] as $i => $r_value) {
              $field['value'] = $r_value;
              $input = self::input($field['type'], $field, true);
              $thisField .= '
              <div id="rptf-'.random_str(18).'" class="ozz-fm__repeat-fields">
                <span class="ozz-fm__repeat-number">'.((int) $i + 1).'</span>
                <div class="ozz-fm__repeat-fields-field">'
                .$input['field'].
                '</div>
                <span class="ozz-fm__repeat-remove button micro danger">Delete</span>
              </div>';
            }

            $thisField .= '</div><span class="ozz-fm__repeat-add button mini">'.$repeater_label.'</span></div>';
          } else {
            // Single repeater without values
            $thisField = '
            <div id="rpt-'.$s_repeaterID.'" class="ozz-fm__repeat single" data-ozz-repeat="true" '.$max_repeat.' data-rpt="'.$f_name.'">
              <div id="rptw-'.$s_repeaterID.'" class="ozz-fm__repeat-wrapper">
                <div id="rptf-'.random_str(18).'" class="ozz-fm__repeat-fields">
                  <span class="ozz-fm__repeat-number">1</span>
                  <div class="ozz-fm__repeat-fields-field">'.$thisField.'</div>
                  <span class="ozz-fm__repeat-remove button micro danger">Delete</span>
                </div>
              </div>
              <span class="ozz-fm__repeat-add button mini">'.$repeater_label.'</span>
            </div>';
          }
        }

        // Wrap input and label
        $formInnerDOM = '';

        // Add an element Before field
        if(isset($field['before'])){
          $formInnerDOM .= $field['before'];
        }

        if(isset($field['wrapper']) && $field['wrapper'] !== false){
          $formInnerDOM .= str_replace('##', "\n".$thisLabel.$thisNote.$thisField."\n", $field['wrapper'])."\n";
        } else {
          $formInnerDOM .= $thisLabel.$thisNote.$thisField;
        }

        // Add an element After field
        if(isset($field['after'])){
          $formInnerDOM .= $field['after'];
        }

        // Global each element wrapper
        if($global_options !== false){
          // Wrapper Class
          if (isset($global_options['wrapper']) && (!isset($field['wrapper']) || $field['wrapper'] !== false)) {
            if(isset($field['wrapper_class'])){
              if (strpos($global_options['wrapper'], 'class=') !== false) {
                $global_options['wrapper'] = preg_replace('/class="/', 'class="' . $field['wrapper_class'] . ' ', $global_options['wrapper'], 1);
              } else {
                $global_options['wrapper'] = preg_replace('/<div/', '<div class="'.$field['wrapper_class'].'"', $global_options['wrapper'], 1);
              }
            }

            // Add field type to wrapper class
            if ($type) {
              $global_options['wrapper'] = preg_replace('/class="/', 'class="ozz-fm__'.$type.'-wrap ', $global_options['wrapper'], 1);
            }

            $formInnerDOM = str_replace('##', "\n".$formInnerDOM."\n", $global_options['wrapper'])."\n";
          }
        }

        $html .= $formInnerDOM;
      }
    }

    return $html;
  }

  /**
   * Default input field generator
   * @param string $type
   * @param array $args Field settings
   * @param boolean $field_label_spr Separate field and label and return as array if true
   */
  public static function input($type, $args, $field_label_spr=false){
    // Set input field
    $thisField = '';
    $attrs_only = $args;
    $is_single_repeatable = (isset($args['repeat']) && $args['repeat'] === true) ? true : false;

    // Validation Attrs
    if(isset($args['validate'])) {
      if(isset($args['label']) && (str_contains($args['validate'], 'req') || str_contains($args['validate'], 'required'))) {
        $args['label'] .= '<span class="required-star">*</span>';
      }
    }

    unset(
      $attrs_only['type'],
      $attrs_only['label'],
      $attrs_only['options'],
      $attrs_only['optgroup'],
      $attrs_only['wrapper'],
      $attrs_only['wrapper_class'],
      $attrs_only['input_wrapper'],
      $attrs_only['note'],
      $attrs_only['note_class'],
      $attrs_only['validate'],
      $attrs_only['media_settings'],
      $attrs_only['settings'],
      $attrs_only['before'],
      $attrs_only['after'],
      $attrs_only['selected'],
      $attrs_only['field_error_wrapper'],
      $attrs_only['view_file'],
      $attrs_only['html'],
      $attrs_only['repeat_label'],
      $attrs_only['max_repeat'],
      $attrs_only['repeat'],
    );

    // Label
    $thisLabel = '';
    $labelAttrs = '';
    foreach($args as $key => $val) {
      if(strpos($key, 'label_') === 0){
        $attrKey = str_replace('label_', "", $key);
        $labelAttrs .= " {$attrKey}=\"{$val}\"";
        unset($attrs_only[$key]);
      }
    }

    $labelFor = isset($args['id']) ? " for=\"{$args['id']}\"" : '';
    $thisLabel = isset($args['label']) ? '<label'.$labelFor.$labelAttrs.'>'.$args['label'].'</label>'."\n" : '';

    // Field Note
    $thisNote = '';
    if(isset($args['note'])){
      $note_class = isset($args['note_class']) ? 'field_note '.$args['note_class'] : 'field_note';
      $thisNote = '<span class="'.$note_class.'">'.$args['note'].'</span>';
    }

    // Set up field by input type
    if(in_array($type, self::$input_types)){
      // Input field
      $this_attrs = '';
      foreach ($attrs_only as $ky => $vl) {
        if(!is_array($vl)){
          $vl = is_bool($vl) ? ($vl ? 'true' : 'false') : htmlspecialchars($vl);
          if($ky == 'checked'){
            $this_attrs .= $vl == 'true' ? " checked" : '';
          } else {
            $this_attrs .= " {$ky}=\"{$vl}\"";
          }
        }
      }

      if(in_array($type, ['radio', 'checkbox']) && isset($args['options']) && is_array($args['options'])){
        $has_value = isset($args['value']) ? $args['value'] : false;
        $thisField = '<div class="ozz-fm__'.$type.'-wrapper">';
        $thisField .= '<input type="hidden" name="'.rtrim($args['name'], '[]').'" value="0">';
        foreach ($args['options'] as $key => $val) {
          $c_vl = is_string($key) ? $key : $val;
          $id = rtrim($args['name'], '[]').'-id-'.$key;
          $checked = '';

          if($has_value){
            if (is_array($args['value'])) {
              foreach ($has_value as $vl) {
                if($vl == $c_vl){
                  $checked = 'checked';
                  break;
                }
              }
            } elseif ($has_value == $c_vl) {
              $checked = 'checked';
            }
          }

          if (isset($args['checked']) && $args['checked'] === false) {
            $checked = '';
          }

          $thisField .= "<div class=\"ozz-fm__$type\"><input type=\"$type\"$this_attrs id=\"$id\" $checked value=\"$c_vl\"><label for=\"$id\">$val</label></div>\n";
        }
        $thisField .= '</div>';
      } else {
        $thisField = "<input type=\"$type\"$this_attrs>\n";
      }
    } elseif(in_array($type, self::$tag_types)){
      // Tag input field
      ($type === 'textarea' && !isset($attrs_only['rows'])) ? $attrs_only['rows'] = 5 : false;
      $this_attrs = '';
      foreach ($attrs_only as $ky => $vl) {
        if(!is_array($vl)){
          $vl = is_bool($vl) ? ($vl ? 'true' : 'false') : $vl;
          $this_attrs .= " {$ky}=\"{$vl}\"";
        }
      }

      $thisField = '<'.$type.$this_attrs.'>';
      $thisField .= (isset($args['value']) && is_string($args['value'])) ? $args['value'] : '';
      $thisField .= '</'.$type.'>';
    } elseif(in_array($type, self::$tag_option_types)){
      // Tag option fields
      $optionField = '';
      $this_attrs = '';

      // Selected / Value item (if exist)
      $selected_val = isset($args['selected']) ? $args['selected'] : '';
      $this_value = isset($args['value']) ? $args['value'] : '';

      // Datalist
      if($type == 'datalist'){
        $fld_id = isset($args['id']) ? $args['id'] : $args['name'];

        foreach ($attrs_only as $ky => $vl) {
          if(!is_array($vl)){
            $vl = is_bool($vl) ? ($vl ? 'true' : 'false') : $vl;
            $this_attrs .= ($ky !== 'id') ? " {$ky}=\"{$vl}\"" : '';
          }
        }

        $optionField .= "<input list=\"{$fld_id}\"{$this_attrs} value=\"{$selected_val}\">";
        $optionField .= '<'.$type.' id="'.$fld_id.'"'.$this_attrs.'>'."\n";
      } else {
        foreach ($attrs_only as $ky => $vl) {
          if(!is_array($vl)){
            $vl = is_bool($vl) ? ($vl ? 'true' : 'false') : $vl;
            $this_attrs .= " {$ky}=\"{$vl}\"";
          }
        }
        $optionField .= '<'.$type.$this_attrs.'>'."\n";
      }

      // Default Options
      if (isset($args['options'])) {
        foreach ($args['options'] as $k => $option) {
          $selected = (in_array($selected_val, [$k, $option]) || in_array($this_value, [$k, $option])) ? 'selected' : '';

          if ($type == 'datalist') {
            $optionField .= '<option value="'.$option.'">'.PHP_EOL;
          } else {
            $optionField .= '<option value="'.$k.'" '.$selected.'>'.$option.'</option>'. PHP_EOL;
          }
        }
      }

      // Option group
      if (isset($args['optgroup'])) {
        foreach ($args['optgroup'] as $ky => $val) {
          $optionField .= '<optgroup label="' . $val['label'] . '">' . PHP_EOL;
          foreach ($val['options'] as $k => $option) {
            $selected = (in_array($selected_val, [$k, $option]) || in_array($this_value, [$k, $option])) ? 'selected' : '';
            $optionField .= '<option value="'.$k.'" '.$selected.'>'.$option.'</option>'.PHP_EOL;
          }
          $optionField .= '</optgroup>'.PHP_EOL;
        }
      }

      $optionField .= '</'.$type.'>'."\n";
      $thisField = $optionField;
    } elseif(in_array($type, self::$custom_field_types)){
      // Custom Field Types
      if ($type == 'rich-text') {
        $classes = isset($args['class']) ? $args['class'] : '';
        $rich_txt_val = isset($args['value']) ? '<div data-editor-area>'.html_decode($args['value']).'</div>' : ''; // Rich-text value
        $thisField = '<div data-ozz-wyg data-field-name="'.$args['name'].'" class="'.$classes.'">'.$rich_txt_val.'</div>';
      }
    }

    if($field_label_spr === true){
      return ['label' => $thisLabel, 'field' => $thisField, 'note' => $thisNote];
    }

    return $thisLabel.$thisNote.$thisField;
  }


  /**
   * Return input fields
   */
  public static function text($args=[]){
    return self::input('text', $args);
  }

  public static function email($args=[]){
    return self::input('email', $args);
  }

  public static function password($args=[]){
    return self::input('password', $args);
  }

  public static function select($args=[]){
    return self::input('select', $args);
  }

  public static function checkbox($args=[]){
    return self::input('checkbox', $args);
  }

  public static function radio($args=[]){
    return self::input('radio', $args);
  }

  public static function submit($args=[]){
    return self::input('submit', $args);
  }

  public static function reset($args=[]){
    return self::input('reset', $args);
  }

  public static function file($args=[]){
    return self::input('file', $args);
  }

  public static function image($args=[]){
    return self::input('image', $args);
  }

  public static function hidden($args=[]){
    return self::input('hidden', $args);
  }

  public static function date($args=[]){
    return self::input('date', $args);
  }

  public static function dateTimeLocal($args=[]){
    return self::input('datetime-local', $args);
  }

  public static function month($args=[]){
    return self::input('month', $args);
  }

  public static function week($args=[]){
    return self::input('week', $args);
  }

  public static function time($args=[]){
    return self::input('time', $args);
  }

  public static function color($args=[]){
    return self::input('color', $args);
  }

  public static function range($args=[]){
    return self::input('range', $args);
  }

  public static function search($args=[]){
    return self::input('search', $args);
  }

  public static function tel($args=[]){
    return self::input('tel', $args);
  }

  public static function url($args=[]){
    return self::input('url', $args);
  }

  public static function number($args=[]){
    return self::input('number', $args);
  }

  public static function textarea($args=[]){
    return self::input('textarea', $args);
  }

  public static function button($args=[]){
    return self::input('button', $args);
  }

  public static function progress($args=[]){
    return self::input('progress', $args);
  }

  public static function meter($args=[]){
    return self::input('meter', $args);
  }

  public static function datalist($args=[]){
    return self::input('datalist', $args);
  }


}