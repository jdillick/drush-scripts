<?php

$doppels = hfc_doppel_get_doppels();
$products = array();
foreach ( $doppels as $name => $doppel ) {
  if ( $doppel->doppel_identity == 'product' ) {
    $count = get_node_count($name);
    $fields = field_info_instances('node', $name);
    foreach ($fields as $field_name => $instance ) {
      if ( strpos($field_name, 'body') !== FALSE ) $body_field = $field_name;
    }

    $nodes = node_load_multiple(array(), array('type' => $name));

    foreach ( $nodes as $nid => $node ) {
      $field_items = field_get_items('node', $node, $doppel->doppel_identity_field);
      $identity_nid = $field_items[0]['target_id'];
      $feeds_item = feeds_item_info_load('node', $identity_nid);

      if ( empty($feeds_item->id) ) continue;
      $body = field_get_items('node', $node, $body_field);
      $products[$feeds_item->guid] = $body;
    }
  }
}

var_export($products);

function get_node_count($content_type) {
     $query = "SELECT COUNT(*) amount FROM {node} n ".
              "WHERE n.type = :type";
     $result = db_query($query, array(':type' => $content_type))->fetch();
     return $result->amount;
}
