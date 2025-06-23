<?php

defined( 'ABSPATH' ) || exit;

/**
 * Post Cards Shortcode.
 */
if ( ! class_exists( 'Wpex_Post_Cards_Shortcode' ) ) {

	class Wpex_Post_Cards_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'wpex_post_cards';

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
			return esc_html__( 'Post Cards', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Post based card list, grid or carousel.', 'total-theme-core' );
		}

		/**
		 * Shortcode custom output.
		 */
		public static function output( $atts, $content = null, $shortcode_tag = '' ): ?string {
			if ( ! is_array( $atts ) || ! class_exists( 'TotalThemeCore\Vcex\Post_Cards' ) ) {
				return null; // @note this element can't render without settings.
			}
			return (new TotalThemeCore\Vcex\Post_Cards( $atts ))->get_output();
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				// General
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Card Style', 'total-theme-core' ),
					'param_name' => 'card_style',
					'description' => self::param_description( 'card_select' ),
					'admin_label' => true,
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
						esc_html__( 'Grid', 'total-theme-core' ) => 'grid',
						esc_html__( 'List', 'total-theme-core' ) => 'list',
						esc_html__( 'Carousel', 'total-theme-core' ) => 'carousel',
						esc_html__( 'Flex Container', 'total-theme-core' ) => 'flex_wrap',
						esc_html__( 'Horizontal Scroll', 'total-theme-core' ) => 'flex',
						esc_html__( 'Unordered List (ul)', 'total-theme-core' ) => 'ul_list',
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
					'choices' => self::get_media_breakpoint_choices( false ),
					'description' => esc_html__( 'The breakpoint at which the entries will stack vertically. By default the flex container will create a horizontal scroll bar instead of stacking.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'display_type', 'value' => 'flex' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Grid Style', 'total-theme-core' ),
					'param_name' => 'grid_style',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => 'fit_rows',
						esc_html__( 'Masonry', 'total-theme-core' ) => 'masonry',
						esc_html__( 'Modern CSS Grid', 'total-theme-core' ) => 'css_grid',
					],
					'edit_field_class' => 'vc_col-sm-4 vc_column clear',
					'dependency' => [ 'element' => 'display_type', 'value' => [ 'grid', 'masonry_grid' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_grid_columns',
					'heading' => esc_html__( 'Columns', 'total-theme-core' ),
					'param_name' => 'grid_columns',
					'std' => '3',
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'dependency' => [ 'element' => 'display_type', 'value' => [ 'grid', 'masonry_grid' ] ],
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
					'edit_field_class' => 'vc_col-sm-4 vc_column',
					'dependency' => [ 'element' => 'grid_columns', 'value' => [ '2', '3', '4', '5', '6', '7', '8', '9', '10' ] ],
				],
				[
					'type' => 'vcex_grid_columns_responsive',
					'heading' => esc_html__( 'Responsive Column Settings', 'total-theme-core' ),
					'param_name' => 'grid_columns_responsive_settings',
					'dependency' => [ 'element' => 'grid_columns_responsive', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Maintain Horizontal Order', 'total-theme-core' ),
					'description' => esc_html__( 'By default, the masonry script preserves the order of your items. However, if there is a lot of empty space in your grid, you can disable this setting to allow items to better fill the gaps.', 'total-theme-core' ),
					'param_name' => 'masonry_horizontal_order',
					'dependency' => array( 'element' => 'grid_style', 'value' => [ 'masonry', 'no-margins' ] ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Column Gap', 'total-theme-core' ),
					'param_name' => 'grid_spacing',
					'choices' => 'gap',
					'dependency' => [
						'element' => 'display_type',
						'value' => [ 'grid', 'masonry_grid', 'flex', 'flex_wrap' ],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'List Spacing', 'total-theme-core' ),
					'param_name' => 'list_spacing',
					'css' => [
						'property' => 'gap',
						'selector' => '.wpex-post-cards-list',
					],
					'choices' => 'gap',
					'dependency' => [ 'element' => 'display_type', 'value' => 'list' ],
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
					'dependency' => [ 'element' => 'display_type', 'value' => 'list' ],
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
						'selector' => '.wpex-card-list-divider',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Remove Divider Before First Entry?', 'total-theme-core' ),
					'param_name' => 'list_divider_remove_first',
					'std' => 'true',
					'dependency' => [ 'element' => 'list_divider', 'not_empty' => true ],

					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Remove Divider After Last Entry?', 'total-theme-core' ),
					'param_name' => 'list_divider_remove_last',
					'std' => 'false',
					'dependency' => [ 'element' => 'list_divider', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Alternate Thumbnail Position', 'total-theme-core' ),
					'param_name' => 'alternate_flex_direction',
					'description' => esc_html__( 'Enable to alternate the position of your thumbnail when using certain cards styles. For example if you are using a card style with a left thumbnail every other item will display the thumbnail on the right. When using a custom card template it will reverse the order of your columns every other item.', 'total-theme-core' ),
					'std' => 'false',
					'dependency' => [ 'element' => 'display_type', 'value' => 'list' ],
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
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Sequential Animation', 'total-theme-core' ),
					'param_name' => 'css_animation_sequential',
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
					'heading' => esc_html__( 'Dot Navigation', 'total-theme-core' ),
					'param_name' => 'dots',
					'std' => 'false',
					'dependency' => [ 'element' => 'display_type', 'value' => 'carousel' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Auto Play', 'total-theme-core' ),
					'param_name' => 'auto_play',
					'std' => 'false',
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
				// Query
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Query Type', 'total-theme-core' ),
					'param_name' => 'query_type',
					'admin_label' => true,
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'choices_callback' => [ self::class, 'get_query_type_choices' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Callback', 'total-theme-core' ),
					'param_name' => 'query_callback',
					'choices' => 'callback_functions',
					'description' => sprintf( esc_html__( 'Callback functions must be %swhitelisted%s for security reasons.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', '</a>' ),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'value' => 'callback' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'attach_images',
					'heading' => esc_html__( 'Images', 'total-theme-core' ),
					'param_name' => 'attachments',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'value' => 'attachments' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'std' => 'post',
					'heading' => esc_html__( 'Automatic Query Preview Post Type', 'total-theme-core' ),
					'param_name' => 'auto_query_preview_pt',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Enter a post type name to use as the placeholder for the preview while editing in the WPBakery live editor.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'value' => 'auto' ],
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textarea_safe',
					'heading' => esc_html__( 'Query Parameter String or Callback Function Name', 'total-theme-core' ),
					'param_name' => 'custom_query_args',
					'description' => self::param_description( 'advanced_query' ),
					'value' => 'posts_per_page=12&post_type=post&orderby=date',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'value' => 'custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'posts',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'description' => esc_html__( 'Select a custom field assigned to the current post that returns an array of post ID\'s.', 'total-theme-core' ),
					'param_name' => 'query_custom_field',
					'dependency' => [ 'element' => 'query_type', 'value' => 'custom_field' ],
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'posttypes',
					'heading' => esc_html__( 'Post types', 'total-theme-core' ),
					'param_name' => 'post_types',
					'std' => 'post',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [
						'element' => 'query_type',
						'value' => [ '', 'author', 'related', 'term_card' ]
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Taxonomy', 'total-theme-core' ),
					'param_name' => 'related_taxonomy',
					'choices' => 'taxonomy',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'value' => [ 'related' ] ],
				],
				[
					'type' => 'vcex_text',
					'input_type' => 'number',
					'heading' => esc_html__( 'Posts Per Page', 'total-theme-core' ),
					'param_name' => 'posts_per_page',
					'value' => '12',
					'description' => esc_html__( 'You can enter "-1" to display all posts.', 'total-theme-core' ),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [
						'element' => 'query_type',
						'value_not_equal_to' => [ 'auto', 'custom', 'callback', 'custom_field' ],
					],
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Pagination', 'total-theme-core' ),
					'param_name' => 'pagination',
					'value' => [
						esc_html__( 'Disabled', 'total-theme-core' ) => '',
						esc_html__( 'Numbered', 'total-theme-core' ) => 'numbered',
						esc_html__( 'Numbered (Ajaxed)', 'total-theme-core' ) => 'numbered_ajax',
						esc_html__( 'Load More', 'total-theme-core' ) => 'loadmore',
						esc_html__( 'Infinite Scroll', 'total-theme-core' ) => 'infinite_scroll',
					],
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'By default pagination is disabled unless using an Auto Query in which case it will used the numbered pagination by default.', 'total-theme-core' ),
					'dependency' => [
						'element' => 'query_type',
						'value_not_equal_to' => [ 'custom_field', 'term_card' ]
					],
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'input_type' => 'number',
					'heading' => esc_html__( 'Offset', 'total-theme-core' ),
					'param_name' => 'offset',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Number of post to displace or pass over.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Query Specific Posts', 'total-theme-core' ),
					'param_name' => 'posts_in',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'settings' => [
						'multiple' => true,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'delay' => 0,
						'auto_focus' => true,
						'sortable' => true,
						'display_inline' => false, // Important because true will break sortable.
					],
					'description' => esc_html__( 'Start typing a post name to locate and add it. Make sure you have selected the Post Types above so they match the post types of the selected posts.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'is_empty' => true ],
					'elementor' => [
						'type' => 'text',
						'description' => esc_html__( 'Enter a comma separated list of post ID\'s to include.', 'total-theme-core' ),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Limit By Author', 'total-theme-core' ),
					'param_name' => 'author_in',
					'settings' => [
						'multiple' => true,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					],
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'is_empty' => true ],
					'elementor' => [
						'type' => 'text',
						'description' => esc_html__( 'Enter a comma separated list of author ID\'s to include.', 'total-theme-core' ),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Include Terms', 'total-theme-core' ),
					'param_name' => 'terms_in',
					'settings' => [
						'multiple' => true,
						'min_length' => 1,
						'groups' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					],
					'description' => esc_html__( 'Search for terms (categories, tags or custom taxonomies) you wish to include in the query.', 'total-theme-core' ),
					'elementor' => [
						'type' => 'text',
						'label_block' => true,
						'description' => esc_html__( 'Enter a comma separated list of taxonomy term ids.', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [
						'element' => 'query_type',
						'value_not_equal_to' => [
							'auto',
							'custom',
							'callback',
							'custom_field',
							'post_series',
							'post_gallery',
							'attachments',
							'post_children',
							'related',
							'woo_related',
							'woo_upsells',
							'term_card',
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Exclude Terms', 'total-theme-core' ),
					'param_name' => 'terms_not_in',
					'settings' => [
						'multiple' => true,
						'min_length' => 1,
						'groups' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					],
					'description' => esc_html__( 'Search for terms (categories, tags or custom taxonomies) you wish to exclude from the query.', 'total-theme-core' ),
					'elementor' => [
						'type' => 'text',
						'label_block' => true,
						'description' => esc_html__( 'Enter a comma separated list of taxonomy term ids.', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [
						'element' => 'query_type',
						'value_not_equal_to' => [
							'auto',
							'custom',
							'callback',
							'custom_field',
							'post_series',
							'post_gallery',
							'attachments',
							'post_children',
							'related',
							'woo_related',
							'woo_upsells',
							'term_card',
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'URL Search Parameter', 'total-theme-core' ),
					'param_name' => 'url_search_param',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'You can enter a custom parameter if you want this element to perform searches based on the URL. For instance, if you input "_search" and visit the page with the URL format page.com?_search=hello, it will conduct a search for "hello" within the selected query. Remember, the term "search" is a default parameter that redirects to the native WordPress search archive, so be sure to use a different word.', 'total-theme-core' ),
					'dependency' => [
						'element' => 'query_type',
						'value_not_equal_to' => [
							'auto',
							'post_series',
							'post_gallery',
							'attachments',
							'post_children',
							'related',
							'woo_related',
							'woo_upsells',
							'term_card',
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Meta Query', 'total-theme-core' ),
					'param_name' => 'meta_query',
					'dependency' => [ 'element' => 'query_type', 'value' => [ '', 'related' ] ],
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'std' => 'AND',
					'heading' => esc_html__( 'Meta Query Relation', 'total-theme-core' ),
					'param_name' => 'meta_query_relation',
					'choices' => [
						'AND' => 'AND',
						'OR' => 'OR',
					],
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'meta_query', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'param_group',
					'param_name' => 'meta_query_items',
					'heading' => esc_html__( 'Meta Query Items', 'total-theme-core' ),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'meta_query', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
					'params' => [
						[
							'type' => 'textfield',
							'heading' => esc_html__( 'Key', 'total-theme-core' ),
							'param_name' => 'key',
							'admin_label' => true,
							'editors' => [ 'wpbakery', 'elementor' ],
						],
						[
							'type' => 'vcex_select',
							'heading' => esc_html__( 'Compare', 'total-theme-core' ),
							'param_name' => 'compare',
							'choices' => [
								'=' => '=',
								'!=' => '!=',
								'>' => '>',
								'>=' => '>=',
								'<' => '<',
								'<=' => '<=',
								'LIKE' => 'LIKE',
								'NOT LIKE' => 'NOT LIKE',
								'IN' => 'IN',
								'NOT IN' => 'NOT IN',
								'BETWEEN' => 'BETWEEN',
								'NOT BETWEEN' => 'NOT BETWEEN',
								'EXISTS' => 'EXISTS',
								'NOT EXISTS' => 'NOT EXISTS'
							],
							'editors' => [ 'wpbakery', 'elementor' ],
						],
						[
							'type' => 'vcex_text',
							'heading' => esc_html__( 'Value', 'total-theme-core' ),
							'param_name' => 'value',
							'admin_label' => true,
							'editors' => [ 'wpbakery', 'elementor' ],
							'description' => esc_html__( 'Leave empty when checking for the existence of the field. For "IN", "BETWEEN", or "NOT BETWEEN" comparisons, enter a comma-separated list of values.', 'total-theme-core' ),
						],
						[
							'type' => 'vcex_select',
							'heading' => esc_html__( 'Type', 'total-theme-core' ),
							'param_name' => 'type',
							'choices' => [
								'CHAR' => 'CHAR',
								'NUMERIC' => 'NUMERIC',
								'BINARY' => 'BINARY',
								'DATE' => 'DATE',
								'DATETIME' => 'DATETIME',
								'DECIMAL' => 'DECIMAL',
								'SIGNED' => 'SIGNED',
								'TIME' => 'TIME',
								'UNSIGNED' => 'UNSIGNED',
							],
							'editors' => [ 'wpbakery', 'elementor' ],
						],
					],
				],
				/*
				// @todo
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Allow URL Sorting', 'total-theme-core' ),
					'param_name' => 'url_sort',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => sprintf(
						esc_html__( 'When enabled the Query will check the current URL for _sort_ parameters to modify the query. %sLearn more%s', 'total-theme-core' ),
						'<a href="https://totalwptheme.com/docs/docs/post-cards-url-sort/" target="_blank" rel="noopener noreferrer">',
						'</a>'
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],*/
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Ignore Sticky Posts', 'total-theme-core' ),
					'param_name' => 'ignore_sticky_posts',
					'std' => 'false',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Include sticky posts, but not at the top.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Sticky Posts Only', 'total-theme-core' ),
					'description' => esc_html__( 'Important: Sticky post queries are limited to the latest 50 sticky posts. Because of how WordPress sticky posts work these posts must be queried by their ID and it if you have too many it could crash your site.', 'total-theme-core' ),
					'param_name' => 'show_sticky_posts',
					'std' => 'false',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Exclude Sticky Posts', 'total-theme-core' ),
					'description' => esc_html__( 'Important: Sticky post queries are limited to the latest 50 sticky posts. Because of how WordPress sticky posts work these posts must be queried by their ID and it if you have too many it could crash your site.', 'total-theme-core' ),
					'param_name' => 'exclude_sticky_posts',
					'std' => 'false',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Exclude Current Post', 'total-theme-core' ),
					'param_name' => 'exclude_current_post',
					'std' => 'false',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Post With Thumbnails Only', 'total-theme-core' ),
					'param_name' => 'thumbnail_query',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order', 'total-theme-core' ),
					'param_name' => 'order',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => 'default',
						esc_html__( 'DESC', 'total-theme-core' ) => 'DESC',
						esc_html__( 'ASC', 'total-theme-core' ) => 'ASC',
					],
					'dependency' => [
						'element' => 'query_type',
						'value_not_equal_to' => [
							'auto',
							'custom',
							'callback',
							'post_series',
							'post_gallery',
							'attachments',
							'woo_best_selling',
							'woo_upsells',
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Order By', 'total-theme-core' ),
					'param_name' => 'orderby',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [
						'element' => 'query_type',
						'value_not_equal_to' => [
							'auto',
							'custom',
							'callback',
							'post_series',
							'post_gallery',
							'attachments',
							'just_events_all',
							'just_events_upcoming',
							'just_events_past',
							'just_events_today',
							'just_events_ongoing',
							'woo_best_selling',
							'woo_upsells',
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Orderby: Meta Key', 'total-theme-core' ),
					'param_name' => 'orderby_meta_key',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'orderby', 'value' => [ 'meta_value_num', 'meta_value' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textarea',
					'heading' => esc_html__( 'No Posts Found Message', 'total-theme-core' ),
					'param_name' => 'no_posts_found_message',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Leave empty to disable.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Pagination
				[
					'type' => 'vcex_notice',
					'param_name' => 'pagination_notice',
					'text' => esc_html__( 'Note: The pagination can be globally and further modified via the Customizer.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'pagination', 'value' => [ 'numbered', 'numbered_ajax', 'loadmore', 'infinite_scroll' ] ],
					'group' => esc_html__( 'Pagination', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Top Margin', 'total-theme-core' ),
					'param_name' => 'pagination_top_margin',
					'placeholder' => '30px',
					'description' => esc_html__( 'Allowed units:', 'total-theme-core' ) . ' px, em, rem, vw, vmin, vmax.<br>' . esc_html__( 'Allowed CSS functions:', 'total-theme-core' ) . ' calc(), clamp().',
					'group' => esc_html__( 'Pagination', 'total-theme-core' ),
					'css' => [
						'property' => 'margin-block-start',
						'selector' => '.wpex-post-cards-pagination',
					],
					'dependency' => [ 'element' => 'pagination', 'value' => [ 'numbered', 'numbered_ajax', 'loadmore', 'infinite_scroll' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Alignment', 'total-theme-core' ),
					'param_name' => 'pagination_align',
					'group' => esc_html__( 'Pagination', 'total-theme-core' ),
					'dependency' => [ 'element' => 'pagination', 'value' => [ 'numbered', 'numbered_ajax' ] ],
					'css' => [
						'property' => 'text-align',
						'selector' => '.wpex-pagination',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Load More Button Text', 'total-theme-core' ),
					'param_name' => 'loadmore_text',
					'group' => esc_html__( 'Pagination', 'total-theme-core' ),
					'dependency' => [ 'element' => 'pagination', 'value' => 'loadmore' ],
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
					'css' => [ 'selector' => '.wpex-post-cards-heading', 'property' => 'margin-block-end' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'dependency' => [ 'element' => 'heading', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Max Width', 'total-theme-core' ),
					'param_name' => 'heading_max_width',
					'css' => [ 'selector' => '.wpex-post-cards-heading', 'property' => 'max-width' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'dependency' => [ 'element' => 'heading', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Text Color', 'total-theme-core' ),
					'param_name' => 'heading_color',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.wpex-post-cards-heading', 'property' => 'color' ],
					'dependency' => [ 'element' => 'heading', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'heading_border_color',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.wpex-post-cards-heading', 'property' => '--theme-heading-border-color' ],
					'dependency' => [ 'element' => 'heading_style', 'value' => [ 'border-side', 'border-bottom', 'border-w-color' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_width',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'heading_border_width',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.wpex-post-cards-heading', 'property' => '--theme-heading-border-width' ],
					'dependency' => [ 'element' => 'heading_style', 'value' => [ 'border-side', 'border-bottom', 'border-w-color' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_style',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'heading_border_style',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.wpex-post-cards-heading', 'property' => '--theme-heading-border-style' ],
					'dependency' => [ 'element' => 'heading_style', 'value' => [ 'border-side', 'border-bottom', 'border-w-color' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'heading_font_size',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.wpex-post-cards-heading', 'property' => 'font-size' ],
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
				// Entry
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Title Font Size', 'total-theme-core' ),
					'param_name' => 'title_font_size',
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'css' => [
						'property' => 'font-size',
						'selector' => '.wpex-post-cards-loop .wpex-card-title',
					],
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Title Tag', 'total-theme-core' ),
					'param_name' => 'title_tag',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						'h2' => 'h2',
						'h3' => 'h3',
						'h4' => 'h4',
						'h5' => 'h5',
						'h6' => 'h6',
						'div' => 'div',
					],
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Media Width', 'total-theme-core' ),
					'param_name' => 'media_width',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						'20%'  => '20',
						'25%'  => '25',
						'30%'  => '30',
						'33%'  => '33',
						'40%'  => '40',
						'50%'  => '50',
						'60%'  => '60',
						'70%'  => '70',
						'80%'  => '80',
					],
					'description' => esc_html__( 'Applies to card styles that have the media (image/video) displayed to the side.', 'total-theme-core' ),
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'aspect_ratio',
					'heading' => esc_html__( 'Media Aspect Ratio', 'total-theme-core' ),
					'description' => esc_html__( 'Allows you to apply the same size to all images without having to crop them.', 'total-theme-core' ),
					'param_name' => 'media_aspect_ratio',
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'css' => [
						'property' => 'aspect-ratio',
						'selector' => '.wpex-post-cards-loop :is(.wpex-card-media,.wpex-card-thumbnail) :is(img,iframe,video)',
					],
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'object_fit',
					'heading' => esc_html__( 'Media Object Fit', 'total-theme-core' ),
					'description' => esc_html__( 'Select how your image should be resized to fit its aspect ratio.', 'total-theme-core' ),
					'param_name' => 'media_object_fit',
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'css' => [
						'property' => 'object-fit',
						'selector' => '.wpex-post-cards-loop :is(.wpex-card-media,.wpex-card-thumbnail) :is(img,iframe,video)',
					],
					'dependency' => [
						'element' => 'media_aspect_ratio',
						'not_empty' => true,
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Media Max Width', 'total-theme-core' ),
					'param_name' => 'media_max_width',
					'description' => esc_html__( 'Allows you to set a max-width for the media element. For example if you select 60% above for the media width but want to make sure the image is never larger than 200px wide you can enter 200px here.', 'total-theme-core' ),
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'css' => [
						'property' => '--wpex-card-media-max-width',
						'selector' => '.wpex-post-cards-loop',
					],
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Breakpoint', 'total-theme-core' ),
					'param_name' => 'media_breakpoint',
					'choices' => self::get_media_breakpoint_choices(),
					'description' => esc_html__( 'The breakpoint at which a left/right card styles swaps to a column view. The default for most cards is "md".', 'total-theme-core' ),
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Thumbnail Size', 'total-theme-core' ),
					'param_name' => 'thumbnail_size',
					'std' => 'full',
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'description' => esc_html__( 'Note: For security reasons custom cropping only works on images hosted on your own server in the WordPress uploads folder. If you are using an external image it will display in full.', 'total-theme-core' ),
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Thumbnail Crop Location', 'total-theme-core' ),
					'param_name' => 'thumbnail_crop',
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'dependency' => [ 'element' => 'thumbnail_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Thumbnail Crop Width', 'total-theme-core' ),
					'param_name' => 'thumbnail_width',
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'dependency' => [ 'element' => 'thumbnail_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Thumbnail Crop Height', 'total-theme-core' ),
					'param_name' => 'thumbnail_height',
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'dependency' => [ 'element' => 'thumbnail_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Excerpt Length', 'total-theme-core' ),
					'param_name' => 'excerpt_length',
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'description' => esc_html__( 'Enter how many words to display for the excerpt. To display the full post content enter "-1". To display the full post content up to the "more" tag enter "9999".', 'total-theme-core' ),
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Date Format', 'total-theme-core' ),
					'param_name' => 'date_format',
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'description' => sprintf(
						esc_html__( 'Change the default card date format using one of the %sallowed format strings%s', 'total-theme-core' ),
						'<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank" rel="noopener noreferrer">',
						'</a>'
					),
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom More Link Text', 'total-theme-core' ),
					'param_name' => 'more_link_text',
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'description' => esc_html__( 'You can enter "0" to disable.', 'total-theme-core' ),
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'card_el_class',
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'description' => esc_html__( 'Extra class name to apply to the ".wpex-card" element.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Media
				[
					'type' => 'checkbox',
					'heading' => esc_html__( 'Allowed Media Types', 'total-theme-core' ),
					'param_name' => 'allowed_media',
					'std' => 'thumbnail',
					'value' => [
						esc_html__( 'Thumbnail', 'js_composer' ) => 'thumbnail',
						esc_html__( 'Video', 'js_composer' ) => 'video',
					//	esc_html__( 'Audio', 'js_composer' ) => 'audio',
					//	esc_html__( 'Gallery', 'js_composer' ) => 'gallery',
					],
					'description' => esc_html__( 'Note: Not all card styles support all media types.', 'total-theme-core' ),
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'group' => esc_html__( 'Media', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'overlay_style',
					'heading' => esc_html__( 'Thumbnail Overlay', 'total-theme-core' ),
					'param_name' => 'thumbnail_overlay_style',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => array_merge( self::custom_card_styles(), self::overlay_card_styles() ),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Button Text', 'total-theme-core' ),
					'param_name' => 'thumbnail_overlay_button_text',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => [ 'element' => 'thumbnail_overlay_style', 'value' => 'hover-button' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'image_hover',
					'heading' => esc_html__( 'Image Hover', 'total-theme-core' ),
					'param_name' => 'thumbnail_hover',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Image Hover Speed', 'total-theme-core' ),
					'param_name' => 'thumbnail_hover_speed',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'description' => esc_html__( 'Enter a custom value for the image hover "transition-duration" property. Example: 500ms.', 'total-theme-core' ),
					'choices' => 'transition_duration',
					'css' => [ 'property' => '--wpex-image-hover-speed' ],
					'dependency' => [ 'element' => 'thumbnail_hover', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'image_filter',
					'heading' => esc_html__( 'Image Filter', 'total-theme-core' ),
					'param_name' => 'thumbnail_filter',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'media_el_class',
					'description' => esc_html__( 'Extra class name to apply to the ".wpex-card-thumbnail" element.', 'total-theme-core' ),
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Link
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Link Type', 'total-theme-core' ),
					'param_name' => 'link_type',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'Go to Post', 'total-theme-core' ) => 'post',
						esc_html__( 'Lightbox', 'total-theme-core' ) => 'lightbox',
						esc_html__( 'Modal Dialog (Browser Modal)', 'total-theme-core' ) => 'dialog',
						esc_html__( 'Modal Popup (Lightbox Script)', 'total-theme-core' ) => 'modal',
						esc_html__( 'None', 'total-theme-core' ) => 'none',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => [ 'woocommerce' ],
					],
					'description' => esc_html__( 'By default, all cards link to the associated post unless specified otherwise when creating custom cards. To set a different URL, you can use the Card Settings metabox to specify an alternative link on a per-post basis. Alternatively, you can assign a post-specific URL by adding a custom field named "wpex_card_url".', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Modal Title', 'total-theme-core' ),
					'param_name' => 'modal_title',
					'std' => 'true',
					'dependency' => [ 'element' => 'link_type', 'value' => [ 'dialog', 'modal' ] ],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'template',
					'heading' => esc_html__( 'Custom Modal Template', 'total-theme-core' ),
					'param_name' => 'modal_template',
					'dependency' => [ 'element' => 'link_type', 'value' => [ 'dialog', 'modal' ] ],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Link Target', 'total-theme-core' ),
					'param_name' => 'link_target',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'value' => [
						esc_html__( 'Same Tab', 'total-theme-core' ) => '',
						esc_html__( 'New Tab', 'total-theme-core' ) => '_blank',
					],
					'dependency' => [
						'element' => 'link_type',
						'value_not_equal_to' => [ 'dialog', 'modal', 'none' ]
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Link Rel', 'total-theme-core' ),
					'param_name' => 'link_rel',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => [
						'element' => 'link_type',
						'value_not_equal_to' => [ 'dialog', 'modal', 'none' ]
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Featured
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Featured Card', 'total-theme-core' ),
					'param_name' => 'featured_card',
					'std' => 'false',
					'description' => esc_html__( 'Enable to display the first entry as a "featured" card with it\'s own unique style above the other entries.', 'total-theme-core' ),
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'card_style',
					'heading' => esc_html__( 'Featured Card Style', 'total-theme-core' ),
					'param_name' => 'featured_style',
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Featured Post', 'total-theme-core' ),
					'param_name' => 'featured_post_id',
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'settings' => [
						'multiple' => false,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => false,
						'delay' => 0,
						'auto_focus' => true,
					],
					'elementor' => [
						'type' => 'text',
						'description' => esc_html__( 'Enter your Featured Post ID.', 'total-theme-core' ),
					],
					'dependency' => [
						'element' => 'query_type',
						'value_not_equal_to' => [ 'post_gallery', 'attachments' ]
					],
					'description' => esc_html__( 'Start typing a post name to locate and select it. Leave empty to display the first post as the featured post.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Display on Paginated Pages', 'total-theme-core' ),
					'param_name' => 'featured_show_on_paged',
					'std' => 'true',
					'description' => esc_html__( 'If disabled your posts per page count does not need to include the featured post.', 'total-theme-core' ),
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Placement', 'total-theme-core' ),
					'param_name' => 'featured_location',
					'value' => [
						esc_html__( 'Top', 'total-theme-core' ) => 'top',
						esc_html__( 'Left', 'total-theme-core' ) => 'left',
						esc_html__( 'Right', 'total-theme-core' ) => 'right',
					],
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Fill Container', 'total-theme-core' ),
					'param_name' => 'aside_flex',
					'std' => 'false',
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'dependency' => [ 'element' => 'featured_location', 'value' => [ 'left', 'right' ] ],
					'description' => esc_html__( 'When enabled the cards displayed next to the featured card will stretch vertically to fill up any empty space if the featured card is larger and vice versa. Important: This setting works best with the "Modern CSS" grid style.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'featured_width',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						'70%' => '70%',
						'67%' => '67%',
						'60%' => '60%',
						'50%' => '50%',
					],
					'dependency' => [ 'element' => 'featured_location', 'value' => [ 'left', 'right' ] ],
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'breakpoint',
					'heading' => esc_html__( 'Breakpoint', 'total-theme-core' ),
					'param_name' => 'featured_breakpoint',
					'dependency' => [ 'element' => 'featured_location', 'value' => [ 'left', 'right' ] ],
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'featured_margin',
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Divider Style', 'total-theme-core' ),
					'param_name' => 'featured_divider',
					'value' => [
						esc_html__( 'None', 'total-theme-core' ) => '',
						esc_html__( 'Solid', 'total-theme-core' ) => 'solid',
						esc_html__( 'Dashed', 'total-theme-core' ) => 'dashed',
						esc_html__( 'Dotted', 'total-theme-core' ) => 'dotted',
					],
					'dependency' => [ 'element' => 'featured_location', 'value' => 'top' ],
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_width',
					'heading' => esc_html__( 'Divider Size', 'total-theme-core' ),
					'param_name' => 'featured_divider_size',
					'dependency' => [ 'element' => 'featured_divider', 'not_empty' => true ],
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Divider Margin', 'total-theme-core' ),
					'param_name' => 'featured_divider_margin',
					'dependency' => [ 'element' => 'featured_divider', 'not_empty' => true ],
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'List Divider Color', 'total-theme-core' ),
					'param_name' => 'featured_divider_color',
					'css' => [
						'property' => 'border-color',
						'selector' => '.wpex-post-cards-featured-card-divider',
					],
					'dependency' => [ 'element' => 'featured_divider', 'not_empty' => true ],
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Title Font Size', 'total-theme-core' ),
					'param_name' => 'featured_title_font_size',
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'css' => [
						'property' => 'font-size',
						'selector' => '.wpex-card-featured .wpex-card-title',
					],
					'dependency' => [
						'element' => 'featured_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Title Tag', 'total-theme-core' ),
					'param_name' => 'featured_title_tag',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						'h2' => 'h2',
						'h3' => 'h3',
						'h4' => 'h4',
						'h5' => 'h5',
						'h6' => 'h6',
						'div' => 'div',
					],
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'dependency' => [
						'element' => 'featured_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Media Width', 'total-theme-core' ),
					'param_name' => 'featured_media_width',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						'20%'  => '20',
						'25%'  => '25',
						'30%'  => '30',
						'33%'  => '33',
						'40%'  => '40',
						'50%'  => '50',
						'60%'  => '60',
						'70%'  => '70',
						'80%'  => '80',
					],
					'description' => esc_html__( 'Applies to card styles that have the media (image/video) displayed to the side.', 'total-theme-core' ),
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'dependency' => [
						'element' => 'featured_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'aspect_ratio',
					'heading' => esc_html__( 'Media Aspect Ratio', 'total-theme-core' ),
					'param_name' => 'featured_media_aspect_ratio',
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'css' => [
						'property' => 'aspect-ratio',
						'selector' => '.wpex-post-cards-featured :is(.wpex-card-media,.wpex-card-thumbnail) :is(img,iframe,video)',
					],
					'dependency' => [
						'element' => 'card_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'object_fit',
					'heading' => esc_html__( 'Media Object Fit', 'total-theme-core' ),
					'param_name' => 'featured_media_object_fit',
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'css' => [
						'property' => 'object-fit',
						'selector' => '.wpex-post-cards-featured :is(.wpex-card-media,.wpex-card-thumbnail) :is(img,iframe,video)',
					],
					'dependency' => [
						'element' => 'featured_media_aspect_ratio',
						'not_empty' => true,
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Media Max Width', 'total-theme-core' ),
					'param_name' => 'featured_media_max_width',
					'description' => esc_html__( 'Allows you to set a max-width for the media element. For example if you select 60% above for the media width but want to make sure the image is never larger than 200px wide you can enter 200px here.', 'total-theme-core' ),
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'css' => [
						'property' => '--wpex-card-media-max-width',
						'selector' => '.wpex-post-cards-featured',
					],
					'dependency' => [
						'element' => 'featured_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Breakpoint', 'total-theme-core' ),
					'param_name' => 'featured_media_breakpoint',
					'choices' => self::get_media_breakpoint_choices(),
					'description' => esc_html__( 'The breakpoint at which a left/right card styles swaps to a column view. The default for most cards is "md".', 'total-theme-core' ),
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'dependency' => [
						'element' => 'featured_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Thumbnail Size', 'total-theme-core' ),
					'param_name' => 'featured_thumbnail_size',
					'std' => 'full',
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'description' => esc_html__( 'Note: For security reasons custom cropping only works on images hosted on your own server in the WordPress uploads folder. If you are using an external image it will display in full.', 'total-theme-core' ),
					'dependency' => [
						'element' => 'featured_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Thumbnail Crop Location', 'total-theme-core' ),
					'param_name' => 'featured_thumbnail_crop',
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'dependency' => [ 'element' => 'featured_thumbnail_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Thumbnail Crop Width', 'total-theme-core' ),
					'param_name' => 'featured_thumbnail_width',
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'dependency' => [ 'element' => 'featured_thumbnail_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Thumbnail Crop Height', 'total-theme-core' ),
					'param_name' => 'featured_thumbnail_height',
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'dependency' => [ 'element' => 'featured_thumbnail_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Excerpt Length', 'total-theme-core' ),
					'param_name' => 'featured_excerpt_length',
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'description' => esc_html__( 'Enter how many words to display for the excerpt. To display the full post content enter "-1". To display the full post content up to the "more" tag enter "9999".', 'total-theme-core' ),
					'dependency' => [
						'element' => 'featured_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom More Link Text', 'total-theme-core' ),
					'param_name' => 'featured_more_link_text',
					'description' => esc_html__( 'You can enter "0" to disable.', 'total-theme-core' ),
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'dependency' => [
						'element' => 'featured_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'featured_el_class',
					'description' => esc_html__( 'Extra class name to apply to the ".wpex-card" element.', 'total-theme-core' ),
					'group' => esc_html__( 'Featured', 'total-theme-core' ),
					'dependency' => [
						'element' => 'featured_style',
						'value_not_equal_to' => self::custom_card_styles(),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Deprecated fields
				[ 'type' => 'hidden', 'param_name' => 'auto_query' ],
				[ 'type' => 'hidden', 'param_name' => 'custom_query' ],
				[ 'type' => 'hidden', 'param_name' => 'tax_query' ],
				[ 'type' => 'hidden', 'param_name' => 'tax_query_taxonomy' ],
				[ 'type' => 'hidden', 'param_name' => 'tax_query_terms' ], // moved to include_terms
				[ 'type' => 'hidden', 'param_name' => 'pagination_loadmore' ],
				// AJAX fields
				[ 'type' => 'hidden', 'param_name' => 'post_id' ],
				[ 'type' => 'hidden', 'param_name' => 'query_vars' ],
				[ 'type' => 'hidden', 'param_name' => 'ajax_filter' ],
				[ 'type' => 'hidden', 'param_name' => 'ajax_action' ],
				[ 'type' => 'hidden', 'param_name' => 'ignore_tax_query' ],
			];
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}

			// Switch to new pagination type.
			if ( isset( $atts['pagination_loadmore'] ) && vcex_validate_att_boolean( 'pagination_loadmore', $atts ) ) {
				$atts['pagination'] = 'loadmore';
				unset( $atts['pagination_loadmore'] );
			} elseif ( isset( $atts['pagination'] ) ) {
				switch ( $atts['pagination'] ) {
					case 'true':
						$atts['pagination'] = 'numbered';
						break;
					case 'false':
						$atts['pagination'] = '';
						break;
				}
			}

			// Switch to new query type.
			if ( empty( $atts['query_type'] ) ) {
				if ( vcex_validate_att_boolean( 'auto_query', $atts ) ) {
					$atts['query_type'] = 'auto';
					unset( $atts['auto_query'] );
				} elseif ( vcex_validate_att_boolean( 'custom_query', $atts ) ) {
					$atts['query_type'] = 'custom';
					unset( $atts['custom_query'] );
				}
			}

			/**
			 * Switch from tax_query_terms to terms_in
			 *
			 * @since 5.6
			 */
			if ( empty( $atts['terms_in'] )
				&& vcex_validate_att_boolean( 'tax_query', $atts )
				&& ! empty( $atts['tax_query_taxonomy'] )
				&& ! empty( $atts['tax_query_terms'] )
				&& is_string( $atts['tax_query_terms'] )
			) {
				$terms_in = [];
				$items = preg_split( '/\,[\s]*/', $atts['tax_query_terms'] );
				if ( is_array( $items ) ) {
					foreach ( $items as $item ) {
						if ( strlen( $item ) > 0 ) {
							if ( is_numeric( $item ) ) {
								$terms_in[] = $item;
							} else {
								if ( 'post-format-standard' === $item ) {
									if ( ! empty( $atts['terms_not_in'] ) ) {
										continue;
									}
									$all_formats = [
										'post-format-aside',
										'post-format-gallery',
										'post-format-link',
										'post-format-image',
										'post-format-quote',
										'post-format-status',
										'post-format-audio',
										'post-format-chat',
										'post-format-video',
									];
									$terms_not_in = [];
									foreach ( $all_formats as $format ) {
										$term_obj = get_term_by( 'slug', $format, 'post_format' );
										if ( $term_obj && ! is_wp_error( $term_obj ) ) {
											$terms_not_in[] = $term_obj->term_taxonomy_id ?? $term_obj->term_id;
										}
									}
									$atts['terms_not_in'] = implode( ',', $terms_not_in );
								} else {
									$term_obj = get_term_by( 'slug', $item, $atts['tax_query_taxonomy'] );
									if ( $term_obj && ! is_wp_error( $term_obj ) ) {
										$terms_in[] = $term_obj->term_taxonomy_id ?? $term_obj->term_id;
									}
								}
							}
						}
					}
					if ( $terms_in ) {
						$atts['terms_in'] = implode( ', ', $terms_in ); // must return string.
						unset( $atts['tax_query'] );
						unset( $atts['tax_query_taxonomy'] );
						unset( $atts['tax_query_terms'] );
					}
				}
			}

			return $atts;
		}

		/**
		 * Returns array of custom card styles to use with dependencies
		 * so we can hide various settings if a custom card is selected.
		 */
		protected static function custom_card_styles(): array {
			$styles   = array_keys( (array) totalthemecore_call_static( 'Cards\Builder', 'get_custom_cards' ) );
			$styles[] = 'woocommerce';
			return $styles;
		}

		/**
		 * Returns array of overlay card styles.
		 */
		protected static function overlay_card_styles(): array {
			return [
				'overlay_1',
				'overlay_2',
				'overlay_3',
				'overlay_4',
				'overlay_5',
				'overlay_6',
				'overlay_7',
				'overlay_8',
				'overlay_9',
				'overlay_10',
				'overlay_11',
				'overlay_12',
				'overlay_13',
				'overlay_14',
			];
		}

		/**
		 * Returns array of breakpoint choices for the media element.
		 */
		public static function get_media_breakpoint_choices( $add_false = true ) {
			$choices = [];
			if ( function_exists( 'wpex_utl_breakpoints' ) ) {
				$choices = wpex_utl_breakpoints();
			}
			if ( $choices && $add_false ) {
				$choices['false'] = esc_html__( 'Do not stack', 'total-theme-core' );
			}
			return $choices;
		}

		/**
		 * Returns query types.
		 */
		public static function get_query_type_choices(): array {
			$choices = [
				''              => esc_html__( 'Default', 'total-theme-core' ),
				'auto'          => esc_html__( 'Auto (dynamic templates)', 'total-theme-core' ),
				'custom'        => esc_html__( 'Advanced (custom arguments)', 'total-theme-core' ),
				'callback'      => esc_html__( 'Callback (child theme function)', 'total-theme-core' ),
				'post_gallery'  => esc_html__( 'Post Gallery', 'total-theme-core' ),
				'attachments'   => esc_html__( 'Custom Gallery', 'total-theme-core' ),
				'custom_field'  => esc_html__( 'Custom Field', 'total-theme-core' ),
				'post_children' => esc_html__( 'Child Pages', 'total-theme-core' ),
				'related'       => esc_html__( 'Related by Taxonomy', 'total-theme-core' ),
				'term_card'     => esc_html__( 'Term Card', 'total-theme-core' ),
			];

			/*if ( taxonomy_exists( 'post_series' ) ) {
				$choices['post_series'] = esc_html__( 'Post Series', 'total-theme-core' );
			}*/

			if ( get_theme_mod( 'staff_enable', true ) && post_type_exists( 'staff' ) ) {
				$choices['author'] = esc_html__( 'Posts by Author or Current Staff Member', 'total-theme-core' );
			}

			// 3rd party integrations.
			if ( class_exists( 'Just_Events\Plugin', false ) ) {
				$choices['just_events_all']      = esc_html__( 'All Events', 'total-theme-core' );
				$choices['just_events_upcoming'] = esc_html__( 'Upcoming Events', 'total-theme-core' );
				$choices['just_events_today']    = esc_html__( 'Today\'s Events', 'total-theme-core' );
				$choices['just_events_ongoing']  = esc_html__( 'Ongoing Events', 'total-theme-core' );
				$choices['just_events_past']     = esc_html__( 'Past Events', 'total-theme-core' );
			}

			if ( class_exists( 'WooCommerce', false ) ) {
				$choices['woo_featured']     = esc_html__( 'WooCommerce - Featured', 'total-theme-core' );
				$choices['woo_onsale']       = esc_html__( 'WooCommerce - On Sale', 'total-theme-core' );
				$choices['woo_best_selling'] = esc_html__( 'WooCommerce - Best Selling', 'total-theme-core' );
				$choices['woo_related']      = esc_html__( 'WooCommerce - Related', 'total-theme-core' );
				$choices['woo_upsells']      = esc_html__( 'WooCommerce - Upsells', 'total-theme-core' );
			}

			return (array) apply_filters( 'wpex_post_cards_query_type_choices', $choices );
		}

		/**
		 * Register autocomplete hooks.
		 */
		public static function register_vc_autocomplete_hooks(): void {
			// Terms.
			add_filter(
				'vc_autocomplete_wpex_post_cards_terms_in_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms_Ids::callback'
			);
			add_filter(
				'vc_autocomplete_wpex_post_cards_terms_in_render',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms_Ids::render'
			);
			add_filter(
				'vc_autocomplete_wpex_post_cards_terms_not_in_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms_Ids::callback'
			);
			add_filter(
				'vc_autocomplete_wpex_post_cards_terms_not_in_render',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms_Ids::render'
			);
			// Author query.
			add_filter(
				'vc_autocomplete_wpex_post_cards_author_in_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Users::callback'
			);
			add_filter(
				'vc_autocomplete_wpex_post_cards_author_in_render',
				'TotalThemeCore\WPBakery\Autocomplete\Users::render'
			);
			// Posts In query.
			add_filter(
				'vc_autocomplete_wpex_post_cards_posts_in_callback',
				'vc_include_field_search'
			);
			add_filter(
				'vc_autocomplete_wpex_post_cards_posts_in_render',
				'vc_include_field_render'
			);
			// Featured Card ID.
			add_filter(
				'vc_autocomplete_wpex_post_cards_featured_post_id_callback',
				'vc_include_field_search'
			);
			add_filter(
				'vc_autocomplete_wpex_post_cards_featured_post_id_render',
				'vc_include_field_render'
			);
		}

	}

}

new Wpex_Post_Cards_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_wpex_post_cards' ) ) {
	class WPBakeryShortCode_wpex_post_cards extends WPBakeryShortCode {}
}
