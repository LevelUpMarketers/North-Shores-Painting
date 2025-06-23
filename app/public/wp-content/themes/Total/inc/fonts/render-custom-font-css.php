<?php

namespace TotalTheme\Fonts;

\defined( 'ABSPATH' ) || exit;

/**
 * Renders CSS for custom font.
 */
class Render_Custom_Font_CSS {

	/**
	 * The font name.
	 */
	protected $font = '';

	/**
	 * Font arguments.
	 */
	protected $args = '';

	/**
	 * CSS output.
	 */
	protected $css  = '';

	/**
	 * Render the Custom CSS.
	 */
	public function render( $font, $args ) {
		if ( empty( $args['custom_fonts'] ) || ! \is_array( $args['custom_fonts'] ) ) {
			return '';
		}

		$this->font = $font;
		$this->args = $args;

		$custom_fonts = $this->args['custom_fonts'];

		foreach ( $custom_fonts as $custom_font ) {
			$this->render_variation( $custom_font );
		}

		return $this->css;
	}

	/**
	 * Font css variation
	 */
	private function render_variation( $custom_font ) {
		$css = '';

		$urls = [];

		if ( ! empty( $custom_font[ 'woff2' ] ) ) {
			$woff2_escaped = \set_url_scheme( \esc_url( $custom_font[ 'woff2' ] ) );
			$urls[] = 'url(' . $woff2_escaped . ") format('woff2')";
		}

		if ( ! empty( $custom_font[ 'woff' ] ) ) {
			$woff_escaped = \set_url_scheme( \esc_url( $custom_font[ 'woff' ] ) );
			$urls[] = 'url(' . $woff_escaped . ") format('woff')";
		}

		if ( ! empty( $custom_font[ 'ttf' ] ) ) {
			$ttf_escaped = \set_url_scheme( \esc_url( $custom_font[ 'ttf' ] ) );
			$urls[] = 'url(' . $ttf_escaped . ") format('truetype')";
		}

		if ( $urls ) {
			$css  .= '@font-face {';
				$css .= 'font-family:\'' . $this->sanitize_font_name( $this->font ) . '\';';
				$css .= 'src:' . \implode( ', ', $urls ) . ';';
				if ( ! empty( $custom_font['weight'] ) ) {
					$css .= 'font-weight:' . \esc_attr( $custom_font['weight'] ) . ';';
				}
				if ( ! empty( $custom_font['style'] ) ) {
					$css .= 'font-style:' . \esc_attr( $custom_font['style'] ) . ';';
				}
				if ( ! empty( $this->args['display'] ) ) {
					$css .= 'font-display:' . \esc_attr( $this->args['display'] ) . ';';
				}
			$css .= '}';
		}

		if ( $css ) {
			$this->css .= $css;
		}
	}

	/**
	 * Sanitize font name.
	 */
	private function sanitize_font_name( $font ) {
		return \esc_attr( $font );
	}

}
