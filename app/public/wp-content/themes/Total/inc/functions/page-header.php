<?php

defined( 'ABSPATH' ) || exit;

/*-------------------------------------------------------------------------------*/
/* [ Table of contents ]
/*-------------------------------------------------------------------------------*

	# General
	# Content
	# Aside
	# Title
	# Subheading
	# Background
	# Inline CSS

/*-------------------------------------------------------------------------------*/
/* [ General ]
/*-------------------------------------------------------------------------------*/

/**
 * Outputs page header class tag.
 */
function wpex_page_header_class() {
	echo 'class="' . esc_attr( wpex_page_header_classes() ) . '"';
}

/**
 * Adds correct classes to the page header.
 */
function wpex_page_header_classes() {
	$post_id           = wpex_get_current_post_id();
	$page_header_style = TotalTheme\Page\Header::style();
	$is_global_style   = TotalTheme\Page\Header::is_global_style();

	// Define main class.
	$classes = [
		'page-header',
	];

	// Check if has aside.
	if ( has_action( 'wpex_hook_page_header_aside' ) ) {
		$classes[] = 'has-aside';
	}

	// Add classes for title style.
	if ( $page_header_style ) {
		$classes[] = sanitize_html_class( $page_header_style ) . '-page-header';
	}

	// Add background image styles.
	if ( ( $is_global_style || 'background-image' === $page_header_style ) && wpex_page_header_background_image() ) {
		$classes[] = 'has-bg-image';
		if ( $bg_style = wpex_page_header_background_image_style() ) {
			$classes[] = 'bg-' . sanitize_html_class( $bg_style ); // deprecated class.
			$classes[] = wpex_parse_background_style_class( $bg_style );
		}
	}

	// Get custom text align.
	$text_align = get_theme_mod( 'page_header_text_align' );

	// Utility classes.
	$classes[] = 'wpex-relative';
	$classes[] = 'wpex-mb-40';

	switch ( $page_header_style ) {

		case 'background-image':
			$classes[] = 'wpex-flex';
			$classes[] = 'wpex-items-' . sanitize_html_class( get_theme_mod( 'page_header_align_items' ) ?: 'center' );
			$classes[] = 'wpex-flex-wrap';
			$classes[] = 'wpex-bg-gray-900';
			$classes[] = 'wpex-text-white';
			if ( ! $text_align ) {
				$classes[] = 'wpex-text-center';
			}
			break;

		case 'solid-color':
			$classes[] = 'wpex-bg-accent';
			$classes[] = 'wpex-py-20';
			break;

		case 'centered':
			$classes[] = 'wpex-surface-2';
			$classes[] = 'wpex-py-30';
			$classes[] = 'wpex-border-t';
			$classes[] = 'wpex-border-b';
			$classes[] = 'wpex-border-solid';
			$classes[] = 'wpex-border-surface-3';
			$classes[] = 'wpex-text-2';
			if ( ! $text_align ) {
				$classes[] = 'wpex-text-center';
			}
			break;

		case 'centered-minimal':
			$classes[] = 'wpex-surface-1';
			$classes[] = 'wpex-py-30';
			$classes[] = 'wpex-border-t';
			$classes[] = 'wpex-border-b';
			$classes[] = 'wpex-border-solid';
			$classes[] = 'wpex-border-main';
			$classes[] = 'wpex-text-2';
			if ( ! $text_align ) {
				$classes[] = 'wpex-text-center';
			}
			break;

		default:
			$classes[] = 'wpex-surface-2';
			$classes[] = 'wpex-py-20';
			$classes[] = 'wpex-border-t';
			$classes[] = 'wpex-border-b';
			$classes[] = 'wpex-border-solid';
			$classes[] = 'wpex-border-surface-3';
			$classes[] = 'wpex-text-2';
			break;

	}

	// Add text align.
	if ( $text_align ) {
		$classes[] = 'wpex-text-' . sanitize_html_class( $text_align );
	}

	// Container class.
	if ( ! get_theme_mod( 'page_header_full_width', true ) ) {
		$classes[] = 'container';
	}

	// Allow customizations to this header style if it's the globally defined style.
	if ( $is_global_style || ! in_array( $page_header_style, array( 'background-image', 'solid-color' ) ) ) {
		$classes[] = 'wpex-supports-mods';
	}

	// Remove duplicate and empty classes.
	$classes = array_filter( $classes );
	$classes = array_unique( $classes );

	/*** deprecated ***/
	$classes = apply_filters( 'wpex_page_header_classes', $classes );
	$classes = apply_filters( 'wpex_page_header_class', $classes );

	// Parse to array.
	$classes = (array) $classes;

	return implode( ' ', $classes );
}

/**
 * Page header inner class.
 */
function wpex_page_header_inner_class() {
	$full_width = get_theme_mod( 'page_header_full_width', true );

	$class = [
		'page-header-inner',
	];

	if ( $full_width ) {
		$class[] = 'container';
	} else {
		$class[] = 'wpex-mx-auto';
	}

	$page_header_style = TotalTheme\Page\Header::style();

	switch ( $page_header_style ) {

		case 'background-image':
			if ( $full_width ) {
				$class[] = 'wpex-py-20';
			} else {
				$class[] = 'wpex-p-20';
			}
			$class[] = 'wpex-z-5';
			$class[] = 'wpex-relative';
		break;

		case 'solid-color':
			$classes[] = 'wpex-text-white';
		break;

	}

	// Flex styles.
	if ( has_action( 'wpex_hook_page_header_content' ) ) {
		if ( ( 'default' === $page_header_style || 'solid-color' === $page_header_style )
			&& has_action( 'wpex_hook_page_header_aside' )
		) {
			$bk = TotalTheme\Page\Header::breakpoint();
			if ( $bk ) {
				$class[] = "wpex-{$bk}-flex";
				$class[] = "wpex-{$bk}-flex-wrap";
				$class[] = "wpex-{$bk}-items-center";
				$class[] = "wpex-{$bk}-justify-between";
			}
		}
	}

	$class = (array) apply_filters( 'wpex_page_header_inner_class', $class );

	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Content ]
/*-------------------------------------------------------------------------------*/

/**
 * Page header content class.
 */
function wpex_page_header_content_class() {
	$classes = [
		'page-header-content',
	];

	if ( has_action( 'wpex_hook_page_header_aside' ) ) {
		$page_header_style = TotalTheme\Page\Header::style();
		if ( 'default' === $page_header_style || 'solid-color' === $page_header_style ) {
			$bk = TotalTheme\Page\Header::breakpoint();
			if ( $bk ) {
				$classes[] = "wpex-{$bk}-mr-15";
			}
		}
	}

	$classes = (array) apply_filters( 'wpex_page_header_content_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Aside ]
/*-------------------------------------------------------------------------------*/

/**
 * Page header aside class.
 */
function wpex_page_header_aside_class() {
	$classes = [
		'page-header-aside',
	];

	if ( has_action( 'wpex_hook_page_header_content' ) ) {
		$page_header_style = TotalTheme\Page\Header::style();
		if ( 'default' === $page_header_style || 'solid-color' === $page_header_style ) {
			$bk = TotalTheme\Page\Header::breakpoint();
			if ( $bk ) {
				$classes[] = "wpex-{$bk}-text-right";
			}
		} else {
			$classes[] = 'wpex-mt-5';
		}
	}
	$classes = (array) apply_filters( 'wpex_page_header_aside_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Title ]
/*-------------------------------------------------------------------------------*/

/**
 * Echo page header tag.
 */
function wpex_page_header_title_tag( $title_args = null ) {
	if ( ! $title_args ) {
		$title_args = wpex_page_header_title_args();
	}
	echo ! empty( $title_args['html_tag'] ) ? tag_escape( $title_args['html_tag'] ) : 'div';
}

/**
 * Echo page header title class.
 */
function wpex_page_header_title_class() {
	$page_header_style = TotalTheme\Page\Header::style();
	$class = [
		'page-header-title',
		'wpex-block',
		'wpex-m-0',
	];
	if ( totaltheme_has_classic_styles() ) {
		switch ( $page_header_style ) {
			case 'background-image':
				$class[] = 'wpex-text-7xl';
				break;
			case 'centered':
			case 'centered-minimal':
				$class[] = 'wpex-text-5xl';
				break;
			case 'solid-color':
			default:
				$class[] = 'wpex-text-2xl';
				break;
		}
	} else {
		if ( 'background-image' === $page_header_style ) {
			$class[] = 'wpex-text-5xl';
		} else {
			$class[] = 'wpex-text-2xl';
		}
	}
	if ( 'background-image' === $page_header_style ) {
		$class[] = 'wpex-text-white';
	}
	$class = (array) apply_filters( 'wpex_page_header_title_class', $class );
	$class = array_unique( $class );
	if ( $class ) {
		echo 'class="' . esc_attr( implode( ' ', $class ) ) . '"';
	}
}

/**
 * Return header title args.
 */
function wpex_page_header_title_args() {
	$args           = [];
	$title_instance = totaltheme_get_instance_of( 'Title' );

	if ( $title_instance ) {
		$title = $title_instance->get();
		if ( false === $title_instance->is_h1() ) {
			$args['html_tag'] = 'span';
		}
	} else {
		$title = '';
	}

	if ( is_singular() && $ptu_tag = wpex_get_ptu_type_mod( get_post_type(), 'page_header_title_tag' ) ) {
		$args['html_tag'] = $ptu_tag;
	}

	$args = (array) apply_filters( 'wpex_page_header_title_args', $args, null );

	// Meta check - perform after filter to ensure meta takes priority over the filter above.
	if ( wpex_has_post_meta( TotalTheme\Title::META_KEY ) ) {
		$args['string'] = $title;
	}

	$default_tag = get_theme_mod( 'page_header_html_tag' ) ?: 'h1';

	if ( ! in_array( $default_tag, [ 'h1', 'span' ], true ) ) {
		$default_tag = 'h1';
	}

	return wp_parse_args( $args, [
		'html_tag' => $default_tag,
		'string'   => $title,
	] );
}

/*-------------------------------------------------------------------------------*/
/* [ Subheading ]
/*-------------------------------------------------------------------------------*/

/**
 * Return header subheading class.
 */
function wpex_page_header_subheading_class() {
	$page_header_style = TotalTheme\Page\Header::style();
	$classic_styles = totaltheme_has_classic_styles();

	$classes = [
		'page-subheading',
		'wpex-last-mb-0',
	];

	switch ( $page_header_style ) {
		case 'centered':
		case 'centered-minimal':
			$classes[] = $classic_styles ? 'wpex-text-xl' : 'wpex-text-lg';
			if ( $classic_styles ) {
				$classes[] = 'wpex-font-light';
			}
			break;
		case 'background-image':
			$classes[] = $classic_styles ? 'wpex-text-3xl' : 'wpex-text-xl';
			$classes[] = 'wpex-text-white';
			$classes[] = 'wpex-font-light';
			break;
		default:
			$classes[] = $classic_styles ? 'wpex-text-md' : 'wpex-text-lg';
			break;
	}

	$classes = (array) apply_filters( 'wpex_page_header_subheading_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/*-------------------------------------------------------------------------------*/
/* [ Background ]
/*-------------------------------------------------------------------------------*/

/**
 * Get page header background image URL.
 */
function wpex_page_header_background_image() {
	$post_id = wpex_get_current_post_id();
	$image   = get_theme_mod( 'page_header_background_img' );

	// Fetch from featured image.
	if ( $post_id && ( $image || 'background-image' === TotalTheme\Page\Header::style() ) ) {

		if ( get_theme_mod( 'page_header_background_use_secondary_thumbnail' )
			&& $secondary_thumbnail = totaltheme_get_post_secondary_thumbnail_id( $post_id, false )
		) {
			$image = $secondary_thumbnail;
		} else if ( $fetch_thumbnail_types = get_theme_mod( 'page_header_background_fetch_thumbnail' ) ) {
			if ( ! is_array( $fetch_thumbnail_types ) ) {
				$fetch_thumbnail_types = explode( ',', $fetch_thumbnail_types );
			}
			if ( in_array( get_post_type( $post_id ), $fetch_thumbnail_types, true )
				&& $thumbnail = get_post_thumbnail_id( $post_id )
			) {
				$image = $thumbnail;
			}
		}
	}

	// Apply filters before meta checks => meta should always override.
	$image = apply_filters( 'wpex_page_header_background_img', $image );
	$image = apply_filters( 'wpex_page_header_background_image', $image, $post_id );

	// Check meta for bg image.
	if ( $post_id ) {

		$meta_image = '';

		// Get page header background from meta.
		if ( $post_id && 'background-image' === get_post_meta( $post_id, 'wpex_post_title_style', true ) ) {

			// Redux fallback.
			if ( $new_meta = get_post_meta( $post_id, 'wpex_post_title_background_redux', true ) ) {
				if ( is_array( $new_meta ) && ! empty( $new_meta['url'] ) ) {
					$meta_image = $new_meta['url'];
				} else {
					$meta_image = $new_meta;
				}
			}

			// Newer image title.
			else {
				$meta_image = get_post_meta( $post_id, 'wpex_post_title_background', true );
			}

		}

		if ( $meta_image ) {
			if ( is_numeric( $meta_image ) ) {
				if ( 'attachment' === get_post_type( $meta_image ) ) {
					$image = $meta_image;
				}
			} else {
				$image = $meta_image;
			}
		}

	}

	if ( $image ) {
		return wpex_get_image_url( $image );
	}
}

/**
 * Get correct page header background image style.
 */
function wpex_page_header_background_image_style() {
	$page_header_style = TotalTheme\Page\Header::style();

	$bg_style = ( 'background-image' === $page_header_style ) ? 'cover' : 'fixed';

	if ( $mod = get_theme_mod( 'page_header_background_img_style' ) ) {
		$bg_style = $mod;
	}

	if ( $meta_val = get_post_meta( wpex_get_current_post_id(), 'wpex_post_title_background_image_style', true ) ) {
		$bg_style = $meta_val;
	}

	$bg_style = apply_filters( 'wpex_page_header_background_img_style', $bg_style ); //deprecated
	return (string) apply_filters( 'wpex_page_header_background_image_style', $bg_style );
}

/**
 * Get correct page header overlay style.
 */
function wpex_get_page_header_overlay_style() {
	$overlay_style     = 'dark';
	$page_header_style = TotalTheme\Page\Header::style();

	if ( 'background-image' === $page_header_style ) {
		$post_id = wpex_get_current_post_id();
		if ( $post_id && 'background-image' === get_post_meta( $post_id, 'wpex_post_title_style', true ) ) {
			$overlay_style = get_post_meta( $post_id, 'wpex_post_title_background_overlay', true );
		}
	}

	if ( 'none' === $overlay_style ) {
		$overlay_style = '';
	}

	return (string) apply_filters( 'wpex_page_header_overlay_style', $overlay_style );
}

/**
 * Get correct page header overlay patttern.
 */
function wpex_get_page_header_overlay_pattern() {
	$pattern = '';
	$post_id = wpex_get_current_post_id();
	if ( $post_id && $meta = get_post_meta( $post_id, 'wpex_post_title_background_overlay', true ) ) {
		if ( 'dotted' === $meta ) {
			$pattern = wpex_asset_url( 'images/overlays/dotted.png' );
		} elseif ( 'dashed' === $meta ) {
			$pattern = wpex_asset_url( 'images/overlays/dashed.png' );
		}
	}
	return (string) apply_filters( 'wpex_get_page_header_overlay_pattern', $pattern );
}

/**
 * Get correct page header overlay opacity.
 */
function wpex_get_page_header_overlay_opacity() {
	$post_id = wpex_get_current_post_id();
	$opacity = '';
	if ( $post_id && 'background-image' === get_post_meta( $post_id, 'wpex_post_title_style', true ) ) {
		$meta = get_post_meta( $post_id, 'wpex_post_title_background_overlay_opacity', true );
		if ( $meta ) {
			$opacity = $meta;
		}
	}
	return apply_filters( 'wpex_page_header_overlay_opacity', $opacity );
}

/**
 * Outputs html for the page header overlay.
 */
function wpex_page_header_overlay( ) {
	if ( 'background-image' !== TotalTheme\Page\Header::style() ) {
		return;
	}

	$html          = '';
	$overlay_style = wpex_get_page_header_overlay_style();

	if ( $overlay_style ) {

		$classes = [
			'background-image-page-header-overlay',
			'style-' . sanitize_html_class( $overlay_style ),
			'wpex-z-0',
			'wpex-bg-black',
			'wpex-absolute',
			'wpex-inset-0',
		];

		$overlay_opacity = get_theme_mod( 'page_header_overlay_opacity' ) ?: '50';

		if ( $overlay_opacity ) {
			$classes[] = 'wpex-opacity-' . sanitize_html_class( $overlay_opacity );
		}

		$classes = apply_filters( 'wpex_page_header_overlay_class', $classes );

		$html = '<div class="' . esc_attr( implode( ' ', $classes ) ) . '"></div>';

	}

	echo apply_filters( 'wpex_page_header_overlay', $html );
}

/*-------------------------------------------------------------------------------*/
/* [ Inline CSS ]
/*-------------------------------------------------------------------------------*/

/**
 * Outputs Custom CSS for the page title when selecting a custom title style via the meta option.
 */
function wpex_page_header_css( $css_output ) {
	if ( ! wpex_has_post_meta( 'wpex_post_title_style' ) || ! TotalTheme\Page\Header::is_enabled() ) {
		return $css_output;
	}

	$page_header_style = TotalTheme\Page\Header::style();

	if ( 'default' === $page_header_style ) {
		return $css_output;
	}

	$post_id         = wpex_get_current_post_id();
	$add_css         = '';
	$page_header_css = '';

	// Customize background color.
	if ( 'solid-color' === $page_header_style || 'background-image' === $page_header_style ) {
		$bg_color = get_post_meta( $post_id, 'wpex_post_title_background_color', true );
		if ( $bg_color && '#' !== $bg_color && $bg_color_safe = wpex_parse_color( $bg_color ) ) {
			$page_header_css .= "background-color:{$bg_color_safe}!important;";
		}
	}

	// Background image Style (non global).
	if ( 'background-image' === $page_header_style ) {

		// Background image.
		$bg_img = wpex_page_header_background_image();
		if ( $bg_img && $bg_img_safe = esc_url( $bg_img ) ) {
			$page_header_css .= "background-image:url('{$bg_img_safe}')!important;";
		}

		// Background position.
		$bg_position = ( $bg_position = get_post_meta( $post_id, 'wpex_post_title_background_position', true ) ) ? sanitize_text_field( $bg_position ) : '';
		$bg_position = (string) apply_filters( 'wpex_page_header_background_position', $bg_position );

		if ( $bg_position && $bg_position_safe = sanitize_text_field( $bg_position ) ) {
			$page_header_css .= "background-position:{$bg_position_safe};";
		} else {
			$page_header_css .= 'background-position:50% 0;';
		}

		// Custom height.
		$title_height = get_post_meta( $post_id, 'wpex_post_title_height', true );
		$title_height = (string) apply_filters( 'wpex_post_title_height', $title_height );

		// Add css for title min-height.
		if ( $title_height && $title_height_safe = sanitize_text_field( $title_height ) ) {
			switch ( $title_height_safe ) {
				case 'none':
					$title_height_safe = '0';
					break;
				default:
					if ( is_numeric( $title_height_safe ) ) {
						$title_height_safe = floatval( $title_height_safe ) . 'px';
					}
					break;
			}
			$add_css .= ".page-header.background-image-page-header{min-height:{$title_height_safe}!important;}";
		}

	}

	// Apply all css to the page-header class.
	if ( ! empty( $page_header_css ) ) {
		$add_css .= ".page-header{{$page_header_css}}";
	}

	// Overlay Styles.
	if ( ! empty( $bg_img )
		&& 'background-image' === $page_header_style
		&& $overlay_style = wpex_get_page_header_overlay_style()
	) {
		$overlay_css = '';

		// Use bg_color for overlay background.
		if ( ! empty( $bg_color_safe ) && 'bg_color' === $overlay_style ) {
			$overlay_css .= "background-color:{$bg_color_safe}!important;";
		}

		// Overlay opacity.
		$opacity = wpex_get_page_header_overlay_opacity();
		if ( $opacity && $opacity_safe = sanitize_text_field( $opacity ) ) {
			$overlay_css .= "opacity:{$opacity_safe};";
		}

		// Background pattern.
		$pattern = wpex_get_page_header_overlay_pattern();
		if ( $pattern && $pattern_url_safe = esc_url( $pattern ) ) {
			if ( ! empty( $bg_color_safe ) ) {
				$overlay_css .= "background-color:{$bg_color_safe};";
			} else {
				$overlay_css .= 'background-color:rgba(0,0,0,0.3);';
			}
			$overlay_css .= "background-image: url('{$pattern_url_safe}');";
			$overlay_css .= 'background-repeat: repeat;';
		}

		// Add overlay CSS.
		if ( $overlay_css ) {
			$add_css .= ".background-image-page-header-overlay{{$overlay_css}}";
		}

	}

	if ( ! empty( $add_css ) ) {
		$css_output .= $add_css;
	}

	return $css_output;
}
add_filter( 'wpex_head_css', 'wpex_page_header_css' );
