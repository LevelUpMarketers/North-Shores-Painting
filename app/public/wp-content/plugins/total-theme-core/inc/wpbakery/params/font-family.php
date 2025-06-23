<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Font Family.
 */
final class Font_Family {

	public static function output( $settings, $value ) {
		if ( ! \defined( '\TOTAL_THEME_ACTIVE' ) ) {
			return '<input type="text" class="wpb_vc_param_value '
				. \esc_attr( $settings['param_name'] ) . ' '
				. \esc_attr( $settings['type'] ) . '" name="' . \esc_attr( $settings['param_name'] ) . '" value="' . \esc_attr( $value ) . '">';
		}

		// Get fonts
		$user_fonts     = self::get_user_fonts();
		$custom_fonts   = self::get_custom_fonts();
		$standard_fonts = self::get_standard_fonts();
		$google_fonts   = self::get_google_fonts();
		$chosen_class   = ( \is_array( $google_fonts ) && \count( $google_fonts ) > 10 ) ? 'vcex-chosen ' : '';

		$output = '<select name="'
				. $settings['param_name']
				. '" class="wpb_vc_param_value wpb-input wpb-select '
				. $chosen_class
				. $settings['param_name']
				. ' ' . $settings['type'] .'">'
				. '<option value="" '. \selected( $value, '', false ) .'>'. \esc_html__( 'Default', 'total-theme-core' ) .'</option>';

		$value_exists = false;

		// User fonts
		if ( $user_fonts ) {
			$output .= '<optgroup label="' . \esc_html__( 'My Fonts', 'total-theme-core' ) . '">';
			foreach ( $user_fonts as $font_name => $font_settings ) {
				if ( $font_name === $value ) {
					$value_exists = true;
				}
				$output .= '<option value="' . \esc_html( $font_name ) . '" ' . \selected( $font_name, $value, false ) . '>' . \ucfirst( \esc_html( $font_name ) ) . '</option>';
			}
			$output .= '</optgroup>';
		}

		// Custom fonts.
		if ( $custom_fonts ) {
			$output .= '<optgroup label="' . \esc_html__( 'Custom Fonts', 'total-theme-core' ) . '">';
			foreach ( $custom_fonts as $font ) {
				if ( $font === $value ) {
					$value_exists = true;
				}
				$output .= '<option value="' . \esc_html( $font ) . '" ' . \selected( $font, $value, false ) .'>' . \ucfirst( \esc_html( $font ) ) . '</option>';
			}
			$output .= '</optgroup>';
		}

		// Standard fonts.
		if ( $standard_fonts ) {
			$output .= '<optgroup label="' . \esc_html__( 'Standard Fonts', 'total-theme-core' ) . '">';
				foreach ( $standard_fonts as $font ) {
					if ( $font === $value ) {
						$value_exists = true;
					}
					$output .= '<option value="' . \esc_html( $font ) . '" ' . \selected( $font, $value, false ) . '>' . \esc_html( $font ) .'</option>';
				}
			$output .= '</optgroup>';
		}

		// Google fonts.
		if ( $google_fonts ) {
			$output .= '<optgroup label="'. \esc_html__( 'Google Fonts', 'total-theme-core' ) .'">';
				foreach ( $google_fonts as $font ) {
					if ( $font === $value ) {
						$value_exists = true;
					}
					$output .= '<option value="' . \esc_html( $font ) . '" ' . \selected( $font, $value, false ) . '>' . \esc_html( $font ) .'</option>';
				}
			$output .= '</optgroup>';
		}

		if ( ! empty( $value ) && false === $value_exists ) {
			$output .= '<optgroup label="' . \esc_html__( 'Non Registered Fonts', 'total-theme-core' ) . '">';
				$output .= '<option value="' . \esc_html( $value ) . '" selected="selected">' . \esc_html( $value ) .'</option>';
			$output .= '</optgroup>';
		}

		$output .= '</select>';

		return $output;

	}

	protected static function get_user_fonts() {
		$user_fonts = [];
		if ( \function_exists( '\wpex_get_registered_fonts' ) ) {
			$user_fonts = (array) \wpex_get_registered_fonts();
		}
		return $user_fonts;
	}

	protected static function get_custom_fonts() {
		$custom_fonts = [];
		if ( \function_exists( '\wpex_add_custom_fonts' ) ) {
			$custom_fonts = (array) \wpex_add_custom_fonts();
		}
		return $custom_fonts;
	}

	protected static function get_standard_fonts() {
		$standard_fonts = [];
		if ( \function_exists( '\wpex_standard_fonts' ) ) {
			$standard_fonts = (array) \wpex_standard_fonts();
		}
		return $standard_fonts;
	}

	protected static function get_google_fonts() {
		if ( \function_exists( '\wpex_has_registered_fonts' ) && \wpex_has_registered_fonts() ) {
			return [];
		}
		$google_fonts = [];
		if ( \function_exists( '\wpex_google_fonts_array' ) ) {
			$google_fonts = \wpex_google_fonts_array();
		}
		return $google_fonts;
	}

}
