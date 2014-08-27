<?php

module_load_include('inc', 'ctools', 'includes/export');

$args = drush_get_arguments();
if ( ! isset($args[2]) || ! isset($args[3]) ) {
  drush_set_error('Usage: drush @<alias> scr menu-links-export.php <menu-names> <output-directory>');
  exit();
}

if ( ! is_dir(realpath($args[3])) ) {
  drush_set_error(t("Output directory @op does not exist.", array('@op' => $args[3])));
  exit();
}

$menus = array();
if (strpos($args[2], ',')) {
  $menus = explode(',', $args[2]);
  foreach ( $menus as $index => $nid ) {
    $menus[$index] = trim($nid);
  }
}
else {
  $menus[] = trim($args[2]);
}
$menus = array_filter($menus);

$menu_links = db_select('menu_links', 'm')
  ->fields('m', array('link_path','link_title','menu_name','weight','expanded','hidden','options','mlid','plid','router_path'))
  ->condition('m.menu_name', $menus, 'IN')
  ->condition('m.link_title', 'Home', '<>')
  ->orderBy('depth','ASC')
  ->execute()
  ->fetchAllAssoc('mlid', PDO::FETCH_ASSOC);

foreach ( $menu_links as $mlid => &$menu_link ) {
  if ( strpos($menu_link['link_path'], 'taxonomy/term/') === 0 ||
    strpos($menu_link['link_path'], 'node/') === 0 ) {
    $menu_link['alias'] = drupal_get_path_alias($menu_link['link_path']);
  }
}

$menu_links_file = "<?php\n";
$menu_links_file .= '$menu_links = ' . ctools_var_export($menu_links) . ";\n";
file_put_contents(realpath($args[3]) . '/' . 'menu-links.inc', $menu_links_file);
