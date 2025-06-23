<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Staff_Social {

	public function __construct() {
		if ( ! shortcode_exists( 'staff_social' ) ) {
			add_shortcode( 'staff_social', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts = [] ) {
		if ( function_exists( 'wpex_get_staff_social' ) ) {
			return wpex_get_staff_social( $atts );
		}
	}

}
