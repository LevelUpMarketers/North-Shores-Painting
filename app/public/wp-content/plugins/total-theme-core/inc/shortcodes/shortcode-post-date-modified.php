<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Post_Date_Modified {

	public function __construct() {
		if ( ! shortcode_exists( 'post_modified_date' ) ) {
			add_shortcode( 'post_modified_date', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		return esc_html( get_the_modified_date() );
	}

}
