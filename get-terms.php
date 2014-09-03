<?php

$args = drush_get_arguments();
if ( ! isset($args[2]) ) {
  drush_set_error('Usage: drush @<alias> scr get-terms.php <term names>');
  exit();
}

$term_names = explode(',', trim($args[2]));
$terms = array();
foreach ( $term_names as $name ) {
  $term_array = taxonomy_get_term_by_name($name);
  foreach ( $term_array as $term ) {
    $terms[$name]['path'] = 'taxonomy/term/' . $term->tid;
    $terms[$name]['alias'] = drupal_get_path_alias($terms[$name]['path']);
  }
}

print_r($terms);
