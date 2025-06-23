<?php

namespace TotalThemeCore\WPBakery\Autocomplete;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery AutoComplete => Taxonomy Terms Ids.
 */
final class Taxonomy_Terms_Ids {

	public static function callback( $search_string ) {
		$terms_list = [];
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
						'label'    => "{$term->name} ({$term->taxonomy})",
						'value'    => $term->term_taxonomy_id ?? $term->term_id,
						'group_id' => \sanitize_key( $taxonomy->labels->name ),
						'group'    => $taxonomy->labels->name,
					];
				}
			}
		}
		return $terms_list;
	}

	public static function render( $data ) {
		$value = $data['value'] ?? '';
		if ( $value && \is_numeric( $value ) ) {
			$term_obj = \get_term_by( 'term_taxonomy_id', $value );
			if ( $term_obj && ! \is_wp_error( $term_obj ) ) {
				$data['label'] = "{$term_obj->name} ({$term_obj->taxonomy})";
			} else {
				$data['label'] = \sprintf( \esc_html__( 'Deleted Term: %s', 'total-theme-core' ), $value );
			}
		}
		return $data;
	}

}
