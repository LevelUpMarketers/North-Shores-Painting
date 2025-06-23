<?php

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*/

	# General
	# Archives
	# Entries
	# Single
	# Related
	# Social
	# Cards
	# Deprecated

/*-------------------------------------------------------------------------------*/
/* [ General ]
/*-------------------------------------------------------------------------------*/

/**
 * Check if a staff member has a defined position.
 */
function wpex_has_staff_member_position( $post = '' ): bool {
	return (bool) wpex_get_staff_member_position( $post );
}

/**
 * Returns staff members position.
 */
function wpex_get_staff_member_position( $post = '' ): string {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	$position = get_post_meta( get_the_ID(), 'wpex_staff_position', true );
	return (string) apply_filters( 'wpex_staff_member_position', $position, $post );
}

/*-------------------------------------------------------------------------------*/
/* [ Archives ]
/*-------------------------------------------------------------------------------*/

/**
 * Returns staff archive grid style.
 */
function wpex_staff_archive_grid_style(): string {
	$style = get_theme_mod( 'staff_archive_grid_style' ) ?: 'fit-rows';
	return (string) apply_filters( 'wpex_staff_archive_grid_style', $style );
}

/**
 * Returns the staff loop top class.
 */
function wpex_staff_loop_top_class(): void {
	$class = (array) apply_filters( 'wpex_staff_loop_top_class', wpex_get_staff_wrap_class() );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Returns correct classes for the staff wrap.
 */
function wpex_get_staff_wrap_class(): string {
	$classes = [
		'wpex-row',
	];

	$grid_style = wpex_staff_archive_grid_style();

	// Masonry class.
	if ( 'masonry' === $grid_style || 'no-margins' === $grid_style ) {
		$classes[] = 'wpex-masonry-grid';
		wpex_enqueue_isotope_scripts(); // dynamically load masonry scripts.
	}

	// Add grid style.
	$classes[] = 'staff-' . sanitize_html_class( $grid_style );

	// Add gap class.
	if ( 'no-margins' === $grid_style ) {
		$classes[] = wpex_gap_class( '0px' );
	} elseif ( $gap = get_theme_mod( 'staff_archive_grid_gap' ) ) {
		$classes[] = wpex_gap_class( $gap );
	}

	$classes[] = 'wpex-clr';
	$classes = array_map( 'esc_attr', $classes );
	$classes = (array) apply_filters( 'wpex_staff_wrap_class', $classes );

	return implode( ' ', $classes );

}

/**
 * Returns staff archive columns.
 */
function wpex_staff_archive_columns() {
	$columns = get_theme_mod( 'staff_entry_columns' ) ?: '3';
	return apply_filters( 'wpex_staff_archive_columns', $columns );
}

/**
 * Returns correct classes for the staff grid.
 */
if ( ! function_exists( 'wpex_staff_column_class' ) ) {
	function wpex_staff_column_class( $query ) {
		if ( 'related' === $query ) {
			return wpex_row_column_width_class( get_theme_mod( 'staff_related_columns', '3' ) );
		} else {
			return wpex_row_column_width_class( get_theme_mod( 'staff_entry_columns', '3' ) );
		}
	}
}

/**
 * Checks if match heights are enabled for the staff.
 */
if ( ! function_exists( 'wpex_staff_match_height' ) ) {
	function wpex_staff_match_height(): bool {
		return ( get_theme_mod( 'staff_archive_grid_equal_heights' ) && 'fit-rows' === wpex_staff_archive_grid_style() );
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Entries ]
/*-------------------------------------------------------------------------------*/

/**
 * Get staff entry supported media types.
 */
function wpex_staff_entry_supported_media(): array {
	$supported_media = [
		'thumbnail',
	];
	return (array) apply_filters( 'wpex_staff_entry_supported_media', $supported_media );
}

/**
 * Get Staff entry media type.
 */
function wpex_staff_entry_media_type(): string {
	$supported_media = wpex_staff_entry_supported_media();
	if ( in_array( 'thumbnail', $supported_media ) && has_post_thumbnail() ) {
		$type = 'thumbnail';
	} else {
		$type = '';
	}
	return (string) apply_filters( 'wpex_staff_entry_media_type', $type );
}

/**
 * Staff entry class.
 */
function wpex_staff_entry_class( $loop = '' ): void {
	if ( ! $loop ) {
		$loop = wpex_get_loop_instance();
	}

	// Default entry classes.
	$class = [
		'staff-entry',
	];

	// Add grid column class.
	if ( $col_class = wpex_staff_column_class( $loop ) ) {
		$class[] = 'col';
		$class[] = $col_class;
	}

	// Add loop counter class.
	if ( $loop_counter = wpex_get_loop_counter() ) {
		$class[] = 'col-' . absint( $loop_counter );
	}

	// Masonry Classes.
	if ( 'archive' === $loop ) {

		$grid_style = wpex_staff_archive_grid_style();

		if ( 'masonry' === $grid_style || 'no-margins' === $grid_style ) {
			$class[] = 'wpex-masonry-col';
		}

	}

	$class = (array) apply_filters( 'wpex_staff_entry_class', $class );

	post_class( $class );
}


/**
 * Staff entry inner class.
 */
function wpex_staff_entry_inner_class( $loop = '' ): void {
	if ( ! $loop ) {
		$loop = wpex_get_loop_instance();
	}

	$class = [
		'staff-entry-inner',
		'wpex-last-mb-0',
	];

	if ( wpex_staff_match_height() ) {
		$class[] = 'wpex-flex';
		$class[] = 'wpex-flex-col';
		$class[] = 'wpex-flex-grow';
	} else {
		$class[] = 'wpex-clr';
	}

	$class = (array) apply_filters( 'wpex_staff_entry_inner_class', $class, $loop );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Staff entry media class.
 */
function wpex_staff_entry_media_class(): void {
	$media_type = wpex_staff_entry_media_type();

	$class = [
		'staff-entry-media',
		'entry-media',
		'wpex-relative',
		'wpex-mb-20',
	];

	if ( 'thumbnail' === $media_type ) {
		$overlay = totaltheme_call_static(
			'Overlays',
			'get_entry_image_overlay_style',
			'staff'
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
		if ( $animation = wpex_get_entry_image_animation_classes() ) {
			$class[] = $animation;
		}
	}

	$class = (array) apply_filters( 'wpex_staff_entry_media_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Staff entry title class.
 */
function wpex_staff_entry_title_class(): void {
	$class = [
		'staff-entry-title',
		'entry-title',
		'wpex-mb-5',
	];
	$class = (array) apply_filters( 'wpex_staff_entry_title_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}


/**
 * Staff entry content class.
 */
function wpex_staff_entry_content_class(): void {
	$class = [
		'staff-entry-details',
		'wpex-last-mb-0'
	];
	if ( wpex_staff_match_height() ) {
		$class[] = 'wpex-flex-grow';
	} else {
		$class[] = 'wpex-clr';
	}
	$class = (array) apply_filters( 'wpex_staff_entry_content_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Staff entry excerpt class.
 */
function wpex_staff_entry_excerpt_class(): void {
	$class = [
		'staff-entry-excerpt',
		'wpex-my-15',
		'wpex-text-pretty',
		'wpex-last-mb-0',
	];
	$class = (array) apply_filters( 'wpex_staff_entry_excerpt_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Staff entry position class.
 */
function wpex_staff_entry_position_class(): void {
	$class = [
		'staff-entry-position',
		'entry-position',
		'wpex-mb-15',
		'wpex-text-3',
	];
	if ( totaltheme_has_classic_styles() ) {
		$class[] = 'wpex-text-sm';
	}
	$class = (array) apply_filters( 'wpex_staff_entry_position_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Echo staff entry thumbnail.
 */
function wpex_staff_entry_thumbnail(): void {
	echo wpex_get_staff_entry_thumbnail();
}

/**
 * Returns thumbnail HTML for the staff entries.
 */
function wpex_get_staff_entry_thumbnail( $loop = '', $args = [] ) {
	if ( ! $loop ) {
		$loop = wpex_get_loop_instance();
	}

	$defaults = [
		'size'  => ( 'archive' === $loop ) ? 'staff_entry' : 'staff_related',
		'class' => 'staff-entry-media-img staff-entry-img wpex-align-middle',
	];

	$args = wp_parse_args( $args, $defaults );
	$args = (array) apply_filters( 'wpex_get_staff_entry_thumbnail_args', $args );
	return wpex_get_post_thumbnail( $args );
}

/**
 * Check if staff entry content is enabled.
 */
function wpex_has_staff_entry_content(): bool {
	if ( 'related' === wpex_get_loop_instance() ) {
		$check = get_theme_mod( 'staff_related_excerpts', true );
	} else {
		$check = get_theme_mod( 'staff_entry_details', true );
	}
	return (bool) apply_filters( 'wpex_has_staff_entry_content', $check );

}

/**
 * Returns staff entry excerpt length.
 */
function wpex_staff_entry_excerpt_length(): int {
	$length = get_theme_mod( 'staff_entry_excerpt_length', 20 );
	if ( 'related' === wpex_get_loop_instance() ) {
		$length = get_theme_mod( 'staff_related_entry_excerpt_length', $length );
	}
	return (int) apply_filters( 'wpex_staff_entry_excerpt_length', $length );
}

/*-------------------------------------------------------------------------------*/
/* [ Single ]
/*-------------------------------------------------------------------------------*/

/**
 * Get staff single supported media types.
 */
function wpex_staff_single_supported_media(): array {
	$supported_media = [
		'gallery',
		'thumbnail',
	];
	return (array) apply_filters( 'wpex_staff_single_supported_media', $supported_media );
}

/**
 * Get staff single format.
 */
function wpex_staff_single_media_type(): string {
	$supported_media = wpex_staff_single_supported_media();
	if ( in_array( 'gallery', $supported_media, true ) && wpex_has_post_gallery() ) {
		$type = 'gallery';
	} elseif ( in_array( 'thumbnail', $supported_media, true ) && has_post_thumbnail() ) {
		$type = 'thumbnail';
	} else {
		$type = ''; //important
	}
	return (string) apply_filters( 'wpex_staff_single_media_type', $type );
}

/**
 * Returns thumbnail HTML for the staff posts.
 */
function wpex_get_staff_post_thumbnail( $args = '' ) {
	$defaults = [
		'size'  => 'staff_post',
		'class' => 'staff-single-media-img wpex-align-middle',
	];
	$args = wp_parse_args( $args, $defaults );
	$args = (array) apply_filters( 'wpex_get_staff_post_thumbnail_args', $args ); // deprecated 5.5.3
	$args = (array) apply_filters( 'wpex_staff_post_thumbnail_args', $args );
	return wpex_get_post_thumbnail( $args );
}

/**
 * Staff single media class.
 */
function wpex_staff_single_media_class(): void {
	$class = [
		'single-media',
		'wpex-mb-20',
	];
	$class = (array) apply_filters( 'wpex_staff_single_media_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Staff single header class.
 */
function wpex_staff_single_header_class(): void {
	$class = [
		'single-header',
		'wpex-mb-20',
	];
	$class = (array) apply_filters( 'wpex_staff_single_header_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Staff single title class.
 */
function wpex_has_staff_single_title_position(): bool {
	$check = get_theme_mod( 'staff_single_header_position', true ) && wpex_has_staff_member_position();
	return (bool) apply_filters( 'wpex_has_staff_single_title_position', $check );
}

/**
 * Staff single title class.
 */
function wpex_staff_single_title_class(): void {
	$class = [
		'single-post-title',
		'entry-title',
		'wpex-text-3xl',
	];
	if ( wpex_has_staff_single_title_position() ) {
		$class[] = 'wpex-m-0';
	}
	$class = (array) apply_filters( 'wpex_staff_single_title_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Staff single position class.
 */
function wpex_staff_single_position_class(): void {
	$class = [
		'single-staff-position',
		'wpex-text-3',
	];
	if ( totaltheme_has_classic_styles() ) {
		$class[] = 'wpex-text-md';
	} else {
		$class[] = 'wpex-text-lg';
	}
	$class = (array) apply_filters( 'wpex_staff_single_position_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Staff single content class.
 */
function wpex_staff_single_content_class(): void {
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
	$class = (array) apply_filters( 'wpex_staff_single_content_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Staff single comments class.
 */
function wpex_staff_single_comments_class(): void {
	$class = [
		'single-comments',
		'wpex-mb-40',
		'wpex-clr',
	];

	$class = (array) apply_filters( 'wpex_staff_single_comments_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Related ]
/*-------------------------------------------------------------------------------*/

/**
 * Return staff single related query.
 */
function wpex_staff_single_related_query() {
	$post_id = get_the_ID();

	// Return if disabled via post meta.
	if ( wpex_validate_boolean( get_post_meta( $post_id, 'wpex_disable_related_items', true ) ) ) {
		return false;
	}

	// Posts count.
	$posts_count = (int) get_theme_mod( 'staff_related_count', 3 );

	// Return if count is empty or 0.
	if ( ! $posts_count ) {
		return false;
	}

	// Related query arguments.
	$args = [
		'post_type'      => 'staff',
		'orderby'        => 'date',
		'order'          => 'desc',
		'posts_per_page' => $posts_count,
		'post__not_in'   => [ $post_id ],
		'no_found_rows'  => true,
	];

	// Add custom orderby param.
	$orderby = get_theme_mod( 'staff_related_orderby' );
	if ( $orderby && $orderby_safe = sanitize_sql_orderby( (string) $orderby ) ) {
		$args['orderby'] = $orderby_safe;
	}

	// Add custom order param.
	$order = get_theme_mod( 'staff_related_order' );
	if ( $order && in_array( strtolower( $order ), [ 'asc', 'desc' ] ) ) {
		$args['order'] = $order;
	}

	// Related by taxonomy.
	if ( apply_filters( 'wpex_related_in_same_cat', true ) ) {

		// Add categories to query.
		$related_taxonomy = get_theme_mod( 'staff_related_taxonomy', 'staff_category' );

		// Generate related by taxonomy args.
		if ( 'null' !== $related_taxonomy && taxonomy_exists( $related_taxonomy ) ) {
			$primary_term = totaltheme_get_post_primary_term( $post_id, $related_taxonomy, false );
			if ( $primary_term ) {
				$terms = [ $primary_term->term_id ];
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

	$args = (array) apply_filters( 'wpex_related_staff_args', $args );

	if ( $args ) {
		return new wp_query( $args );
	}
}

/**
 * Display staff single related heading.
 */
function wpex_staff_single_related_heading(): void {
	wpex_heading( [
		'tag'           => get_theme_mod( 'related_heading_tag' ) ?: 'h3',
		'content'		=> wpex_get_translated_theme_mod( 'staff_related_title' ) ?: esc_html__( 'Related Staff', 'total' ),
		'classes'		=> [
			'related-staff-posts-heading',
		],
		'apply_filters'	=> 'staff_related',
	] );
}

/**
 * Staff single related class.
 */
function wpex_staff_single_related_class(): void {
	$class = [
		'related-staff-posts',
		totaltheme_has_classic_styles() ? 'wpex-mb-20' : 'wpex-mb-40',
	];
	if ( 'full-screen' === wpex_content_area_layout() ) {
		$class[] = 'container';
	}
	$class[] = 'wpex-clr';
	$class = (array) apply_filters( 'wpex_staff_single_related_class', $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', array_unique( $class ) ) ) . '"';
	}
}

/**
 * Staff single related row class.
 *
 * @todo deprecate wpex_related_staff_row_classes filter
 */
function wpex_staff_single_related_row_class(): void {
	$classes = [
		'wpex-row',
		'wpex-clr',
	];

	if ( $gap = get_theme_mod( 'staff_related_gap' ) ) {
		$classes[] = wpex_gap_class( $gap );
	}

	$classes = (array) apply_filters( 'wpex_staff_single_related_row_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( apply_filters( 'wpex_related_staff_row_classes', implode( ' ', $classes ) ) ) . '"';
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Social ]
/*-------------------------------------------------------------------------------*/

/**
 * Outputs the staff social options.
 */
if ( ! function_exists( 'wpex_get_staff_social' ) ) {
	function wpex_get_staff_social( $atts = NULL ) {
		extract( shortcode_atts( [
			'before'             => '',
			'after'              => '',
			'post_id'            => '',
			'font_size'          => '',
			'css_animation'      => '',
			'css'                => '',
			'inline_style'       => '',
			'icon_margin'        => '',
			'spacing'            => 5,
			'animation_delay'    => '',
			'animation_duration' => '',
			'show_icons'         => wp_validate_boolean( get_theme_mod( 'staff_social_show_icons', true ) ),
			'labels_only'        => false,
			'format'             => 'flex',
			'separator'          => '',
			'margin_top'         => 15,
			'style'              => get_theme_mod( 'staff_social_default_style', 'minimal-round' ),
			'link_target'        => get_theme_mod( 'staff_social_link_target', 'blank' ),
		], $atts, 'staff_social' ) );

		// Define output.
		$output = '';

		// New flex format added in 5.4.3.
		if ( $format && 'flex' === $format ) {
			$has_flex_format = true;
		} else {
			$has_flex_format = false;
		}

		// Only show labels (added in 5.4.3)
		if ( $labels_only ) {
			$show_icons = false;
		}

		// Get social profiles array.
		$profiles = wpex_staff_social_array();

		// Check for dynamic ID.
		if ( empty( $post_id ) ) {
			$query_var = get_query_var( 'wpex_current_post_id' );
			if ( $query_var ) {
				$post_id = $query_var;
			}
		}

		// Make sure post_id is defined.
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		// Convert icon margin to style - @note: must use vcex_inline_style().
		if ( $icon_margin && function_exists( 'vcex_inline_style' ) ) {
			$icon_margin = vcex_inline_style( [
				'margin' => $icon_margin,
			], false );
		}

		// Parse style to return correct classname.
		if ( $show_icons ) {
			$style_class = wpex_get_social_button_class( $style );
		} else {
			$style_class = '';
		}

		// Wrap classes.
		$wrap_class = [
			'staff-social',
			'wpex-social-btns',
		];

		if ( $margin_top && '0px' !== $margin_top && '0' !== $margin_top ) {
			$wrap_class[] = 'wpex-mt-' . absint( $margin_top );
		}

		if ( $has_flex_format ) {
			$wrap_class[] = 'wpex-flex wpex-flex-wrap';
			if ( $spacing ) {
				$wrap_class[] = 'wpex-gap-' . sanitize_html_class( $spacing );
			}
		} else {
			$wrap_class[] = 'wpex-last-mr-0';
		}

		if ( $css ) {
			$wrap_class[] = vc_shortcode_custom_css_class( $css );
		}
		if ( $css_animation && 'none' != $css_animation && function_exists( 'vcex_get_css_animation' ) ) {
			$wrap_class[] = vcex_get_css_animation( $css_animation );
		}

		// Font size.
		if ( $font_size && $font_size_size = wpex_sanitize_data( $font_size, 'font_size' ) ) {
			$inline_style .= " font-size:{$font_size_size};";
		}

		// Animation delay.
		if ( $animation_delay ) {
			$inline_style .= ' animation-delay:' . floatval( $animation_delay ) . 's;';
		}

		// Animation duration.
		if ( $animation_duration ) {
			$inline_style .= ' animation-duration:' . floatval( $animation_duration ) . 's;';
		}

		// Wrap attributes.
		$wrap_attrs = [
			'class' => array_map( 'esc_attr', $wrap_class ),
			'style' => trim( $inline_style ),
		];

		// Before output.
		if ( $before ) {
			$output .= $before;
		}

		// Start output.
		$output .= '<div ' . wpex_parse_attrs( $wrap_attrs ) . '>';

			// Loop through social options.
			$count = 0;
			foreach ( $profiles as $profile_id => $profile ) :
				$meta       = $profile['meta'];
				$meta_value = (string) get_post_meta( $post_id, $meta, true );

				if ( isset( $profile['key'] ) ) {
					$profile_id = $profile['key'];
				}

				// Continue if no link is set.
				if ( ! $meta_value ) {
					continue;
				}

				// Add to counter.
				$count ++;

				// Add separator.
				if ( $separator && $count > 1 ) {
					$output .= '<span class="staff-social__separator';
						if ( ! $has_flex_format ) {
							$output .= ' wpex-mr-' . sanitize_html_class( $spacing );
						}
					$output .= '">' . trim( sanitize_text_field( $separator ) ) . '</span>';
				}

				// Define URL.
				$url = $meta_value;

				// Parse URL to add prefixes for email/tel.
				switch ( $meta ) {
					case 'wpex_staff_email':
						if ( ! str_starts_with( $url, 'mailto:' ) && is_email( $url ) ) {
							$url = "mailto:{$url}";
						}
						break;
					case 'wpex_staff_skype':
						if ( ! str_starts_with( $url, 'callto:' ) && ! str_contains( $url, 'skype' ) ) {
							$url = "callto:{$url}";
						}
						break;
					case 'wpex_staff_phone_number':
						if ( ! str_starts_with( $url, 'tel:' ) && ! str_starts_with( $url, 'callto:' ) ) {
							$url = "tel:{$url}";
						}
						break;
				}

				// Link classes.
				$link_classes = [
					'wpex-' . sanitize_html_class( str_replace( '_', '-', $profile_id ) ),
				];

				if ( $style_class ) {
					$link_classes[] = $style_class;
				}

				if ( $has_flex_format ) {
					$link_classes[] = '';
				} else {
					$spacing_safe = sanitize_html_class( $spacing );
					$link_classes[] = "wpex-mr-{$spacing_safe}";
					$link_classes[] = "wpex-mt-{$spacing_safe}";
				}

				if ( ! $show_icons && ! $has_flex_format ) {
					$link_classes[] = 'wpex-inline-block';
				}

				// Link attributes.
				$link_attrs  = [
					'href'   => $url,
					'class'  => $link_classes,
					'target' => ( 'wpex_staff_email' === $meta ) ? '' : $link_target,
					'style'  => $icon_margin,
				];

				$output .= '<a '. wpex_parse_attrs( $link_attrs ) .'>';

					if ( $show_icons ) {
						$icon_name = $profile['icon_class'] ?? $profile['svg'] ?? $profile['icon'] ?? $profile_id;
						$icon_html = totaltheme_get_icon( $icon_name );
						if ( ! $icon_html ) {
							$icon_html = '<span class="' . esc_attr( $icon_name ) . '" aria-hidden="true"></span>';
						}
						$output .= $icon_html;
						$output .= '<span class="screen-reader-text">' . esc_html( $profile['label'] ) . '</span>';
					} else {
						$label = $profile['label'];
						if ( ! $labels_only && in_array( $profile_id, [
								'telephone',
								'fax',
								'phone_number',
								'phone',
								'email',
								'website'
						], true ) ) {
							$label = $meta_value;
						} else {
							if ( $labels_only ) {
								switch ( $profile_id ) {
									case 'telephone':
									case 'phone':
									case 'phone_number':
										$label = esc_html__( 'Phone', 'total' );
										break;
								}
							}
							$label = apply_filters( 'wpex_staff_social_item_label', $label, $profile_id );
						}
						$output .= esc_html( $label );
					}

				$output .= '</a>';

			endforeach; // End profiles loop.

		// End output.
		$output .= '</div>';

		// After output.
		if ( $after ) {
			$output .= $after;
		}

		return $output;
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Cards ]
/*-------------------------------------------------------------------------------*/

/**
 * Staff Card Entry.
 */
function wpex_staff_entry_card(): bool {
	$instance = wpex_get_loop_instance();

	if ( 'related' === $instance ) {
		$card_style = get_theme_mod( 'staff_related_entry_card_style' );
	} else {
		$term_meta_check = wpex_get_term_meta( '', 'wpex_entry_card_style', true );
		if ( ! empty( $term_meta_check ) ) {
			$card_style = $term_meta_check;
		} else {
			$card_style = get_theme_mod( 'staff_entry_card_style' );
		}
	}

	$card_style = apply_filters( 'wpex_staff_entry_card_style', $card_style );

	if ( ! $card_style ) {
		return false;
	}

	$args = [
		'style'          => $card_style,
		'post_id'        => get_the_ID(),
		'thumbnail_size' => ( 'related' === $instance ) ? 'staff_related' : 'staff_entry',
		'excerpt_length' => wpex_staff_entry_excerpt_length(),
	];

	$overlay_style = totaltheme_call_static(
		'Overlays',
		'get_entry_image_overlay_style',
		'staff'
	);

	if ( $overlay_style ) {
		$args['thumbnail_overlay_style'] = $overlay_style;
	}

	if ( $hover_style = get_theme_mod( 'staff_entry_image_hover_animation', null ) ) {
		$args['thumbnail_hover'] = $hover_style;
	}

	$args = (array) apply_filters( 'wpex_staff_entry_card_args', $args );

	wpex_card( $args );

	return true;
}

