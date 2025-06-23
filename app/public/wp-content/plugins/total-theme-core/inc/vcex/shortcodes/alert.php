<?php

defined( 'ABSPATH' ) || exit;

/**
 * Alert Shortcode.
 */
if ( ! class_exists( 'Vcex_Alert_Shortcode' ) ) {

	class Vcex_Alert_Shortcode extends \TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_alert';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Shortcode title.
		 */
		public static function get_title(): string {
			return esc_html__( 'Alert', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display an alert or notice.', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Type', 'total-theme-core' ),
					'param_name' => 'type',
					'choices' => [
						''        => esc_html__( 'Default', 'total-theme-core' ),
						'info'    => esc_html__( 'Info', 'total-theme-core' ),
						'success' => esc_html__( 'Success', 'total-theme-core' ),
						'warning' => esc_html__( 'Warning', 'total-theme-core' ),
						'error'   => esc_html__( 'Error', 'total-theme-core' ),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Heading', 'total-theme-core' ),
					'param_name' => 'heading',
					'description' => self::param_description( 'text' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textarea_html',
					'heading' => esc_html__( 'Content', 'total-theme-core' ),
					'param_name' => 'content',
					'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce laoreet vestibulum elit eget fringilla.',
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core'),
					'param_name' => 'animation_duration',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
					'css' => true,
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core'),
					'param_name' => 'animation_delay',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
					'css' => true,
					'editors' => [ 'wpbakery' ],
				],
				// Style
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'padding',
					'heading' => esc_html__( 'Vertical Padding', 'total-theme-core' ),
					'param_name' => 'padding_y',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'padding',
					'heading' => esc_html__( 'Horizontal Padding', 'total-theme-core' ),
					'param_name' => 'padding_x',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
			];
		}

	}

}

new Vcex_Alert_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Alert' ) ) {
	class WPBakeryShortCode_Vcex_Alert extends WPBakeryShortCode {}
}
