<?php
/**
 * @file
 * attach-product-images.php
 */

attach_product_images();

/**
 * Attaches product images to product nodes.
 */
function attach_product_images() {
  $images = gather_product_images();
  $missing = array();
  foreach ( node_load_multiple(array(), array('type' => 'product_general')) as $product ) {
    $product = entity_metadata_wrapper('node', $product);
    $image_candidate = get_product_image_name($product->title->value()) . '.jpg';
    if ( in_array($image_candidate, $images) ) {
      attach_product_image(
        $product,
        manage_product_image('public://product_images/', $image_candidate)
      );
      continue;
    }
    drupal_set_message(t('Missing image @name for "@product" (nid @nid)', array(
      '@name' => 'public://product_images/' . $image_candidate,
      '@product' => $product->title->value(),
      '@nid' => $product->nid->value(),
    )), 'warning', FALSE);
  }
}

/**
 * Scans public stream wrapper product_images directory.
 *
 * @return array list of jpeg images.
 */
function gather_product_images() {
  $images = array();
  foreach ( new DirectoryIterator("public://product_images/") as $image_file ) {
    if ( strpos($image_file->getFileName(), '.jpg') !== FALSE ) {
      $images[] = $image_file->getFileName();
    }
  }
  return $images;
}

/**
 * Attach the managed product image file to the product node.
 *
 * @param  node $product
 * @param  stdClass $managed_image_file
 */
function attach_product_image($product, $managed_image_file) {
  $product->field_product_image->set((array) $managed_image_file);
  $product->save();
}

/**
 * Get the existing managed image file, or create new managed image.
 *
 * @param  string $path the stream wrapper patch to image
 * @param  string $image_file_name the filename
 * @return stdClass the file data for the managed image.
 */
function manage_product_image($path, $image_file_name) {
  $existing = db_select('file_managed', 'f')
    ->fields('f', array('fid'))
    ->condition('f.uri', $path . $image_file_name, '=')
    ->execute()
    ->fetchField(0);

  if ( $existing ) return file_load(array($existing));

  $image_object = new \stdClass;
  $image_object->filename = $image_file_name;
  $image_object->uri = $path . $image_file_name;
  $image_object->filemime  = mime_content_type($path . $image_file_name);
  $image_object->filesize  = filesize($path . $image_file_name);
  $image_object->uid       = 0;
  $image_object->timestamp = time();

  file_save($image_object);
  return $image_object;
}

/**
 * Get a product image name from the product title.
 *
 * @param  string $product_title
 * @return string the product image name, without extension.
 */
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
