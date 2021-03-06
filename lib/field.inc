<?php

function copy_field_instance($field_name, $from_bundle, $to_bundle, $from_entity_type = 'node', $to_entity_type = 'node') {

  if ( ! field_info_instance($to_entity_type, $field_name, $to_bundle) ) {
    $instance = field_info_instance($from_entity_type, $field_name, $from_bundle);
    $instance['entity_type'] = $to_entity_type;
    $instance['bundle'] = $to_bundle;

    field_create_instance($instance);
  }
}

function get_field_type($field_name) {
  $field_info = field_info_field($field_name);
  return $field_info['type'];
}

function delete_field($field_name, $bundle, $entity_type = 'node') {
  $instance = field_info_instance($entity_type, $field_name, $bundle);
  field_delete_instance($instance, TRUE);
  field_purge_instance($instance);
}

function file_fields ($bundle_name, $entity_type = 'node') {
  $file_fields = array();
  $instances = field_info_instances($entity_type, $bundle_name);
  foreach ( $instances as $field_name => $instance ) {
    if ( in_array(get_field_type($field_name), array('file', 'image')) ) {
      $file_fields[] = $field_name;
    }
  }

  return $file_fields;
}

function delete_field_group($group_name, $bundle_name, $entity_type = 'node', $mode = 'form') {
  if ( ($group = field_group_load_field_group($group_name, $entity_type, $bundle_name, $mode)) ) {
    ctools_include('export');
    field_group_group_export_delete($group, FALSE);
  }
}
