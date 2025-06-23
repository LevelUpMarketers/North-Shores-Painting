<?php

defined( 'ABSPATH' ) || exit;

/**
 * Icon Shortcode.
 */
if ( ! class_exists( 'Vcex_Icon_Shortcode' ) ) {

	class Vcex_Icon_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_icon';

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
			return \esc_html__( 'Icon', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display a preset icon or custom character', 'total-theme-core' );
		}

		/**
		 * Returns custom vc map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			return [
				'admin_enqueue_js' => 'icon',
				'js_view'          => 'vcexIconVcBackendView',
			];
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array_merge( array(
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Icon library', 'total-theme-core' ),
					'param_name' => 'icon_type',
					'admin_label' => true,
					'description' => esc_html__( 'For optimal site speed, it\'s strongly recommended to use the theme\'s built-in icon library or upload a custom icon.', 'total-theme-core' ),
					'value' => array(
						esc_html__( 'Theme Icons', 'total-theme-core' ) => 'ticons',
						esc_html__( 'Font Awesome', 'total-theme-core' ) => 'fontawesome',
						esc_html__( 'Open Iconic', 'total-theme-core' ) => 'openiconic',
						esc_html__( 'Typicons', 'total-theme-core' ) => 'typicons',
						esc_html__( 'Entypo', 'total-theme-core' ) => 'entypo',
						esc_html__( 'Linecons', 'total-theme-core' ) => 'linecons',
						esc_html__( 'Material', 'total-theme-core' ) => 'material',
						esc_html__( 'Mono Social', 'total-theme-core' ) => 'monosocial',
						esc_html__( 'Pixel (legacy)', 'total-theme-core' ) => 'pixelicons',
					),
				),
				array(
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon',
					'value' => 'star-o',
					'dependency' => array( 'element' => 'icon_type', 'value' => 'ticons' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_fontawesome',
					'value' => 'fa fa-info-circle',
					'settings' => array( 'emptyIcon' => true, 'type' => 'fontawesome', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'fontawesome' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_openiconic',
					'std' => '',
					'settings' => array( 'emptyIcon' => true, 'type' => 'openiconic', 'iconsPerPage' => 100, ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'openiconic' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_typicons',
					'std' => '',
					'settings' => array( 'emptyIcon' => true, 'type' => 'typicons', 'iconsPerPage' => 100, ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'typicons' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_entypo',
					'std' => '',
					'settings' => array( 'emptyIcon' => true, 'type' => 'entypo', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'entypo' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_linecons',
					'std' => '',
					'settings' => array( 'emptyIcon' => true, 'type' => 'linecons', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'linecons' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_material',
					'std' => '',
					'settings' => array( 'emptyIcon' => true, 'type' => 'material', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'material' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_pixelicons',
					'std' => '',
					'settings' => array( 'emptyIcon' => true, 'type' => 'pixelicons', 'source' => vcex_pixel_icons(), 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'pixelicons' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_monosocial',
					'settings' => array( 'emptyIcon' => true, 'type' => 'monosocial', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'monosocial' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Font Alternative Classes', 'total-theme-core' ),
					'param_name' => 'icon_alternative_classes',
					'description' => esc_html__( 'If your are loading a custom icon font set on your site you can enter a custom classname(s) here.', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Alternative Character', 'total-theme-core' ),
					'param_name' => 'icon_alternative_character',
					'description' => self::param_description( 'text' ),
					'dependency' => array( 'element' => 'icon_alternative_classes', 'is_empty' => true ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// General
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_hover_animations',
					'heading' => esc_html__( 'Hover Animation', 'total-theme-core'),
					'param_name' => 'hover_animation',
				),
				vcex_vc_map_add_css_animation(),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				),
				// Style
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'description' => esc_html__( 'By default it will inherit the alignment based on the parent text align.', 'total-theme-core' ),
					'param_name' => 'align',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => array( 'element' => 'float', 'is_empty' => true ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Size', 'total-theme-core' ),
					'param_name' => 'size',
					'std' => 'normal',
					'value' => array(
						esc_html__( 'Inherit', 'total-theme-core' ) => 'inherit',
						esc_html__( 'Tiny', 'total-theme-core' ) => 'tiny',
						esc_html__( 'Small', 'total-theme-core') => 'small',
						esc_html__( 'Normal', 'total-theme-core' ) => 'normal',
						esc_html__( 'Medium', 'total-theme-core' ) => 'medium',
						esc_html__( 'Large', 'total-theme-core' ) => 'large',
						esc_html__( 'Extra Large', 'total-theme-core' ) => 'xlarge',
					),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Custom Size', 'total-theme-core' ),
					'param_name' => 'custom_size',
					'css' => [ 'property' => 'font-size' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'elementor' => [
						'type' => 'text',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'width',
					'css' => [ 'selector' => '.vcex-icon-wrap', 'property' => 'width' ],
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Height', 'total-theme-core' ),
					'param_name' => 'height',
					'css' => [ 'selector' => '.vcex-icon-wrap', 'property' => 'height' ],
					'description' => self::param_description( 'height' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'color',
					'css' => [ 'selector' => '.vcex-icon-wrap', 'property' => 'color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'color_hover',
					'css' => [ 'selector' => '.vcex-icon-wrap:hover', 'property' => 'color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background',
					'css' => [ 'selector' => '.vcex-icon-wrap', 'property' => 'background-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'background_hover',
					'css' => [ 'selector' => '.vcex-icon-wrap:hover', 'property' => 'background-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'css' => [ 'selector' => '.vcex-icon-wrap', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Border', 'total-theme-core' ),
					'param_name' => 'border',
					'css' => [ 'selector' => '.vcex-icon-wrap', 'property' => 'border' ],
					'description' => self::param_description( 'border' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Float (Soft Deprecated)', 'total-theme-core' ),
					'description' => esc_html__( 'This is an older option that isn\'t recommended anymore as it won\'t play nicely with most page builders.', 'total-theme-core' ),
					'param_name' => 'float',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'value' => array(
						esc_html__( 'None', 'total-theme-core' ) => '',
						esc_html__( 'Left', 'total-theme-core' ) => 'left',
						esc_html__( 'Center', 'total-theme-core' ) => 'center',
						esc_html__( 'Right', 'total-theme-core') => 'right',
					),
					'editors' => [ 'wpbakery' ],
				),
				// Onclick
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Link Type', 'total-theme-core' ),
					'param_name' => 'onclick',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Link', 'total-theme-core' ),
					'param_name' => 'onclick_url',
					'description' => self::param_description( 'text' ),
					'dependency' => array(
						'element' => 'onclick',
						'value' => array(
							'custom_link',
							'local_scroll',
							'popup',
							'lightbox_image',
							'lightbox_video',
							'toggle_element'
						),
					),
					'description' => esc_html__( 'Enter your custom link url, lightbox url or local/toggle element ID (including a # at the front).', 'total-theme-core' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vc_link',
					'heading' => esc_html__( 'Internal Link', 'total-theme-core' ),
					'param_name' => 'onclick_internal_link',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value' => 'internal_link' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_custom_field',
					'choices' => 'links',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name' => 'onclick_custom_field',
					'dependency' => array( 'element' => 'onclick', 'value' => 'custom_field' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_callback_function',
					'heading' => esc_html__( 'Callback Function', 'total-theme-core' ),
					'param_name' => 'onclick_callback_function',
					'dependency' => array( 'element' => 'onclick', 'value' => 'callback_function' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'description' => sprintf( esc_html__( 'Callback functions must be %swhitelisted%s for security reasons.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', '</a>' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'attach_image',
					'heading' => esc_html__( 'Lightbox Image', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_image',
					'dependency' => array( 'element' => 'onclick', 'value' => 'lightbox_image' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'attach_images',
					'heading' => esc_html__( 'Lightbox Gallery', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_gallery',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value' => 'lightbox_gallery' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Aria Label', 'total-theme-core' ),
					'param_name' => 'aria_label',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'description' => sprintf( esc_html__( 'Provides descriptive text for screen readers. %sLearn more about the aria-label tag%s. Shortcodes are allowed.', 'total-theme-core' ), '<a href="https://www.w3.org/WAI/WCAG21/Techniques/aria/ARIA8" target="_blank" rel="noopener noreferrer">', '</a>' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Title Attribute', 'total-theme-core' ),
					'param_name' => 'onclick_title',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value_not_equal_to' => 'toggle_element' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Target', 'total-theme-core' ),
					'param_name' => 'onclick_target',
					'std' => 'self',
					'choices' => array(
						'self'   => esc_html__( 'Self', 'total-theme-core' ),
						'_blank' => esc_html__( 'Blank', 'total-theme-core' ),
					),
					'dependency' => array(
						'element' => 'onclick',
						'value' => array(
							'custom_link',
							'internal_link',
							'custom_field',
							'callback_function',
							'post_permalink',
							'just_event_link',
							'home',
						),
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Rel Attribute', 'total-theme-core' ),
					'param_name' => 'onclick_rel',
					'std' => '',
					'choices' => array(
						'' => esc_html__( 'None', 'total-theme-core' ),
						'nofollow' => esc_html__( 'Nofollow', 'total-theme-core' ),
						'sponsored' => esc_html__( 'Sponsored', 'total-theme-core' ),
					),
					'dependency' => array(
						'element' => 'onclick',
						'value' => array( 'custom_link', 'internal_link', 'custom_field', 'callback_function' ),
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Lightbox Dimensions (optional)', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_dims',
					'description' => esc_html__( 'Enter a custom width and height for your lightbox pop-up window. Use format widthxheight. Example: 1920x1080.', 'total-theme-core' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value' => array( 'lightbox_video', 'popup' ) ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Lightbox Title', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_title',
					'dependency' => array(
						'element' => 'onclick',
						'value' => array( 'lightbox_image', 'lightbox_video', 'popup' )
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textarea',
					'heading' => esc_html__( 'Lightbox Caption', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_caption',
					'dependency' => array(
						'element' => 'onclick',
						'value' => array( 'lightbox_image', 'lightbox_video', 'popup' )
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'exploded_textarea',
					'heading' => esc_html__( 'Custom Data Attributes', 'total-theme-core' ),
					'param_name' => 'onclick_data_attributes',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array(
						'element' => 'onclick',
						'value' => array( 'custom_link', 'custom_field', 'callback_function', 'popup' ),
					),
					'description' => esc_html__( 'Enter your custom data attributes in the format of data|value. Hit enter after each set of data attributes.', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Active Icon', 'total-theme-core' ),
					'param_name' => 'icon_active',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value' => 'toggle_element' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
			), self::get_woo_params(), self::get_deprecated_params() );
		}

		/**
		 * WooCommerce params.
		 *
		 * @note Only register these when WooCommerce is enabled because the dependent cart_toggle setting
		 * 		 is only available when WooCommerce is active.
		 */
		private static function get_woo_params(): array {
			if ( ! class_exists( 'WooCommerce', false ) ) {
				return [];
			}

			return [
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Cart Badge', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to display a badge when items are in the cart.', 'total-theme-core' ),
					'param_name' => 'cart_badge',
					'group' => esc_html__( 'Cart Badge', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value' => 'cart_toggle' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Display Cart Count', 'total-theme-core' ),
					'param_name' => 'cart_badge_count',
					'group' => esc_html__( 'Cart Badge', 'total-theme-core' ),
					'dependency' => array( 'element' => 'cart_badge', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Backgound', 'total-theme-core' ),
					'param_name' => 'cart_badge_bg',
					'group' => esc_html__( 'Cart Badge', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-bg' ],
					'dependency' => array( 'element' => 'cart_badge', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'cart_badge_color',
					'group' => esc_html__( 'Cart Badge', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-color' ],
					'dependency' => array( 'element' => 'cart_badge_count', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Dimensions', 'total-theme-core' ),
					'param_name' => 'cart_badge_dims',
					'group' => esc_html__( 'Cart Badge', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-dims' ],
					'dependency' => array( 'element' => 'cart_badge', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'cart_badge_font_size',
					'group' => esc_html__( 'Cart Badge', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-font-size' ],
					'dependency' => array( 'element' => 'cart_badge_count', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Vertical OffSet', 'total-theme-core' ),
					'param_name' => 'cart_badge_top',
					'group' => esc_html__( 'Cart Badge', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-top' ],
					'dependency' => array( 'element' => 'cart_badge', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Horizontal OffSet', 'total-theme-core' ),
					'param_name' => 'cart_badge_right',
					'group' => esc_html__( 'Cart Badge', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-right' ],
					'dependency' => array( 'element' => 'cart_badge', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Outline Width', 'total-theme-core' ),
					'param_name' => 'cart_badge_outline_width',
					'group' => esc_html__( 'Cart Badge', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-outline-width' ],
					'dependency' => array( 'element' => 'cart_badge', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Outline Color', 'total-theme-core' ),
					'param_name' => 'cart_badge_outline_color',
					'group' => esc_html__( 'Cart Badge', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-outline-color' ],
					'dependency' => array( 'element' => 'cart_badge', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
			];
		}

		/**
		 * Get deprecated params.
		 */
		private static function get_deprecated_params(): array {
			return [
				array( 'type' => 'hidden', 'param_name' => 'link_local_scroll' ),
				array( 'type' => 'hidden', 'param_name' => 'link_url' ),
				array( 'type' => 'hidden', 'param_name' => 'style' ),
				array( 'type' => 'hidden', 'param_name' => 'color_accent' ), // @since 1.2.8
				array( 'type' => 'hidden', 'param_name' => 'background_accent' ), // @since 1.2.8
				array( 'type' => 'hidden', 'param_name' => 'padding', 'css' => [ 'selector' => '.vcex-icon-wrap' ] ),
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

			// Convert link_url att to vcex_link format.
			if ( ! empty( $atts['link_url'] ) && is_string( $atts['link_url'] ) && ! str_contains( $atts['link_url'], 'url:' ) ) {
				$url = 'url:'. rawurlencode( $atts['link_url'] ) .'|';
				$link_title = isset( $atts['link_title'] ) ? 'title:' . rawurlencode( $atts['link_title'] ) . '|' : '|';
				$link_target = ( isset( $atts['link_target'] ) && 'blank' === $atts['link_target'] ) ? 'target:_blank' : '';
				$atts['link_url'] = $url . $link_title . $link_target;
			}

			// Convert accent color on/off to set the accent color for color picker.
			if ( isset( $atts['color_accent'] ) && 'true' === $atts['color_accent'] ) {
				if ( empty( $atts['color'] ) ) {
					$atts['color'] = 'accent';
				}
				unset( $atts['color_accent'] );
			}

			if ( isset( $atts['background_accent'] ) && 'true' === $atts['background_accent'] ) {
				if ( empty( $atts['background'] ) ) {
					$atts['background'] = 'accent';
				}
				unset( $atts['background_accent'] );
			}

			// Update link target.
			if ( isset( $atts['link_target'] ) && 'local' === $atts['link_target'] ) {
				$atts['link_local_scroll'] = 'true';
				unset( $atts['link_target'] );
			}

			// Move from link_url to onclick.
			if ( empty( $atts['onclick'] ) && ! empty( $atts['link_url'] ) && is_string( $atts['link_url'] ) ) {
				$link = vcex_build_link( $atts['link_url'] );
				$link_url = $link['url'] ?? $link;
				if ( $link_url && is_string( $link_url ) ) {
					if ( vcex_validate_att_boolean( 'link_local_scroll', $atts ) ) {
						$atts['onclick'] = 'local_scroll';
						$atts['onclick_url'] = $link_url;
						unset( $atts['link_local_scroll'] );
					} elseif ( str_contains( $atts['link_url'], 'url:' ) ) {
						$atts['onclick'] = 'internal_link';
						$atts['onclick_internal_link'] = $atts['link_url'];
					} else {
						$atts['onclick'] = 'custom_link';
						$atts['onclick_url'] = $link_url;
					}
					if ( ! empty( $link['title'] ) ) {
						$atts['onclick_title'] = $link[ 'title' ];
					}
					if ( ! empty( $link['target'] ) ) {
						$atts['onclick_target'] = $link[ 'target' ];
					}
					if ( ! empty( $link['rel'] ) ) {
						$atts['onclick_rel'] = $link[ 'rel' ];
					}
				}
				unset( $atts['link_url'] );
			}

			return $atts;
		}

	}

}

new Vcex_Icon_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Icon' ) ) {
	class WPBakeryShortCode_Vcex_Icon extends WPBakeryShortCode {
		protected function outputTitle( $title ) {
			$icon = $this->settings( 'icon' );
			return '<h4 class="wpb_element_title"><i class="vc_general vc_element-icon' . ( ! empty( $icon ) ? ' ' . $icon : '' ) . '" aria-hidden="true"></i><span class="wpb_element_title_vcex_icon"></span><span class="wpb_element_title_vcex_text">' . esc_html(  Vcex_Icon_Shortcode::get_title() ) . '</span></h4>';
		}
	}
}
