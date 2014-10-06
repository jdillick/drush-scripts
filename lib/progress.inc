<?php

function display_media_progress($count, $file, $files) {
  static $size_so_far = 0;
  static $spinner_counter = 0;
  $spinner_counter++;

  $progress_bar = text_progress_bar($count, count($files), $spinner_counter);

  $size_so_far += $file->filesize;
  $download_counter = download_counter($size_so_far, get_total_size($files));
  echo $progress_bar . " " . $download_counter . "\r";
}

function display_text_progress_bar($total) {
  static $count = 0;
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
  $bar .= ' ' . $percentage . '%]';

  return $bar;
}

function download_counter($downloaded_bytes, $total_bytes) {
  $kb = 1024;
  $mb = $kb * 1024;
  $unit = $total_bytes > $mb ? $mb : $kb;
  $unit_label = $total_bytes > $mb ? 'M' : 'K';
  return sprintf("(%d of %d%s downloaded)",
    $downloaded_bytes / $unit,
    $total_bytes / $unit,
    $unit_label);
}
