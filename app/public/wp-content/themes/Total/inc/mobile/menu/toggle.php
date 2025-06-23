<?php

namespace TotalTheme\Mobile\Menu;

\defined( 'ABSPATH' ) || exit;

/**
 * Toggle mobile menu.
 */
final class Toggle {

	/**
	 * JS handle.
	 */
	public const JS_HANDLE = 'wpex-mobile-menu-toggle';

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
		return \in_array( \totaltheme_call_static( 'Mobile\Menu', 'style' ), [ 'toggle', 'toggle_inline', 'toggle_full' ], true ) && \totaltheme_call_static( 'Mobile\Menu', 'is_enabled' );
	}

	/**
	 * Enqueue Scripts.
	 */
	public function enqueue_js(): void {
		\wp_enqueue_script(
			self::JS_HANDLE,
			\totaltheme_get_js_file( 'frontend/mobile-menu/toggle' ),
			[ \WPEX_THEME_JS_HANDLE ],
			\WPEX_THEME_VERSION,
			[
				'strategy' => 'defer',
			]
		);
		\wp_localize_script(
			self::JS_HANDLE,
			'wpex_mobile_menu_toggle_params',
			\totaltheme_call_static( 'Mobile\Menu', 'get_global_js_l10n' )
		);
	}

	/**
	 * Returns the menu position.
	 */
	public static function get_position(): string {
		if ( 'toggle_inline' === \totaltheme_call_static( 'Mobile\Menu', 'style' ) && ! \wp_validate_boolean( \get_theme_mod( 'fixed_header_mobile' ) ) ) {
			$position = 'afterheader';
			switch ( \wpex_header_menu_mobile_toggle_style() ) {
				case 'fixed_top':
					$position = 'absolute';
					break;
				case 'navbar':
					if ( 'outer_wrap_before' === get_theme_mod( 'mobile_menu_navbar_position', 'wpex_hook_header_bottom' )
						|| totaltheme_call_static( 'Header\Overlay', 'is_enabled' )
					) {
						$position = 'afterself';
					}
					break;
				default:
					if ( totaltheme_call_static( 'Header\Overlay', 'is_enabled' )
						|| ( get_theme_mod( 'fixed_header_mobile' ) && totaltheme_call_static( 'Header\Sticky', 'is_enabled' ) && Mobile_Menu::breakpoint() >= 9999 )
					) {
						$position = 'absolute';
					}
					break;
			}
		} else {
			$position = 'absolute';
		}
		return $position;
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
