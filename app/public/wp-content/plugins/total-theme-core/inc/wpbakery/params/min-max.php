<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Min Max
 */
final class Min_Max {

	public static function output( $settings, $value ) {
		if ( $value && \is_string( $value ) ) {
			$min_max = \explode( '|', $value );
		}
		$min = $min_max[0] ?? '';
		$min = $min ? \absint( $min ) . 'px' : '';
		$max = $min_max[1] ?? '';
		$max = $max ? \absint( $max ) . 'px' : '';
		$html = '<div class="vcex-param-min-max">';
			// Min
			$html .= '<div class="vcex-param-min-max__item">';
				$html .= '<label for="' . \esc_attr( $settings['param_name'] . '[min]' ) . '">' . \esc_html__( 'Min', 'total-theme-core' ) . '</label>';
				$html .= '<input type="text" id="' . \esc_attr( $settings['param_name'] . '[min]' ) . '" value="' . \esc_attr( $min ) . '">';
			$html .= '</div>';
			// Max
			$html .= '<div class="vcex-param-min-max__item">';
				$html .= '<label for="' . \esc_attr( $settings['param_name'] . '[max]' ) . '">' . \esc_html__( 'Max', 'total-theme-core' ) . '</label>';
				$html .= '<input type="text" id="' . \esc_attr( $settings['param_name'] . '[max]' ) . '" value="' . \esc_attr( $max ) . '">';
			$html .= '</div>';
		$html .= '</div>';

		// Hidden field.
		$html .= '<input name="' . \esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value  ' . \esc_attr( $settings['param_name'] ) . ' ' . \esc_attr( $settings['type'] ) . '_field" type="hidden" value="' . \esc_attr( $value ) . '">';

		return $html;
	}

}
