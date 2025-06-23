<?php

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*/

	# Videos
	# Archives
	# Entries
	# Single
	# Related
	# Cards

/*-------------------------------------------------------------------------------*/
/* [ Videos ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns portfolio featured video url.
 */
if ( ! function_exists( 'wpex_get_portfolio_featured_video_url' ) ) {
	function wpex_get_portfolio_featured_video_url( $post_id = '') {
		return wpex_get_post_video( $post_id );
	}
}

/**
 * Gets the portfolio featured video.
 */
function wpex_get_portfolio_post_video( $video = '' ) {
	if ( ! $video ) {
		$video = wpex_get_post_video();
	}

	$video = wpex_get_post_video_html( $video );

	if ( ! empty( $video ) ) {
		return '<div class="portfolio-featured-video">' . $video . '</div>';
	}
}

/**
 * Displays the portfolio featured video.
 */
if ( ! function_exists( 'wpex_portfolio_post_video' ) ) {
	function wpex_portfolio_post_video( $post_id = '', $video = false ) {
		echo wpex_get_portfolio_post_video( $post_id, $video );
	}
}


/*-------------------------------------------------------------------------------*/
/* [ Archives ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns the portfolio loop top class.
 */
function wpex_portfolio_loop_top_class() {
	$classes = wpex_get_portfolio_wrap_classes();

	/**
	 * Filters the portfolio loop top element classes.
	 *
	 * @param array $classes
	 */
	$classes = (array) apply_filters( 'wpex_portfolio_loop_top_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/**
 * Returns classes for the portfolio wrap.
 */
if ( ! function_exists( 'wpex_get_portfolio_wrap_classes' ) ) {
	function wpex_get_portfolio_wrap_classes() {
		$grid_style = wpex_portfolio_archive_grid_style();

		$classes = [
			'wpex-row',
		];

		// Masonry class.
		if ( 'masonry' === $grid_style || 'no-margins' === $grid_style ) {
			$classes[] = 'wpex-masonry-grid';
			wpex_enqueue_isotope_scripts(); // This is a good spot to enqueue grid scripts
		}

		// Add grid style class.
		$classes[] = 'portfolio-' . sanitize_html_class( $grid_style );

		// Add gap class.
		if ( 'no-margins' === $grid_style ) {
			$classes[] = wpex_gap_class( '0px' );
		} elseif ( $gap = get_theme_mod( 'portfolio_archive_grid_gap' ) ) {
			$classes[] = wpex_gap_class( $gap );
		}

		// Clear floats.
		$classes[] = 'wpex-clr';

		// Sanitize.
		$classes = array_map( 'esc_attr', $classes );

		/**
		 * Filters the portfolio wrap element classes.
		 *
		 * @param array $classes
		 */
		$classes = (array) apply_filters( 'wpex_portfolio_wrap_classes', $classes );

		// Convert classes into string.
		$classes = implode( ' ', $classes );

		return $classes;
	}
}

/**
 * Returns portfolio archive columns.
 */
function wpex_portfolio_archive_columns() {
	$columns = get_theme_mod( 'portfolio_entry_columns' ) ?: 4;
	return apply_filters( 'wpex_portfolio_archive_columns', $columns );
}

/**
 * Returns portfolio archive grid style.
 */
function wpex_portfolio_archive_grid_style() {
	$style = get_theme_mod( 'portfolio_archive_grid_style' ) ?: 'fit-rows';
	return (string) apply_filters( 'wpex_portfolio_archive_grid_style', $style );
}

/**
 * Checks if match heights are enabled for the portfolio.
 */
if ( ! function_exists( 'wpex_portfolio_match_height' ) ) {
	function wpex_portfolio_match_height(): bool {
		return ( get_theme_mod( 'portfolio_archive_grid_equal_heights' ) && 'fit-rows' === wpex_portfolio_archive_grid_style() );
	}
}

/**
 * Returns classes for the portfolio grid.
 */
if ( ! function_exists( 'wpex_portfolio_column_class' ) ) {
	function wpex_portfolio_column_class( $loop = '' ) {
		if ( ! $loop ) {
			$loop = wpex_get_loop_instance();
		}
		if ( 'related' === $loop ) {
			$columns = get_theme_mod( 'portfolio_related_columns', 4 );
		} else {
			$columns = wpex_portfolio_archive_columns();
		}
		return wpex_row_column_width_class( $columns );
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Entries ]
/*-------------------------------------------------------------------------------*/

/**
 * Get portfolio entry supported media types.
 */
function wpex_portfolio_entry_supported_media() {
	$supported_media = [
		'video',
		'thumbnail',
	];
	return (array) apply_filters( 'wpex_portfolio_entry_supported_media', $supported_media );
}

/**
 * Get Portfolio entry media type.
 */
function wpex_portfolio_entry_media_type() {
	$supported_media = wpex_portfolio_entry_supported_media();
	if ( in_array( 'video', $supported_media, true ) && wpex_has_post_video() ) {
		$type = 'video';
	} elseif ( in_array( 'thumbnail', $supported_media, true ) && has_post_thumbnail() ) {
		$type = 'thumbnail';
	} else {
		$type = ''; // important
	}
	return (string) apply_filters( 'wpex_portfolio_entry_media_type', $type );
}

/**
 * Portfolio entry class.
 */
function wpex_portfolio_entry_class() {
	$classes = wpex_portfolio_entry_classes();
	$class = (array) apply_filters( 'wpex_portfolio_entry_class', $classes );
	post_class( $class );
}

/**
 * Returns portfolio entry classs.
 */
function wpex_portfolio_entry_classes( $loop = '' ) {
	if ( ! $loop ) {
		$loop = wpex_get_loop_instance();
	}

	$classes   = [];
	$classes[] = 'portfolio-entry';
	$classes[] = 'loop-' . sanitize_html_class( $loop );
	$classes[] = 'col';
	$classes[] = wpex_portfolio_column_class( $loop );

	if ( $loop_counter = wpex_get_loop_counter() ) {
		$classes[] = 'col-' . absint( $loop_counter );
	}

	if ( 'archive' === $loop ) {
		$grid_style = wpex_portfolio_archive_grid_style();
		if ( 'masonry' === $grid_style || 'no-margins' === $grid_style ) {
			$classes[] = 'wpex-masonry-col';
		}
	}

	$classes = array_map( 'esc_attr', $classes );

	return (array) apply_filters( 'wpex_portfolio_entry_classes', $classes, $loop );
}

/**
 * Portfolio entry inner class.
 */
function wpex_portfolio_entry_inner_class( $loop = '' ) {
	if ( ! $loop ) {
		$loop = wpex_get_loop_instance();
	}

	$class = [
		'portfolio-entry-inner',
		'wpex-last-mb-0',
	];

	if ( wpex_portfolio_match_height() ) {
		$class[] = 'wpex-flex';
		$class[] = 'wpex-flex-col';
		$class[] = 'wpex-flex-grow';
	} else {
		$class[] = 'wpex-clr';
	}

	$class = (array) apply_filters( 'wpex_portfolio_entry_inner_class', $class, $loop );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Portfolio entry media class.
 */
function wpex_portfolio_entry_media_class() {
	$media_type = wpex_portfolio_entry_media_type();

	$class = [
		'portfolio-entry-media',
		'entry-media',
		'wpex-relative',
	];

	if ( 'thumbnail' === $media_type ) {
		$overlay = totaltheme_call_static(
			'Overlays',
			'get_entry_image_overlay_style',
			'portfolio'
		);
		if ( $overlay ) {
			$overlay_class = (string) totaltheme_call_static(
				'Overlays',
				'get_parent_class',
				(string) $overlay
			);
			if ( $overlay_class ) {
				$class[] = $overlay_class;
			}
		}
		if ( $animation_classes = wpex_get_entry_image_animation_classes() ) {
			$class[] = $animation_classes;
		}
	}

	$class = (array) apply_filters( 'wpex_portfolio_entry_media_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Portfolio entry details class.
 */
function wpex_portfolio_entry_content_class() {
	$class = [
		'portfolio-entry-details',
		'wpex-boxed',
		'wpex-last-mb-0'
	];

	if ( wpex_portfolio_match_height() ) {
		$class[] = 'wpex-flex-grow';
	} else {
		$class[] = 'wpex-clr';
	}

	$class = (array) apply_filters( 'wpex_portfolio_entry_content_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Portfolio entry title class.
 */
function wpex_portfolio_entry_title_class() {
	$class = [
		'portfolio-entry-title',
		'entry-title',
		'wpex-mb-5',
	];

	$class = (array) apply_filters( 'wpex_portfolio_entry_title_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Portfolio entry excerpt class.
 */
function wpex_portfolio_entry_excerpt_class() {
	$class = [
		'portfolio-entry-excerpt',
		'wpex-my-15',
		'wpex-last-mb-0',
		'wpex-text-pretty',
	];
	$class = (array) apply_filters( 'wpex_portfolio_entry_excerpt_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Displays the media (featured image or video ) for the portfolio entries.
 */
if ( ! function_exists( 'wpex_portfolio_entry_media' ) ) {
	function wpex_portfolio_entry_media() {
		get_template_part( 'partials/portfolio/entry-media' );
	}
}

/**
 * Returns thumbnail HTML for the portfolio entries.
 */
function wpex_get_portfolio_entry_thumbnail( $loop = '' ) {
	if ( ! $loop ) {
		$loop = wpex_get_loop_instance();
	}
	$args = [
		'size'  => ( 'archive' === $loop ) ? 'portfolio_entry' : 'portfolio_related',
		'class' => 'portfolio-entry-media-img portfolio-entry-img wpex-align-middle',
	];
	$args = (array) apply_filters( 'wpex_get_portfolio_entry_thumbnail_args', $args );
	return wpex_get_post_thumbnail( $args );
}

/**
 * Echo portfolio entry thumbnail.
 */
function wpex_portfolio_entry_thumbnail() {
	echo wpex_get_portfolio_entry_thumbnail();
}

/**
 * Check if portfolio entry content is enabled.
 */
function wpex_has_portfolio_entry_content() {
	$loop = wpex_get_loop_instance();
	if ( 'related' === $loop ) {
		$check = get_theme_mod( 'portfolio_related_excerpts', true );
	} else {
		$check = get_theme_mod( 'portfolio_entry_details', true );
	}
	return (bool) apply_filters( 'wpex_has_portfolio_entry_content', $check );
}

/**
 * Displays the details for the portfolio entries.
 * @deprecated
 */
if ( ! function_exists( 'wpex_portfolio_entry_content' ) ) {
	function wpex_portfolio_entry_content() {
		get_template_part( 'partials/portfolio/entry-content' );
	}
}

/**
 * Returns portfolio entry excerpt length.
 */
function wpex_portfolio_entry_excerpt_length() {
	$length = get_theme_mod( 'portfolio_entry_excerpt_length', 20 );
	if ( 'related' === wpex_get_loop_instance() ) {
		$length = get_theme_mod( 'portfolio_related_entry_excerpt_length', $length );
	}
	return (int) apply_filters( 'wpex_portfolio_entry_excerpt_length', $length );
}

/*-------------------------------------------------------------------------------*/
/* [ Single ]
/*-------------------------------------------------------------------------------*/

/**
 * Get Portfolio single supported media types.
 */
function wpex_portfolio_single_supported_media() {
	$supported_media = array(
		'gallery',
		'video',
		'thumbnail',
	);
	return (array) apply_filters( 'wpex_portfolio_single_supported_media', $supported_media );
}

/**
 * Get Portfolio single format.
 */
function wpex_portfolio_single_media_type() {
	$supported_media = wpex_portfolio_single_supported_media();
	if ( in_array( 'video', $supported_media, true ) && wpex_has_post_video() ) {
		$type = 'video';
	} elseif ( in_array( 'gallery', $supported_media, true ) && wpex_has_post_gallery() ) {
		$type = 'gallery';
	} elseif ( in_array( 'thumbnail', $supported_media, true ) && has_post_thumbnail() ) {
		$type = 'thumbnail';
	} else {
		$type = ''; //important
	}
	return (string) apply_filters( 'wpex_portfolio_single_media_type', $type );
}

/**
 * Returns thumbnail HTML for the portfolio posts.
 */
function wpex_get_portfolio_post_thumbnail( $args = array() ) {
	$defaults = [
		'size'  => 'portfolio_post',
		'class' => 'portfolio-single-media-img wpex-align-middle',
	];
	$args = wp_parse_args( $args, $defaults );
	$args = (array) apply_filters( 'wpex_get_portfolio_post_thumbnail_args', $args ); // deprecated 5.5.3
	$args = (array) apply_filters( 'wpex_portfolio_post_thumbnail_args', $args );
	return wpex_get_post_thumbnail( $args );
}

/**
 * Portfolio single media class.
 */
function wpex_portfolio_single_media_class() {
	$class = [
		'single-media',
		'wpex-mb-20',
	];
	$class = (array) apply_filters( 'wpex_portfolio_single_media_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Portfolio single content class.
 */
function wpex_portfolio_single_content_class() {
	$class = [
		'single-content',
		'entry',
	];
	if ( totaltheme_call_static( 'Integration\WPBakery\Helpers', 'post_has_wpbakery', get_the_ID() ) ) {
		$class[] = totaltheme_has_classic_styles() ? 'wpex-mt-20' : 'wpex-mt-40';
	} else {
		$class[] = totaltheme_has_classic_styles() ? 'wpex-mb-40' : 'wpex-my-40';
	}
	$class[] = 'wpex-clr';
	$class = (array) apply_filters( 'wpex_portfolio_single_content_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Portfolio single header class.
 */
function wpex_portfolio_single_header_class() {
	$class = [
		'single-header',
	];
	if ( ! totaltheme_has_classic_styles() ) {
		$class[] = 'wpex-mb-15';
	}
	$class = (array) apply_filters( 'wpex_portfolio_single_header_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Portfolio single title class.
 */
function wpex_portfolio_single_title_class() {
	$class = [
		'single-post-title',
		'entry-title',
		totaltheme_has_classic_styles() ? 'wpex-mb-10' : 'wpex-m-0',
		'wpex-text-3xl',
	];
	$class = (array) apply_filters( 'wpex_portfolio_single_title_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Portfolio single comments class.
 */
function wpex_portfolio_single_comments_class() {
	$class = [
		'single-comments',
		'wpex-mb-40',
		'wpex-clr',
	];
	$class = (array) apply_filters( 'wpex_portfolio_single_comments_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Related ]
/*-------------------------------------------------------------------------------*/

/**
 * Portfolio related query.
 */
function wpex_portfolio_single_related_query() {
	$post_id = get_the_ID();

	if ( wpex_validate_boolean( get_post_meta( $post_id, 'wpex_disable_related_items', true ) ) ) {
		return false;
	}

	// Get posts count.
	$posts_count = absint( get_theme_mod( 'portfolio_related_count', 4 ) );

	if ( empty( $posts_count ) || '0' === $posts_count ) {
		return;
	}

	// Related query arguments.
	$args = [
		'post_type'      => 'portfolio',
		'orderby'        => 'date',
		'order'          => 'desc',
		'posts_per_page' => $posts_count,
		'post__not_in'   => [ $post_id ],
		'no_found_rows'  => true,
	];

	// Add custom orderby param.
	$orderby = get_theme_mod( 'portfolio_related_orderby' );
	if ( $orderby && $orderby_safe = sanitize_sql_orderby( (string) $orderby ) ) {
		$args['orderby'] = $orderby_safe;
	}

	// Add custom order param.
	$order = get_theme_mod( 'portfolio_related_order' );
	if ( $order && in_array( strtolower( $order ), [ 'asc', 'desc' ] ) ) {
		$args['order'] = $order;
	}

	// Related by taxonomy.
	if ( apply_filters( 'wpex_related_in_same_cat', true ) ) {

		// Add categories to query.
		$related_taxonomy = get_theme_mod( 'portfolio_related_taxonomy', 'portfolio_category' );

		// Generate related by taxonomy args.
		if ( 'null' !== $related_taxonomy && taxonomy_exists( $related_taxonomy ) ) {
			if ( $primary_term = totaltheme_get_post_primary_term( $post_id, $related_taxonomy, false ) ) {
				$terms = array( $primary_term->term_id );
			} else {
				$get_terms = get_the_terms( $post_id, $related_taxonomy );
				if ( $get_terms && ! is_wp_error( $get_terms ) ) {
					$terms = wp_list_pluck( $get_terms, 'term_id' );
				}
			}
			if ( ! empty( $terms ) ) {
				$args['tax_query'] = [
					'relation' => 'AND',
					[
						'taxonomy' => $related_taxonomy,
						'field'    => 'term_id',
						'terms'    => $terms,
					]
				];
			}
		}
	}

	$args = (array) apply_filters( 'wpex_related_portfolio_args', $args );

	if ( $args ) {
		return new wp_query( $args );
	}
}

/**
 * Portfolio single related heading.
 */
function wpex_portfolio_single_related_heading() {
	$text = wpex_portfolio_related_heading();

	if ( ! $text ) {
		return;
	}

	wpex_heading( [
		'tag'           => get_theme_mod( 'related_heading_tag' ) ?: 'h3',
		'content'		=> esc_html( $text ),
		'classes'		=> [
			'related-portfolio-posts-heading',
		],
		'apply_filters'	=> 'portfolio_related',
	] );
}

/**
 * Portfolio related heading.
 */
function wpex_portfolio_related_heading() {
	return wpex_get_translated_theme_mod( 'portfolio_related_title' ) ?: esc_html__( 'Related Projects', 'total' );
}

/**
 * Portfolio related class.
 */
function wpex_portfolio_single_related_class() {
	$class = array(
		'related-portfolio-posts',
		'wpex-mb-40',
	);
	if ( 'full-screen' === wpex_content_area_layout() ) {
		$class[] = 'container';
	}
	$class = (array) apply_filters( 'wpex_portfolio_single_related_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Portfolio single related row class.
 *
 * @todo deprecate wpex_related_portfolio_row_classes filter
 */
function wpex_portfolio_single_related_row_class() {
	$classes = [
		'wpex-row',
		'wpex-clr'
	];
	if ( $gap = get_theme_mod( 'portfolio_related_gap' ) ) {
		$classes[] = wpex_gap_class( $gap );
	}
	$classes = (array) apply_filters( 'wpex_portfolio_single_related_row_class', $classes );
	if ( $classes ) {
		echo 'class="' . esc_attr( apply_filters( 'wpex_related_portfolio_row_classes', implode( ' ', $classes ) ) ) . '"';
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Cards ]
/*-------------------------------------------------------------------------------*/

/**
 * Portfolio Card Entry.
 */
function wpex_portfolio_entry_card() {
	$instance = wpex_get_loop_instance();

	if ( 'related' === $instance ) {
		$card_style = get_theme_mod( 'portfolio_related_entry_card_style' );
	} else {
		$term_meta_check = wpex_get_term_meta( '', 'wpex_entry_card_style', true );
		if ( ! empty( $term_meta_check ) ) {
			$card_style = $term_meta_check;
		} else {
			$card_style = get_theme_mod( 'portfolio_entry_card_style' );
		}
	}

	$card_style = (string) apply_filters( 'wpex_portfolio_entry_card_style', $card_style );

	if ( ! $card_style ) {
		return false;
	}

	$args = [
		'style'          => $card_style,
		'post_id'        => get_the_ID(),
		'thumbnail_size' => ( 'related' === $instance ) ? 'portfolio_related' : 'portfolio_entry',
		'excerpt_length' => wpex_portfolio_entry_excerpt_length(),
	];

	$overlay = totaltheme_call_static(
		'Overlays',
		'get_entry_image_overlay_style',
		'portfolio'
	);

	if ( $overlay ) {
		$args['thumbnail_overlay_style'] = $overlay;
	}

	if ( $hover_style = get_theme_mod( 'portfolio_entry_image_hover_animation', null ) ) {
		$args['thumbnail_hover'] = $hover_style;
	}

	$args = (array) apply_filters( 'wpex_portfolio_entry_card_args', $args );

	wpex_card( $args );

	return true;
}
