<?php

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*/

	# Core
	# Archives
	# Entries
	# Single
	# Cards

/*-------------------------------------------------------------------------------*/
/* [ Core ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if a given testimonial has a rating.
 */
function wpex_has_testimonial_rating( $post = null ) {
	$rating = wpex_get_testimonial_rating( $post );
	return (bool) apply_filters( 'wpex_has_testimonial_rating', (bool) $rating, $post, $rating );
}

/**
 * Get testimonial rating.
 */
function wpex_get_testimonial_rating( $post = null ): string {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	$rating = wpex_get_star_rating( '', $post->ID );
	return (string) apply_filters( 'wpex_testimonial_rating', $rating, $post );
}

/**
 * Display testimonial rating.
 */
function wpex_testimonial_rating( $post = null ) {
	echo wpex_get_testimonial_rating( $post );
}

/**
 * Check if a given testimonial has an author.
 */
function wpex_has_testimonial_author( $post = null ) {
	$author = wpex_get_testimonial_author( $post );
	return (bool) apply_filters( 'wpex_has_testimonial_author', (bool) $author, $post, $author );
}

/**
 * Get testimonial author.
 */
function wpex_get_testimonial_author( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	$author = get_post_meta( $post->ID, 'wpex_testimonial_author', true ) ;
	$author = (string) apply_filters( 'wpex_testimonial_author', $author, $post );
	return $author;
}

/**
 * Check if a given testimonial has a company set.
 */
function wpex_has_testimonial_company( $post = null ) {
	$company = wpex_get_testimonial_company( $post );
	$check = (bool) apply_filters( 'wpex_has_testimonial_company', (bool) $company, $post, $company );
	return $check;
}

/**
 * Get testimonial company.
 */
function wpex_get_testimonial_company( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	$company = get_post_meta( $post->ID, 'wpex_testimonial_company', true );
	$company = apply_filters( 'wpex_testimonial_company', $company );
	return $company;
}

/**
 * Check if a given testimonial has a company url set.
 */
function wpex_has_testimonial_company_link( $post = null ) {
	$company = wpex_get_testimonial_company_url( $post );
	$check = (bool) apply_filters( 'wpex_has_testimonial_company_link', (bool) $company, $post, $company );
	return $check;
}

/**
 * Get testimonial company url.
 */
function wpex_get_testimonial_company_url( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	$url = get_post_meta( $post->ID, 'wpex_testimonial_url', true );
	$url = apply_filters( 'wpex_testimonial_company_url', $url, $post );
	return esc_url( $url );
}

/**
 * Get testimonial company url target.
 */
function wpex_get_testimonial_company_link_target() {
	$target =  '_blank';
	$target = (string) apply_filters( 'wpex_testimonial_company_link_target', $target );
	return $target;
}

/*-------------------------------------------------------------------------------*/
/* [ Archives ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns testimonials archive grid style.
 */
function wpex_testimonials_archive_grid_style() {
	$style = get_theme_mod( 'testimonials_archive_grid_style' ) ?: 'fit-rows';
	$style = apply_filters( 'wpex_testimonials_archive_grid_style', $style );
	return $style;
}

/*-------------------------------------------------------------------------------*/
/* [ Entries ]
/*-------------------------------------------------------------------------------*/

/**
 * Testimonials entry class.
 */
function wpex_testimonials_entry_class() {
	$grid_style = wpex_testimonials_archive_grid_style();
	$columns    = wpex_testimonials_archive_columns();

	$class = array(
		'testimonial-entry',
	);

	// Grid classes.
	if ( 'singular' !== wpex_get_loop_instance() ) {

		// Add grid column class.
		if ( $col_class = wpex_row_column_width_class( $columns ) ) {
			$class[] = 'col';
			$class[] = $col_class;
		}

		// Add counter class.
		$loop_counter = wpex_get_loop_counter();

		if ( $loop_counter ) {
			$class[] = 'col-' . sanitize_html_class( $loop_counter );
		}

		if ( 'masonry' === $grid_style ) {
			$class[] = 'wpex-masonry-col';
		}

	}

	$class = (array) apply_filters( 'wpex_testimonials_entry_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Testimonials entry content class.
 */
function wpex_testimonials_entry_content_class() {
	$class = array(
		'testimonial-entry-content',
		'wpex-relative', // for caret
		'wpex-boxed',
		'wpex-border-0',
		'wpex-clr',
	);
	$class = (array) apply_filters( 'wpex_testimonials_entry_content_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Testimonials entry title class.
 */
function wpex_testimonials_entry_title_class() {
	$class = [
		'testimonial-entry-title',
		'entry-title',
		'wpex-mb-10',
	];
	$class = (array) apply_filters( 'wpex_testimonials_entry_title_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Testimonials entry bottom class.
 */
function wpex_testimonials_entry_bottom_class() {
	$class = [
		'testimonial-entry-bottom',
		'wpex-flex',
		'wpex-flex-wrap',
		'wpex-mt-20',
	];
	if ( ! totaltheme_has_classic_styles() ) {
		$class[] = 'wpex-gap-15';
	}
	$class = (array) apply_filters( 'wpex_testimonials_entry_bottom_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Testimonials entry media class.
 */
function wpex_testimonials_entry_media_class( $custom_dims = false ) {
	$class = [
		'testimonial-entry-thumb',
		$custom_dims ? 'custom-dims' : 'default-dims',
		'wpex-flex-shrink-0',
	];
	if ( totaltheme_has_classic_styles() ) {
		$class[] = 'wpex-mr-20';
	}
	$class = (array) apply_filters( 'wpex_testimonials_entry_media_class', $class, $custom_dims );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Testimonials entry title class.
 */
function wpex_testimonials_entry_meta_class() {
	$class = array(
		'testimonial-entry-meta',
		'wpex-flex-grow',
	);
	$class = (array) apply_filters( 'wpex_testimonials_entry_meta_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Testimonials entry author class.
 */
function wpex_testimonials_entry_author_class() {
	$class = [
		'testimonial-entry-author',
		'entry-title',
	];
	if ( ! totaltheme_has_classic_styles() ) {
		$class[] = 'wpex-text-base';
	}
	$class = (array) apply_filters( 'wpex_testimonials_entry_author_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Testimonials entry rating class.
 */
function wpex_testimonials_entry_rating_class() {
	$class = [
		'testimonial-entry-rating',
	];
	if ( ! totaltheme_has_classic_styles() ) {
		$class[] = 'wpex-text-xs';
	}
	$class = (array) apply_filters( 'wpex_testimonials_entry_rating_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Testimonial entry company class.
 */
function wpex_testimonials_entry_company_class() {
	$class = [
		'testimonial-entry-company',
		'wpex-text-3',
	];
	if ( ! totaltheme_has_classic_styles() ) {
		$class[] = 'wpex-text-sm';
	}
	$class = (array) apply_filters( 'wpex_testimonials_entry_company_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Testimonial entry link target.
 */
function wpex_testimonials_entry_company_link_target() {
	$target = wpex_get_testimonial_company_link_target();
	if ( 'blank' === $target || '_blank' === $target ) {
		echo ' target="_blank" rel="noopener noreferrer"';
	}
}

/**
 * Display testimonial thumbnail
 */
function wpex_testimonials_entry_thumbnail( $args = array() ) {
	echo wpex_get_testimonials_entry_thumbnail( $args );
}

/**
 * Returns correct thumbnail HTML for the testimonials entries.
 */
function wpex_get_testimonials_entry_thumbnail( $args = [] ) {
	$classes = [
		'testimonials-entry-img',
		'wpex-align-middle',
		'wpex-rounded-full',
		'wpex-border',
		'wpex-p-2',
		'wpex-border-solid',
		'wpex-border-main',
	];
	$classes = apply_filters( 'wpex_testimonials_entry_thumbnail_class', $classes );
	$args = wp_parse_args( $args, [
		'size'  => 'testimonials_entry',
		'class' => $classes,
	] );
	return wpex_get_post_thumbnail( $args );
}

/**
 * Returns testimonials archive columns.
 */
function wpex_testimonials_archive_columns() {
	return get_theme_mod( 'testimonials_entry_columns', totaltheme_has_classic_styles() ? '4' : '3' );
}

/**
 * Returns the testimonials loop top class.
 */
function wpex_testimonials_loop_top_class() {
	$classes = wpex_get_testimonials_wrap_classes();
	$classes = (array) apply_filters( 'wpex_testimonials_loop_top_class', wpex_get_testimonials_wrap_classes() );
	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/**
 * Returns correct classes for the testimonials archive wrap.
 */
function wpex_get_testimonials_wrap_classes() {
	$classes = [
		'wpex-row',
	];

	// Get grid style
	$grid_style = wpex_testimonials_archive_grid_style();

	// This is a good spot to enqueue grid scripts
	if ( 'masonry' === $grid_style || 'no-margins' === $grid_style ) {
		$classes[] = 'wpex-masonry-grid';
		wpex_enqueue_isotope_scripts();
	}

	// Get grid style
	if ( 'masonry' === get_theme_mod( 'testimonials_archive_grid_style', 'fit-rows' ) ) {
		$classes[] = 'testimonials-masonry';
	}

	// Add gap
	if ( $gap = get_theme_mod( 'testimonials_archive_grid_gap' ) ) {
		$classes[] = 'gap-' . sanitize_html_class( $gap );
	}

	// Clear floats
	$classes[] = 'wpex-clr';

	// Sanitize
	$classes = array_map( 'esc_attr', $classes );
	$classes = apply_filters( 'wpex_testimonials_wrap_classes', $classes );

	// Turn array into string
	$classes = implode( ' ', $classes );

	return $classes;
}

/*-------------------------------------------------------------------------------*/
/* [ Single ]
/*-------------------------------------------------------------------------------*/

/**
 * Testimonials single layout.
 */
function wpex_get_testimonials_single_layout() {
	return apply_filters( 'wpex_testimonials_single_layout', get_theme_mod( 'testimonial_post_style', 'blockquote' ) );
}

/**
 * Testimonials single content class.
 */
function wpex_testimonials_single_content_class() {
	$class = [
		'single-content',
		'entry',
	];
	if ( ! totaltheme_call_static( 'Integration\WPBakery\Helpers', 'post_has_wpbakery', get_the_ID() ) ) {
		$class[] = 'wpex-mb-40';
	}
	$class[] = 'wpex-clr';
	$class = (array) apply_filters( 'wpex_testimonials_single_content_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Testimonial single comments class.
 */
function wpex_testimonials_single_comments_class() {
	$class = array(
		'single-comments',
		'wpex-mb-40',
		'wpex-clr',
	);
	$class = (array) apply_filters( 'wpex_testimonials_single_comments_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Cards ]
/*-------------------------------------------------------------------------------*/

/**
 * Testimonial Card Entry.
 */
function wpex_testimonials_entry_card() {
	$instance   = wpex_get_loop_instance();
	$card_style = get_theme_mod( 'testimonials_entry_card_style' );

	if ( 'related' === $instance ) {
		$card_style = get_theme_mod( 'testimonials_related_entry_card_style' ) ?: $card_style;
	} else {
		$term_meta_check = wpex_get_term_meta( '', 'wpex_entry_card_style', true );
		if ( ! empty( $term_meta_check ) ) {
			$card_style = $term_meta_check;
		}
	}

	$card_style = (string) apply_filters( 'wpex_testimonials_entry_card_style', $card_style );

	if ( ! $card_style ) {
		return false;
	}

	$args = [
		'style'          => $card_style,
		'post_id'        => get_the_ID(),
		'thumbnail_size' => 'testimonials_entry',
		'excerpt_length' => get_theme_mod( 'testimonials_entry_excerpt_length', '-1' ),
	];

	$args = (array) apply_filters( 'wpex_testimonials_entry_card_args', $args );

	wpex_card( $args );

	return true;
}
