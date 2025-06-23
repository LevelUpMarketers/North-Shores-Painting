<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Scribble_Underline {
	
	/**
	 * Class constructor.
	 */
	public function __construct() {
		if ( ! shortcode_exists( 'scribble_underline' ) ) {
			add_shortcode( 'scribble_underline', [ self::class, 'output' ] );
		}
	}

	/**
	 * Shortcode output.
	 */
	public static function output( $atts, $content = '' ) {
		$atts = \shortcode_atts( [
			'text'       => '',
			'color'      => '',
			'text_color' => '',
			'opacity'    => '',
			'top'        => '',
			'style'      => 'one',
		], $atts, 'span' );

		$class = 'wpex-scribble-underline wpex-inline-block wpex-relative wpex-whitespace-nowrap';

		if ( ! empty( $atts['text'] ) ) {
			$content = $atts['text'];
		}

		$wrap_style = '';
		$underline_style = '';

		if ( ( ! empty( $atts['color'] ) || ! empty( $atts['text_color'] ) || ! empty( $atts['opacity'] ) || ! empty( $atts['top'] ) )
			&& class_exists( 'TotalThemeCore\Vcex\Inline_Style' )
		) {
			// Wrap style
			$wrap_style = \totalthemecore_init_class( 'Vcex\Inline_Style', [
				'color' => $atts['text_color'],
			], true )->return_style() ?? '';
			// Underline style
			$underline_style = \totalthemecore_init_class( 'Vcex\Inline_Style', [
				'color'   => $atts['color'],
				'opacity' => $atts['opacity'],
				'top'     => ( isset( $atts['top'] ) && is_numeric( $atts['top'] ) ) ? "{$atts['top']}%" : $atts['top'],
			], true )->return_style();
		}

		$content_safe = \function_exists( 'vcex_parse_text_safe' ) ? \vcex_parse_text_safe( $content ) : \wp_kses_post( $content );

		return '<span class="' . \esc_attr( $class ) . '"' . $wrap_style . '>' . self::get_svg( $atts['style'], $underline_style ) . '<span class="wpex-relative"> ' . $content_safe . '</span></span>';
	}

	/**
	 * Get SVG.
	 */
	protected static function get_svg( $style, $css ): string {
		$styles = [
			// Style one
			'one' => 'Coming Soon',
		];
		return isset( $styles[ $style ] ) ? str_replace( '<svg', '<svg' . $css, $styles[ $style ] ) : '';
	}

}
