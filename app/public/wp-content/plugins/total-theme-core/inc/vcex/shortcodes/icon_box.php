<?php

defined( 'ABSPATH' ) || exit;

/**
 * Icon Box Shortcode.
 */
if ( ! class_exists( 'VCEX_Icon_Box_Shortcode' ) ) {

	class VCEX_Icon_Box_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_icon_box';

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
			return esc_html__( 'Icon Box', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Content box with icon', 'total-theme-core' );
		}

		/**
		 * Returns custom vc map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			return [
				'admin_enqueue_js' => 'icon-box',
				'js_view'          => 'vcexIconBoxVcBackendView',
			];
		}

		/**
		 * Returns the list of element style choices.
		 */
		protected static function get_style_choices(): array {
			$choices  = [
				'one'   => esc_html__( 'Left Icon', 'total-theme-core' ),
				'seven' => esc_html__( 'Right Icon', 'total-theme-core' ),
				'two'   => esc_html__( 'Top Icon', 'total-theme-core' ),
				'eight' => esc_html__( 'Bottom Icon', 'total-theme-core' ),
				'four'  => esc_html__( 'Top Icon with Outline', 'total-theme-core' ),
				'five'  => esc_html__( 'Top Icon with Gray Background', 'total-theme-core' ),
				'six'   => esc_html__( 'Top Icon with Black Background', 'total-theme-core' ),
				'three' => esc_html__( 'Top Icon (legacy)', 'total-theme-core' ),
			];
			$choices = (array) apply_filters( 'vcex_icon_box_styles', $choices );
			return array_flip( $choices ); // for use with vc.
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				// Main
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Heading', 'total-theme-core' ),
					'param_name' => 'heading',
					'std' => 'Sample Heading',
					'description' => self::param_description( 'text' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Badge', 'total-theme-core' ),
					'param_name' => 'heading_badge',
					'description' => self::param_description( 'text' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Badge Background', 'total-theme-core' ),
					'param_name' => 'heading_badge_background_color',
					'css' => [ 'selector' => '.wpex-badge', 'property' => 'background-color' ],
					'dependency' => [ 'element' => 'heading_badge', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textarea_html',
					'holder' => 'div', // !!important!! without it breaks in the backend editor.
					'heading' => esc_html__( 'Content', 'total-theme-core' ),
					'param_name' => 'content',
					'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// General
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
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
					'param_name' => 'classes',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
					'editors' => [ 'wpbakery' ],
				],
				// Style
				[
					'type' => 'dropdown',
					'std' => 'one',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'value' => self::get_style_choices(),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'breakpoint',
					'heading' => esc_html__( 'Stack at Breakpoint', 'total-theme-core' ),
					'param_name' => 'stack_bk',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'style', 'value' => [ 'one', 'seven' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'width',
					'description' => self::param_description( 'width' ),
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Aligment', 'total-theme-core' ),
					'param_name' => 'float',
					'std' => 'center',
					'dependency' => [ 'element' => 'width', 'not_empty' => true ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Icon Spacing', 'total-theme-core' ),
					'param_name' => 'icon_spacing',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Vertical Align Center', 'total-theme-core' ),
					'param_name' => 'align_center',
					'dependency' => [ 'element' => 'style', 'value' => [ 'one', 'seven' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'alignment',
					'std' => 'center',
					'exclude_choices' => [ '' ],
					'dependency' => [ 'element' => 'style', 'value' => [ 'two', 'eight' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background_color',
					'css' => [ 'property' => 'background-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'hover_background',
					'css' => [ 'selector' => '{{WRAPPER}}:hover', 'property' => 'background-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'White Text On Hover', 'total-theme-core' ),
					'param_name' => 'hover_white_text',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'dependency' => [ 'element' => 'style', 'value' => 'four' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'dependency' => [ 'element' => 'style', 'value' => 'four' ],
					'css' => [ 'property' => 'border-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'padding',
					'heading' => esc_html__( 'Vertical Padding', 'total-theme-core' ),
					'param_name' => 'padding_y',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'padding',
					'heading' => esc_html__( 'Horizontal Padding', 'total-theme-core' ),
					'param_name' => 'padding_x',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'css' => [ 'property' => 'border-radius' ],
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
					'type' => 'vcex_select',
					'choices' => 'shadow',
					'heading' => esc_html__( 'Shadow: Hover', 'total-theme-core' ),
					'param_name' => 'shadow_hover',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_hover_animations',
					'heading' => esc_html__( 'Hover Animation', 'total-theme-core'),
					'param_name' => 'hover_animation',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				// Content
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'font_color',
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-icon-box-content', 'property' => 'color' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => [ 'selector' => '.vcex-icon-box-content', 'property' => 'font-size' ],
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'css' => [ 'selector' => '.vcex-icon-box-content', 'property' => 'font-family' ],
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-icon-box-content', 'property' => 'font-weight' ],
					'param_name' => 'font_weight',
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'content_typo',
					'selector' => '.vcex-icon-box-content',
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Heading
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'margin',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'heading_bottom_margin',
					'css' => [ 'selector' => '.vcex-icon-box-heading', 'property' => 'margin-bottom' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'typography_style',
					'heading' => esc_html__( 'Typography Style', 'total-theme-core' ),
					'param_name' => 'heading_typography_style',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'heading' => esc_html__( 'HTML Tag', 'total-theme-core' ),
					'param_name' => 'heading_type',
					'type' => 'vcex_select_buttons',
					'std' => apply_filters( 'vcex_icon_box_heading_default_tag', 'h2' ),
					'choices' => 'html_tag',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'heading_color',
					'css' => [ 'selector' => '.vcex-icon-box-heading', 'property' => 'color' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'heading_size',
					'css' => [ 'selector' => '.vcex-icon-box-heading', 'property' => 'font-size' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'heading_font_family',
					'css' => [ 'selector' => '.vcex-icon-box-heading', 'property' => 'font-family' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'heading_weight',
					'css' => [ 'selector' => '.vcex-icon-box-heading', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'heading_transform',
					'choices' => 'text_transform',
					'css' => [ 'selector' => '.vcex-icon-box-heading', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'letter_spacing',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'heading_letter_spacing',
					'css' => [ 'selector' => '.vcex-icon-box-heading', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'line_height',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'heading_line_height',
					'css' => [ 'selector' => '.vcex-icon-box-heading', 'property' => 'line-height' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'heading_typo',
					'selector' => '.vcex-icon-box-heading',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Icons
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
						esc_html__( 'Material', 'total-theme-core' ) => 'material',
						esc_html__( 'Pixel', 'total-theme-core' ) => 'pixelicons',
					],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon',
					'value' => 'star-o',
					'dependency' => [ 'element' => 'icon_type', 'value' => 'ticons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_fontawesome',
					'value' => 'fas fa-info-circle',
					'settings' => [ 'emptyIcon' => true, 'type' => 'fontawesome', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'fontawesome' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_openiconic',
					'settings' => [ 'emptyIcon' => true, 'type' => 'openiconic', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'openiconic' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_typicons',
					'settings' => [ 'emptyIcon' => true, 'type' => 'typicons', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'typicons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_entypo',
					'settings' => [ 'emptyIcon' => true, 'type' => 'entypo', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'entypo' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_linecons',
					'settings' => [ 'emptyIcon' => true, 'type' => 'linecons', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'linecons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_material',
					'settings' => [ 'emptyIcon' => true, 'type' => 'material', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'material' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_pixelicons',
					'settings' => [ 'emptyIcon' => true, 'type' => 'pixelicons', 'source' => vcex_pixel_icons() ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'pixelicons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Alternative Classes', 'total-theme-core' ),
					'param_name' => 'icon_alternative_classes',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Alternative Character', 'total-theme-core' ),
					'param_name' => 'icon_alternative_character',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'description' => self::param_description( 'text' ),
					'dependency' => [ 'element' => 'icon_alternative_classes', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'icon_font_weight',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'dependency' => [ 'element' => 'icon_alternative_character', 'not_empty' => true ],
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Size', 'total-theme-core' ),
					'param_name' => 'icon_size',
					'placeholder' => '28px',
					'css' => [ 'selector' => '.vcex-icon-box-icon', 'property' => 'icon-size' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'icon_color',
					'css' => [ 'selector' => '.vcex-icon-box-icon', 'property' => 'color' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'icon_background',
					'css' => [ 'selector' => '.vcex-icon-box-icon', 'property' => 'background-color' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'border_radius',
					'supports_blobs' => 'true',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'icon_border_radius',
					'css' => [ 'selector' => '.vcex-icon-box-icon', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'icon_shadow',
					'choices' => 'shadow',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'description' => esc_html__( 'For optimal display add a white background to your icon or give the icon a custom width/height.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_width',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'icon_border_width',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Width', 'total-theme-core' ),
					'param_name' => 'icon_width',
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Height', 'total-theme-core' ),
					'param_name' => 'icon_height',
					'description' => self::param_description( 'height' ),
					'css' => [ 'selector' => '.vcex-icon-box-icon', 'property' => 'height' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'margin',
					'heading' => esc_html__( 'Top Margin', 'total-theme-core' ),
					'param_name' => 'icon_top_margin',
					'dependency' => [ 'element' => 'style', 'value' => [ 'two', 'three', 'four', 'five', 'six' ] ],
					'css' => [ 'selector' => '.vcex-icon-box-symbol--icon', 'property' => 'margin-block-start' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'margin',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'icon_bottom_margin',
					'dependency' => [ 'element' => 'style', 'value' => [ 'two', 'three', 'four', 'five', 'six' ] ],
					'css' => [ 'selector' => '.vcex-icon-box-symbol--icon', 'property' => 'margin-block-end' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Image
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Source', 'total-theme-core' ),
					'param_name' => 'image_source',
					'std' => 'media_library',
					'value' => [
						esc_html__( 'Media Library', 'total-theme-core' ) => 'media_library',
						esc_html__( 'Featured Image', 'total-theme-core' ) => 'featured',
						esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
						esc_html__( 'External', 'total-theme-core' ) => 'external',
					],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'attach_image',
					'heading' => esc_html__( 'Image', 'total-theme-core' ),
					'param_name' => 'image',
					'dependency' => [ 'element' => 'image_source', 'value' => 'media_library' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'External Image', 'total-theme-core' ),
					'param_name' => 'external_image',
					'description' => self::param_description( 'text' ),
					'dependency' => [ 'element' => 'image_source', 'value' => 'external' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'image',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name' => 'image_custom_field',
					'dependency' => [ 'element' => 'image_source', 'value' => 'custom_field' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'margin',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'image_bottom_margin',
					'css' => [ 'selector' => '.vcex-icon-box-symbol--image', 'property' => 'margin-block-end' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'style', 'value' => [ 'two', 'three', 'four', 'five', 'six' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'image_width',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Height', 'total-theme-core' ),
					'param_name' => 'image_height',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Crop Image', 'total-theme-core' ),
					'param_name' => 'resize_image',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to run the image through the resizing script which will create a new cropped version.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'image_source', 'value' => [ 'media_library', 'custom_field', 'featured' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Fit', 'total-theme-core' ),
					'param_name' => 'image_fit',
					'choices' => [
						'' => esc_html__( 'None', 'total-theme-core' ),
						'cover' => esc_html__( 'Cover', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'resize_image', 'value' => 'false' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Lazy Load', 'total-theme-core' ),
					'param_name' => 'img_lazy_load',
					'std' => 'true',
					'description' => esc_html__( 'Consider disabling if your element is above the fold.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Fetch Priority', 'total-theme-core' ),
					'param_name' => 'img_fetchpriority',
					'choices' => [
						'' => esc_html__( 'Auto', 'total-theme-core' ),
						'low' => esc_html__( 'Low', 'total-theme-core' ),
						'high' => esc_html__( 'High', 'total-theme-core' ),
					],
					'description' => esc_html__( 'Set the fetchpriority attribute for your image.', 'total-theme-core' ) . ' <a href="https://web.dev/priority-hints/" target="_blank" rel="noopener noreferrer">' . esc_html( 'Learn more from Google\'s web.dev blog') . '</a>',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'image_shadow',
					'choices' => 'shadow',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'image_border_radius',
					'choices' => 'border_radius',
					'supports_blobs' => true,
					'css' => [ 'selector' => '.vcex-icon-box-image', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Link
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Apply Link To Entire Element?', 'total-theme-core' ),
					'param_name' => 'url_wrap',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => [
						'element' => 'style',
						'value' => [ 'one', 'two', 'three', 'seven', 'eight' ],
					],
					'description' => esc_html__( 'Important: If you have added any links to your icon box text this setting will not work because it\'s not possible to have nested links in HTML.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Link Type', 'total-theme-core' ),
					'param_name' => 'onclick',
					'value' => [
						esc_html__( 'None', 'total-theme-core' ) => '',
						esc_html__( 'Custom Link', 'total-theme-core' ) => 'custom_link',
						esc_html__( 'Internal Page', 'total-theme-core' ) => 'internal_link',
						esc_html__( 'Scroll to Section', 'total-theme-core' ) => 'local_scroll',
						esc_html__( 'Toggle Element', 'total-theme-core' ) => 'toggle_element',
						esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
						esc_html__( 'Callback Function', 'total-theme-core' ) => 'callback_function',
						esc_html__( 'Inline Content or iFrame Popup', 'total-theme-core' ) => 'popup',
						esc_html__( 'Image lightbox', 'total-theme-core' ) => 'lightbox_image',
						esc_html__( 'Image Gallery Lightbox', 'total-theme-core' ) => 'lightbox_gallery',
						esc_html__( 'Post Image Gallery Lightbox', 'total-theme-core' ) => 'lightbox_post_gallery',
						esc_html__( 'Video Lightbox', 'total-theme-core' ) => 'lightbox_video',
						esc_html__( 'Post Video Lightbox', 'total-theme-core' ) => 'lightbox_post_video',
					],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Link', 'total-theme-core' ),
					'param_name' => 'onclick_url',
					'description' => self::param_description( 'text' ),
					'dependency' => [
						'element' => 'onclick',
						'value' => [
							'custom_link',
							'local_scroll',
							'popup',
							'lightbox_image',
							'lightbox_video',
							'toggle_element',
						],
					],
					'description' => esc_html__( 'Enter your custom link url, lightbox url or local/toggle element ID (including a # at the front).', 'total-theme-core' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vc_link',
					'heading' => esc_html__( 'Internal Link', 'total-theme-core' ),
					'param_name' => 'onclick_internal_link',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => [ 'element' => 'onclick', 'value' => 'internal_link' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'links',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name' => 'onclick_custom_field',
					'dependency' => [ 'element' => 'onclick', 'value' => 'custom_field' ],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_callback_function',
					'heading' => esc_html__( 'Callback Function', 'total-theme-core' ),
					'param_name' => 'onclick_callback_function',
					'dependency' => [ 'element' => 'onclick', 'value' => 'callback_function' ],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'description' => sprintf( esc_html__( 'Callback functions must be %swhitelisted%s for security reasons.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', '</a>' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'attach_image',
					'heading' => esc_html__( 'Lightbox Image', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_image',
					'dependency' => [ 'element' => 'onclick', 'value' => 'lightbox_image' ],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'attach_images',
					'heading' => esc_html__( 'Lightbox Gallery', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_gallery',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => [ 'element' => 'onclick', 'value' => 'lightbox_gallery' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Title Attribute', 'total-theme-core' ),
					'param_name' => 'onclick_title',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => [ 'element' => 'onclick', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Target', 'total-theme-core' ),
					'param_name' => 'onclick_target',
					'std' => 'self',
					'choices' => [
						'self'   => esc_html__( 'Self', 'total-theme-core' ),
						'_blank' => esc_html__( 'Blank', 'total-theme-core' ),
					],
					'dependency' => [
						'element' => 'onclick',
						'value' => [ 'custom_link', 'internal_link', 'custom_field', 'callback_function' ],
					],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Rel Attribute', 'total-theme-core' ),
					'param_name' => 'onclick_rel',
					'std' => '',
					'choices' => [
						'' => esc_html__( 'None', 'total-theme-core' ),
						'nofollow' => esc_html__( 'Nofollow', 'total-theme-core' ),
						'sponsored' => esc_html__( 'Sponsored', 'total-theme-core' ),
					],
					'dependency' => [
						'element' => 'onclick',
						'value' => [ 'custom_link', 'internal_link', 'custom_field', 'callback_function' ],
					],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Lightbox Dimensions (optional)', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_dims',
					'description' => esc_html__( 'Enter a custom width and height for your lightbox pop-up window. Use format widthxheight. Example: 1920x1080.', 'total-theme-core' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => [ 'element' => 'onclick', 'value' => [ 'lightbox_video', 'popup' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Lightbox Title', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_title',
					'dependency' => [
						'element' => 'onclick',
						'value' => [ 'lightbox_image', 'lightbox_video', 'popup' ],
					],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textarea',
					'heading' => esc_html__( 'Lightbox Caption', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_caption',
					'dependency' => [
						'element' => 'onclick',
						'value' => [ 'lightbox_image', 'lightbox_video', 'popup' ],
					],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'exploded_textarea',
					'heading' => esc_html__( 'Custom Data Attributes', 'total-theme-core' ),
					'param_name' => 'onclick_data_attributes',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => [
						'element' => 'onclick',
						'value' => [ 'custom_link', 'custom_field', 'callback_function', 'popup' ],
					],
					'description' => esc_html__( 'Enter your custom data attributes in the format of data|value. Hit enter after each set of data attributes.', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				// Button.
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Show Button?', 'total-theme-core' ),
					'param_name' => 'show_button',
					'std' => 'false',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'onclick', 'not_empty' => true ],
					'description' => esc_html__( 'Note: If you use the Grid Container to display multiple icon boxes the buttons will be bottom aligned.', 'total-theme-core' ),
					'elementor' => [
						'description' => '',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Text', 'total-theme-core' ),
					'param_name' => 'button_text',
					'placeholder' => esc_html__( 'Learn more', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => 'true' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'button_font_size',
					'css' => [ 'selector' => '.vcex-icon-box-button', 'property' => 'font-size' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => 'true' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'button_typo',
					'selector' => '.vcex-icon-box-button',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => 'true' ],
					'editors' => [ 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'border_radius',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'button_border_radius',
					'css' => [ 'selector' => '.vcex-icon-box-button', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'button_padding',
					'description' => self::param_description( 'padding' ),
					'css' => [ 'selector' => '.vcex-icon-box-button', 'property' => 'padding' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'button_background',
					'css' => [ 'selector' => '.vcex-icon-box-button', 'property' => 'background' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'button_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-icon-box-button', 'property' => 'color' ],
					'dependency' => [ 'element' => 'show_button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'button_hover_background',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-icon-box-button:hover', 'property' => 'background' ],
					'dependency' => [ 'element' => 'show_button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'button_hover_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-icon-box-button:hover', 'property' => 'color' ],
					'dependency' => [ 'element' => 'show_button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				// Deprecated fields.
				[ 'type' => 'hidden', 'param_name' => 'background' ],
				[ 'type' => 'hidden', 'param_name' => 'background_image' ],
				[ 'type' => 'hidden', 'param_name' => 'padding' ],
				[ 'type' => 'hidden', 'param_name' => 'margin_bottom' ],
				// @since v5.1
				[ 'type' => 'hidden', 'param_name' => 'url' ],
				[ 'type' => 'hidden', 'param_name' => 'url_target' ],
				[ 'type' => 'hidden', 'param_name' => 'url_rel' ],
				[ 'type' => 'hidden', 'param_name' => 'icon_color_accent' ],
				[ 'type' => 'hidden', 'param_name' => 'icon_background_accent' ],
				// @since 6.0
				[ 'type' => 'hidden', 'param_name' => 'wpex_padding' ],
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

			// Convert wpex_padding to padding_y and padding_x
			if ( ! empty( $atts['wpex_padding'] ) && ( empty( $atts['padding_y'] ) && empty( $atts['padding_x'] ) ) ) {
				$atts['padding_x'] = $atts['padding_y'] = $atts['wpex_padding'];
				unset( $atts['wpex_padding'] );
			}

			// Move items to new onclick param.
			if ( empty( $atts['onclick'] ) ) {

				if ( ! empty( $atts['url'] ) ) {
					if ( empty( $atts['onclick_url'] ) ) {
						$atts['onclick_url'] = $atts['url'];
					}
					$atts['onclick'] = 'custom_link';
					unset( $atts['url'] );
				}

				if ( ! empty( $atts['url_target'] ) ) {
					if ( 'local' === $atts['url_target' ] ) {
						if ( empty( $atts['onclick'] ) ) {
							$atts['onclick'] = 'local_scroll';
						}
						$atts['onclick_target'] = 'self';
					} else {
						$atts['onclick_target'] = $atts['url_target'];
					}
					unset( $atts['url_target'] );
				}

				if ( ! empty( $atts['url_rel'] ) ) {
					$atts['onclick_rel'] = $atts['url_rel'];
					unset( $atts['url_rel'] );
				}

			}

			// Deprecate old use accent color settings.
			if ( isset( $atts['icon_color_accent'] ) && 'true' === $atts['icon_color_accent'] ) {
				if ( empty( $atts['icon_color'] ) ) {
					$atts['icon_color'] = 'accent';
				}
				unset( $atts['icon_color_accent'] );
			}

			if ( isset( $atts['icon_background_accent'] ) && 'true' === $atts['icon_background_accent'] ) {
				if ( empty( $atts['icon_background'] ) ) {
					$atts['icon_background'] = 'accent';
				}
				if ( empty( $atts['icon_color'] ) ) {
					$atts['icon_color'] = '#fff';
				}
				unset( $atts['icon_background_accent'] );
			}

			return $atts;
		}

		/**
		 * Advanced CSS.
		 */
		protected static function css_pre_render( $css, $atts = [] ): void {
			$style = $atts['style'] ?? 'one';

			// Deprecated styles that get moved to CSS.
			if ( empty( $atts['css'] ) ) {
				if ( ! empty( $atts['padding'] ) && empty( $atts['wpex_padding'] ) ) {
					$css->add_extra_css( [
						'selector' => '{{WRAPPER}}',
						'property' => 'padding',
						'val'      => $atts['padding'],
					] );
				}
				if ( ! empty( $atts['background'] ) && in_array( $style, [ 'four', 'five', 'six' ], true ) ) {
					$wrap_styles['background-color'] = $atts['background'];
				}
				if ( ! empty( $atts['background_image'] )
					&& is_numeric( $atts['background_image'] )
					&& in_array( $style, [ 'four', 'five', 'six' ], true )
					&& $background_image = wp_get_attachment_url( $atts['background_image'] )
				) {
					$css->add_extra_css( [
						'selector' => '{{WRAPPER}}',
						'property' => 'background-image',
						'val'      => $background_image,
					] );
				}
				if ( ! empty( $atts['margin_bottom'] ) ) {
					$css->add_extra_css( [
						'selector' => '{{WRAPPER}}',
						'property' => 'margin-bottom',
						'val'      => $atts['margin_bottom'],
					] );
				}
			}

			// Image.
			if ( ! empty( $atts['image_width'] ) ) {
				$css->add_extra_css( [
					'selector' => '.vcex-icon-box-image',
					'property' => 'width',
					'val'      => $atts['image_width'],
				] );
			}
			if ( ! vcex_validate_att_boolean( 'resize_image', $atts, true ) && ! empty( $atts['image_height'] ) ) {
				$css->add_extra_css( [
					'selector' => '.vcex-icon-box-image',
					'property' => 'height',
					'val'      => $atts['image_height'],
				] );
			}

			// Icon width (to support % widths on side icons).
			if ( ! empty( $atts['icon_width'] ) ) {
				$css->add_extra_css( [
					'selector' => ( 'one' === $style || 'seven' === $style ) ? '.vcex-icon-box-symbol' : '.vcex-icon-box-icon',
					'property' => 'width',
					'val'      => $atts['icon_width'],
				] );
			}

		}

	}

}

new VCEX_Icon_Box_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Icon_Box' ) ) {
	class WPBakeryShortCode_Vcex_Icon_Box extends WPBakeryShortCode {
		protected function outputTitle( $title ) {
			$icon = $this->settings( 'icon' );
			$title = VCEX_Icon_Box_Shortcode::get_title();
			return '<h4 class="wpb_element_title"><i class="vc_general vc_element-icon' . ( ! empty( $icon ) ? ' ' . $icon : '' ) . '" aria-hidden="true"></i><span class="wpb_element_title_vcex_icon"></span><span class="wpb_element_title_vcex_text" data-title="' . esc_attr( $title ) . '">' . esc_html( $title ) . '</span></h4>';
		}
	}
}
