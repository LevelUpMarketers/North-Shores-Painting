<?php

namespace TotalThemeCore\Shortcodes;

\defined( 'ABSPATH' ) || exit;

final class Shortcode_Underline {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		if ( ! \shortcode_exists( 'underline' ) ) {
			\add_shortcode( 'underline', [ self::class, 'output' ] );
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
			'color'    => '',
			'size'     => '',
			'offset'   => '',
			'style'    => '',
			'skip_ink' => '',
		], $atts, 'highlight' );

		$style = $atts['style'] ?? 'default';
		$wrap_css = '';

		$class = 'wpex-underline-shortcode wpex-underline';

		if ( \function_exists( '\vcex_inline_style' ) && ! empty( $atts['color'] ) && 'accent' !== $atts['color'] ) {
			$wrap_css = \vcex_inline_style( [
				'text_decoration_color' => $atts['color'] ?? '',
			], true );
		}

		if ( ! empty( $atts['color'] ) && 'accent' === $atts['color'] ) {
			$class .= ' wpex-decoration-accent';
		}

		$size_safe = ! empty( $atts['size'] ) ? \absint( $atts['size'] ) : '4';
		$class .= " wpex-decoration-{$size_safe}";

		if ( ! empty( $atts['style'] ) ) {
			$style_safe = \sanitize_html_class( $atts['style'] );
			$class .= " wpex-decoration-{$style_safe}";
		}

		if ( ! empty( $atts['offset'] ) ) {
			$offset_safe = \absint( $atts['offset'] );
			$class .= " wpex-underline-offset-{$offset_safe}";
		}

		if ( ! empty( $atts['skip_ink'] ) && 'none' === $atts['skip_ink'] ) {
			$skip_ink_safe = \sanitize_html_class( $atts['skip_ink'] );
			$class .= " wpex-decoration-skip-ink-{$skip_ink_safe}";
		}

		$html = '<span class="' . esc_attr( $class ) . '"' . $wrap_css . '>';

			if ( \function_exists( '\vcex_parse_text_safe' ) ) {
				$html .= \vcex_parse_text_safe( $content );
			} else {
				$html .= \wp_kses_post( $content );
			}

		$html .= '</span>';

		return $html;
	}

}
