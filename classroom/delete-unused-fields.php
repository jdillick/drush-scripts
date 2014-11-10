<?php

include_once dirname(__FILE__) . '/../lib/progress.inc';
include_once dirname(__FILE__) . '/../lib/doppel.inc';
include_once dirname(__FILE__) . '/../lib/field.inc';

$cleanup = array(
  'product' => array(
    'field_hub_google_custom_categori',
  ),
  'textual_content' => array(
    'field_hub_abstract',
    'field_hub_banner_credits',
    'field_hub_banner_title',
    'field_hub_call_to_action',
    'field_hub_contributor',
    'field_hub_display_title',
    'field_hub_images',
    'field_hub_keep_retire',
    'field_hub_large_banner_url',
    'field_hub_legacy_content_type',
    'field_hub_legacy_url',
    'field_hub_medium_banner_url',
    'field_hub_nid',
    'field_hub_published_in_magazine',
    'field_hub_published_on_site',
    'field_hub_rating',
    'field_hub_season',
    'field_hub_small_banner_url',
    'field_hub_tags',
    'field_hub_tc_active',
    'field_hub_tc_age_high',
    'field_hub_tc_age_low',
    'field_hub_tc_brand_alignment',
    'field_hub_tc_brand_pillar',
    'field_hub_tc_citation',
    'field_hub_tc_expiration_date',
    'field_hub_tc_grade_high',
    'field_hub_tc_grade_low',
    'field_hub_tc_published_date',
    'field_hub_tc_related_content',
    'field_hub_tc_target_audience',
    'field_hub_tc_type_of_content',
    'field_sites_allowed',
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
    'field_hub_stream_service',
    'field_hub_streaming_service_id',
    'field_hub_target_audience',
    'field_sites_allowed',
    'field_toolbox_type_of_content',
  ),
  'field_hub_contributor' => array(
    'field_hub_contributor_bio',
    'field_hub_contributor_company_lo',
    'field_hub_contributor_company_na',
  ),
  'field_hub_content_tiers' => array(
    'field_hub_tc_url_call_to_action',
  ),
  'club' => array(
    'field_hub_club_active',
    'field_hub_club_assoc_products',
    'field_hub_club_authoritative_url',
    'field_hub_club_base_price',
    'field_hub_club_brand_align',
    'field_hub_club_brand_pillar',
    'field_hub_club_expiration_date',
    'field_hub_club_features_list',
    'field_hub_club_grade_high',
    'field_hub_club_grade_low',
    'field_hub_club_high_res_img_alt',
    'field_hub_club_legal_terms',
    'field_hub_club_magento_sku',
    'field_hub_club_product_demo',
    'field_hub_club_product_images',
    'field_hub_club_publish_date',
    'field_hub_club_subtitle',
    'field_hub_club_tags',
    'field_hub_club_type_of_content',
    'field_hub_nid',
    'field_sites_allowed',
  ),
  'publication_club' => array(
    'field_spoke_club_features_list',
    'field_spoke_club_high_res_img_al',
    'field_spoke_club_legal_terms',
    'field_spoke_club_product_demo',
    'field_spoke_club_subtitle',
  ),
  'hub_magazine' => array(
    'field_hub_mg_features_list',
    'field_hub_mg_hi_res_image_alt',
    'field_hub_mg_product_demo_link',
  ),
  'publication_magazine' => array(
    'field_spoke_mg_features_list',
    'field_spoke_mg_hi_res_image_alt',
    'field_spoke_mg_product_demo_link',
  ),
);

foreach ( $cleanup as $content_type => $fields ) {
  echo "Cleaning up unwanted fields in $content_type\n";
  display_text_progress_bar(count($fields), TRUE);
  cleanup_doppels($content_type, $fields);

  foreach ( $fields as $field_name ) {
    delete_field($field_name, $content_type);
    display_text_progress_bar(count($fields));
  }
}

function cleanup_doppels($content_type, $fields) {
  $doppels = get_doppelled_configurations(array($content_type));
  if ( isset($doppels[$content_type]) ) {
    foreach ( $doppels[$content_type] as $doppel_name => $doppel ) {
      $profile = $doppel->doppel_profile;
      foreach ( $profile as $doppel_field => $config ) {
        if ( in_array($config['identity_field'], $fields) ) {
          unset($profile[$doppel_field]);
        }
      }
      update_doppel_profile($doppel_name, $doppel->doppel_identity, $profile);
    }
  }
}
