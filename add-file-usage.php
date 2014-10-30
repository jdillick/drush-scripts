<?php

include_once "lib/field.inc";
include_once "lib/progress.inc";

$args = drush_get_arguments();
if ( ! isset($args[2]) ) {
  drush_set_error('Usage: drush scr save-all-nodes-in-types.php <content_type1[,content_type2,content_type3...]>');
  exit();
}

// filter out stuff that isn't a content type
$content_types = explode(',', $args[2]);
$content_types = array_filter($content_types, 'node_type_load');

foreach ( $content_types as $content_type ) {
  $file_fields = file_fields($content_type);
  foreach ( $file_fields as $file_field ) {
    fix_file_usage($file_field, $content_type);
  }
}

function fix_file_usage( $file_field, $content_type ) {
  echo "field: $file_field bundle: $content_type\n";
  $query = new EntityFieldQuery;
  $result = $query
    ->entityCondition('entity_type', 'node')
    ->entityCondition('bundle', $content_type)
    ->fieldCondition($file_field, 'fid', 'NULL', '<>')
    ->execute();

  if ( ! $result['node'] ) {
    echo "No nodes in $content_type with files\n";
    return;
  }

  $count = count($result['node']);
  display_text_progress_bar($count, TRUE);
  foreach ( node_load_multiple(array_keys($result['node'])) as $node ) {
    display_text_progress_bar($count);
    $items = field_get_items('node', $node, $file_field);
    if ( ! $items ) {
      echo "No file field items of type $file_field in node $node->nid!\n";
    }
    foreach ( $items as $item ) {
      $file = (object) $item;
      if ( isset($file->fid) && ! has_file_usage($file, $node) ) {
        file_usage_add($file , 'file', 'node', $node->nid);
      }
    }
  }
}

function has_file_usage($file, $node) {
  $file_usage = file_usage_list($file);
  $used = FALSE;
  foreach ( $file_usage as $module => $types ) {
    foreach ( $types as $entity => $usage ) {
      foreach ( $usage as $id => $count ) {
        if ( $node->nid == $id && $count ) {
          $used = TRUE;
        }
      }
    }
  }

  return $used;
}
