<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Site_Name {

	public function __construct() {
		if ( ! shortcode_exists( 'site_name' ) ) {
			add_shortcode( 'site_name', [ self::class, 'output' ] );
		}
	}

	public static function output() {
		return esc_html( get_bloginfo( 'name' ) );
	}

}
