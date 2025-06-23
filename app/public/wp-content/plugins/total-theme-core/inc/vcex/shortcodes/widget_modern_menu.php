<?php

defined( 'ABSPATH' ) || exit;

/**
 * Widget_Modern_Menu Shortcode.
 */
if ( ! class_exists( 'Vcex_Widget_Modern_Menu_Shortcode' ) ) {

	class Vcex_Widget_Modern_Menu_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_widget_modern_menu';

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
			return esc_html__( 'Sidebar Menu Widget', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display a Sidebar Menu Widget without a widget area', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Menu', 'total-theme-core' ),
					'param_name' => 'menu_id',
					'choices' => 'menu',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Aria Label', 'total-theme-core' ),
					'param_name' => 'aria_label',
					'description' => esc_html__( 'Label for screen readers.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'param_name' => 'style',
					'heading' => \esc_html__( 'Style', 'total-theme-core' ),
					'choices' => [
						'bordered' => \esc_html__( 'Default', 'total-theme-core' ),
						'clean' => \esc_html__( 'Clean', 'total-theme-core' ),
						'plain' => \esc_html__( 'Plain', 'total-theme-core' ),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'param_name' => 'active_highlight',
					'std' => 'true',
					'heading' => \esc_html__( 'Highlight Active Page', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'param_name' => 'show_arrows',
					'std' => 'true',
					'heading' => \esc_html__( 'Show Side Arrows', 'total-theme-core' ),
					'description' => \esc_html__( 'If "Hide Dropdowns" is enabled, arrows will only appear on items that have dropdowns, not on all menu items.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'param_name' => 'show_descriptions',
					'std' => 'false',
					'heading'   => \esc_html__( 'Show Descriptions', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'param_name' => 'hide_dropdowns',
					'std' => 'false',
					'heading' => \esc_html__( 'Hide Dropdowns', 'total-theme-core' ),
					'description' => \esc_html__( 'Dropdowns will open when the parent menu item is clicked. This option works best with the "Clean" or "Plain" style.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'choices' => [
						'' => esc_html__( 'Default', 'total-theme-core' ),
						'after_title' => esc_html__( 'After Title', 'total-theme-core' ),
						'before_title' => esc_html__( 'Before Title', 'total-theme-core' ),
					],
					'heading' => esc_html__( 'Arrow Position', 'total-theme-core' ),
					'param_name' => 'arrow_position',
					'dependency' => [ 'element' => 'hide_dropdowns', 'value' => 'false' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'param_name' => 'expand_active_dropdowns',
					'std' => 'true',
					'heading' => \esc_html__( 'Expand Active Dropdowns', 'total-theme-core' ),
					'description' => \esc_html__( 'Automatically expand dropdowns that include the active page.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'hide_dropdowns', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'param_name' => 'el_class',
					'heading' => \esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => \esc_html__( 'Add extra classes to the widget container.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
			];
		}

	}

}

new Vcex_Widget_Modern_Menu_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Widget_Modern_Menu' ) ) {
	class WPBakeryShortCode_Vcex_Widget_Modern_Menu extends WPBakeryShortCode {}
}
