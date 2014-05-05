<?php
$args = drush_get_arguments();
if ( ! file_exists($args[2]) || ! is_readable($args[2]) ) {
  drush_set_error('Usage: drush scr makefile-parse.php <makefile>');
  exit();
}

$stuff = make_parse_info_file($args[2]);

$defaults = array(
  'ctools',
  'date',
  'diff',
  'entity',
  'entity_view_mode',
  'entityreference',
  'features',
  'field_group',
  'hfc_fancybox',
  'inline_entity_form',
  'jquery_update',
  'libraries',
  'link',
  'menu_import',
  'menu_position',
  'metatag',
  'metatag_panels',
  'metatag_views',
  'module_filter',
  'panels',
  'panels_mini',
  'pathauto',
  'pathauto_persist',
  'sticky_local_tabs',
  'strongarm',
  'token',
  'views',
  'views_bulk_operations',
  'views_content',
  'views_ui',
  'webform',
  'workbench',
  'workbench_moderation',
  'wysiwyg_filter',
  'xmlsitemap',
);

$modules = array();
foreach ( $stuff['projects'] as $name => $project ) {
  if ( isset($project['subdir']) ) {
    $modules[] = $name;
  }
}

// Collect sub modules
if ( isset($stuff['sub_projects']) ) {
  foreach ( $stuff['sub_projects'] as $name => $project) {
    $modules[] = $name;
  }
}

// Collect core modules that are explicitly enabled


$modules = array_merge($modules, $defaults);
$modules = array_unique($modules);
$diff = array_diff($modules, $defaults);

sort($modules);
echo "Modules (merged with defaults) from makefile: \n";
foreach ( $modules as $module ) {
  echo $module . "\n";
}

echo "\n";
echo "Modules that differ from makefile to default.\n";
foreach ( $diff as $module ) {
  echo $module . "\n";
}
