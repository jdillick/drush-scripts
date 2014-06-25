<?php

$it = new DirectoryIterator("public://product_images/");
$images = array();
foreach ( $it as $f ) {
  if ( strpos($f->getFileName(), '.jpg') !== FALSE ) {
    $images[] = $f->getFileName();
  }
}

$found = 0;
$missing = 0;
$confirmed = $fuzzy = array();
foreach ( node_load_multiple(array(), array('type' => 'product_general')) as $product ) {
  $product = entity_metadata_wrapper('node', $product);
  $image_candidate = get_product_image_name($product->title->value()) . '.jpg';
  if ( ! in_array($image_candidate, $images) ) {
    $missing++;
    echo "File $image_candidate does not exist for " . $product->title->value() . ".\n";
  } else {
    $confirmed[$product->nid->value() . " " . $product->title->value()] = $image_candidate;
    $found++;
  }
}

// print_r($confirmed);

function get_product_image_name($product_title) {
  // no ndash you son of bitch
  $product_title = str_replace('–', '-', $product_title);
  // no question marks
  $product_title = str_replace('?', '', $product_title);
  // no single quotes
  $product_title = preg_replace('/\'+/', '', $product_title);

  // find parenthetical grade ranges
  $product_title = trim(preg_replace('/(.*?)(\()(\d+-\d+)(\))/', '\1 \3', $product_title));

  // get rid of parenthetical phrase at end of some book titles
  $product_title = trim(preg_replace('/(.*?)\(.*$/', '\1', $product_title));

  // replace spaces, dashes, puctuation with -
  $product_title = preg_replace('/[ _\/,.&!:…]/', '-', $product_title);

  // no trailing -
  $product_title = preg_replace('/[-]+$/', '', $product_title);
  // no leading -
  $product_title = preg_replace('/^[-]+/', '', $product_title);

  // replace duplicate - with single -
  $product_title = preg_replace('/[-]+/', '-', $product_title);

  $product_title = strtolower($product_title);

  return $product_title;
}
