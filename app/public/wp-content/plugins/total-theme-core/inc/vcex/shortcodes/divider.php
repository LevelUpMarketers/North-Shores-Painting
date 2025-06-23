<?php

defined( 'ABSPATH' ) || exit;

/**
 * Divider Shortcode.
 */
if ( ! class_exists( 'Vcex_Divider_Shortcode' ) ) {

	class Vcex_Divider_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_divider';

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
			return esc_html__( 'Divider', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Line Separator with optional icon', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				// General
				[
					'type' => 'vcex_select_buttons',
					'admin_label' => true,
					'heading' => esc_html__( 'Type', 'total-theme-core' ),
					'param_name' => 'style',
					'std' => 'solid',
					'choices' => [
						'solid' => esc_html__( 'Solid', 'total-theme-core' ),
						'dashed' => esc_html__( 'Dashed', 'total-theme-core' ),
						'double' => esc_html__( 'Double', 'total-theme-core' ),
						'zig-zag' => esc_html__( 'Zig-Zag', 'total-theme-core' ),
						'dotted-line' => esc_html__( 'Dotted', 'total-theme-core' ),
						'dotted' => esc_html__( 'Ben-Day', 'total-theme-core' ),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
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
				// Style
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Spacing', 'total-theme-core' ),
					'param_name' => 'margin_y',
					'description' => esc_html__( 'Top and Botom element margin.', 'total'),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'placeholder' => '100%',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'width',
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'description' => esc_html__( 'Important: The alignment setting uses left/right auto margins. If you enter a custom left or right margin below the alignment will not work correctly.', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'center',
					'dependency' => [ 'element' => 'width', 'not_empty' => true ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'placeholder' => '1px',
					'heading' => esc_html__( 'Height', 'total-theme-core' ),
					'param_name' => 'height',
					'description' => self::param_description( 'px' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'color',
					'dependency' => [ 'element' => 'style', 'value_not_equal_to' => 'dotted' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'margin',
					'description' => self::param_description( 'margin' ),
					'dependency' => [ 'element' => 'margin_y', 'is_empty' => true ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				// Icon
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Icon library', 'total-theme-core' ),
					'param_name' => 'icon_type',
					'description' => esc_html__( 'For optimal site speed, it\'s strongly recommended to use the theme\'s built-in icon library or upload a custom icon.', 'total-theme-core' ),
					'value' => [
						esc_html__( 'Theme Icons', 'total-theme-core' ) => 'ticons',
						esc_html__( 'Font Awesome', 'total-theme-core' ) => 'fontawesome',
						esc_html__( 'Open Iconic', 'total-theme-core' ) => 'openiconic',
						esc_html__( 'Typicons', 'total-theme-core' ) => 'typicons',
						esc_html__( 'Entypo', 'total-theme-core' ) => 'entypo',
						esc_html__( 'Linecons', 'total-theme-core' ) => 'linecons',
						esc_html__( 'Pixel', 'total-theme-core' ) => 'pixelicons',
					],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon',
					'dependency' => [ 'element' => 'icon_type', 'value' => 'ticons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_fontawesome',
					'settings' => [ 'emptyIcon' => true, 'type' => 'fontawesome', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'fontawesome' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_openiconic',
					'settings' => [ 'emptyIcon' => true, 'type' => 'openiconic', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'openiconic' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_typicons',
					'settings' => [ 'emptyIcon' => true, 'type' => 'typicons', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'typicons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_entypo',
					'settings' => [ 'emptyIcon' => true, 'type' => 'entypo', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'entypo' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_linecons',
					'settings' => [ 'emptyIcon' => true, 'type' => 'linecons', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'linecons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_pixelicons',
					'settings' => [ 'emptyIcon' => true, 'type' => 'pixelicons', 'source' => vcex_pixel_icons() ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'pixelicons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Alternative Character', 'total-theme-core' ),
					'param_name' => 'icon_alternative_character',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'description' => self::param_description( 'text' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Icon Color', 'total-theme-core' ),
					'param_name' => 'icon_color',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Icon Background', 'total-theme-core' ),
					'param_name' => 'icon_bg',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Side Margin', 'total-theme-core' ),
					'param_name' => 'icon_spacing',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Size', 'total-theme-core' ),
					'param_name' => 'icon_size',
					'description' => self::param_description( 'icon_size' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Height', 'total-theme-core' ),
					'param_name' => 'icon_height',
					'description' => self::param_description( 'height' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Width', 'total-theme-core' ),
					'param_name' => 'icon_width',
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Border Radius', 'total-theme-core' ),
					'param_name' => 'icon_border_radius',
					'description' => self::param_description( 'border_radius' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Icon Padding', 'total-theme-core' ),
					'param_name' => 'icon_padding',
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				// Hidden Removed attributes
				[ 'type' => 'hidden', 'param_name' => 'margin_top' ],
				[ 'type' => 'hidden', 'param_name' => 'margin_bottom' ],
				[ 'type' => 'hidden', 'param_name' => 'dotted_height' ], // @since 1.8.8
			];
		}

		/**
		 * Parse WPBakery attributes on edit.
		 */
		public static function vc_edit_form_fields_attributes( $atts = [] ) {
			return self::parse_deprecated_attributes( $atts );
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}

			if ( ! empty( $atts['dotted_height'] ) && isset( $atts['style'] ) && 'dotted' === $atts['style'] ) {
				$atts['height'] = $atts['dotted_height'];
				unset( $atts['dotted_height'] );
			}

			if ( empty( $atts['margin'] ) ) {
				$margin_top    = $atts['margin_top'] ?? '';
				$margin_bottom = $atts['margin_bottom'] ?? '';
				if ( $margin_top || $margin_bottom ) {
					$atts['margin'] = vcex_combine_trbl_fields( $margin_top, '', $margin_bottom, '' );
				}
				unset( $atts['margin_top'] );
				unset( $atts['margin_bottom'] );
			}

			return $atts;
		}

	}

}

new Vcex_Divider_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Divider' ) ) {
	class WPBakeryShortCode_Vcex_Divider extends WPBakeryShortCode {}
}
