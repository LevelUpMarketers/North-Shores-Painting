<?php

defined( 'ABSPATH' ) || exit;

/**
 * Get togglebar content ID.
 */
function wpex_togglebar_content_id() {
	$content_id = get_theme_mod( 'toggle_bar_page', null );
	$content_id = (int) apply_filters( 'wpex_toggle_bar_content_id', $content_id );
	if ( $content_id ) {
		return wpex_parse_obj_id( $content_id );
	}
}

/**
 * Returns togglebar content.
 */
function wpex_togglebar_content(): string {
	static $content = null;
	if ( null === $content ) {
		$content = get_post_meta( wpex_get_current_post_id(), 'wpex_togglebar_content', true );
		if ( ! $content ) {
			if ( $togglebar_id = wpex_togglebar_content_id() ) {
				$content = totaltheme_shortcode_unautop( get_post_field( 'post_content', $togglebar_id ) );
			}
			if ( ! $content ) {
				$content = get_theme_mod( 'toggle_bar_content', null );
			}
		}
		$content = apply_filters( 'wpex_togglebar_content', $content );
	}
	return (string) $content;
}

/**
 * Check if togglebar is enabled.
 */
function wpex_has_togglebar( $post_id = '' ): bool {
	static $check = null;
	if ( null === $check ) {
		if ( get_theme_mod( 'toggle_bar_remember_state', false )
			&& get_theme_mod( 'toggle_bar_enable_dismiss', false )
			&& 'visible' === get_theme_mod( 'toggle_bar_default_state', 'hidden' )
			&& 'hidden' === wpex_togglebar_state_cookie()
		) {
			$check = false;
		} elseif ( ! wpex_togglebar_content() && ! \totaltheme_call_static( 'Integration\Elementor', 'location_exists', 'togglebar' ) ) {
			$check = false;
		} else {
			// Check if enabled in Customizer.
			$check = get_theme_mod( 'toggle_bar', true );
			// Get post ID if not defined.
			if ( ! $post_id ) {
				$post_id = wpex_get_current_post_id();
			}
			// Check meta.
			if ( $post_id ) {
				if ( 'enable' === get_post_meta( $post_id, 'wpex_disable_toggle_bar', true ) ) {
					$check = true;
				}
				if ( 'on' === get_post_meta( $post_id, 'wpex_disable_toggle_bar', true ) ) {
					$check = false;
				}
			}
			// @deprecated v5.1.3
			$check = apply_filters( 'wpex_toggle_bar_active', $check );
			$check = apply_filters( 'wpex_has_togglebar', $check );
		}
	}
	return (bool) $check;
}

/**
 * Get correct togglebar style.
 */
function wpex_togglebar_style() {
	$style = ( $style = get_theme_mod( 'toggle_bar_display' ) ) ? sanitize_text_field( $style ) : 'overlay';
	$style = (string) apply_filters( 'wpex_togglebar_style', $style );
	return $style;
}

/**
 * Returns togglebar classes.
 */
function wpex_togglebar_class() {
	if ( $classes = wpex_togglebar_classes() ) {
		echo 'class="' . esc_attr( $classes ) . '"';
	}
}

/**
 * Returns togglebar inner classes.
 */
function wpex_togglebar_inner_class() {
	$classes = [
		'wpex-flex',
		'wpex-flex-col',
		'wpex-justify-center',
	];

	$align = get_theme_mod( 'toggle_bar_align' );

	if ( $align && ( 'start' === $align || 'center' === $align || 'end' === $align ) ) {
		$classes[] = 'wpex-items-' . sanitize_html_class( $align );
	}

	if ( get_theme_mod( 'toggle_bar_fullwidth', false ) ) {
		$classes[] = 'wpex-px-30';
	} else {
		$classes[] = 'container';
	}

	$classes = (array) apply_filters( 'wpex_togglebar_inner_class', $classes );

	if ( $classes ) {
		echo 'class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/**
 * Returns togglebar data attributes.
 */
function wpex_togglebar_data_attributes() {
	$attributes                        = [];
	$current_state                     = wpex_togglebar_state();
	$attributes['data-state']          = $current_state;
	$remember_state                    = wp_validate_boolean( get_theme_mod( 'toggle_bar_remember_state', false ) );
	$attributes['data-remember-state'] = ( true === $remember_state ) ? 'true' : 'false';

	if ( get_theme_mod( 'toggle_bar_enable_dismiss', false ) && 'visible' === get_theme_mod( 'toggle_bar_default_state', 'hidden' ) ) {
		$attributes['data-allow-toggle'] = 'false';
	} else {
		$attributes['data-allow-toggle'] = 'true';
	}

	$attributes = (array) apply_filters( 'wpex_togglebar_data_attributes', $attributes );

	if ( $attributes ) {
		foreach ( $attributes as $attribute_k => $attribute_v ) {
			echo ' ' . esc_attr( $attribute_k ) . '="' . esc_attr( $attribute_v ) . '"';
		}
	}
}

/**
 * Returns togglebar state.
 *
 * @return (string) hidden or visible
 */
function wpex_togglebar_state() {
	static $state = null;
	if ( null === $state ) {
		if ( get_theme_mod( 'toggle_bar_remember_state', false ) ) {
			$state = wpex_togglebar_state_cookie();
			if ( $state ) {
				return $state; // bail early.
			}
		}
		$state = get_theme_mod( 'toggle_bar_default_state', 'hidden' );
		$state = (string) apply_filters( 'wpex_togglebar_state', $state );
		switch ( $state ) {
			case 'open':
				$state = 'visible';
				break;
			case 'closed':
				$state = 'hidden';
				break;
		}
	}
	return $state;
}

/**
 * Returns togglebar state.
 */
function wpex_togglebar_state_cookie() {
	if ( ! empty( $_COOKIE['total_togglebar_state'] ) ) {
		$cookie_safe = sanitize_text_field( wp_unslash( $_COOKIE['total_togglebar_state'] ) );
		if ( in_array( $cookie_safe, [ 'hidden', 'visible' ], true ) ) {
			return $cookie_safe;
		}
	}
}

/**
 * Returns togglebar visibility.
 *
 * @todo deprecate
 */
function wpex_togglebar_visibility(): string {
	return (string) apply_filters( 'wpex_togglebar_visibility', get_theme_mod( 'toggle_bar_visibility' ) );;
}

/**
 * Returns togglebar classes.
 */
function wpex_togglebar_classes() {
	$style       = wpex_togglebar_style();
	$state       = wpex_togglebar_state();
	$is_builder  = \totaltheme_call_static( 'Integration\Elementor', 'location_exists', 'togglebar' );
	$animation   = get_theme_mod( 'toggle_bar_animation', 'fade' );
	$padding_y   = get_theme_mod( 'toggle_bar_padding_y' );
	$dismissable = false;

	if ( get_theme_mod( 'toggle_bar_enable_dismiss', false ) && 'visible' === get_theme_mod( 'toggle_bar_default_state', 'hidden' ) ) {
		$dismissable = true;
	}

	if ( $padding_y && '0px' !== $padding_y ) {
		$padding_y = absint( $padding_y );
	}

	/*** Add theme classes ***/
	$classes = [];

	$classes[] = sanitize_html_class( "toggle-bar-{$style}" );

		if ( 'overlay' === $style && $animation ) {
			$classes[] = sanitize_html_class( "toggle-bar-{$animation}" );
		}

		if ( 'visible' === $state ) {
			$classes[] = 'active-bar';
		} elseif( ! get_theme_mod( 'toggle_bar_remember_state', false ) ) {
			$classes[] = 'close-on-doc-click';
		}

		if ( $visibility = wpex_togglebar_visibility() ) {
			$classes[] = totaltheme_get_visibility_class( (string) $visibility );
		}

	/*** Add utility classes ***/

		// Default.
		$classes[] = 'wpex-invisible';
		$classes[] = 'wpex-opacity-0';
		$classes[] = 'wpex-w-100';
		$classes[] = 'wpex-surface-1';

		// Style specific classes.
		switch ( $style ) {
			case 'overlay':
				$classes[] = 'wpex-fixed';
			//	$classes[] = 'wpex-top-0'; // we use CSS to offset things like admin bar and site frame
				$classes[] = 'wpex-inset-x-0';
				$classes[] = 'wpex-z-overlay';
				$classes[] = 'wpex-max-h-100';
				$classes[] = 'wpex-overflow-auto';
				$classes[] = 'wpex-shadow';
				if ( ! $padding_y ) {
					$padding_y = '40';
				}
				break;
			case 'inline':
				if ( $dismissable ) {
					$classes[] = 'wpex-relative';
				}
				$classes[] = 'wpex-hidden';
				$classes[] = 'wpex-border-b';
				$classes[] = 'wpex-border-solid';
				$classes[] = 'wpex-border-main';
				if ( ! $padding_y ) {
					$padding_y = '20';
				}
				break;
		}

		// Add vertical padding.
		if ( ! $is_builder && $padding_y && '0px' !== $padding_y ) {
			$classes[] = 'wpex-py-' . sanitize_html_class( $padding_y );
		}

		// Add animation classes.
		if ( 'overlay' === $style && $animation ) {
			$classes[] = 'wpex-transition-all';
			$classes[] = 'wpex-duration-300';
			if ( 'fade-slide' === $animation ) {
				$classes[] = '-wpex-translate-y-50';
			}
		}

		// Sanitize.
		$classes = array_map( 'esc_attr', $classes );

		// Apply filters.
		$classes = apply_filters( 'wpex_togglebar_classes', $classes );

		// Turn classes into string.
		if ( is_array( $classes ) ) {
			$classes = implode( ' ', $classes );
		}

	return $classes;
}
