<?php

namespace TotalTheme\Header;

\defined( 'ABSPATH' ) || exit;

/**
 * Vertical Header.
 */
class Vertical {

	/**
	 * Vertical header is enabled or not.
	 */
	protected static $is_enabled;

	/**
	 * The vertical header style.
	 */
	protected static $style;

	/**
	 * The vertical header position.
	 */
	protected static $position;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Registers the vertical header stylesheet.
	 */
	public static function register_stylesheet(): void {
		$theme_handle  = \totaltheme_call_static( 'Scripts\CSS', 'get_theme_handle' );
		$mm_breakpoint = \totaltheme_call_static( 'Mobile\Menu', 'breakpoint' );

		if ( $mm_breakpoint < 9999 && \wpex_is_layout_responsive() ) {
			$min_media = 'only screen and (min-width:' . ( $mm_breakpoint + 1 )  . 'px)';
		}

		\wp_register_style(
			'wpex-vertical-header',
			\totaltheme_get_css_file( 'frontend/header/vertical' ),
			$theme_handle ? [ $theme_handle ] : [],
			\WPEX_THEME_VERSION,
			$min_media ?? 'all'
		);
	}

	/**
	 * Enqueues the vertical header stylesheet.
	 */
	public static function enqueue_stylesheet(): void {
		\wp_enqueue_style( 'wpex-vertical-header' );
	}

	/**
	 * Enqueues the vertical header stylesheet if enabled.
	 */
	public static function maybe_enqueue_stylesheet(): void {
		if ( ! self::is_enabled() ) {
			return;
		}
		self::register_stylesheet();
		self::enqueue_stylesheet();
	}

	/**
	 * Checks if the vertical header is enabled or not.
	 */
	public static function is_enabled(): bool {
		if ( ! \is_null( self::$is_enabled ) ) {
			return self::$is_enabled;
		}

		$check = false;

		if ( 'six' === Core::style() ) {
			$check = true;
		}

		/**
		 * Filters whether the site is using a vertical header.
		 *
		 * @param bool $check
		 */
		$check = (bool) \apply_filters( 'totaltheme/header/vertical/is_enabled', $check );

		/*** deprecated ***/
		$check = (bool) \apply_filters( 'wpex_has_vertical_header', $check );

		self::$is_enabled = $check;

		return self::$is_enabled;
	}

	/**
	 * Returns the vertical header style (default or fixed).
	 */
	public static function style(): string {
		if ( ! \is_null( self::$style ) ) {
			return self::$style;
		}

		self::$style = (string) \get_theme_mod( 'vertical_header_style' );

		return self::$style;
	}

	/**
	 * Returns the vertical header position.
	 */
	public static function position(): string {
		if ( ! \is_null( self::$position ) ) {
			return self::$position;
		}

		$position = \get_theme_mod( 'vertical_header_position' ) ?: 'left';

		/**
		 * Filters whether the site is using a vertical header.
		 *
		 * @param bool $check
		 */
		$position = (string) \apply_filters( 'totaltheme/header/vertical/position', $position );

		/*** deprecated ***/
		$position = (string) \apply_filters( 'wpex_vertical_header_position', $position );

		self::$position = $position;

		return self::$position;
	}

}
