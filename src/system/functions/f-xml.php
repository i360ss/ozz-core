<?php
use Ozz\Core\Response;

# ----------------------------------------------------
// XML manipulation
# ----------------------------------------------------
/**
 * Send array data as XML response
 * 
 * @param array $data The data to convert to XML
 * @param string $rootName Root element name (default: 'root')
 * @param array $options XML conversion options
 * @return Response Returns response instance for method chaining
 */
function xml(array $data, string $rootName = 'root', array $options = []): Response {
  $response = Response::getInstance();

  // Convert array to XML string
  $xmlContent = array_to_xml($data, $rootName, $options);

  // Set XML content type if not already set
  if (!$response->hasHeader('Content-Type')) {
    $response->setHeader('Content-Type', 'application/xml; charset=' . CONFIG['CHARSET']);
  }

  // Set content and send response
  $response->setContent($xmlContent);

  return $response->send();
}

/**
 * Convert an array to XML string
 * @param array $data The array to convert
 * @param string $rootName Root element name (default: 'root')
 * @param array $options Configuration options
 * @return string XML string
 * @throws InvalidArgumentException
 */
function array_to_xml(array $data, string $rootName = 'root', array $options = []): string {
  // Default options
  $options = array_merge([
    'version' => '1.0',
    'encoding' => 'UTF-8',
    'formatOutput' => true,
    'attributePrefix' => '@',
    'valueKey' => '#text',
    'cdataKey' => '#cdata',
    'numericTagPrefix' => 'item',
    'preserveNumericKeys' => false,
    'standalone' => null
  ], $options);

  // Validate root name
  if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_\-:]*$/', $rootName)) {
    throw new InvalidArgumentException("Invalid root element name: {$rootName}");
  }

  $dom = new DOMDocument($options['version'], $options['encoding']);

  if ($options['formatOutput']) {
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
  }

  if ($options['standalone'] !== null) {
    $dom->xmlStandalone = $options['standalone'];
  }

  $root = $dom->createElement($rootName);
  $dom->appendChild($root);

  build_xml_from_array($data, $root, $dom, $options);

  return $dom->saveXML();
}

/**
 * Recursively build XML from array data
 * @param mixed $data The data to process
 * @param DOMElement $parent Parent DOM element
 * @param DOMDocument $dom DOM document
 * @param array $options Configuration options
 */
function build_xml_from_array($data, DOMElement $parent, DOMDocument $dom, array $options): void {
  // Handle non-array values (scalar, null, etc.)
  if (!is_array($data)) {
    $parent->appendChild($dom->createTextNode(escape_xml_value($data)));
    return;
  }

  // Check if array is empty
  if (empty($data)) {
    return;
  }

  // Extract and remove special keys
  $attributes = [];
  $hasCdata = false;
  $cdataContent = null;
  $textContent = null;

  // Extract attributes (keys starting with attribute prefix)
  foreach ($data as $key => $value) {
    if (strpos($key, $options['attributePrefix']) === 0) {
      $attrName = substr($key, strlen($options['attributePrefix']));
      $attributes[$attrName] = $value;
      unset($data[$key]);
    }
  }

  // Extract CDATA content
  if (isset($data[$options['cdataKey']])) {
    $hasCdata = true;
    $cdataContent = $data[$options['cdataKey']];
    unset($data[$options['cdataKey']]);
  }

  // Extract direct text content
  if (isset($data[$options['valueKey']])) {
    $textContent = $data[$options['valueKey']];
    unset($data[$options['valueKey']]);
  }

  // Add attributes to parent element
  foreach ($attributes as $name => $value) {
    $parent->setAttribute($name, escape_xml_value($value));
  }

  // Handle text/CDATA only (no child elements)
  if (empty($data)) {
    if ($hasCdata) {
      $parent->appendChild($dom->createCDATASection((string)$cdataContent));
    } elseif ($textContent !== null) {
      $parent->appendChild($dom->createTextNode(escape_xml_value($textContent)));
    }
    return;
  }

  // Process child elements
  foreach ($data as $key => $value) {
    // Handle numeric keys
    if (is_numeric($key)) {
      if ($options['preserveNumericKeys']) {
        $key = "_{$key}";
      } else {
        $key = $options['numericTagPrefix'];
      }
    }

    // Sanitize tag name
    $tagName = sanitize_xml_tag($key);

    // Handle sequential arrays (list of items)
    if (is_array($value) && is_sequential_array($value)) {
      // Multiple items with same tag
      foreach ($value as $item) {
        $child = $dom->createElement($tagName);
        build_xml_from_array($item, $child, $dom, $options);
        $parent->appendChild($child);
      }
    } else {
      // Single item
      $child = $dom->createElement($tagName);
      build_xml_from_array($value, $child, $dom, $options);
      $parent->appendChild($child);
    }
  }
}

/**
 * Check if array is sequential (list, not associative)
 * @param array $array The array to check
 * @return bool
 */
function is_sequential_array(array $array): bool {
  if (empty($array)) {
    return false;
  }

  // Check if keys are sequential integers starting from 0
  return array_keys($array) === range(0, count($array) - 1);
}

/**
 * Sanitize XML tag name (remove invalid characters)
 * @param string $tag The tag name to sanitize
 * @return string Sanitized tag name
 */
function sanitize_xml_tag(string $tag): string {
  // Remove invalid characters
  $tag = preg_replace('/[^a-zA-Z0-9_\-:]/', '_', $tag);

  // Ensure first character is valid (letter, underscore, or colon)
  if (!preg_match('/^[a-zA-Z_:]/', $tag)) {
    $tag = '_' . $tag;
  }

  return $tag;
}

/**
 * Escape XML special characters and handle null/boolean values
 * @param mixed $value The value to escape
 * @return string Escaped string
 */
function escape_xml_value($value): string {
  if ($value === null) {
    return '';
  }

  if (is_bool($value)) {
    return $value ? 'true' : 'false';
  }

  // Convert to string and escape XML special characters
  $string = (string)$value;

  // Use htmlspecialchars with ENT_XML1 for proper XML escaping
  return htmlspecialchars($string, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}