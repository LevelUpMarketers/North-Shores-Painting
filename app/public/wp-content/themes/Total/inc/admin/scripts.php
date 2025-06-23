<?php

namespace TotalTheme\Admin;

\defined( 'ABSPATH' ) || exit;

/**
 * Register and Enqueue Admin scripts.
 */
final class Scripts {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Scripts.
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
		\add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ], 5 );
	}

	/**
	 * Register admin scripts.
	 */
	public function register_scripts() {

		// Custom properties.
		\wp_register_style(
			'wpex-custom-properties',
			\get_theme_file_uri( '/assets/css/wpex-custom-properties.min.css' ),
			[],
			\WPEX_THEME_VERSION,
			'all'
		);

		// Utility.
		\wp_register_style(
			'wpex-utility',
			\get_theme_file_uri( '/assets/css/wpex-utility.min.css' ),
			[],
			\WPEX_THEME_VERSION,
			'all'
		);

		// Chosen select.
		\wp_register_style(
			'wpex-chosen',
			\totaltheme_get_css_file( 'vendor/chosen' ),
			false,
			'1.4.1'
		);

		\wp_register_script(
			'wpex-chosen',
			\totaltheme_get_js_file( 'vendor/chosen.jquery' ),
			[ 'jquery' ],
			'1.4.1'
		);

		// Components.
		if ( \file_exists( \WPEX_THEME_DIR . '/build/components.asset.php' ) ) {
			$components_asset = require \WPEX_THEME_DIR . '/build/components.asset.php';
		}

		\wp_register_style(
			'totaltheme-components',
			\WPEX_THEME_URI . '/build/style-components.css',
			[ 'wp-components' ],
			$components_asset['version'] ?? '1.0'
		);

		\wp_register_script(
			'totaltheme-components',
			\WPEX_THEME_URI . '/build/components.js',
			$components_asset['dependencies'] ?? [ 'wp-components' ],
			$components_asset['version'] ?? '1.0'
		);

		\wp_localize_script(
			'totaltheme-components',
			'totaltheme_components_params',
			self::get_components_l10n()
		);

		// Theme Panel.
		\wp_register_style(
			'totaltheme-admin-panel',
			\totaltheme_get_css_file( 'admin/panel' ),
			[],
			\WPEX_THEME_VERSION
		);

		\wp_register_style(
			'totaltheme-admin-bar',
			\totaltheme_get_css_file( 'admin/bar' ),
			[],
			\WPEX_THEME_VERSION
		);

		\wp_register_style(
			'totaltheme-admin-pages',
			\totaltheme_get_css_file( 'admin/pages' ),
			[],
			\WPEX_THEME_VERSION
		);

		\wp_register_script(
			'totaltheme-admin-panel',
			\totaltheme_get_js_file( 'admin/panel' ),
			[ 'jquery' ],
			\WPEX_THEME_VERSION,
			true
		);

		\wp_register_script(
			'totaltheme-admin-pages',
			\totaltheme_get_js_file( 'admin/pages' ),
			[ 'jquery' ],
			\WPEX_THEME_VERSION,
			true
		);

	}

	/**
	 * Enqueue theme icons.
	 */
	public function enqueue_ticons() {
		\_deprecated_function( __METHOD__, 'Total Theme 6.0' );
	}

	/**
	 * Returns locale for total components.
	 */
	private static function get_components_l10n(): array {
		$l10n = [
			'global_colors'        => \totaltheme_call_static( 'Color_Palette', 'get_color_component_list' ),
			'refresh_colors_nonce' => \wp_create_nonce( 'totaltheme_color_palette_refresh' )
		];
		if ( \class_exists( '\WPEX_Color_Palette' ) ) {
			$l10n['customColorsAdminUrl'] = \esc_url( \admin_url( 'edit.php?post_type=wpex_color_palette' ) );
		}
		return $l10n;
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
