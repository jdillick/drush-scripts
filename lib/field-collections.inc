<?php

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

  return $field_collections;
}

function nodes_with_field_collections($field_collections = array(), $include_bundles = array()) {
  $nids = array();
  foreach ( $field_collections as $field_collection => $bundles ) {
    // skip field collections that aren't included
    if ( $include_bundles && ! ($bundles = array_intersect($include_bundles, $bundles)) ) continue;

    foreach ( $bundles as $bundle ) {
      // if bundle is a field_collection, skip
      if ( ($field = field_info_field($bundle)) && 'field_collection' == $field['type'] ) continue;
        $query = new EntityFieldQuery();
        $query
          ->entityCondition('entity_type', 'node')
          ->entityCondition('bundle', $bundle)
          ->fieldCondition($field_collection, 'value', 'NULL', '<>');
        $results = $query->execute();

        if ( $results ) {
          $nids = array_merge($nids, array_keys($results['node']));
        }
    }
  }

  sort($nids, SORT_NUMERIC);
  return array_unique($nids);
}

function get_nested_field_collection_instances() {
  $field_collections = get_all_field_collections();
  $nested_collections = array();

  foreach ( $field_collections as $field_name => $bundles ) {
    foreach ( $bundles as $bundle ) {
      if ( ($field = field_info_field($bundle)) && 'field_collection' == $field['type'] ) {

        $nested_collections[] = field_info_instance('field_collection_item', $field_name, $bundle);
      }
    }
  }

  return $nested_collections;
}

function bundle_field_collections($field_collections = array(), $bundle ) {
  $collections = array();
  foreach ( $field_collections as $field_collection => $bundles ) {
    if ( in_array($bundle, $bundles) ) $collections[] = $field_collection;
  }
  return $collections;
}

function field_collection_replacement($field_collection) {
  $content_type = strtr($field_collection, array(
    'field_' => '',
    'hub_' => '',
  ));

  return array(
    'content_type' => $content_type,
    'ct_name' => ucwords(str_replace('_', ' ', $content_type)),
    'fc_name' => ucwords(str_replace('_', ' ', $field_collection)),
    'er_field' => $content_type . '_er',
  );
}

function field_collection_replacements($field_collections = array()) {
  $replacements = array();
  foreach ( $field_collections as $field_collection ) {
    $replacements[$field_collection] = field_collection_replacement($field_collection);
  }
  return $replacements;
}

function get_field_collection_items_from_node($node) {
    $field_collection_item_ids = array();
    $node_wrapper = entity_metadata_wrapper('node', $node);

    $bundle_field_collections = bundle_field_collections(get_all_field_collections(), $node->type);
    foreach ( $bundle_field_collections as $bundle_field_collection ) {
      $field_collection_items = $node_wrapper->$bundle_field_collection;
      if ( ! is_a($field_collection_items, 'EntityListWrapper') ) {
        $field_collection_items = array($field_collection_items);
      }

      foreach ( $field_collection_items as $field_collection_item ) {
        if ( ($item = $field_collection_item->value()) && $item->item_id ) {
          $field_collection_item_ids[] = $item->item_id;
        }
      }
    }

    return $field_collection_item_ids;
}
