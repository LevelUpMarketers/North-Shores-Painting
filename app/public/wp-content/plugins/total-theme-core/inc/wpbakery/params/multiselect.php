<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => MultiSelect.
 */
class MultiSelect {

	/**
	 * Renders the custom param field.
	 */
	public static function output( $settings, $value ) {
		$choices      = $settings['choices'] ?? $settings['param_name'] ?? [];
		$value_exists = false;

		if ( '' === $value && isset( $settings['std'] ) ) {
			$value = $settings['std'];
		}

		if ( \is_string( $value ) ) {
			$value = explode( ',', $value );
		}

		if ( is_string( $choices ) && class_exists( '\TotalThemeCore\Vcex\Setting_Choices' ) ) {
			$choices = (new \TotalThemeCore\Vcex\Setting_Choices( $choices, $settings ))->get_choices();
		}

		// If we don't have any choices return a text field with the value.
		// This is important to prevent issues where the choices is empty but we had a value saved.
		if ( ! $choices ) {
			$output = '<input';
				$output .= ' name="'. \esc_attr( $settings['param_name'] ) .'"';
				$output .= ' class="wpb_vc_param_value wpb-textinput ' . \esc_attr( $settings['param_name'] ) . ' ' . \esc_attr( $settings['type'] ) . '"';
				$output .= ' type="text"';
				$output .= ' value="' . \esc_attr( $value ) . '"';
			$output .= '>';
			return $output;
		}

		// If value isn't part of the choices we add it anyway so it can be saved.
		if ( isset( $value[0] ) && ! \array_key_exists( $value[0], $choices ) ) {
			$choices[ $value[0] ] = $value[0];
		}

		$output = '<div class="vcex-param-multiselect">';
			foreach ( $choices as $key => $label ) {
				if ( '' === $key ) {
					continue; // don't show empty select.
				}
				$checked = \in_array( $key, $value ) ? ' checked="checked"' : '';
				$output .= '<label class="vc_checkbox-label"><input id="' . \esc_attr( $settings['param_name'] ) . '-' . \esc_attr( $key ) . '" value="' . \esc_attr( $key ) . '" class="wpb_vc_param_value ' . \esc_attr( $settings['param_name'] ) . ' ' . \esc_attr(  $settings['type'] ) . '" type="checkbox" name="' . \esc_attr( $settings['param_name'] ) . '" ' . $checked . '> ' . \esc_attr( $label ) . '</label>';
			}
		$output .= '</div>';

		return $output;
	}

}
