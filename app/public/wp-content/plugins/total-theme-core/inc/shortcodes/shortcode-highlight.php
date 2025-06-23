<?php

namespace TotalThemeCore\Shortcodes;

\defined( 'ABSPATH' ) || exit;

final class Shortcode_Highlight {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		if ( ! \shortcode_exists( 'highlight' ) ) {
			\add_shortcode( 'highlight', [ self::class, 'output' ] );
		}
	}

	/**
	 * Renders the shortcode.
	 */
	public static function output( $atts, $content = '' ) {
		$content = $atts['text'] ?? $content;

		if ( ! $content ) {
			return;
		}

		$atts = \shortcode_atts( [
			'color'      => '',
			'text_color' => '',
			'height'     => '',
			'bottom'     => '',
			'opacity'    => '',
		], $atts, 'highlight' );

		$wrap_css = '';
		$inner_css = '';

		if ( \function_exists( '\vcex_inline_style' ) ) {
			$wrap_css = \vcex_inline_style( [
				'color' => $atts['text_color'] ?? '',
			], true );
			$inner_css = \vcex_inline_style( [
				'background' => $atts['color'] ?? '',
				'height'     => $atts['height'] ?? '',
				'bottom'     => $atts['bottom'] ?? '',
				'opacity'    => $atts['opacity'] ?? '',
			], true );
		}

		$html = '<span class="wpex-highlight"' . $wrap_css . '>';
			$html .= '<span class="wpex-highlight__bg wpex-bg-accent wpex-after"' . $inner_css . '></span>';

			if ( \function_exists( '\vcex_parse_text_safe' ) ) {
				$content_safe = \vcex_parse_text_safe( $content );
			} else {
				$content_safe = \wp_kses_post( $content );
			}

			$html .= '<span class="wpex-highlight__text wpex-relative">' . $content_safe . '</span>';

		$html .= '</span>';

		return $html;
	}

}
