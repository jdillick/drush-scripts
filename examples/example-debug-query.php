<?php

/**
 * Debug a query by replacing all the tokens.
 *
 * Shamelessly stolen from devel's dpq().
 * @param  object $query a drupal query object
 * @return string the expanded query
 */
function debug_query($query) {
  if (method_exists($query, 'preExecute')) {
    $query->preExecute();
  }
  $sql = (string) $query;
  $quoted = array();
  $connection = Database::getConnection();
  if (method_exists($query, 'arguments')) {
    foreach ((array) $query->arguments() as $key => $val) {
      $quoted[$key] = $connection->quote($val);
    }
  }

  // insertValues propery is protected... oh well
  // Good job with the consistent interface, Drupal.
  // if ( isset($query->insertValues) ) {
  //   foreach ((array) $query->insertValues as $key => $val) {
  //     $quoted[$key] = $connection->quote($val);
  //   }
  // }
  return strtr($sql, $quoted);
}

// Example Debug select query
$query = db_select('node','n')
->condition('n.nid', 2, '=');

echo debug_query($query) . "\n";
