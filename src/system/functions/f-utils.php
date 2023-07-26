<?php
# ----------------------------------------------------
// Utility functions
# ----------------------------------------------------

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

  if(empty($url)) {
    return $type;
  }

  if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
    $type = 'youtube';
  } elseif (strpos($url, 'vimeo.com') !== false) {
    $type = 'vimeo';
  } else {
    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $url = ($url[0] === '/') ? substr($url, 1) : $url;

    if(!is_absolute_url($url) && !file_exists($url)){
      return 'file_not_found';
    }

    $contentType = $fileInfo->buffer(file_get_contents($url));
    if($contentType){
      $type = match (true) {
        (strpos($contentType, 'image/') === 0) => 'image',
        (strpos($contentType, 'video/') === 0) => 'video',
        (strpos($contentType, 'audio/') === 0) => 'audio',
        (strpos($contentType, 'application/pdf') === 0) => 'pdf',
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
        (strpos($contentType, 'image/svg+xml') === 0) => 'svg',
        default => 'unknown',
      };
    }
  }

  return $type;
}
