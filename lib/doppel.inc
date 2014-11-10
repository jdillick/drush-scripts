<?php

require_once 'field.inc';
require_once 'progress.inc';

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
 * Get the doppel configuration of content types that have one of these
 * identities.
 *
 * @param array $identity_types list of content types
 * @return array of configurations for each identity type.
 */
function get_doppelled_configurations( $identity_types = array() ) {
  $doppels = array();
  foreach ( hfc_doppel_get_doppels() as $doppel_type => $config ) {
    if ( in_array($config->doppel_identity, $identity_types) ) {
      $doppels[$config->doppel_identity][$doppel_type] = $config;
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
  echo "Copying identity field instances from $from_bundle to $to_bundle\n";
  $from_instances = array();

  $fields = field_info_instances('node', $from_bundle);
  display_text_progress_bar(count($fields), TRUE);

  foreach ( $fields as $field_name => $instance ) {
    display_text_progress_bar(count($fields));

    // skip existing instances
    foreach ( $profile as $doppel_field_name => $config ) {
      if ( $config['identity_field'] == $field_name ) {
        continue 2;
      }
    }

    copy_field_instance($field_name, $from_bundle, $to_bundle);

    // Add new field instances to profile
    $profile[$field_name]['identity_field'] =
      $profile[$field_name]['doppel_field'] = $field_name;
  }

  update_doppel_profile($to_bundle, $from_bundle, $profile);
}

function delete_doppel_profile($doppel_name) {
  $doppel = hfc_doppel_get_doppel($doppel_name);
  if ( $doppel ) {
    delete_field($doppel->doppel_identity_field, $doppel_name);
  }

  hfc_doppel_delete_doppel($doppel_name);
}
