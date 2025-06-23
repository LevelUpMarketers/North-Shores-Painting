<?php

namespace TotalTheme\Integration;

defined( 'ABSPATH' ) || exit;

/**
 * Polylang Integration.
 */
final class Polylang {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Polylang.
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
		\add_action( 'init', [ $this, 'register_strings' ] );
		\add_filter( 'pll_get_post_types', [ $this, 'post_types' ] );

		if ( \wpex_is_request( 'admin' ) ) {
			\add_filter( 'wpex_shortcodes_tinymce_json', [ $this, 'tinymce_shortcode' ] );
		}
	}

	/**
	 * Registers theme_mod strings into Polylang.
	 */
	public function register_strings() {
		if ( \function_exists( 'pll_register_string' ) ) {
			$strings = \wpex_register_theme_mod_strings();
			if ( $strings ) {
				foreach ( $strings as $string => $default ) {
					\pll_register_string( $string, \get_theme_mod( $string, $default ), 'Theme Settings', true );
				}
			}
		}
	}

	/**
	 * Add shortcodes to the tiny MCE.
	 */
	public function tinymce_shortcode( $data ) {
		if ( \shortcode_exists( 'polylang_switcher' ) ) {
			$data['shortcodes']['polylang_switcher'] = [
				'text' => esc_html__( 'PolyLang Switcher', 'total' ),
				'insert' => '[polylang_switcher dropdown="false" show_flags="true" show_names="false"]',
			];
		}
		return $data;
	}

	/**
	 * Include Post Types.
	 */
	public function post_types( $types ) {
		$types['templatera']     = 'templatera';
		$types['wpex_card']      = 'wpex_card';
		$types['wpex_templates'] = 'wpex_templates';
		return $types;
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
