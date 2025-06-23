<?php

namespace TotalThemeCore\Shortcodes;

use WP_User;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Username {

	public function __construct() {
		if ( ! shortcode_exists( 'username' ) ) {
			add_shortcode( 'username', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		$current_user = wp_get_current_user();

		if ( ! ( $current_user instanceof WP_User ) ) {
			return;
		}

		return esc_html( $current_user->display_name );
	}

}
