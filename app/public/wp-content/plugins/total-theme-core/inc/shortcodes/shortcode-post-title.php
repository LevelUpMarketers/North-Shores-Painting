<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Post_Title {

	public function __construct() {
		if ( ! shortcode_exists( 'post_title' ) ) {
			add_shortcode( 'post_title', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		return esc_html( get_the_title() );
	}

}
