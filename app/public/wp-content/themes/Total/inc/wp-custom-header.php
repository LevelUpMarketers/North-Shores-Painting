<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Adds support for the Custom Header image and adds it to the header.
 */
final class WP_Custom_Header {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Hook into actions and filters.
	 */
	public static function init() {
		\add_filter( 'after_setup_theme', [ self::class, 'add_support' ] );
		\add_filter( 'wpex_head_css', [ self::class, 'custom_header_css' ], 100 );
	}

	/**
	 * Retrieves cached CSS or generates the responsive CSS
	 */
	public static function add_support() {
		\add_theme_support( 'custom-header', \apply_filters( 'wpex_custom_header_args', array(
			'default-image'          => '',
			'width'                  => 0,
			'height'                 => 0,
			'flex-width'             => true,
			'flex-height'            => true,
			'admin-head-callback'    => 'wpex_admin_header_style',
			'admin-preview-callback' => 'wpex_admin_header_image',
		) ) );
	}

	/**
	 * Displays header image as a background for the header
	 */
	public static function custom_header_css( $output ) {
		if ( $header_image = \get_header_image() ) {
			$output .= '#site-header,.is-sticky #site-header{background-image:url(' . \esc_url( $header_image ) . ');background-size: cover;}';
		}
		return $output;
	}

}
