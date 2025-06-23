<?php

namespace TotalTheme\Integration;

defined( 'ABSPATH' ) || exit;

/**
 * Revslider Integration.
 */
final class Revslider {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Revslider.
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
		\add_filter( 'revslider_meta_generator', '__return_false' );

		if ( \wpex_is_request( 'admin' ) ) {
			$this->admin_actions();
		}
	}

	/**
	 * Check if license is valid.
	 */
	private function is_license_valid() {
		return \get_option( 'revslider-valid', 'false' );
	}

	/**
	 * Admin actions.
	 */
	public function admin_actions() {
		if ( 'false' === $this->is_license_valid() ) {
			\add_action( 'admin_notices', [ $this, 'remove_plugins_page_notices' ], PHP_INT_MAX );
		}
		\add_action( 'do_meta_boxes', [ $this, 'remove_metabox' ] );
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );
	}

	/**
	 * Remove Revolution Slider plugin notices.
	 */
	public function remove_plugins_page_notices() {
		$plugin_id = 'revslider/revslider.php';

		\remove_action(
			"after_plugin_row_{$plugin_id}",
			array( 'RevSliderAdmin', 'add_notice_wrap_pre' ),
			10,
			3
		);

		\remove_action(
			"after_plugin_row_{$plugin_id}",
			array( 'RevSliderAdmin', 'show_purchase_notice' ),
			10,
			3
		);

		\remove_action(
			"after_plugin_row_{$plugin_id}",
			array('RevSliderAdmin', 'add_notice_wrap_post' ),
			10,
			3
		);
	}

	/**
	 * Remove metabox where not needed.
	 */
	public function remove_metabox() {
		\remove_meta_box(
			'slider_revolution_metabox',
			[
				'vc_grid_item',
				'templatera',
				'wpex_sidebars',
				'wpex_font',
				'wpex_color_palette',
				'wpex_card',
				'ptu',
				'ptu_tax',
				'wpex_templates',
			],
			'side'
		);
	}

	/**
	 * Load CSS in the admin.
	 */
	public function enqueue_admin_styles() {
		if ( ! \array_key_exists( 'page', $_GET )
			|| 'revslider' !== \sanitize_text_field( \wp_unslash( $_GET['page'] ) )
		) {
			return;
		}
		\wp_enqueue_style(
			'totaltheme-admin-revslider',
			\totaltheme_get_css_file( 'admin/revslider' ),
			[],
			WPEX_THEME_VERSION
		);
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
