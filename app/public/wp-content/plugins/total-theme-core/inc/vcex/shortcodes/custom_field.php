<?php

defined( 'ABSPATH' ) || exit;

/**
 * Custom Field Shortcode.
 */
if ( ! class_exists( 'VCEX_Custom_Field_Shortcode' ) ) {

	class VCEX_Custom_Field_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_custom_field';

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
			return esc_html__( 'Custom Field', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display custom field & ACF meta value', 'total-theme-core' );
		}

		/**
		 * Returns custom vc map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			return [
				'admin_enqueue_js' => 'custom-field',
				'js_view'          => 'vcexBackendViewCustomField',
			];
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				array(
					'heading' => esc_html__( 'HTML Tag', 'total-theme-core' ),
					'param_name' => 'tag',
					'std' => 'div',
					'type' => 'vcex_select',
					'choices' => [
						'div' => 'div',
						'span' => 'span',
						'code' => 'code',
						'pre' => 'pre',
						'ins' => 'ins',
						'del' => 'del',
						'strong' => 'strong',
						'blockquote' => 'blockquote',
						'h1' => 'h1',
						'h2' => 'h2',
						'h3' => 'h3',
						'h4' => 'h4',
						'h5' => 'h5',
						'h6' => 'h6',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_custom_field',
					'heading' => esc_html__( 'Custom Field', 'total-theme-core' ),
					'param_name' => 'name',
					'admin_label' => true,
					'description' => esc_html__( 'Select or enter a custom field name to display it\'s value. For more advanced displays you can hook into the "vcex_custom_field_value_output" filter.', 'total-theme-core' ),
					'elementor' => [
						'label_block' => true,
						'type' => 'text',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Fallback Value', 'total-theme-core' ),
					'label_block' => true, // elementor param.
					'param_name' => 'fallback',
					'description' => esc_html__( 'If the custom field value is empty you can add a fallback text to display on the site (optional).', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Parse Callback Function Name', 'total-theme-core' ),
					'label_block' => true, // elementor param.
					'param_name' => 'parse_callback',
					'description' => esc_html__( 'Enter a callback function name to pass your custom field output through. This is useful for added sanitization or if you want to render your data in a specific way. A basic example would be if your custom field returns an image attachment ID you could enter "wp_get_attachment_image" in this field to return an image. For most cases you\'ll most likely be writing your own callback functions.', 'total-theme-core' ) . ' (<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Callback functions must be whitelisted', 'total-theme-core' ) . '</a>)',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Automatically Add Paragraphs', 'total-theme-core' ),
					'param_name' => 'autop',
					'std' => 'false',
					'description' => esc_html__( 'Enable to automatically add paragraph tags for new lines.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Prefix', 'total-theme-core' ),
					'param_name' => 'prefix',
					'description' => self::param_description( 'text' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Suffix', 'total-theme-core' ),
					'param_name' => 'suffix',
					'description' => self::param_description( 'text' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Affix Spacing', 'total-theme-core' ),
					'param_name' => 'affix_spacing',
					'description' => esc_html__( 'Enable to add a space between the prefix/suffix and the custom field value.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
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
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core' ),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core' ),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				// Style
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background_color',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'padding',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'padding_all',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'border_style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				// Typography
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'param_name' => 'heading_margin',
					'heading' => esc_html__( 'Heading Margin', 'total-theme-core' ),
					'dependency' => array( 'element' => 'tag', 'value_not_equal_to' => [ '', 'default', 'div', 'span' ] ),
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'color',
					'css' => [
						'selector' => [ 'self', 'a' ],
						'property' => 'color',
					],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'align',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'font_weight',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'text_transform',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'line_height',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'letter_spacing',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Italic', 'total-theme-core' ),
					'param_name' => 'italic',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				// Icon
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Icon library', 'total-theme-core' ),
					'param_name' => 'icon_type',
					'description' => esc_html__( 'For optimal site speed, it\'s strongly recommended to use the theme\'s built-in icon library or upload a custom icon.', 'total-theme-core' ),
					'value' => array(
						esc_html__( 'Theme Icons', 'total-theme-core' ) => 'ticons',
						esc_html__( 'Font Awesome', 'total-theme-core' ) => 'fontawesome',
						esc_html__( 'Open Iconic', 'total-theme-core' ) => 'openiconic',
						esc_html__( 'Typicons', 'total-theme-core' ) => 'typicons',
						esc_html__( 'Entypo', 'total-theme-core' ) => 'entypo',
						esc_html__( 'Linecons', 'total-theme-core' ) => 'linecons',
						esc_html__( 'Material', 'total-theme-core' ) => 'material',
					),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon',
					'dependency' => array( 'element' => 'icon_type', 'value' => 'ticons' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_fontawesome',
					'dependency' => array( 'element' => 'icon_type', 'value' => 'fontawesome' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_openiconic',
					'settings' => array(
						'emptyIcon' => true,
						'type' => 'openiconic',
						'iconsPerPage' => 100,
					),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'openiconic' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_typicons',
					'settings' => array(
						'emptyIcon' => true,
						'type' => 'typicons',
						'iconsPerPage' => 100,
					),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'typicons' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_entypo',
					'settings' => array(
						'emptyIcon' => true,
						'type' => 'entypo',
						'iconsPerPage' => 300,
					),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'entypo' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_linecons',
					'settings' => array(
						'emptyIcon' => true,
						'type' => 'linecons',
						'iconsPerPage' => 100,
					),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'linecons' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),

				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_material',
					'settings' => array(
						'emptyIcon' => true,
						'type' => 'material',
						'iconsPerPage' => 100,
					),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'material' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'icon_color',
					'css' => [
						'property' => 'color',
						'selector' => '.vcex-custom-field-icon',
					],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Side Margin', 'total-theme-core' ),
					'param_name' => 'icon_side_margin',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Before Text
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Text', 'total-theme-core' ),
					'param_name' => 'before',
					'group' => esc_html__( 'Before Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'before_font_weight',
					'group' => esc_html__( 'Before Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'before_color',
					'css' => [
						'selector' => '.vcex-custom-field-before',
						'property' => 'color',
					],
					'group' => esc_html__( 'Before Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'before_el_class',
					'description' => self::param_description( 'el_class' ),
					'group' => esc_html__( 'Before Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// CSS
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				// Elementor only params.
				array(
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'typography',
					'selector' => '.vcex-custom-field',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				),
			);
		}

	}

}

new VCEX_Custom_Field_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Custom_Field' ) ) {
	class WPBakeryShortCode_Vcex_Custom_Field extends WPBakeryShortCode {}
}
