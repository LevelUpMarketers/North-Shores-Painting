<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Color Picker.
 */
final class Colorpicker {

	/**
	 * Param callback.
	 */
	public static function output( $settings, $value ) {
		if ( ! \function_exists( 'totaltheme_component' ) ) {
			return \function_exists( 'vc_textfield_form_field' ) ? \vc_textfield_form_field( $settings, $value ) : '';
		}
		ob_start();
			totaltheme_component( 'color', [
				'allow_global' => $settings['allow_global'] ?? true,
				'value'        => $value,
				'default'      => $settings['std'] ?? $settings['default'] ?? '',
				'exclude'      => $settings['exclude'] ?? '',
				'input_name'   => $settings['param_name'],
				'input_class'  => \esc_attr( "wpb_vc_param_value {$settings['param_name']} {$settings['type']}_field" ),
			] );
		return ob_get_clean();
	}

}
