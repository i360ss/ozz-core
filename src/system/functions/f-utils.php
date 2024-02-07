<?php
# ----------------------------------------------------
// Utility functions
# ----------------------------------------------------
use Ozz\Core\Form;

/**
 * Check if absolute URL
 * @param string $url
 * @return boolean true if absolute URL
 */
function is_absolute_url($url) {
  $parsedUrl = parse_url($url);
  return isset($parsedUrl['scheme']);
}

/**
 * Detect the File type by URL
 * @param string $url The URL to detect
 */
function get_file_type_by_url($url) {
  $type = 'unknown';

  if (empty($url)) {
    return $type;
  }

  if (strpos($url, BASE_URL) !== false) {
    $url = str_replace(BASE_URL, '', $url);
  }

  if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
    return 'youtube';
  } elseif (strpos($url, 'vimeo.com') !== false) {
    return 'vimeo';
  }

  $url = ltrim($url, '/');

  if (file_exists($url)) {
    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $contentType = $fileInfo->buffer(file_get_contents($url));
  }

  if ($contentType) {
    $type = match (true) {
      (strpos($contentType, 'image/svg+xml') === 0) => 'svg',
      (strpos($contentType, 'image/') === 0) => 'image',
      (strpos($contentType, 'video/') === 0) => 'video',
      (strpos($contentType, 'audio/') === 0) => 'audio',
      (strpos($contentType, 'application/pdf') === 0) => 'pdf',
      (strpos($contentType, 'application/x-empty') === 0) => 'empty',
      (strpos($contentType, 'application/msword') === 0 || strpos($contentType, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') === 0) => 'word',
      (strpos($contentType, 'application/vnd.ms-excel') === 0 || strpos($contentType, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') === 0) => 'excel',
      (strpos($contentType, 'application/vnd.ms-powerpoint') === 0 || strpos($contentType, 'application/vnd.openxmlformats-officedocument.presentationml.presentation') === 0) => 'powerpoint',
      (strpos($contentType, 'application/zip') === 0) => 'zip',
      (strpos($contentType, 'application/json') === 0) => 'json',
      (strpos($contentType, 'text/plain') === 0) => 'text',
      (strpos($contentType, 'application/xml') === 0 || strpos($contentType, 'text/xml') === 0) => 'xml',
      (strpos($contentType, 'application/octet-stream') === 0) => 'binary',
      (strpos($contentType, 'application/x-gzip') === 0) => 'gzip',
      (strpos($contentType, 'application/x-tar') === 0) => 'tar',
      (strpos($contentType, 'audio/mpeg') === 0) => 'mp3',
      (strpos($contentType, 'application/vnd.oasis.opendocument.text') === 0) => 'odt',
      (strpos($contentType, 'application/vnd.oasis.opendocument.spreadsheet') === 0) => 'ods',
      (strpos($contentType, 'application/vnd.openxmlformats-officedocument.presentationml.slideshow') === 0) => 'pptx',
      (strpos($contentType, 'application/vnd.adobe.flash-movie') === 0) => 'swf',
      (strpos($contentType, 'text/html') === 0 || strpos($contentType, 'application/xhtml+xml') === 0) => 'html',
      (strpos($contentType, 'application/x-php') === 0 || strpos($contentType, 'text/x-php') === 0) => 'php',
      (strpos($contentType, 'application/javascript') === 0 || strpos($contentType, 'text/javascript') === 0 || strpos($contentType, 'application/x-javascript') === 0) => 'js',
      (strpos($contentType, 'text/css') === 0) => 'css',
      (strpos($contentType, 'application/vnd.openxmlformats-officedocument.spreadsheetml.template') === 0) => 'xltx',
      (strpos($contentType, 'application/vnd.openxmlformats-officedocument.wordprocessingml.template') === 0) => 'dotx',
      (strpos($contentType, 'application/vnd.openxmlformats-officedocument.presentationml.template') === 0) => 'potx',
      (strpos($contentType, 'application/vnd.ms-excel.sheet.macroEnabled.12') === 0) => 'xlsm',
      (strpos($contentType, 'application/vnd.ms-word.document.macroEnabled.12') === 0) => 'docm',
      (strpos($contentType, 'application/vnd.ms-powerpoint.presentation.macroEnabled.12') === 0) => 'pptm',
      default => 'unknown',
    };
  }

  return $type;
}

/**
 * Get All Items paths inside a directory as array
 * @param string $dir Directory to scan
 */
function get_directory_content($dir, $nested=false) {
  $result = [];
  $contents = scandir($dir);
  $contents = array_diff($contents, array('.', '..', '.gitkeep', '.gitignore', '.htaccess'));
  foreach ($contents as $item) {
    $path = $dir . DIRECTORY_SEPARATOR . $item;
    if(is_dir($path)){
      $result['/'.$item] = $nested ? get_directory_content($path) : [];
    } else {
      $result[] = $item;
    }
  }

  return $result;
}

/**
 * Size Units (Add units to bytes)
 * @param int $bytes
 */
function format_size_units($bytes) {
  $units = ['B', 'KB', 'MB', 'GB'];
  $exp = (int) floor(log($bytes, 1024)) ?: 0;
  return round($bytes / (1024 ** $exp), 2) . ' ' . $units[$exp];
}

/**
 * Convert youtube URL to embed URL
 * @param string $url
 */
function youtube_embed_url($inputURL) {
  if (strpos($inputURL, 'youtube.com') !== false) {
    $query = parse_url($inputURL, PHP_URL_QUERY);
    parse_str($query, $params);
    if (isset($params['v'])) {
      $videoID = $params['v'];
    } else {
      return "Invalid YouTube URL";
    }
  } elseif (strpos($inputURL, 'youtu.be') !== false) {
    $videoID = substr($inputURL, strrpos($inputURL, '/') + 1);
  } elseif (strpos($inputURL, 'embed') !== false) {
    return $inputURL;
  } else {
    return "Invalid YouTube URL";
  }

  return "https://www.youtube.com/embed/$videoID";
}

/**
 * Vimeo embed URL
 * @param string $url
 */
function vimeo_embed_url($vimeoURL) {
  if (strpos($vimeoURL, 'vimeo.com') === false) {
    return "Invalid Vimeo URL";
  }
  $videoID = substr($vimeoURL, strrpos($vimeoURL, '/') + 1);

  return "https://player.vimeo.com/video/$videoID";
}

/**
 * Simple Pagination
 * @param array $data Array of information to be paginated
 * @param int $items_per_page
 * @param int $current_index
 */
function array_pagination($data, $items_per_page, $current_index) {
  $data = is_array($data) ? $data : [];
  $total_items = count($data);
  $total_pages = ceil($total_items / $items_per_page);

  if ($current_index < 1) {
    $current_index = 1;
  } elseif ($current_index > $total_pages) {
    $current_index = $total_pages;
  }

  $start_index = ($current_index - 1) * $items_per_page;
  $paginated_data = array_slice($data, $start_index, $items_per_page);

  $result = [
    'data' => $paginated_data,
    'number_of_pages' => $total_pages,
    'current_page' => $current_index,
    'total_items' => $total_items,
  ];

  return $result;
}

/**
 * Pagination DOM
 * @param int $num_pages Number of total pages
 * @param int $current_page Current page ID
 * @param string $url URL to the links
 * @param int $pages_to_show Page links to show in DOM
 */
function pagination_dom($num_pages, $current_page, $pages_to_show=5, $link_url=false) {
  $dom = '<div class="pagination">';
  $url = $link_url === false ? $_SERVER['REQUEST_URI'] : $link_url;
  $half = floor($pages_to_show / 2);
  $start = max(1, $current_page - $half);
  $end = min($num_pages, $start + $pages_to_show - 1);

  if ($end - $start + 1 < $pages_to_show) {
    $start = max(1, $end - $pages_to_show + 1);
  }

  // Prev button
  ($start > 1)
    ? $dom .= "<a href='".url_add_query($url, ['p' => $start-1])."'><button class='prev'> < </button></a>"
    : false;

  // Page numbers
  for ($i=$start; $i <= $end; $i++) {
    $dom .= $i == $current_page
      ? "<button class='current-page'>$i</button>"
      : "<a href='".url_add_query($url, ['p' => $i])."'><button>$i</button></a>";
  }

  // Next button
  ($end < $num_pages)
    ? $dom .= "<a href='".url_add_query($url, ['p' => $end+1])."'><button class='next'> > </button></a>"
    : false;

  return $dom.'</div>';
}

/**
 * Add query-string / fragment to URL
 * @param string $url
 * @param array $params
 */
function url_add_query($url, $params) {
  $new_url = parse_url($url);

  // Check if the URL includes a scheme and host
  $scheme = isset($new_url['scheme']) ? $new_url['scheme'] . '://' : '';
  $host = isset($new_url['host']) ? $new_url['host'] : '';

  if (!isset($new_url['query']) || $new_url['query'] === '') {
    $querySeparator = (strpos($url, '?') === false) ? '?' : '&';
    return $scheme . $host . $new_url['path'] . $querySeparator . http_build_query($params);
  }

  parse_str($new_url['query'], $query_params);
  $new_url['query'] = http_build_query(array_merge($query_params, $params));

  // Reconstruct the URL
  $newUrl = $scheme . $host . $new_url['path'];
  if (!empty($new_url['query'])) {
    $newUrl .= '?' . $new_url['query'];
  }
  if (isset($new_url['fragment'])) {
    $newUrl .= '#' . $new_url['fragment'];
  }

  return $newUrl;
}

/**
 * Create Ozz Form
 * @param array $args
 * @param array $values
 */
function create_form($args, $values=[]) {
  return Form::create($args, $values);
}
function _create_form($args, $values=[]) {
  echo Form::create($args, $values);
}

/**
 * Get File MIME type
 * @param array $file
 */
function get_mime_type($file=false) {
  if (is_string($file) && file_exists($file)) {
    // If $file is a string (file path), check its MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file);
    finfo_close($finfo);
    return $mime;
  } elseif (is_array($file) && isset($file['tmp_name']) && file_exists($file['tmp_name'])) {
    // If $file is an array (file input), check its MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    return $mime;
  } else {
    return 'unknown';
  }
}

/**
 * Detect file type
 * @param array $file
 */
function get_file_type_to_upload($file) {
  $mime = get_mime_type($file);
  require __DIR__.'/../utils/file_mime_types.php';

  if (in_array($mime, $IMAGE_MIMES)) {
    return 'image';
  } elseif (in_array($mime, $DOCUMENT_MIMES)) {
    return 'document';
  } elseif (in_array($mime, $VIDEO_MIMES)) {
    return 'video';
  } elseif (in_array($mime, $AUDIO_MIMES)) {
    return 'audio';
  } else {
    return 'unknown';
  }
}

/**
 * Ozz Format Date
 * @param int $date Unix datetime
 */
function ozz_format_date($date, $format=1) {
  if ($format == 1) {
    return date('M d, Y | h:i a', (int) $date);
  } elseif($format == 2) {
    return date('Y-m-d\TH:i', (int) $date);
  } else {
    return date($format, (int) $date);
  }
}