<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * Contact Form 7 Integration.
 */
final class Contact_Form_7 {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Contact_Form_7.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Hook into actions and filters.
	 */
	private function __construct() {
		\add_action( 'wp_enqueue_scripts', [ $this, 'register_theme_css' ] );
		if ( \wpex_is_request( 'frontend' ) ) {
			\add_action( 'wpcf7_contact_form', [ $this, 'enqueue_theme_css' ] );
		}
	}

	/**
	 * Register custom theme css for Contact Form 7.
	 *
	 * @todo perhaps we should remove the theme styles now.
	 */
	public function register_theme_css() {
		\wp_register_style(
			'wpex-contact-form-7',
			\totaltheme_get_css_file( 'frontend/cf7' ),
			[ 'contact-form-7' ],
			\WPEX_THEME_VERSION
		);
	}

	/**
	 * Enqueues theme CSS for Contact Form 7.
	 */
	public function enqueue_theme_css() {
		\wp_enqueue_style( 'wpex-contact-form-7' );
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
