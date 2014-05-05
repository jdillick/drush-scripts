<?php

$args = drush_get_arguments();
if ( ! isset($args[2]) ) {
  drush_set_error('Usage: drush @<alias> scr generate-strongarm.php <module_name> <base_path>');
  exit();
}

$module_name = $args[2];
if ( ! isset($args[3]) ) {
  drush_set_error('Usage: drush @<alias> scr generate-strongarm.php <module_name> <base_path>');
  exit();
}

if ( empty($args[3]) || ! is_dir($args[3]) || ! is_writable($args[3]) ) {
  drush_set_error('Invalid path.');
  exit();
}
$path = realpath($args[3]);

if ( file_exists($path . '/' . $module_name) && is_dir($path . '/' . $module_name) ) {
  $overwrite = drush_choice(array('Y' => 'Yes', 'N' => 'No'), 'Overwrite directory?');
  if ( $overwrite !== 'Y' ) exit();
} else {
  mkdir($path . '/' . $module_name);
}

//////////////////////////////// FEATURES
drush_print("Generating $module_name.features.inc.");
$features_inc = <<<EOT
<?php
/**
 * @file
 * $module_name.features.inc
 */

/**
 * Implements hook_ctools_plugin_api().
 */
function {$module_name}_ctools_plugin_api() {
  list(\$module, \$api) = func_get_args();
  if (\$module == "strongarm" && \$api == "strongarm") {
    return array("version" => "1");
  }
}

EOT;
$features_inc_file = $path . '/' . $module_name . '/' . $module_name . ".features.inc";
file_put_contents($features_inc_file, $features_inc);

//////////////////////////////// STRONGARM
module_load_include('inc', 'features', 'includes/features.ctools');
$strongarm = strongarm_export_all();

drush_print("Generating $module_name.strongarm.inc.");
$strongarm_inc = <<<BEFORE
<?php
/**
 * @file
 * $module_name.strongarm.inc
 */

/**
 * Implements hook_strongarm().
 */
function {$module_name}_strongarm() {

BEFORE;
if ( ! empty($strongarm) ) $strongarm_inc .= $strongarm['strongarm'] . "\n";
$strongarm_inc .= <<<AFTER
}

AFTER;
$strongarm_inc_file = $path . '/' . $module_name . '/' . $module_name . ".strongarm.inc";
file_put_contents($strongarm_inc_file, $strongarm_inc);


//////////////////////////////// INFO
eval($strongarm['strongarm']);

drush_print("Generating $module_name.info.");
$info = <<<INFOPREFIX
name = $module_name
description = Strongarm variables for site.

core = 7.x
package = Features
dependencies[] = ctools
dependencies[] = strongarm
features[ctools][] = strongarm:strongarm:1
features[features_api][] = api:1

INFOPREFIX;

foreach ( $export as $name => $value ) {
  $info .= "features[variable][] = $name\n";
}
$info_file = $path . '/' . $module_name . '/' . $module_name . ".info";
file_put_contents($info_file, $info);


//////////////////////////////// MODULE
drush_print("Generating $module_name.module.");
$module = <<<MODULE
<?php
/**
 * @file
 * Code for the $module_name feature.
 */

include_once '$module_name.features.inc';

MODULE;
$module_file = $path . '/' . $module_name . '/' . $module_name . ".module";
file_put_contents($module_file, $module);

function strongarm_export_all() {
  ctools_include('export');
  $schema = ctools_export_get_schema('variable');
  $code = '  $export = array();'."\n\n";
  $identifier = $schema['export']['identifier'];
  $result = db_select('variable', 'v')
    ->fields('v', array('name', 'value'))
    ->orderBy('name')
    ->execute();
  foreach ($result as $object) {
    $object = _ctools_export_unpack_object($schema, $object);
    $code .= _ctools_features_export_crud_export('variable', $object, '  ');
    $code .= "  \$export[" . ctools_var_export($object->name) . "] = \${$identifier};\n\n";
  }
  $code .= '  return $export;';

  return array($schema['export']['default hook'] => $code);
}


