<?php
/**
 * @file
 * field_collection_entities_to_nodes.php
 *
 * Assuming that field_collection_to_content_types.php has been run previously,
 * this script will create nodes for each field collection item entity, and
 * populate new entity reference fields with targets to new nodes.
 */

require 'lib/field_collections.inc';

field_collection_entities_to_nodes();

function field_collection_entities_to_nodes() {
  $all_field_collections = get_all_field_collections();
  $nodes = node_load_multiple(nodes_with_field_collections($all_field_collections));
  foreach ( $nodes as $nid => $node ) {
    $field_collections = bundle_field_collections($all_field_collections, $node->type);
    $replacements = field_collection_replacements($field_collections);
    $node_wrapper = entity_metadata_wrapper('node', $node);
    foreach ( $field_collections as $field_collection ) {
      $nids = array();
      $replacement = $replacements[$field_collection];
      if ( $node->$field_collection ) {
        foreach ( $node_wrapper->{$field_collection}->value() as $field_collection_item ) {

          // skip field collections that have been converted already
          if ( isset($converted_field_collection_items[$field_collection_item->item_id]) ) continue;

          $nid = convert_field_collection_item_to_node($field_collection_item, $replacement['content_type']);
          $node_wrapper->{$replacement['er_field']}[] = $nid;
        }
      }
    }

    $node_wrapper->save();
  }

}

function convert_field_collection_item_to_node($item, $replacement_type) {
  $node = new stdClass;
  $node->type = $replacement_type;
  $node->title = "Item {$item->item_id}.";
  $node_wrapper = entity_metadata_wrapper('node', $node);

  $item_wrapper = entity_metadata_wrapper('field_collection_item', $item);
  $item_instances = field_info_instances('field_collection_item', $item->field_name);

  foreach ( $item_instances as $field_name => $instance ) {
    // only process fields with data
    if ( $item_wrapper->{$field_name}->value() ) {

      // recurse if field collection field
      if ( ($field = field_info_field($field_name)) && 'field_collection' == $field['type'] ) {
        $replacement = field_collection_replacement($field_name);
        foreach ( $item_wrapper->{$field_name} as $field_collection_item ) {
          $node_wrapper->{$replacement['er_field']}[] = convert_field_collection_item_to_node($field_collection_item, $replacement['content_type']);
        }
      }
      else {
        $node_wrapper->{$field_name}->set($item_wrapper->{$field_name}->value());
      }
    }
  }

  $node_wrapper->save();
  return $node_wrapper->nid->value();
}
