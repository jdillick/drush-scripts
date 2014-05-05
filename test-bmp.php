<?php

module_load_include('install', 'bmp_state', 'bmp_state');
$args = drush_get_arguments();

if ( count($args) == 3 && function_exists($args[2]) ) {
  $return_value = $args[2]();
} else {
  echo "function " . $args[2] . " doesn't exist\n";
}

if ( $return_value ) print_r($return_value);
