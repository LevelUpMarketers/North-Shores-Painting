<?php

defined( 'ABSPATH' ) || exit;

/**
 * Post Series Shortcode.
 */
if ( ! class_exists( 'VCEX_Post_Series_Shortcode' ) ) {

	class VCEX_Post_Series_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_post_series';

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
			return esc_html__( 'Post Series', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display your post series', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_notice',
					'param_name' => 'main_notice',
					'text' => esc_html__( 'You can also use the Post Cards element to showcase the post series. Simply set the Query type to "Related by Taxonomy," then select "Post Series" as the taxonomy to display posts from the current series.', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Max Width', 'total-theme-core' ),
					'param_name' => 'max_width',
					'css' => true,
					'description' => self::param_description( 'width' ),
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Aligment', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'center',
					'dependency' => [ 'element' => 'max_width', 'not_empty' => true ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background',
					'css' => [ 'selector' => '.wpex-post-series-toc', 'property' => 'background' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'padding',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'padding',
					'css' => [ 'selector' => '.wpex-post-series-toc', 'property' => 'padding' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'border_style',
					'css' => [ 'selector' => '.wpex-post-series-toc', 'property' => 'border-style' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'css' => [ 'selector' => '.wpex-post-series-toc', 'property' => 'border-width' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'css' => [ 'selector' => '.wpex-post-series-toc', 'property' => 'border-color' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'css' => [ 'selector' => '.wpex-post-series-toc', 'property' => 'border-radius' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Heading
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'heading_bottom_margin',
					'css' => [ 'selector' => '.wpex-post-series-toc-header', 'property' => 'margin-block-end' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'heading_color',
					'css' => [ 'selector' => '.wpex-post-series-toc-header', 'property' => 'color' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'heading_font_size',
					'css' => [ 'selector' => '.wpex-post-series-toc-header', 'property' => 'font-size' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'heading_font_weight',
					'css' => [ 'selector' => '.wpex-post-series-toc-header', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'letter_spacing',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'heading_letter_spacing',
					'css' => [ 'selector' => '.wpex-post-series-toc-header', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'line_height',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'heading_line_height',
					'css' => [ 'selector' => '.wpex-post-series-toc-header', 'property' => 'line-height' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				// List
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'margin',
					'heading' => esc_html__( 'Spacing', 'total-theme-core' ),
					'param_name' => 'list_space',
					'css' => [ 'selector' => '.wpex-post-series-toc-entry:not(:last-child)', 'property' => 'margin-block-end' ],
					'group' => esc_html__( 'List', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Active Post Color', 'total-theme-core' ),
					'param_name' => 'list_active_color',
					'css' => [ 'selector' => '.wpex-post-series-toc-active', 'property' => 'color' ],
					'group' => esc_html__( 'List', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Link Color', 'total-theme-core' ),
					'param_name' => 'list_link_color',
					'css' => [ 'selector' => '.wpex-post-series-toc-link', 'property' => 'color' ],
					'group' => esc_html__( 'List', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'list_font_size',
					'css' => [ 'selector' => '.wpex-post-series-toc-list', 'property' => 'font-size' ],
					'group' => esc_html__( 'List', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'list_font_weight',
					'css' => [ 'selector' => '.wpex-post-series-toc-list', 'property' => 'font-weight' ],
					'group' => esc_html__( 'List', 'total-theme-core' ),
				],
				// Number
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'number_color',
					'css' => [ 'selector' => '.wpex-post-series-toc-number', 'property' => 'color' ],
					'group' => esc_html__( 'Number', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'number_font_size',
					'css' => [ 'selector' => '.wpex-post-series-toc-number', 'property' => 'font-size' ],
					'group' => esc_html__( 'Number', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'number_font_weight',
					'css' => [ 'selector' => '.wpex-post-series-toc-number', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Number', 'total-theme-core' ),
				],
				// Elementor typography
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Element', 'total-theme-core' ),
					'param_name' => 'typography',
					'selector' => '.wpex-post-series-toc',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Heading', 'total-theme-core' ),
					'param_name' => 'heading_typography',
					'selector' => '.wpex-post-series-toc-header',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'List', 'total-theme-core' ),
					'param_name' => 'list_typography',
					'selector' => '.wpex-post-series-toc-list',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Number', 'total-theme-core' ),
					'param_name' => 'number_typography',
					'selector' => '.wpex-post-series-toc-number',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
			];
		}

	}

}

new VCEX_Post_Series_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Post_Series' ) ) {
	class WPBakeryShortCode_Vcex_Post_Series extends WPBakeryShortCode {}
}
