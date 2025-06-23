<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Select Callback Function.
 */
final class Select_Callback_Function {

	public static function output( $settings, $value ) {
		if ( ! \is_string( $value ) || empty( $value ) ) {
			$value = '';
		}

		$output = '<select name="'
			. \esc_attr( $settings['param_name'] )
			. '" class="wpb_vc_param_value wpb-input wpb-select '
			. \esc_attr( $settings['param_name'] )
			. ' ' . \esc_attr( $settings['type'] ) . '">';

		$output .= '<option value="" ' . \selected( $value, '', false ) . '>' . \esc_html__( '— Select —', 'total-theme-core' ) . '</option>';

		$callbacks = [];

		if ( \defined( '\VCEX_CALLBACK_FUNCTION_WHITELIST' ) && \is_array( \VCEX_CALLBACK_FUNCTION_WHITELIST ) ) {
			$callbacks = \VCEX_CALLBACK_FUNCTION_WHITELIST;
		}

		foreach ( $callbacks as $callback ) {
			$callback = str_replace( '\\', '\\\\', $callback ); // replace single \ to double \\ to prevent issues with namespaces.
			$output .= '<option value="' . \esc_attr( $callback )  . '" ' . \selected( $value, $callback, false ) . '>' . \esc_html( $callback ) . '</option>';
		}

		if ( $value && ! \in_array( $value, $callbacks ) ) {
			$output .= '<option value="' . \esc_attr( $value )  . '" selected="selected">' . \esc_html( $value ) . '</option>';
		}

		$output .= '</select>';

		return $output;
	}

}
