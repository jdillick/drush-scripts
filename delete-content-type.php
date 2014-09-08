<?php
$args = drush_get_arguments();
$ct = $args[2];

$nodes = node_load_multiple(array(), array('type' => $ct));
node_delete_multiple(array_keys($nodes));
$instances = field_read_instances(array('bundle' => $ct), array('include_inactive' => TRUE, 'include_deleted' => TRUE));
foreach ( $instances as $instance ) {
  field_delete_instance($instance);
  field_purge_instance($instance);
}
node_type_delete($ct);
