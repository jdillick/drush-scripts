<?php
/**
 * @file
 * strongarm2conf.php
 *
 * Converts strongarm module into conf variables.
 */

global $my_args;
$my_args = $args;

if ( ! isset($args[1]) || ! ($strongarm_module_name = $args[1]) ) {
  drush_set_error('Usage: drush @<alias> scr strongarm2conf.php <strongarm_module_name> ["comma-separated-blacklist"]');
} else if ( ! ($strongarm_module_path = drush_core_drupal_directory($strongarm_module_name))
  || ! is_readable($strongarm_module_path . '/' . $strongarm_module_name . '.strongarm.inc') ) {
  drush_set_error(t('Module @module does not exist or is not a strongarm feature.', array(
      '@module' => $strongarm_module_name,
    )));
} else {
  include $strongarm_module_path . '/' . $strongarm_module_name . '.strongarm.inc';

  // Require ctools_var_export()
  if (module_exists('ctools')) module_load_include('inc', 'ctools', 'includes/export');
  else require 'ctools/includes/export.inc';

  $strongarms_func = $strongarm_module_name . '_strongarm';
  $strongarms = $strongarms_func();
  $strongarms = array_intersect_key($strongarms,
    array_flip(array_filter(array_keys($strongarms), 'strongarm2conf_blacklist')));
  foreach ( $strongarms as $key => $Object ) {
    echo "\$conf['$key'] = " . ctools_var_export($Object->value) . ";\n";
  }
}

function strongarm2conf_blacklist($strongarm) {
  global $my_args;

  $blacklist = array();

  if ( isset($my_args[2]) ) {
    $blacklist = explode(',', $my_args[2]);
  }

  if ( is_readable(getenv('HOME') . '/.strongarm2conf_blacklist') ) {
    $file_blacklist = file_get_contents(getenv('HOME') . '/.strongarm2conf_blacklist');
    $blacklist = array_filter(array_merge($blacklist, explode("\n", $file_blacklist)));
  }

  foreach ($blacklist as $item ) {
    $pattern = "/" . $item . "/";
    if (preg_match($pattern, $strongarm)) {
      return false;
    }
  }

  return true;
}
