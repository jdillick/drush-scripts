<?php

include_once 'lib/doppel.inc';

$args = drush_get_arguments();
if ( ! isset($args[2]) ) {
  drush_set_error('Usage: drush scr detach-doppels.php <doppel_type1[,doppel_type2,doppel_type3...]>');
  exit();
}

// filter out stuff that isn't a content type or doppel content type
$doppels = explode(',', $args[2]);
$doppels = array_filter($doppels, 'node_type_load');
$doppels = array_filter($doppels, 'hfc_doppel_get_doppel');

foreach ( $doppels as $doppel_name ) {
  delete_doppel_profile($doppel_name);
}
