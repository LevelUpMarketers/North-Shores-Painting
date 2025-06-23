<?php

namespace TotalTheme\Mobile\Menu;

\defined( 'ABSPATH' ) || exit;

/**
 * Full Screen mobile menu.
 */
final class Full_Screen {

	/**
	 * JS handle.
	 */
	public const JS_HANDLE = 'wpex-mobile-menu-full-screen';

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of our class.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {}

	/**
	 * Check if the toggle mobile menu is enabled.
	 */
	public function is_enabled(): bool {
		return \in_array( \totaltheme_call_static( 'Mobile\Menu', 'style' ), [ 'full_screen', 'full_screen_under_header' ], true ) && \totaltheme_call_static( 'Mobile\Menu', 'is_enabled' );
	}

	/**
	 * Enqueue Scripts.
	 */
	public function enqueue_js(): void {
		\wp_enqueue_script(
			self::JS_HANDLE,
			\totaltheme_get_js_file( 'frontend/mobile-menu/full-screen' ),
			[ \WPEX_THEME_JS_HANDLE ],
			\WPEX_THEME_VERSION,
			[
				'strategy' => 'defer',
			]
		);
		\wp_localize_script(
			self::JS_HANDLE,
			'wpex_mobile_menu_full_screen_params',
			\totaltheme_call_static( 'Mobile\Menu', 'get_global_js_l10n' )
		);
	}

	/**
	 * Register Scripts.
	 */
	public function register_js(): void {
		\_deprecated_function( __METHOD__, 'Total Theme 6.0' );
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}

}
