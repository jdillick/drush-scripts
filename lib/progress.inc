<?php

function display_media_progress($count, $file, $files) {
  static $size_so_far = 0;
  static $spinner_counter = 0;
  $spinner_counter++;

  $size_so_far += $file->filesize;
  $progress_bar = text_progress_bar($size_so_far, get_total_size($files), $spinner_counter);
  $download_counter = download_counter($size_so_far, get_total_size($files));
  echo $progress_bar . " " . $download_counter . "\r";
}

function display_text_progress_bar($total, $reset = FALSE) {
  static $count = 0;
  if ( $reset ) $count = 0;

  $count++;
  echo text_progress_bar($count, $total) . "\r";
}

function text_progress_bar( $item_count = 0, $total = 1, $heartbeat = FALSE ) {
  if ( $heartbeat === FALSE ) {
    $heartbeat = $item_count;
  }

  $percentage = floor($item_count / $total * 100);

  $spinner = array('|','/','-','\\','|','-');
  $bar = '[';
  for($i = 1; $i <= 10; $i++) {
    if($i <= ($percentage / 10)) $bar .= "#";
    else if ($i < ($percentage / 10 + 1)) $bar .= $spinner[$heartbeat % count($spinner)];
    else $bar .= " ";
  }
  $bar .= sprintf(' %3s%%]', $percentage);

  return $bar;
}

function download_counter($downloaded_bytes, $total_bytes) {
  $units = array(
    1024 => 'KiB',
    (1024 * 1024) => 'MiB',
    (1024 * 1024 * 1024) => 'GiB',
  );

  foreach ( $units as $size => $label ) {
    if ( $downloaded_bytes >= $size ) {
      $from = $size;
      $from_label = $label;
    }

    if ( $total_bytes >= $size ) {
      $to = $size;
      $to_label = $label;
    }
  }

  return sprintf("(%.2f %s of %.2f %s downloaded)",
    $downloaded_bytes / $from,
    $from_label,
    $total_bytes / $to,
    $to_label);
}
