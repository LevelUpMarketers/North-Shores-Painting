<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Contact Form 7 Select.
 */
final class Cf7_Select {

	public static function output( $settings, $value ) {
		$cf7 = \get_posts( [
			'post_type'   => 'wpcf7_contact_form',
			'numberposts' => '-1',
		] );
		if ( $cf7 ) {
			$output = '<select name="'
				. $settings['param_name']
				. '" class="wpb_vc_param_value wpb-input wpb-select '
				. $settings['param_name']
				. ' ' . $settings['type'] .'">';

			$output .= '<option value="" ' . \selected( $value, '', false ) . '>' . \esc_html__( '— Select —', 'total-theme-core' ) . '</option>';
			foreach ( $cf7 as $cform ) {
				$output .= '<option value="' . \esc_attr( $cform->ID )  . '" ' . \selected( $value, $cform->ID, false ) . '>' . \esc_attr( $cform->post_title ) . '</option>';
			}
			$output .= '</select>';
			return $output;
		}
	}

}
