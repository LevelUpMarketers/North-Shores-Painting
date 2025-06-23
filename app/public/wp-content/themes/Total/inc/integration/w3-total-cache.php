<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * W3 Total Cache Configuration Class.
 */
final class W3_Total_cache {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of W3_Total_cache.
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
	private function __construct() {
		\add_filter( 'w3tc_minify_css_do_tag_minification', [ $this, 'exclude_css_from_minify' ], 10, 3 );
	}

	/**
	 * Exclude certain theme files from the minification process.
	 */
	public function exclude_css_from_minify( $do_tag_minification, $style_tag, $file ) {
		if ( ! empty( $file ) ) {
			$exclude_files = [
				'wpex-mobile-menu-breakpoint-max',
				'wpex-mobile-menu-breakpoint-min',
				'wpex-overlay-header-css',
				'wpex-vertical-header',
			];
			foreach ( $exclude_files as $excluded_file ) {
				if ( false !== \strpos( $file, $excluded_file ) ) {
					return false;
				}
			}
		}
		return $do_tag_minification;
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
