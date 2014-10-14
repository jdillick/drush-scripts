<?php

include_once dirname(__FILE__) . '/../lib/field.inc';

$cleanup = array(
  'product' => array(
    'field_hub_google_custom_categori',
  ),
  'textual_content' => array(
    'field_hub_images',
  ),
  'classroom_product' => array(
    'field_class_google_custom_catego',
  ),
);

foreach ( $cleanup as $content_type => $fields ) {
  foreach ( $fields as $field_name ) {
    delete_field($field_name, $content_type);
  }
}
