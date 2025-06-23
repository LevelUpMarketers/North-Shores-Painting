<?php

namespace TotalThemeCore\WPBakery\Autocomplete;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery AutoComplete => Categories.
 */
final class Categories {

	public static function callback( $search_string ) {
		$categories = [];
		$get_terms = \get_terms( 'category', [
			'hide_empty' => false,
			'search'     => $search_string,
		] );
		if ( $get_terms ) {
			foreach ( $get_terms as $term ) {
				$categories[] = [
					'label' => $term->name,
					'value' => $term->term_id,
				];
			}
		}
		return $categories;
	}

	public static function render( $data ) {
		$value = $data['value'];
		$category = \get_term_by( 'term_id', \intval( $value ), 'category' );
		if ( \is_object( $category ) ) {
			return [
				'label' => $category->name,
				'value' => $value,
			];
		}
		return $data;
	}

}
