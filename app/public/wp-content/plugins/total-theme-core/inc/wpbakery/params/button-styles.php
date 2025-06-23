<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Button Styles.
 */
final class Button_Styles {

	public static function output( $settings, $value ) {

		if ( \function_exists( '\wpex_button_styles' ) ) {

			$output = '<select name="'
				. \esc_attr( $settings['param_name'] )
				. '" class="wpb_vc_param_value wpb-input wpb-select '
				. \esc_attr( $settings['param_name'] )
				. ' ' . \esc_attr( $settings['type'] ) .'">';

			$excluded = $settings['exclude_choices'] ?? array();

			$options = \wpex_button_styles();

			foreach ( $options as $key => $name ) {
				if ( \in_array( $key, $excluded ) ) {
					continue;
				}
				$output .= '<option value="'. \esc_attr( $key )  .'" '. \selected( $value, $key, false ) .'>'. \esc_attr( $name ) .'</option>';
			}

			$output .= '</select>';

		} else {
			$output = \vcex_total_exclusive_notice();
			$output .= '<input type="hidden" class="wpb_vc_param_value '
					. \esc_attr( $settings['param_name'] ) . ' '
					. \esc_attr( $settings['type'] ) . '" name="' . \esc_attr( $settings['param_name'] ) . '" value="' . \esc_attr( $value ) . '">';
		}

		return $output;

	}

}
