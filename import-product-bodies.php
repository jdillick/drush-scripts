<?php

include 'hub-product-bodies.php';
$nodes = node_load_multiple(array_keys($hub_products));
foreach ($nodes as $nid => $node ) {
  $original = $node->field_hub_body['und'][0];
  $new = $hub_products[$nid][0];

  // don't compare text formats
  // only save node if body text differs
  unset($original['format'], $new['format']);

  $diff = array_udiff_assoc($original, $new, 'compare_hub_bodies');

  if ( ! empty($diff) ) {
    echo "$nid: \n";
    echo "from: "; print_r($original);
    echo "\nto: "; print_r($new);
    echo "\n";
    $count++;

    // enforce content_creator text format on the hub
    $hub_products[$nid][0]['format'] = 'content_creator';
    $node->field_hub_body['und'] = $hub_products[$nid];
    node_save($node);
  }
}
echo "$count differences\n out of " . count($hub_products) . "\n";

function compare_hub_bodies ( $hub, $spoke ) {
  // Ignore whitespace differences
  $from = preg_replace('/\s+/', '', $hub);
  $to = preg_replace('/\s+/', '', $spoke);

  if ( $from != $to ) {
    echo 'hub: ' . print_r($hub, TRUE) . "\n";
    echo 'spoke: ' . print_r($spoke, TRUE) . "\n";
    return 1;
  }
  return 0;
}
