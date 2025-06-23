<?php

defined( 'ABSPATH' ) || exit;

use TotalThemeCore\Vcex\Shortcode_CSS;

/**
 * Horizontal Menu Shortcode.
 */
if ( ! class_exists( 'Vcex_Horizontal_Menu_Shortcode' ) ) {

	class Vcex_Horizontal_Menu_Shortcode extends \TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_horizontal_menu';

		/**
		 * Holds array of extras to insert at the end of the menu (search,cart,dark mode).
		 */
		private static $extra_items = [];

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
			return esc_html__( 'Horizontal Menu', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Horizontal menu with dropdowns & mobile support', 'total-theme-core' );
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
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$extra_choices = self::_get_extra_choices();

			$settings = [
				[
					'type' => 'vcex_text',
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
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'std' => 'start',
					'choices' => [
						'start' => esc_html__( 'Start', 'total-theme-core' ),
						'center' => esc_html__( 'Center', 'total-theme-core' ),
						'end' => esc_html__( 'End', 'total-theme-core' ),
					],
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'inner_justify',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'justify_content',
					'heading' => esc_html__( 'Horizontal Justification', 'total-theme-core' ),
					'description' => esc_html__( 'Controls how items will display when stacked if the menu doesn\'t fit on a single line.', 'total-theme-core' ),
					'param_name' => 'nav_list_justify',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background Color', 'total-theme-core' ),
					'param_name' => 'background_color',
					'css' => [ 'property' => 'background-color' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'border_radius',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'css' => [ 'property' => 'border-radius' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
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
				// Nav
				[
					'type' => 'vcex_text',
					'placeholder' => 'none',
					'heading' => esc_html__( 'Item Minimum Height', 'total-theme-core' ),
					'param_name' => 'item_min_height',
					'description' => esc_html__( 'Used primarily for adding extra space between your menu items and dropdowns.', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__list > .vcex-horizontal-menu-nav__item', 'property' => 'min-height' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'align_items',
					'heading' => esc_html__( 'Item Vertical Alignment', 'total-theme-core' ),
					'param_name' => 'item_align',
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Item Background Hover', 'total-theme-core' ),
					'param_name' => 'item_bg_hover_enable',
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'padding',
					'heading' => esc_html__( 'Item Vertical Padding', 'total-theme-core' ),
					'param_name' => 'item_padding_y',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item-content', 'property' => 'padding-block' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'padding',
					'heading' => esc_html__( 'Item Horizontal Padding', 'total-theme-core' ),
					'param_name' => 'item_padding_x',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item-content', 'property' => 'padding-inline' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'gap',
					'heading' => esc_html__( 'Space Between Items (Gap)', 'total-theme-core' ),
					'param_name' => 'nav_list_gap',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__list', 'property' => 'gap' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'border_radius',
					'heading' => esc_html__( 'Item Border Radius', 'total-theme-core' ),
					'param_name' => 'item_border_radius',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item-content', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Color', 'total-theme-core' ),
					'param_name' => 'item_color',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item-content', 'property' => 'color' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Color: Hover', 'total-theme-core' ),
					'param_name' => 'item_color_hover',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item-content:hover', 'property' => 'color' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Background: Hover', 'total-theme-core' ),
					'param_name' => 'item_bg_hover',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item-content:hover', 'property' => 'background-color' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Heading Color', 'total-theme-core' ),
					'param_name' => 'item_heading_color',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item-heading', 'property' => 'color' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Description Color', 'total-theme-core' ),
					'param_name' => 'item_desc_color',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item-desc', 'property' => 'color' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Icon Color', 'total-theme-core' ),
					'param_name' => 'item_icon_color',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__icon', 'property' => 'color' ],
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
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item-content', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'text_transform',
					'heading' => esc_html__( 'Item Text Transform', 'total-theme-core' ),
					'param_name' => 'item_text_transform',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item-content', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'letter_spacing',
					'heading' => esc_html__( 'Item Letter Spacing', 'total-theme-core' ),
					'param_name' => 'item_letter_spacing',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item-content', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Item Font Family', 'total-theme-core' ),
					'param_name' => 'item_font_family',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item-content', 'property' => 'font-family' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Item Font Size', 'total-theme-core' ),
					'param_name' => 'item_font_size',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item-content', 'property' => 'font-size' ],
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'item_typography',
					'selector' => '.vcex-horizontal-menu-nav__item-content',
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Current
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Active Page Highlight', 'total-theme-core' ),
					'param_name' => 'item_current_highlight',
					'group' => esc_html__( 'Active State', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Active Color', 'total-theme-core' ),
					'param_name' => 'item_current_color',
					'css' => [
						'selector' => '.vcex-horizontal-menu-nav__list > .current-menu-item > .vcex-horizontal-menu-nav__item-content',
						'property' => 'color'
					],
					'dependency' => [ 'element' => 'item_current_highlight', 'value' => 'true' ],
					'group' => esc_html__( 'Active State', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Active Background', 'total-theme-core' ),
					'css' => [
						'selector' => '.vcex-horizontal-menu-nav__list > .current-menu-item > .vcex-horizontal-menu-nav__item-content',
						'property' => 'background-color'
					],
					'param_name' => 'item_current_bg',
					'dependency' => [ 'element' => 'item_current_highlight', 'value' => 'true' ],
					'group' => esc_html__( 'Active State', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Active Page Underline', 'total-theme-core' ),
					'param_name' => 'item_current_underline',
					'group' => esc_html__( 'Active State', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => [
						''  => esc_html__( 'Default', 'total-theme-core' ),
						'1px' => '1px',
						'2px' => '2px',
						'4px' => '4px',
						'8px' => '8px',
					],
					'heading' => esc_html__( 'Underline Offset', 'total-theme-core' ),
					'param_name' => 'item_current_underline_offset',
					'dependency' => [ 'element' => 'item_current_underline', 'value' => 'true' ],
					'group' => esc_html__( 'Active State', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => [
						''  => esc_html__( 'Default', 'total-theme-core' ),
						'1px' => '1px',
						'2px' => '2px',
						'4px' => '4px',
						'8px' => '8px',
					],
					'heading' => esc_html__( 'Underline Thickness', 'total-theme-core' ),
					'param_name' => 'item_current_underline_thickness',
					'dependency' => [ 'element' => 'item_current_underline', 'value' => 'true' ],
					'group' => esc_html__( 'Active State', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Underline Color', 'total-theme-core' ),
					'param_name' => 'item_current_underline_color',
					'dependency' => [ 'element' => 'item_current_underline', 'value' => 'true' ],
					'css' => [
						'selector' => '.vcex-horizontal-menu-nav__list > .current-menu-item > .vcex-horizontal-menu-nav__item-content',
						'property' => 'text-decoration-color'
					],
					'group' => esc_html__( 'Active State', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Dropdowns.
				[
					'type' => 'vcex_select_buttons',
					'std' => 'hover',
					'choices' => [
						'hover' => esc_html__( 'Hover', 'total-theme-core' ),
						'click' => esc_html__( 'Click', 'total-theme-core' ),
					],
					'heading' => esc_html__( 'Trigger', 'total-theme-core' ),
					'param_name' => 'sub_trigger',
					'description' => esc_html__( 'Select if dropdowns should display when hovering or clicking on menu items.', 'total-theme-core' ),
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'transition_duration',
					'heading' => esc_html__( 'Speed', 'total-theme-core' ),
					'param_name' => 'sub_speed',
					'css' => [ 'property' => '--wpex-dropmenu-transition-duration' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Animation', 'total-theme-core' ),
					'param_name' => 'sub_animate',
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'margin',
					'heading' => esc_html__( 'Animation Offset', 'total-theme-core' ),
					'param_name' => 'sub_animate_offset',
					'dependency' => [ 'element' => 'sub_animate', 'value' => 'true' ],
					'css' => [ 'property' => '--wpex-dropmenu-animate-offset' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'shadow',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'sub_shadow',
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Minimum Width', 'total-theme-core' ),
					'param_name' => 'sub_min_width',
					'placeholder' => '100%',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub', 'property' => 'min-width' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Maximum Width', 'total-theme-core' ),
					'param_name' => 'sub_max_width',
					'placeholder' => '320px',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav', 'property' => '--wpex-dropmenu-max-width' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'padding',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'sub_padding',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub', 'property' => 'padding' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'sub_bg',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub', 'property' => 'background-color' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_radius',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'sub_border_radius',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'padding',
					'heading' => esc_html__( 'Item Vertical Padding', 'total-theme-core' ),
					'param_name' => 'sub_item_padding_block',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__item-content', 'property' => 'padding-block' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'padding',
					'heading' => esc_html__( 'Item Horizontal Padding', 'total-theme-core' ),
					'param_name' => 'sub_item_padding_inline',
					'placeholder' => esc_html__( 'Inherit from main menu item padding', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__item-content', 'property' => 'padding-inline' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'border_radius',
					'heading' => esc_html__( 'Item Border Radius', 'total-theme-core' ),
					'param_name' => 'sub_item_border_radius',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__item-content', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Color', 'total-theme-core' ),
					'param_name' => 'sub_item_color',
					'css' => [
						'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__item-content',
						'property' => 'color'
					],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Color: Hover', 'total-theme-core' ),
					'param_name' => 'sub_item_color_hover',
					'css' => [
						'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__item-content:hover',
						'property' => 'color',
					],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Background: Hover', 'total-theme-core' ),
					'param_name' => 'sub_item_bg_hover',
					'css' => [
						'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__item-content:hover',
						'property' => 'background-color',
					],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Heading Color', 'total-theme-core' ),
					'param_name' => 'sub_item_heading_color',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__item-heading', 'property' => 'color' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Description Color', 'total-theme-core' ),
					'param_name' => 'sub_item_desc_color',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__item-desc', 'property' => 'color' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Item Icon Color', 'total-theme-core' ),
					'param_name' => 'sub_item_icon_color',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__icon', 'property' => 'color' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Item Font Weight', 'total-theme-core' ),
					'param_name' => 'sub_item_font_weight',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__item-content', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'text_transform',
					'heading' => esc_html__( 'Item Text Transform', 'total-theme-core' ),
					'param_name' => 'sub_item_text_transform',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__item-content', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'letter_spacing',
					'heading' => esc_html__( 'Item Letter Spacing', 'total-theme-core' ),
					'param_name' => 'sub_item_letter_spacing',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__item-content', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Item Font Family', 'total-theme-core' ),
					'param_name' => 'sub_item_font_family',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__item-content', 'property' => 'font-family' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Item Font Size', 'total-theme-core' ),
					'param_name' => 'sub_item_font_size',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__item-content', 'property' => 'font-size' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'sub_item_typography',
					'selector' => '.vcex-horizontal-menu-nav__sub .vcex-horizontal-menu-nav__item-content',
					'group' => esc_html__( 'Items', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
				],
				// Dropdown > Arrows.
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Arrows', 'total-theme-core' ),
					'param_name' => 'sub_arrow_enable',
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
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
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'gap',
					'heading' => esc_html__( 'Arrow Spacing', 'total-theme-core' ),
					'param_name' => 'sub_arrow_spacing',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item-content', 'property' => 'gap' ],
					'dependency' => [ 'element' => 'sub_arrow_enable', 'value' => 'true' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Arrow Size', 'total-theme-core' ),
					'param_name' => 'sub_arrow_font_size',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__arrow-icon', 'property' => 'font-size' ],
					'dependency' => [ 'element' => 'sub_arrow_enable', 'value' => 'true' ],
					'group' => esc_html__( 'Dropdowns', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Dropdown > Mega.
				[
					'type' => 'vcex_text',
					'placeholder' => 'max-content',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'description' => esc_html__( 'By default, mega menus will expand to fit the dropdown content. This setting can be used to constrain the mega menu width or extend it. You can use 100% to have mega menus expand to the width of the parent container. If you do this, it is best to also switch to the "click" method for dropdowns.', 'total-theme-core' ),
					'param_name' => 'mega_width',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__mega', 'property' => 'width' ],
					'group' => esc_html__( 'Mega', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'padding',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'mega_padding',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__mega', 'property' => 'padding' ],
					'group' => esc_html__( 'Mega', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'gap',
					'heading' => esc_html__( 'Column Gap', 'total-theme-core' ),
					'param_name' => 'mega_column_gap',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__mega', 'property' => 'column-gap' ],
					'group' => esc_html__( 'Mega', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'gap',
					'heading' => esc_html__( 'Row Gap', 'total-theme-core' ),
					'param_name' => 'mega_row_gap',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__mega', 'property' => 'row-gap' ],
					'group' => esc_html__( 'Mega', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
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
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__mega-heading', 'property' => 'color' ],
					'dependency' => [ 'element' => 'mega_heading_enabled', 'value' => 'true' ],
					'group' => esc_html__( 'Mega', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Heading Font Family', 'total-theme-core' ),
					'param_name' => 'mega_heading_font_family',
					'dependency' => [ 'element' => 'mega_heading_enabled', 'value' => 'true' ],
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__mega-heading', 'property' => 'font-family' ],
					'group' => esc_html__( 'Mega', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Heading Font Size', 'total-theme-core' ),
					'param_name' => 'mega_heading_font_size',
					'dependency' => [ 'element' => 'mega_heading_enabled', 'value' => 'true' ],
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__mega-heading', 'property' => 'font-size' ],
					'group' => esc_html__( 'Mega', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Extras
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
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'extra_item_font_size',
					'css' => [ 'selector' => '.vcex-horizontal-menu-nav__item--extra', 'property' => 'font-size' ],
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
					'dependency' => [ 'element' => 'extra_items', 'value_includes' => 'search_toggle' ],
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
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
					'group' => esc_html__( 'Extras', 'total-theme-core' ),
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
		 * Returns the wp nav menu.
		 */
		public static function render_wp_nav_menu( $atts ) {
			$extra_items = ! empty( $atts['extra_items'] ) ? $atts['extra_items'] : '';

			if ( $extra_items && ! is_array( $extra_items ) ) {
				$extra_items = wp_parse_list( sanitize_text_field( $extra_items ) );
			}

			self::$extra_items = $extra_items;

			$menu_class = [
				'vcex-horizontal-menu-nav__list',
				'wpex-flex',
				'wpex-flex-wrap',
				'wpex-m-0',
				'wpex-list-none',
			];

			if ( ! empty( $atts['nav_list_justify'] ) && 'start' !== $atts['nav_list_justify'] ) {
				$menu_class[] = vcex_parse_justify_content_class( $atts['nav_list_justify'] );
			}

			$menu_class[] = 'wpex-dropdown-menu';
			
			$trigger = ! empty( $atts['sub_trigger'] ) ? sanitize_text_field( $atts['sub_trigger'] ) : 'hover';

			$menu_class[] = sanitize_html_class( "wpex-dropdown-menu--on{$trigger}" );

			if ( vcex_validate_att_boolean( 'sub_animate', $atts, true ) ) {
				$menu_class[] = 'wpex-dropdown-menu--animate';
			}

			if ( self::$extra_items ) {
				add_filter( 'wp_nav_menu_objects', [self::class, '_insert_extras' ], 10, 2 );
			}

			$menu_id = ! empty( $atts['menu_id'] ) ? absint( $atts['menu_id'] ) : ( get_nav_menu_locations()['main_menu'] ?? 0 );

			wp_nav_menu( [
				'menu'            => absint( $menu_id ),
				'menu_class'      => implode( ' ', $menu_class ),
				'echo'            => true,
				'fallback_cb'     => false,
				'walker'          => totalthemecore_init_class( 'Vcex\Walkers\Nav_Menu_Horizontal' ),
				'vcex_atts'       => $atts,
				'vcex_uniquid'    => uniqid(),
				'container'       => '',
				'container_class' => '',
				'container_id'    => '',
			] );

			if ( self::$extra_items ) {
				remove_filter( 'wp_nav_menu_objects', [self::class, '_insert_extras' ], 10, 2 );
				self::$extra_items = [];
			}
		}

		/**
		 * Hooks into wp_nav_menu_objects to insert extra items.
		 */
		public static function _insert_extras( $items, $args ) {
			foreach ( self::$extra_items as $extra_item ) {
				$items[] = self::_new_toggle_item_object( $extra_item );
			}
			return $items;
		}

		/**
		 * Helper function creates a new menu item.
		 */
		private static function _new_toggle_item_object( $item_name ) {
			return (object ) [
				'ID'               => "vcex-extra-{$item_name}",
				'db_id'            => 0,
				'menu_item_parent' => '0',
				'object_id'        => 0,
				'post_parent'      => 0,
				'type'             => 'custom',
				'object'           => 'custom',
				'type_label'       => 'VCEX Extra',
				'title'            => '',
				'url'              => '#',
				'target'           => '',
				'attr_title'       => '',
				'description'      => '',
				'xfn'              => '',
				'current'          => false,
			];
		}

	}

}

new Vcex_Horizontal_Menu_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Menu' ) ) {
	class WPBakeryShortCode_Vcex_Menu extends WPBakeryShortCode {}
}
