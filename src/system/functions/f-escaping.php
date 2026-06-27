<?php
use Ozz\Core\Sanitize;

# ----------------------------------------------------
// Ozz escaping functions
# ----------------------------------------------------

/**
 * Escape HTML
 * @param string $str
 */
function esc(?string $str): string {
  if ($str === null) {
    return '';
  }

  return htmlspecialchars($str, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8', false);
}

function esc_attr(?string $str): string {
  return esc($str);
}

/**
 * Escape Javascript
 * @param string $str
 */
function esc_js(?string $str): string {
  if ($str === null) {
    return "''";
  }
  $result = json_encode($str, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);

  return $result !== false ? $result : '""';
}

/**
 * Escape CSS
 * @param string $str
 */
function esc_css(?string $str): string {
  if ($str === null) {
    return '';
  }

  return preg_replace_callback('/[^A-Za-z0-9]/', function ($matches) {
    $char = $matches[0];

    // Convert the character to its UTF-32 hex representation for CSS
    // e.g., # becomes \23 (or \000023 )
    $hex = ltrim(str_pad(dechex(mb_ord($char, 'UTF-8')), 6, '0', STR_PAD_LEFT), '0');

    return '\\' . ($hex === '' ? '0' : $hex) . ' ';
  }, $str);
}

/**
 * Escape URL
 * @param string $url
 * @param array $allowed_protocols
 */
function esc_url(?string $url, array $allowed_protocols = ['http', 'https', 'mailto', 'ftp']): string{
  if ($url === null || $url === '') {
    return '';
  }

  $url = trim(preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $url));

  $scheme = parse_url($url, PHP_URL_SCHEME);
  if ($scheme !== null) {
    if (!in_array(strtolower($scheme), $allowed_protocols, true)) {
      return '';
    }
  } elseif (str_starts_with(strtolower($url), 'javascript:')) {
    return '';
  }

  return htmlspecialchars($url, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8');
}

// Sanitize SVG
function esc_svg($svg, $allowed_elms=[]) {
  return Sanitize::svg($svg, $allowed_elms);
}

/**
 * Decode encoded HTML
 * @param string $str encoded HTML
 * 
 * @return HTML
 */
function html_decode($str, $flag=false){
  return htmlspecialchars_decode( $value, ENT_QUOTES );
}

/**
 * Remove all inline CSS (style attributes)
 * @param string $html
 */
function strip_inline_styles(string $html): string {
  if (trim($html) === '') {
    return '';
  }

  // Suppress warnings caused by malformed HTML snippets
  libxml_use_internal_errors(true);

  $dom = new \DOMDocument();
  // Load HTML ensuring UTF-8 encoding is preserved
  $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

  $xpath = new \DOMXPath($dom);
  $nodes = $xpath->query('//*[@style]');

  foreach ($nodes as $node) {
    $node->removeAttribute('style');
  }

  $result = $dom->saveHTML();
  libxml_clear_errors();

  return $result !== false ? $result : '';
}