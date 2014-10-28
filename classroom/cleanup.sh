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

# Fix Doppelled Field Collections
drush @classroom scr correct-renamed-doppel-field-collections.php classroom_product

# Copy Id Fields to Doppel Types
drush @classroom scr copy-identity-field-instances-to-doppels.php classroom_product,parent_teacher_guide,publication_club,publication_magazine,toolbox_media_content

# Save all Doppel Types (causes doppel flattening)
drush @classroom scr save-all-nodes-in-types.php classroom_product,parent_teacher_guide,publication_club,publication_magazine,toolbox_media_content

# Fix missing file usage
drush @classroom scr add-file-usage.php classroom_product,parent_teacher_guide,publication_club,publication_magazine,toolbox_media_content

# Detach Doppel Profiles
drush @classroom scr detach-doppels.php classroom_product,parent_teacher_guide,publication_club,publication_magazine,toolbox_media_content

# Remove Identity Content Types
drush @classroom scr delete-content-type.php product
drush @classroom scr delete-content-type.php textual_content
drush @classroom scr delete-content-type.php club
drush @classroom scr delete-content-type.php hub_magazine
drush @classroom scr delete-content-type.php media_asset

# Create FC Replacement for Parent Teacher Guide
drush @classroom scr field-collections-to-content-types.php parent_teacher_guide
drush @classroom cc all; memflush --server=127.0.0.1; drush cc drush

# Copy FC Entities to Nodes for Parent Teacher Guide
drush @classroom scr field-collection-entities-to-nodes.php parent_teacher_guide

# Delete FC Entities and field instances for Parent Teacher Guide
drush @classroom scr delete-field-collections.php parent_teacher_guide
