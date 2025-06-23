<?php

defined( 'ABSPATH' ) || exit;

/**
 * Star Rating Shortcode.
 */
if ( ! class_exists( 'Vcex_Star_Rating_Shortcode' ) ) {

	class Vcex_Star_Rating_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_star_rating';

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
			return esc_html__( 'Star Rating', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display a star rating', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'choices' => [
						'' => esc_html__( 'Plain', 'total-theme-core' ),
						'boxed' => esc_html__( 'Boxed', 'total-theme-core' ),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'std' => '0-5',
					'heading' => esc_html__( 'Scale', 'total-theme-core' ),
					'param_name' => 'scale',
					'choices' => [ '0-5' => '0-5', '0-10' => '0-10' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Value', 'total-theme-core' ),
					'param_name' => 'value',
					'admin_label' => true,
					'description' => esc_html__( 'Leave empty to display the current post rating.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Icon Type', 'total-theme-core' ),
					'param_name' => 'icon_type',
					'choices' => [
						'' => esc_html__( 'Theme Icon', 'total-theme-core' ),
						'material' => esc_html__( 'Material Design', 'total-theme-core' ),
					],
					'description' => self::param_description( 'text' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Show number', 'total-theme-core' ),
					'param_name' => 'show_number',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Label', 'total-theme-core' ),
					'param_name' => 'label',
					'admin_label' => true,
					'description' => self::param_description( 'text' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'std' => 'top',
					'heading' => esc_html__( 'Label Position', 'total-theme-core' ),
					'param_name' => 'label_position',
					'choices' => [
						'top' => esc_html__( 'Top', 'total-theme-core' ),
						'inline' => esc_html__( 'Inline', 'total-theme-core' ),
					],
					'dependency' => [ 'element' => 'label', 'not_empty' => true ],
					'description' => self::param_description( 'text' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
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
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Style.
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'align',
					'css' => [
						'property' => 'text_align',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Icon Size', 'total-theme-core' ),
					'param_name' => 'icon_font_size',
					'css' => [
						'selector' => '.vcex-star-rating__star',
						'property' => 'font-size',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Star Color', 'total-theme-core' ),
					'param_name' => 'star_color',
					'css' => [
						'selector' => '.vcex-star-rating__star',
						'property' => 'color',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Empty Background', 'total-theme-core' ),
					'param_name' => 'empty_bg',
					'css' => [
						'selector' => '.vcex-star-rating__star',
						'property' => 'background-color',
					],
					'dependency' => [ 'element' => 'style', 'value' => 'boxed' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Fill Background', 'total-theme-core' ),
					'param_name' => 'fill_bg',
					'css' => [
						'selector' => '.vcex-star-rating__star-fill',
						'property' => 'background-color',
					],
					'dependency' => [ 'element' => 'style', 'value' => 'boxed' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Label Color', 'total-theme-core' ),
					'param_name' => 'label_color',
					'css' => [
						'selector' => '.vcex-star-rating__label',
						'property' => 'color',
					],
					'dependency' => [ 'element' => 'label', 'not_empty' => true ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Number Color', 'total-theme-core' ),
					'param_name' => 'number_color',
					'css' => [
						'selector' => '.vcex-star-rating__number',
						'property' => 'color',
					],
					'dependency' => [ 'element' => 'show_number', 'value' => 'true' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Star Dimensions', 'total-theme-core' ),
					'param_name' => 'star_dims',
					'css' => [
						'selector' => '.vcex-star-rating__star',
						'property' => [ 'width', 'height' ],
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'style', 'value' => 'boxed' ],
					'description' => esc_html__( 'Enter a custom value for the width and height of each star.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Spacing Between Stars', 'total-theme-core' ),
					'param_name' => 'stars_gap',
					'choices' => 'gap',
					'css' => [
						'selector' => '.vcex-star-rating__stars',
						'property' => 'gap',
					],
					'description' => self::param_description( 'gap' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Label Spacing', 'total-theme-core' ),
					'param_name' => 'label_margin',
					'choices' => 'gap',
					'dependency' => [ 'element' => 'label', 'not_empty' => true ],
					'description' => self::param_description( 'gap' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Number Spacing', 'total-theme-core' ),
					'param_name' => 'number_margin',
					'choices' => 'gap',
					'css' => [
						'selector' => '.vcex-star-rating__number',
						'property' => 'margin-left',
					],
					'dependency' => [ 'element' => 'show_number', 'value' => 'true' ],
					'description' => self::param_description( 'gap' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Typography
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Label Font Size', 'total-theme-core' ),
					'param_name' => 'label_font_size',
					'css' => [
						'selector' => '.vcex-star-rating__label',
						'property' => 'font-size',
					],
					'dependency' => [ 'element' => 'label', 'not_empty' => true ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Label Font Weight', 'total-theme-core' ),
					'param_name' => 'label_font_weight',
					'choices' => 'font_weight',
					'css' => [
						'selector' => '.vcex-star-rating__label',
						'property' => 'font-weight',
					],
					'dependency' => [ 'element' => 'label', 'not_empty' => true ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Number Font Size', 'total-theme-core' ),
					'param_name' => 'number_font_size',
					'css' => [
						'selector' => '.vcex-star-rating__number',
						'property' => 'font-size',
					],
					'dependency' => [ 'element' => 'show_number', 'value' => 'true' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Number Font Weight', 'total-theme-core' ),
					'param_name' => 'number_font_weight',
					'choices' => 'font_weight',
					'css' => [
						'selector' => '.vcex-star-rating__number',
						'property' => 'font-weight',
					],
					'dependency' => [ 'element' => 'show_number', 'value' => 'true' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				// Elementor exclusive
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Label Font', 'total-theme-core' ),
					'param_name' => 'label_typography',
					'selector' => '.vcex-star-rating__label',
					'dependency' => [ 'element' => 'label', 'not_empty' => true ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Number Font', 'total-theme-core' ),
					'param_name' => 'number_typography',
					'selector' => '.vcex-star-rating__number',
					'dependency' => [ 'element' => 'show_number', 'value' => 'true' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
			];
		}

		/**
		 * Advanced CSS.
		 */
		protected static function css_pre_render( $css, $atts = [] ): void {
			if ( ! empty( $atts['label_margin'] ) ) {
				$label_position = ! empty( $atts['label_position'] ) ? sanitize_text_field( $atts['label_position'] ) : 'top';
				$css->add_extra_css( [
					'selector' => '.vcex-star-rating__label',
					'property' => 'inline' === $label_position ? 'margin-inline-end' : 'margin-block-end',
					'val' => $atts['label_margin'],
				] );
			}
		}

	}

}

new Vcex_Star_Rating_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Star_Rating' ) ) {
	class WPBakeryShortCode_Vcex_Star_Rating extends WPBakeryShortCode {}
}
