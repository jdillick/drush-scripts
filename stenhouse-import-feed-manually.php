<?php
$module_path = drupal_get_path('module', 'stenhouse_state');
$product_csv = DRUPAL_ROOT . '/' . $module_path . "/products/products.csv";

$feeds_source = feeds_source('stenhouse_feed_product_variation');
$feeds_source->addConfig( array_merge($feeds_source->getConfig(), array(
  'FeedsFileFetcher' => array(
    'source' => $product_csv,
  ),
)) );

$feeds_source->startImport();
while(FEEDS_BATCH_COMPLETE != ($progress = $feeds_source->import())) {
  echo t("Processing: @p %\r", array('@p' => floor($progress * 100)));
}
