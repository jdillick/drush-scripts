<?php
module_load_include('inc', 'ctools', 'includes/export');

$args = drush_get_arguments();
if ( ! isset($args[2]) || ! isset($args[3]) ) {
  drush_set_error('Usage: drush @<alias> scr panels-export.php <panel-nids> <output-directory>');
  exit();
}

if ( ! is_dir(realpath($args[3])) ) {
  drush_set_error(t("Output directory @op does not exist.", array('@op' => $args[3])));
  exit();
}

$nids = array();
if (strpos($args[2], ',')) {
  $nids = explode(',', $args[2]);
  foreach ( $nids as $index => $nid ) {
    $nids[$index] = trim($nid);
  }
}
else {
  $nids[] = trim($args[2]);
}
$nids = array_filter($nids);

// 2078 - holiday catalog
$nodes = node_load_multiple($nids);

foreach ( $nodes as $nid => $node ) {
  $panel_file = '<?php' . "\n";
  $panel_file .= '$node = ' . ctools_var_export($node) . ";\n";

  $display = db_select('panels_display', 'd')
    ->fields('d')
    ->condition('d.did', $node->panels_node['did'], '=')
    ->execute()
    ->fetchAssoc();

  $panel_file .= '$display = ' . ctools_var_export($display) . ";\n";

  $panes = db_select('panels_pane', 'p')
    ->fields('p')
    ->condition('p.did', $node->panels_node['did'], '=')
    ->execute()
    ->fetchAllAssoc('pid', PDO::FETCH_ASSOC);

  $panel_file .= '$panes = ' . ctools_var_export($panes) . ";\n";

  file_put_contents(realpath($args[3]) . '/' . 'panel-' . make_safe_filename(trim($node->title)) . '.inc', $panel_file);
}

function make_safe_filename($str, $del = '-') {
  return preg_replace('/\b\s?[\s|&|*|\.|nbsp;|\-|\']\s?\b/', $del, strtolower($str));
}
