<?php
field_collections_to_content_types();

function field_collections_to_content_types() {
  $collections = get_all_field_collections();

  foreach ( $collections as $fc => $bundles ) {
    echo "\n==================== $fc to content type ====================\n";
    $content_type = save_field_collection_content_type($fc);
    print_r($content_type);
    copy_fc_field_instances_to_content_type($fc, $content_type->type);
    create_er_fields($bundles, $content_type->type);
  }
}

function get_all_field_collections() {
  $field_collections = array();
  foreach ( field_info_instances() as $entity_type => $type_bundles ) {
    foreach ( $type_bundles as $bundle => $bundle_instances ) {
      foreach ( $bundle_instances as $field_name => $instance ) {
        $field = field_info_field($field_name);
        if ( $field['type'] == 'field_collection' ) {
          $field_collections[$field_name][] = $instance['bundle'];
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
    unset($instance['id']);
    $instance['entity_type'] = 'node';
    $instance['bundle'] = $content_type;
    $new_instance = field_create_instance($instance);
  }
}

function create_er_fields($source_bundles, $target_bundle) {
  $field_name = $target_bundle . '_er';

  $field = create_er_base_field($field_name, $target_bundle);
  echo "Creating/updating entity reference base field $field_name\n";

  foreach ( $source_bundles as $bundle ) {
    $instance = create_er_field_instance($field_name, $info['field_id'], $bundle);
    echo "Creating/updating entity reference field instance $field_name in $bundle\n";
  }
}
function create_er_base_field($field_name, $target_bundle) {
  $er_field = array(
    'field_name' => $field_name,
    'type' => 'entityreference',
    'translatable' => '0',
    'entity_types' => array(
    ),
    'settings' => array(
      'target_type' => 'node',
      'handler' => 'base',
      'handler_settings' => array(
        'target_bundles' => array(
          $target_bundle => $target_bundle,
        ),
        'sort' => array(
          'type' => 'none',
        ),
        'behaviors' => array(
          'views-select-list' => array(
            'status' => 0,
          ),
        ),
      ),
    ),
  );

  $info = field_info_field($field_name);
  if ( ! isset($info) ) {
    return field_create_field($er_field);
  }
  return field_update_field($er_field);
}

function create_er_field_instance($field_name, $field_id, $bundle) {
  $er_instance = array(
    'field_name' => $field_name,
    'field_id' => $field_id,
    'entity_type' => 'node',
    'bundle' => $bundle,
    'label' => ucwords(str_replace('_', ' ', $field_name)),
    'widget' => array(
      'weight' => '42',
      'type' => 'inline_entity_form',
      'module' => 'inline_entity_form',
      'active' => 1,
      'settings' => array(
        'fields' => array(
        ),
        'type_settings' => array(
          'allow_existing' => 0,
          'match_operator' => 'CONTAINS',
          'delete_references' => 0,
          'override_labels' => 0,
          'label_singular' => 'node',
          'label_plural' => 'nodes',
        ),
      ),
    ),
    'settings' => array(
      'user_register_form' => false,
    ),
    'display' => array(
      'default' => array(
        'label' => 'above',
        'type' => 'entityreference_label',
        'settings' => array(
          'link' => false,
        ),
        'module' => 'entityreference',
        'weight' => 1,
      ),
    ),
    'required' => 0,
    'description' => '',
  );

  $instance = field_info_instance('node', $field_name, $bundle);
  if ( ! isset($instance) ) {
    return field_create_instance($er_instance);
  }
  return field_update_instance($er_instance);
}
