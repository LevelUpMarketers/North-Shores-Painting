<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Disable Google Services.
 */
class Disable_Google_Services {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Add hooks.
	 */
	public static function init() {
		\add_filter( 'wpex_google_fonts_array', '__return_empty_array' );
		\add_filter( 'vc_google_fonts_render_filter', '__return_false' );
		\add_filter( 'vc_google_fonts_get_fonts_filter', '__return_empty_array' );
		\add_action( 'wp_print_scripts', [ self::class, 'remove_scripts' ], 10 );
		\add_action( 'wp_enqueue_scripts', [ self::class, 'remove_scripts' ], 10 );
		\add_action( 'wp_footer', [ self::class, 'remove_inline_scripts' ], 10 );
	}

	/**
	 * Remove scripts.
	 */
	public static function remove_scripts() {
		\wp_dequeue_script( 'webfont' );
		\wp_dequeue_style( 'rs-roboto' );
	}

	/**
	 * Remove footer scripts.
	 */
	public static function remove_inline_scripts() {
		global $wp_styles;
		if ( $wp_styles ) {
			foreach ( $wp_styles->registered as $handle => $data ) {
				if ( is_string( $handle ) ) {
					self::maybe_dequeue_style( $handle );
				}
			}
		}
	}

	/**
	 * Checks to see if a specific style should be removed.
	 */
	private static function maybe_dequeue_style( string $handle ) {
		if ( \str_starts_with( $handle, 'vc_google_fonts_' ) ) {
			\wp_deregister_style( $handle );
			\wp_dequeue_style( $handle );
		}
	}

}
