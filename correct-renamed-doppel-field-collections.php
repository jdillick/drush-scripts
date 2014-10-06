<?php
/**
 * @file
 * correct-renamed-doppel-field-collections.php
 */

require_once 'lib/doppel.inc';
require_once 'lib/field.inc';

$args = drush_get_arguments();
if ( ! isset($args[2]) ) {
  drush_set_error('Usage: drush scr correct-renamed-doppel-field-collections.php <doppel_type1[,doppel_type2,doppel_type3...]>');
  exit();
}

// filter out stuff that isn't a content type or doppel content type
$doppels = explode(',', $args[2]);
$doppels = array_filter($doppels, 'node_type_load');
$doppels = array_filter($doppels, 'hfc_doppel_get_doppel');

correct_renamed_doppel_field_collections($doppels);

function correct_renamed_doppel_field_collections($doppels) {
  foreach ( get_doppel_configuration($doppels) as $doppel_name => $config ) {
    foreach ( $config->doppel_profile as $field_name => $map ) {
      $is_changed_name = ($field_name != $map['identity_field']);
      $is_field_collection = ('field_collection' == get_field_type($field_name));

      if ( $is_changed_name && $is_field_collection ) {
        echo "$field_name in $doppel_name is renamed field collection. Fixing.\n";

        copy_field_instance($map['identity_field'], $config->doppel_identity, $doppel_name);
        replace_doppel_field($doppel_name, $field_name, $map['identity_field'], $config);
        delete_field($field_name, $doppel_name);
      }
    }
  }
}

function replace_doppel_field($doppel_name, $old_field, $new_field, $config) {
  $config->doppel_profile[$new_field] = array(
    'identity_field' => $new_field,
    'doppel_field' => $new_field,
  );

  unset($config->doppel_profile[$old_field]);
  update_doppel_profile($doppel_name, $config->doppel_identity, $config->doppel_profile);
}
