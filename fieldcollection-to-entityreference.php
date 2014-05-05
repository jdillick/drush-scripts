<?php

// 1. create new node content type to replace FC bundle
// 2. foreach field instance in each FC bundle
//   a. set node CT as bundle
// 3. foreach stored FC entity
//   a. create new node
//   b. set target entity for all stored fields to new nid
//   c. track exisint FC entity id and new nid
// 4. foreach node type with FC field
//   a. add entity reference field
// 5. foreach node with FC field, for each target FC entity
//   a. add target nid to ER field referencing corresponding new nid.

// Gather my conversion arsenal
$fc_entity_info = entity_get_info('field_collection_item');
$replacement_content_types = get_fc_replacement_content_types($fc_entity_info['bundles']);
$fc_entity_attached_field_instances = get_fc_entity_field_instances($fc_entity_info['bundles']);

/**
 * Get field instances that are attached to a field collection entity bundle
 */
function get_fc_entity_field_instances($fc_entity_bundles) {
  $fc_instances = array();
  foreach ( $fc_entity_bundles as $fc_bundle ) {
    $fc_instances[$fc_bundle] = field_read_instances(array('bundle' => $fc_bundle));
  }
  return $fc_instances;
}

/**
 * get configuration array for new content types to replace
 * field collection entity bundles.
 */
function get_fc_replacement_content_types($fc_entity_bundles) {
  $content_types = array();
  foreach ( $fc_entity_bundles as $fc_bundle ) {
    // New content type information
    $content_type_machine_name = str_replace('field_', '', $fc_bundle);
    $content_type_name = implode(' ', array_map('ucfirst', explode('_', $content_type_machine_name)));
    $content_types = array_merge(array(
        $content_type_machine_name => array(
          'name' => $content_type_name,
          'base' => 'node_content',
          'description' => "Automatically created content type from fc entity bundle $fc_bundle.",
          'has_title' => 1,
          'title_label' => t('Title (for admin purposes only).'),
          'help' => '',
        ),
      ), $content_types);
  }
  return $content_types;
}

/**
 * Create nodes to replace stored field collection entities.
 */
function create_fc_er_nodes() {
  $items = entity_load('field_collection_item');
  $nodes = array();
  foreach ( $items as $FieldCollection) {
    print_r($FieldCollection); break;
  }
}

/**
 * Helper utility borrowed from bms module to create new content types.
 * ssh://git@git.highlights.com:7999/~wruvalcaba/bms.git
 */
function fc_to_er_content_type_setup($types = array()){
  foreach ($types as $type_name => $type_info) {
    drupal_set_message(t('Setting up content type ' . $type_name), 'status', FALSE);

    // Set defaults for content type based on features export
    $content_type = node_type_set_defaults($type_info);
    $content_type->type = $type_name;

    // Save the content type to the db if content type doesn't exist
    node_type_save($content_type);

    variable_set('comment_'. $type_name, '1');
    variable_set('node_preview_'. $type_name, 0);
    variable_set('node_submitted_'. $type_name, 0);
    variable_set('node_options_'. $type_name, array(
      0 => 'status',
      1 => 'revision',
    ));
  }
}
