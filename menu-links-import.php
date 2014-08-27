<?php
namespace hfc\menu_links_import;
use \GlobIterator;

module_load_include('inc', 'ctools', 'includes/export');

$args = drush_get_arguments();

if ( ! isset($args[2]) ) {
  drush_set_error('Usage: drush @<alias> scr menu-links-import.php <input-directory>');
  exit();
}

if ( ! is_dir($args[2]) ) {
  drush_set_error(t("Input directory @ip does not exist.", array('@ip' => $args[2])));
  exit();
}

if ( ! file_exists($args[2] . '/menu-links.inc') ) {
  drush_set_error(t("File @file does not exist.", array('@file' => $args[2] . '/menu-links.inc')));
  exit();
}

if ( ! file_exists($args[2] . '/main_menu_menu_config.json') ) {
  drush_set_error(t("File @file does not exist.", array('@file' => $args[2] . '/main_menu_menu_config.json')));
  exit();
}

if ( ! file_exists($args[2] . '/main_menu_block_config.json') ) {
  drush_set_error(t("File @file does not exist.", array('@file' => $args[2] . '/main_menu_block_config.json')));
  exit();
}

import($args[2]);

function import($path) {
  $mappings = array();
  include_once realpath($path) . '/menu-links.inc';
  insert_menu_links($menu_links, $mappings);

  $menu_config = json_decode(file_get_contents(realpath($path) . '/main_menu_menu_config.json'));
  $block_config = file_get_contents(realpath($path) . '/main_menu_block_config.json');
  save_tb_megamenus($menu_config, $block_config, $mappings);
}

function insert_menu_links(&$menu_links, &$mappings) {
  drupal_set_message(t('Importing Menu Links'), 'ok');
  foreach ( $menu_links as $mlid => &$menu_link ) {
    $menu_link['options'] = unserialize($menu_link['options']);

    // Handle parent link mapping
    if ( $menu_link['plid'] ) {
      $plid = $menu_link['plid'];
      $menu_link['plid'] = $mappings[$menu_link['plid']];
    }

    if ( isset($menu_link['alias']) ) {
      // fix link_path for imported tid or nid
      $target = drupal_lookup_path('source', $menu_link['alias']);
      $source = $menu_link['link_path'];
      $menu_link['link_path'] = $target;


      // Fix Identifier
      if ( strpos($target, 'taxonomy/term/') === 0 ) {
        str_replace($source, $target, $menu_link['options']['identifier']);
      }
      unset($menu_link['alias']);
    }

    unset($menu_link['mlid']);
    $mappings[$mlid] = menu_link_save($menu_link);
  }

}

function save_tb_megamenus($source_menu_config, $block_config, $mappings) {
  drupal_set_message(t('Mapping Mega Menu'), 'ok');
  $mapped_config = array();
  foreach ( (array) $source_menu_config as $mlid => $config ) {
    $config = $source_menu_config->$mlid;

    $mapped_config[$mappings[$mlid]] = $config;
    if ( ! empty($config->rows_content) ) {

      foreach ( $config->rows_content as &$row ) {
        foreach ( $row as &$column ) {
          foreach ( $column->col_content as &$content) {
            $content->mlid = $mappings[$content->mlid];
          }
        }
      }


    }
  }

  $menu_config = json_encode($mapped_config);

  $tb_megamenu = db_select('tb_megamenus', 't')->fields('t')->condition('menu_name', 'main-menu')->execute()->fetchObject();
  if($tb_megamenu) {
    drupal_set_message(t('Update Existing Mega Menu'), 'ok');
    db_update('tb_megamenus')->fields(array('menu_config' => $menu_config, 'block_config' => $block_config))->condition('menu_name', 'main-menu')->execute();
  }
  else {
    drupal_set_message(t('Inserting New Mega Menu'), 'ok');
    db_insert('tb_megamenus')->fields(array('menu_name' => 'main-menu', 'block_config' => $block_config, 'menu_config' => $menu_config))->execute();
  }

}
