<?php

$license = entity_load_single('commerce_license', array(8));
$user = user_load(array($license->uid));
echo $user->mail;
echo "\n";
