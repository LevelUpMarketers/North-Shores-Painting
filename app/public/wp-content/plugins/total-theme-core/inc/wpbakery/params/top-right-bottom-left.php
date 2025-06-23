<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Top_Right_Bottom_Left.
 */
final class Top_Right_Bottom_Left {

	public static function output( $settings, $value ) {
		$defaults = [
			'top'    => '',
			'right'  => '',
			'bottom' => '',
			'left'   => '',
		];

		// Convert none multi_attribute to multi_attribute
		if ( $value && ! str_contains( $value, ':' ) ) {
			$array = \explode( ' ', $value );
			$count = \count( $array );
			if ( $array ) {
				if ( 1 == $count ) {
					$field_values = [
						'top'    => $array[0],
						'right'  => $array[0],
						'bottom' => $array[0],
						'left'   => $array[0],
					];
				} elseif ( 2 == $count ) {
					$field_values = [
						'top'    => $array[0] ?? '',
						'right'  => $array[1] ?? '',
						'bottom' => $array[0] ?? '',
						'left'   => $array[1] ?? '',
					];
				} else {
					$field_values = [
						'top'    => $array[0] ?? '',
						'right'  => $array[1] ?? '',
						'bottom' => $array[2] ?? '',
						'left'   => $array[3] ?? '',
					];
				}
			}
		} else {
			$field_values = \vcex_parse_multi_attribute( $value, $defaults );
		}

		$output = '<div class="vcex-param-trbl">';
			foreach ( $field_values as $k => $v ) {
				$field_name = \esc_attr( $settings['param_name'] . '[' . $k . ']' );
				$icon = $k;
				switch ( $icon ) {
					case 'top':
						$icon = 'up';
						$label = \esc_html( 'Top', 'total-theme-core' );
						break;
					case 'bottom':
						$icon = 'down';
						$label = \esc_html( 'Bottom', 'total-theme-core' );
						break;
					case 'left':
						$label = \esc_html( 'Left', 'total-theme-core' );
						break;
					case 'right':
						$label = \esc_html( 'Right', 'total-theme-core' );
						break;
				}
				$output .= '<div class="vcex-param-trbl__item">';
					$output .= '<div class="vcex-param-trbl__icon">';
						$output .= '<span class="dashicons dashicons-arrow-' . \esc_attr( $icon ) . '-alt"></span>';
						$output .= '</div>';
					$output .= '<label for="' . \esc_attr( $field_name ) .'" class="screen-reader-text">' . $label . '</label>';
					$output .= '<input id="' . \esc_attr( $field_name ) .'" data-vcex-position="' . \esc_attr( $k ) . '" value="' . \esc_attr( $v ) . '" type="text" placeholder="-">';
				$output .= '</div>';
			}
		$output .= '</div>';
		$output .= '<input name="' . \esc_attr( $settings['param_name'] ) . '" class="vcex-hidden-input wpb-input wpb_vc_param_value  ' . \esc_attr( $settings['param_name'] ) . ' ' . \esc_attr( $settings['type'] ) . '_field" type="hidden" value="' . \esc_attr( $value ) . '">';
		return $output;

	}

}
