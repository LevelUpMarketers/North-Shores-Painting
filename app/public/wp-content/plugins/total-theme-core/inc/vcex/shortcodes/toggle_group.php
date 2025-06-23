<?php

defined( 'ABSPATH' ) || exit;

/**
 * Toggle Group Shortcode.
 */
if ( ! class_exists( 'Vcex_Toggle_Group_Shortcode' ) ) {

	class Vcex_Toggle_Group_Shortcode extends \TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_toggle_group';

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
			return \esc_html__( 'Toggle (FAQ) Group', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Create an accordion using toggle elements', 'total-theme-core' );
		}

		/**
		 * Returns custom vc map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			return [
				'allowed_container_element' => false,
				'is_container' => true,
				'content_element' => true,
				'js_view' => 'VcColumnView',
				'as_parent' => [ 'only' => 'vcex_toggle' ],
			];
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Animate', 'total-theme-core' ),
					'description' => esc_html__( 'Important: Because of how WPBakery works when changing this option the change will not be visible in the frontend editor but the changes will be reflected on the live site.', 'total-theme-core' ),
					'param_name' => 'animate',
					'std' => 'true',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'FAQ Markup', 'total-theme-core' ),
					'param_name' => 'faq_microdata',
					'std' => 'false',
					'description' => esc_html__( 'Enable to include FAQ microdata markup for use with FAQ schema page types.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Animation Speed', 'total-theme-core' ),
					'description' => esc_html__( 'Important: Because of how WPBakery works when changing this option the change will not be visible in the frontend editor but the changes will be reflected on the live site.', 'total-theme-core' ),
					'param_name' => 'animation_speed',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						'75ms' => '75',
						'100ms' => '100',
						'150ms' => '150',
						'200ms' => '200',
						'300ms' => '300',
						'400ms' => '400',
						'500ms' => '500',
						'600ms' => '600',
						'700ms' => '700',
						'1000ms' => '1000',
					],
					'dependency' => [ 'element' => 'animate', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
				],
				[
					'type' => 'textfield',
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
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				],
				// Style
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'value' => [
						esc_html__( 'With Borders', 'total-theme-core' ) => 'w-borders',
						esc_html__( 'None', 'total-theme-core' ) => 'none',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Remove First Border', 'total-theme-core' ),
					'param_name' => 'no_top_border',
					'dependency' => [ 'element' => 'style', 'value' => 'w-borders' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Remove Last Border', 'total-theme-core' ),
					'param_name' => 'no_bottom_border',
					'dependency' => [ 'element' => 'style', 'value' => 'w-borders' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Max Width', 'total-theme-core' ),
					'param_name' => 'max_width',
					'css' => true,
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'css' => [
						'selector' => '.vcex-toggle',
						'property' => '--vcex-border-color',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'style', 'value' => 'w-borders' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Border Spacing', 'total-theme-core' ),
					'param_name' => 'border_spacing',
					'css' => [ 'property' => '--vcex-spacing' ],
					'description' => self::param_description( 'margin' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'style', 'value' => 'w-borders' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Gap Between Toggles', 'total-theme-core' ),
					'param_name' => 'gap',
					'css' => [ 'property' => '--vcex-spacing' ],
					'description' => self::param_description( 'margin' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'style', 'value' => 'none' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'choices' => [
						'' => esc_html__( 'Default', 'total-theme-core' ),
						'1px' => '1px',
						'2px' => '2px',
						'3px' => '3px',
						'4px' => '4px',
					],
					'css' => [
						'selector' => '.vcex-toggle',
						'property' => '--vcex-border-width',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'style', 'value' => 'w-borders' ],
				],
				// Headings
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Inline Heading', 'total-theme-core' ),
					'param_name' => 'heading_inline',
					'std' => 'false',
					'description' => esc_html__( 'Enable to display the heading inline so any white space next to the heading is not clickable.', 'total-theme-core' ),
					'group' => esc_html__( 'Headings', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_family_select',
					'choices' => 'heading_font_family',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'heading_font_family',
					'css' => [
						'selector' => '.vcex-toggle__heading',
						'property' => 'font-family',
					],
					'group' => esc_html__( 'Headings', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'heading_font_size',
					'css' => [
						'selector' => '.vcex-toggle__heading',
						'property' => 'font-size',
					],
					'group' => esc_html__( 'Headings', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'heading_color',
					'css' => [
						'selector' => '.vcex-toggle__trigger',
						'property' => 'color',
					],
					'group' => esc_html__( 'Headings', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'heading_color_hover',
					'css' => [
						'selector' => '.vcex-toggle__trigger:hover',
						'property' => 'color',
					],
					'group' => esc_html__( 'Headings', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Active', 'total-theme-core' ),
					'param_name' => 'heading_color_active',
					'css' => [
						'selector' => '.vcex-toggle__trigger[aria-expanded="true"]',
						'property' => 'color',
					],
					'group' => esc_html__( 'Headings', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'heading_font_weight',
					'css' => [
						'selector' => '.vcex-toggle__heading',
						'property' => 'font-weight',
					],
					'group' => esc_html__( 'Headings', 'total-theme-core' ),
				],
				// Icons
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Icon Type', 'total-theme-core' ),
					'description' => esc_html__( 'Important: Because of how WPBakery works when changing this option the change will not be visible in the frontend editor but the changes will be reflected on the live site.', 'total-theme-core' ),
					'param_name' => 'icon_type',
					'std' => 'plus',
					'choices' => [
						'plus' => esc_html__( 'Plus', 'total-theme-core' ),
						'angle' => esc_html__( 'Angle', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Icon Position', 'total-theme-core' ),
					'param_name' => 'icon_position',
					'std' => 'left',
					'choices' => [
						'left' => esc_html__( 'Left', 'total-theme-core' ),
						'right' => esc_html__( 'Right', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Dimensions', 'total-theme-core' ),
					'description' => esc_html__( 'Controls the height and width of the icon wrapper div.', 'total-theme-core' ),
					'param_name' => 'icon_dims',
					'css' => [
						'selector' => '.vcex-toggle__icon',
						'property' => [ 'width', 'height' ],
					],
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Size', 'total-theme-core' ),
					'description' => esc_html__( 'Controls the height and width of the icon SVG.', 'total-theme-core' ),
					'param_name' => 'icon_size',
					'css' => [
						'selector' => '.vcex-toggle__icon svg',
						'property' => [ 'width', 'height' ],
					],
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Icon Color', 'total-theme-core' ),
					'param_name' => 'icon_color',
					'css' => [
						'selector' => '.vcex-toggle__icon',
						'property' => 'color',
					],
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Icon Background', 'total-theme-core' ),
					'param_name' => 'icon_background',
					'css' => [
						'selector' => '.vcex-toggle__icon',
						'property' => 'background-color',
					],
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_radius',
					'heading' => esc_html__( 'Icon Border Radius', 'total-theme-core' ),
					'param_name' => 'icon_border_radius',
					'css' => [
						'selector' => '.vcex-toggle__icon',
						'property' => 'border-radius',
					],
					'dependency' => [ 'element' => 'icon_background', 'not_empty' => true ],
					'group' => esc_html__( 'Icons', 'total-theme-core' ),
				],
				// Content
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'content_font_size',
					'css' => [
						'selector' => '.vcex-toggle__content',
						'property' => 'font-size',
					],
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'content_color',
					'css' => [
						'selector' => '.vcex-toggle__content',
						'property' => 'color',
					],
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				],
				// Design
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				// Hidden
				[ 'type' => 'hidden', 'param_name' => 'parse_content', 'std' => 'true' ]
			];
		}

	}

}

new Vcex_Toggle_Group_Shortcode;

if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Toggle_Group' ) ) {
	class WPBakeryShortCode_Vcex_Toggle_Group extends WPBakeryShortCodesContainer {}
}
