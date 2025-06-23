<?php

namespace TotalTheme\Header;

\defined( 'ABSPATH' ) || exit;

/**
 * Sticky Header.
 */
final class Sticky {

	/**
	 * Check if enabled or not.
	 */
	protected static $is_enabled;

	/**
	 * The sticky style.
	 */
	protected static $style;

	/**
	 * The sticky logo src.
	 */
	protected static $logo_src;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Returns an array of style choices for the sticky header.
	 */
	public static function style_choices(): array {
		return [
			'standard'        => esc_html__( 'Standard', 'total' ),
			'shrink'          => esc_html__( 'Shrink', 'total' ),
			'shrink_animated' => esc_html__( 'Animated Shrink', 'total' ),
		];
	}

	/**
	 * Returns sticky header style.
	 */
	public static function style(): string {
		if ( ! \is_null( self::$style ) ) {
			return self::$style;
		}

		if ( \totaltheme_call_static( 'Header\core', 'is_custom' ) ) {
			// @note - we can't ust CSS sticky for transparent header.
			return ( 'css' === \get_theme_mod( 'header_builder_sticky_type', 'js' ) && ! \totaltheme_call_static( 'Header\Overlay', 'is_enabled' ) ) ? 'css' : 'standard';
		}

		// Get default style from customizer.
		$style = \get_theme_mod( 'fixed_header_style', 'standard' );

		// Move old disabled style to new on/off switch.
		if ( 'disabled' === $style ) {
			\set_theme_mod( 'fixed_header', false );
			\remove_theme_mod( 'fixed_header_style' );
		}

		// Fallback style.
		if ( ! $style || 'disabled' === $style ) {
			$style = 'standard';
		}

		$style = \apply_filters( 'wpex_sticky_header_style', $style ); // @deprecated

		self::$style = (string) \apply_filters( 'totaltheme/header/sticky/style', $style );

		return self::$style;
	}

	/**
	 * Checks if the sticky header is enabled or not.
	 */
	public static function is_enabled(): bool {
		if ( ! \is_null( self::$is_enabled ) ) {
			return self::$is_enabled;
		}

		if ( ! \totaltheme_call_static( 'Header\core', 'is_enabled' ) || \totaltheme_is_wpb_frontend_editor() || \totaltheme_is_elementor_editor() ) {
			self::$is_enabled = false;
			return self::$is_enabled;
		}

		$check = false;

		// Check meta first it should override any filter!
		if ( $post_id = \wpex_get_current_post_id() ) {
			$meta_check = \get_post_meta( $post_id, 'wpex_sticky_header', true );
			if ( 'disable' === $meta_check ) {
				return false;
			} elseif ( 'enable' === $meta_check ) {
				return true;
			}
		}

		// Get header style.
		$header_style = totaltheme_call_static( 'Header\Core', 'style' );

		// Sticky header for builder.
		if ( 'builder' === $header_style ) {
			$check = \get_theme_mod( 'header_builder_sticky', false );
		}

		// Standard sticky header.
		else {

			// @note: we still need to check the style incase someone used filters to set the header style to disabled.
			$check = ( 'disabled' === self::style() ) ? false : \get_theme_mod( 'fixed_header', true );

			// Ok it's enabled now we need to check based on the header style.
			if ( $check ) {
				if ( \in_array( $header_style, \wpex_get_header_styles_with_sticky_support(), true ) ) {
					$check = true;
				} elseif ( \in_array( $header_style, [ 'two', 'three', 'four', 'six' ], true ) ) {
					$check = \get_theme_mod( 'fixed_header_mobile' );
				}
			}
		}

		$check = \apply_filters( 'wpex_has_fixed_header', $check ); // @deprecated

		self::$is_enabled = (bool) \apply_filters( 'totaltheme/header/sticky/is_enabled', $check );

		return self::$is_enabled;
	}

	/**
	 * Checks if the sticky header has mobile support.
	 */
	public static function has_mobile_support(): bool {
		if ( ! \wpex_is_layout_responsive() ) {
			return true;
		}
		if ( \totaltheme_call_static( 'Header\core', 'is_custom' ) ) {
			$check = true;
		} else {
			$check = \get_theme_mod( 'fixed_header_mobile' ); // has always been disabled by default.
		}
		return (bool) \apply_filters( 'totaltheme/header/sticky/has_mobile_support', $check );
	}

	/**
	 * Returns the sticky header breakpoint.
	 */
	public static function breakpoint(): int {
		$mobile_menu_breakpoint = \totaltheme_call_static( 'Mobile\Menu', 'breakpoint' );
		$breakpoint = ! \is_null( $mobile_menu_breakpoint ) ? \absint( $mobile_menu_breakpoint ) + 1 : '959';
		$breakpoint = \apply_filters( 'wpex_sticky_header_breakpoint', $breakpoint ); // @deprecated
		return (int) \apply_filters( 'totaltheme/header/sticky/breakpoint', $breakpoint );
	}

	/**
	 * Returns the sticky header start position.
	 */
	public static function get_start_position(): string {
		$position = \get_theme_mod( 'fixed_header_start_position' );
		if ( \is_singular() ) {
			$meta_position = \get_post_meta( get_the_ID(), 'fixed_header_start_position', true );
			if ( $meta_position ) {
				$position = $meta_position;
			}
		}
		if ( $position ) {
			$position = \sanitize_text_field( $position );
		}
		$position = \apply_filters( 'wpex_sticky_header_start_position', $position ); // @deprecated
		return (string) \apply_filters( 'totaltheme/header/sticky/start_position', $position );
	}

	/**
	 * Checks if the shrink header is enabled.
	 */
	public static function is_shrink_enabled(): bool {
		$check = \in_array( self::style(), [ 'shrink', 'shrink_animated', true ], true );
		$check = \apply_filters( 'wpex_has_shrink_sticky_header', $check ); // @deprecated
		return (bool) \apply_filters( 'totaltheme/header/sticky/is_shrink_enabled', $check );
	}

	/**
	 * Checks if the shrink header is enabled on mobile.
	 */
	public static function is_shrink_enabled_mobile(): bool {
		$check = true;
		$check = \apply_filters( 'wpex_has_shrink_sticky_header_mobile', $check ); // @deprecated
		return (bool) \apply_filters( 'totaltheme/header/sticky/is_shrink_enabled_mobile', $check );
	}

	/**
	 * Returns the shrink height.
	 */
	private static function get_shrink_height(): int {
		$height = ( $height = \get_theme_mod( 'fixed_header_shrink_end_height' ) ) ? \intval( $height ) : 50;
		if ( ! \totaltheme_call_static( 'Header\Core', 'has_flex_container' ) ) {
			$height = $height + 20;
		}
		return $height;
	}

	/**
	 * Returns sicky header logo image url.
	 */
	public static function get_logo_image_url() {
		if ( \totaltheme_call_static( 'Header\core', 'is_custom' ) ) {
			return ''; // Not needed for the sticky header builder.
		}

		$logo_src = self::get_logo_image_src();

		if ( isset( $logo_src[0] ) ) {
			return \wpex_get_image_url( $logo_src[0]  );
		}
	}

	/**
	 * Returns sicky header logo image src.
	 */
	public static function get_logo_image_src() {
		if ( null !== self::$logo_src ) {
			return self::$logo_src;
		}

		$logo = \get_theme_mod( 'fixed_header_logo' );

		// Set sticky logo to header logo for overlay header when custom overlay logo is set
		// This way you can have a white logo on overlay but the default on sticky.
		if ( empty( $logo )
			&& ! \totaltheme_call_static( 'Header\Overlay', 'is_global' ) // make sure the page is not using a global overlay header.
			&& \totaltheme_call_static( 'Header\Overlay', 'logo_img' ) // check for custom overlay header logo.
			&& \totaltheme_call_static( 'Header\Overlay', 'is_enabled' ) // check if overlay header is enabled.
		) {
			$header_logo = \totaltheme_call_static( 'Header\Logo', 'get_image_id', false );
			if ( $header_logo ) {
				$logo = $header_logo;
			}
		}

		$logo = \apply_filters( 'wpex_fixed_header_logo', $logo ); // @deprecated
		$logo = \apply_filters( 'totaltheme/header/sticky/logo_image_id', $logo );

		if ( \is_numeric( $logo ) ) {
			self::$logo_src = \wp_get_attachment_image_src( $logo, 'full', false );
		} elseif ( is_string( $logo ) ) {
			self::$logo_src = [ $logo, '', '', '' ];
		}

		return self::$logo_src;
	}

	/**
	 * Returns sicky header logo image height.
	 */
	public static function get_logo_image_height() {
		$logo_src = self::get_logo_image_src();
		if ( ! empty( $logo_src[2] ) ) {
			return \absint( $logo_src[2] );
		} else {
			return \totaltheme_call_static( 'Header\Logo', 'get_image_height' );
		}
	}

	/**
	 * Returns sicky header logo image width.
	 */
	public static function get_logo_image_width() {
		$logo_src = self::get_logo_image_src();
		if ( ! empty( $logo_src[1] ) ) {
			return \absint( $logo_src[1] );
		} else {
			return \totaltheme_call_static( 'Header\Logo', 'get_image_width' );
		}
	}

	/**
	 * Returns sicky header logo image url.
	 */
	public static function get_retina_logo_image_url() {
		$logo = \wpex_get_translated_theme_mod( 'fixed_header_logo_retina' );
		/*
		 * Set retina logo for sticky header when the header overlay is set
		 * and the sticky header logo isn't set, since the default logo is displayed for the sticky header
		 * when using the overlay header and a custom logo.
		 */
		if ( ! $logo && ! \get_theme_mod( 'fixed_header_logo' ) ) {
			$logo = \wpex_get_translated_theme_mod( 'retina_logo' );
			$logo = \apply_filters( 'wpex_header_logo_img_retina_url', $logo ); // @deprecated
		}
		$logo = \apply_filters( 'wpex_fixed_header_logo_retina', $logo );  // @deprecated
		$logo = \apply_filters( 'totaltheme/header/sticky/logo_retina_image_id', $logo );
		return \wpex_get_image_url( $logo );
	}

	/**
	 * Register Scripts.
	 */
	private static function register_js(): void {
		\wp_register_script(
			'wpex-sticky-header',
			\totaltheme_get_js_file( 'frontend/sticky/header' ),
			[],
			\WPEX_THEME_VERSION,
			[
				'in_footer' => false,
				'strategy'  => 'defer',
			]
		);

		$sticky_params = [];

		if ( \class_exists( 'Scroll_Up_Sticky_Header_For_Total' ) ) {
			$sticky_params['scrollUp'] = 1;
		}

		// Check old filter.
		$old_filter = (array) \apply_filters( 'wpex_localize_array', [] );

		// Breakpoint.
		if ( isset( $old_filter['stickyHeaderBreakPoint'] ) ) {
			$sticky_params['breakpoint'] = absint( $old_filter['stickyHeaderBreakPoint'] );
		} else {
			$sticky_params['breakpoint'] = self::breakpoint();
		}

		// Custom offset.
		if ( isset( $old_filter['addStickyHeaderOffset'] ) ) {
			$sticky_params['offset'] = absint( $old_filter['addStickyHeaderOffset'] );
		}

		// Sticky on mobile check.
		if ( isset( $old_filter['hasStickyMobileHeader'] ) ) {
			$sticky_params['mobileSupport'] = $old_filter['hasStickyMobileHeader'];
		} elseif ( self::has_mobile_support() ) {
			$sticky_params['mobileSupport'] = 1;
		}

		// Sticky header start position.
		if ( isset( $old_filter['stickyHeaderStartPosition'] ) ) {
			$sticky_params['startPoint'] = $old_filter['stickyHeaderStartPosition'];
		} elseif ( $fixed_startp = self::get_start_position() ) {
			$sticky_params['startPoint'] = \str_replace( 'px', '', $fixed_startp );
		}

		// Shrink sticky header > used for local-scroll offset.
		if ( isset( $old_filter['hasStickyHeaderShrink'] ) ) {
			$sticky_params['shrink'] = $old_filter['hasStickyHeaderShrink'];
		} elseif ( self::is_shrink_enabled() ) {
			$sticky_params['shrink'] = 1;
		}
		if ( isset( $sticky_params['shrink'] ) && \wp_validate_boolean( $sticky_params['shrink'] ) ) {
			if ( isset( $old_filter['hasStickyMobileHeaderShrink'] ) ) {
				$sticky_params['shrinkOnMobile'] = $old_filter['hasStickyMobileHeaderShrink'];
			} elseif ( self::is_shrink_enabled_mobile() ) {
				$sticky_params['shrinkOnMobile'] = 1;
			}
			if ( isset( $old_filter['shrinkHeaderHeight'] ) ) {
				$sticky_params['shrinkHeight'] = $old_filter['shrinkHeaderHeight'];
			} else {
				$sticky_params['shrinkHeight'] = self::get_shrink_height();
			}
		}

		if ( true === (bool) \apply_filters( 'totaltheme/header/sticky/run_on_window_load', false ) ) {
			$sticky_params['runOnWindowLoad'] = 1;
		}

		\wp_localize_script( 'wpex-sticky-header', 'wpex_sticky_header_params', $sticky_params );
	}

	/**
	 * Enqueues the sticky js.
	 */
	public static function enqueue_js(): void {
		if ( \in_array( \totaltheme_call_static( 'Header\Sticky', 'style' ), [ 'standard', 'shrink', 'shrink_animated' ], true ) ) {
			self::register_js();
			\wp_enqueue_script( 'wpex-sticky-header' );
		}
	}

}
