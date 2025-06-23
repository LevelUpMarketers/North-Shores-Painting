<?php

defined( 'ABSPATH' ) || exit;

/**
 * Custom Field Shortcode.
 */
if ( ! class_exists( 'VCEX_ACF_Repeater_Shortcode' ) ) {

	class VCEX_ACF_Repeater_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_acf_repeater';

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
			return esc_html__( 'ACF Repeater', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Render an ACF repeater field template', 'total-theme-core' );
		}

		/**
		 * Returns custom vc map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			return [
				'admin_enqueue_js' => 'acf-repeater',
				'js_view'          => 'vcexBackendViewAcfRepeater',
			];
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_select',
					'choices' => 'acf_repeater_fields',
					'heading' => esc_html__( 'Field', 'total-theme-core' ),
					'param_name' => 'key',
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'acf_repeater_templates',
					'heading' => esc_html__( 'Template', 'total-theme-core' ),
					'param_name' => 'template',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Fallback Value', 'total-theme-core' ),
					'param_name' => 'fallback',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'autocomplete',
					'settings' => [
						'multiple' => false,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => false,
						'delay' => 0,
						'auto_focus' => true,
					],
					'heading' => esc_html__( 'Preview Post', 'total-theme-core' ),
					'description' => esc_html__( 'Select a post to use as a preview while working with dynamic templates in the frontend editor.', 'total-theme-core' ),
					'elementor' => [
						'type' => 'text',
						'description' => esc_html__( 'Enter a post ID to use as a preview while working with dynamic templates in the frontend editor.', 'total-theme-core' ),
					],
					'param_name' => 'preview_id',
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
					'type' => 'dropdown',
					'heading' => esc_html__( 'Display Type', 'total-theme-core' ),
					'param_name' => 'display_type',
					'value' => [
						esc_html__( 'List', 'total-theme-core' ) => 'list',
						esc_html__( 'Grid', 'total-theme-core' ) => 'grid',
						esc_html__( 'Carousel', 'total-theme-core' ) => 'carousel',
						esc_html__( 'Flex Container', 'total-theme-core' ) => 'flex_wrap',
						esc_html__( 'Horizontal Scroll', 'total-theme-core' ) => 'flex',
						esc_html__( 'Unstyled List (ul)', 'total-theme-core' ) => 'ul_list',
						esc_html__( 'Ordered List (ol)', 'total-theme-core' ) => 'ol_list',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Shrink Items', 'total-theme-core' ),
					'param_name' => 'flex_shrink',
					'description' => esc_html__( 'By default entries will shrink to fit on the screen. Disable this option to allow them to expand to their natural width.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'display_type', 'value' => 'flex' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Hide Scrollbar', 'total-theme-core' ),
					'param_name' => 'hide_scrollbar',
					'dependency' => [ 'element' => 'display_type', 'value' => 'flex' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Flex Basis', 'total-theme-core' ),
					'param_name' => 'flex_basis',
					'choices' => [
						'' => esc_html__( 'Auto', 'total-theme-core' ),
						'1' => esc_html__( '1 Column', 'total-theme-core' ),
						'2' => esc_html__( '2 Columns', 'total-theme-core' ),
						'3' => esc_html__( '3 Columns', 'total-theme-core' ),
						'4' => esc_html__( '4 Columns', 'total-theme-core' ),
						'5' => esc_html__( '5 Columns', 'total-theme-core' ),
						'6' => esc_html__( '6 Columns', 'total-theme-core' ),
					],
					'description' => esc_html__( 'Set the initial size (width) for your entries.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'display_type', 'value' => [ 'flex', 'flex_wrap' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Flex Justification', 'total-theme-core' ),
					'param_name' => 'flex_justify',
					'choices' => 'justify_content',
					'dependency' => [ 'element' => 'display_type', 'value' => [ 'flex', 'flex_wrap' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Scroll Snap Type', 'total-theme-core' ),
					'param_name' => 'flex_scroll_snap_type',
					'choices' => [
						'proximity' => esc_html__( 'Proximity', 'total-theme-core' ),
						'mandatory' => esc_html__( 'Mandatory', 'total-theme-core' ),
						'none' => esc_html__( 'None', 'total-theme-core' ),
					],
					'description' => esc_html__( 'Sets how strictly snap points are enforced on the scroll container in case there is one.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'display_type', 'value' => 'flex' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Flex Breakpoint', 'total-theme-core' ),
					'param_name' => 'flex_breakpoint',
					'choices' => 'breakpoint',
					'description' => esc_html__( 'The breakpoint at which the entries will stack vertically. By default the flex container will create a horizontal scroll bar instead of stacking.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'display_type', 'value' => 'flex' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_grid_columns',
					'heading' => esc_html__( 'Columns', 'total-theme-core' ),
					'param_name' => 'grid_columns',
					'std' => '3',
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency' => [ 'element' => 'display_type', 'value' => 'grid' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Responsive', 'total-theme-core' ),
					'param_name' => 'grid_columns_responsive',
					'value' => [
						esc_html__( 'Yes', 'total-theme-core' ) => 'true',
						esc_html__( 'No', 'total-theme-core' ) => 'false',
					],
					'edit_field_class' => 'vc_col-sm-6 vc_column',
					'dependency' => [ 'element' => 'grid_columns', 'value' => [ '2', '3', '4', '5', '6', '7', '8', '9', '10' ] ],
				],
				[
					'type' => 'vcex_grid_columns_responsive',
					'heading' => esc_html__( 'Responsive Column Settings', 'total-theme-core' ),
					'param_name' => 'grid_columns_responsive_settings',
					'dependency' => [ 'element' => 'grid_columns_responsive', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Gap', 'total-theme-core' ),
					'param_name' => 'gap',
					'css' => [ 'selector' => '.vcex-acf-repeater__list', 'property' => 'gap' ],
					'dependency' => [
						'element' => 'display_type',
						'value_not_equal_to' => 'carousel',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'List Divider', 'total-theme-core' ),
					'param_name' => 'list_divider',
					'value' => [
						esc_html__( 'None', 'total-theme-core' ) => '',
						esc_html__( 'Solid', 'total-theme-core' ) => 'solid',
						esc_html__( 'Dashed', 'total-theme-core' ) => 'dashed',
						esc_html__( 'Dotted', 'total-theme-core' ) => 'dotted',
					],
					'dependency' => [ 'element' => 'display_type', 'value' => [ 'list', 'ul_list', 'ol_list' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_width',
					'heading' => esc_html__( 'List Divider Size', 'total-theme-core' ),
					'param_name' => 'list_divider_size',
					'dependency' => [ 'element' => 'list_divider', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'List Divider Color', 'total-theme-core' ),
					'param_name' => 'list_divider_color',
					'dependency' => [ 'element' => 'list_divider', 'not_empty' => true ],
					'css' => [
						'property' => 'border-color',
						'selector' => '.vcex-acf-repeater__divider',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Divider Before First Entry', 'total-theme-core' ),
					'param_name' => 'list_divider_before',
					'std' => 'true',
					'dependency' => [ 'element' => 'list_divider', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Divider After Last Entry', 'total-theme-core' ),
					'param_name' => 'list_divider_after',
					'std' => 'true',
					'dependency' => [ 'element' => 'list_divider', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Carousel Settings.
				[
					'type' => 'vcex_subheading',
					'param_name' => 'vcex_subheading__carousel',
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'text' => \esc_html__( 'Carousel Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'std' => '',
					'choices' => [
						'' => esc_html__( 'Disabled', 'total-theme-core' ),
						'end' => esc_html__( 'End Only', 'total-theme-core' ),
						'start-end' => esc_html__( 'Both Sides', 'total-theme-core' ),
					],
					'heading' => esc_html__( 'Bleed', 'total-theme-core' ),
					'description' => esc_html__( 'This setting allows items to overflow. Make sure your carousel has enough items to function properly on large screens. Note: This is a complex feature that may not work in every situation.', 'total-theme-core' ),
					'param_name' => 'carousel_bleed',
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Bleed Overlay Color', 'total-theme-core' ),
					'description' => esc_html__( 'Overlay added over the hidden items on the non-bleeding side.', 'total-theme-core' ),
					'param_name' => 'carousel_bleed_overlay_bg',
					'css' => [ 'property' => '--wpex-carousel-bleed-overlay-bg' ],
					'dependency' => [ 'element' => 'carousel_bleed', 'value' => 'end' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Arrows', 'total-theme-core' ),
					'param_name' => 'arrows',
					'std' => 'true',
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'carousel_arrow_styles',
					'heading' => esc_html__( 'Arrows Style', 'total-theme-core' ),
					'param_name' => 'arrows_style',
					'dependency' => [ 'element' => 'arrows', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'carousel_arrow_positions',
					'heading' => esc_html__( 'Arrows Position', 'total-theme-core' ),
					'param_name' => 'arrows_position',
					'dependency' => [ 'element' => 'arrows', 'value' => 'true' ],
					'std' => 'default',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Dot Navigation', 'total-theme-core' ),
					'param_name' => 'dots',
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Auto Play', 'total-theme-core' ),
					'param_name' => 'auto_play',
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => \esc_html__( 'Autoplay Type', 'total-theme-core' ),
					'param_name' => 'autoplay_type',
					'std' => 'default',
					'choices' => [
						'default' => \esc_html__( 'Default', 'total-theme-core' ),
						'smooth' => \esc_html__( 'Smooth', 'total-theme-core' ),
					],
					'description' => \esc_html__( 'The "Smooth" autoplay type will remove the carousel arrows and dot navigation. Items will scroll automatically and can\'t be paused, ideal for displaying logos.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'auto_play', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'input_type' => 'number',
					'heading' => esc_html__( 'Autoplay Interval Timeout.', 'total-theme-core' ),
					'param_name' => 'timeout_duration',
					'placeholder' => '5000',
					'description' => esc_html__( 'Time in milliseconds between each auto slide.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'autoplay_type', 'value' => 'default' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => \esc_html__( 'Pause on Hover', 'total-theme-core' ),
					'param_name' => 'hover_pause',
					'std' => 'true',
					'dependency' => [ 'element' => 'autoplay_type', 'value' => 'default' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Infinite Loop', 'total-theme-core' ),
					'param_name' => 'infinite_loop',
					'std' => 'true',
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Center Item', 'total-theme-core' ),
					'description' => \esc_html__( 'Enable to center the middle slide when displaying slides divisible by 2.', 'total-theme-core' ),
					'param_name' => 'center',
					'std' => 'false',
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'input_type' => 'number',
					'heading' => esc_html__( 'Items To Display', 'total-theme-core' ),
					'param_name' => 'items',
					'placeholder' => '4',
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => \totalthemecore_call_static( 'Vcex\Carousel\Core', 'get_out_animation_choices' ),
					'heading' => esc_html__( 'Animation', 'total-theme-core' ),
					'param_name' => 'out_animation',
					'dependency' => [ 'element' => 'items', 'value' => '1' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'input_type' => 'number',
					'placeholder' => '250',
					'heading' => esc_html__( 'Animation Speed', 'total-theme-core' ),
					'param_name' => 'animation_speed',
					'description' => \esc_html__( 'Time it takes to transition between slides. In milliseconds.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Auto Height?', 'total-theme-core' ),
					'param_name' => 'auto_height',
					'dependency' => [ 'element' => 'items', 'value' => '1' ],
					'description' => esc_html__( 'Allows the carousel to change height based on the active item. This setting is used only when you are displaying 1 item per slide.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'input_type' => 'number',
					'heading' => esc_html__( 'Items To Scrollby', 'total-theme-core' ),
					'param_name' => 'items_scroll',
					'placeholder' => '1',
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'input_type' => 'number',
					'heading' => esc_html__( 'Tablet: Items To Display', 'total-theme-core' ),
					'param_name' => 'tablet_items',
					'placeholder' => '3',
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'input_type' => 'number',
					'heading' => esc_html__( 'Mobile Landscape: Items To Display', 'total-theme-core' ),
					'param_name' => 'mobile_landscape_items',
					'placeholder' => '2',
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'input_type' => 'number',
					'heading' => esc_html__( 'Mobile Portrait: Items To Display', 'total-theme-core' ),
					'param_name' => 'mobile_portrait_items',
					'placeholder' => '1',
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'input_type' => 'number',
					'heading' => esc_html__( 'Margin Between Items', 'total-theme-core' ),
					'description' => esc_html__( 'Value in pixels.', 'total-theme-core' ),
					'param_name' => 'items_margin',
					'placeholder' => '15',
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Heading
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Heading', 'total-theme-core' ),
					'param_name' => 'heading',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'HTML Tag', 'total-theme-core' ),
					'param_name' => 'heading_tag',
					'std' => 'h2',
					'choices' => 'html_tag',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'dependency' => [ 'element' => 'heading', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'header_style',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'heading_style',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'description' => self::param_description( 'header_style' ),
					'dependency' => [ 'element' => 'heading', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'choices' => 'margin',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'heading_margin_bottom',
					'css' => [ 'selector' => '.vcex-acf-repeater__heading', 'property' => 'margin-block-end' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'dependency' => [ 'element' => 'heading', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Max Width', 'total-theme-core' ),
					'param_name' => 'heading_max_width',
					'css' => [ 'selector' => '.vcex-acf-repeater__heading', 'property' => 'max-width' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'dependency' => [ 'element' => 'heading', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Text Color', 'total-theme-core' ),
					'param_name' => 'heading_color',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-acf-repeater__heading', 'property' => 'color' ],
					'dependency' => [ 'element' => 'heading', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'heading_border_color',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-acf-repeater__heading', 'property' => '--theme-heading-border-color' ],
					'dependency' => [ 'element' => 'heading_style', 'value' => [ 'border-side', 'border-bottom', 'border-w-color' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_width',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'heading_border_width',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-acf-repeater__heading', 'property' => '--theme-heading-border-width' ],
					'dependency' => [ 'element' => 'heading_style', 'value' => [ 'border-side', 'border-bottom', 'border-w-color' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_style',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'heading_border_style',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-acf-repeater__heading', 'property' => '--theme-heading-border-style' ],
					'dependency' => [ 'element' => 'heading_style', 'value' => [ 'border-side', 'border-bottom', 'border-w-color' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'heading_font_size',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-acf-repeater__heading', 'property' => 'font-size' ],
					'dependency' => [ 'element' => 'heading', 'not_empty' => true ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'heading_align',
					'dependency' => [ 'element' => 'heading', 'not_empty' => true ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Extra class and other
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				/* - seems like extra bloat...
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background_color',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'padding_all',
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'css' => [ 'property' => 'padding' ],
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
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
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
					'dependency' => array( 'element' => 'border_style', 'not_empty' => true ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => array( 'element' => 'border_width', 'not_empty' => true ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				*/
			];
		}

		/**
		 * Advanced CSS.
		 */
		protected static function css_pre_render( $css, $atts = [] ): void {
			if ( ! empty( $atts['flex_basis'] )
				&& ! empty( $atts['display_type'] )
				&& ( 'flex' === $atts['display_type'] || 'flex_wrap' === $atts['display_type'] )
			) {
				if ( ! empty( $atts['flex_breakpoint'] ) ) {
					$breakpoints = [
						'xl' => '1280px',
						'lg' => '1024px',
						'md' => '768px',
						'sm' => '640px',
					];
					$flex_bk_px = $breakpoints[ $atts['flex_breakpoint'] ] ?? null;
					if ( $flex_bk_px ) {
						$media_query = "@media only screen and (min-width: {$flex_bk_px})";
					}
				}
				$css->add_extra_css( [
					'selector'    => '.vcex-acf-repeater__item',
					'property'    => 'flex-basis',
					'val'         => vcex_get_flex_basis( $atts['flex_basis'], ! empty( $atts['gap'] ) ? $atts['gap'] : 25 ),
					'media_query' => $media_query ?? '',
				] );
			}
		}

		/**
		 * Register autocomplete hooks.
		 */
		public static function register_vc_autocomplete_hooks(): void {
			add_filter(
				'vc_autocomplete_vcex_acf_repeater_preview_id_callback',
				'vc_include_field_search'
			);
			add_filter(
				'vc_autocomplete_vcex_acf_repeater_preview_id_render',
				'vc_include_field_render'
			);
		}

	}

}

new VCEX_ACF_Repeater_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Acf_Repeater' ) ) {
	class WPBakeryShortCode_Vcex_Acf_Repeater extends WPBakeryShortCode {}
}
