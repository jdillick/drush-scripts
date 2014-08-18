<?php
$args = drush_get_arguments();

if ( ! isset($args[2]) ) {
  drush_set_error('Usage: drush @<alias> scr panels-import.php <input-directory>');
  exit();
}

if ( ! is_dir($args[2]) ) {
  drush_set_error(t("Input directory @ip does not exist.", array('@ip' => $args[2])));
  exit();
}

foreach ( new GlobIterator(realpath($args[2]) . "/panel*.inc") as $panel_import ) {
  include_once $panel_import->getPathname();

  // insert or update node
  drupal_set_message(t('Saving panels node @nid', array('@nid'=>$node->nid)));
  node_save($node);

  $existing_display = db_select('panels_display','d')
    ->fields('d', array('did'))
    ->condition('did', $display['did'], '=')
    ->range(0,1)
    ->execute()
    ->fetch();
  if ( ! $existing_display ) {
    drupal_set_message(t('Inserting panels display @did', array('@did'=>$display['did'])));
    db_insert('panels_display')->fields($display)->execute();
  } else {
    drupal_set_message(t('Updating panels display @did', array('@did'=>$display['did'])));
    db_update('panels_display')->fields($display)->condition('did', $display['did'], '=')->execute();
  }

  $existing_panes = db_select('panels_pane', 'p')
    ->fields('p')
    ->condition('p.pid', array_keys($panes), 'IN')
    ->condition('p.did', $display['did'], '=')
    ->execute()
    ->fetchAllAssoc('pid', PDO::FETCH_ASSOC);

  foreach ( $panes as $pid => $pane ) {
    if ( ! in_array($pid, array_keys($existing_panes)) ) {
      drupal_set_message(t('Inserting panels pane @pid', array('@pid'=>$pid)));
      db_insert('panels_pane')->fields($pane)->execute();
    } else {
      drupal_set_message(t('Updating panels pane @pid', array('@pid'=>$pid)));
      db_update('panels_pane')->fields($pane)->condition('pid', $pid, '=')->execute();
    }
  }
}
