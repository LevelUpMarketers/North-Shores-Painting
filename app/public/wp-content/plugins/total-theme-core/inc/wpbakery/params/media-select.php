<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Media_Select.
 *
 * @deprecated v6.0
 */
final class Media_Select {

	public static function output( $settings, $value ) {
		$media_type = $settings['media_type'] ?? 'image';
		$return_val = $settings['return_val'] ?? 'url';
		$output = '<div class="vcex-param-media-select" data-media-type="' . \esc_attr( $media_type ) . '"  data-return-val="' . \esc_attr( $return_val ) . '"><input';
			$output .= ' name="'. \esc_attr( $settings['param_name'] ) .'"';
			$output .= ' class="wpb_vc_param_value wpb-textinput ' . \esc_attr( $settings['param_name'] ) . ' ' . \esc_attr( $settings['type'] ) . '"';
			$output .= ' type="text"';
			$output .= ' value="' . \esc_attr( $value ) . '"';
		$output .= '>';
		$output .= '<button type="button" class="vcex-param-media-select__button button button-secondary">' . esc_html__( 'Select', 'total-theme-core' ) . '</button></div>';
		return $output;
	}

}
