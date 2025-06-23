<?php

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Template Shortcode.
 */
if ( ! class_exists( 'Vcex_WooCommerce_Content' ) ) {

	class Vcex_WooCommerce_Content extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_woocommerce_content';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Get shortcode title.
		 */
		public static function get_title() {
			return esc_html__( 'Woo Content', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Returns the output of woocommerce_content()', 'total-theme-core' );
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
		 * Shortcode output.
		 */
		public static function output( $atts, $content = null, $shortcode_tag = '' ): ?string {
			if ( ! vcex_maybe_display_shortcode( self::TAG, $atts )
				|| ! function_exists( 'is_woocommerce' )
				|| ! function_exists( 'woocommerce_content' )
			) {
				return null;
			}
			if ( vcex_is_template_edit_mode() ) {
				return '<div class="wpex-surface-3 wpex-text-2 wpex-p-15 wpex-text-center">' . esc_html__( 'This element is disabled in the frontend editor to prevent an endless loop.', 'total-theme-core' ) .'</div>';
			} else {
				ob_start();
					self::get_woocommerce_content();
				return ob_get_clean();
			}
		}

		/**
		 * Returns the WooCommerce content.
		 * 
		 * This is essentially the same thing as the woocommerce_content() function but we remove the loop
		 * in the is_singular() check because it can cause an endless loop in dynamic templates.
		 */
		protected static function get_woocommerce_content() {
			if ( is_singular( 'product' ) ) {
				wc_get_template_part( 'content', 'single-product' );
			} else {
				woocommerce_content();
			}
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				array(
					'type' => 'vcex_notice',
					'param_name' => 'main_notice',
					'text' => esc_html__( 'This element should be used only when creating dynamic templates and it display the current page/archive WooCommerce output. This element displays the output of the woocommerce_content function, aka the woocommerce.php file.', 'total-theme-core' ),
				),
			);
		}

	}

}

new Vcex_WooCommerce_Content;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_WooCommerce_Content' ) ) {
	class WPBakeryShortCode_Vcex_WooCommerce_Content extends WPBakeryShortCode {}
}
