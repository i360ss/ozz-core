<?php
# ----------------------------------------------------
// Utility functions
# ----------------------------------------------------
use Ozz\Core\Form;
use Ozz\Core\AppInit;
use Ozz\Core\Session;
use Ozz\Core\Csrf;

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
 * @param bool $byExtention check only extention if this is true
 */
function get_file_type_by_url(string $url, $byExtention = false) {
  $type = 'unknown';

  if (empty($url)) {
    return $type;
  }

  if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
    return 'youtube';
  } elseif (strpos($url, 'vimeo.com') !== false) {
    return 'vimeo';
  }

  if (function_exists('base_url') && strpos($url, base_url()) !== false) {
    $url = str_replace(base_url(), '', $url);
  }

  $filePath = file_exists($url) ? $url : ltrim($url, '/');
  $contentType = false;

  if (file_exists($filePath)) {
    // Local File exists: read actual MIME type from disk safely
    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $contentType = $fileInfo->file($filePath);
  } elseif ($byExtention) {
    // Local File does NOT exist: extract extension from URL path and map to MIME type
    $path = parse_url($url, PHP_URL_PATH);
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    $contentType = get_mime_by_extension($ext);
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
 * Maps a file extension to its corresponding MIME type
 */
function get_mime_by_extension(string $ext): string|false {
  $mimes = [
    // Images
    'svg'  => 'image/svg+xml',
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'gif'  => 'image/gif',
    'webp' => 'image/webp',

    // Video & Audio
    'mp4'  => 'video/mp4',
    'webm' => 'video/webm',
    'ogg'  => 'video/ogg',
    'mp3'  => 'audio/mpeg',
    'wav'  => 'audio/wav',

    // Documents (PDF & Plain Text)
    'pdf'  => 'application/pdf',
    'txt'  => 'text/plain',
    'html' => 'text/html',
    'htm'  => 'text/html',
    'json' => 'application/json',
    'xml'  => 'text/xml',
    'js'   => 'application/javascript',
    'css'  => 'text/css',
    'php'  => 'application/x-php',

    // MS Office - Standard
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls'  => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'ppt'  => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',

    // MS Office - Templates
    'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
    'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
    'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',

    // MS Office - Macro Enabled
    'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
    'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
    'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',

    // OpenDocument
    'odt'  => 'application/vnd.oasis.opendocument.text',
    'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',

    // Archives & Compressed
    'zip'  => 'application/zip',
    'gz'   => 'application/x-gzip',
    'gzip' => 'application/x-gzip',
    'tar'  => 'application/x-tar',

    // Binaries & Legacy
    'bin'  => 'application/octet-stream',
    'swf'  => 'application/vnd.adobe.flash-movie',
  ];

  return $mimes[strtolower($ext)] ?? false;
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

  return [
    'data' => $paginated_data,
    'items_per_page' => $items_per_page,
    'number_of_pages' => $total_pages,
    'current_page' => $current_index,
    'total_items' => $total_items,
  ];
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
 * Start Form
 * @param array $args
 */
function start_form($args) {
  return Form::start($args);
}

/**
 * End Form
 */
function end_form($args=false) {
  return Form::end($args);
}

/**
 * Generate form fields
 * @param array $args
 */
function form_create_fields($args) {
  return Form::generateFields($args);
}

/**
 * Get File MIME type
 * @param array $file
 */
function get_mime_type($file=false) {
  if (is_string($file) && file_exists($file)) {
    // If $file is a string (file path), check its MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    return finfo_file($finfo, $file);
  } elseif (is_array($file) && isset($file['tmp_name']) && file_exists($file['tmp_name'])) {
    // If $file is an array (file input), check its MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    return finfo_file($finfo, $file['tmp_name']);;
  }

  return 'unknown';
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

/**
 * Convert PHP size to bytes
 */
function convert_php_size_to_bytes($sSize) {
  if (is_numeric($sSize)) {
    return $sSize;
  }
  $sSuffix = strtoupper(substr($sSize, -1));
  $iValue = (int) substr($sSize, 0, -1);

  switch ($sSuffix) {
    case 'P': $iValue *= 1024 * 1024 * 1024 * 1024 * 1024;
      break;
    case 'T': $iValue *= 1024 * 1024 * 1024 * 1024;
      break;
    case 'G': $iValue *= 1024 * 1024 * 1024;
      break;
    case 'M': $iValue *= 1024 * 1024;
      break;
    case 'K': $iValue *= 1024;
      break;
    default: $iValue;
  }

  return $iValue;
}

// Dynamically resolves the current application request URL
function app_url(): string {
  return rtrim($_SERVER['HTTP_HOST'] ?? '', '/') . '/';
}

// Dynamically resolves the full secure protocol base url string
function base_url(bool $reset = false): string {
  static $cachedBaseUrl = null;

  if ($reset) {
    $cachedBaseUrl = null;
    return '';
  }

  if ($cachedBaseUrl === null) {
    $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
      (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $cachedBaseUrl = ($is_secure ? 'https://' : 'http://') . app_url();
  }

  return $cachedBaseUrl;
}

// Assets URL
function assets_url(bool $reset = false): string {
  static $cachedAssetsUrl = null;

  if ($reset) {
    $cachedAssetsUrl = null;
    return '';
  }

  if ($cachedAssetsUrl === null) {
    $cachedAssetsUrl = '/' . trim(CONFIG['APP_PATHS']['assets'], '/') . '/';
  }

  return $cachedAssetsUrl;
}

/**
 * Asset loader
 * @param string $path Path for the asset
 * @param boolean $firstOnly Return first item if multiple paths provided (for media library)
 * @return string Asset URL with version query for cache busting
 */
function asset($path, $firstOnly=true) {
  if (is_json($path)) {
    // Handle media library single and multiple paths
    $paths = json_decode($path, true);
    if (count($paths) === 1) {
      return asset($paths[0]['url'] ?? '');
    } else {
      $result = [];
      foreach ($paths as $item) {
        $result[] = asset($item['url'] ?? '');
      }
      return $firstOnly === true ? ($result[0] ?? '') : $result;
    }
  }

  if (empty($path)) {
    return false;
  }

  $decoded_path = rawurldecode($path);
  $fullPath = PUBLIC_DIR . $decoded_path;
  if (!file_exists($fullPath)) {
    return base_url() . $path;
  }

  return base_url() . $path . "?v=" . base_convert(filemtime($fullPath), 10, 36);
}

// App CSP nonce
function csp_nonce(bool $reset = false) {
  static $localNonce = null;
  if ($reset) {
    $localNonce = null;
    return '';
  }

  if ($localNonce === null) {
    $localNonce = AppInit::hashKey('csp-nonce');
  }

  return $localNonce;
}

// Locale (Current app language)
function locale(bool $reset = false) {
  static $cachedLang = null;

  if ($reset) {
    $cachedLang = null;
    return '';
  }

  if ($cachedLang === null) {
    if (!Session::has('app_lang')) {
      Session::set('app_lang', env('app', 'APP_LANG'));
    }
    $cachedLang = Session::get('app_lang'); 
  }

  return $cachedLang;
}

// CSRF token
function csrf_token(bool $reset = false) {
  static $cachedToken = null;

  if ($reset) {
    $cachedToken = null;
    return '';
  }

  if ($cachedToken === null) {
    $cachedToken = Csrf::getToken();
  }

  return $cachedToken;
}

// CSRF Field
function csrf_field(bool $reset = false) {
  static $cachedField = null;

  if ($reset) {
    $cachedField = null;
    return '';
  }

  if ($cachedField === null) {
    $cachedField = Csrf::getTokenField();
  }

  return $cachedField;
}

// Check if input file selected
function has_file($fileInput) {
  if (!isset($fileInput) || empty($fileInput['name'])) {
    return false;
  }

  if (is_array($fileInput['error'])) {
    foreach ($fileInput['error'] as $key => $error) {
      if ($error !== UPLOAD_ERR_NO_FILE && $fileInput['size'][$key] > 0) {
        return true; 
      }
    }
    return false;
  }
  else {
    if ($fileInput['error'] !== UPLOAD_ERR_NO_FILE && $fileInput['size'] > 0) {
      return true;
    }
    return false;
  }
}

// Generate UUID
function generate_uuidv4() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}