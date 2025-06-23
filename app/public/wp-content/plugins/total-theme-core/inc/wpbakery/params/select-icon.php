<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * Select Icon WPBakery Parameter.
 */
final class Select_Icon {

	/**
	 * Callback function for the param output.
	 */
	public static function output( $settings, $value = '' ) {
		if ( ! \function_exists( 'totaltheme_call_static' ) ) {
			return '';
		}
		if ( isset( $settings['std'] ) && ! $value ) {
			$value = $settings['std'];
		}
		$choices = $settings['choices'] ?? [];
		if ( $choices && is_callable( $choices ) ) {
			$choices = (array) call_user_func( $choices );
		}
		\ob_start();
			\totaltheme_call_static( 'Helpers\Icon_Select', 'render_form', [
				'selected'    => \str_replace( 'fa fa-', '', (string) $value ), // Incase it's a very old site where it was using fa fa-
				'choices'     => \array_values( $choices ),
				'input_name'  => $settings['param_name'],
				'input_class' => 'vcex-hidden-input wpb-input wpb_vc_param_value ' . $settings['param_name'] . ' ' . $settings['type'] . '_field',
			], false );
		return \ob_get_clean();
	}

}
