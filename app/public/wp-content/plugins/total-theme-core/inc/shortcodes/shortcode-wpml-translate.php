<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Wpml_Translate {

	public function __construct() {
		if ( ! shortcode_exists( 'wpml_translate' ) ) {
			add_shortcode( 'wpml_translate', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		if ( ! defined( 'ICL_LANGUAGE_CODE' ) ) {
			return;
		}

		extract( shortcode_atts( [
			'lang'	=> '',
		], $atts ) );

		if ( $lang == ICL_LANGUAGE_CODE ) {
			return do_shortcode( wp_kses_post( $content ) );
		}
	}

}
