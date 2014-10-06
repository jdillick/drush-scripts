<?php
require_once 'lib/doppel.inc';

$args = drush_get_arguments();
if ( ! isset($args[2]) ) {
  drush_set_error('Usage: drush scr copy-identity-field-instances-to-doppels.php <doppel_type1[,doppel_type2,doppel_type3...]>');
  exit();
}

// filter out stuff that isn't a content type or doppel content type
$doppels = explode(',', $args[2]);
$doppels = array_filter($doppels, 'node_type_load');
$doppels = array_filter($doppels, 'hfc_doppel_get_doppel');

copy_identity_field_instances_to_doppels($doppels);

/**
 * Given a list of doppel content types, all missing field instances from the
 * identity content type are copied to the doppel content type.
 *
 * @param array $doppel_types list of doppel content types
 */
function copy_identity_field_instances_to_doppels( $doppel_types = array() ) {
  $config = get_doppel_configuration($doppel_types);
  print_r($config);
  foreach ( $doppel_types as $type ) {
    copy_identity_field_instances($config[$type]->doppel_identity, $type, $config[$type]->doppel_profile);
  }
}
