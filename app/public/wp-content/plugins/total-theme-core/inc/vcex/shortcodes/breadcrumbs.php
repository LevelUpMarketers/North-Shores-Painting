<?php

defined( 'ABSPATH' ) || exit;

/**
 * Breadcrumbs Shortcode.
 */
if ( ! class_exists( 'VCEX_Breadcrumbs_Shortcode' ) ) {

	class VCEX_Breadcrumbs_Shortcode extends \TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_breadcrumbs';

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
			return esc_html__( 'Breadcrumbs', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display page breadcrumbs', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Home Text', 'total-theme-core' ),
					'param_name' => 'home_text',
					'description' => esc_html__( 'Applies only to the theme default breadcrumbs not if you are using a plugin like Yoast SEO or Rank Math SEO or custom child theme output.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Separator', 'total-theme-core' ),
					'param_name' => 'separator',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'Arrow', 'total-theme-core' ) => 'arrow',
						esc_html__( 'Double Arrow', 'total-theme-core' ) => 'double_arrow',
						esc_html__( 'Dot', 'total-theme-core' ) => 'dot',
						esc_html__( 'Dash', 'total-theme-core' ) => 'dash',
						esc_html__( 'Long Dash', 'total-theme-core' ) => 'long_dash',
						esc_html__( 'Forward Slash', 'total-theme-core' ) => 'forward_slash',
						esc_html__( 'Backslash', 'total-theme-core' ) => 'backslash',
						esc_html__( 'Pipe', 'total-theme-core' ) => 'pipe',
						esc_html__( 'Angle', 'total-theme-core' ) => 'angle',
					],
					'description' => esc_html__( 'Applies only to the theme default breadcrumbs not if you are using a plugin like Yoast SEO or Rank Math SEO or custom child theme output.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Separator Color', 'total-theme-core' ),
					'param_name' => 'separator_color',
					'css' => [ 'selector' => '.sep', 'property' => 'color' ],
					'description' => esc_html__( 'Applies only to the theme default breadcrumbs not if you are using a plugin like Yoast SEO or Rank Math SEO or custom child theme output.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'margin',
					'heading' => esc_html__( 'Separator Margin', 'total-theme-core' ),
					'param_name' => 'separator_margin',
					'css' => [ 'selector' => '.sep', 'property' => 'margin-inline' ],
					'description' => esc_html__( 'Applies only to the theme default breadcrumbs not if you are using a plugin like Yoast SEO or Rank Math SEO or custom child theme output.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Ending Trail Title', 'total-theme-core' ),
					'label_block' => true,
					'param_name' => 'show_trail_end',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'Enabled', 'total-theme-core' ) => 'true',
						esc_html__( 'Disabled', 'total-theme-core' ) => 'false',
					],
					'description' => esc_html__( 'Applies only to the theme default breadcrumbs not if you are using a plugin like Yoast SEO or Rank Math SEO or custom child theme output.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Primary Post Term or Category Only', 'total-theme-core' ),
					'label_block' => true,
					'param_name' => 'first_term_only',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'Enabled', 'total-theme-core' ) => 'true',
						esc_html__( 'Disabled', 'total-theme-core' ) => 'false',
					],
					'description' => esc_html__( 'Applies only to the theme default breadcrumbs not if you are using a plugin like Yoast SEO or Rank Math SEO or custom child theme output.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Show Parent Pages', 'total-theme-core' ),
					'label_block' => true,
					'param_name' => 'show_parents',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'Enabled', 'total-theme-core' ) => 'true',
						esc_html__( 'Disabled', 'total-theme-core' ) => 'false',
					],
					'description' => esc_html__( 'Applies only to the theme default breadcrumbs not if you are using a plugin like Yoast SEO or Rank Math SEO or custom child theme output.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery' ],
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
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
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background_color',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'padding_all',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'border_style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				// Typography
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'align',
					'css' => [ 'property' => 'text-align' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'color',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'line_height',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'letter_spacing',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'heading' => esc_html__( 'Font Style', 'total-theme-core' ),
					'param_name' => 'font_style',
					'type' => 'vcex_select_buttons',
					'choices' => [
						'' => esc_html__( 'Normal', 'total-theme-core' ),
						'italic' => esc_html__( 'Italic', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Font', 'total-theme-core' ),
					'param_name' => 'typography',
					'selector' => '.vcex-breadcrumbs',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Links
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Use Text Color', 'total-theme-core' ),
					'param_name' => 'link_inherit_color',
					'std' => 'true',
					'description' => esc_html__( 'By default the breadcrumbs links inherit the text color. Disable this setting to use the default site link colors.', 'total-theme-core' ),
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'link_color',
					'css' => [ 'property' => '--wpex-link-color' ],
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'link_color_hover',
					'css' => [ 'property' => '--wpex-hover-link-color' ],
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Link Underline', 'total-theme-core' ),
					'param_name' => 'link_underline',
					'css' => [ 'property' => '--wpex-link-decoration-line' ],
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'Underline', 'total-theme-core' ) => 'true',
						esc_html__( 'No underline', 'total-theme-core' ) => 'false',
					],
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Link Underline: Hover', 'total-theme-core' ),
					'param_name' => 'link_underline_hover',
					'css' => [ 'property' => '--wpex-hover-link-decoration-line' ],
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'Underline', 'total-theme-core' ) => 'true',
						esc_html__( 'No underline', 'total-theme-core' ) => 'false',
					],
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
			];
		}

	}

}

new VCEX_Breadcrumbs_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Breadcrumbs' ) ) {
	class WPBakeryShortCode_Vcex_Breadcrumbs extends WPBakeryShortCode {}
}
