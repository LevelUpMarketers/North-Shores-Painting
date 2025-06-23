<?php

namespace TotalTheme\Header\Menu;

\defined( 'ABSPATH' ) || exit;

/**
 * Sticky Header Menu.
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
		if ( ! \totaltheme_call_static( 'Header\Menu', 'is_enabled' ) || \totaltheme_is_wpb_frontend_editor() ) {
			self::$is_enabled = false;
		} else {
			if ( \in_array( \totaltheme_call_static( 'Header\Core', 'style' ), [ 'two', 'three', 'four' ], true ) ) {
				$check = \get_theme_mod( 'fixed_header_menu', true );
			} else {
				$check = false;
			}
			self::$is_enabled = (bool) \apply_filters( 'wpex_has_sticky_header_menu', $check );
		}
		return self::$is_enabled;
	}

	/**
	 * Returns the sticky breakpoint.
	 */
	public static function breakpoint(): int {
		// @todo need fix for when the mobile menu is disabled \totaltheme_call_static( 'Mobile\Menu', 'is_enabled' )
		// there are some issues currently because of how settings/show hide in the Customizer and this
		// affects very few people.
		return (int) \totaltheme_call_static( 'Mobile\Menu', 'breakpoint' );
	}

	/**
	 * Register Scripts.
	 */
	private static function register_js(): void {
		\wp_register_script(
			'wpex-sticky-main-nav',
			\totaltheme_get_js_file( 'frontend/sticky/main-nav' ),
			[],
			\WPEX_THEME_VERSION,
			[
				'in_footer' => false,
				'strategy'  => 'defer',
			]
		);

		$sticky_params = [];

		// Check old filter.
		$old_filter = (array) \apply_filters( 'wpex_localize_array', [] );

		if ( isset( $old_filter['stickyNavbarBreakPoint'] ) ) {
			$sticky_params['breakpoint'] = \absint( $old_filter['stickyNavbarBreakPoint'] );
		} else {
			$sticky_params['breakpoint'] = self::breakpoint();
		}

		if ( isset( $old_filter['addStickyHeaderOffset'] ) ) {
			$sticky_params['offset'] = absint( $offset );
		}

		if ( true === (bool) \apply_filters( 'totaltheme/header/menu/sticky/run_on_window_load', false ) ) {
			$sticky_params['runOnWindowLoad'] = 1;
		}

		\wp_localize_script( 'wpex-sticky-main-nav', 'wpex_sticky_main_nav_params', $sticky_params );
	}

	/**
	 * Enqueues the sticky js.
	 */
	public static function enqueue_js(): void {
		self::register_js();
		\wp_enqueue_script( 'wpex-sticky-main-nav' );
	}

}
