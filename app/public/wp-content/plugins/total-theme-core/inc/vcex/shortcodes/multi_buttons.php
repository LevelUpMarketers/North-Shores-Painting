<?php

defined( 'ABSPATH' ) || exit;

/**
 * Multi Buttons Shortcode.
 */
if ( ! class_exists( 'VCEX_Multi_Buttons_Shortcode' ) ) {

	class VCEX_Multi_Buttons_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_multi_buttons';

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
			return esc_html__( 'Multi-Buttons', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Multiple Buttons side by side', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				// Buttons
				array(
					'type' => 'param_group',
					'param_name' => 'buttons',
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'value' => urlencode( json_encode( array(
						array(
							'text' => esc_html__( 'Button 1', 'total-theme-core' ),
							'link' => 'url:#',
						),
						array(
							'text' => esc_html__( 'Button 2', 'total-theme-core' ),
							'link' => 'url:#',
						),
					) ) ),
					'params' => array(
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Text', 'total-theme-core' ),
							'param_name' => 'text',
							'admin_label' => true,
						),
						array(
							'type' => 'vc_link',
							'heading' => esc_html__( 'Link', 'total-theme-core' ),
							'param_name' => 'link',
						),
						array(
							'type' => 'vcex_select_buttons',
							'std' => 'flat',
							'heading' => esc_html__( 'Style', 'total-theme-core' ),
							'param_name' => 'style',
							'choices' => apply_filters( 'wpex_button_styles', array(
								'flat' => esc_html__( 'Flat', 'total-theme-core' ),
								'outline' => esc_html__( 'Outline', 'total-theme-core' ),
								'plain-text' => esc_html__( 'Plain Text', 'total-theme-core' ),
							) ),
						),
						array(
							'type' => 'vcex_button_colors',
							'heading' => esc_html__( 'Prefixed Color', 'total-theme-core' ),
							'param_name' => 'color',
							'description' => esc_html__( 'Custom color options can be added via a child theme.', 'total-theme-core' ),
						),
						array(
							'type' => 'vcex_colorpicker',
							'heading' => esc_html__( 'Custom Color', 'total-theme-core' ),
							'param_name' => 'custom_color',
						),
						array(
							'type' => 'vcex_colorpicker',
							'heading' => esc_html__( 'Custom Color: Hover', 'total-theme-core' ),
							'param_name' => 'custom_color_hover',
						),
						array(
							'type' => 'vcex_ofswitch',
							'heading' => esc_html__( 'Local Scroll', 'total-theme-core' ),
							'param_name' => 'local_scroll',
							'std' => 'false',
						),
						array(
							'type' => 'vcex_ofswitch',
							'std' => 'false',
							'heading' => esc_html__( 'Use Download Attribute?', 'total-theme-core' ),
							'param_name' => 'download_attribute',
						),
						vcex_vc_map_add_css_animation(),
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Animation Duration', 'total'),
							'param_name' => 'animation_duration',
							'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
						),
						array(
							'type' => 'textfield',
							'heading' => esc_html__( 'Animation Delay', 'total'),
							'param_name' => 'animation_delay',
							'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
						),
					),
				),
				// General
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'center',
					'exclude_choices' => array( '' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Button Width', 'total-theme-core' ),
					'param_name' => 'width',
					'css' => [
						'selector' => [ '.theme-button', '.theme-txt-link' ],
					],
					'description' => self::param_description( 'px' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'line_height',
					'css' => [
						'selector' => [ '.theme-button', '.theme-txt-link' ],
					],
					'description' => self::param_description( 'line_height' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Button Padding', 'total-theme-core' ),
					'param_name' => 'padding',
					'css' => [
						'selector' => [ '.theme-button', '.theme-txt-link' ],
					],
					'description' => self::param_description( 'padding' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Gap', 'total-theme-core' ),
					'param_name' => 'spacing',
					'css' => [ 'property' => 'gap' ],
					'description' => esc_html__( 'Enter a custom spacing in pixels that will be added between the buttons.', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'css' => [
						'selector' => [ '.theme-button', '.theme-txt-link' ],
						'property' => 'border-radius',
					],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'css' => [
						'selector' => [ '.theme-button', '.theme-txt-link' ],
						'property' => 'border-width',
					],
					'description' => esc_html__( 'Please enter a px value. This will control the border width when using the outline style button. Default is 3px.', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Full-Width on Small Screens', 'total-theme-core' ),
					'param_name' => 'small_screen_full_width',
					'description' => esc_html__( 'If enabled the buttons will render at 100% width on devices under 480px.', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
				),
				array(
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'total-theme-core' ),
				),
				// Typography
				array(
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'css' => [
						'selector' => [ '.theme-button', '.theme-txt-link' ],
					],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => [
						'selector' => [ '.theme-button', '.theme-txt-link' ],
					],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'letter_spacing',
					'css' => [
						'selector' => [ '.theme-button', '.theme-txt-link' ],
					],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'font_weight',
					'css' => [
						'selector' => [ '.theme-button', '.theme-txt-link' ],
					],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'text_transform',
					'css' => [
						'selector' => [ '.theme-button', '.theme-txt-link' ],
					],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
			);
		}

	}

}

new VCEX_Multi_Buttons_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Multi_Buttons' ) ) {
	class WPBakeryShortCode_Vcex_Multi_Buttons extends WPBakeryShortCode {}
}
