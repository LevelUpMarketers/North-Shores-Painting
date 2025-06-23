<?php

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce Notices Shortcode.
 */
if ( ! class_exists( 'VCEX_WooCommerce_Notices_Shortcode' ) ) {

	class VCEX_WooCommerce_Notices_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_woocommerce_notices';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Get shortcode title.
		 */
		public static function get_title(): string {
			return esc_html__( 'Woo Notices', 'total-theme-core' );
		}

		/**
		 * Get shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Outputs all WooCommerce notices', 'total-theme-core' );
		}

		/**
		 * Custom VC map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			$settings = [
				'category' => [ 'WooCommerce' ],
				'show_settings_on_create' => false,
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
			if ( ! vcex_maybe_display_shortcode( self::TAG, $atts ) || ! function_exists( 'woocommerce_output_all_notices' ) ) {
				return null;
			}
			ob_start();
				woocommerce_output_all_notices();
			return ob_get_clean();
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [];
		}

	}

}

new VCEX_WooCommerce_Notices_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_WooCommerce_Notices' ) ) {
	class WPBakeryShortCode_Vcex_WooCommerce_Notices extends WPBakeryShortCode {}
}
