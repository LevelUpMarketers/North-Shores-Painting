<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Current_Year {

	public function __construct() {

		if ( ! shortcode_exists( 'current_year' ) ) {
			add_shortcode( 'current_year', [ self::class, 'output' ] );
		}

	}

	public static function output( $atts, $content = '' ) {
		return date( 'Y' );
	}

}
