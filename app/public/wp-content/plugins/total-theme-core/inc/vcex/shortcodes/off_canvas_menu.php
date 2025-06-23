<?php

defined( 'ABSPATH' ) || exit;

use TotalThemeCore\Vcex\Shortcode_CSS;

/**
 * Horizontal Menu Shortcode.
 */
if ( ! class_exists( 'Vcex_Off_Canvas_Menu_Shortcode' ) ) {
	class Vcex_Off_Canvas_Menu_Shortcode extends \TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_off_canvas_menu';

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
			return esc_html__( 'Off Canvas Menu', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Drawer or full screen menu', 'total-theme-core' );
		}

		/**
		 * Returns custom vc map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			return [
				'admin_enqueue_js' => 'menu',
				'js_view'          => 'vcexMenuView',
			];
		}

		/**
		 * Returns list of script dependencies.
		 */
		public static function get_script_depends(): array {
			return [
				'wpex-off-canvas',
			];
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$extra_choices = self::_get_extra_choices();

			$settings = [
				[
					'type' => 'textfield',
					'placeholder' => esc_html__( 'Menu', 'total-theme-core' ),
					'heading' => esc_html__( 'Aria Label', 'total-theme-core' ),
					'param_name' => 'aria_label',
					'description' => esc_html__( 'Label for screen readers.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Menu', 'total-theme-core' ),
					'param_name' => 'menu_id',
					'choices' => 'menu',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Add Core Hooks', 'total-theme-core' ),
					'description' => esc_html__( 'Adds the WordPress core hooks to the menu items. Enable this function if you have 3rd party plugins or code that need to insert items into your menu. This is disabled by default to prevent potential conflicts.', 'total-theme-core' ),
					'param_name' => 'add_hooks',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Display Under Header', 'total-theme-core' ),
					'param_name' => 'under_header',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Backdrop', 'total-theme-core' ),
					'param_name' => 'backdrop',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Swap Menu Placement (Left or Right)', 'total-theme-core' ),
					'param_name' => 'swap_side',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background',
					'css' => [ 'selector' => '.wpex-off-canvas', 'property' => 'background-color' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'placeholder' => '352px',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'width',
					'description' => esc_html__( 'Set to 100% for a full-screen style menu.', 'total-theme-core' ),
					'css' => [ 'selector' => '.wpex-off-canvas', 'property' => 'width' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'transition_duration',
					'heading' => esc_html__( 'Animation Speed', 'total-theme-core' ),
					'param_name' => 'transition_duration',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'admin_label' => true,
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
					'param_name' => 'el_class',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Toggle
				[
					'type' => 'vcex_select_buttons',
					'std' => 'start',
					'choices' => [
						'start' => esc_html__( 'Start', 'total-theme-core' ),
						'center' => esc_html__( 'Center', 'total-theme-core' ),
						'end' => esc_html__( 'End', 'total-theme-core' ),
					],
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'toggle_align',
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Animate', 'total-theme-core' ),
					'param_name' => 'toggle_animate',
					'dependency' => [ 'element' => 'under_header', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Open Aria Label', 'total-theme-core' ),
					'placeholder' => esc_html__( 'Open menu', 'total-theme-core' ),
					'param_name' => 'toggle_aria_open',
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Close Aria Label', 'total-theme-core' ),
					'placeholder' => esc_html__( 'Close menu', 'total-theme-core' ),
					'param_name' => 'toggle_aria_close',
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Label', 'total-theme-core' ),
					'param_name' => 'toggle_label',
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'toggle_label_font_size',
					'css' => [ 'selector' => '.vcex-off-canvas-menu__toggle-label', 'property' => 'font-size' ],
					'dependency' => [ 'element' => 'toggle_label', 'not_empty' => true ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_buttons',
					'choices' => [
						'hamburger' => esc_html__( 'Hamburger', 'total-theme-core' ),
						'icon' => esc_html__( 'Icon', 'total-theme-core' ),
					],
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'toggle_style',
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_icon',
					'std' => 'bars',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'toggle_icon',
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'dependency' => [ 'element' => 'toggle_style', 'value' => 'icon' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Icon: Active', 'total-theme-core' ),
					'param_name' => 'toggle_icon_active',
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'dependency' => [ 'element' => 'toggle_style', 'value' => 'icon' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Icon Size', 'total-theme-core' ),
					'param_name' => 'toggle_icon_size',
					'css' => [ 'selector' => '.vcex-off-canvas-menu__toggle', 'property' => 'font-size' ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'dependency' => [ 'element' => 'toggle_style', 'value' => 'icon' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Rounded', 'total-theme-core' ),
					'param_name' => 'toggle_rounded',
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'dependency' => [ 'element' => 'toggle_style', 'value' => 'hamburger' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'placeholder' => '22px',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'toggle_width',
					'dependency' => [ 'element' => 'toggle_style', 'value' => 'hamburger' ],
					'css' => [ 'selector' => '.vcex-off-canvas-menu__toggle', 'property' => '--wpex-hamburger-icon-width' ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'placeholder' => '3px',
					'heading' => esc_html__( 'Bar Height', 'total-theme-core' ),
					'param_name' => 'toggle_bar_height',
					'dependency' => [ 'element' => 'toggle_style', 'value' => 'hamburger' ],
					'css' => [ 'selector' => '.vcex-off-canvas-menu__toggle', 'property' => '--wpex-hamburger-icon-bar-height' ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'placeholder' => '4px',
					'heading' => esc_html__( 'Bar Gap', 'total-theme-core' ),
					'param_name' => 'toggle_bar_gap',
					'dependency' => [ 'element' => 'toggle_style', 'value' => 'hamburger' ],
					'css' => [ 'selector' => '.vcex-off-canvas-menu__toggle', 'property' => '--wpex-hamburger-icon-gutter' ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'toggle_color',
					'css' => [ 'selector' => ':is(.vcex-off-canvas-menu__toggle,.vcex-off-canvas-menu__extra-item)', 'property' => 'color' ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'toggle_color_hover',
					'css' => [ 'selector' => ':is(.vcex-off-canvas-menu__toggle,.vcex-off-canvas-menu__extra-item):hover', 'property' => 'color' ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'toggle_bg',
					'css' => [ 'selector' => ':is(.vcex-off-canvas-menu__toggle,.vcex-off-canvas-menu__extra-item)', 'property' => 'background-color' ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'toggle_bg_hover',
					'css' => [ 'selector' => ':is(.vcex-off-canvas-menu__toggle,.vcex-off-canvas-menu__extra-item):hover', 'property' => 'background-color' ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Minimum Width & Height', 'total-theme-core' ),
					'param_name' => 'toggle_min_w_h',
					'css' => [ 'selector' => ':is(.vcex-off-canvas-menu__toggle,.vcex-off-canvas-menu__extra-item)', 'property' => [ 'min-width', 'min-height' ] ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'padding',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'toggle_padding',
					'css' => [ 'selector' => ':is(.vcex-off-canvas-menu__toggle,.vcex-off-canvas-menu__extra-item)', 'property' => 'padding' ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_radius',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'toggle_border_radius',
					'css' => [ 'selector' => ':is(.vcex-off-canvas-menu__toggle,.vcex-off-canvas-menu__extra-item)', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_width',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'toggle_border_width',
					'css' => [ 'selector' => ':is(.vcex-off-canvas-menu__toggle,.vcex-off-canvas-menu__extra-item)', 'property' => 'border-width' ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_style',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'toggle_border_style',
					'dependency' => [ 'element' => 'toggle_border_width', 'not_empty' => true ],
					'css' => [ 'selector' => ':is(.vcex-off-canvas-menu__toggle,.vcex-off-canvas-menu__extra-item)', 'property' => 'border-style' ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'toggle_border_color',
					'dependency' => [ 'element' => 'toggle_border_width', 'not_empty' => true ],
					'css' => [ 'selector' => ':is(.vcex-off-canvas-menu__toggle,.vcex-off-canvas-menu__extra-item)', 'property' => 'border-color' ],
					'group' => esc_html__( 'Toggle', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Items
				[
					'type' => 'vcex_select',
					'choices' => 'align_items',
					'heading' => esc_html__( 'Menu Vertical Align', 'total-theme-core' ),
					'param_name' => 'nav_align_items',
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'justify_content',
					'heading' => esc_html__( 'Item Horizontal Align', 'total-theme-core' ),
					'description' => esc_html__( 'Primarily used to create spacing between parent menu items and the dropdown arrow.', 'total-theme-core' ),
					'param_name' => 'item_justify_content',
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'padding',
					'heading' => esc_html__( 'Item Vertical Padding', 'total-theme-core' ),
					'param_name' => 'item_padding_y',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__item-content', 'property' => 'padding-block' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Centered Menu Text', 'total-theme-core' ),
					'param_name' => 'nav_centered',
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Items Divider', 'total-theme-core' ),
					'param_name' => 'item_divider',
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Divider Color', 'total-theme-core' ),
					'param_name' => 'item_divider_bg',
					'dependency' => [ 'element' => 'item_divider', 'value' => 'true' ],
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__item-divider', 'property' => 'background' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Color', 'total-theme-core' ),
					'param_name' => 'item_color',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__item-content', 'property' => 'color' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Color: Hover', 'total-theme-core' ),
					'param_name' => 'item_color_hover',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__item-content:not(span):hover', 'property' => 'color' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Heading Color', 'total-theme-core' ),
					'param_name' => 'item_heading_color',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__item-heading', 'property' => 'color' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Description Color', 'total-theme-core' ),
					'param_name' => 'item_desc_color',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__item-desc', 'property' => 'color' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Icon Color', 'total-theme-core' ),
					'param_name' => 'item_icon_color',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__icon', 'property' => 'color' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'transition_duration',
					'heading' => esc_html__( 'Item Hover Animation Speed', 'total-theme-core' ),
					'param_name' => 'item_transition_duration',
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Item Font Weight', 'total-theme-core' ),
					'param_name' => 'item_font_weight',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__item-content', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'text_transform',
					'heading' => esc_html__( 'Item Text Transform', 'total-theme-core' ),
					'param_name' => 'item_text_transform',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__item-content', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'letter_spacing',
					'heading' => esc_html__( 'Item Letter Spacing', 'total-theme-core' ),
					'param_name' => 'item_letter_spacing',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__item-content', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Item Font Family', 'total-theme-core' ),
					'param_name' => 'item_font_family',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__item-content', 'property' => 'font-family' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Item Font Size', 'total-theme-core' ),
					'param_name' => 'item_font_size',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__item-content', 'property' => 'font-size' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Sub Menus
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Expanded', 'total-theme-core' ),
					'description' => esc_html__( 'Enable this setting to exand dropdowns by default. This will remove the toggle affect and also allow parent items to be clicked.', 'total-theme-core' ),
					'param_name' => 'sub_expanded',
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Side Border', 'total-theme-core' ),
					'param_name' => 'sub_border_enable',
					'dependency' => [ 'element' => 'nav_centered', 'value' => 'false' ],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Side Border Color', 'total-theme-core' ),
					'param_name' => 'sub_border_color',
					'dependency' => [ 'element' => 'sub_border_enable', 'value' => 'true' ],
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__sub', 'property' => 'border-color' ],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'placeholder' => '15px',
					'heading' => esc_html__( 'Sub Menu Side Margin', 'total-theme-core' ),
					'param_name' => 'sub_margin_start',
					'dependency' => [ 'element' => 'sub_border_enable', 'value' => 'false' ],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'placeholder' => '15px',
					'heading' => esc_html__( 'Sub Menu Child Side Margin', 'total-theme-core' ),
					'param_name' => 'sub_sub_margin_start',
					'dependency' => [ 'element' => 'sub_border_enable', 'value' => 'false' ],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'padding',
					'heading' => esc_html__( 'Item Vertical Padding', 'total-theme-core' ),
					'param_name' => 'sub_item_padding_block',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__sub .vcex-off-canvas-menu-nav__item-content', 'property' => 'padding-block' ],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Color', 'total-theme-core' ),
					'param_name' => 'sub_item_color',
					'css' => [
						'selector' => '.vcex-off-canvas-menu-nav__sub .vcex-off-canvas-menu-nav__item-content',
						'property' => 'color'
					],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Color: Hover', 'total-theme-core' ),
					'param_name' => 'sub_item_color_hover',
					'css' => [
						'selector' => '.vcex-off-canvas-menu-nav__sub .vcex-off-canvas-menu-nav__item-content:hover',
						'property' => 'color',
					],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Background: Hover', 'total-theme-core' ),
					'param_name' => 'sub_item_bg_hover',
					'css' => [
						'selector' => '.vcex-off-canvas-menu-nav__sub .vcex-off-canvas-menu-nav__item-content:hover',
						'property' => 'background-color',
					],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Item Font Weight', 'total-theme-core' ),
					'param_name' => 'sub_item_font_weight',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__sub .vcex-off-canvas-menu-nav__item-content', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'text_transform',
					'heading' => esc_html__( 'Item Text Transform', 'total-theme-core' ),
					'param_name' => 'sub_item_text_transform',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__sub .vcex-off-canvas-menu-nav__item-content', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'letter_spacing',
					'heading' => esc_html__( 'Item Letter Spacing', 'total-theme-core' ),
					'param_name' => 'sub_item_letter_spacing',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__sub .vcex-off-canvas-menu-nav__item-content', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Item Font Family', 'total-theme-core' ),
					'param_name' => 'sub_item_font_family',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__sub .vcex-off-canvas-menu-nav__item-content', 'property' => 'font-family' ],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Item Font Size', 'total-theme-core' ),
					'param_name' => 'sub_item_font_size',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__sub .vcex-off-canvas-menu-nav__item-content', 'property' => 'font-size' ],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Arrows', 'total-theme-core' ),
					'param_name' => 'sub_arrow_enable',
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'std' => 'chevron',
					'choices' => [
						'chevron' => esc_html__( 'Chevron', 'total-theme-core' ),
						'angle' => esc_html__( 'Angle', 'total-theme-core' ),
						'caret' => esc_html__( 'Caret', 'total-theme-core' ),
					],
					'heading' => esc_html__( 'Arrow Icon', 'total-theme-core' ),
					'param_name' => 'sub_arrow_icon',
					'dependency' => [ 'element' => 'sub_arrow_enable', 'value' => 'true' ],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'gap',
					'heading' => esc_html__( 'Arrow Spacing', 'total-theme-core' ),
					'param_name' => 'sub_arrow_spacing',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__item-content', 'property' => 'gap' ],
					'dependency' => [ 'element' => 'sub_arrow_enable', 'value' => 'true' ],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Arrow Size', 'total-theme-core' ),
					'param_name' => 'sub_arrow_font_size',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__arrow-icon', 'property' => 'font-size' ],
					'dependency' => [ 'element' => 'sub_arrow_enable', 'value' => 'true' ],
					'group' => esc_html__( 'Sub Menus', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Top/Title
				[
					'type' => 'attach_image',
					'heading' => esc_html__( 'Image', 'total-theme-core' ),
					'param_name' => 'logo',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Max Width', 'total-theme-core' ),
					'param_name' => 'logo_max_width',
					'css' => [ 'selector' => '.wpex-off-canvas__logo img', 'property' => 'max-width' ],
					'dependency' => [ 'element' => 'logo', 'not_empty' => true ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Text', 'total-theme-core' ),
					'param_name' => 'title',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => [ 'element' => 'logo', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'title_color',
					'css' => [ 'selector' => '.wpex-off-canvas__title', 'property' => 'color' ],
					'dependency' => [ 'element' => 'title', 'not_empty' => true ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'title_font_weight',
					'css' => [ 'selector' => '.wpex-off-canvas__title', 'property' => 'font-weight' ],
					'dependency' => [ 'element' => 'title', 'not_empty' => true ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'title_font_family',
					'css' => [ 'selector' => '.wpex-off-canvas__title', 'property' => 'font-family' ],
					'dependency' => [ 'element' => 'title', 'not_empty' => true ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'title_font_size',
					'css' => [ 'selector' => '.wpex-off-canvas__title', 'property' => 'font-size' ],
					'dependency' => [ 'element' => 'title', 'not_empty' => true ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Border', 'total-theme-core' ),
					'description' => esc_html__( 'Enable a border below your custom title/logo.', 'total-theme-core' ),
					'param_name' => 'top_border_enable',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'top_border_color',
					'dependency' => [ 'element' => 'top_border_enable', 'value' => 'true' ],
					'css' => [ 'selector' => '.wpex-off-canvas__header', 'property' => 'border-color' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'placeholder' => '20px',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'top_margin_bottom',
					'description' => esc_html__( 'Bottom margin applied to the mobile menu title and/or close button.', 'total-theme-core' ),
					'css' => [ 'selector' => '.wpex-off-canvas__header', 'property' => 'margin-block-end' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Dropdown > Mega.
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Headings', 'total-theme-core' ),
					'param_name' => 'mega_heading_enabled',
					'group' => esc_html__( 'Mega', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Heading Color', 'total-theme-core' ),
					'param_name' => 'mega_heading_color',
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__mega-heading', 'property' => 'color' ],
					'dependency' => [ 'element' => 'mega_heading_enabled', 'value' => 'true' ],
					'group' => esc_html__( 'Mega', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Heading Font Family', 'total-theme-core' ),
					'param_name' => 'mega_heading_font_family',
					'dependency' => [ 'element' => 'mega_heading_enabled', 'value' => 'true' ],
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__mega-heading', 'property' => 'font-family' ],
					'group' => esc_html__( 'Mega', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Heading Font Size', 'total-theme-core' ),
					'param_name' => 'mega_heading_font_size',
					'dependency' => [ 'element' => 'mega_heading_enabled', 'value' => 'true' ],
					'css' => [ 'selector' => '.vcex-off-canvas-menu-nav__mega-heading', 'property' => 'font-size' ],
					'group' => esc_html__( 'Mega', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Footer Template
				[
					'type' => 'vcex_select',
					'choices' => 'template',
					'template_type' => 'part',
					'heading' => esc_html__( 'Footer Template', 'total-theme-core' ),
					'param_name' => 'bottom_template',
					'group' => esc_html__( 'Footer', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Footer Button Link', 'total-theme-core' ),
					'param_name' => 'bottom_button_link',
					'dependency' => [ 'element' => 'bottom_template', 'is_empty' => true ],
					'group' => esc_html__( 'Footer', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Footer Button Link Text', 'total-theme-core' ),
					'param_name' => 'bottom_button_text',
					'dependency' => [ 'element' => 'bottom_button_link', 'not_empty' => true ],
					'group' => esc_html__( 'Footer', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Fixed at Bottom', 'total-theme-core' ),
					'param_name' => 'fixed_footer',
					'group' => esc_html__( 'Footer', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Border', 'total-theme-core' ),
					'param_name' => 'bottom_border_enable',
					'group' => esc_html__( 'Footer', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'bottom_border_color',
					'dependency' => [ 'element' => 'bottom_border_enable', 'value' => 'true' ],
					'css' => [ 'selector' => '.wpex-off-canvas__footer', 'property' => 'border-color' ],
					'group' => esc_html__( 'Footer', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Close Button
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'close_btn_enable',
					'group' => esc_html__( 'Close', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'close_btn_color',
					'dependency' => [ 'element' => 'close_btn_enable', 'value' => 'true' ],
					'group' => esc_html__( 'Close', 'total-theme-core' ),
					'css' => [ 'selector' => '.wpex-off-canvas__close', 'property' => 'color' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'close_btn_color_hover',
					'dependency' => [ 'element' => 'close_btn_enable', 'value' => 'true' ],
					'group' => esc_html__( 'Close', 'total-theme-core' ),
					'css' => [ 'selector' => '.wpex-off-canvas__close:hover', 'property' => 'color' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Size', 'total-theme-core' ),
					'param_name' => 'close_btn_size',
					'dependency' => [ 'element' => 'close_btn_enable', 'value' => 'true' ],
					'css' => [ 'selector' => '.wpex-off-canvas__close', 'property' => 'font-size' ],
					'group' => esc_html__( 'Close', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Extra Items
				[
					'type' => 'vcex_sorter',
					'heading' => esc_html__( 'Icons', 'total-theme-core' ),
					'param_name' => 'extra_items',
					'choices' => $extra_choices,
					'description' => esc_html__( 'Click and drag to sort items.', 'total-theme-core' ),
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Icons Gap', 'total-theme-core' ),
					'param_name' => 'extra_items_gap',
					'choices' => 'gap',
					'dependency' => [ 'element' => 'extra_items', 'not_empty' => true ],
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Icons Placement', 'total-theme-core' ),
					'param_name' => 'extra_items_position',
					'std' => 'start',
					'choices' => [
						'start' => esc_html__( 'Start', 'total-theme-core' ),
						'end' => esc_html__( 'End', 'total-theme-core' ),
					],
					'dependency' => [ 'element' => 'extra_items', 'not_empty' => true ],
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'extra_items_font_size',
					'css' => [ 'selector' => '.vcex-off-canvas-menu__extra-item', 'property' => 'font-size' ],
					'dependency' => [ 'element' => 'extra_items', 'not_empty' => true ],
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Search Icon', 'total-theme-core' ),
					'param_name' => 'search_toggle_icon',
					'std' => 'search',
					'choices' => [
						'search' => 'search',
						'bootstrap-search' => 'bootstrap-search',
						'material-search' => 'material-search',
						'ionicons-search' => 'ionicons-search',
						'ionicons-search-outline' => 'ionicons-search-outline',
						'ionicons-search-sharp' => 'ionicons-search-sharp',
					],
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'dependency' => [ 'element' => 'extra_items', 'value_includes' => 'search_toggle' ],
					'elementor' => [
						'type' => 'select',
						'conditions' => [
							'terms' => [
								[
									'name' => 'extra_items',
									'operator' => 'contains',
									'value' => 'search_toggle',
								],
							],
						],
						'choices' => [
							'search' => esc_html__( 'Default', 'total-theme-core' ),
							'bootstrap-search' => esc_html__( 'Bootstrap', 'total-theme-core' ),
							'material-search' => esc_html__( 'Material Design', 'total-theme-core' ),
							'ionicons-search' => esc_html__( 'Ionicons', 'total-theme-core' ),
							'ionicons-search-outline' => esc_html__( 'Ionicons: Outline', 'total-theme-core' ),
							'ionicons-search-sharp' => esc_html__( 'Ionicons: Sharp', 'total-theme-core' ),
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
			];

			if ( array_key_exists( 'cart_toggle', $extra_choices ) ) {
				$settings[] = [
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Cart Icon', 'total-theme-core' ),
					'param_name' => 'cart_toggle_icon',
					'std' => 'shopping-cart-alt',
					'choices' => 'TotalThemeCore\Vcex\WooCommerce::get_cart_icon_choices',
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'dependency' => [ 'element' => 'extra_items', 'value_includes' => 'cart_toggle' ],
					'elementor' => [
						'type' => 'select',
						'choices' => array_merge(
							[ '' => esc_html__( 'Default', 'total-theme-core' ) ],
							\TotalThemeCore\Vcex\WooCommerce::get_cart_icon_choices( true )
						),
						'conditions' => [
							'terms' => [
								[
									'name' => 'extra_items',
									'operator' => 'contains',
									'value' => 'cart_toggle',
								],
							],
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				];
				// Cart Badge
				$settings[] = [
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Cart Badge', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to display a badge when items are in the cart.', 'total-theme-core' ),
					'param_name' => 'cart_badge',
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'dependency' => [ 'element' => 'extra_items', 'value_includes' => 'cart_toggle' ],
					'elementor' => [
						'conditions' => [
							'terms' => [
								[
									'name' => 'extra_items',
									'operator' => 'contains',
									'value' => 'cart_toggle',
								],
							],
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				];
				$settings[] = [
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Display Cart Count', 'total-theme-core' ),
					'param_name' => 'cart_badge_count',
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'dependency' => array( 'element' => 'cart_badge', 'value' => 'true' ),
					'elementor' => [
						'conditions' => [
							'terms' => [
								[
									'name' => 'extra_items',
									'operator' => 'contains',
									'value' => 'cart_toggle',
								],
								[
									'name' => 'cart_badge',
									'operator' => '===',
									'value' => 'true',
								],
							],
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				];
				$settings[] = [
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Backgound', 'total-theme-core' ),
					'param_name' => 'cart_badge_bg',
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-bg' ],
					'dependency' => array( 'element' => 'cart_badge', 'value' => 'true' ),
					'elementor' => [
						'conditions' => [
							'terms' => [
								[
									'name' => 'extra_items',
									'operator' => 'contains',
									'value' => 'cart_toggle',
								],
								[
									'name' => 'cart_badge',
									'operator' => '===',
									'value' => 'true',
								],
							],
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				];
				$settings[] = [
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'cart_badge_color',
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-color' ],
					'dependency' => array( 'element' => 'cart_badge_count', 'value' => 'true' ),
					'elementor' => [
						'conditions' => [
							'terms' => [
								[
									'name' => 'extra_items',
									'operator' => 'contains',
									'value' => 'cart_toggle',
								],
								[
									'name' => 'cart_badge',
									'operator' => '===',
									'value' => 'true',
								],
							],
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				];
				$settings[] = [
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Dimensions', 'total-theme-core' ),
					'param_name' => 'cart_badge_dims',
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-dims' ],
					'dependency' => array( 'element' => 'cart_badge', 'value' => 'true' ),
					'elementor' => [
						'conditions' => [
							'terms' => [
								[
									'name' => 'extra_items',
									'operator' => 'contains',
									'value' => 'cart_toggle',
								],
								[
									'name' => 'cart_badge',
									'operator' => '===',
									'value' => 'true',
								],
							],
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				];
				$settings[] = [
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'cart_badge_font_size',
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-font-size' ],
					'dependency' => array( 'element' => 'cart_badge_count', 'value' => 'true' ),
					'elementor' => [
						'conditions' => [
							'terms' => [
								[
									'name' => 'extra_items',
									'operator' => 'contains',
									'value' => 'cart_toggle',
								],
								[
									'name' => 'cart_badge',
									'operator' => '===',
									'value' => 'true',
								],
							],
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				];
				$settings[] = [
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Vertical OffSet', 'total-theme-core' ),
					'param_name' => 'cart_badge_top',
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-top' ],
					'dependency' => array( 'element' => 'cart_badge', 'value' => 'true' ),
					'elementor' => [
						'conditions' => [
							'terms' => [
								[
									'name' => 'extra_items',
									'operator' => 'contains',
									'value' => 'cart_toggle',
								],
								[
									'name' => 'cart_badge',
									'operator' => '===',
									'value' => 'true',
								],
							],
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				];
				$settings[] = [
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Horizontal OffSet', 'total-theme-core' ),
					'param_name' => 'cart_badge_right',
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-right' ],
					'dependency' => array( 'element' => 'cart_badge', 'value' => 'true' ),
					'elementor' => [
						'conditions' => [
							'terms' => [
								[
									'name' => 'extra_items',
									'operator' => 'contains',
									'value' => 'cart_toggle',
								],
								[
									'name' => 'cart_badge',
									'operator' => '===',
									'value' => 'true',
								],
							],
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				];
				$settings[] = [
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Outline Width', 'total-theme-core' ),
					'param_name' => 'cart_badge_outline_width',
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-outline-width' ],
					'dependency' => array( 'element' => 'cart_badge', 'value' => 'true' ),
					'elementor' => [
						'conditions' => [
							'terms' => [
								[
									'name' => 'extra_items',
									'operator' => 'contains',
									'value' => 'cart_toggle',
								],
								[
									'name' => 'cart_badge',
									'operator' => '===',
									'value' => 'true',
								],
							],
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				];
				$settings[] = [
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Outline Color', 'total-theme-core' ),
					'param_name' => 'cart_badge_outline_color',
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
					'css' => [ 'property' => '--wpex-cart-badge-outline-color' ],
					'dependency' => array( 'element' => 'cart_badge', 'value' => 'true' ),
					'elementor' => [
						'conditions' => [
							'terms' => [
								[
									'name' => 'extra_items',
									'operator' => 'contains',
									'value' => 'cart_toggle',
								],
								[
									'name' => 'cart_badge',
									'operator' => '===',
									'value' => 'true',
								],
							],
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				];
			}

			return $settings;
		}

		/**
		 * Returns extra item choices.
		 */
		public static function _get_extra_choices(): array {
			$choices = [
				'search_toggle' => esc_html__( 'Search', 'total-theme-core' ),
			];
			if ( function_exists( 'totaltheme_call_static' ) && totaltheme_call_static( 'Dark_Mode', 'is_enabled' ) ) {
				$choices['dark_mode_toggle'] = esc_html__( 'Dark Mode', 'total-theme-core' );
			}
			if ( class_exists( 'WooCommerce', false ) ) {
				$choices['cart_toggle'] = esc_html__( 'Cart', 'total-theme-core' );
			}
			return $choices;
		}

		/**
		 * Stores a counter each time an off canvas is rendered so they each have a unique ID.
		 */
		public static function get_counter(): int {
			static $count = 0;
			return $count++;
		}

		/**
		 * Advanced CSS.
		 */
		protected static function css_pre_render( $css, $atts = [] ): void {
			if ( vcex_validate_att_boolean( 'item_divider', $atts ) && ! empty( $atts['item_padding_y'] ) ) {
				$css->add_extra_css( [
					'selector' => '.vcex-off-canvas-menu-nav__sub',
					'property' => 'margin-block-end',
					'val'      => sanitize_text_field( $atts['item_padding_y'] ),
				] );
			}
		}

	}
}

new Vcex_Off_Canvas_Menu_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Menu' ) ) {
	class WPBakeryShortCode_Vcex_Menu extends WPBakeryShortCode {}
}
