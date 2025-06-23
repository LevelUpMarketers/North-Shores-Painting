<?php

namespace TotalTheme\Integration;

\defined( 'ABSPATH' ) || exit;

/**
 * WPML Integration.
 */
class WPML {

	/**
	 * Init.
	 */
	public static function init() {
		\add_filter( 'upload_dir', [ self::class, 'upload_dir' ] );
		if ( \is_admin( 'admin' ) ) {
			\add_action( 'admin_init', [ self::class, 'register_strings' ] );
			\add_filter( 'wpex_shortcodes_tinymce_json', [ self::class, 'tinymce_shortcode' ] );
		} else {
			\add_filter( 'body_class', [ self::class, 'body_class' ] );
		}
	}

	/**
	 * Registers theme_mod strings into WPML.
	 */
	public static function register_strings() {
		if ( \function_exists( 'icl_register_string' ) && $strings = \wpex_register_theme_mod_strings() ) {
			foreach ( $strings as $string => $default ) {
				\icl_register_string( 'Theme Settings', $string, \get_theme_mod( $string, $default ) );
			}
		}
	}

	/**
	 * Adds wpml-language-{lang} class to the body tag.
	 */
	public static function body_class( $classes ) {
		if ( $current_language = (string) \apply_filters( 'wpml_current_language', null ) ) {
			$classes[] = 'wpml-language-' . \sanitize_html_class( $current_language );
		}
		return $classes;
	}

	/**
	 * Fix for when users have the Language URL Option on "different domains"
	 * which causes cropped images to fail.
	 *
	 * @todo is this still needed?
	 */
	public static function upload_dir( $upload ) {
		if ( 2 === \apply_filters( 'wpml_setting', false, 'language_negotiation_type' ) ) {
			$upload['baseurl'] = \apply_filters( 'wpml_permalink', $upload['baseurl'] );
		}
		return $upload;
	}

	/**
	 * Add shortcodes to the tiny MCE.
	 */
	public static function tinymce_shortcode( $data ) {
		if ( \shortcode_exists( 'wpml_translate' ) ) {
			$data['shortcodes']['wpml_lang_selector'] = [
				'text'   => \esc_html__( 'WPML Switcher', 'total' ),
				'insert' => '[wpml_lang_selector]',
			];
		}
		return $data;
	}

}
