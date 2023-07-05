<?php
/**
 * Ozz micro framework
 * Author: Shakir
 * Contact: shakeerwahid@gmail.com
 */

namespace Ozz\Core;

use Ozz\Core\Request;

class Form {

  public static $inputTypes = [
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

  public static $tagTypes = [
    "textarea",
    "button",
    "progress",
    "meter",
  ];

  public static $tagOptionTypes = [
    "select",
    "datalist",
  ];

  /**
   * Start a form
   * @param array $args for arguments
   * @return HTML form Starting DOM
   */
  public static function start($args=[]){
    // Set up form attributes
    $action     = isset($args['action']) ? $args['action'] : '/';
    $method     = isset($args['method']) ? $args['method'] : 'get';
    $form_class = isset($args['class']) ? " class=\"${args['class']}\"" : '';
    $name       = isset($args['name']) ? " name=\"${args['name']}\"" : '';
    $id         = isset($args['id']) ? " id=\"${args['id']}\"" : '';
    $enctype    = isset($args['enctype']) ? " enctype=\"${args['enctype']}\"" : '';

    $formAttributes = ' action="'.$action.'"';
    $formAttributes .= ' method="'.$method.'"';
    $formAttributes .= $name;
    $formAttributes .= $id;
    $formAttributes .= $form_class;
    $formAttributes .= $enctype;

    if(isset($args['attr']) && !empty($args['attr'])){
      foreach ($args['attr'] as $key => $value) {
        $formAttributes .= " ${key}=\"${value}\"";
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

    // Start form
    $form = self::start($args);

    if(isset($args['fields'])){
      foreach ($args['fields'] as $key => $fld_val) {
        $has_error = has_error($fld_val['name']);

        // Add global field each class
        if(isset($args['field_options']['class'])){
          if(isset($fld_val['class'])){
            $fld_val['class'] = $fld_val['class'].' '.$args['field_options']['class'];
          } else {
            $fld_val['class'] = $args['field_options']['class'];
          }
        }

        // Add global label each class
        if(isset($args['field_options']['label_class'])){
          if(isset($fld_val['label_class'])){
            $fld_val['label_class'] = $fld_val['label_class'].' '.$args['field_options']['label_class'];
          } else {
            $fld_val['label_class'] = $args['field_options']['label_class'];
          }
        }

        // Add error class if has error
        if($has_error){
          $fld_val['class'] = isset($fld_val['class']) ? $fld_val['class'].' error' : 'error';
        }

        // Assign value if provided as second argument
        if(!empty($values) && isset($values[$fld_val['name']])){
          if(in_array($fld_val['type'], ['radio', 'checkbox'])){
            // Assign values for checkbox and radio
            if(is_array($values[$fld_val['name']])){
              foreach ($values[$fld_val['name']] as $checked_val) {
                $fld_val['value'] == $checked_val
                  ? $fld_val['checked'] = 'checked'
                  : false;
              }
            } else {
              if($fld_val['value'] == $values[$fld_val['name']]){
                $fld_val['checked'] = 'checked';
              }
            }
          } elseif($fld_val['type'] == 'file'){
            !isset($fld_val['show_image']) ? $fld_val['show_image'] = true : false;

            // Show Images if available
            if($fld_val['show_image'] !== false){
              $uploaded_img = '';
              if(is_array($values[$fld_val['name']])){
                $uploaded_img = '<div class="uploaded-file">';
                foreach ($values[$fld_val['name']] as $img) {
                  $uploaded_img .= '<div class="uploaded-file__single"><img src="'.$img.'" alt="'.$img.'" /></div>';
                }
                $uploaded_img .= '</div>';
              } else {
                $uploaded_img = '<div class="uploaded-file"><img src="'.$values[$fld_val['name']].'" alt="'.$values[$fld_val['name']].'" /></div>';
              }

              if(isset($fld_val['before'])){
                $fld_val['before'] .= $uploaded_img;
              } else {
                $fld_val['before'] = $uploaded_img;
              }
            }
          } elseif(in_array($fld_val['type'], ['select', 'datalist'])){
            // Assign values to Selections
            $fld_val['selected'] = $values[$fld_val['name']];
          } elseif(!isset($fld_val['value'])){
            // Assign values to other fields
            $fld_val['value'] = $values[$fld_val['name']];
          }
        }

        $eachInputDOM = self::input($fld_val['type'], $fld_val, true);
        $thisField = $eachInputDOM['field'];
        $thisLabel = $eachInputDOM['label'];
        $thisNote = $eachInputDOM['note'];

        // Wrap input only (Internal)
        if(isset($fld_val['input_wrapper'])){
          $thisField = str_replace('##', "\n$thisField", $fld_val['input_wrapper']);
        }

        // Set field error if available
        if($has_error && is_string($fld_val['name']) && !empty($fld_val['name'])){
          $error_msg = isset($fld_val['field_error_wrapper']) 
            ? str_replace('##', error($fld_val['name']), $fld_val['field_error_wrapper'])
            : '<span class="field-error">'.error($fld_val['name']).'</span>';
          $thisField .= $error_msg;
        }

        // Wrap input and label
        $formInnerDOM = '';

        // Render Raw HTML
        if(isset($fld_val['raw_html'])){
          $formInnerDOM = $fld_val['raw_html'];
        }

        // Add an element Before field
        if(isset($fld_val['before'])){
          $formInnerDOM .= $fld_val['before'];
        }

        if(isset($fld_val['wrapper']) && $fld_val['wrapper'] !== false){
          $formInnerDOM .= str_replace('##', "\n".$thisLabel.$thisNote.$thisField."\n", $fld_val['wrapper'])."\n";
        } else {
          $formInnerDOM .= $thisLabel.$thisNote.$thisField;
        }

        // Add an element After field
        if(isset($fld_val['after'])){
          $formInnerDOM .= $fld_val['after'];
        }

        // Global each element wrapper
        if(isset($args['field_options'])){
          if(isset($args['field_options']['wrapper'])){
            if(isset($fld_val['wrapper'])){
              if($fld_val['wrapper'] !== false){
                $formInnerDOM = str_replace('##', "\n".$formInnerDOM."\n", $args['field_options']['wrapper'])."\n";
              }
            } else {
              $formInnerDOM = str_replace('##', "\n".$formInnerDOM."\n", $args['field_options']['wrapper'])."\n";
            }
          }
        }

        $form .= $formInnerDOM;
      }
    }

    // Close form
    $form .= self::end();

    return $form;
  }

  /**
   * Default input field generator
   */
  public static function input($type, $args, $field_label_spr=false){
    // Set input field
    $thisField = '';
    $attrs_only = $args;

    // Validation Attrs
    if(isset($args['validate'])) {
      if(str_contains($args['validate'], 'req') || str_contains($args['validate'], 'required')) {
        $args['label'] .= '<span class="required-star">*</span>';
      }
    }

    unset(
      $attrs_only['type'],
      $attrs_only['label'],
      $attrs_only['options'],
      $attrs_only['optgroup'],
      $attrs_only['wrapper'],
      $attrs_only['input_wrapper'],
      $attrs_only['note'],
      $attrs_only['note_class'],
      $attrs_only['validate'],
      $attrs_only['media_settings'],
      $attrs_only['before'],
      $attrs_only['after'],
      $attrs_only['selected'],
      $attrs_only['field_error_wrapper'],
    );

    // Label
    $thisLabel = '';
    $labelAttrs = '';
    foreach($args as $key => $val) {
      if(strpos($key, 'label_') === 0){
        $attrKey = str_replace('label_', "", $key);
        $labelAttrs .= " ${attrKey}=\"${val}\"";
        unset($attrs_only[$key]);
      }
    }

    $labelFor = isset($args['id']) ? " for=\"${args['id']}\"" : '';
    $thisLabel = isset($args['label']) ? '<label'.$labelFor.$labelAttrs.'>'.$args['label'].'</label>'."\n" : '';

    // Field Note
    $thisNote = '';
    if(isset($args['note'])){
      $note_class = isset($args['note_class']) ? 'field_note '.$args['note_class'] : 'field_note';
      $thisNote = '<span class="'.$note_class.'">'.$args['note'].'</span>';
    }

    // Set up field by input type
    if(in_array($type, self::$inputTypes)){
      // Input field
      $this_attrs = '';
      foreach ($attrs_only as $ky => $vl) {
        if(is_string($vl)){
          $this_attrs .= " ${ky}=\"${vl}\"";
        }
      }
      $thisField = '<input type="'.$type.'"'.$this_attrs.'>'."\n";
    } elseif(in_array($type, self::$tagTypes)){
      // Tag input field
      $this_attrs = '';
      foreach ($attrs_only as $ky => $vl) {
        $this_attrs .= " ${ky}=\"${vl}\"";
      }

      $thisField = '<'.$type.$this_attrs.'>';
      $thisField .= $args['value'] ?? '';
      $thisField .= '</'.$type.'>';
    } elseif(in_array($type, self::$tagOptionTypes)){
      // Tag option fields
      $optionField = '';
      $this_attrs = '';

      // Datalist
      if($type == 'datalist'){
        foreach ($attrs_only as $ky => $vl) {
          $this_attrs .= ($ky !== 'id') ? " ${ky}=\"${vl}\"" : '';
        }
        $optionField .= "<input list=\"${args['id']}\"${this_attrs}>";
        $optionField .= '<'.$type.' id="'.$args['id'].'"'.$this_attrs.'>'."\n";
      } else {
        foreach ($attrs_only as $ky => $vl) {
          $this_attrs .= " ${ky}=\"${vl}\"";
        }
        $optionField .= '<'.$type.$this_attrs.'>'."\n";
      }

      if(isset($args['options'])){
        foreach ($args['options'] as $k => $option) {
          $selected = '';
          if(isset($args['selected'])){
            if($args['selected'] == $k || $args['selected'] == $option) {
              $selected = 'selected';
            }
          }

          if($type == 'datalist'){
            $optionField .= '<option value="'.$option.'" '.$selected.'></option>'."\n";
          } else {
            $optionField .= '<option value="'.$k.'" '.$selected.'>'.$option.'</option>'."\n";
          }
        }
      }

      if(isset($args['optgroup'])){
        foreach ($args['optgroup'] as $ky => $val) {
          $optionField .= '<optgroup label="'.$val['label'].'">';
          foreach ($val['options'] as $k => $option) {
            $optionField .= '<option value="'.$k.'">'.$option.'</option>'."\n";
          }
          $optionField .= '</optgroup>';
        }
      }
      $optionField .= '</'.$type.'>'."\n";
      $thisField = $optionField;
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