<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Number.
 */
final class Number {
	public static function output( $settings, $value ) {
		$value = $value ? \floatval( $value ) : '';
		$min   = isset( $settings['min'] ) ? \floatval( $settings['min'] ) : '1';
		$max   = isset( $settings['max'] ) ? \floatval( $settings['max'] ) : '100';
		$step  = isset( $settings['step'] ) ? \floatval( $settings['step'] ) : '1';
		$output = '<input';
			$output .= ' name="'. \esc_attr( $settings['param_name'] ) .'"';
			$output .= ' class="wpb_vc_param_value wpb-input wpb-select ' . \esc_attr( $settings['param_name'] ) . ' ' . \esc_attr( $settings['type'] ) . '_field vcex-number-param"';
			$output .= 'type="number"';
			$output .= 'value="' . \esc_attr( $value ) . '"';
			$output .= 'min="' . \esc_attr( $min ). '"';
			$output .= 'max="' . \esc_attr( $max ) . '"';
			$output .= 'step="' . \esc_attr( $step ) . '"';
			if ( $min && $max ) {
				$output .= 'placeholder="' . \esc_html__( 'range', 'total-theme-core' ) . ' ' . \esc_attr( $min ) . '&dash;' . \esc_attr( $max ) . '"';
			}
		$output .= '>';
		return $output;
	}
}
