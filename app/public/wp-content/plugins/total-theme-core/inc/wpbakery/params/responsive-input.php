<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Responsive Input.
 */
final class Responsive_Input {

	public static function output( $settings, $value ) {
		if ( $value && ! \str_contains( $value, ':' ) ) {
			$ogvalue = $value;
			$value = "d:{$value}";
		}

		$medias = [
			'd'  => [
				'label' => \esc_html__( 'Desktop', 'total-theme-core' ),
				'icon'  => 'dashicons dashicons-desktop',
			],
			'tl' => [
				'label' => \esc_html__( 'Tablet Landscape', 'total-theme-core' ),
				'icon'  => 'dashicons dashicons-tablet',
			],
			'tp' => [
				'label' => \esc_html__( 'Tablet Portrait', 'total-theme-core' ),
				'icon'  => 'dashicons dashicons-tablet',
			],
			'pl' => [
				'label' => \esc_html__( 'Phone Landscape', 'total-theme-core' ),
				'icon'  => 'dashicons dashicons-smartphone',
			],
			'pp' => [
				'label' => \esc_html__( 'Phone Portrait', 'total-theme-core' ),
				'icon'  => 'dashicons dashicons-smartphone',
			],
		];

		$defaults = [];

		foreach ( $medias as $key => $val ) {
			$defaults[$key] = '';
		}

		if ( \function_exists( '\vcex_parse_multi_attribute' ) ) {
			$field_values = \vcex_parse_multi_attribute( $value, $defaults );
		} else {
			$field_values = [];
			$params_pairs = \explode( '|', $value );
			if ( ! empty( $params_pairs ) ) {
				foreach ( $params_pairs as $pair ) {
					$param = preg_split( '/\:/', $pair );
					if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
						if ( 'http' == $param[1] && isset( $param[2] ) ) {
							$param[1] = \rawurlencode( 'http:' . $param[2] ); // fix for incorrect urls that are not encoded
						}
						$field_values[ $param[0] ] = \rawurldecode( $param[1] );
					}
				}
			}
		}

		$output = '<div class="vcex-param-responsive-fields">';

		$count = 0;
		foreach ( $medias as $key => $val ) {
			$count++;

			$field_name = "{$settings['param_name']}[{$key}]";
			$icon_class = $val['icon'];

			$output .= '<div class="vcex-param-responsive-fields__item">';

				if ( 'pl' === $key || 'tl' === $key ) {
					$icon_class .= ' dashicons--flip';
				}

				$output .= '<div class="vcex-param-responsive-fields__icon"><span class="' . \esc_attr( $icon_class ) . '"></span></div>';

				$output .= '<label for="' . \esc_attr( $field_name ) .'" class="screen-reader-text">' . $medias[$key]['label'] . '</label>';

				$output .= '<input id="' . \esc_attr( $field_name ) .'" data-vcex-device="' . \esc_attr( $key ) . '" value="' . \esc_attr( $field_values[$key] ) . '" type="text" placeholder="-">';

			$output .= '</div>';

		}

		if ( ! empty( $ogvalue ) ) {
			$value = $ogvalue;
		}

		$output .= '<input name="' . \esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value  ' . \esc_attr( $settings['param_name'] ) . ' ' . \esc_attr( $settings['type'] ) . '_field" type="hidden" value="' . \esc_attr( $value ) . '">';

		$output .= '</div>';

		return $output;
	}

}
