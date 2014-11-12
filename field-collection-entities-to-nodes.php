<?php
/**
 * @file
 * field_collection_entities_to_nodes.php
 *
 * Assuming that field_collection_to_content_types.php has been run previously,
 * this script will create nodes for each field collection item entity, and
 * populate new entity reference fields with targets to new nodes.
 */

require 'lib/field-collections.inc';
require 'lib/progress.inc';

$args = drush_get_arguments();
$bundles = array();
if ( isset($args[2]) ) {
  $bundles = explode(',', $args[2]);
  $bundles = array_filter($bundles, 'node_type_load');
  if ( ! $bundles ) {
    drush_set_error('Usage: drush scr field_collection_entities_to_nodes.php [fc_bundle[,fc_bundle2...]]');
    exit();
  }
  echo "Converting field collections entities found in:\n";
  foreach ( $bundles as $bundle ) {
    echo "* $bundle\n";
  }
  echo "\n";
}

try {
  field_collection_entities_to_nodes($bundles);
} catch ( Exception $e ) {
  print_r($e);
}

function field_collection_entities_to_nodes($include_bundles = array()) {
  $all_field_collections = get_all_field_collections();
  $nodes = node_load_multiple(nodes_with_field_collections($all_field_collections, $include_bundles));
  foreach ( $nodes as $nid => $node ) {
    display_text_progress_bar(count($nodes));

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
  $node->language = LANGUAGE_NONE;
  $node->status = 1;
  $node_wrapper = entity_metadata_wrapper('node', $node);

  $item_wrapper = entity_metadata_wrapper('field_collection_item', $item);
  $item_instances = field_info_instances('field_collection_item', $item->field_name);
  $files = array();

  foreach ( $item_instances as $field_name => $instance ) {
    // only process fields with data
    if ( ($value = $item_wrapper->{$field_name}->value()) ) {

      // recurse if field collection field
      if ( ($field = field_info_field($field_name)) && 'field_collection' == $field['type'] ) {
        $replacement = field_collection_replacement($field_name);
        foreach ( $item_wrapper->{$field_name} as $field_collection_item ) {
          $node_wrapper->{$replacement['er_field']}[] = convert_field_collection_item_to_node($field_collection_item, $replacement['content_type']);
        }
      }
      else {
        // Add image/file fields to list for file usage
        if ( in_array($field['type'], array('image','file')) ) {
          $files[] = $node_wrapper->{$field_name};
        }
        $node_wrapper->{$field_name}->set($value);
      }
    }
  }

  $node_wrapper->save();
  update_file_usage($files, $node_wrapper->nid->value());

  return $node_wrapper->nid->value();
}

/**
 * Add file usage entry for each file in replacement node
 */
function update_file_usage($files, $nid) {
  foreach ( $files as $field ) {
    if ( 'EntityListWrapper' != get_class($field) ) $field = array($field);
    foreach ( $field as $file ) {
      if ( ( $file = (object) $file->value() ) && $file->fid) {
        file_usage_add($file, 'file', 'node', $nid);
      }
    }
  }
}
