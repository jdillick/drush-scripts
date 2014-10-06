<?php

require_once 'field.inc';

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

    copy_field_instance($field_name, $from_bundle, $to_bundle);

    // Add new field instances to profile
    $profile[$field_name]['identity_field'] =
      $profile[$field_name]['doppel_field'] = $field_name;
  }

  update_doppel_profile($to_bundle, $from_bundle, $profile);
}