#!/bin/bash

# Disabled/Enable Modules
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
drush @classroom scr copy-identity-field-instances-to-doppels.php parent_teacher_guide,toolbox_media_content

# Save all Doppel Types (causes doppel flattening)
drush @classroom scr save-all-nodes-in-types.php parent_teacher_guide,toolbox_media_content

# Fix missing file usage
drush @classroom scr add-file-usage.php parent_teacher_guide,toolbox_media_content

# Detach Doppel Profiles
drush @classroom scr detach-doppels.php parent_teacher_guide,toolbox_media_content

# Remove Identity Content Types
drush @classroom scr delete-content-type.php textual_content
drush @classroom scr delete-content-type.php media_asset

# Create FC Replacement for Parent Teacher Guide and toolbox
drush @classroom scr field-collections-to-content-types.php parent_teacher_guide,toolbox_media_content
drush @classroom cc all; memflush --server=127.0.0.1; drush cc drush

# Copy FC Entities to Nodes for Parent Teacher Guide
drush @classroom scr field-collection-entities-to-nodes.php parent_teacher_guide,toolbox_media_content

# Delete FC Entities and field instances for Parent Teacher Guide
drush @classroom scr delete-field-collections.php parent_teacher_guide,toolbox_media_content

drush @classroom cc all; memflush --server=127.0.0.1; drush cc drush
