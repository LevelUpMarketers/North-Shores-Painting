<?php

namespace TotalThemeCore\WPBakery\Autocomplete;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery AutoComplete => Taxonomies.
 */
final class Taxonomies {

	public static function callback( $search_string ) {
		$taxonomies_list = [];
		$taxonomies = \get_taxonomies( [
			'public' => true,
		] );
		foreach ( $taxonomies as $taxonomy ) {
			$tax = \get_taxonomy( $taxonomy );
			$label = $tax->labels->name;
			if ( false !== \stripos( $label, $search_string ) ) {
				$taxonomies_list[] = [
					'label' => $label,
					'value' => $taxonomy,
				];
			}
		}
		return $taxonomies_list;
	}

	public static function render( $data ) {
		$value = $data['value'];
		$tax   = \get_taxonomy( $value );
		if ( \is_object( $tax ) && ! empty( $tax->labels->name ) ) {
			return [
				'label' => $tax->labels->name,
				'value' => $value,
			];
		}
		return $data;
	}

}
