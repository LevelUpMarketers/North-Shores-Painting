<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Text.
 */
final class Text {
	public static function output( $settings, $value ) {
		$input_type = $settings['input_type'] ?? 'text';
		$output = '<input';
			$output .= ' name="'. \esc_attr( $settings['param_name'] ) .'"';
			$output .= ' class="wpb_vc_param_value wpb-textinput ' . \esc_attr( $settings['param_name'] ) . ' ' . \esc_attr( $settings['type'] ) . '"';
			$output .= ' type="' . \esc_attr( $input_type ) . '"';
			$output .= ' value="' . \esc_attr( $value ) . '"';
			if ( isset( $settings['placeholder'] ) && '' !== $settings['placeholder'] ) {
				$output .= ' placeholder="' . \esc_attr( $settings['placeholder'] ) . '"';
			}
		$output .= '>';
		return $output;
	}
}
