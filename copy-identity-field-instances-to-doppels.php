<?php

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

/**
 * Get doppel configurations for a list of doppel content types.
 * @see hfc_doppel_get_doppels()
 *
 * @param array $doppel_types list of doppel content types
 * @return array of configuration for each doppel content type
 */
function get_doppel_configuration( $doppel_types = array() ) {
  $doppels = array();
  foreach ( hfc_doppel_get_doppels() as $doppel_type => $config ) {
    if ( in_array($doppel_type, $doppel_types) ) {
      $doppels[$doppel_type] = $config;
    }
  }

  return $doppels;
}

/**
 * Copy field instances from one bundle to another.
 *
 * @param  $from_bundle source node bundle
 * @param  $to_bundle target node bundle
 * @param  $profile the doppel field mappings for existing field instances in source bundle.
 */
function copy_identity_field_instances($from_bundle, $to_bundle, $profile = array()) {
  $from_instances = array();

  foreach ( field_info_instances('node', $from_bundle) as $field_name => $instance ) {
    // skip existing instances
    if ( in_array($field_name, array_keys($profile))) continue;

    if ( ! field_info_instance('node', $field_name, $to_bundle) ) {
      $instance = field_info_instance('node', $field_name, $from_bundle);
      $instance['bundle'] = $to_bundle;
      field_create_instance($instance);

      // Add new field instances to profile
      $profile[$field_name]['identity_field'] =
        $profile[$field_name]['doppel_field'] = $field_name;
    }
  }

  update_doppel_profile($to_bundle, $from_bundle, $profile);
}

/**
 * Update a doppel profile.
 *
 * @param string $doppel_name the name of the doppel content type
 * @param string $doppel_identity the name of the parent/identity content type
 * @param array $doppel_profile updated field mappings for doppel content type
 */
function update_doppel_profile($doppel_name, $doppel_identity, $doppel_profile) {
  db_update('hfc_doppel')
    ->fields(array('doppel_profile' => serialize($doppel_profile)))
    ->condition('doppel_name', $doppel_name)
    ->condition('doppel_identity', $doppel_identity)
    ->execute();
}
