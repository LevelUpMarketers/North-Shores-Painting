<?php

/**
 * Overlay: Category Tag.
 *
 * @package TotalTheme
 * @subpackage Partials
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( 'outside_link' !== $position ) {
	return;
}

$post_type = get_post_type();
$terms     = [];

// Get terms.
if ( in_array( $post_type, [ 'wpex_card', 'elementor_library', 'wpex_templates' ] ) ) {
	$sample_term = new stdClass();
	$sample_term->name = esc_html( 'Sample Term', 'total' );
	$sample_term->slug = 'sample-term-0';
	$terms[] = $sample_term;
} elseif ( $taxonomy = wpex_get_post_type_cat_tax( $post_type ) ) {
	if ( isset( $args['first_term_only'] ) && true === $args['first_term_only'] ) {
		$first_term = totaltheme_get_post_primary_term( get_post(), $taxonomy );
		if ( $first_term ) {
			$terms[] = $first_term;
		}
	} else {
		$terms = (array) get_the_terms( get_the_ID(), $taxonomy );
	}
}

if ( ! $terms || is_wp_error( $terms ) ) {
	return;
}

?>

<div class="overlay-category-tag theme-overlay wpex-absolute wpex-top-0 wpex-left-0 wpex-z-10 wpex-uppercase wpex-text-xs wpex-font-semibold wpex-clr"><?php

	$count = 0;
	foreach ( $terms as $term ) {
		$count++;

		$link_class = [
			'term-' . sanitize_html_class( $term->slug ),
			'count-' . sanitize_html_class( $count ),
			'wpex-block',
			'wpex-float-left',
			'wpex-mr-5',
			'wpex-mb-5',
			'wpex-text-white',
			'wpex-hover-text-white',
			'wpex-bg-black',
			'wpex-py-5',
			'wpex-px-10',
			'wpex-no-underline',
			'wpex-transition-colors',
			'wpex-duration-200',
		];

		if ( ! isset( $sample_term ) ) {
			$link_class[] = totaltheme_get_term_color_background_classname( $term );
		}

		/**
		 * Filters the category tag overlay link class.
		 *
		 * @param array $class
		 */
		$link_class = (array) apply_filters( 'wpex_overlay_category_tag_link_class', $link_class );

		$attributes = [
			'href' => ! isset( $sample_term ) ? get_term_link( $term->term_id, $taxonomy ) : '#',
			'class' => $link_class,
		];

		echo wpex_parse_html( 'a', $attributes, esc_html( $term->name ) );
	}

?></div>
