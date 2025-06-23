<?php

namespace TotalThemeCore\Shortcodes;

defined( 'ABSPATH' ) || exit;

final class Shortcode_Header_Dark_Mode_Icon {

	/**
	 * Register the shortcode and add filters.
	 */
	public function __construct() {
		\add_shortcode( 'header_dark_mode_icon', [ self::class, 'output' ] );
	}

	/**
	 * Shortcode output.
	 */
	public static function output( $atts ): string {
		if ( ! \is_callable( '\TotalTheme\Dark_mode::get_icon_name' ) || ! \function_exists( '\totaltheme_get_icon' ) ) {
			return '';
		}

		$atts = \shortcode_atts( [
			'class'      => '',
			'visibility' => '',
		], $atts, 'header_dark_mode_icon' );

		$icon_dark  = \totaltheme_get_icon( \TotalTheme\Dark_mode::get_icon_name( 'dark' ), 'wpex-flex wpex-icon--w' );
		$icon_light = \totaltheme_get_icon( \TotalTheme\Dark_mode::get_icon_name( 'light' ), ' wpex-flex wpex-icon--w' );

		$class = 'wpex-header-dark-mode-icon wpex-inline-flex wpex-items-center';

		if ( ! empty( $atts['class'] ) ) {
			$class .= ' ' . \str_replace( '.', '', \trim( $atts['class'] ) );
		}

		if ( ! empty( $atts['visibility'] )
			&& \function_exists( '\totaltheme_get_visibility_class' )
			&& $visibility_class = \totaltheme_get_visibility_class( $atts['visibility'] )
		) {
			$class .= " {$visibility_class}";
		}

		$output = '<span class="' . \esc_attr( $class ) . '"><button type="button" class="wpex-header-dark-mode-icon__button wpex-unstyled-button wpex-inline-flex wpex-hover-link-color" data-wpex-toggle="theme" data-role="button">';
			$output .= '<span class="hidden-dark-mode wpex-header-dark-mode-icon__icon wpex-inline-flex wpex-items-center">' . $icon_dark . '</span>';
			$output .= '<span class="visible-dark-mode wpex-header-dark-mode-icon__icon wpex-inline-flex wpex-items-center">' . $icon_light . '</span>';
		$output .= '</button></span>';

		return $output;

	}

}
