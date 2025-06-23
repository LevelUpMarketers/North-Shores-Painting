<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Cf_Value {

	public function __construct() {
		if ( ! shortcode_exists( 'cf_value' ) ) {
			add_shortcode( 'cf_value', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		$atts = shortcode_atts( [
			'name'        => '',
			'in_template' => false,
		], $atts );

		if ( empty( $atts['name'] ) ) {
			return;
		}

		$post_id = get_the_ID();

		if ( $post_id ) {
			return get_post_meta( $post_id, $atts['name'], true );
		}
	}

}
