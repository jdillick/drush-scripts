<?php

// California
$state = 10;

// Texas
$state = 86;

// Get destinations from above state
$state_query = new EntityFieldQuery;
$result = $state_query->entityCondition('entity_type', 'node')
          ->entityCondition('bundle', 'destination')
          ->fieldCondition('field_state', 'target_id', $state, '=')
          ->fieldCondition('field_games', 'target_id', NULL, 'IS NOT NULL')
          ->propertyCondition('status', 1, '=')
          ->execute();

// my test child account uid
$billy_uid = 536;
$billy = user_load($billy_uid);
$flag = flag_get_flag('visited');

  echo "||Destination||Title||Puzzle||Type||Puzzle NID||Visited||\n";

  foreach($result['node'] as $nid => $a_node) {

    $dest_node = node_load($nid);

    foreach ($dest_node->field_games as $puzzle_id => $puzzle ) {
      $puzzle = node_load($puzzle[0]['target_id']);

      // for any non crossword puzzle destination, set visited flag
      if ($puzzle->type != 'game_crossword') {
        $flag->flag('flag', $nid, $billy, TRUE);
      }

      echo t("|@dnid|@title|@puzzle|@type|@pnid|@visited|\n", array(
        '@title' => $dest_node->title,
        '@dnid' => $nid,
        '@puzzle' => $puzzle->title,
        '@type' => $puzzle->type,
        '@pnid' => $puzzle->nid,
        '@visited' => $flag->is_flagged($nid, $billy_uid) ? "Yes" : "*No*",
        ));
    }
  }

