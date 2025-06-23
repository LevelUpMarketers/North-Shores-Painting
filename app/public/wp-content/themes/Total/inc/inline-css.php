<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Adds custom CSS to the site from Customizer settings.
 */
final class Inline_CSS {

	/**
	 * Static only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		// Add custom CSS to head tag.
		\add_action( 'wp_head', [ self::class, 'on_wp_head' ], 9999 );

		// Minify custom CSS on front-end only.
		// Note: Can't minify on backend or messes up the Custom CSS panel.
		if ( ! \is_admin() && ! \is_customize_preview() && \apply_filters( 'wpex_minify_inline_css', true ) ) {
			\add_filter( 'wp_get_custom_css', 'wpex_minify_css' );
		}
	}

	/**
	 * Ouput all inline CSS into the WP Header.
	 */
	public static function on_wp_head(): void {
		$css = '';

		/**
		 * Hook: totaltheme/inline_css
		 *
		 * @todo Update all functions that hook into "wpex_head_css" filter to instead hook into here.
		 */
		if ( \has_action( 'totaltheme/inline_css' ) ) {
			\ob_start();
				\do_action( 'totaltheme/inline_css' );
			$css_action = \ob_get_clean();
			if ( $css_action && \is_string( $css_action ) ) {
				$css .= $css . \trim( $css_action );
			}
		}

		$css = (string) \apply_filters( 'wpex_head_css', $css );

		// Custom CSS panel => Add last after all filters to make sure it always overrides.
		// Deprecated in 4.0 - the theme now uses native WP additional css function for the custom css.
		if ( $custom_css = (string) \get_theme_mod( 'custom_css', null ) ) {
			$css .= "/*CUSTOM CSS*/{$custom_css}";
		}

		// Minify and output CSS in the wp_head.
		if ( ! empty( $css ) ) {

			// Sanitize output | important don't use esc_attr because it breaks quotes in custom fonts.
			$css = (string) \wp_strip_all_tags( \wpex_minify_css( $css ) );

			// Echo output.
			// Don't rename #wpex-css or things will break !!! Important !!!
			if ( $css ) {
				echo '<style data-type="wpex-css" id="wpex-css">' . \trim( $css ) . '</style>';
			}

		}

	}

}
