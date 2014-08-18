<?php

$node = node_load(array(466));

/**
 * hfc_get_age_values
 *
 * Returns array with low and high age value and units.
 *
 * Example:
 * Array
 * (
 *     [high] => Array
 *         (
 *             [value] => 1
 *             [unit] => years
 *         )
 *
 *     [low] => Array
 *         (
 *             [value] => 9
 *             [unit] => months
 *         )
 *
 * )
 *
 * A value of 0 in the high value means "and up".
 *
 * @param  entity $entity      node or entity object
 * @param  string $entity_type (optional) defaults to node
 * @return array ages array
 */
function hfc_get_age_values($entity, $entity_type = 'node') {
  $wrapper = entity_metadata_wrapper($entity_type, $entity);

  $values = array();
  foreach ( array_keys($wrapper->getPropertyInfo()) as $field_name ) {
    if ( preg_match('/.*age.*low/', $field_name) ) {
      $values['low'] = array('value' => $wrapper->$field_name->value());
    }
    if ( preg_match('/.*age.*high/', $field_name) ) {
      $values['high'] = array('value' => $wrapper->$field_name->value());
    }
  }

  foreach ( $values as $age => $data ) {
    $value = $values[$age]['value'];
    if ( is_numeric($value) && $value < 1 ) {
      $values[$age]['unit'] = 'months';
      $values[$age]['value'] = intval($value * 12);
    } else {
      $values[$age]['unit'] = 'years';
      $values[$age]['value'] = intval($value);
    }
  }
  return $values;
}

$ages = hfc_get_age_values($node);
print_r($ages);
