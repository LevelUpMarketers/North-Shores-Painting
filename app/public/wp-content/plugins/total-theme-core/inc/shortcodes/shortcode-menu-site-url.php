<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Menu_Site_Url {

	public function __construct() {
		if ( ! shortcode_exists( 'menu_site_url' ) ) {
			add_shortcode( 'menu_site_url', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		$url = get_site_url( null, '', 'http' );
		$url = str_replace( 'http://', '', $url );
		return $url;
	}

}
