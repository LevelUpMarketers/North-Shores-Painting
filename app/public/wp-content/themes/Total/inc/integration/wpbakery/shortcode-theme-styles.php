<?php

namespace TotalTheme\Integration\WPBakery;

use WPBMap;

\defined( 'ABSPATH' ) || exit;

class Shortcode_Theme_Styles {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		\add_action( 'vc_after_init', [ self::class, 'register_styles' ] );

		if ( \defined( '\VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
			\add_filter( \VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, [ self::class, 'filter_shortcode_classes' ], 10, 3 );
		}
	}

	/**
	 * Check if the theme style should be set as the default style.
	 *
	 * @todo enable by default on all installations.
	 */
	private static function use_as_default( $element = '' ): bool {
		$check = \get_theme_mod( 'vcex_theme_style_is_default', true );
		return (bool) \apply_filters( 'wpex_wpbakery_theme_styles_set_default', $check, $element );
	}

	/**
	 * Register theme styles.
	 */
	public static function register_styles() {
		if ( ! \is_callable( 'WPBMap::getParam' ) || ! \function_exists( '\vc_update_shortcode_param' ) || ! \function_exists( '\vc_add_param' ) ) {
			return;
		}

		$shortcodes = [
			'vc_tta_accordion',
			'vc_tta_tour',
			'vc_toggle',
			'vc_tta_tabs',
		];

		foreach ( $shortcodes as $shortcode ) {
			$style_param = WPBMap::getParam( $shortcode, 'style' );

			if ( ! $style_param ) {
				continue;
			}

			if ( 'vc_tta_tabs' === $shortcode ) {
				$theme_styles = [
					\esc_html__( 'Theme Style', 'total' ) => 'total',
					\esc_html__( 'Theme Style #2', 'total' ) => 'total_2',
					\esc_html__( 'Theme Style #2 (White Text)', 'total' ) => 'total_2_white',
					\esc_html__( 'Theme Style #3', 'total' ) => 'total_3',
					\esc_html__( 'Theme Style #4', 'total' ) => 'total_4',
				];
			} else {
				$theme_styles = [
					\esc_html__( 'Theme Style', 'total' ) => 'total',
				];
			}

			$style_param['value'] = \array_merge( $style_param['value'], $theme_styles );

			if ( self::use_as_default( $shortcode ) ) {
				$style_param['std'] = 'total';
			}

			\vc_update_shortcode_param( $shortcode, $style_param );

			$hide_params = [
				'color',
				'shape',
				'no_fill',
				'no_fill_content_area',
			];

			if ( 'vc_toggle' === $shortcode ) {
				$hide_params[] = 'size';
			} elseif ( 'vc_tta_tabs' === $shortcode ) {
				$hide_params[] = 'pagination_style';
			}

			foreach ( $hide_params as $hide_param ) {
				if ( $get_param = WPBMap::getParam( $shortcode, $hide_param ) ) {
					if ( 'pagination_style' === $hide_param ) {
						$not_equal_to = [ 'total_2', 'total_3', 'total_4' ];
					} else {
						$not_equal_to = \array_values( $theme_styles );
					}
					$get_param['dependency'] = [
						'element'            => 'style',
						'value_not_equal_to' => $not_equal_to,
					];
					\vc_update_shortcode_param( $shortcode, $get_param );
				}
			}
		}

		// Add alignment option to vc_tta_tabs.
		if ( $tabs_align_param = WPBMap::getParam( 'vc_tta_tabs', 'alignment' ) ) {
			$tabs_align_param['value'][ \esc_html__( 'Spaced Out (Theme Styles 2 and 3 Only)', 'total' ) ] = 'space-between';
			\vc_update_shortcode_param( 'vc_tta_tabs', $tabs_align_param );
		}
	}

	/**
	 * Filter shortcode classes.
	 */
	public static function filter_shortcode_classes( $class_string, $tag, $atts ) {
		if ( ! \in_array( $tag, [ 'vc_tta_tabs' ], true ) ) {
			return $class_string;
		}
		$style = $atts['style'] ?? '';
		if ( 'total_2_white' === $style ) {
			$class_string = \str_replace( 'vc_tta-color-grey', 'vc_tta-color-white', $class_string );
			$class_string = \str_replace( 'vc_tta-style-total_2_white', 'vc_tta-style-total_2', $class_string );
		} elseif ( \in_array( $style, [ 'total_2', 'total_3', 'total_4' ], true ) ) {
			$class_string = \str_replace( ' vc_tta-color-grey', '', $class_string );
		}
		return $class_string;	
	}
}
