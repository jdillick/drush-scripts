<?php
$roles = array(
  'anonymous user',
  'authenticated user',
  // 'administrator',
  'back-end developer',
  'front-end developer',
  'content editor',
  'content creator',
  'hfc_user_management_admin',
  'marketing operations',
  'marketing creative',
  'seo',
  'social media',
);

$allperms = array();
foreach ( $roles as $role ) {
  $roleObject = user_role_load_by_name($role);
  $perms = user_role_permissions(array($roleObject->rid => 1));
  $allperms[$role] = array_keys(current($perms));
}

print var_export($allperms);
