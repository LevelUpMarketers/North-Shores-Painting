<?php

defined( 'ABSPATH' ) || exit;

/**
 * Divider Dots Shortcode.
 */
if ( ! class_exists( 'VCEX_Divider_Dots_Shortcode' ) ) {

	class VCEX_Divider_Dots_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_divider_dots';

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
			return esc_html__( 'Divider Dots', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Dot Separator', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				// General
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'center',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'color',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Count', 'total-theme-core' ),
					'param_name' => 'count',
					'value' => '3',
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Spacing', 'total-theme-core' ),
					'param_name' => 'spacing',
					'choices' => 'margin',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Size', 'total-theme-core' ),
					'param_name' => 'size',
					'choices' => [
						'' => esc_html__( 'Default', 'total-theme-core' ),
						'sm' => esc_html__( 'Small', 'total-theme-core' ),
						'md' => esc_html__( 'Medium', 'total-theme-core' ),
						'lg' => esc_html__( 'Large', 'total-theme-core' ),
						'xl' => esc_html__( 'Extra large', 'total-theme-core' ),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'description' => self::param_description( 'padding' ),
					'param_name' => 'margin',
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
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
				],
				// Hidden Removed attributes
				[ 'type' => 'hidden', 'param_name' => 'margin_top' ],
				[ 'type' => 'hidden', 'param_name' => 'margin_bottom' ],
			];
		}

		/**
		 * Parse attributes.
		 */
		public static function parse_deprecated_attributes( $atts ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}

			if ( empty( $atts['margin'] ) ) {
				$margin_top = isset( $atts['margin_top'] ) ?  $atts['margin_top'] : '';
				$margin_bottom = isset( $atts['margin_bottom'] ) ?  $atts['margin_bottom'] : '';
				if ( $margin_top || $margin_bottom ) {
					$atts['margin'] = vcex_combine_trbl_fields( $margin_top, '', $margin_bottom, '' );
					unset( $atts['margin_top'] );
					unset( $atts['margin_bottom'] );
				}
			}

			return $atts;
		}

	}
}

new VCEX_Divider_Dots_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Divider_Dots' ) ) {
	class WPBakeryShortCode_Vcex_Divider_Dots extends WPBakeryShortCode {}
}
