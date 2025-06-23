<?php

namespace TotalTheme\Fonts;

\defined( 'ABSPATH' ) || exit;

/**
 * Global_Fonts Class.
 * 
 * Loads site-wide fonts and adds CSS font font targeting.
 */
final class Global_Fonts {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Global_Fonts.
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
		// Load any global adobe/google fonts globally.
		\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_registered_fonts' ] );

		// Load all custom fonts globally.
		\add_filter( 'wpex_head_css', [ $this, 'add_custom_fonts_css' ] );
		\add_filter( 'wpex_head_css', [ $this, 'assign_registered_fonts' ] );
	}

	/**
	 * Load globally registered custom fonts.
	 */
	public function enqueue_registered_fonts(): void {
		foreach ( \wpex_get_registered_fonts() as $font => $args ) {
			$type = $args['type'] ?? '';
			if ( ! \in_array( $type, [ 'google', 'adobe' ], true ) ) {
				continue;
			}
			if ( ! empty( $args['is_global'] ) || ! empty( $args['assign_to'] ) ) {
				\wpex_enqueue_font( $font, 'registered', $args );
			}
		}
	}

	/**
	 * Adds Header CSS for custom uploaded fonts.
	 */
	public function add_custom_fonts_css( $css ) {
		$font_css = '';
		foreach ( \wpex_get_registered_fonts() as $font => $args ) {
			if ( isset( $args['type'] )
				&& 'custom' === $args['type']
				&& ! empty( $args['custom_fonts'] )
				&& $custom_font_css = \wpex_render_custom_font_css( $font, $args )
			) {
				$font_css .= $custom_font_css;
			}
		}
		if ( $font_css ) {
			$css .= "/*CUSTOM FONTS*/{$font_css}";
		}
		return $css;
	}

	/**
	 * Assign registered fonts to their corresponding elements.
	 */
	public function assign_registered_fonts( $css ) {
		$registered_font_css = '';
		foreach ( \wpex_get_registered_fonts() as $font => $args ) {
			if ( ! empty( $args['assign_to'] ) ) {
				foreach ( $args['assign_to'] as $el ) {
					if ( $font_css_safe = \wpex_parse_css( $font, 'font-family', $el ) ) {
						$registered_font_css .= $font_css_safe;
					}
				}
			}
		}
		if ( $registered_font_css ) {
			$css .= "/*REGISTERED FONT ASSIGNEMENT*/{$registered_font_css}";
		}
		return $css;
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
