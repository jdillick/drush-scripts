<?php

require 'lib/field-collections.inc';

foreach ( get_nested_field_collection_instances() as $instance ) {
  echo "Deleting field instance " . $instance['field_name'] . " in bundle " . $instance['bundle'] . "\n";
  field_delete_instance($instance, TRUE);
}
