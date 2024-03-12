<?php
/**
 * Generate default initial content on file creation
 * Set up contents for all file types that can be created via ozz commands
 */
function Content($type, $data){
  switch ($type) {
    case 'middleware':
      return ozz_content_Middleware($data);
      break;

    case 'model':
      return ozz_content_Model($data);
      break; 

    case 'controller':
      return ozz_content_Controller($data);
      break;

    case 'view':
      return ozz_content_ViewContent($data);
      break;
    
    case 'layout':
      return ozz_content_Layout();
      break;

    case 'component':
      return ozz_content_Component($data);
      break;

    case 'email_template':
      return ozz_content_EmailTemplate();
      break;
  }
}

/**
 * Middleware Contents
 * @param array $data The Namespace and Class name of the middleware
 */
function ozz_content_Middleware($data){
  extract($data);
  return "<?php\nnamespace $namespace;
\nuse Ozz\Core\Request;
\nclass ".ucwords($class)." {\n
  public function handle(){\n    \n    
  }
}";
}

/**
 * Model Contents
 * @param array $data The Namespace and Class name of the model
 */
function ozz_content_Model($data){
  extract($data);
  $table = '$table';
  return "<?php\nnamespace $namespace;
\nuse Ozz\Core\Model;
\nclass ".ucwords($class)." extends Model {
  protected $table = '".strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class))."';
}";
}

/**
 * Controller Contents
 * @param array $data The Namespace and Class name of the controller
 */
function ozz_content_Controller($data){
  extract($data);
  return "<?php\nnamespace $namespace;
\nuse Ozz\Core\Controller;
use Ozz\Core\Request;
\nclass ".ucwords($class)." extends Controller {\n
  public function index(){\n    \n    
  }\n
}";
}

/**
 * View Contents
 * @param array $data The Name and path of the view file
 */
function ozz_content_ViewContent($data){
  extract($data);
  return '<?php
/**
 * View Name: '.$name.'
 * Path: '.$path.'
 * @param array $data arguments passed from controller or route
 */
?>
{{ title        = "page_title_here" }}
{{ keywords     = "meta_keywords_here" }}
{{ description  = "meta_description_here" }}
{{ body_class   = "view-'.$name.'" }}

{{ content }}
  <!-- Page Content should be here -->
{{ content-end }}';
}

/**
 * Email Template Contents
 */
function ozz_content_EmailTemplate() {
  return '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
  <title></title>
  <style>
    body { font-family: sans-serif; margin: 0; padding: 0; }
  </style>
</head>
<body>
  <h1>Hello, {{ Name }}</h1>
  <p>{{ Message }}</p>
</body>
</html>';
}

/**
 * Component file Contents (View)
 * @param array $data The Name and path of the component
 */
function ozz_content_Component($data){
  extract($data);
  return '<?php
/**
 * Component Name: '.$name.'
 * Path: '.$path.'
 * @param array $args Given arguments on component call
 */
?>

';
}

/**
 * Base Layout file Contents
 */
function ozz_content_Layout(){
  return '<!DOCTYPE html>
<html lang="<?=APP_LANG?>">
<head>
  <meta charset=<?=CONFIG[\'CHARSET\']?>>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{% title %}</title>
  <meta name="keywords" content="{% keywords %}">
  <meta name="description" content="{% description %}">
  <meta name="robots" content="index, follow">
  <meta name="csrf-token" content="<?=CSRF_TOKEN?>">
  <meta name="url" content="<?=BASE_URL?>">
  <link rel="icon" type="image/x-icon" href="<?=ASSETS?>icons/favicon.ico">
  <link nonce="<?=CSP_NONCE?>" rel="stylesheet" href="<?=ASSETS?>css/styles.css">
</head>
<body class="{% body_class %}">
<main>
  {% content %}
</main>
<script nonce="<?=CSP_NONCE?>" src="<?=ASSETS?>js/app.js"></script>
</body>
</html>';
}