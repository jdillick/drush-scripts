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
  'toolbox_media_content' => array(
    'field_banner_abstract',
    'field_featured_on_toolbox',
    'field_promo_hires_1_1',
  ),
  'media_asset' => array(
    'field_hub_brand_alignment',
    'field_hub_caption',
    'field_hub_file_link',
    'field_hub_id',
    'field_hub_legacy_content_type',
    'field_hub_legacy_url',
    'field_hub_ma_brand_pillar',
    'field_hub_ma_expiration_date',
    'field_hub_ma_publish_date',
    'field_hub_ma_tags',
    'field_hub_ma_type_of_content',
    'field_hub_me_age_high',
    'field_hub_me_age_low',
    'field_hub_me_grade_high',
    'field_hub_me_grade_low',
    'field_hub_nid',
    'field_hub_related_content',
    'field_hub_streaming_service_id',
    'field_hub_stream_service',
    'field_hub_target_audience',
    'field_sites_allowed',
    'field_toolbox_type_of_content',
  ),
);

foreach ( $cleanup as $content_type => $fields ) {
  foreach ( $fields as $field_name ) {
    delete_field($field_name, $content_type);
  }
}
