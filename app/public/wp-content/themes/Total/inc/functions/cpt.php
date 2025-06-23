<?php

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*/

	# Entry
	# Single
	# Related
	# Cards

/*-------------------------------------------------------------------------------*/
/* [ Entry ]
/*-------------------------------------------------------------------------------*/

/**
 * Post Type Entry Supported Media Types.
 */
function wpex_cpt_entry_supported_media(): array {
	$supported_media = [
		'video',
		'audio',
		//'gallery', // not supported by default needs to be enabled via child theme
		'thumbnail',
	];
	return (array) apply_filters( 'wpex_cpt_entry_supported_media', $supported_media, get_post_type() );
}

/**
 * Get Post Type Entry media type.
 */
function wpex_cpt_entry_media_type(): string {
	$type            = '';
	$supported_media = wpex_cpt_entry_supported_media();
	if ( in_array( 'video', $supported_media, true ) && wpex_has_post_video() ) {
		$type = 'video';
	} elseif ( in_array( 'audio', $supported_media, true ) && wpex_has_post_audio() ) {
		$type = 'audio';
	} elseif ( in_array( 'gallery', $supported_media, true ) && wpex_has_post_gallery() ) {
		$type = 'gallery';
	} elseif ( in_array( 'thumbnail', $supported_media, true ) && has_post_thumbnail() ) {
		$type = 'thumbnail';
	}
	return (string) apply_filters( 'wpex_cpt_entry_media_type', $type );
}

/**
 * Get post type entry thumbnail size.
 */
function wpex_cpt_entry_thumbnail_size() {
	$size      = 'full';
	$post_type = get_post_type();
	$instance  = wpex_get_loop_instance();

	// Related entry image size
	if ( 'related' === $instance ) {
		$size = "{$post_type}_single_related";
	}

	// Standard entry image size
	else {

		$size = "{$post_type}_archive";

		if ( totaltheme_is_integration_active( 'post_types_unlimited' ) ) {

			$loadmore = \TotalTheme\Pagination\Load_More::get_data();

			if ( $loadmore ) {
				$taxonomy = $loadmore['query_vars']['taxonomy'] ?? null;
			}

			if ( ! empty( $taxonomy ) || is_tax() ) {
				$taxonomy = $taxonomy ?? get_query_var( 'taxonomy' );
				$ptu_check = wpex_get_ptu_tax_mod( $taxonomy, 'entry_image_size' );
				if ( ! empty( $ptu_check ) ) {
					$size = $ptu_check;
				}
			}
		}

	}

	return (string) apply_filters( "wpex_{$post_type}_entry_thumbnail_size", $size );
}

/**
 * Get Post Type Entry overlay style.
 * 
 * @todo move to overlays.php somehow.
 */
function wpex_cpt_entry_overlay_style(): string {
	$style   = '';
	$post_type = get_post_type();

	if ( 'related' === wpex_get_loop_instance() ) {
		$style = wpex_get_ptu_type_mod( $post_type, 'related_entry_overlay_style' );
		$style = apply_filters( 'wpex_cpt_single_related_overlay', $style ); // legacy
		$style = apply_filters( "wpex_{$post_type}_related_entry_overlay_style", $style ); // new in 5.0
	} else {
		$is_cpt_archive = is_post_type_archive();
		$loadmore       = \TotalTheme\Pagination\Load_More::get_data();

		if ( $loadmore ) {
			$taxonomy       = $loadmore['query_vars']['taxonomy'] ?? null;
			$post_type      = $loadmore['query_vars']['post_type'] ?? null;
			$is_cpt_archive = $taxonomy ? false : true;
		}

		if ( $is_cpt_archive ) {
			$style = wpex_get_ptu_type_mod( $post_type, 'entry_overlay_style' );
		}

		if ( ! empty( $taxonomy ) || is_tax() ) {
			$taxonomy = $taxonomy ?? get_query_var( 'taxonomy' );
			$style = wpex_get_ptu_tax_mod( $taxonomy, 'entry_overlay_style' );
		}

		$style = (string) apply_filters( "wpex_{$post_type}_entry_overlay_style", $style );
	}

	if ( ! $style ) {
		$style = 'none'; // !important! @todo revise
	}

	return (string) $style;
}

/**
 * Get post type entry excerpt length..5
 */
function wpex_cpt_entry_excerpt_length() {
	$post_type = get_post_type();
	$instance  = wpex_get_loop_instance();

	// Related entry excerpt length
	if ( 'related' === $instance ) {
		$length = '15';
		$ptu_length = wpex_get_ptu_type_mod( $post_type, 'related_entry_excerpt_length' );
		if ( isset( $ptu_length ) && '' !== trim( $ptu_length ) ) {
			$length = $ptu_length;
		}
		$length = apply_filters( 'wpex_cpt_single_related_excerpt_length', $length ); // legacy
		$length = apply_filters( "wpex_{$post_type}_single_related_excerpt_length", $length );
	}

	// Archives excerpt length
	else {
		$length = '40';

		if ( totaltheme_is_integration_active( 'post_types_unlimited' ) ) {
			$loadmore = \TotalTheme\Pagination\Load_More::get_data();

			// Allow the archive setting to work for all entries
			$ptu_length = wpex_get_ptu_type_mod( $post_type, 'entry_excerpt_length' );
			if ( isset( $ptu_length ) && '' !== trim( $ptu_length ) ) {
				$length = $ptu_length;
			}

			// Custom tax entry excerpt length.
			if ( $loadmore ) {
				$taxonomy = $loadmore['query_vars']['taxonomy'] ?? null;
			}

			if ( ! empty( $taxonomy ) || is_tax() ) {
				$taxonomy = $taxonomy ?? get_query_var( 'taxonomy' );
				$ptu_length = wpex_get_ptu_tax_mod( $taxonomy, 'entry_excerpt_length' );
				if ( isset( $ptu_length ) && '' !== trim( $ptu_length ) ) {
					$length = $ptu_length;
				}
			}

		}

		$length = apply_filters( "wpex_{$post_type}_entry_excerpt_length", $length );

	}

	return intval( $length ); // note use intval since we support -1 for full excerpt.
}

/**
 * Post Type Entry Class.
 */
function wpex_cpt_entry_class() {
	$class = wpex_get_archive_grid_entry_class();
	$class = (array) apply_filters( 'wpex_cpt_entry_class', $class );
	post_class( $class );
}

/**
 * Post Type Entry Inner Class.
 */
function wpex_cpt_entry_inner_class() {
	$class = [
		'cpt-entry-inner',
		'entry-inner',
	];
	$class[] = 'wpex-last-mb-0';
	$class[] = 'wpex-clr';
	$class   = (array) apply_filters( 'wpex_cpt_entry_inner_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Post Type Entry Media Class.
 */
function wpex_cpt_entry_media_class() {
	$media_type = wpex_cpt_entry_media_type();
	$class = [
		'cpt-entry-media',
		'cpt-entry-' . sanitize_html_class( $media_type ),
		'entry-media',
		'wpex-relative',
	];
	if ( 'thumbnail' === $media_type ) {
		$overlay = wpex_cpt_entry_overlay_style();
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
	}
	$class[] = 'wpex-mb-20';
	$class = (array) apply_filters( 'wpex_cpt_entry_media_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( trim( implode( ' ', $class ) ) ) . '"';
	}
}

/**
 * Post Type Entry Header Class.
 */
function wpex_cpt_entry_header_class() {
	$class = [
		'cpt-entry-header',
		'entry-header',
	];
	$class = (array) apply_filters( 'wpex_cpt_entry_header_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Post Type Entry Title Class.
 */
function wpex_cpt_entry_title_class() {
	$columns = (int) wpex_get_grid_entry_columns();

	$class = [
		'cpt-entry-title',
		'entry-title',
	];

	if ( 1 === $columns ) {
		$class[] = 'wpex-text-3xl';
	}

	$class = (array) apply_filters( 'wpex_cpt_entry_title_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Post Type Entry Excerpt Class.
 */
function wpex_cpt_entry_excerpt_class() {
	$class = [
		'cpt-entry-excerpt',
		'entry-excerpt',
		'wpex-text-pretty',
	];

	$columns = (int) wpex_get_grid_entry_columns();

	if ( 1 === $columns ) {
		$class[] = 'wpex-my-20';
	} else {
		$class[] = 'wpex-my-15';
	}

	$class[] = 'wpex-last-mb-0';
	$class[] = 'wpex-clr';

	$class = (array) apply_filters( 'wpex_cpt_entry_excerpt_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Post Type Entry Button Class.
 */
function wpex_cpt_entry_button_wrap_class() {
	$class = [
		'cpt-entry-readmore-wrap',
		'entry-readmore-wrap',
	];

	$columns = (int) wpex_get_grid_entry_columns();

	if ( 1 === $columns ) {
		$class[] = 'wpex-my-20';
	} else {
		$class[] = 'wpex-my-15';
	}

	$class[] = 'wpex-clr';

	$class = (array) apply_filters( 'wpex_cpt_entry_button_wrap_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Post Type Entry Button Class.
 */
function wpex_cpt_entry_button_class() {
	$args = [
		'style' => '',
		'color' => '',
	];

	$args = (array) apply_filters( 'wpex_' . get_post_type() . '_entry_button_args', $args );

	$button_class = wpex_get_button_classes( $args );

	if ( is_array( $button_class ) ) {
		$class = $button_class;
	} else {
		$class = explode( ' ', $button_class );
	}

	$class = (array) apply_filters( 'wpex_cpt_entry_button_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Returns Post Type Entry Button Text.
 */
function wpex_get_cpt_entry_button_text() {
	$post_type = get_post_type();
	$text = wpex_get_ptu_type_mod( $post_type, 'entry_readmore_text' ) ?: get_theme_mod( "{$post_type}_readmore_text" );
	$text = apply_filters( "wpex_{$post_type}_readmore_link_text", $text );
	if ( ! $text ) {
		if ( 'just_event' === $post_type ) {
			$text = esc_html__( 'View Event', 'total' );
		} else {
			$text = esc_html__( 'Read more', 'total' );
		}
	}
	return (string) apply_filters( 'wpex_cpt_entry_button_text', $text, $post_type );
}

/**
 * Outputs Type Entry Button Text.
 */
function wpex_cpt_entry_button_text() {
	echo wp_kses_post( wpex_get_cpt_entry_button_text() );
}

/**
 * Post Type Entry Thumbnail.
 */
function wpex_cpt_entry_thumbnail() {
	$post_type = get_post_type();
	$args = [
		'size'  => wpex_cpt_entry_thumbnail_size(),
		'class' => 'cpt-entry-media-img wpex-align-middle',
	];
	$args = apply_filters( "wpex_{$post_type}_entry_thumbnail_args", $args, $post_type );
	wpex_post_thumbnail( $args );
}

/**
 * Post Type Entry divider.
 */
function wpex_cpt_entry_divider() {
	$columns = wpex_get_grid_entry_columns();
	switch ( $columns ) {
		case '1':
			$divider = '<div class="cpt-entry-sep entry-sep wpex-divider wpex-my-40"></div>';
			break;
		default:
			$divider = '';
			break;
	}
	echo (string) apply_filters( 'wpex_cpt_entry_sep', $divider );
}

/*-------------------------------------------------------------------------------*/
/* [ Single ]
/*-------------------------------------------------------------------------------*/

/**
 * Get Post Type single supported media types.
 */
function wpex_cpt_single_supported_media(): array {
	$supported_media = [
		'video',
		'audio',
		'gallery',
		'thumbnail',
	];
	return (array) apply_filters( 'wpex_cpt_single_supported_media', $supported_media );
}

/**
 * Get Post type single format.
 */
function wpex_cpt_single_media_type(): string {
	$type = '';
	$supported_media = wpex_cpt_single_supported_media();
	if ( in_array( 'video', $supported_media ) && wpex_has_post_video() ) {
		$type = 'video';
	} elseif ( in_array( 'audio', $supported_media, true ) && wpex_has_post_audio() ) {
		$type = 'audio';
	} elseif ( in_array( 'gallery', $supported_media, true ) && wpex_has_post_gallery() ) {
		$type = 'gallery';
	} elseif ( in_array( 'thumbnail', $supported_media, true ) && has_post_thumbnail() ) {
		$type = 'thumbnail';
	}
	return (string) apply_filters( 'wpex_cpt_single_media_type', $type );
}

/**
 * Post Type Single Thumbnail.
 */
function wpex_cpt_single_thumbnail() {
	$post_type = get_post_type();

	$thumbnail_html = wpex_get_post_thumbnail( apply_filters( "wpex_{$post_type}_single_thumbnail_args", [
		'size'  => "{$post_type}_single",
		'class' => 'cpt-single-media-img wpex-align-middle',
	], $post_type ) );

	if ( shortcode_exists( 'featured_revslider' ) ) {
		$thumbnail_html = do_shortcode( "[featured_revslider]{$thumbnail_html}[/featured_revslider]" );
	}

	echo apply_filters( "wpex_{$post_type}_post_thumbnail", $thumbnail_html );
}

/**
 * Post Type single media class.
 */
function wpex_cpt_single_media_class() {
	$class = [
		'single-media',
		'wpex-relative',
		'wpex-mb-20',
	];
	if ( 'above' === wpex_get_custom_post_media_position() ) {
		$class[] = 'wpex-md-mb-30';
	}
	$class = (array) apply_filters( 'wpex_cpt_single_media_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Post Type single header class.
 */
function wpex_cpt_single_header_class() {
	$class = [
		'single-header',
		totaltheme_has_classic_styles() ? 'wpex-mb-10' : 'wpex-mb-15',
		'wpex-clr'
	];
	$class = (array) apply_filters( 'wpex_cpt_single_header_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Post Type single title class.
 */
function wpex_cpt_single_title_class() {
	$class = [
		'entry-title',
		'single-post-title',
		'wpex-text-3xl',
	];
	$class = (array) apply_filters( 'wpex_cpt_single_title_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Post Type single content class.
 */
function wpex_cpt_single_content_class() {
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

	$class = (array) apply_filters( 'wpex_cpt_single_content_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Related ]
/*-------------------------------------------------------------------------------*/

/**
 * Post Type single related class.
 */
function wpex_cpt_single_related_class() {
	$class = [
		'single-related',
		'related-posts',
		'wpex-mb-20',
	];

	if ( 'full-screen' === wpex_content_area_layout() ) {
		$class[] = 'container';
	}

	$class[] = 'wpex-clr';

	$class = (array) apply_filters( 'wpex_cpt_single_related_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Post Type single related row class.
 */
function wpex_cpt_single_related_row_class() {
	$classes = [
		'wpex-row',
		'wpex-clr'
	];
	if ( $ptu_gap = wpex_get_ptu_type_mod( get_post_type(), 'related_gap' ) ) {
		$classes[] = wpex_gap_class( $ptu_gap );
	}
	$classes = (array) apply_filters( 'wpex_cpt_single_related_row_class', $classes );
	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/**
 * Return cpt single related query.
 */
function wpex_cpt_single_related_query() {
	$post_id   = get_the_ID();
	$post_type = get_post_type();

	// Return if disabled via post meta.
	if ( wpex_validate_boolean( get_post_meta( $post_id, 'wpex_disable_related_items', true ) ) ) {
		return false;
	}

	// Posts count.
	$count = wpex_get_ptu_type_mod( $post_type, 'related_count' ) ?: get_theme_mod( "{$post_type}_related_count", 3 );

	// Return if count is empty or 0.
	if ( empty( $count ) || '0' === $count ) {
		return false;
	}

	// Related query arguments.
	$args = [
		'post_type'      => $post_type,
		'posts_per_page' => $count,
		'order'          => 'desc',
		'orderby'        => 'date',
		'post__not_in'   => [ $post_id ],
		'no_found_rows'  => true,
	];

	// Add custom orderby param.
	$orderby = wpex_get_ptu_type_mod( $post_type, 'related_orderby' ) ?: get_theme_mod( "{$post_type}_related_orderby" );
	if ( $orderby && $orderby_safe = sanitize_sql_orderby( (string) $orderby ) ) {
		$args['orderby'] = $orderby_safe;
	}

	// Add custom order param.
	$order = wpex_get_ptu_type_mod( $post_type, 'related_order' ) ?: get_theme_mod( "{$post_type}_related_order" );
	if ( $order && in_array( strtolower( $order ), [ 'asc', 'desc' ] ) ) {
		$args['order'] = $order;
	}

	// Related by taxonomy.
	$same_cat = apply_filters( 'wpex_cpt_single_related_in_same_term', true ); // legacy filter
	$same_cat = apply_filters( 'wpex_related_in_same_cat', $same_cat );

	if ( $same_cat ) {

		// Add categories to query.
		$related_taxonomy = wpex_get_ptu_type_mod( $post_type, 'related_taxonomy' );
		if ( empty( $related_taxonomy ) ) {
			$related_taxonomy = get_theme_mod( "{$post_type}_related_taxonomy", wpex_get_post_type_cat_tax() );
		}

		// Generate related by taxonomy args.
		if ( 'null' !== $related_taxonomy && taxonomy_exists( $related_taxonomy ) ) {
			$terms = [];
			$primary_term = totaltheme_get_post_primary_term( $post_id, $related_taxonomy, false );
			if ( $primary_term ) {
				$terms = [ $primary_term->term_id ];
			} else {
				$get_terms = get_the_terms( $post_id, $related_taxonomy );
				if ( $get_terms && ! is_wp_error( $get_terms ) ) {
					$terms = wp_list_pluck( $get_terms, 'term_id' );
				}
			}
			if ( $terms ) {
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

	$args = apply_filters( 'wpex_cpt_single_related_query_args', $args ); // @deprecated
	$args = (array) apply_filters( "wpex_related_{$post_type}_args", $args );

	if ( $args ) {
		return new wp_query( $args );
	}

}

/**
 * CPT single related entry class.
 */
function wpex_cpt_single_related_entry_class() {
	$classes = [
		'related-post',
		'col'
	];

	$columns = wpex_cpt_single_related_columns();

	if ( $columns ) {
		$classes[] = wpex_row_column_width_class( $columns );
	}

	$counter = wpex_get_loop_counter();

	if ( $counter ) {
		$classes[] = 'col-' . sanitize_html_class( $counter );
	}

	$classes[] = 'wpex-clr';

	$classes = (array) apply_filters( 'wpex_blog_single_related_entry_class', $classes );

	post_class( $classes );
}

/**
 * Return cpt single related columns.
 */
function wpex_cpt_single_related_columns() {
	$post_type = get_post_type();
	$columns   = wpex_get_ptu_type_mod( $post_type, 'related_columns' );
	if ( ! $columns ) {
		$columns = get_theme_mod( "{$post_type}_related_columns", 3 );
	}
	return apply_filters( 'wpex_cpt_single_related_columns', $columns );
}

/*-------------------------------------------------------------------------------*/
/* [ Cards ]
/*-------------------------------------------------------------------------------*/

/**
 * CPT entry card style.
 */
function wpex_cpt_entry_card_style(): ?string {
	$post_type = get_post_type();

	if ( 'related' === wpex_get_loop_instance() ) {
		$card_style = wpex_get_ptu_type_mod( $post_type, 'related_entry_card_style' );
		if ( ! $card_style ) {
			$card_style = get_theme_mod( "{$post_type}_related_entry_card_style" );
		}
	} else {
		$is_archive = is_post_type_archive();
		$loadmore   = \TotalTheme\Pagination\Load_More::get_data();

		if ( $loadmore ) {
			$taxonomy   = $loadmore['query_vars']['taxonomy'] ?? null;
			$is_archive = $taxonomy ? false : true;
		}

		if ( $is_archive ) {
			$card_style = wpex_get_ptu_type_mod( $post_type, 'entry_card_style' );
		} else {
			if ( ! empty( $taxonomy ) || is_tax() ) {
				$taxonomy = $taxonomy ?? get_query_var( 'taxonomy' );
				$term_meta_check = wpex_get_term_meta( '', 'wpex_entry_card_style', true );
				if ( ! empty( $term_meta_check ) ) {
					$card_style = $term_meta_check;
				} else {
					$card_style = wpex_get_ptu_tax_mod( $taxonomy, 'entry_card_style' );
				}
			}
		}
		if ( empty( $card_style ) ) {
			$card_style = get_theme_mod( "{$post_type}_entry_card_style" );
		}
	}

	return (string) apply_filters( "wpex_{$post_type}_entry_card_style", $card_style );
}

/**
 * CPT entry card.
 */
function wpex_cpt_entry_card(): bool {
	$card_style = wpex_cpt_entry_card_style();

	if ( ! $card_style ) {
		return false; // !!! important !!!
	}

	$post_type = get_post_type();
	$instance  = wpex_get_loop_instance();

	$args = [
		'style'          => $card_style,
		'post_id'        => get_the_ID(),
		'thumbnail_size' => wpex_cpt_entry_thumbnail_size(),
		'excerpt_length' => wpex_cpt_entry_excerpt_length(),
	];

	$ptu_more_text = wpex_get_ptu_type_mod( $post_type, 'entry_readmore_text' );

	if ( $ptu_more_text ) {
		$args['more_link_text'] = $ptu_more_text;
	}

	if ( $overlay = wpex_cpt_entry_overlay_style() ) {
		$args['thumbnail_overlay_style'] = $overlay;
	}

	$args = (array) apply_filters( "wpex_{$post_type}_entry_card_args", $args );

	wpex_card( $args );

	return true;
}
