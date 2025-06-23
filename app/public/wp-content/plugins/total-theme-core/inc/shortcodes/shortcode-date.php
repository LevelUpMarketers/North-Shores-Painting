<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Date {

	public function __construct() {
		if ( ! shortcode_exists( 'date' ) ) {
			add_shortcode( 'date', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		$atts = shortcode_atts( [
			'id'     => null,
			'format' => 'F j, Y',
		], $atts );

		$id     = ! empty( $atts['id'] ) ? $atts['id'] : get_the_ID();
		$format = ! empty( $atts['format'] ) ? $atts['format'] : get_option( 'date_format' );

		return esc_html( get_the_date( $format, $id ) );
	}

}
