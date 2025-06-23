<?php

defined( 'ABSPATH' ) || exit;

/**
 * Form Shortcode.
 */
if ( ! class_exists( 'VCEX_Form_Shortcode' ) ) {

	class VCEX_Form_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_form_shortcode';

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
			return esc_html__( 'Form Shortcode', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Form shortcode with style', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$params = [
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Shortcode', 'total-theme-core' ),
					'param_name' => 'content',
					'admin_label' => true,
					'dependency' => [ 'element' => 'cf7_id', 'is_empty' => true ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'el_class',
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core'),
					'param_name' => 'animation_duration',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core'),
					'param_name' => 'animation_delay',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
				],
				// Style
				[
					'type' => 'vcex_select',
					'choices' => 'form_style',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'description' => esc_html__( 'The theme will try and apply the necessary styles to your form (works best with Contact Form 7) but remember every contact form plugin has their own styles so additional tweaks may be required.', 'total-theme-core' ),
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
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Full-Width Inputs', 'total-theme-core' ),
					'param_name' => 'full_width',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'text_align',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'width',
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'description' => self::param_description( 'font_size' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Text Color', 'total-theme-core' ),
					'param_name' => 'color',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background_color',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'padding_all',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'border_style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
			];

			if ( defined( 'WPCF7_VERSION' ) ) {
				array_unshift( $params, [
					'type'        => 'vcex_cf7_select',
					'heading'     => esc_html__( 'Contact Form 7 Select', 'total-theme-core' ),
					'param_name'  => 'cf7_id',
					'admin_label' => true,
				] );
			}

			return $params;
		}

	}

}

new VCEX_Form_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Form_Shortcode' ) ) {
	class WPBakeryShortCode_Vcex_Form_Shortcode extends WPBakeryShortCode {}
}