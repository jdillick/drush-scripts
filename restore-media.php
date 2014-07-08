<?php

$fields = db_select('field_config', 'fc')
  ->condition('fc.type', 'image', '=')
  ->fields('fc', array('field_name'))
  ->execute()
  ->fetchAll(PDO::FETCH_COLUMN);

$fids = array();
foreach ( $fields as $field ) {
  $fids = array_merge(db_select('field_data_' . $field, 'fd')
      ->fields('fd', array($field . '_fid'))
      ->execute()
      ->fetchAll(PDO::FETCH_COLUMN), $fids);
}

$files = file_load_multiple($fids);

$count = 0;
foreach ( $files as $file ) {
  $url = file_create_url($file->uri);
  $file = drupal_realpath($file->uri);
  if ($url && $file) replace_media($url, $file);

  echo floor($count++ / count($files) * 100) . "% Complete\r";
}

function replace_media ($url, $file) {
  $url = str_replace($_ENV['ENVTYPE'] . '.', '', $url);
  if ( ! file_exists($file) ) {
    $base = dirname($file);
    if ( ! is_dir($base) ) {
      mkdir($base, 02777, TRUE);
    }

    $contents = file_get_contents($url);
    if ( ! empty($contents) ) {
      file_put_contents($file, $contents);
    } else {
      echo "Unable to get $url\n";
    }
  }
}

