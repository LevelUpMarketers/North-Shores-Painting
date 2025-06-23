<?php

defined( 'ABSPATH' ) || exit;

/**
 * Author Bio Shortcode.
 */
if ( ! class_exists( 'Vcex_Author_Bio_Shortcode' ) ) {

	class Vcex_Author_Bio_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_author_bio';

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
			return esc_html__( 'Author Bio', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display current author bio', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$alt_styles = [
				'alt-1',
				'alt-2',
				'alt-3',
				'alt-4',
				'alt-5'
			];

			return array(
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
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
				),
				// Style.
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'std' => 'default',
					'admin_label' => true,
					'value' => array(
						esc_html__( 'Theme Default', 'total-theme-core' ) => 'default',
						esc_html__( 'Alt 1', 'total-theme-core' )         => 'alt-1',
						esc_html__( 'Alt 2', 'total-theme-core' )         => 'alt-2',
						esc_html__( 'Alt 3', 'total-theme-core' )         => 'alt-3',
						esc_html__( 'Alt 4', 'total-theme-core' )         => 'alt-4',
						esc_html__( 'Alt 5', 'total-theme-core' )         => 'alt-5',
					),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'description' => esc_html__( 'Note: The "Theme Default" style has a 40px bottom margin by default.', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue.
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'max_width',
					'css' => [ 'property' => 'width' ],
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Aligment', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'center',
					'dependency' => array( 'element' => 'max_width', 'not_empty' => true ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background_color',
					'css' => true,
					'dependency' => array( 'element' => 'style', 'value' => $alt_styles ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'padding_all',
					'dependency' => array( 'element' => 'style', 'value' => $alt_styles ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'border_style',
					'dependency' => array( 'element' => 'style', 'value' => $alt_styles ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'dependency' => array( 'element' => 'style', 'value' => $alt_styles ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'css' => true,
					'dependency' => array( 'element' => 'style', 'value' => $alt_styles ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'dependency' => array( 'element' => 'style', 'value' => $alt_styles ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Avatar.
				array(
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Avatar Spacing', 'total-theme-core' ),
					'param_name' => 'avatar_spacing',
					'dependency' => array( 'element' => 'style', 'value' => $alt_styles ),
					'group' => esc_html__( 'Avatar', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Avatar Size', 'total-theme-core'),
					'param_name' => 'avatar_size',
					'dependency' => array( 'element' => 'style', 'value' => $alt_styles ),
					'group' => esc_html__( 'Avatar', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'border_radius',
					'supports_blobs' => true,
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'avatar_border_radius',
					'dependency' => array( 'element' => 'style', 'value' => $alt_styles ),
					'group' => esc_html__( 'Avatar', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Author Link.
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'On click action', 'total-theme-core' ),
					'param_name' => 'author_onclick',
					'dependency' => array( 'element' => 'style', 'value' => $alt_styles ),
					'std' => 'author_archive',
					'value' => array(
						esc_html__( 'Open author archive', 'total-theme-core' ) => 'author_archive',
						esc_html__( 'Open author website', 'total-theme-core' ) => 'author_website',
						esc_html__( 'Do nothing', 'total-theme-core' )          => 'null',
					),
					'group' => esc_html__( 'Author Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Title Attribute', 'total-theme-core' ),
					'param_name' => 'author_onclick_title',
					'dependency' => array(
						'element' => 'author_archive',
						'value' => array( 'author_archive', 'author_website' )
					),
					'group' => esc_html__( 'Author Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// CSS.
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'dependency' => array( 'element' => 'style', 'value' => $alt_styles ),
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				),
			);
		}

	}

}

new Vcex_Author_Bio_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_vcex_author_bio' ) ) {
	class WPBakeryShortCode_vcex_author_bio extends WPBakeryShortCode {}
}
