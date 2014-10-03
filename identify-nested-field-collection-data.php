<?php

require dirname(__FILE__) . '/lib/field_collections.inc';

$field_collections = get_all_field_collections();
foreach ( $field_collections as $field_name => $bundles ) {
  foreach ( $bundles as $bundle ) {
    if ( ($field = field_info_field($bundle)) && 'field_collection' == $field['type'] ) {
      echo "$field_name is a field collection in a field collection $bundle.\n";
        $query = new EntityFieldQuery();
        $query
          ->entityCondition('entity_type', 'field_collection_item')
          ->entityCondition('bundle', $bundle)
          ->fieldCondition($field_name, 'value', 'NULL', '<>');
        $results = $query->execute();
        print_r($results);

        $items = array();
        if ( isset($results['field_collection_item']) ) {
          foreach ( $results['field_collection_item'] as $result ) {
            $items[] = $result->item_id;
          }
        }

        if ( $items ) {
          $query = new EntityFieldQuery();
          $query
            ->entityCondition('entity_type', 'node')
            ->fieldCondition($bundle, 'value', $items, 'IN');
          echo "Nodes with nested $field_name in $bundle:\n";
          print_r($query->execute());
        }
    }
  }
}
