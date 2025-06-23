<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Post_Date {

	public function __construct() {
		if ( ! shortcode_exists( 'post_publish_date' ) ) {
			add_shortcode( 'post_publish_date', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		return esc_html( get_the_date() );
	}

}
