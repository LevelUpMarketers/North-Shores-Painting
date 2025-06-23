<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Enqueue_Lightbox {

	public function __construct() {
		if ( ! shortcode_exists( 'wpex_lightbox_scripts' ) ) {
			add_shortcode( 'wpex_lightbox_scripts', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		if ( function_exists( 'wpex_enqueue_lightbox_scripts' ) ) {
			wpex_enqueue_lightbox_scripts();
		}
	}

}
