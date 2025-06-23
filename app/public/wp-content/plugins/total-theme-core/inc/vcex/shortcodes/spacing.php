<?php

defined( 'ABSPATH' ) || exit;

/**
 * Spacing Shortcode.
 */
if ( ! class_exists( 'Vcex_Spacing_Shortcode' ) ) {

	class Vcex_Spacing_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_spacing';

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
			return esc_html__( 'Spacing', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Adds spacing anywhere you need it', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$height_desc = esc_html__( 'Allowed units:', 'total-theme-core' ) . ' px, em, rem, vw, vmin, vmax, vh.<br>' . esc_html__( 'Allowed CSS functions:', 'total-theme-core' ) . ' calc(), clamp(), min(), max()';

			return [
				[
					'type' => 'vcex_text',
					'admin_label' => true,
					'heading' => esc_html__( 'Size', 'total-theme-core' ),
					'param_name' => 'size',
					'dependency' => [ 'element' => 'responsive', 'value' => 'false' ],
					'description' => $height_desc,
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Responsive?', 'total-theme-core' ),
					'param_name' => 'responsive',
					'value' => 'false',
				],
				[
					'type' => 'vcex_responsive_sizes',
					'heading' => esc_html__( 'Height', 'total-theme-core' ),
					'param_name' => 'size_responsive',
					'value' => '30px',
					'expanded' => true,
					'description' => $height_desc,
					'dependency' => [ 'element' => 'responsive', 'value' => 'true' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'class',
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
				],
			];
		}

	}

}

new Vcex_Spacing_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Spacing' ) ) {
	class WPBakeryShortCode_Vcex_Spacing extends WPBakeryShortCode {}
}
