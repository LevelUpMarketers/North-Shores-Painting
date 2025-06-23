<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

class Modify_Colorpicker {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		if ( \function_exists( '\vc_post_param' ) && 'vc_edit_form' === \vc_post_param( 'action' ) ) {
			\add_filter( 'vc_css_editor', [ self::class, 'filter_vc_css_editor' ] );
			//\add_filter( 'vc_single_param_edit', [ self::class, 'filter_vc_single_param_edit' ] ); // can't do this for various reasons.
		}
	}

	/**
	 * Hooks into the "filter_vc_css_editor" filter.
	 */
	public static function filter_vc_css_editor( $css_editor ) {
		$css_editor = \str_replace(
			'<div class="wpb-color-picker"></div><input type="text" name="border_color" value="" class="vc_color-control vc_ui-hidden">',
			self::get_color_component( 'border_color' ),
			$css_editor
		);
		$css_editor = \str_replace(
			'<div class="wpb-color-picker"></div><input type="text" name="background_color" value="" class="vc_color-control vc_ui-hidden">',
			self::get_color_component( 'background_color' ),
			$css_editor
		);
		return $css_editor;
	}

	/**
	 * Hooks into the "vc_single_param_edit" filter.
	 */
	public static function filter_vc_single_param_edit( $param ) {
		if ( isset( $param['type'] ) && 'colorpicker' === $param['type'] ) {
			// Need to re-add the classnames cause wpb...
			$param['vc_single_param_edit_holder_class'] = [
				'wpb_el_type_vcex_colorpicker',
				'vc_wrapper-param-type-vcex_colorpicker',
				'vc_shortcode-param',
				'vc_column',
			];
			if ( ! empty( $param['param_holder_class'] ) ) {
				$param['vc_single_param_edit_holder_class'][] = $param['param_holder_class'];
			}
			$param['type'] = 'vcex_colorpicker';
		}
		return $param;
	}

	/**
	 * Returns the theme's color component.
	 */
	public static function get_color_component( $input_name ) {
		ob_start();
		\totaltheme_component( 'color', [
			'input_name' => $input_name,
			'exclude'    => 'transparent,currentColor',
			'parse_vars' => true,
		] );
		return ob_get_clean();
	}

}
