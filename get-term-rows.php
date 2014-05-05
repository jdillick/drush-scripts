<?php
function drush_get_term_rows() {
  // look up the defined terms
  $terms = array();
  $result = db_query('SELECT term, machine_name, weight FROM {hfc_magento_offer_terms} ORDER BY weight' );

  foreach ($result as $data) {
    $terms[$data->machine_name] = array('term' => $data->term, 'machine_name' => $data->machine_name, 'weight' => $data->weight);
  }

  return $terms;
}

$result = db_query('SELECT term, machine_name, weight FROM {hfc_magento_offer_terms} ORDER BY weight' )->execute();
print_r($result);
