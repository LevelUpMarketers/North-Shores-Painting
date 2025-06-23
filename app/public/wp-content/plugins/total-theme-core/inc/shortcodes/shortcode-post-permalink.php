<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Post_Permalink {

	public function __construct() {
		if ( ! shortcode_exists( 'post_permalink' ) ) {
			add_shortcode( 'post_permalink', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		return esc_url( get_permalink() );
	}

}
