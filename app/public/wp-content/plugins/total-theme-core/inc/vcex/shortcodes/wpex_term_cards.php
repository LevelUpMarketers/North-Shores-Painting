<?php

defined( 'ABSPATH' ) || exit;

/**
 * Post Cards Shortcode.
 */
if ( ! class_exists( 'Wpex_Term_Cards_Shortcode' ) ) {

	class Wpex_Term_Cards_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'wpex_term_cards';

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
			return esc_html__( 'Term Cards', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Category, tag or term based card list, grid or carousel', 'total-theme-core' );
		}

		/**
		 * Shortcode custom output.
		 */
		public static function output( $atts, $content = null, $shortcode_tag = '' ): ?string {
			if ( ! is_array( $atts ) || ! class_exists( 'TotalThemeCore\Vcex\Term_Cards' ) ) {
				return null; // @note this element can't render without settings.
			}
			return (new TotalThemeCore\Vcex\Term_Cards( $atts ))->get_output();
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
						esc_html__( 'Default', 'total-theme-core' ) => 'css_grid',
						esc_html__( 'Masonry', 'total-theme-core' ) => 'masonry',
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
						'selector' => '.wpex-term-cards-list',
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
					'heading' => esc_html__( 'Taxonomy', 'total-theme-core' ),
					'param_name' => 'taxonomy',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'value' => [ '', 'post_terms', 'primary_term' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Hide Empty Terms?', 'total-theme-core' ),
					'param_name' => 'hide_empty',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Parent Terms Only', 'total-theme-core' ),
					'param_name' => 'parent_terms',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'value' => [ 'custom', 'post_terms' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Child Of', 'total-theme-core' ),
					'param_name' => 'child_of',
					'settings' => [
						'multiple' => true,
						'min_length' => 1,
						'groups' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					],
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'elementor' => [
						'type' => 'text',
						'description' => esc_html__( 'Enter a parent term ID.', 'total-theme-core' ),
					],
					'dependency' => [ 'element' => 'query_type', 'value_not_equal_to' => 'primary_term' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Include Terms', 'total-theme-core' ),
					'param_name' => 'include_terms',
					'settings' => [
						'multiple' => true,
						'min_length' => 1,
						'groups' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					],
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'elementor' => [
						'type' => 'text',
						'description' => esc_html__( 'Enter a comma separated list of term ID\'s.', 'total-theme-core' ),
					],
					'dependency' => [ 'element' => 'query_type', 'value_not_equal_to' => 'primary_term' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Exclude Terms', 'total-theme-core' ),
					'param_name' => 'exclude_terms',
					'settings' => [
						'multiple' => true,
						'min_length' => 1,
						'groups' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					],
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'elementor' => [
						'type' => 'text',
						'description' => esc_html__( 'Enter a comma separated list of term ID\'s.', 'total-theme-core' ),
					],
					'dependency' => [ 'element' => 'query_type', 'value_not_equal_to' => 'primary_term' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order', 'total-theme-core' ),
					'param_name' => 'order',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'value' => [
						esc_html__( 'ASC', 'total-theme-core' ) => 'ASC',
						esc_html__( 'DESC', 'total-theme-core' ) => 'DESC',
					],
					'dependency' => [ 'element' => 'query_type', 'value_not_equal_to' => 'primary_term' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order By', 'total-theme-core' ),
					'param_name' => 'orderby',
					'std' => 'name',
					'value' => [
						esc_html__( 'Name', 'total-theme-core' ) => 'name',
						esc_html__( 'Slug', 'total-theme-core' ) => 'slug',
						esc_html__( 'Term Group', 'total-theme-core' ) => 'term_group',
						esc_html__( 'Term ID', 'total-theme-core' ) => 'term_id',
						'ID' => 'id',
						esc_html__( 'Description', 'total-theme-core' ) => 'description',
						esc_html__( 'Count', 'total-theme-core' ) => 'count',
					],
					'dependency' => [ 'element' => 'query_type', 'value_not_equal_to' => 'primary_term' ],
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textarea',
					'heading' => esc_html__( 'No Terms Found Message', 'total-theme-core' ),
					'param_name' => 'no_terms_found_message',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Leave empty to disable.', 'total-theme-core' ),
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
					'css' => [ 'selector' => '.wpex-term-cards-heading', 'property' => 'margin-block-end' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'dependency' => [ 'element' => 'heading', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Max Width', 'total-theme-core' ),
					'param_name' => 'heading_max_width',
					'css' => [ 'selector' => '.wpex-term-cards-heading', 'property' => 'max-width' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'dependency' => [ 'element' => 'heading', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Text Color', 'total-theme-core' ),
					'param_name' => 'heading_color',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.wpex-term-cards-heading', 'property' => 'color' ],
					'dependency' => [ 'element' => 'heading', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'heading_border_color',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.wpex-term-cards-heading', 'property' => '--theme-heading-border-color' ],
					'dependency' => [ 'element' => 'heading_style', 'value' => [ 'border-side', 'border-bottom', 'border-w-color' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_width',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'heading_border_width',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.wpex-term-cards-heading', 'property' => '--theme-heading-border-width' ],
					'dependency' => [ 'element' => 'heading_style', 'value' => [ 'border-side', 'border-bottom', 'border-w-color' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_style',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'heading_border_style',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.wpex-term-cards-heading', 'property' => '--theme-heading-border-style' ],
					'dependency' => [ 'element' => 'heading_style', 'value' => [ 'border-side', 'border-bottom', 'border-w-color' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'heading_font_size',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'css' => [ 'selector' => '.wpex-term-cards-heading', 'property' => 'font-size' ],
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
						'selector' => '.wpex-term-cards-loop .wpex-card-title',
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
						'selector' => '.wpex-term-cards-loop :is(.wpex-card-media,.wpex-card-thumbnail) :is(img,iframe,video)',
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
						'selector' => '.wpex-term-cards-loop :is(.wpex-card-media,.wpex-card-thumbnail) :is(img,iframe,video)',
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
						'selector' => '.wpex-term-cards-loop',
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
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'card_el_class',
					'group' => esc_html__( 'Entry', 'total-theme-core' ),
					'description' => esc_html__( 'Extra class name to apply to the ".wpex-card" element.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Media
				[
					'type' => 'vcex_select',
					'choices' => 'overlay_style',
					'exclude_choices' =>  [
						'category-tag',
						'category-tag-primary',
						'category-tag-two',
						'category-tag-two-primary',
						'thumb-swap',
						'thumb-swap-title',
						'video-icon',
						'video-icon_2',
						'video-icon_3',
						'video-icon_4',
						'title-price-hover',
						'title-date-hover',
						'title-date-visible',
						'post-author',
						'post-author-hover',
					],
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
						esc_html__( 'Go to archive', 'total-theme-core' ) => 'term',
						esc_html__( 'Lightbox', 'total-theme-core' ) => 'lightbox',
						esc_html__( 'Modal Dialog (Browser Modal)', 'total-theme-core' ) => 'dialog',
						esc_html__( 'Modal Popup (Lightbox Script)', 'total-theme-core' ) => 'modal',
						esc_html__( 'None', 'total-theme-core' ) => 'none',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
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
			];
		}

		/**
		 * Returns array of custom card styles to use with dependencies.
		 */
		protected static function custom_card_styles(): array {
			return array_keys( (array) totalthemecore_call_static( 'Cards\Builder', 'get_custom_cards' ) );
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
				''             => esc_html__( 'Default', 'total-theme-core' ),
				'post_terms'   => esc_html__( 'Current Post Terms', 'total-theme-core' ),
				'primary_term' => esc_html__( 'Current Post Primary Term', 'total-theme-core' ),
				'tax_children' => esc_html__( 'Current Taxonomy Child Terms', 'total-theme-core' ),
				'tax_parent'   => esc_html__( 'Current Taxonomy Direct Child Terms', 'total-theme-core' ),
			];
			return (array) apply_filters( 'wpex_term_cards_query_type_choices', $choices );
		}

		/**
		 * Register autocomplete hooks.
		 */
		public static function register_vc_autocomplete_hooks(): void {
			\add_filter(
				'vc_autocomplete_wpex_term_cards_include_terms_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms_Ids::callback'
			);
			\add_filter(
				'vc_autocomplete_wpex_term_cards_include_terms_render',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms_Ids::render'
			);
			\add_filter(
				'vc_autocomplete_wpex_term_cards_exclude_terms_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms_Ids::callback'
			);
			\add_filter(
				'vc_autocomplete_wpex_term_cards_exclude_terms_render',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms_Ids::render'
			);
			\add_filter(
				'vc_autocomplete_wpex_term_cards_child_of_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms_Ids::callback'
			);
			\add_filter(
				'vc_autocomplete_wpex_term_cards_child_of_render',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms_Ids::render'
			);
		}

	}

}

new Wpex_Term_Cards_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_wpex_term_cards' ) ) {
	class WPBakeryShortCode_wpex_term_cards extends WPBakeryShortCode {}
}
