<?php
$files = db_select('file_managed', 'fm')
  ->fields('fm', array('uri', 'filename'))
  ->execute();

$records = array();
while ( $records[] = $files->fetchAssoc() ) {}
shuffle($records);

foreach ( $records as $record ) {
  $url = file_create_url($record['uri']);
  $file = drupal_realpath($record['uri']);
  if ($url && $file) replace_media($url, $file);
}

function replace_media ($url, $file) {
  $url = str_replace($_ENV['ENVTYPE'] . '.', '', $url);
  if ( ! file_exists($file) ) {
    $base = dirname($file);
    if ( ! is_dir($base) ) {
      echo "Creating $base directory.\n";
      mkdir($base, 02777, TRUE);
    }

    $contents = file_get_contents($url);
    if ( ! empty($contents) ) {
      file_put_contents($file, $contents);
      echo "Fetched from $url and stored $file.\n";
    } else {
      echo "Unable to get $url\n";
    }
  }
}
