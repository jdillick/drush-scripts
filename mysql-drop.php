<?php

$result = db_query('SHOW TABLES');
$tables = array();
foreach ( $result as $table ) {

  echo "DROP TABLE " . $table->Tables_in_www_stenhouse_com . "\n";
  db_query("DROP TABLE " . $table->Tables_in_www_stenhouse_com);
}
