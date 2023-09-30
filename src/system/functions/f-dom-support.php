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
    if($file_type == 'image') {
      $viewDOM .= '<img src="'.$path.'">';
    }
    elseif ($file_type == 'svg') {
      $viewDOM .= '<object type="image/svg+xml" data="'.$path.'">
        <img src="'.$path.'" />
      </object>';
    }
    elseif($file_type == 'video') {
      $viewDOM .= '<video width="400" height="250" controls>
        <source src="'.$path.'" type="video/mp4">
        <source src="'.$path.'" type="video/ogg">
      </video>';
    }
    elseif($file_type == 'youtube') {
      $viewDOM .= '<iframe width="400" height="250" src="'.$path.'"></iframe>';
    }
    elseif($file_type == 'vimeo') {
      $viewDOM .= '<iframe src="'.$path.'" width="400" height="250" frameborder="0" picture-in-picture" allowfullscreen></iframe>';
    }
    elseif($file_type == 'audio' || $file_type == 'mp3') {
      $viewDOM .= '<audio controls>
        <source src="'.$path.'" type="audio/ogg">
        <source src="'.$path.'" type="audio/mpeg">
      </audio>';
    }
    elseif($file_type == 'pdf') {
      $viewDOM .= '<object data="'.$path.'" type="application/pdf" width="100%" height="500px">
        <p>Unable to display PDF file. <a href="'.$path.'">Download</a> instead.</p>
      </object>';
    }
    elseif(in_array($file_type, ['word', 'excel', 'powerpoint', 'text', 'pptx', 'odt', 'ods', 'zip', 'tar', 'gzip', 'swf', 'json'])) {
      $viewDOM .= '<a href="'.$path.'" target="_blank">'.$path.'</a>';
    }
    elseif($file_type == 'unknown') {
      // Unknown file type
      $viewDOM .= '<span class="unknown-file-type"></span>';
    }
    elseif($file_type == 'file_not_found') {
      // File not found
      $viewDOM .= '<span class="file-not-found"></span>';
    }
    $viewDOM .= '</div>';
  }
  $viewDOM .= '</div>';

  return $viewDOM;
}