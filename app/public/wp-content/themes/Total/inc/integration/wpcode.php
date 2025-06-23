<?php

namespace TotalTheme\Integration;

defined( 'ABSPATH' ) || exit;

/**
 * WPCode Integration.
 */
class WPCode {

	/**
	 * Init.
	 */
	public static function init() {
		if ( \is_admin() ) {
			add_action( 'wpcode_before_admin_pages_loaded', [ self::class, 'register_library_username' ] );
		}
	}

	/**
	 * Registers the totaltheme username to display snippets in the WPCode library.
	 */
	public static function register_library_username(): void {
		wpcode_register_library_username( 'totaltheme', 'TotalTheme', WPEX_THEME_VERSION );
	}

}
