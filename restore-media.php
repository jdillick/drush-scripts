<?php

require 'lib/progress.inc';

$force = FALSE;
$args = drush_get_arguments();
if ( isset($args[2]) && $args[2] == 'force' ) $force = TRUE;

function restore_missing_media($force = FALSE) {
  $count = 0;
  $files = get_managed_files();
  $totalsize = get_total_size($files);
  foreach ( $files as $file ) {
    $file_url = file_create_url($file->uri);
    $file_name = drupal_realpath($file->uri);
    if ($file_url && $file_name) {
      replace_media($file_url, $file_name, $force);
      display_media_progress($count++, $file, $files);
    } else {
      echo "Missing file url (" . $file_url .
        ") or file name (" . $file_name . ") for fid:" . $file->fid . "\n";
    }

  }
}

function get_managed_files() {
  $db_or = db_or()
    ->condition('fc.type', 'image', '=')
    ->condition('fc.type', 'file', '=');

  $fields = db_select('field_config', 'fc')
    ->condition($db_or)
    ->condition('fc.deleted', 1, '<>')
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

  return file_load_multiple($fids);
}

function get_total_size($files) {
  $size = 0;
  foreach ( $files as $file ) {
    $size += $file->filesize;
  }
  return $size;
}

function replace_media ($url, $file, $force = FALSE) {
  $url = str_replace($_ENV['ENVTYPE'] . '.', '', $url);
  if ( ! file_exists($file) || $force ) {
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

restore_missing_media($force);
