<?php

defined( 'ABSPATH' ) || exit;

/**
 * Social Share Shortcode.
 */
if ( ! class_exists( 'VCEX_Social_Share_Shortcode' ) ) {

	class VCEX_Social_Share_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_social_share';

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
			return esc_html__( 'Social Share', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display post social share', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			if ( function_exists( 'wpex_social_share_items' ) ) {
				$social_share_items = (array) wpex_social_share_items();
			}

			if ( empty( $social_share_items ) ) {
				return [];
			}

			$default_sites = [];
			$site_choices  = [];

			foreach ( $social_share_items as $k => $v ) {
				$default_sites[ $k ] = [
					'site' => $k
				];
				$site_choices[ $v['site'] ] = $k;
			}

			return [
				// Sites
				[
					'type' => 'param_group',
					'param_name' => 'sites',
					'heading' => esc_html__( 'Sites', 'total-theme-core' ),
					'value' => urlencode( json_encode( $default_sites ) ),
					'params' => [
						[
							'type' => 'dropdown',
							'heading' => esc_html__( 'Site', 'total-theme-core' ),
							'param_name' => 'site',
							'admin_label' => true,
							'value' => $site_choices,
							'editors' => [ 'wpbakery', 'elementor' ],
						],
					],
					'elementor' => [
						'title_field' => '{{{ site }}}',
					],
					'group' => esc_html__( 'Sites', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// General
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
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core' ),
					'param_name' => 'animation_duration',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core' ),
					'param_name' => 'animation_delay',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
				],
				// Style
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Custom Design', 'total-theme-core' ),
					'param_name' => 'is_custom',
					'std' => 'false',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to control this element design independently from the Customizer settings. Note: You will still be able to control your custom labels via the Customizer.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'Flat', 'total-theme-core' )    => 'flat',
						esc_html__( 'Minimal', 'total-theme-core' ) => 'minimal',
						esc_html__( '3D', 'total-theme-core' )      => 'three-d',
						esc_html__( 'Rounded', 'total-theme-core' ) => 'rounded',
						esc_html__( 'Magazine', 'total-theme-core' ) => 'mag',
						esc_html__( 'Custom', 'total-theme-core' )  => 'custom',
					],
					'description' => esc_html__( 'You can customize your social share buttons under Appearance > Customize > General Theme Options > Social Share Buttons.', 'total-theme-core' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'is_custom', 'value_not_equal_to' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'none',
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
					'choices' => 'border_radius',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'button_border_radius',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'is_custom', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Gap', 'total-theme-core' ),
					'param_name' => 'gap',
					'css' => [
						'selector' => '.vcex-social-share__buttons',
						'property' => 'gap',
					],
					'description' => self::param_description( 'gap' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'is_custom', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Show Labels', 'total-theme-core' ),
					'param_name' => 'has_labels',
					'std' => 'false',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'is_custom', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Label Breakpoint', 'total-theme-core' ),
					'description' => esc_html__( 'Select a breakpoint if you wish to hide the labels at screen sizes smaller than the selected breakpoint.', 'total-theme-core' ),
					'param_name' => 'label_bk',
					'choices' => 'breakpoint',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'has_labels', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Label Margin', 'total-theme-core' ),
					'param_name' => 'label_margin',
					'choices' => 'margin',
					'css' => [
						'selector' => '.vcex-social-share__button',
						'property' => 'gap',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'has_labels', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Expand Items', 'total-theme-core' ),
					'param_name' => 'expand',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'has_labels', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'button_font_size',
					'css' => [ 'selector' => '.vcex-social-share__button', 'property' => 'font_size' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'is_custom', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'button_font_weight',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'has_labels', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Button Min-Width', 'total-theme-core' ),
					'param_name' => 'button_width',
					'css' => [
						'selector' => '.vcex-social-share__button',
						'property' => 'min-width',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'is_custom', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Button Min-Height', 'total-theme-core' ),
					'param_name' => 'button_height',
					'css' => [
						'selector' => '.vcex-social-share__button',
						'property' => 'min-height',
					],
					'description' => self::param_description( 'height' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'is_custom', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Button Vertical Padding', 'total-theme-core' ),
					'param_name' => 'button_padding_y',
					'css' => [
						'selector' => '.vcex-social-share__button',
						'property' => 'padding-block',
					],
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'is_custom', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Button Horizontal Padding', 'total-theme-core' ),
					'param_name' => 'button_padding_x',
					'css' => [
						'selector' => '.vcex-social-share__button',
						'property' => 'padding-inline',
					],
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'is_custom', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Button Color', 'total-theme-core' ),
					'param_name' => 'button_color',
					'css' => [
						'selector' => '.vcex-social-share__button',
						'property' => 'color',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'is_custom', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Button Background', 'total-theme-core' ),
					'param_name' => 'button_bg',
					'css' => [
						'selector' => '.vcex-social-share__button',
						'property' => 'background',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'is_custom', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Button Color: Hover', 'total-theme-core' ),
					'param_name' => 'button_color_hover',
					'css' => [
						'selector' => '.vcex-social-share__button:hover',
						'property' => 'color',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'is_custom', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Button Background: Hover', 'total-theme-core' ),
					'param_name' => 'button_bg_hover',
					'css' => [
						'selector' => '.vcex-social-share__button:hover',
						'property' => 'background',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'is_custom', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Modal
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Modal Popup', 'total-theme-core' ),
					'param_name' => 'modal',
					'description' => esc_html__( 'Enable to display the social share links in a popup modal window.', 'total-theme-core' ),
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Max Width', 'total-theme-core' ),
					'param_name' => 'modal_max_width',
					'placeholder' => '500px',
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'dependency' => [ 'element' => 'modal', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Top Margin', 'total-theme-core' ),
					'param_name' => 'modal_top_margin',
					'placeholder' => 'auto',
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'description' => esc_html__( 'By default the <dialog> element is placed in the middle of the viewport but you can enter a custom top margin if you wish to place it near the top.', 'total-theme-core' ),
					'css' => [
						'selector' => '.vcex-social-share-modal',
						'property' => 'margin-block-start',
					],
					'dependency' => [ 'element' => 'modal', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'std' => 'icon_text',
					'heading' => esc_html__( 'Button Type', 'total-theme-core' ),
					'param_name' => 'modal_button_type',
					'choices' => [
						'icon_text' => esc_html__( 'Icon & Text', 'total-theme-core' ),
						'icon' => esc_html__( 'Icon Only', 'total-theme-core' ),
						'text' => esc_html__( 'Text Only', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'dependency' => [ 'element' => 'modal', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'std' => 'none',
					'heading' => esc_html__( 'Button Design', 'total-theme-core' ),
					'param_name' => 'modal_button_style',
					'choices' => [
						'none' => esc_html__( 'Plain', 'total-theme-core' ),
						'theme' => esc_html__( 'Theme Button', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'dependency' => [ 'element' => 'modal', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'std' => 'right',
					'heading' => esc_html__( 'Icon Placement', 'total-theme-core' ),
					'placeholder' => esc_html__( 'Share', 'total-theme-core' ),
					'param_name' => 'modal_button_icon_placement',
					'choices' => [
						'right' => esc_html__( 'Right', 'total-theme-core' ),
						'left' => esc_html__( 'Left', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'dependency' => [ 'element' => 'modal_button_type', 'value' => 'icon_text' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'std' => 'default',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'placeholder' => esc_html__( 'Share', 'total-theme-core' ),
					'param_name' => 'modal_button_svg',
					'choices' => [
						'default' => esc_html__( 'Default', 'total-theme-core' ),
						'arrow' => esc_html__( 'Arrow', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'dependency' => [ 'element' => 'modal_button_type', 'value' => [ 'icon_text', 'icon' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Icon Size', 'total-theme-core' ),
					'param_name' => 'modal_button_svg_dims',
					'css' => [ 'selector' => '.vcex-social-share-modal-trigger__icon', 'property' => 'font-size' ],
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'dependency' => [ 'element' => 'modal_button_type', 'value' => 'icon_text' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Space Between Text & Icon', 'total-theme-core' ),
					'description' => self::param_description( 'gap' ),
					'param_name' => 'modal_button_gap',
					'css' => [ 'selector' => '.vcex-social-share-modal-trigger', 'property' => 'gap' ],
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'dependency' => [ 'element' => 'modal_button_type', 'value' => 'icon_text' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Button Text', 'total-theme-core' ),
					'description' => esc_html__( 'If you are only showing an icon for the button, this text will still be added for screen readers.', 'total-theme-core' ),
					'placeholder' => esc_html__( 'Share', 'total-theme-core' ),
					'param_name' => 'modal_button_text',
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'dependency' => [ 'element' => 'modal', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Button Font Size', 'total-theme-core' ),
					'param_name' => 'modal_button_font_size',
					'css' => [ 'selector' => '.vcex-social-share-modal-trigger', 'property' => 'font-size' ],
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'dependency' => [ 'element' => 'modal', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Button Font Weight', 'total-theme-core' ),
					'param_name' => 'modal_button_font_weight',
					'css' => [ 'selector' => '.vcex-social-share-modal-trigger', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'dependency' => [ 'element' => 'modal', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Button Color', 'total-theme-core' ),
					'param_name' => 'modal_button_color',
					'css' => [ 'selector' => '.vcex-social-share-modal-trigger', 'property' => 'color' ],
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'dependency' => [ 'element' => 'modal_button_style', 'value' => 'none' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Button Color: Hover', 'total-theme-core' ),
					'param_name' => 'modal_button_color_hover',
					'css' => [ 'selector' => '.vcex-social-share-modal-trigger:hover', 'property' => 'color' ],
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'dependency' => [ 'element' => 'modal_button_style', 'value' => 'none' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Title', 'total-theme-core' ),
					'param_name' => 'modal_title',
					'placeholder' => esc_html__( 'Share', 'total-theme-core' ),
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'dependency' => [ 'element' => 'modal', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Title Font Size', 'total-theme-core' ),
					'param_name' => 'modal_title_font_size',
					'css' => [ 'selector' => '.vcex-social-share-modal__title', 'property' => 'font-size' ],
					'group' => esc_html__( 'Modal', 'total-theme-core' ),
					'dependency' => [ 'element' => 'modal', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
			];
		}

	}

}

new VCEX_Social_Share_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Social_Share' ) ) {
	class WPBakeryShortCode_Vcex_Social_Share extends WPBakeryShortCode {}
}
