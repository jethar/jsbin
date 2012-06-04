<?php 

include('app.php'); 

list($code, $revision) = getCodeIdParams($request);
$edit_mode = false;

if ($code_id) {
  list($latest_revision, $html, $javascript, $css) = getCode($code_id, $revision, true);
} else {
  list($latest_revision, $html, $javascript, $css) = defaultCode();
} 

$code_id = $code;

if ($revision != 1 && $revision) {
  $code_id .= '/' . $revision;
}
$code_id_path = ROOT;
if ($code_id) {
  $code_id_path = ROOT . '/' . $code_id;
}

// Include and capture the results of the show saved function.
ob_start();
showSaved($home);
$list_history = ob_get_clean();

$code_id_domain = preg_replace('/https?:\/\//', '', $code_id_path);

$view = file_get_contents('../views/index.html');
$mustache = new Mustache;
echo $mustache->render($view, array(
  'token' => $csrf,
  'root' => ROOT,
  'version' => VERSION,
  'home' => $home,
  'revision' => $revision,
  'code_id' => $code_id,
  'code_id_path' => $code_id_path,
  'code_id_domain' => $code_id_domain,
  'json_template' => json_encode(array(
    'url' => $code_id_path,
    'html' => $html,
    'css' => $css,
    'javascript' => $javascript
  )),
  'custom_css' => isset($custom['css']) ? preg_replace('/^\//', '', $custom['css']) : null,
  'production?' => IS_PRODUCTION,
  'analytics_id' => ANALYTICS_ID,
  'embed' => $embed,
  'tips' => file_get_contents('tips.json'),
  'list_history' => $embed ? '' : $list_history,
  'jsbin' => json_encode(array(
    'root' => ROOT,
    'version' => VERSION,
    'state' => array(
      'stream' => false,
      'code' => isset($code) && $code ? $code : null,
      'token' => $csrf,
      'revision' => $revision
    ),
    'settings' => isset($custom['settings']) ? $custom['settings'] : array('panels' => array()) 
  ))
));
?>
