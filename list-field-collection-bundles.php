<?php
require 'lib/field-collections.inc';

foreach ( get_all_field_collections() as $field_collection => $bundles ) {
  echo "$field_collection:\n";
  foreach ( $bundles as $bundle ) {
    echo "* $bundle\n";
  }
  echo "\n";
}
