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

function copy_identity_field_instances_to_doppels( $doppel_types = array() ) {
  $config = get_doppel_configuration($doppel_types);
  print_r($config);
  foreach ( $doppel_types as $type ) {
    copy_identity_field_instances($config[$type]->doppel_identity, $type, $config[$type]->doppel_profile);
  }
}

function get_doppel_configuration( $doppel_types = array() ) {
  $doppels = array();
  foreach ( hfc_doppel_get_doppels() as $doppel_type => $profile ) {
    if ( in_array($doppel_type, $doppel_types) ) {
      $doppels[$doppel_type] = $profile;
    }
  }

  return $doppels;
}

function copy_identity_field_instances($from_bundle, $to_bundle, $existing = array()) {
  $from_instances = array();

  foreach ( field_info_instances('node', $from_bundle) as $field_name => $instance ) {
    // skip existing instances
    if ( in_array($field_name, array_keys($existing))) continue;

    if ( ! field_info_instance('node', $field_name, $to_bundle) ) {
      $instance = field_info_instance('node', $field_name, $from_bundle);
      $instance['bundle'] = $to_bundle;
      field_create_instance($instance);

      $existing[$field_name]['identity_field'] =
        $existing[$field_name]['doppel_field'] = $field_name;
    }
  }

  update_doppel_profile($to_bundle, $from_bundle, $existing);
}

function update_doppel_profile($doppel_name, $doppel_identity, $doppel_profile) {
  db_update('hfc_doppel')
    ->fields(array('doppel_profile' => serialize($doppel_profile)))
    ->condition('doppel_name', $doppel_name)
    ->condition('doppel_identity', $doppel_identity)
    ->execute();
}
