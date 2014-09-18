<?php
field_collections_to_content_types();

function field_collections_to_content_types() {
  $collections = get_all_field_collections();
  foreach ( $collections as $fc ) {
    echo "\n==================== $fc to content type ====================\n";
    $content_type = save_field_collection_content_type($fc);
    print_r($content_type);
    copy_fc_field_instances_to_content_type($fc, $content_type->type);
  }
}

function get_all_field_collections() {
  $field_collections = array();
  foreach ( field_info_instances() as $entity_type => $type_bundles ) {
    foreach ( $type_bundles as $bundle => $bundle_instances ) {
      foreach ( $bundle_instances as $field_name => $instance ) {
        $field = field_info_field($field_name);
        if ( $field['type'] == 'field_collection' ) {
          if ( ! in_array($field_name, $field_collections) ) {
            $field_collections[] = $field_name;
          }
        }
      }
    }
  }
  return $field_collections;
}

function save_field_collection_content_type($fc) {
  $content_type = strtr($fc, array(
    'field_' => '',
    'hub_' => '',
  ));
  $ct_name = ucwords(str_replace('_', ' ', $content_type));
  $fc_name = ucwords(str_replace('_', ' ', $fc));

  // Create New Content Type
  echo "Creating/Updating content type $content_type to replace $fc.\n";
  $new_ct = (object) array(
    'type' => $content_type,
    'orig_type' => $content_type,
    'base' => 'node_content',
    'name' => $ct_name,
    'description' => t('Replaces @ct_name field collection.', array('@ct_name' => $ct_name)),
    'locked' => TRUE,
    'custom' => TRUE,
    'disabled' => FALSE,
    'has_title' => FALSE,
    'module' => 'node',
  );
  node_type_save($new_ct);
  return node_type_load($content_type);
}

function copy_fc_field_instances_to_content_type($fc, $content_type) {
  // Get field instances of field collection, copy instance to new ct
  $field_instances_info = field_info_instances('field_collection_item', $fc);
  $ct_instances_info = field_info_instances('node', $content_type);
  foreach ( $field_instances_info as $instance_name => &$instance ) {
    if ( in_array($instance_name, array_keys($ct_instances_info)) ) {
      echo "Instance $instance_name exists in {$content_type}\n";
      continue;
    }
    echo "Creating new instance $instance_name in {$content_type}\n";
    unset($instance['id'], $instance['field_id']);
    $instance['entity_type'] = 'node';
    $instance['bundle'] = $content_type;
    $new_instance = field_create_instance($instance);
  }
}
