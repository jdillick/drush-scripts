<?php

module_load_include('inc', 'hfc_magento_offer', 'includes/hfc_magento_offer');

$mag = 'hho';
$key = 'X3JXA148';
// $key = 'garbage';
$result = hfc\magento_offer\cds_verify_code($mag, $key);
echo $result ? "$key is good key for $mag\n" : "$key is bad key for $mag\n";
