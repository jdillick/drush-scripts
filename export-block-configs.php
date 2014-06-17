<?php

$existing = db_select('block', 'b')->fields('b')->execute()->fetchAllAssoc('bid');
$export = array();
foreach ( $existing as $bid => $block ) {
  unset($block->bid);
  $export[] = (array) $block;
}

echo var_export($export);
