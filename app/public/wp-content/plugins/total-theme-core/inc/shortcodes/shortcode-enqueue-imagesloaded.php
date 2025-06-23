<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Enqueue_Imagesloaded {

	public function __construct() {
		if ( ! shortcode_exists( 'enqueue_imagesloaded' ) ) {
			add_shortcode( 'enqueue_imagesloaded', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		wp_enqueue_script( 'imagesloaded' );
	}

}
