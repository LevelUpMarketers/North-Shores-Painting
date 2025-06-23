<?php

defined( 'ABSPATH' ) || exit;

/**
 * Cart Link Shortcode.
 */
if ( ! class_exists( 'Vcex_Cart_Link_Shortcode' ) ) {

	class Vcex_Cart_Link_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'cart_link';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Shortcode title.
		 */
		public static function get_title() {
			return esc_html__( 'Cart Link', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Cart link with count and price', 'total-theme-core' );
		}

		/**
		 * Custom VC map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			$settings = [
				'category' => [ 'WooCommerce' ],
			];
			if ( $branding = \vcex_shortcodes_branding() ) {
				$settings['category'][] = $branding;
			}
			return $settings;
		}

		/**
		 * Custom Output.
		 */
		public static function output( $atts, $content = null, $shortcode_tag = '' ): ?string {
			return totalthemecore_call_static( 'TotalThemeCore\Shortcodes\Shortcode_Cart_Link', 'output', $atts );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Link to Cart', 'total'),
					'param_name' => 'link',
					'std' => 'true',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'checkbox',
					'heading' => esc_html__( 'Items', 'total-theme-core' ),
					'param_name' => 'items',
					'std' => 'icon,count,total',
					'value' => [
						esc_html__( 'Icon', 'total-theme-core' ) => 'icon',
						esc_html__( 'Count', 'total-theme-core' ) => 'count',
						esc_html__( 'Total', 'total-theme-core' ) => 'total',
					],
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Custom Icon', 'total-theme-core' ),
					'param_name' => 'icon',
					'editors' => [ 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type'  => 'vcex_font_family_select',
					'heading'  => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name'  => 'font_family',
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'description' => self::param_description( 'font_size' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'font_color',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'typography',
					'selector' => '.wpex-cart-link',
					'editors' => [ 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Margin', 'total-theme-core' ),
					'param_name' => 'icon_margin',
					'description' => self::param_description( 'margin' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
			];
		}

	}

}

new Vcex_Cart_Link_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Cart_Link' ) ) {
	class WPBakeryShortCode_Cart_Link extends WPBakeryShortCode {}
}
