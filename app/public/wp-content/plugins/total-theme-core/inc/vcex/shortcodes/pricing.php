<?php

defined( 'ABSPATH' ) || exit;

/**
 * Pricing Shortcode.
 */
if ( ! class_exists( 'VCEX_Pricing_Shortcode' ) ) {

	class VCEX_Pricing_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_pricing';

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
			return esc_html__( 'Pricing Table', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Insert a pricing column', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				// General
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'choices' => [
					'' => esc_html__( 'Default', 'total-theme-core' ),
					'alt-1' => esc_html__( 'Alt 1', 'total-theme-core' ),
					'alt-2' => esc_html__( 'Alt 2', 'total-theme-core' ),
					'alt-3' => esc_html__( 'Alt 3', 'total-theme-core' ),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textarea_html',
				//	'holder' => 'div',  // adding this will make the item display in the backend editor.
					'heading' => esc_html__( 'Features List', 'total-theme-core' ),
					'param_name' => 'content',
					'value' => '<ul>
					<li>30GB Storage</li>
					<li>512MB Ram</li>
					<li>10 databases</li>
					<li>1,000 Emails</li>
					<li>25GB Bandwidth</li>
					</ul>',
					'description' => esc_html__( 'Enter your pricing content. You can use a UL list as shown by default but anything would really work!', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'content_typo',
					'selector' => '.vcex-pricing-content',
					'editors' => [ 'elementor' ],
				],
				[
					'type' => 'vcex_hover_animations',
					'heading' => esc_html__( 'Hover Animation', 'total-theme-core'),
					'param_name' => 'hover_animation',
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'el_class',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				],
				// Style
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Advanced Styling Options', 'total-theme-core' ),
					'param_name' => 'advanced_settings',
					'std' => 'true',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'description' => esc_html__( 'Important: If you disable this option and save your module it will reset your styling options.', 'total-theme-core' ),
				],
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
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background_color',
					'css' => [ 'property' => 'background-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'css' => [ 'property' => 'border-radius' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'css' => [ 'property' => 'border-width' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'border_style',
					'css' => [ 'property' => 'border-style' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'border_width', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'css' => [ 'property' => 'border-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'border_style', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Plan
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Featured', 'total-theme-core'),
					'param_name' => 'featured',
					'group' => esc_html__( 'Plan', 'total-theme-core' ),
					'std' => 'no',
					'vcex' => [ 'on'  => 'yes', 'off' => 'no' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Plan', 'total-theme-core' ),
					'param_name' => 'plan',
					'group' => esc_html__( 'Plan', 'total-theme-core' ),
					'std' => esc_html__( 'Basic', 'total-theme-core' ),
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'plan_weight',
					'css' => [ 'selector' => '.vcex-pricing-plan', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Plan', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'plan_font_family',
					'css' => [ 'selector' => '.vcex-pricing-plan', 'property' => 'font-family' ],
					'group' => esc_html__( 'Plan', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'plan_text_transform',
					'choices' => 'text_transform',
					'css' => [ 'selector' => '.vcex-pricing-plan', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Plan', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'plan_size',
					'css' => [ 'selector' => '.vcex-pricing-plan', 'property' => 'font-size' ],
					'group' => esc_html__( 'Plan', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'plan_letter_spacing',
					'css' => [ 'selector' => '.vcex-pricing-plan', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Plan', 'total-theme-core' ),
					'choices' => 'letter_spacing',
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'plan_background',
					'group' => esc_html__( 'Plan', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-plan', 'property' => 'background' ],
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'plan_color',
					'group' => esc_html__( 'Plan', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-plan', 'property' => 'color' ],
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'plan_padding',
					'group' => esc_html__( 'Plan', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-plan', 'property' => 'padding' ],
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'plan_margin',
					'group' => esc_html__( 'Plan', 'total-theme-core' ),
					'description' => self::param_description( 'margin' ),
					'css' => [ 'selector' => '.vcex-pricing-plan', 'property' => 'margin' ],
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Border', 'total-theme-core' ),
					'param_name' => 'plan_border',
					'description' => self::param_description( 'border' ),
					'css' => [ 'selector' => '.vcex-pricing-plan', 'property' => 'border' ],
					'group' => esc_html__( 'Plan', 'total-theme-core' ),
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'plan_typo',
					'selector' => '.vcex-pricing-plan',
					'group' => esc_html__( 'Plan', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Cost
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Cost', 'total-theme-core' ),
					'param_name' => 'cost',
					'group' => esc_html__( 'Cost', 'total-theme-core' ),
					'std' => '$20',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'cost_font_family',
					'group' => esc_html__( 'Cost', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'cost_weight',
					'css' => [ 'selector' => '.vcex-pricing-ammount', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Cost', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'cost_size',
					'css' => [ 'selector' => '.vcex-pricing-ammount', 'property' => 'font-size' ],
					'group' => esc_html__( 'Cost', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'cost_background',
					'group' => esc_html__( 'Cost', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-cost', 'property' => 'background' ],
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'cost_color',
					'css' => [ 'selector' => '.vcex-pricing-ammount', 'property' => 'color' ],
					'group' => esc_html__( 'Cost', 'total-theme-core' ),
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'cost_padding',
					'css' => [ 'selector' => '.vcex-pricing-cost', 'property' => 'padding' ],
					'group' => esc_html__( 'Cost', 'total-theme-core' ),
					'description' => self::param_description( 'padding' ),
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Border', 'total-theme-core' ),
					'param_name' => 'cost_border',
					'description' => self::param_description( 'border' ),
					'css' => [ 'selector' => '.vcex-pricing-cost', 'property' => 'border' ],
					'group' => esc_html__( 'Cost', 'total-theme-core' ),
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'cost_typo',
					'selector' => '.vcex-pricing-ammount',
					'group' => esc_html__( 'Cost', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Per
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Per', 'total-theme-core' ),
					'param_name' => 'per',
					'group' => esc_html__( 'Per', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Display', 'total-theme-core' ),
					'param_name' => 'per_display',
					'std' => '',
					'choices' => [
						'' => esc_html__( 'Default', 'total-theme-core' ),
						'inline' => esc_html__( 'Inline', 'total-theme-core' ),
						'block' => esc_html__( 'Block', 'total-theme-core' ),
						'inline-block' => esc_html__( 'Inline-Block', 'total-theme-core' ),
					],
					'css' => [ 'selector' => '.vcex-pricing-per', 'property' => 'display' ],
					'group' => esc_html__( 'Per', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'per_font_family',
					'css' => [ 'selector' => '.vcex-pricing-per', 'property' => 'font-family' ],
					'group' => esc_html__( 'Per', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'per_weight',
					'css' => [ 'selector' => '.vcex-pricing-per', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Per', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'per_transform',
					'choices' => 'text_transform',
					'css' => [ 'selector' => '.vcex-pricing-per', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Per', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'per_size',
					'css' => [ 'selector' => '.vcex-pricing-per', 'property' => 'font-size' ],
					'group' => esc_html__( 'Per', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'per_color',
					'group' => esc_html__( 'Per', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-per', 'property' => 'color' ],
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'per_typo',
					'selector' => '.vcex-pricing-per',
					'group' => esc_html__( 'Per', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Features
				[
					'type' => 'vcex_notice',
					'param_name' => 'main_notice',
					'text' => esc_html__( 'Visit the "General" tab to edit the features list.', 'total-theme-core' ),
					'group' => esc_html__( 'Features', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'css' => [ 'selector' => '.vcex-pricing-content', 'property' => 'font-family' ],
					'group' => esc_html__( 'Features', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => [ 'selector' => '.vcex-pricing-content', 'property' => 'font-size' ],
					'group' => esc_html__( 'Features', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'line_height',
					'css' => [ 'selector' => '.vcex-pricing-content', 'property' => 'line-height' ],
					'group' => esc_html__( 'Features', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'font_color',
					'group' => esc_html__( 'Features', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-content', 'property' => 'color' ],
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'features_bg',
					'group' => esc_html__( 'Features', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-content', 'property' => 'background' ],
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'features_padding',
					'group' => esc_html__( 'Features', 'total-theme-core' ),
					'description' => self::param_description( 'padding' ),
					'css' => [ 'selector' => '.vcex-pricing-content', 'property' => 'padding' ],
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Border', 'total-theme-core' ),
					'param_name' => 'features_border',
					'description' => self::param_description( 'border' ),
					'group' => esc_html__( 'Features', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-content', 'property' => 'border' ],
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Button
				[
					'type' => 'textarea_raw_html',
					'heading' => esc_html__( 'Custom Button HTML', 'total-theme-core' ),
					'param_name' => 'custom_button',
					'description' => esc_html__( 'Enter your custom button HTML, such as your paypal button code.', 'total-theme-core' ),
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vc_link',
					'heading' => esc_html__( 'URL', 'total-theme-core' ),
					'param_name' => 'button_url',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'description' => self::param_description( 'text' ),
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Local Scroll?', 'total-theme-core' ),
					'param_name' => 'button_local_scroll',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Text', 'total-theme-core' ),
					'param_name' => 'button_text',
					'value' => esc_html__( 'Text', 'total-theme-core' ),
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'description' => self::param_description( 'text' ),
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_button_styles',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'button_style',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_button_colors',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'button_style_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'button_font_family',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-button', 'property' => 'font-family' ],
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'button_weight',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-button__link', 'property' => 'font-weight' ],
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'button_transform',
					'choices' => 'text_transform',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'button_size',
					'css' => [ 'selector' => '.vcex-pricing-button__link', 'property' => 'font-size' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'button_bg_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-button__link', 'property' => 'background' ],
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'button_hover_bg_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-button__link:hover', 'property' => 'background' ],
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'button_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-button__link', 'property' => 'color' ],
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'button_hover_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-button__link:hover', 'property' => 'color' ],
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'button_border_width',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-button__link', 'property' => 'border-width' ],
					'description' => self::param_description( 'border_width' ),
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'button_border_radius',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'choices' => 'border_radius',
					'css' => [ 'selector' => '.vcex-pricing-button__link', 'property' => 'border-radius' ],
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'button_letter_spacing',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'choices' => 'letter_spacing',
					'css' => [ 'selector' => '.vcex-pricing-button__link', 'property' => 'letter-spacing' ],
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Button Padding', 'total-theme-core' ),
					'param_name' => 'button_padding',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'description' => self::param_description( 'padding' ),
					'css' => [ 'selector' => '.vcex-pricing-button__link', 'property' => 'padding' ],
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Area Background', 'total-theme-core' ),
					'param_name' => 'button_wrap_bg',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-button', 'property' => 'background' ],
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Area Padding', 'total-theme-core' ),
					'param_name' => 'button_wrap_padding',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'description' => self::param_description( 'padding' ),
					'css' => [ 'selector' => '.vcex-pricing-button', 'property' => 'padding' ],
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Area Border', 'total-theme-core' ),
					'param_name' => 'button_wrap_border',
					'description' => self::param_description( 'border' ),
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-pricing-button', 'property' => 'border' ],
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'button_typo',
					'selector' => '.vcex-pricing-button',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				//Icons
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Icon library', 'total-theme-core' ),
					'param_name' => 'icon_type',
					'description' => esc_html__( 'For optimal site speed, it\'s strongly recommended to use the theme\'s built-in icon library or upload a custom icon.', 'total-theme-core' ),
					'value' => [
						esc_html__( 'Theme Icons', 'total-theme-core' ) => 'ticons',
						esc_html__( 'Font Awesome', 'total-theme-core' ) => 'fontawesome',
						esc_html__( 'Open Iconic', 'total-theme-core' ) => 'openiconic',
						esc_html__( 'Typicons', 'total-theme-core' ) => 'typicons',
						esc_html__( 'Entypo', 'total-theme-core' ) => 'entypo',
						esc_html__( 'Linecons', 'total-theme-core' ) => 'linecons',
						esc_html__( 'Pixel', 'total-theme-core' ) => 'pixelicons',
					],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_button', 'is_empty' => true ],
				],
				[
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Icon Left', 'total-theme-core' ),
					'param_name' => 'button_icon_left',
					'dependency' => [ 'element' => 'icon_type', 'value' => 'ticons' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Left', 'total-theme-core' ),
					'param_name' => 'button_icon_left_fontawesome',
					'settings' => [
						'emptyIcon' => true,
						'iconsPerPage' => 100,
						'type' => 'fontawesome',
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'fontawesome' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Left', 'total-theme-core' ),
					'param_name' => 'button_icon_left_openiconic',
					'settings' => [
						'emptyIcon' => true,
						'type' => 'openiconic',
						'iconsPerPage' => 100,
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'openiconic' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Left', 'total-theme-core' ),
					'param_name' => 'button_icon_left_typicons',
					'settings' => [
						'emptyIcon' => true,
						'type' => 'typicons',
						'iconsPerPage' => 100,
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'typicons' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Left', 'total-theme-core' ),
					'param_name' => 'button_icon_left_entypo',
					'settings' => [
						'emptyIcon' => true,
						'type' => 'entypo',
						'iconsPerPage' => 300,
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'entypo' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Left', 'total-theme-core' ),
					'param_name' => 'button_icon_left_linecons',
					'settings' => [
						'emptyIcon' => true,
						'type' => 'linecons',
						'iconsPerPage' => 100,
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'linecons' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Left', 'total-theme-core' ),
					'param_name' => 'button_icon_left_pixelicons',
					'settings' => [
						'emptyIcon' => false,
						'type' => 'pixelicons',
						'source' => vcex_pixel_icons(),
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'pixelicons' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Icon Right', 'total-theme-core' ),
					'param_name' => 'button_icon_right',
					'dependency' => [ 'element' => 'icon_type', 'value' => 'ticons' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Right', 'total-theme-core' ),
					'param_name' => 'button_icon_right_fontawesome',
					'settings' => [ 'emptyIcon' => true, 'iconsPerPage' => 100, 'type' => 'fontawesome' ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'fontawesome' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Right', 'total-theme-core' ),
					'param_name' => 'button_icon_right_openiconic',
					'settings' => [
						'emptyIcon' => true,
						'type' => 'openiconic',
						'iconsPerPage' => 100,
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'openiconic' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Right', 'total-theme-core' ),
					'param_name' => 'button_icon_right_typicons',
					'settings' => [
					'emptyIcon' => true,
						'type' => 'typicons',
						'iconsPerPage' => 100,
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'typicons' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Right', 'total-theme-core' ),
					'param_name' => 'button_icon_right_entypo',
					'settings' => [
						'emptyIcon' => true,
						'type' => 'entypo',
						'iconsPerPage' => 300,
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'entypo' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Right', 'total-theme-core' ),
					'param_name' => 'button_icon_right_linecons',
					'settings' => [
						'emptyIcon' => true,
						'type' => 'linecons',
						'iconsPerPage' => 100,
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'linecons' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Right', 'total-theme-core' ),
					'param_name' => 'button_icon_right_pixelicons',
					'settings' => [
						'emptyIcon' => false,
						'type' => 'pixelicons',
						'source' => vcex_pixel_icons(),
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'pixelicons' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Left Icon: Hover Transform x', 'total-theme-core' ),
					'param_name' => 'button_icon_left_transform',
					'css' => [ 'selector' => '.theme-button-icon-left', 'property' => '--wpex-btn-icon-animate-h' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Right Icon: Hover Transform x', 'total-theme-core' ),
					'param_name' => 'button_icon_right_transform',
					'css' => [ 'selector' => '.theme-button-icon-right', 'property' => '--wpex-btn-icon-animate-h' ],
					'group' => esc_html__( 'Button Icons', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
					'dependency' => [ 'element' => 'advanced_settings', 'value' => 'true' ],
				],
			];
		}

		/**
		 * Parse WPBakery attributes on edit.
		 */
		public static function vc_edit_form_fields_attributes( $atts = [] ) {
			if ( ! empty( $atts['button_url'] )
				&& is_string( $atts['button_url'] )
				&& ! str_contains( $atts['button_url'], 'url:' )
				&& '|' !== $atts['button_url']
				&& '||' !== $atts['button_url']
				&& '|||' !== $atts['button_url']
			) {
				$url = 'url:' . \rawurlencode( $atts['button_url'] ) . '|';
				$atts['button_url'] = $url;
			}
			return $atts;
		}

	}

}

new VCEX_Pricing_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Pricing' ) ) {
	class WPBakeryShortCode_Vcex_Pricing extends WPBakeryShortCode {}
}
