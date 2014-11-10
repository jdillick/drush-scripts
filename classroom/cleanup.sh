#!/bin/bash

# Disabled/Enable these modules BEFORE deploying code
drush @classroom dis -y hfc_content_publication_taxonomy hfc_structure_taxonomy_product_section hfc_hub_taxonomy_target_audience hfc_hub_taxonomy_tags hfc_hub_taxonomy_stream_service hfc_hub_taxonomy_media_type hfc_hub_taxonomy_brand_pillar hfc_hub_taxonomy_brand_alignment hfc_grownups_misc_content_types hfc_content_promotion hfc_content_main_page bugherd email field_permissions forward globalredirect googleanalytics menu_node quicktabs redirect varnish workbench_access wysiwyg_filter forward_multi_template hfc_act_content_hidden_pictures migrate_hpi hfc_activity_engine_api hfc_adobe_search_forms hfc_content_premium hfc_content_scheduled_discount hfc_get_param_auth hfc_hub_content_club hfc_hub_content_magazine hfc_hub_content_media_asset hfc_hub_content_product hfc_hub_content_testimonial hfc_hub_content_textual_content hfc_hub_feeds_field_collections hfc_hub_sites_allowed_taxonomy hfc_services_enhancements hfc_spoke hfc_spoke_feed_club hfc_spoke_feed_magazine hfc_spoke_feed_media_asset hfc_spoke_feed_product hfc_spoke_feed_testimonial hfc_spoke_feed_textual_content

# NOW, deploy code and perform the below:
drush @classroom en -y hfc_environment_modules
drush @classroom cc all; memflush --server=127.0.0.1; drush cc drush
drush @classroom envm
drush @classroom cc all; memflush --server=127.0.0.1; drush cc drush

# Remove Unused Content Types
drush @classroom scr delete-content-type.php act_hidden_pictures
drush @classroom scr delete-content-type.php testimonial
drush @classroom scr detach-doppels.php toolbox_content
drush @classroom scr delete-content-type.php toolbox_content

# Remove Unused Fields
drush @classroom scr classroom/delete-unused-fields.php

# Copy Id Fields to Doppel Types
drush @classroom scr copy-identity-field-instances-to-doppels.php parent_teacher_guide,toolbox_media_content,publication_club,publication_magazine

# Save all Doppel Types (causes doppel flattening)
drush @classroom scr save-all-nodes-in-types.php parent_teacher_guide,toolbox_media_content,publication_club,publication_magazine

# Fix missing file usage
drush @classroom scr add-file-usage.php parent_teacher_guide,toolbox_media_content,publication_club,publication_magazine

# Detach Doppel Profiles
drush @classroom scr detach-doppels.php parent_teacher_guide,toolbox_media_content,publication_club,publication_magazine

drush @classroom cc all; memflush --server=127.0.0.1; drush cc drush

# Remove Identity Content Types
drush @classroom scr delete-content-type.php textual_content
drush @classroom scr delete-content-type.php media_asset
drush @classroom scr delete-content-type.php club
drush @classroom scr delete-content-type.php hub_magazine

# Create FC Replacement for Parent Teacher Guide and toolbox
drush @classroom scr field-collections-to-content-types.php parent_teacher_guide,toolbox_media_content
drush @classroom cc all; memflush --server=127.0.0.1; drush cc drush

# Copy FC Entities to Nodes for Parent Teacher Guide
drush @classroom scr field-collection-entities-to-nodes.php parent_teacher_guide,toolbox_media_content

# Delete FC Entities and field instances for Parent Teacher Guide
drush @classroom scr delete-field-collections.php parent_teacher_guide,toolbox_media_content

drush @classroom cc all; memflush --server=127.0.0.1; drush cc drush

# Enable new content type feature module
drush @classroom en -y classroom_content_types
drush @classroom fr -y --force classroom_content_types
