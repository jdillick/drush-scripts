<?php
/**
 * @file
 * delete_field_collections.php
 */

require 'lib/field-collections.inc';

$args = drush_get_arguments();
$bundles = array();
if ( isset($args[2]) ) {
  $bundles = explode(',', $args[2]);
  $bundles = array_filter($bundles, 'node_type_load');
  if ( ! $bundles ) {
    drush_set_error('Usage: drush scr delete_field_collections.php [fc_bundle[,fc_bundle2...]]');
    exit();
  }
  echo "Deleting field collections entities and field instances found in:\n";
  foreach ( $bundles as $bundle ) {
    echo "* $bundle\n";
  }
  echo "\n";
}

delete_field_collections($bundles);

function delete_field_collections($include_bundles = array()) {
  $processed = array();

  $field_collections = get_all_field_collections();
  foreach ( $field_collections as $field_collection => $bundles ) {
    // skip bundles that aren't included
    if ( $include_bundles && ! array_intersect($include_bundles, $bundles) ) continue;

    delete_field_collection_entities($field_collection, $bundles);
    delete_field_collection_instances($field_collection, $bundles);
  }
}

function delete_field_collection_entities($field_collection, $bundles) {
  $field_collections = array($field_collection => $bundles);

  $nodes = node_load_multiple(nodes_with_field_collections($field_collections, $bundles));
  $field_collection_item_ids = array();

  foreach ( $nodes as $nid => $node ) {
    $fc_item_ids = get_field_collection_items_from_node($node);
    $field_collection_item_ids = array_merge($field_collection_item_ids, $fc_item_ids);
  }

  echo t("Deleting @count @fc items in bundles @bundles\n", array(
    '@count' => count($field_collection_item_ids),
    '@fc' => $field_collection,
    '@bundles' => implode(',', $bundles),
  ));

  entity_delete_multiple('field_collection_item', $field_collection_item_ids);
}


function delete_field_collection_instances($field_collection, $bundles) {
  foreach ( $bundles as $bundle ) {
    echo "Deleting field instance $field_collection in bundle $bundle\n";
    $instance = field_info_instance('node', $field_collection, $bundle);
    field_delete_instance($instance);
    field_purge_instance($instance);
  }
}
