<?php

defined( 'ABSPATH' ) || exit;

/**
 * Social Links Shortcode.
 */
if ( ! class_exists( 'VCEX_Social_Links_Shortcode' ) ) {

	class VCEX_Social_Links_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_social_links';

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
			return esc_html__( 'Social Links', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display social profile links', 'total-theme-core' );
		}

		/**
		 * Parses the shortcode atts.
		 *
		 * Allows adding social options using the format {{site}}={{link}}
		 */
		public static function shortcode_atts( $atts ) {
			if ( ( empty( $atts['source'] ) || 'custom' === $atts['source'] )
				&& ( empty( $atts['social_links'] ) || '%5B%5D' === $atts['social_links'] )
			) {
				$social_links = self::get_social_links_from_atts( $atts );
				if ( $social_links ) {
					$atts['social_links'] = $social_links;
				}
			}
			return \vcex_shortcode_atts( self::TAG, $atts, self::class );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$social_link_select = []; // important we can't have a default/select empty setting because it breaks Elementor
			
			// Get list of social sites (only needed for the editors)
			if ( is_admin() || vcex_vc_is_inline() ) {
				if ( function_exists( 'wpex_social_profile_options_list' ) ) {
					$social_links = wpex_social_profile_options_list();
				}

				if ( ! empty( $social_links ) ) {
					foreach ( $social_links as $key => $val ) {
						$name = $val['name'] ?? $val['label'] ?? $key;
						$social_link_select[ $name ] = $key;
					}
				}
			}

			$source_choices = [
				''             => esc_html__( 'Custom', 'total-theme-core' ),
				'post_author'  => esc_html__( 'Post Author Links', 'total-theme-core' ),
				'custom_field' => esc_html__( 'Custom Fields', 'total-theme-core' ),
			];

			if ( \get_theme_mod( 'staff_enable', true ) ) {
				$source_choices['staff_member'] = esc_html__( 'Current Staff Member', 'total-theme-core' );
			}

			return [
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Source', 'total-theme-core' ),
					'param_name' => 'source',
					'choices' => $source_choices,
					'group' => esc_html__( 'Profiles', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'param_group',
					'param_name' => 'social_links',
					'group' => esc_html__( 'Profiles', 'total-theme-core' ),
					'value' => '%5B%5D', // url encoded array.
					'dependency' => [
						'element' => 'source',
						'value_not_equal_to' => [
							'post_author',
							'custom_field',
							'staff_member',
						],
					],
					'params' => [
						[
							'type' => 'dropdown',
							'heading' => esc_html__( 'Site', 'total-theme-core' ),
							'param_name' => 'site',
							'admin_label' => true,
							'value' => $social_link_select,
							'editors' => [ 'wpbakery', 'elementor' ],
						],
						[
							'type' => 'textfield',
							'heading' => esc_html__( 'Link', 'total-theme-core' ),
							'param_name' => 'link',
							'editors' => [ 'wpbakery', 'elementor' ],
							'elementor' => [
								'default' => '#',
							],
						],
					],
					'elementor' => [
						'title_field' => '{{{ site }}}',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'param_group',
					'param_name' => 'custom_fields',
					'group' => esc_html__( 'Profiles', 'total-theme-core' ),
					'value' => '%5B%5D', // url encoded array.
					'dependency' => [ 'element' => 'source', 'value' => 'custom_field' ],
					'params' => [
						[
							'type' => 'dropdown',
							'heading' => esc_html__( 'Site', 'total-theme-core' ),
							'param_name' => 'site',
							'admin_label' => true,
							'value' => $social_link_select,
							'editors' => [ 'wpbakery', 'elementor' ],
						],
						[
							'type' => 'textfield',
							'heading' => esc_html__( 'Custom Field Name', 'total-theme-core' ),
							'param_name' => 'key',
							'editors' => [ 'wpbakery', 'elementor' ],
						],
					],
					'elementor' => [
						'title_field' => '{{{ site }}}',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// General
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_hover_animations',
					'heading' => esc_html__( 'Hover Animation', 'total-theme-core'),
					'param_name' => 'hover_animation',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Link Target', 'total-theme-core'),
					'param_name' => 'link_target',
					'std' => 'self',
					'choices' => 'link_target',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Show Labels', 'total-theme-core' ),
					'param_name' => 'show_label',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'placeholder' => '10px',
					'heading' => esc_html__( 'Gap Between Icon and Label', 'total-theme-core' ),
					'param_name' => 'label_gap',
					'css' => [
						'selector' => '.vcex-social-links__item',
						'property' => 'gap',
					],
					'dependency' => [ 'element' => 'show_label', 'value' => 'true' ],
					'description' => self::param_description( 'margin' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'classes',
					'editors' => [ 'wpbakery', 'elementor' ],
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
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				],
				// Style
				[
					'type' => 'vcex_social_button_styles',
					'heading' => esc_html__( 'Style', 'total-theme-core'),
					'param_name' => 'style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Spacing', 'total-theme-core' ),
					'param_name' => 'spacing',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Expand Items', 'total-theme-core' ),
					'param_name' => 'expand',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'std' => 'horizontal',
					'heading' => esc_html__( 'Direction', 'total-theme-core' ),
					'param_name' => 'direction',
					'choices' => [
						'horizontal' => esc_html__( 'Horizontal', 'total-theme-core' ),
						'vertical' => esc_html__( 'Vertical', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'none',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'expand', 'value' => 'false' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'target' => 'font-size',
					'heading' => esc_html__( 'Custom Icon Size', 'total-theme-core' ),
					'param_name' => 'size',
					'css' => 'font-size',
					'description' => self::param_description( 'font_size' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_radius',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'css' => [
						'selector' => '.vcex-social-links__item',
						'property' => 'border-radius',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'style', 'value' => 'none' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Vertical Padding', 'total-theme-core' ),
					'param_name' => 'padding_y',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'css' => [
						'selector' => '.vcex-social-links__item',
						'property' => 'padding-block',
					],
					'description' => self::param_description( 'padding' ),
					'dependency' => [ 'element' => 'height', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Horizontal Padding', 'total-theme-core' ),
					'param_name' => 'padding_x',
					'css' => [
						'selector' => '.vcex-social-links__item',
						'property' => 'padding-inline',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'description' => self::param_description( 'padding' ),
					'dependency' => [ 'element' => 'width', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'width',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'description' => self::param_description( 'width' ),
					'dependency' => [ 'element' => 'expand', 'value' => 'false' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Height', 'total-theme-core' ),
					'param_name' => 'height',
					'css' => [
						'selector' => '.vcex-social-links__item',
						'property' => 'height',
					],
					'description' => self::param_description( 'height' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'color',
					'css' => [
						'selector' => '.vcex-social-links__item',
						'property' => 'color',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'hover_color',
					'css' => [
						'selector' => '.vcex-social-links__item:hover',
						'property' => 'color',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'bg',
					'css' => [
						'selector' => '.vcex-social-links__item',
						'property' => 'background',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'hover_bg',
					'css' => [
						'selector' => '.vcex-social-links__item:hover',
						'property' => 'background',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'css' => [
						'selector' => '.vcex-social-links__item',
						'property' => 'border-color',
					],
					'dependency' => [
						'element' => 'style',
						'value' => [
							'minimal',
							'minimal-rounded',
							'minimal-round',
							'bordered',
							'bordered-rounded',
							'bordered-round',
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'Custom CSS applied to each social link.', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				// Deprecated
				[ 'type' => 'hidden', 'param_name' => 'author_links' ], // @since 1.8
				[ 'type' => 'hidden', 'param_name' => 'line_height' ], // @since 1.2.8
			];
		}

		/**
		 * Parse WPBakery attributes on edit.
		 */
		public static function vc_edit_form_fields_attributes( $atts = [] ) {
			if ( ( empty( $atts['source'] ) || 'custom' === $atts['source'] )
				&& ( empty( $atts['social_links'] ) || '%5B%5D' === $atts['social_links'] )
			) {
				$social_links = self::get_social_links_from_atts( $atts );
				if ( $social_links ) {
					$atts['social_links'] = \urlencode( \json_encode( $social_links ) );
					/* - Won't work because the atts aren't saved in vc_map
					foreach ( $social_links as $link ) {
						unset( $atts[ $link['site'] ] );
					}
					*/
				}
			}
			return self::parse_deprecated_attributes( $atts );
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( isset( $atts['author_links'] ) && 'true' === $atts['author_links'] ) {
				$atts['source'] = 'post_author';
				unset( $atts['author_links'] );
			}
			return $atts;
		}

		/**
		 * Returns social links array from atts.
		 */
		protected static function get_social_links_from_atts( $atts ): array {
			$custom_links = [];
			if ( $atts && is_array( $atts ) && function_exists( 'wpex_social_profile_options_list' ) ) {
				$social_options = (array) wpex_social_profile_options_list();
				foreach ( $atts as $key => $val ) {
					if ( array_key_exists( $key, $social_options ) ) {
						$custom_links[] = [
							'site' => $key,
							'link' => $atts[ $key ],
						];
					}
				}
			}
			return $custom_links;
		}

	}

}

new VCEX_Social_Links_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Social_Links' ) ) {
	class WPBakeryShortCode_Vcex_Social_Links extends WPBakeryShortCode {}
}
