<?php

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Loop Carousel.
 */
if ( ! class_exists( 'VCEX_WooCommerce_Loop_Carousel' ) ) {

	class VCEX_WooCommerce_Loop_Carousel extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_woocommerce_loop_carousel';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Shortcode title.
		 */
		public static function get_title(): string {
			return esc_html__( 'Woo Products Carousel', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'WooCommerce products carousel', 'total-theme-core' );
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
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$params = array(
				// General
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'classes',
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
				),
				vcex_vc_map_add_css_animation(),
				// Query
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Advanced Query', 'total-theme-core' ),
					'param_name' => 'custom_query',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to build a custom query using your own parameters.', 'total-theme-core' ),
				),
				array(
					'type' => 'textarea_safe',
					'heading' => esc_html__( 'Query Parameter String or Callback Function Name', 'total-theme-core' ),
					'param_name' => 'custom_query_args',
					'description' => self::param_description( 'advanced_query' ),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'true' ) ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Post Count', 'total-theme-core' ),
					'param_name' => 'count',
					'value' => '8',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Display', 'total-theme-core' ),
					'param_name' => 'query_products_by',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'value' => array(
						esc_html__( 'All Products', 'total-theme-core' ) => '',
						esc_html__( 'Featured Products', 'total-theme-core' ) => 'featured',
						esc_html__( 'On Sale Products', 'total-theme-core' ) => 'on_sale',
					),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => '',
					'vcex' => array( 'on' => true, 'off' => '' ),
					'heading' => esc_html__( 'Exclude Out of Stock Products', 'total-theme-core' ),
					'param_name' => 'exclude_products_out_of_stock',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Include Categories', 'total-theme-core' ),
					'param_name' => 'include_categories',
					'param_holder_class' => 'vc_not-for-custom',
					'admin_label' => true,
					'settings' => array(
						'multiple' => true,
						'min_length' => 1,
						'groups' => true,
						'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Exclude Categories', 'total-theme-core' ),
					'param_name' => 'exclude_categories',
					'param_holder_class' => 'vc_not-for-custom',
					'admin_label' => true,
					'settings' => array(
						'multiple' => true,
						'min_length' => 1,
						'groups' => true,
						'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order', 'total-theme-core' ),
					'param_name' => 'order',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'value' => array(
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'DESC', 'total-theme-core' ) => 'DESC',
						esc_html__( 'ASC', 'total-theme-core' ) => 'ASC',
					),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Order By', 'total-theme-core' ),
					'param_name' => 'orderby',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'post_type' => 'woo_product',
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Orderby: Meta Key', 'total-theme-core' ),
					'param_name' => 'orderby_meta_key',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array(
						'element' => 'orderby',
						'value' => array( 'meta_value_num', 'meta_value' ),
					),
				),
				array( 'type' => 'hidden', 'param_name' => 'entry_output' ),
			);

			return array_merge( $params, vcex_vc_map_carousel_settings() );
		}

		/**
		 * Register autocomplete hooks.
		 */
		public static function register_vc_autocomplete_hooks(): void {
			// Get autocomplete suggestion.
			add_filter(
				'vc_autocomplete_vcex_woocommerce_loop_carousel_include_categories_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Woo_Categories::callback'
			);
			add_filter(
				'vc_autocomplete_vcex_woocommerce_loop_carousel_exclude_categories_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Woo_Categories::callback'
			);
			// Render autocomplete suggestions.
			add_filter(
				'vc_autocomplete_vcex_woocommerce_loop_carousel_include_categories_render',
				'TotalThemeCore\WPBakery\Autocomplete\Woo_Categories::render'
			);
			add_filter(
				'vc_autocomplete_vcex_woocommerce_loop_carousel_exclude_categories_render',
				'TotalThemeCore\WPBakery\Autocomplete\Woo_Categories::render'
			);
		}
	}

}

new VCEX_WooCommerce_Loop_Carousel;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_woocommerce_Loop_Carousel' ) ) {
	class WPBakeryShortCode_Vcex_woocommerce_Loop_Carousel extends WPBakeryShortCode {}
}