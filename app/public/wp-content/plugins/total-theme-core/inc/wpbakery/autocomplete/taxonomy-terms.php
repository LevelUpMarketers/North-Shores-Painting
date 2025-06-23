<?php

namespace TotalThemeCore\WPBakery\Autocomplete;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery AutoComplete => Taxonomy Terms.
 */
final class Taxonomy_Terms {

	public static function callback( $search_string ) {
		$terms_list = [];
		$terms_list[] = [
			'label'    => \esc_html__( 'Standard Posts', 'total-theme-core' ),
			'value'    => 'post-format-standard',
			'group_id' => 'format',
			'group'    => \esc_html__( 'Formats', 'total-theme-core' ),
		];
		$taxonomies = \get_taxonomies( [
			'public' => true
		], 'objects' );
		foreach ( $taxonomies as $taxonomy ) {
			$terms = \get_terms( $taxonomy->name, [
				'hide_empty' => false,
				'search'     => $search_string,
			] );
			if ( $terms ) {
				foreach ( $terms as $term ) {
					$terms_list[] = [
						'label'    => $term->name,
						'value'    => $term->slug,
						'group_id' => $taxonomy->labels->name,
						'group'    => $taxonomy->labels->name,
					];
				}
			}
		}
		return $terms_list;
	}

	public static function render( $data ) {
		return $data; // No way around it, must show slug :(
	}

}
