<?php

defined( 'ABSPATH' ) || exit;

/**
 * Leader Shortcode.
 */
if ( ! class_exists( 'VCEX_Leader_Shortcode' ) ) {

	class VCEX_Leader_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_leader';

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
			return esc_html__( 'Leader (Menu Items)', 'total-theme-core' );
		}

	   /**
		* Shortcode description.
		*/
		public static function get_description(): string {
			return esc_html__( 'CSS dot or line leader (menu item)', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$default = [
				[
					'label' => esc_html__( 'One', 'total-theme-core' ),
					'value' => '$10',
				],
				[
					'label' => esc_html__( 'Two', 'total-theme-core' ),
					'value' => '$20',
				],
			];
			return [
				[
					'type' => 'param_group',
					'param_name' => 'leaders',
					'value' => urlencode( json_encode( $default ) ),
					'params' => [
						[
							'type' => 'textfield',
							'heading' => esc_html__( 'Label', 'total-theme-core' ),
							'param_name' => 'label',
							'admin_label' => true,
							'elementor' => [
								'default' => esc_html__( 'Menu Item', 'total-theme-core' ),
							],
							'editors' => [ 'wpbakery', 'elementor' ],
						],
						[
							'type' => 'textfield',
							'heading' => esc_html__( 'Value', 'total-theme-core' ),
							'param_name' => 'value',
							'admin_label' => true,
							'elementor' => [
								'default' => '$10',
							],
							'editors' => [ 'wpbakery', 'elementor' ],
						],
					],
					'elementor' => [
						'title_field' => '{{{ label }}}',
						'default' => $default,
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				vcex_vc_map_add_css_animation(),
				// Style
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'std' => 'dots',
					'choices' => [
						'dots' => esc_html__( 'Dots', 'total-theme-core' ),
						'dashes' => esc_html__( 'Dashes', 'total-theme-core' ),
						'minimal' => esc_html__( 'Empty Space', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Responsive', 'total-theme-core' ),
					'param_name' => 'responsive',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Space Between Items', 'total-theme-core' ),
					'param_name' => 'spacing',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'color',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background',
					'css' => [ 'selector' => [ '.vcex-leader-item__label', '.vcex-leader-item__value' ], 'property' => 'background' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				// Label
				[
					'type' => 'vcex_colorpicker',
					'param_name' => 'label_color',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'group' => esc_html__( 'Label', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-leader-item__label', 'property' => 'color' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'label_font_family',
					'css' => [ 'selector' => '.vcex-leader-item__label', 'property' => 'font-family' ],
					'group' => esc_html__( 'Label', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'param_name' => 'label_font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-leader-item__label', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Label', 'total-theme-core' ),
				],
				[
					'heading' => esc_html__( 'Font Style', 'total-theme-core' ),
					'param_name' => 'label_font_style',
					'type' => 'vcex_select_buttons',
					'std' => '',
					'choices' => [
						'' => esc_html__( 'Normal', 'total-theme-core' ),
						'italic' => esc_html__( 'Italic', 'total-theme-core' ),
					],
					'css' => [ 'selector' => '.vcex-leader-item__label', 'property' => 'font-style' ],
					'group' => esc_html__( 'Label', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'label_typography',
					'selector' => '.vcex-leader-item__label',
					'group' => esc_html__( 'Label', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Value
				[
					'type' => 'vcex_colorpicker',
					'param_name' => 'value_color',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'group' => esc_html__( 'Value', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-leader-item__value', 'property' => 'color' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'value_font_family',
					'group' => esc_html__( 'Value', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-leader-item__value', 'property' => 'font-family' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'param_name' => 'value_font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'group' => esc_html__( 'Value', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-leader-item__value', 'property' => 'font-weight' ],
				],
				[
					'heading' => esc_html__( 'Font Style', 'total-theme-core' ),
					'param_name' => 'value_font_style',
					'type' => 'vcex_select_buttons',
					'std' => '',
					'choices' => [
						'' => esc_html__( 'Normal', 'total-theme-core' ),
						'italic' => esc_html__( 'Italic', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Value', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-leader-item__value', 'property' => 'font-style' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'value_typography',
					'selector' => '.vcex-leader-item__value',
					'group' => esc_html__( 'Value', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
			];
		}

	}

}

new VCEX_Leader_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Leader' ) ) {
	class WPBakeryShortCode_Vcex_Leader extends WPBakeryShortCode {}
}
