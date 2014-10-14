<?php

include_once "lib/progress.inc";

$args = drush_get_arguments();
if ( ! isset($args[2]) ) {
  drush_set_error('Usage: drush scr save-all-nodes-in-types.php <content_type1[,content_type2,content_type3...]>');
  exit();
}

// filter out stuff that isn't a content type
$content_types = explode(',', $args[2]);
$content_types = array_filter($content_types, 'node_type_load');

$query = new EntityFieldQuery();
$query
  ->entityCondition('entity_type', 'node')
  ->entityCondition('bundle', $content_types, 'IN');
$results = $query->execute();

$nodes = node_load_multiple(array_keys($results['node']));
foreach ( $nodes as $node ) {
  display_text_progress_bar(count($nodes));
  node_save($node);
}
