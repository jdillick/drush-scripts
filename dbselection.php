<?php
module_load_include('install', 'hfc_commerce_gpg', 'hfc_commerce_gpg');

$schema = _hfc_commerce_gpg_alternate_schema();
$default_db = db_set_active(variable_get('pcidatabase', 'pci'));
db_create_table('hfc_commerce_gpg_pci', $schema['hfc_commerce_gpg_pci']);
db_set_active($default_db);
