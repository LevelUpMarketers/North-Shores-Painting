<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * Gravity Forms Integration.
 */
final class Gravity_Forms {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Gravity_Forms.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		if ( \wpex_is_request( 'frontend' ) && \apply_filters( 'wpex_gravity_forms_css', true ) ) {
			\add_action( 'wp_enqueue_scripts', [ $this, 'gravity_forms_css' ], 40 );
		}
	}

	/**
	 * Loads Gravity Forms stylesheet.
	 */
	public function gravity_forms_css() {
		global $post;

		if ( \is_a( $post, 'WP_Post' ) && \has_shortcode( $post->post_content, 'gravityform' ) ) {
			\wp_enqueue_style(
				'wpex-gravity-forms',
				\totaltheme_get_css_file( 'frontend/gravity-forms' ),
				[],
				\WPEX_THEME_VERSION
			);
		}
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
