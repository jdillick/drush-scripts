<?php
/**
 * @file
 * field_collections_to_content_types.php
 *
 * @brief Eliminated field collections.
 * @details Transforms site field collections into replacement content types.
 *   Adds entity reference fields to the bundles of field collection item
 *   instances, and targets the replacement content types.
 */

field_collections_to_content_types();

/**
 * Iterate through field collections:
 * - Create new replacement content types
 * - Copy field instances to new content types
 * - Create entity reference fields for each field collection instance in their
 *   source bundles.
 */
function field_collections_to_content_types() {
  $collections = get_all_field_collections();

  foreach ( $collections as $field_collection => $bundles ) {
    echo "\n==================== $field_collection to content type ====================\n";
    $content_type = save_field_collection_content_type($field_collection);
    print_r($content_type);
    copy_fc_field_instances_to_content_type($field_collection, $content_type->type);
    create_er_fields($bundles, $content_type->type);
  }
}

/**
 * Get all field collections.
 *
 * @return array An array indexed by field collections, and array of bundles with
 *   those field collections.
 */
function get_all_field_collections() {
  $field_collections = array();

  // loop through all the field instances, by entity and bundle, looking for
  // field collections.
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

  echo "Field collection bundles:\n";
  print_r($field_collections);

  return $field_collections;
}

/**
 * Give a field collection, creates or updates a new node content type as a replacement.
 *
 * @return stdClass Saved node content type.
 */
function save_field_collection_content_type($field_collection) {
  $content_type = strtr($field_collection, array(
    'field_' => '',
    'hub_' => '',
  ));
  $ct_name = ucwords(str_replace('_', ' ', $content_type));
  $fc_name = ucwords(str_replace('_', ' ', $field_collection));

  // Create New Content Type
  echo "Creating/Updating content type $content_type to replace $field_collection.\n";
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
    'title_label' => 'Title',
    'module' => 'node',
  );
  node_type_save($new_ct);
  return node_type_load($content_type);
}

/**
 * Copies field collection fields to replacement content type.
 */
function copy_fc_field_instances_to_content_type($field_collection, $content_type) {
  // Get field instances of field collection, copy instance to new ct
  $field_instances_info = field_info_instances('field_collection_item', $field_collection);
  $content_type_instances_info = field_info_instances('node', $content_type);

  // Loop through field instances in field collection, copy to new content type
  foreach ( $field_instances_info as $instance_name => &$instance ) {
    // skip instance that already exist
    if ( in_array($instance_name, array_keys($content_type_instances_info)) ) continue;

    // skip field collection instances (don't want field collections in new content type)
    if ( ($field = field_info_field($instance_name)) && 'field_collection' == $field['type'] ) continue;

    echo "Creating new instance $instance_name in {$content_type}\n";
    unset($instance['id']);
    $instance['entity_type'] = 'node';
    $instance['bundle'] = $content_type;
    $new_instance = field_create_instance($instance);
  }
}

/**
 * Create entity reference field in each bundle that currently contains a field colleciton.
 */
function create_er_fields($source_bundles, $entity_ref_target_bundle) {
  $field_name = $entity_ref_target_bundle . '_er';

  $field = create_er_base_field($field_name, $entity_ref_target_bundle);
  echo "Creating/updating entity reference base field $field_name\n";

  foreach ( $source_bundles as $bundle ) {
    // if the field collection bundle is another field collection (nested fc)
    if ( ($info = field_info_field($bundle)) && $info['type'] == 'field_collection' ) {

      // create the er in the new ct, not the old fc
      $bundle = strtr($bundle, array(
        'field_' => '',
        'hub_' => '',
      ));
    }

    $instance = create_er_field_instance($field_name, $info['field_id'], $bundle);
    echo "Creating/updating entity reference field instance $field_name in $bundle\n";
  }
}

/**
 * Create new entity reference field base (named similarly to existing field collection).
 * Sets the target bundle of the entity reference to the field collection replacement content type.
 */
function create_er_base_field($field_name, $entity_ref_target_bundle) {
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
          $entity_ref_target_bundle => $entity_ref_target_bundle,
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

/**
 * Create an infinity cardinality entity reference field instance in the source
 * bundle.
 */
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