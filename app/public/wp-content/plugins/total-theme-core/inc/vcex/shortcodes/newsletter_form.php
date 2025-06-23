<?php

defined( 'ABSPATH' ) || exit;

/**
 * Newsletter Shortcode.
 */
if ( ! class_exists( 'VCEX_Newsletter_Shortcode' ) ) {

	class VCEX_Newsletter_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_newsletter_form';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Shortcode label.
		 */
		public static function get_title(): string {
			return esc_html__( 'Newsletter Form', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Newsletter subscription form', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				// General
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Form Action URL', 'total-theme-core' ),
					'param_name' => 'form_action',
					'admin_label' => true,
					'value'       => '',
					'description' => esc_html__( 'Enter your newsletter service form action URL.', 'total-theme-core' ) . ' <a href="https://totalwptheme.com/docs/mailchimp-form-action-url/" target="_blank" rel="noopener noreferrer">'. esc_html__( 'Learn More', 'total-theme-core' ) .' &rarr;</a>',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'description' => self::param_description( 'unique_id' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'classes',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				vcex_vc_map_add_css_animation(),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core' ),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core' ),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				// Style
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'input_width',
					'css' => [
						'selector' => '.vcex-newsletter-form-wrap',
						'property' => 'width',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'description' => self::param_description( 'width' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Alignment', 'total-theme-core' ),
					'param_name' => 'input_align',
					'std' => 'none',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => array( 'element' => 'input_width', 'not_empty' => true ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Stack Fields?', 'total-theme-core'),
					'param_name' => 'stack_fields',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Full-Width on Mobile', 'total-theme-core'),
					'param_name' => 'fullwidth_mobile',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => array( 'element' => 'stack_fields', 'value_not_equal_to' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Gap', 'total-theme-core' ),
					'param_name' => 'gap',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'description' => esc_html__( 'Spacing between the input field and the button.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Input
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Placeholder', 'total-theme-core' ),
					'param_name' => 'placeholder_text',
					'value' => esc_html__( 'Enter your email address', 'total-theme-core' ),
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Name Attribute', 'total-theme-core' ),
					'param_name' => 'input_name',
					'value' => 'EMAIL',
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'input_bg',
					'css' => [
						'selector' => 'input',
						'property' => 'background',
					],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'input_color',
					'css' => [
						'selector' => 'input',
						'property' => 'color',
					],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'input_border_color',
					'css' => [
						'selector' => 'input',
						'property' => 'border-color',
					],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Height', 'total-theme-core' ),
					'param_name' => 'input_height',
					'css' => [
						'selector' => 'input',
						'property' => 'height',
					],
					'description' => self::param_description( 'height' ),
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'input_padding',
					'css' => [ 'selector' => 'input', 'property' => 'padding' ],
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Border', 'total-theme-core' ),
					'param_name' => 'input_border',
					'css' => [ 'selector' => 'input', 'property' => 'border' ],
					'description' => self::param_description( 'border' ),
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'input_border_radius',
					'choices' => 'border_radius',
					'css' => [ 'selector' => 'input', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'input_font_size',
					'css' => [ 'selector' => 'input', 'property' => 'font_size' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'input_weight',
					'css' => [ 'selector' => 'input', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'input_letter_spacing',
					'choices' => 'letter_spacing',
					'css' => [ 'selector' => 'input', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'input_text_transform',
					'choices' => 'text_transform',
					'css' => [ 'selector' => 'input', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'input_typo',
					'selector' => '.vcex-newsletter-form input[type="email"]',
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				),
				// Submit
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Text', 'total-theme-core' ),
					'param_name' => 'submit_text',
					'std' => esc_html__( 'Sign Up', 'total-theme-core' ),
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'submit_bg',
					'css' => [ 'selector' => '.vcex-newsletter-form-button', 'property' => 'background' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'submit_hover_bg',
					'css' => [ 'selector' => '.vcex-newsletter-form-button:hover', 'property' => 'background' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'submit_color',
					'css' => [ 'selector' => '.vcex-newsletter-form-button', 'property' => 'color' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'submit_hover_color',
					'css' => [ 'selector' => '.vcex-newsletter-form-button:hover', 'property' => 'color' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Height', 'total-theme-core' ),
					'param_name' => 'submit_height',
					'css' => [ 'selector' => '.vcex-newsletter-form-button', 'property' => 'height' ],
					'description' => self::param_description( 'height' ),
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'submit_padding',
					'css' => [ 'selector' => '.vcex-newsletter-form-button', 'property' => 'padding' ],
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Border', 'total-theme-core' ),
					'param_name' => 'submit_border',
					'css' => [ 'selector' => '.vcex-newsletter-form-button', 'property' => 'border' ],
					'description' => self::param_description( 'border' ),
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'submit_border_radius',
					'choices' => 'border_radius',
					'css' => [ 'selector' => '.vcex-newsletter-form-button', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'submit_font_size',
					'css' => [ 'selector' => '.vcex-newsletter-form-button', 'property' => 'font-size' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'submit_weight',
					'css' => [ 'selector' => '.vcex-newsletter-form-button', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'submit_letter_spacing',
					'css' => [ 'selector' => '.vcex-newsletter-form-button', 'property' => 'letter-spacing' ],
					'choices' => 'letter_spacing',
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'submit_text_transform',
					'choices' => 'text_transform',
					'css' => [ 'selector' => '.vcex-newsletter-form-button', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'submit_typo',
					'selector' => '.vcex-newsletter-form-button',
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				),
				// Hidden Fields
				array(
					'type' => 'exploded_textarea',
					'heading' => esc_html__( 'Hidden Fields', 'total-theme-core' ),
					'param_name' => 'hidden_fields',
					'description' => esc_html__( 'Here you can define hidden fields to be added to the newsletter form. Enter each set of hidden fields using the format name|value. One per line.', 'total-theme-core' ),
					'group' => esc_html__( 'Hidden Fields', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Deprecated attributes.
				array( 'type' => 'hidden', 'param_name' => 'mailchimp_form_action' ),
			);
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}

			if ( ! empty( $atts['mailchimp_form_action'] ) ) {
				if ( empty( $atts['form_action'] ) ) {
					$atts['form_action'] = $atts['mailchimp_form_action'];
				}
				unset( $atts['mailchimp_form_action'] );
			}

			return $atts;
		}

	}

}

new VCEX_Newsletter_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Newsletter_Form' ) ) {
	class WPBakeryShortCode_Vcex_Newsletter_Form extends WPBakeryShortCode {}
}
