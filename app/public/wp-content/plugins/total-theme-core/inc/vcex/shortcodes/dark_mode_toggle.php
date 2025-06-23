<?php

defined( 'ABSPATH' ) || exit;

/**
 * Dark Mode Toggle Shortcode.
 */
if ( ! class_exists( 'Vcex_Dark_Mode_Toggle' ) ) {

	class Vcex_Dark_Mode_Toggle extends \TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_dark_mode_toggle';

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
			return esc_html__( 'Dark Mode Toggle', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Dark mode toggle button.', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_select_buttons',
					'heading' => \esc_html__( 'Type', 'total-theme-core' ),
					'param_name' => 'type',
					'std' => 'button',
					'choices' => [
						'button' => \esc_html__( 'Unstyled Button', 'total-theme-core' ),
						'theme_button' => \esc_html__( 'Theme Button', 'total-theme-core' ),
						'link' => \esc_html__( 'Link', 'total-theme-core' ),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => \esc_html__( 'Show Label', 'total-theme-core' ),
					'param_name' => 'show_label',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'placeholder' => esc_html__( 'Dark Mode', 'total' ),
					'heading' => \esc_html__( 'Dark Mode Label', 'total-theme-core' ),
					'param_name' => 'dark_label',
					'dependency' => [ 'element' => 'show_label', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'placeholder' => esc_html__( 'Light Mode', 'total' ),
					'heading' => \esc_html__( 'Light Mode Label', 'total-theme-core' ),
					'param_name' => 'light_label',
					'dependency' => [ 'element' => 'show_label', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => \esc_html__( 'Gap', 'total-theme-core' ),
					'description' => \esc_html__( 'Controls the space between the icon and the label.', 'total-theme-core' ),
					'param_name' => 'gap',
					'dependency' => [ 'element' => 'show_label', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => \esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => true,
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'dropdown',
					'heading' => \esc_html__( 'Icon Size', 'total-theme-core' ),
					'param_name' => 'icon_size',
					'value' => [
						\esc_html__( 'Inherit', 'total-theme-core' ) => '',
						'2xs' => '2xs',
						'xs' => 'xs',
						'sm' => 'sm',
						'lg' => 'lg',
						'xl' => 'xl',
						'2xl' => '2xl',
					],
					'dependency' => [ 'element' => 'show_label', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => \esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'typography',
					'selector' => '.vcex-dark-mode-toggle',
					'editors' => [ 'elementor' ],
				],
				[
					'type' => 'textfield',
					'param_name' => 'el_class',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
			];
		}
	}

}

new Vcex_Dark_Mode_Toggle;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Dark_Mode_Toggle' ) ) {
	class WPBakeryShortCode_Vcex_Dark_Mode_Toggle extends WPBakeryShortCode {}
}
