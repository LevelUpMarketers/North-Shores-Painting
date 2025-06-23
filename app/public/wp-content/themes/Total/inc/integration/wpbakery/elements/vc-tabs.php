<?php

namespace TotalTheme\Integration\WPBakery\Elements;

\defined( 'ABSPATH' ) || exit;

/**
 * WPBakery OLD Tabs Mods.
 */
final class Vc_Tabs {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Initialize the class.
	 */
	public static function init(): void {
		\add_action( 'vc_after_init', [ self::class, 'add_params' ], 40 );

		if ( \defined( '\VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
			\add_filter( \VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, [ self::class, 'shortcode_classes' ], 99, 3 );
		}
	}

	/**
	 * Add custom params.
	 */
	public static function add_params(): void {
		if ( ! \function_exists( 'vc_add_param' ) ) {
			return;
		}

		$style_param = [
			'type' => 'dropdown',
			'heading' => \esc_html__( 'Style', 'total' ),
			'param_name' => 'style',
			'value' => [
				\esc_html__( 'Default', 'total' ) => 'default',
				\esc_html__( 'Alternative #1', 'total' ) => 'alternative-one',
				\esc_html__( 'Alternative #2', 'total' ) => 'alternative-two',
			],
			'weight' => 9999,
		];

		\vc_add_param( 'vc_tabs', $style_param );
		\vc_add_param( 'vc_tour', $style_param );
	}

	/**
	 * Add custom classes.
	 */
	public static function shortcode_classes( $class_string, $tag, $atts ) {
		if ( ( 'vc_tabs' == $tag || 'vc_tour' == $tag ) && ! empty( $atts['style'] ) ) {
			$class_string .= ' tab-style-' . \sanitize_html_class( $atts['style'] );
		}
		return $class_string;
	}

}
