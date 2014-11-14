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
  'field_tier' => array(
    'field_call_to_action',
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
    'field_hub_mg_1_year_sub_ca',
    'field_hub_mg_1_year_sub_intl',
    'field_hub_mg_1_year_subscription',
    'field_hub_mg_2_year_sub_ca',
    'field_hub_mg_2_year_sub_intl',
    'field_hub_mg_2_year_subscription',
    'field_hub_mg_3_year_sub_ca',
    'field_hub_mg_3_year_sub_intl',
    'field_hub_mg_3_year_subscription',
    'field_hub_mg_active',
    'field_hub_mg_addl_product_images',
    'field_hub_mg_authoritative_url',
    'field_hub_mg_brand_alignment',
    'field_hub_mg_brand_pillar',
    'field_hub_mg_expriation_date',
    'field_hub_mg_grade_high',
    'field_hub_mg_grade_low',
    'field_hub_mg_published_date',
    'field_hub_mg_renewal_url',
    'field_hub_mg_type_of_content',
    'field_hub_nid',
    'field_sites_allowed',
  ),
  'publication_magazine' => array(
    'field_spoke_mg_features_list',
    'field_spoke_mg_hi_res_image_alt',
    'field_spoke_mg_product_demo_link',
  ),
  'product' => array(
    'field_hub_active',
    'field_hub_authoritative_url',
    'field_hub_catalog_category',
    'field_hub_expiration_date',
    'field_hub_google_availability',
    'field_hub_google_brand',
    'field_hub_google_category',
    'field_hub_google_custom_categori',
    'field_hub_grade_high',
    'field_hub_grade_high',
    'field_hub_grade_low',
    'field_hub_high_res_image_alt',
    'field_hub_is_catalog',
    'field_hub_issn',
    'field_hub_nid',
    'field_hub_other_set_prices',
    'field_hub_pr_brand_alignment',
    'field_hub_pr_tags',
    'field_hub_product_id',
    'field_hub_publish_date',
    'field_hub_purchase_link_s_',
    'field_hub_thumb_large',
    'field_hub_thumb_medium',
    'field_hub_thumb_small',
    'field_hub_type_of_content',
    'field_hub_upc',
    'field_sites_allowed',
  ),
  'classroom_product' => array(
    'field_class_active',
    'field_class_authoritative_url',
    'field_class_catalog_category',
    'field_class_expiration_date',
    'field_class_google_availability',
    'field_class_google_brand',
    'field_class_google_category',
    'field_class_google_custom_catego',
    'field_class_google_custom_categori',
    'field_class_grade_high',
    'field_class_grade_high',
    'field_class_grade_low',
    'field_class_is_catalog',
    'field_class_issn',
    'field_class_nid',
    'field_class_other_set_prices',
    'field_class_pr_brand_alignment',
    'field_class_pr_tags',
    'field_class_product_id',
    'field_class_publish_date',
    'field_class_purchase_link_s_',
    'field_class_thumb_large',
    'field_class_thumb_medium',
    'field_class_thumb_small',
    'field_class_type_of_content',
    'field_class_upc',
    'field_scheduled_discount_product',
    'field_sites_allowed',
  ),
);

$cleanup_field_groups = array(
  'textual_content' => array(
    'group_archival_kids_data',
    'group_content_admin',
    'group_hub_data',
    'group_hub_metadata',
    'group_legacy_info',
    'group_metadata',
    'group_promotional_fields',
  ),
  'publication_club' => array(
    'group_publication_club',
  ),
  'publication_magazine' => array(
    'group_publication_magazine',
  ),
  'classroom_product' => array(
    'group_classroom_product',
  ),
);

// Do the cleanup tasks
cleanup_fields($cleanup);
cleanup_field_groups($cleanup_field_groups);

function cleanup_fields($cleanup) {
  foreach ( $cleanup as $content_type => $fields ) {
    $entity_type = 'node';
    // assuming it's a field collection if it isn't a node type
    $entity_type = (node_type_load($content_type) ? 'node' : 'field_collection_item');

    echo "Cleaning up unwanted fields in $content_type\n";
    display_text_progress_bar(count($fields), TRUE);
    cleanup_doppels($content_type, $fields);

    foreach ( $fields as $field_name ) {
      delete_field($field_name, $content_type, $entity_type);
      display_text_progress_bar(count($fields));
    }
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

function cleanup_field_groups($field_groups) {
  foreach ( $field_groups as $content_type => $groups ) {
    echo "Cleaning up unwanted field groups in $content_type\n";
    foreach ( $groups as $group_name ) {
      delete_field_group($group_name, $content_type);
    }
  }
}
