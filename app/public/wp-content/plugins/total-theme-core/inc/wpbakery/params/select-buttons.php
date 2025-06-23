<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Select Buttons.
 */
final class Select_Buttons {

	public static function output( $settings, $value ) {
		$default_value = ! empty( $settings['std'] ) ? $settings['std'] : '';
		$wrap_classes = [
			'vcex-select-buttons-param',
			'vcex-custom-select',
			'vcex-noselect',
		];

		$choices = [];

		$choices = $settings['choices'] ?? $settings['param_name'] ?? false;

		if ( $choices && class_exists( '\TotalThemeCore\Vcex\Setting_Choices' ) ) {
			$choices = (new \TotalThemeCore\Vcex\Setting_Choices( $choices ))->get_choices();
		}

		if ( ! is_array( $choices ) ) {
			return '<input type="text" class="wpb_vc_param_value '
				. \esc_attr( $settings['param_name'] ) . ' '
				. \esc_attr( $settings['type'] ) . '" name="' . \esc_attr( $settings['param_name'] ) . '" value="' . \esc_attr( $value ) . '">';
		}

		$output = '<div class="' . \esc_attr( implode( ' ', $wrap_classes ) ) . '">';

		if ( ! $value ) {
			if ( $default_value ) {
				$value = $default_value;
			} else {
				$temp_choices = $choices;
				reset( $temp_choices );
				$value = key( $temp_choices );
			}
		}

		foreach ( $choices as $id => $label ) {
			if ( $default_value && '' === $id ) {
				continue; // remove the "Default" option when the param has a defined default.
			}
			$choice_class = [ 'vcex-opt' ];
			if ( $id == $value ) {
				$choice_class[] = 'vcex-active';
			}
			if ( $id ) {
				$choice_class[] = 'vcex-opt-' . sanitize_html_class( $id );
			}
			if ( ! \defined( '\TOTAL_THEME_ACTIVE' ) ) {
				$label = str_replace( 'ticon', 'fa', $label );
			}
			$output .= '<button type="button" class="' . \esc_attr( implode( ' ', $choice_class ) ) . '" data-value="' . \esc_attr( $id )  . '">' . wp_kses_post( $label ) . '</button>';
		}

		$output .= '<input name="' . \esc_attr( $settings['param_name'] ) . '" class="vcex-hidden-input wpb-input wpb_vc_param_value ' . \esc_attr( $settings['param_name'] ) . ' ' . \esc_attr( $settings['type'] ) . '_field" type="hidden" value="' . \esc_attr( $value ) . '">';

		$output .= '</div>';

		return $output;
	}

}
