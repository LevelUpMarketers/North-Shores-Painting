<?php

defined( 'ABSPATH' ) || exit;

/**
 * Callout Shortcode.
 */
if ( ! class_exists( 'VCEX_Callout_Shortcode' ) ) {

	class VCEX_Callout_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_callout';

		/**
		 * Define shortcode name.
		 *
		 * Keep as fallback.
		 */
		public $shortcode = 'vcex_callout';

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
			return esc_html__( 'Callout', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Call to action section with or without button', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				// Content
				[
					'type' => 'textarea_html',
					'holder' => 'div',
					'class' => 'vcex-callout',
					'heading' => esc_html__( 'Content', 'total-theme-core' ),
					'param_name' => 'content',
					'value' => 'Add your callout text here to make a strong impact. Highlight key messages to draw user attention and demand action.',
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// General
				[
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'description' => self::param_description( 'unique_id' ),
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
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
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
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'std' => 'boxed',
					'choices' => apply_filters( 'vcex_callout_styles', [
						'none'     => esc_html__( 'None', 'total-theme-core' ),
						'boxed'    => esc_html__( 'Boxed', 'total-theme-core' ),
						'bordered' => esc_html__( 'Bordered', 'total-theme-core' ),
				 	] ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Layout', 'total-theme-core' ),
					'param_name' => 'layout',
					'std' => '75-25',
					'value' => [
						'75% | 25%'   => '75-25',
						'50% | 50%'   => '50-50',
						'60% | 40%'   => '60-40',
						'80% | 20%'   => '80-20',
						'100% | 100%' => '100-100',
						esc_html__( 'Auto', 'total-theme-core' ) => 'auto',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Breakpoint', 'total-theme-core' ),
					'param_name' => 'breakpoint',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'padding',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'padding_all',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background',
					'css' => [ 'property' => 'background-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'border_style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Content
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'content_color',
					'css' => [ 'selector' => '.vcex-callout-caption', 'property' => 'color' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'content_font_family',
					'css' => [ 'selector' => '.vcex-callout-caption', 'property' => 'font-family' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'content_font_weight',
					'css' => [ 'selector' => '.vcex-callout-caption', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'content_font_size',
					'css' => [ 'selector' => '.vcex-callout-caption', 'property' => 'font-size' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'content_letter_spacing',
					'choices' => 'letter_spacing',
					'css' => [ 'selector' => '.vcex-callout-caption', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'content_typo',
					'selector' => '.vcex-callout-caption',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Button
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'URL', 'total-theme-core' ),
					'param_name' => 'button_url',
					'description' => self::param_description( 'text' ),
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Text', 'total-theme-core' ),
					'param_name' => 'button_text',
					'description' => self::param_description( 'text' ),
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Button Full-Width', 'total-theme-core' ),
					'param_name' => 'button_full_width',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Button Align', 'total-theme-core' ),
					'param_name' => 'button_align',
					'dependency' => [ 'element' => 'button_full_width', 'value' => 'false' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_button_styles',
					'heading' => esc_html__( 'Button Style', 'total-theme-core' ),
					'param_name' => 'button_style',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_button_colors',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'button_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'button_border_radius',
					'choices' => 'border_radius',
					'css' => [ 'selector' => '.vcex-callout-link', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Link Target', 'total-theme-core' ),
					'param_name' => 'button_target',
					'std' => '',
					'choices' => [
						'' => esc_html__( 'Self', 'total-theme-core' ),
						'blank' => esc_html__( 'Blank', 'total-theme-core' ),
						'local' => esc_html__( 'Local', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Rel', 'total-theme-core' ),
					'param_name' => 'button_rel',
					'std' => '',
					'choices' => [
						'' => esc_html__( 'None', 'total-theme-core' ),
						'nofollow' => esc_html__( 'Nofollow', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'button_custom_background',
					'css' => [ 'selector' => '.vcex-callout-link', 'property' => 'background-color' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'button_custom_hover_background',
					'css' => [ 'selector' => '.vcex-callout-link:hover', 'property' => 'background-color' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'button_custom_color',
					'css' => [ 'selector' => '.vcex-callout-link', 'property' => 'color' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'button_custom_hover_color',
					'css' => [ 'selector' => '.vcex-callout-link:hover', 'property' => 'color' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'button_padding',
					'css' => [ 'selector' => '.vcex-callout-link', 'property' => 'padding' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'button_font_family',
					'css' => [ 'selector' => '.vcex-callout-link', 'property' => 'font-family' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'button_font_weight',
					'css' => [ 'selector' => '.vcex-callout-link', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'button_font_size',
					'css' => [ 'selector' => '.vcex-callout-link', 'property' => 'font-size' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'button_letter_spacing',
					'choices' => 'letter_spacing',
					'css' => [ 'selector' => '.vcex-callout-link', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'button_typo',
					'selector' => '.vcex-callout-button .theme-button',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
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
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Icon Left', 'total-theme-core' ),
					'param_name' => 'button_icon_left',
					'dependency' => [ 'element' => 'icon_type', 'value' => 'ticons' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Left', 'total-theme-core' ),
					'param_name' => 'button_icon_left_fontawesome',
					'settings' => [ 'emptyIcon' => true, 'iconsPerPage' => 100, 'type' => 'fontawesome' ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'fontawesome' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Left', 'total-theme-core' ),
					'param_name' => 'button_icon_left_openiconic',
					'settings' => [ 'emptyIcon' => true, 'type' => 'openiconic', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'openiconic' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Left', 'total-theme-core' ),
					'param_name' => 'button_icon_left_typicons',
					'settings' => [ 'emptyIcon' => true, 'type' => 'typicons', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'typicons' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Left', 'total-theme-core' ),
					'param_name' => 'button_icon_left_entypo',
					'settings' => [ 'emptyIcon' => true, 'type' => 'entypo', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'entypo' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Left', 'total-theme-core' ),
					'param_name' => 'button_icon_left_linecons',
					'settings' => [ 'emptyIcon' => true, 'type' => 'linecons', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'linecons' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Icon Right', 'total-theme-core' ),
					'param_name' => 'button_icon_right',
					'dependency' => [ 'element' => 'icon_type', 'value' => 'ticons' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Right', 'total-theme-core' ),
					'param_name' => 'button_icon_right_fontawesome',
					'settings' => [ 'emptyIcon' => true, 'iconsPerPage' => 100, 'type' => 'fontawesome' ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'fontawesome' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Right', 'total-theme-core' ),
					'param_name' => 'button_icon_right_openiconic',
					'settings' => [ 'emptyIcon' => true, 'type' => 'openiconic', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'openiconic' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Right', 'total-theme-core' ),
					'param_name' => 'button_icon_right_typicons',
					'settings' => [ 'emptyIcon' => true, 'type' => 'typicons', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'typicons' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Right', 'total-theme-core' ),
					'param_name' => 'button_icon_right_entypo',
					'settings' => [ 'emptyIcon' => true, 'type' => 'entypo', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'entypo' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon Right', 'total-theme-core' ),
					'param_name' => 'button_icon_right_linecons',
					'settings' => [ 'emptyIcon' => true, 'type' => 'linecons', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'linecons' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				// Deprecated
				[ 'type' => 'hidden', 'param_name' => 'classes' ],
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
			if ( ! empty( $atts['classes'] ) ) {
				$atts['el_class'] = $atts['classes'];
				unset( $atts['classes'] );
			}
			return $atts;
		}

	}

}

new VCEX_Callout_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Callout' ) ) {
	class WPBakeryShortCode_Vcex_Callout extends WPBakeryShortCode {}
}
