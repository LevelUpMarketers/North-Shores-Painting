<?php

namespace TotalThemeCore\WPBakery\Params;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery Param > Sorter.
 */
final class Sorter {

	/**
	 * Param output.
	 */
	public static function output( $settings, $value ) {
		$choices = $settings['choices'] ?? $settings['param_name'] ?? [];

		if ( is_string( $choices ) && class_exists( '\TotalThemeCore\Vcex\Setting_Choices' ) ) {
			$choices = (new \TotalThemeCore\Vcex\Setting_Choices( $choices, $settings ))->get_choices();
		}

		if ( ! $choices ) {
			return;
		}

		$enabled_blocks = ( $value && \is_string( $value ) ) ? \explode( ',', $value ) : [];

		$output = '<ul class="vcex-sorter-param">';

			// Display all enabled blocks at the top.
			foreach ( $enabled_blocks as $block ) {
				
				if ( ! isset( $choices[ $block ] ) ) {
					continue;
				}

				$output .= '<li data-value="' . \esc_attr( $block ) . '">';

				$output .= \esc_html( $choices[ $block ] );
					$output .= self::toggle_button();
				$output .= '</li>';

				unset( $choices[ $block ] );
			}

			// Display disabled blocks at bottom.
			if ( ! empty( $choices ) ) {
				foreach ( $choices as $c_val => $c_label ) {
					if ( ! $c_val ) {
						continue;
					}
					$output .= '<li class="vcex-disabled" data-value="' . \esc_attr( $c_val ) . '">';
						$output .= \esc_html( $c_label );
						$output .= self::toggle_button();
					$output .= '</li>';
				}
			}

		$output .= '</ul>';

		$output .= '<input name="' . \esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value  ' . \esc_attr( $settings['param_name'] ) . ' ' . \esc_attr( $settings['type'] ) . '_field" type="hidden" value="' . \esc_attr( $value ) . '">';

		return $output;
	}

	/**
	 * Render the toggle button.
	 */
	private static function toggle_button() {
		$html = '<a href="#" aria-role="button" aria-label="' . \esc_html__( 'Toggle on/off', 'total-theme-core' ) . '">';
			$html .= '<svg class="vcex-sorter-param__toggle-on" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M192 64C86 64 0 150 0 256S86 448 192 448H384c106 0 192-86 192-192s-86-192-192-192H192zm192 96a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"/></svg></span>';
			$html .= '<svg class="vcex-sorter-param__toggle-off" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M384 128c70.7 0 128 57.3 128 128s-57.3 128-128 128H192c-70.7 0-128-57.3-128-128s57.3-128 128-128H384zM576 256c0-106-86-192-192-192H192C86 64 0 150 0 256S86 448 192 448H384c106 0 192-86 192-192zM192 352a96 96 0 1 0 0-192 96 96 0 1 0 0 192z"/></svg>';
		$html .= '</a>';

		return $html;
	}
}
