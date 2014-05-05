<?php

$args = drush_get_arguments();
$feed = array_pop($args);
if ( ! $feed ||  ! feeds_importer($feed)->load() ) {
  die("$feed is bad feed.");
}

$items = db_query("SELECT entity_id FROM {feeds_item} WHERE id = :id", array(':id' => $feed));
$nids = array();
foreach ( $items as $item ) {
  $nids[] = $item->entity_id;
}

$node = node_load($item->entity_id);

$gaps = db_select('node', 'n')
  ->fields('n', array('nid', 'title'))
  ->condition('n.nid', $nids, 'NOT IN')
  ->condition('n.type', $node->type, '=')
  ->execute()
  ->fetchAllAssoc('nid');

$link = mysqli_connect("127.0.0.1","root","","hub.highlights.com") or die("Error: " . mysqli_error($link));
$query = "SELECT nid, title from node where type='" . $node->type . "'";
$result = $link->query($query);

while ( $row = mysqli_fetch_array($result) ) {
  $hub[$row['nid']] = $row['title'];
}

// $source = db_select('feeds_source', 's')
//   ->fields('s', array('source'))
//   ->condition('id',$feed,'=')
//   ->execute()
//   ->fetchAssoc();
// $url = drupal_parse_url($source['source']);
// unset($url['query']['display_id']);
// $url = url($url['path'], array('query' => $url['query']));
// $result = drupal_http_request($url);
// $data = json_decode($result->data);
// $hub = array();
// foreach ( $data as $node ) {
//   $hub[$node->nid] = $node->title;
// }
// print_r($hub);
// echo "\n";

if ( count($gaps) ) {
  echo "||nid||Title||HUB NID||\n";
}
$items = array();
foreach ( $gaps as $gap ) {
  extract(get_object_vars($gap));
  $hub_nid = array_search($title, $hub);
  if ( TRUE || $hub_nid ) {
    $item = array(
      'entity_type' => 'node',
      'entity_id' => $nid,
      'id' => $feed,
      'feed_nid' => 0,
      'imported' => mktime(),
      'url' => '',
      'guid' => $hub_nid,
    );
    $item['hash'] = hash('md5', serialize($item));
    $items[$nid] = $item;
  } // $map[$nid] = $hub_nid;
  echo "|$nid|$title|" . ($hub_nid ? $hub_nid : '*none*') . "|\n";
}
echo "\n" . count($gaps) . " missing feeds info\n";

var_export($items);
