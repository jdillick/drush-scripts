<?php

$rule = rules_config_load('commerce_payment_hfc_commerce_gpg');
if ( $rule instanceof RulesReactionRule ) {
  $rule->active = TRUE;
  $rule->save();
}
