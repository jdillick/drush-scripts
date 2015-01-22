<?php

require dirname(__FILE__) . '/../vendor/autoload.php';
include_once dirname(__FILE__) . '/../lib/progress.inc';

use League\Csv\Reader;

$csv = Reader::createFromPath(dirname(__FILE__) . '/recipes.csv');
$data = $csv->fetchAll();
$headers = array_shift($data);
$recipes = array();
$headermeta = array();
$nutritions = array();

foreach ($headers as $index => $header) {
	if ( strpos($header, 'nutrition') !== FALSE) {
		$column = explode('/', $header);
		if ( ! $column[2] ) {
			$headermeta[$index] = 'nutrition-label';
		} else {
			$headermeta[$index] = 'nutrition-value';
		}
	}

	elseif ( strpos($header, 'tags') !== FALSE) {
		$headermeta[$index] = 'tag';
	}
	elseif ( strpos($header, 'ingredient') !== FALSE) {
		$headermeta[$index] = 'ingredient';
	}
	elseif ( strpos($header, 'direction') !== FALSE) {
		$headermeta[$index] = 'direction';
	}
	else {
		$headermeta[$index] = $header;
	}
}

$field_mappings = array(
	'title' => 'title_field',
	'shortDescription' => 'body',
	'photoUrl' => 'field_image',
	'prepTime' => 'field_prep_time',
	'cookTime' => 'field_cook_time',
	'totalTime' => 'field_total_time',
	'servingsNumber' => 'field_servings',
	'tags' => 'field_tags',
	'directions' => 'field_directions',
	'ingredients' => 'field_ingredients',
	'Calcium' => 'field_calcium',
	'Carbohydrates' => 'field_carbohydrates',
	'Cholesterol' => 'field_cholesterol',
	'Dietary Fiber' => 'field_dietary_fiber',
	'Fat' => 'field_fat',
	'Fiber' => 'field_dietary_fiber',
	'Folate' => 'field_folate',
	'Iron' => 'field_iron',
	'Magnesium' => 'field_magnesium',
	'Niacin Equivalents' => 'field_niacin',
	'Potassium' => 'field_potassium',
	'Protein' => 'field_protein',
	'Saturated Fat' => 'field_saturated_fat',
	'Sodium' => 'field_sodium',
	'Sugars' => 'field_sugars',
	'Thiamin' => 'field_thiamin',
	'Total Carbs' => 'field_carbohydrates',
	'Total Fat' => 'field_fat',
	'Trans Fat' => 'field_trans_fat',
	'Vitamin A' => 'field_vitamin_a',
	'Vitamin A - IU' => 'field_vitamin_a',
	'Vitamin B6' => 'field_vitamin_b6',
	'Vitamin C' => 'field_vitamin_c',
	'caloriesFromFat' => 'field_fat_calories',
	'calories' => 'field_calories',
);

foreach ( $data as $i => $row ) {
	$recipe = (object) array(
		'nutrition' => array(),
		'tags' => array(),
		'directions' => array(),
		'ingredients' => array(),
	);

	foreach ($row as $j => $col) {
		$value = preg_replace('/( )+/', ' ', trim($col));

		if ( ! $value ||  $headermeta[$j] == 'nutrition-label' ) continue;

		if ( $headermeta[$j] == 'nutrition-value' ) {
			$recipe->nutrition[$field_mappings[$row[$j - 1]]] = $value;
			$nutritions[] = $row[$j - 1];
		} 
		elseif ( $headermeta[$j] == 'tag' ) {
			$recipe->{$field_mappings['tags']}[] = $value;
		}
		elseif ( $headermeta[$j] == 'ingredient' ) {
			$recipe->{$field_mappings['ingredients']}[] = $value;
		}
		elseif ( $headermeta[$j] == 'direction' ) {
			$recipe->{$field_mappings['directions']}[] = $value;
		}
		else {
			$recipe->{$field_mappings[$headermeta[$j]]} = $value;
		}
	}
	$recipes[] = $recipe;
}

display_text_progress_bar(count($recipes), TRUE);
foreach ( $recipes as $i => $recipe ) {
	$title = check_plain(trim($recipe->title_field));
	if (!$title) continue;

	$node = (object) array(
		'title' => $title,
		'type' => 'recipe',
		'language' => 'en',
		'status' => 1,
	);

	foreach ( $field_mappings as $field ) {
		if ( ! $recipe->$field ) continue; 

		if ( $field == 'body' ) {
			$value = array(array('value' => check_plain(trim($recipe->$field))));
		} 
		elseif ( $field == 'field_directions' || $field == 'field_ingredients' ) {
			$value = array();
			foreach ( $recipe->$field as $item ) {
				if ( check_plain(trim($item)) ) {
					$value[] = array('value' => check_plain(trim($item)));
				}
			}
		}
		elseif ( $field == 'field_image') {
			$value = get_remote_file($recipe->$field);
			if ($value) {
				$value = array((array) $value);
			}
		}
		elseif ( $field == 'field_tags') {
			$vocab = taxonomy_vocabulary_machine_name_load('tags');
			$terms = array();
			foreach ( $recipe->$field as $tag ) {
				$term = (object) array('name' => check_plain($tag), 'vid' => $vocab->vid);
				taxonomy_term_save($term);
				$terms[] = array('tid' => $term->tid);
			}
			if ( empty($terms) ) continue;
			$value = $terms;
		} 
		else {
			$value = array(array('value' => check_plain(trim($recipe->$field))));
		}


		$node->{$field} = array('en' => $value);
	}
	display_text_progress_bar(count($recipes));

	try {
		node_save($node);
	} catch (Exception $e) {
		print_r($node);
		print_r($e);
		die();
	}
}

function get_remote_file($url) {
	return system_retrieve_file($url, 'public://', TRUE, FILE_EXISTS_REPLACE);
}