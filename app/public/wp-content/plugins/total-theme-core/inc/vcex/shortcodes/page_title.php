<?php

defined( 'ABSPATH' ) || exit;

/**
 * Page Title Shortcode.
 */
if ( ! class_exists( 'VCEX_Page_Title_Shortcode' ) ) {

	class VCEX_Page_Title_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_page_title';

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
			return esc_html__( 'Title', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Current page, post, term or archive title', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				array(
					'heading' => esc_html__( 'HTML Tag', 'total-theme-core' ),
					'param_name' => 'html_tag',
					'type' => 'vcex_select_buttons',
					'std' => 'h1',
					'choices' => array(
						'h1'   => 'h1',
						'h2'   => 'h2',
						'h3'   => 'h3',
						'h4'   => 'h4',
						'h5'   => 'h5',
						'div'  => 'div',
						'span' => 'span',
					),
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Before Text', 'total-theme-core' ),
					'param_name' => 'before_text',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'After Text', 'total-theme-core' ),
					'param_name' => 'after_text',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'width',
					'css' => true,
					'description' => self::param_description( 'width' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Aligment', 'total-theme-core' ),
					'param_name' => 'float',
					'std' => 'center',
					'dependency' => array( 'element' => 'width', 'not_empty' => true ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Link to Post?', 'total-theme-core' ),
					'param_name' => 'link_to_post',
					'description' => esc_html__( 'Adds a link to the current post (useful when creating card templates).', 'total-theme-core' ),
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
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				vcex_vc_map_add_css_animation(),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
					'editors' => [ 'wpbakery' ],
				),
				// Typography
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'text_align',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'color',
					'css' => [ 'selector' => '.vcex-page-title__heading' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => [ 'selector' => '.vcex-page-title__heading' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'css' => [ 'selector' => '.vcex-page-title__heading' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'text_transform',
					'css' => [ 'selector' => '.vcex-page-title__heading' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'font_weight',
					'css' => [ 'selector' => '.vcex-page-title__heading' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'line_height',
					'css' => [ 'selector' => '.vcex-page-title__heading' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'letter_spacing',
					'css' => [ 'selector' => '.vcex-page-title__heading' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'typography',
					'selector' => '.vcex-page-title__heading',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				),
				// Design options
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
			);
		}

	}

}

new VCEX_Page_Title_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Page_Title' ) ) {
	class WPBakeryShortCode_Vcex_Page_Title extends WPBakeryShortCode {}
}
