<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Notice.
 */
final class Notice {

	public static function output( $settings, $value ) {
		return '<div style="color: #9d8967;border: 1px solid #ffeccc;background-color:#fff4e2;padding:1em;">' . \esc_html( $settings['text'] ) . '<input class="wpb_vc_param_value" type="hidden" name="' . \esc_attr( $settings['param_name'] ) . '" value=""></div>';
	}

}
