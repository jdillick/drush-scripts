<?php

module_load_include('install', 'stenhouse_state', 'stenhouse_state');
module_load_include('module', 'stenhouse_config', 'stenhouse_config');
$args = drush_get_arguments();

if ( count($args) == 3 && function_exists($args[2]) ) {
  $args[2]();
}
