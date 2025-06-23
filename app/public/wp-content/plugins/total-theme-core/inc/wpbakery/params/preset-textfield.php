<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Preset textfield.
 */
final class Preset_Textfield {

	/**
	 * Custom Param output.
	 */
	public static function output( $settings, $value ) {
		$is_preset = false;
		$choices   = $settings['choices'] ?? $settings['param_name'] ?? [];

		if ( class_exists( '\TotalThemeCore\Vcex\Setting_Choices' ) ) {
			$choices = (new \TotalThemeCore\Vcex\Setting_Choices( $choices, $settings ))->get_choices();
		}

		// update value based on gap.
		if ( 'gap' === $choices ) {
			if ( '0px' === $value ) {
				$value = 'none';
			} elseif ( \in_array( $value, $choices ) ) {
				$value = \absint( $value );
			}
		}

		if ( ! $is_preset && ( ! $value || \array_key_exists( $value, $choices ) ) ) {
			$is_preset = true;
		}

		$html = '<div class="vcex-param-preset-textfield">';

			if ( $choices && is_array( $choices ) ) {
				$html .= '<div class="vcex-param-preset-textfield__fields">';
					$hidden = $is_preset ? '' : ' style="display: none;"';
					$html .= '<div class="vcex-param-preset-textfield__preset"' . $hidden . '>';
						$html .= self::select( $value, $choices );
					$html .= '</div>';
					$hidden = $is_preset ? ' style="display: none;"' : '';
					$html .= '<div class="vcex-param-preset-textfield__custom"' . $hidden . '>';
						$html .= self::custom_input( $is_preset, $value, $settings );
					$html .= '</div>';
				$html .= '</div>';
				$html .= self::toggle_button( $is_preset, $value );
			}

			// This is needed or else nothing gets saved.
			$html .= '<input name="' . \esc_attr( $settings['param_name'] ) . '" class="' . \esc_attr( 'wpb_vc_param_value ' . $settings['param_name'] . ' ' . $settings['type'] . '_field' ) . '" type="hidden" value="' . \esc_attr( $value ) . '">';

		$html .= '</div>';

		if ( empty( $settings['description'] )
			&& isset( $settings['choices'] )
			&& \is_string( $settings['choices'] )
			&& $desc = \vcex_shortcode_param_description( $settings['choices'] )
		) {
			$html .= '<div class="vc_description vc_clearfix">' . \wp_kses( $desc, [ 'br' => [] ] ) . '</div>';
		}

		return $html;
	}

	/**
	 * Returns the select html.
	 */
	protected static function select( $value, $choices ) {
		$html = '<select class="vcex-param-preset-textfield__select wpb-select">';
			foreach ( $choices as $choice_k => $choice_name ) {
				$html .= '<option value="' . \esc_attr( $choice_k ) . '" ' . \selected( $value, $choice_k, false ) . '>' . \esc_html( $choice_name ) . '</option>';
			}
		$html .= '</select>';
		return $html;
	}

	/**
	 * Returns the toggle button html.
	 */
	protected static function toggle_button( $is_preset, $value ) {
		$toggle_pressed = ( ! $is_preset && $value ) ? 'true' : 'false';
		return '<button type="button" class="vcex-param-preset-textfield__toggle" aria-pressed="' . $toggle_pressed . '" aria-label="' . \esc_attr( 'Toggle between preset options and custom inputs.', 'total-theme-core' ) . '"><svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false" fill="currentColor"><path d="M14.5 13.8c-1.1 0-2.1.7-2.4 1.8H4V17h8.1c.3 1 1.3 1.8 2.4 1.8s2.1-.7 2.4-1.8H20v-1.5h-3.1c-.3-1-1.3-1.7-2.4-1.7zM11.9 7c-.3-1-1.3-1.8-2.4-1.8S7.4 6 7.1 7H4v1.5h3.1c.3 1 1.3 1.8 2.4 1.8s2.1-.7 2.4-1.8H20V7h-8.1z"></path></svg></button>';
	}

	/**
	 * Returns the custom input field.
	 */
	protected static function custom_input( $is_preset, $value, $settings ) {
		$custom_val  = ! $is_preset ? $value : '';
		$placeholder = isset( $settings['placeholder'] ) ? 'placeholder="' . \esc_attr( $settings['placeholder'] ) . '"' : '';
		$responsive = $settings['responsive_input'] ?? false;
		if ( $responsive ) {
			return self::responsive_input( $custom_val, $settings );
		} else {
			return '<input class="vcex-param-preset-textfield__input wpb-textinput" type="text" value="' . \esc_attr( $custom_val ) . '"' . $placeholder . '>';
		}
	}

	/**
	 * Returns a resppnsive input field.
	 */
	protected static function responsive_input( $value, $settings ) {
		if ( $value && false === \strpos( $value, ':' ) ) {
			$ogvalue = $value;
			$value = 'd:' . $value;
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
					$param = \preg_split( '/\:/', $pair );
					if ( ! empty( $param[0] ) && isset( $param[1] ) ) {
						if ( 'http' == $param[1] && isset( $param[2] ) ) {
							$param[1] = \rawurlencode( 'http:' . $param[2] ); // fix for incorrect urls that are not encoded
						}
						$field_values[ $param[0] ] = \rawurldecode( $param[1] );
					}
				}
			}
		}

		$html = '<div class="vcex-param-responsive-fields">';
			$count = 0;
			foreach ( $medias as $key => $val ) {
				$count++;
				$field_name = $settings['param_name'] . '[' . $key . ']';
				$icon_class = $val['icon'];
				$html .= '<div class="vcex-param-responsive-fields__item">';
					if ( 'pl' === $key || 'tl' === $key ) {
						$icon_class .= ' dashicons--flip';
					}
					$html .= '<div class="vcex-param-responsive-fields__icon"><span class="' . \esc_attr( $icon_class ) . '"></span></div>';
					$html .= '<label for="' . \esc_attr( $field_name ) .'" class="screen-reader-text">' . $medias[$key]['label'] . '</label>';
					$html .= '<input id="' . \esc_attr( $field_name ) .'" data-vcex-device="' . \esc_attr( $key ) . '" value="' . \esc_attr( $field_values[$key] ) . '" type="text" placeholder="-">';
				$html .= '</div>';
			}
		$html .= '</div>';

		if ( ! empty( $ogvalue ) ) {
			$value = $ogvalue;
		}

		$html .= '<input class="vcex-param-preset-textfield__input" value="' . \esc_attr( $value ) . '" type="hidden">';

		return $html;

	}

}
