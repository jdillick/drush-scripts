<?php
namespace hfc\panels_import;
use \GlobIterator;

module_load_include('inc', 'ctools', 'includes/export');

$args = drush_get_arguments();

if ( ! isset($args[2]) ) {
  drush_set_error('Usage: drush @<alias> scr panels-import.php <input-directory>');
  exit();
}

if ( ! is_dir($args[2]) ) {
  drush_set_error(t("Input directory @ip does not exist.", array('@ip' => $args[2])));
  exit();
}

import($args[2]);

function import($path) {
  $nodes = array();
  $displays = array();
  $node_panes = array();
  $mappings = array();

  foreach ( new GlobIterator(realpath($path) . "/panel*.inc") as $panel_import ) {
    include_once $panel_import->getPathname();
    $nodes[$node->nid] = $node;
    $displays[$node->nid] = $display;
    $node_panes[$node->nid] = $panes;
  }

  make_nodes_portable($nodes, $displays);
  save_panels_nodes($nodes, $mappings);
  map_panes($node_panes, $mappings, $nodes);
  save_panels_pane($node_panes);
}

function make_nodes_portable(&$nodes, &$displays) {
  foreach ( $nodes as $nid => $node ) {
    unset(
      $node->nid,
      $node->vid,
      $node->panels_node['did'],
      $node->panels_node['nid'],
      $node->path['pid'],
      $node->path['source'],
      $node->workbench_moderation
    );

    $display = $displays[$nid];
    $display['did'] = 'new';
    $node->export_display = '$display = ' . ctools_var_export((object) $display) . ';';
  }
}

function save_panels_nodes(&$nodes, &$mappings) {
  foreach ( $nodes as $nid => $node ) {
    node_save($node);
    $mappings[$nid] = $node->nid;
    drupal_set_message(t('Saving panels node Source: @snid Target: @tnid', array(
      '@snid'=>$nid,
      '@tnid'=>$node->nid,
    )));
  }
}

function map_panes(&$node_panes, &$mappings, $nodes) {
  foreach ( $node_panes as $nid => &$panes) {
    foreach ( $panes as $pid => &$pane ) {
      $pane['pid'] = 0;
      $pane['did'] = $nodes[$nid]->panels_node['did'];

      // fix nids
      if ( $pane['type'] == 'node' ) {
        $config = unserialize($pane['configuration']);
        if ( in_array($config['nid'], array_keys($mappings)) ) {
          $config['nid'] = $mappings[$config['nid']];
          $pane['configuration'] = serialize($config);
        }
      }
    }
  }
}

function save_panels_pane($node_panes) {
  foreach ( $node_panes as $nid => $panes ) {
    foreach ( $panes as $pid => $pane ) {
      $new_pid = db_insert('panels_pane')->fields($pane)->execute();
      drupal_set_message(t('Inserting panels source nid: @nid source pane: @spid target pane: @tpid', array(
        '@nid'=>$nid,
        '@spid'=>$pid,
        '@tpid'=>$new_pid,
      )));
    }
  }
}
