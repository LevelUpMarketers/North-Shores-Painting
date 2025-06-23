<?php

namespace TotalThemeCore\WPBakery\Autocomplete;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery AutoComplete => Woocommerce Categories.
 */
final class Testimonial_Categories {

	public static function callback( $search_string ) {
		$testimonials_categories = [];
		$get_terms = \get_terms( 'testimonials_category', [
			'hide_empty' => false,
			'search'     => $search_string,
		] );
		if ( $get_terms ) {
			foreach ( $get_terms as $term ) {
				if ( $term->parent ) {
					$parent = \get_term( $term->parent, 'testimonials_category' );
					$label = "{$term->name} ({$parent->name})";
				} else {
					$label = $term->name;
				}
				$testimonials_categories[] = [
					'label' => $label,
					'value' => $term->term_id,
				];
			}
		}
		return $testimonials_categories;
	}

	public static function render( $data ) {
		$value = $data['value'];
		$term = \get_term_by( 'term_id', \intval( $value ), 'testimonials_category' );
		if ( \is_object( $term ) ) {
			if ( $term->parent ) {
				$parent = \get_term( $term->parent, 'testimonials_category' );
				$label = "{$term->name} ({$parent->name})";
			} else {
				$label = $term->name;
			}
			return [
				'label' => $label,
				'value' => $value,
			];
		}
		return $data;
	}

}
