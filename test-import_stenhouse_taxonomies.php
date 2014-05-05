<?php
module_load_include('install', 'stenhouse_state', 'stenhouse_state');

foreach ( array_keys(taxonomy_get_vocabularies()) as $vid ) {
  taxonomy_vocabulary_delete($vid);
}

import_stenhouse_taxonomies();
