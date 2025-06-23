<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

class Deprecated_CSS_Params_Style {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Generate CSS for deprecated params.
	 */
	public static function generate_css( $atts = [], $return = 'temp_class' ) {
		if ( empty( $atts ) || ! \is_array( $atts ) ) {
			return '';
		}

		$css = '';
		$important = 'temp_class' === $return ? ' !important' : '';

		// Margin and Padding.
		$dirs = [
			'top',
			'bottom',
			'right',
			'left',
		];

		foreach ( $dirs as $dir ) {
			if ( ! empty( $atts["margin_{$dir}"] ) && $margin_safe = \wpex_sanitize_data( $atts["margin_{$dir}"], 'px-pct' ) ) {
				$css .= "margin-{$dir}: {$margin_safe}{$important};";
			}
			if ( ! empty( $atts["padding_{$dir}"] ) && $padding_safe = \wpex_sanitize_data( $atts["padding_{$dir}"], 'px-pct' ) ) {
				$css .= "padding-{$dir}: {$padding_safe}{$important};";
			}
		}

		// Border.
		if ( ! empty( $atts['border_width'] )
			&& ! empty( $atts['border_color'] )
			&& $border_width_safe = \sanitize_text_field( $atts['border_width'] )
		) {
			$border_style_safe = ! empty( $atts['border_style'] ) ? \sanitize_text_field( $atts['border_style'] ) : 'solid';
			$css .= "border-style: {$border_style_safe}{$important};";
			$border_color_safe = \sanitize_text_field( $atts['border_color'] );
			$css .= "border-color: {$border_color_safe}{$important};";
			$bw_array = \explode( ' ', $border_width_safe );
			$bw_count = count( $bw_array );
			if ( 1 === $bw_count ) {
				$border_dir = [
					'top'    => $bw_array[0],
					'right'  => $bw_array[0],
					'bottom' => $bw_array[0],
					'left'   => $bw_array[0],
				];
			} elseif ( 2 === $bw_count ) {
				$border_dir = [
					'top'    => $bw_array[0],
					'right'  => $bw_array[1],
					'bottom' => $bw_array[0],
					'left'   => $bw_array[1],
				];
			} elseif ( 3 === $bw_count ) {
				$border_dir = [
					'top'    => $bw_array[0],
					'right'  => $bw_array[1],
					'bottom' => $bw_array[2],
					'left'   => $bw_array[1],
				];
			} else {
				$border_dir = [
					'top'    => $bw_array[0],
					'right'  => $bw_array[1] ?? '0px',
					'bottom' => $bw_array[2] ?? '0px',
					'left'   => $bw_array[3] ?? '0px',
				];
			}
			foreach ( $border_dir as $k => $v ) {
				$css .= "border-{$k}-width: {$v}{$important};";
			}
		}

		// Background image.
		if ( ! empty( $atts['bg_image'] ) ) {
			if ( \is_numeric( $atts['bg_image'] ) ) {
				$bg_image = \wp_get_attachment_url( $atts['bg_image'] );
				if ( 'temp_class' === $return && $bg_image ) {
					$bg_image = $bg_image . '?id='. $atts['bg_image'];
				}
			} else {
				$bg_image = \sanitize_text_field( $atts['bg_image'] );
			}
			if ( $bg_image && $bg_image_safe = \esc_url( $bg_image ) ) {
				$css .= "background-image: url({$bg_image_safe}){$important};";
				$bg_style = ! empty( $atts['bg_style'] ) ? $atts['bg_style'] : 'stretch';
				switch ( $bg_style ) {
					case 'stretch':
						$bg_position = 'center';
						$bg_repeat   = 'no-repeat';
						$bg_size     = 'cover';
						break;
					case 'fixed':
						$bg_position = '0 0';
						$bg_repeat   = 'no-repeat';
						break;
					case 'repeat':
						$bg_position = '0 0';
						$bg_repeat   = 'repeat';
						break;
				}
				if ( isset( $bg_position ) ) {
					$css .= "background-position: {$bg_position}{$important};";
				}
				if ( isset( $bg_repeat ) ) {
					$css .= "background-repeat: {$bg_repeat}{$important};";
				}
				if ( isset( $bg_size ) ) {
					$css .= "background-size: {$bg_size}{$important};";
				}
			}
		}

		// Background Color.
		if ( ! empty( $atts['bg_color'] ) && $bg_color_safe = \sanitize_text_field( $atts['bg_color'] ) ) {
			$css .= "background-color: {$bg_color_safe}{$important};";
		}

		// Return CSS.
		if ( $css ) {
			if ( 'temp_class' === $return ) {
				$uniqid = uniqid( '.vc_custom_' );
				return "{$uniqid}{{$css}}";
			} elseif ( 'inline_css' === $return ) {
				return $css;
			}
		}

		return '';
	}

}
