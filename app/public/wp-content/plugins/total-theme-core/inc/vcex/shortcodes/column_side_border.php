<?php

defined( 'ABSPATH' ) || exit;

/**
 * Column Side Border Shortcode.
 */
if ( ! class_exists( 'VCEX_Column_Side_Border_Shortcode' ) ) {

	class VCEX_Column_Side_Border_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_column_side_border';

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
			return esc_html__( 'Column Side Border', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Responsive column side border', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_notice',
					'param_name' => 'editor_notice',
					'text' => esc_html__( 'Due to how the page builder works this module will display a placeholder in the front-end editor you will have to save and preview your live site to view the final result.', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Position', 'total-theme-core' ),
					'param_name' => 'position',
					'std' => 'right',
					'choices' => [
						'left' => esc_html__( 'Left', 'total-theme-core' ),
						'right' => esc_html__( 'Right', 'total-theme-core' ),
					],
					'admin_label' => true,
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background_color',
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Height', 'total-theme-core' ),
					'param_name' => 'height',
					'description' => esc_html__( 'Enter a custom px or % value. Default: 100%', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Width', 'total-theme-core' ),
					'param_name' => 'width',
					'description' => esc_html__( 'Enter a custom px value. Default: 1px', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Classes', 'total-theme-core' ),
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

new VCEX_Column_Side_Border_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Column_Side_Border' ) ) {
	class WPBakeryShortCode_Vcex_Column_Side_Border extends WPBakeryShortCode {}
}
