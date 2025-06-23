<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param => Text Align.
 */
final class Text_Align {

	/**
	 * Returns choices.
	 * 
	 * @note Used for Elementor integration.
	 */
	public static function get_choices( $settings ): array {
		$choices = [
			''       => \esc_html( 'Inherit', 'total-theme-core' ),
			'left'   => \esc_html( 'Start', 'total-theme-core' ),
			'center' => \esc_html( 'Center', 'total-theme-core' ),
			'right'  => \esc_html( 'End', 'total-theme-core' ),
		];
		if ( ! empty( $settings['std'] ) ) {
			$choices = [ 'none' => \esc_html( 'None', 'total-theme-core' ) ] + $choices;
			unset( $choices[''] );
		}
		if ( ! empty( $settings['exclude_choices'] ) && \is_array( $settings['exclude_choices'] ) ) {
			$choices = \array_diff_key( $choices, $settings['exclude_choices'] );
		}
		return $choices;
	}

	/**
	 * Param output.
	 */
	public static function output( $settings, $value ) {
		$default = $settings['std'] ?? '';

		if ( empty( $value ) && isset( $settings['std'] ) ) {
			$value = $default;
		}

		$output = '<div class="vcex-select-buttons-param vcex-custom-select vcex-noselect">';

			foreach ( self::get_choices( $settings ) as $choice => $label ) {
				if ( ! $choice || 'none' === $choice ) {
					if ( 'none' === $choice ) {
						$active = ( 'none' === $value ) ? ' vcex-active' : '';
					} else {
						$choice = '';
						$active = ! $value ? ' vcex-active' : '';
					}
					$output .= '<button type="button" class="vcex-opt vcex-default' . $active . '" data-value="' . \esc_attr( $choice ) . '">' . \esc_html( $label ) . '</button>';
				} else {
					$active = $value === $choice ? ' vcex-active' : '';
					$output .= '<button type="button" title="' . \esc_attr( $label ) . '" class="vcex-opt' . $active . '" data-value="' . \esc_attr( $choice )  . '" arial-label="' . \esc_attr( $label ) . '">' . self::get_option_svg( $choice )  .'</button>';
				}
			}

		$output .= '<input name="' . \esc_attr( $settings['param_name'] ) . '" class="' . \esc_attr( "vcex-hidden-input wpb-input wpb_vc_param_value {$settings['param_name']} {$settings['type']}_field" ) . '" type="hidden" value="' . \esc_attr( $value ) . '">';

		$output .= '</div>';

		return $output;
	}

	/**
	 * Returns option svg icon.
	 */
	private static function get_option_svg( $option ): string {
		if ( \is_rtl()
			&& function_exists( 'vcex_is_bidirectional' )
			&& vcex_is_bidirectional()
			&& function_exists( 'vcex_parse_direction' )
		) {
			$option = vcex_parse_direction( $option );
		}
		$svg_map = [
			'left' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M15 15H3v2h12v-2zm0-8H3v2h12V7zM3 13h18v-2H3v2zm0 8h18v-2H3v2zM3 3v2h18V3H3z"/></svg>',
			'center' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M7 15v2h10v-2H7zm-4 6h18v-2H3v2zm0-8h18v-2H3v2zm4-6v2h10V7H7zM3 3v2h18V3H3z"/></svg>',
			'right' => '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="currentColor"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M3 21h18v-2H3v2zm6-4h12v-2H9v2zm-6-4h18v-2H3v2zm6-4h12V7H9v2zM3 3v2h18V3H3z"/></svg>',
		];
		return $svg_map[ $option ] ?? $svg_map['right'];
	}

}
