<?php

namespace hfc\commerce\gpg;
module_load_include('inc', 'hfc_commerce_gpg', 'hfc_commerce_gpg');

global $user;
$user = user_load(1);
$encrypted_data = retrieve_encrypted_data(5);
echo $encrypted_data;
$decrypted_data = decrypt($encrypted_data);
print_r($decrypted_data);
