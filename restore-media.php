<?php

function restore_missing_media() {
  $count = 0;
  $files = get_managed_image_files();
  $totalsize = get_total_size($files);
  foreach ( $files as $file ) {
    $url = file_create_url($file->uri);
    $file_name = drupal_realpath($file->uri);
    if ($file_url && $file_name) replace_media($file_url, $file_name);

    display_media_progress($count++, $file, $files);
  }
}

function get_managed_image_files() {
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

  return file_load_multiple($fids);
}

function get_total_size($files) {
  $size = 0;
  foreach ( $files as $file ) {
    $size += $file->filesize;
  }
  return $size;
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

function display_media_progress($count, $file, $files) {
  static $size_so_far = 0;
  $size_so_far += $file->filesize;
  $totalsize = get_total_size($files);
  $percentage = floor($count / count($files) * 100);

  echo '[';
  for($i = 1; $i <= 10; $i++) {
    if($i <= ($percentage / 10)) echo "#";
    else echo " ";
  }

  $kb = 1024;
  $mb = $kb * 1024;
  $unit = $totalsize > $mb ? $mb : $kb;
  $unit_label = $totalsize > $mb ? 'M' : 'K';
  echo sprintf("] (%d%% %d of %d%s downloaded)\r", $percentage, $size_so_far / $unit, $totalsize / $unit, $unit_label);
}

restore_missing_media();
