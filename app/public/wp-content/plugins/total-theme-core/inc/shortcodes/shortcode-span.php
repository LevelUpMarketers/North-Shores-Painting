<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Span {

	public function __construct() {
		if ( ! shortcode_exists( 'span' ) ) {
			add_shortcode( 'span', [ self::class, 'output' ] );
		}
	}

	public static function output( $atts, $content = '' ) {
		$atts = shortcode_atts( [
			'class'  => '',
			'text'   => '',
		], $atts, 'span' );

		$class = 'wpex-span';

		if ( $atts['class'] ) {
			$class .= ' ' . $atts['class'];
		}

		if ( ! empty( $atts['text'] ) ) {
			$content = $atts['text'];
		}

		$content_safe = function_exists( 'vcex_parse_text_safe' ) ? vcex_parse_text_safe( $content ) : wp_kses_post( $content );

		return '<span class="' . esc_attr( $class ) . '">' . $content_safe . '</span>';
	}

}
