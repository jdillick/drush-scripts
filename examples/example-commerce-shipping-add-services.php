<?php
  $shipping_services = array(
    array(
      'name' => 'ground',
      'title' => 'Ground shipping',
      'display_title' => 'Ground shipping',
      'description' => 'Ground shipping',
      'rules_component' => 1,
      'data' => '',
      'is_new' => TRUE,
      'amount' => 750,
      'currency_code' => 'USD',
    ),
    array(
      'name' => 'two_day',
      'title' => 'Two-day shipping',
      'display_title' => 'Two-day shipping',
      'description' => 'Two-day shipping',
      'rules_component' => 1,
      'data' => '',
      'is_new' => TRUE,
      'amount' => 1500,
      'currency_code' => 'USD',
    ),
    array(
      'name' => 'free',
      'title' => 'Free Shipping',
      'display_title' => 'Free Shipping',
      'description' => 'Free Shipping',
      'rules_component' => 1,
      'data' => '',
      'is_new' => TRUE,
      'amount' => 0,
      'currency_code' => 'USD',
    )
  );

  foreach($shipping_services as $index => $service) {
    commerce_flat_rate_service_save($service, FALSE);
  }

  $rule = rules_config_load('commerce_shipping_service_free');
  $rule->condition('data_is', array(
    'data:select' => 'commerce-order:commerce-order-total:amount',
    'op' => '>',
    'value' => '2499'
  ));
  $rule->save();

  $rule = rules_config_load('commerce_shipping_service_ground');
  $rule->condition('data_is', array(
    'data:select' => 'commerce-order:commerce-order-total:amount',
    'op' => '<',
    'value' => '2500'
  ));
  $rule->save();
