<?php

# ----------------------------------------------------
// HTML DOM support
# ----------------------------------------------------
/**
* Embed Uploaded file to view under the field
* @param mixed $value The file path/URL or multiple file paths/URLs as array
* @param boolean $thumb_only Show the thumbnail/Icon of the file if true. Else embed the complete document
* @return html $viewDOM HTML DOM to render after the file field
*/
function embed_files_to_dom($value, $thumb_only=false) {
  $viewDOM = '';

  if(is_string($value)){
    $paths[] = $value;
  } elseif(is_array($value)){
    $paths = get_all_strings_from_array($value);
  }

  $viewDOM .= '<div class="ozz-embed-file">';
  foreach ($paths as $k => $path) {
    $file_type = get_file_type_by_url($path);

    $viewDOM .= '<div class="ozz-embed-file__single '.$file_type.'">';
    if($file_type == 'image' || $file_type == 'svg') {
      // Embed Image
      $viewDOM .= '<img src="'.$path.'">';
    } elseif($file_type == 'video') {
      
    } elseif($file_type == 'youtube') {
      
    } elseif($file_type == 'vimeo') {
      
    } elseif($file_type == 'audio' || $file_type == 'mp3') {
      
    } elseif($file_type == 'pdf') {
      
    } elseif($file_type == 'word') {
      
    } elseif($file_type == 'excel') {
      
    } elseif($file_type == 'powerpoint') {
      
    } elseif($file_type == 'text') {
      
    } elseif($file_type == 'zip') {
      
    } elseif($file_type == 'json') {
      
    } elseif($file_type == 'binary') {
      
    } elseif($file_type == 'gzip') {
      
    } elseif($file_type == 'tar') {
      
    } elseif($file_type == 'odt') {
      
    } elseif($file_type == 'ods') {
      
    } elseif($file_type == 'pptx') {
      
    } elseif($file_type == 'swf') {
      
    } elseif($file_type == 'unknown') {
      // Unknown file type
    } elseif($file_type == 'file_not_found') {
      // File not found
      
    }
    $viewDOM .= '</div>';
  }
  $viewDOM .= '</div>';

  return $viewDOM;
}