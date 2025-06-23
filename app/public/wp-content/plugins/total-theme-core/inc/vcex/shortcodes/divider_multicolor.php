<?php

defined( 'ABSPATH' ) || exit;

/**
 * Divider Multi-Color Shortcode.
 */
if ( ! class_exists( 'Vcex_Multi_Color_Divider_Shortcode' ) ) {

	class Vcex_Multi_Color_Divider_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_divider_multicolor';

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
			return esc_html__( 'Divider - Multicolor', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'A multicolor divider', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$default_colors = [
				[ 'value' => '#301961' ],
				[ 'value' => '#452586' ],
				[ 'value' => '#301961' ],
				[ 'value' => '#5f3aae' ],
				[ 'value' => '#01c1a8' ],
				[ 'value' => '#11e2c5' ],
				[ 'value' => '#6ffceb' ],
				[ 'value' => '#b0fbff' ],
			];
			return array(
				[
					'type' => 'param_group',
					'param_name' => 'colors',
					'value' => urlencode( json_encode( $default_colors ) ),
					'params' => [
						[
							'type' => 'colorpicker',
							'heading' => esc_html__( 'Color', 'total-theme-core' ),
							'param_name' => 'value',
							'admin_label' => true,
							'elementor' => [
								'default' => '#301961',
							],
							'editors' => [ 'wpbakery', 'elementor' ],
						],
					],
					'elementor' => [
						'title_field' => '{{{ value }}}',
						'default' => $default_colors,
					],
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
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'width',
					'placeholder' => '100%',
					'css' => [ 'property' => 'max-width' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'center',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Margin Bottom', 'total-theme-core' ) . ' ' . esc_html__( '(legacy option)', 'total-theme-core' ),
					'description' => self::param_description( 'margin' ),
					'param_name' => 'margin_bottom',
					'css' => [ 'property' => 'margin-block-end' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Height', 'total-theme-core' ),
					'param_name' => 'height',
					'description' => self::param_description( 'px' ),
					'placeholder' => '8px',
					'css' => [ 'selector' => '.vcex-divider-multicolor__item' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
			);
		}

	}

}

new Vcex_Multi_Color_Divider_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Divider_Multicolor' ) ) {
	class WPBakeryShortCode_Vcex_Divider_Multicolor extends WPBakeryShortCode {}
}
