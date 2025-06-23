<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Wpml_Language_Selector {

	public function __construct() {
		if ( ! shortcode_exists( 'wpml_lang_selector' ) ) {
			add_shortcode( 'wpml_lang_selector', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		ob_start();
			do_action( 'icl_language_selector' );
		return ob_get_clean();
	}

}
