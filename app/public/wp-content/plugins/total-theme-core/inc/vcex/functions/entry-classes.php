<?php

defined( 'ABSPATH' ) || exit;

/**
 * Entry Inner.
 */
function vcex_get_entry_inner_class( $class = '', $shortcode_tag = '', $atts = '' ): array {
	$classes = [];

	if ( $class ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_map( 'esc_attr', $class );
	} else {
		$class = [];
	}

	$classes[] = 'entry-inner';
	$classes[] = 'wpex-first-mt-0';
	$classes[] = 'wpex-last-mb-0';

	if ( isset( $atts['columns'] )
		&& 1 === (int) $atts['columns']
		&& ! empty( $atts['single_column_style'] )
		&& 'left_thumbs' === $atts['single_column_style']
	) {
		$classes[] = 'wpex-md-flex';
		$classes[] = 'wpex-md-flex-wrap';
		$classes[] = 'wpex-md-justify-between';
	} else {
		$classes[] = 'wpex-clr';
	}

	if ( ! empty( $atts['entry_shadow'] ) ) {
		$classes[] = vcex_parse_shadow_class( $atts['entry_shadow'] );
	}

	if ( ! empty( $atts['entry_css'] ) ) {
		$classes[] = vcex_vc_shortcode_custom_css_class( $atts['entry_css'] );
	}

	$classes = (array) apply_filters( 'vcex_entry_inner_class', $classes, $class, $shortcode_tag, $atts );

	return array_unique( array_filter( $classes ) );
}

/**
 * Entry Media.
 */
function vcex_get_entry_media_class( $class = '', $shortcode_tag = '', $atts = '' ): array {
	$classes = [];

	if ( $class ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_map( 'esc_attr', $class );
	} else {
		$class = [];
	}

	$classes[] = 'entry-media';
	$classes[] = 'wpex-relative';

	// Add image specific classes.
	if ( ! empty( $atts[ 'media_type'] )
		&& ( 'thumbnail' == $atts['media_type'] || 'image' == $atts['media_type'] )
	) {

		if ( 'vcex_image_grid' == $shortcode_tag && ! empty( $atts['hover_animation'] ) ) {
			$classes[] = vcex_hover_animation_class( $atts['hover_animation'] );
		}

		if ( ! empty( $atts['img_hover_style'] ) ) {
			$classes[] = vcex_image_hover_classes( $atts['img_hover_style'] );
		}

		if ( ! empty( $atts['img_filter'] ) ) {
			$classes[] = vcex_image_filter_class( $atts['img_filter'] );
		}

		if ( ! empty( $atts['overlay_style'] ) && 'none' !== $atts['overlay_style'] ) {
			$classes[] = vcex_image_overlay_classes( $atts['overlay_style'] );
		}

		// Deprecated rounded_image atts.
		if ( empty( $atts['img_border_radius'] ) && isset( $atts['rounded_image'] ) ) {
			if ( 'yes' == $atts['rounded_image'] || 'true' == $atts['rounded_image'] ) {
				$atts['img_border_radius'] = 'round';
			}
		}

		// Apply border radius to media container as well.
		if ( ! empty( $atts['img_border_radius'] ) ) {
			$classes[] = 'wpex-' . sanitize_html_class( $atts['img_border_radius'] );
		}

	}

	// Users grid checks.
	if ( 'vcex_users_grid' == $shortcode_tag && ! empty( $atts['avatar_hover_style'] ) ) {
		$classes[] = vcex_image_hover_classes( $atts['avatar_hover_style'] );
	}

	// Left thumb class.
	if ( isset( $atts['columns'] )
		&& 1 === (int) $atts['columns']
		&& ! empty( $atts['single_column_style'] )
		&& 'left_thumbs' === $atts['single_column_style']
	) {
		$classes[] = 'wpex-mb-20 wpex-md-mb-0';
	} else {
		// Add bottom margin for plain/none content_style if background color or padding isn't defined.
		if ( empty( $atts['content_background_color'] )
			&& ( empty( $atts['content_style'] ) || 'none' === $atts['content_style'] )
			&& ( empty( $atts['content_padding_all'] ) || ! vcex_parse_padding_class( $atts['content_padding_all'] ) )
		) {
			$classes[] = 'wpex-mb-20';
		}
	}

	$classes = (array) apply_filters( 'vcex_entry_media_class', $classes, $class, $shortcode_tag, $atts );

	return array_unique( $classes );
}

/**
 * Entry Thumbnail Class.
 */
function vcex_get_entry_thumbnail_class( $class = '', $shortcode_tag = '', $atts = '' ): array {
	$classes = [];

	if ( $class ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_map( 'esc_attr', $class );
	} else {
		$class = [];
	}

	// Deprecated rounded_image atts.
	if ( empty( $atts['img_border_radius'] ) && isset( $atts['rounded_image'] ) ) {
		if ( 'yes' == $atts['rounded_image'] || 'true' == $atts['rounded_image'] ) {
			$atts['img_border_radius'] = 'round';
		}
	}

	$classes[] = 'wpex-align-middle';

	if ( ! empty( $atts['img_border_radius'] ) ) {
		$classes[] = 'wpex-' . sanitize_html_class( $atts['img_border_radius'] );
	}

	if ( ! empty( $atts['img_shadow'] ) ) {
		$classes[] = vcex_parse_shadow_class( $atts['img_shadow'] );
	}

	if ( ! empty( $atts['img_shadow_hover'] ) ) {
		$classes[] = 'wpex-transition-shadow wpex-duration-300';
		$classes[] = vcex_parse_shadow_class( $atts['img_shadow_hover'], 'hover' );
	}

	$classes = (array) apply_filters( 'vcex_entry_thumbnail_class', $classes, $class, $shortcode_tag, $atts );

	return array_unique( $classes );
}

/**
 * Entry Details.
 */
function vcex_get_entry_details_class( $class = '', $shortcode_tag = '', $atts = '' ): array {
	$classes = [];

	if ( $class ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_map( 'esc_attr', $class );
	} else {
		$class = [];
	}

	$classes[] = 'entry-details';

	$content_style_default = 'bordered';

	if ( isset( $atts['columns'] )
		&& 1 === (int) $atts['columns']
		&& isset( $atts['single_column_style'] )
		&& 'left_thumbs' == $atts['single_column_style']
	) {
		$content_style_default = 'none';
	} else {
		if ( ! empty( $atts['content_style'] ) && 'none' !== $atts['content_style'] ) {
			$classes[] = 'wpex-' . sanitize_html_class( $atts['content_style'] );
		}
	}

	if ( ! empty( $atts['content_padding_all'] ) ) {
		$classes[] = vcex_parse_padding_class( $atts['content_padding_all'] );
	}

	if ( ! empty( $atts['content_border_style'] ) ) {
		$classes[] = vcex_parse_border_style_class( $atts['content_border_style'] );
	}

	if ( ! empty( $atts['content_border_width'] ) ) {
		$classes[] = vcex_parse_border_width_class( $atts['content_border_width'] );
	}

	$classes[] = 'wpex-first-mt-0';
	$classes[] = 'wpex-last-mb-0';
	$classes[] = 'wpex-clr';

	if ( ! empty( $atts['content_css'] ) ) {
		$classes[] = vcex_vc_shortcode_custom_css_class( $atts['content_css'] );
	}

	$classes = (array) apply_filters( 'vcex_entry_details_class', $classes, $class, $shortcode_tag, $atts );

	return array_unique( array_filter( $classes ) );
}

/**
 * Entry Title.
 */
function vcex_get_entry_title_class( $class = '', $shortcode_tag = '', $atts = '' ): array {
	$classes = [];

	if ( $class ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_map( 'esc_attr', $class );
	} else {
		$class = [];
	}

	$classes[] = 'entry-title';

	if ( ! empty( $atts['single_column_style'] ) && 'left_thumbs' === $atts['single_column_style'] ) {
		$classes[] = 'wpex-text-2xl';
	}

	$classes[] = 'wpex-mb-5';

	if ( ! empty( $atts['content_heading_color'] ) ) {
		$classes[] = 'wpex-child-inherit-color';
	}

	$classes = (array) apply_filters( 'vcex_entry_title_class', $classes, $class, $shortcode_tag, $atts );

	return array_unique( $classes );
}

/**
 * Entry Date.
 */
function vcex_get_entry_date_class( $class = '', $shortcode_tag = '', $atts = '' ): array {
	$classes = [];
	if ( $class ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_map( 'esc_attr', $class );
	} else {
		$class = [];
	}
	$classes[] = 'entry-date';
	$classes[] = 'wpex-text-sm';
	$classes[] = 'wpex-text-3';
	$classes[] = 'wpex-mb-5';
	$classes = (array) apply_filters( 'vcex_entry_date_class', $classes, $class, $shortcode_tag, $atts );
	return array_unique( $classes );
}

/**
 * Entry Categories.
 */
function vcex_get_entry_categories_class( $class = '', $shortcode_tag = '', $atts = '' ): array {
	$classes = [];
	if ( $class ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_map( 'esc_attr', $class );
	} else {
		$class = [];
	}
	$classes[] = 'entry-categories';
	$classes[] = 'wpex-text-sm';
	$classes[] = 'wpex-leading-tight';
	$classes[] = 'wpex-text-3';
	$classes[] = 'wpex-child-inherit-color';
	$classes[] = 'wpex-mb-5';
	$classes = (array) apply_filters( 'vcex_entry_categories_class', $classes, $class, $shortcode_tag, $atts );
	return array_unique( $classes );
}

/**
 * Entry Staff Position.
 */
function vcex_get_entry_staff_position_class( $class = '', $shortcode_tag = '', $atts = '' ): array {
	$classes = [];
	if ( $class ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_map( 'esc_attr', $class );
	} else {
		$class = [];
	}
	$classes = [
		'staff-entry-position',
		'entry-position',
		'wpex-text-sm',
		'wpex-text-3',
		'wpex-leading-snug',
		'wpex-mb-5', // can't use 15 because there could be comments below it.
	];
	$classes = (array) apply_filters( 'vcex_entry_staff_position_class', $classes, $class, $shortcode_tag, $atts );
	return array_unique( $classes );
}

/**
 * Entry Excerpt.
 */
function vcex_get_entry_excerpt_class( $class = '', $shortcode_tag = '', $atts = '' ): array {
	$classes = [];
	if ( $class ) {
		if ( ! is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
		$classes = array_map( 'esc_attr', $class );
	} else {
		$class = [];
	}
	$classes[] = 'entry-excerpt';
	$classes[] = 'wpex-my-15';
	$classes[] = 'wpex-text-pretty';
	$classes[] = 'wpex-last-mb-0';
	$classes[] = 'wpex-clr';
	$classes = (array) apply_filters( 'vcex_entry_excerpt_class', $classes, $class, $shortcode_tag, $atts );
	return array_unique( $classes );
}

/**
 * Entry Readmore Wrap.
 */
function vcex_get_entry_button_wrap_class( $class = '', $shortcode_tag = '', $atts = '' ): array {
	$classes = [];
	if ( $class ) {
        if ( ! is_array( $class ) ) {
            $class = preg_split( '#\s+#', $class );
        }
        $classes = array_map( 'esc_attr', $class );
    } else {
    	$class = [];
    }
	$classes[] = 'entry-readmore-wrap';
	$classes[] = 'wpex-my-15';
	$classes[] = 'wpex-clr';
	$classes = (array) apply_filters( 'vcex_entry_button_wrap_class', $classes, $class, $shortcode_tag, $atts );
	return array_unique( $classes );
}
