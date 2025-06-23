<?php

namespace TotalTheme\Topbar;

\defined( 'ABSPATH' ) || exit;

/**
 * Sticky Topbar.
 */
class Sticky {

	/**
	 * Check if enabled or not.
	 */
	protected static $is_enabled;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Checks if the sticky header is enabled or not.
	 */
	public static function is_enabled(): bool {
		if ( ! \is_null( self::$is_enabled ) ) {
			return self::$is_enabled;
		}
		if ( ! \totaltheme_call_static( 'Topbar\Core', 'is_enabled' ) || \totaltheme_is_wpb_frontend_editor() ) {
			self::$is_enabled = false;
		} else {
			$check = \get_theme_mod( 'top_bar_sticky' );
			$check = \apply_filters( 'wpex_has_sticky_topbar', $check );
			self::$is_enabled = (bool) \apply_filters( 'totaltheme/topbar/sticky/is_enabled', $check );
		}
		return self::$is_enabled;
	}

	/**
	 * Returns the sticky breakpoint.
	 */
	public static function breakpoint(): int {
		$old_filter = (array) \apply_filters( 'wpex_localize_array', [] );
		if ( isset( $old_filter['hasStickyTopBarMobile'] ) && \wp_validate_boolean( $old_filter['hasStickyTopBarMobile'] ) ) {
			return 0;
		} elseif ( isset( $old_filter['stickyTopBarBreakPoint'] ) ) {
			return \absint( $old_filter['stickyTopBarBreakPoint'] );
		} else {
			return \wp_validate_boolean( \get_theme_mod( 'top_bar_sticky_mobile', true ) ) ? 0 : (int) \totaltheme_call_static( 'Mobile\Menu', 'breakpoint' );
		}
	}

	/**
	 * Register Scripts.
	 */
	private static function register_js(): void {
		\wp_register_script(
			'wpex-sticky-topbar',
			\totaltheme_get_js_file( 'frontend/sticky/topbar' ),
			[],
			\WPEX_THEME_VERSION,
			[
				'in_footer' => false,
				'strategy'  => 'defer',
			]
		);

		$sticky_params = [
			'breakpoint' => self::breakpoint(),
		];

		if ( true === (bool) \apply_filters( 'totaltheme/topbar/sticky/run_on_window_load', false ) ) {
			$sticky_params['runOnWindowLoad'] = 1;
		}

		\wp_localize_script( 'wpex-sticky-topbar', 'wpex_sticky_topbar_params', $sticky_params );
	}

	/**
	 * Enqueues the sticky js.
	 */
	public static function enqueue_js(): void {
		self::register_js();
		\wp_enqueue_script( 'wpex-sticky-topbar' );
	}

}
