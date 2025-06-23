<?php

defined( 'ABSPATH' ) || exit;

/**
 * Flex Container Shortcode.
 */

if ( ! class_exists( 'Vcex_Flex_Container_Shortcode' ) ) {

	class Vcex_Flex_Container_Shortcode extends \TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_flex_container';

		/**
		 * Main constructor.
		 */
		public function __construct() {

			// Call parent class constructor.
			parent::__construct();
		}

		/**
		 * Shortcode title.
		 */
		public static function get_title(): string {
			return esc_html__( 'Flexible Container', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Place certain elements in a flexible container', 'total-theme-core' );
		}

		/**
		 * Returns custom vc map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			$allowed_elements = \apply_filters(
				'vcex_flex_container_allowed_elements',
				'vcex_heading,vcex_icon_box,vcex_milestone,vcex_bullets,vcex_button,vcex_list_item,vcex_teaser,vc_column_text,vcex_image,vcex_pricing,vcex_custom_field,vcex_navbar,vcex_post_terms,vcex_post_meta,vcex_page_title,vcex_image_banner,vcex_social_links,vcex_newsletter_form,vcex_icon,vcex_social_share,vcex_author_bio,vcex_feature_box,vcex_shortcode,vcex_tribe_event_data,vcex_star_rating,vcex_post_excerpt,vcex_just_events_date,vcex_just_events_time,vcex_horizontal_menu,vcex_off_canvas_menu,vcex_searchbar,vcex_dark_mode_toggle,vc_raw_html,vcex_woocommerce_template,vcex_post_next_prev'
			);
			return [
				'allowed_container_element' => false,
				'is_container'				=> true,
				'content_element'			=> true,
				'js_view'					=> 'VcColumnView',
				'as_parent'					=> [ 'only' => $allowed_elements ],
			];
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_notice',
					'param_name' => 'editor_notice',
					'text' => esc_html__( 'Because of how the frontend editor works, there could be some design inconsistencies when using this element, so it\'s best used via the backend.', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_buttons',
					'std' => 'row',
					'heading' => esc_html__( 'Direction', 'total-theme-core' ),
					'param_name' => 'flex_direction',
					'choices' => [
						'row' => esc_html__( 'Horizontal (Row)', 'total-theme-core' ),
						'column' => esc_html__( 'Vertical', 'total-theme-core' ),
					],
					'description' => sprintf( esc_html__( 'If you are not familiar with the flex model you can learn more via the %sFirefox manual%s.', 'total-theme-core' ), '<a href="https://developer.mozilla.org/en-US/docs/Learn/CSS/CSS_layout/Flexbox" target="_blank" rel="noopener noreferrer">', '</a>' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Align Items', 'total-theme-core' ),
					'param_name' => 'align_items',
					'description' => esc_html__( 'Set the align-items CSS property.', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Justify Content', 'total-theme-core' ),
					'param_name' => 'justify_content',
					'description' => esc_html__( 'Set the justify-content CSS property.', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'breakpoint',
					'heading' => esc_html__( 'Stack Elements Breakpoint', 'total-theme-core' ),
					'param_name' => 'row_stack_bp',
					'dependency' => [ 'element' => 'flex_direction', 'value' => 'row' ],
					'description' => esc_html__( 'Select a breakpoint if you wish to stack your elements vertically at a certain point. Note: Flex Basis, Align Items and Justify Content values are ignored at the stacking point to prevent issues as the flex direction will change from row to column.', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Stack Reverse order', 'total-theme-core' ),
					'param_name' => 'row_stack_reverse',
					'std' => 'false',
					'dependency' => [ 'element' => 'row_stack_bp', 'not_empty' => true ],
					'description' => esc_html__( 'Reverse the order of elements when they stack.', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Gap', 'total-theme-core' ),
					'param_name' => 'gap',
					'description' => esc_html__( 'Spacing between elements. Default is 20px.', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Stack Gap', 'total-theme-core' ),
					'param_name' => 'row_stack_gap',
					'dependency' => [ 'element' => 'row_stack_bp', 'not_empty' => true ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Element Width (Flex Basis)', 'total-theme-core' ),
					'param_name' => 'flex_basis',
					'description' => esc_html__( 'Set the initial width for the inner elements. Enter a single value to target all elements or a coma separated string to target each elements individually. Make sure to keep the Gap in consideration, for example if you enter 50% for the flex-basis each item will have an initial width of 50% but the default gap is 20px so there will not be enough room for both elements.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'flex_direction', 'value' => 'row' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Flex Grow', 'total-theme-core' ),
					'param_name' => 'flex_grow',
					'description' => esc_html__( 'When enabled it will set the inner items flex-grow property to 1 so that they will stretch to fill up empty space. Note: If you have set a custom flex basis for your items only items with an "auto" value will be allowed to grow.', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Flex Wrap', 'total-theme-core' ),
					'param_name' => 'flex_wrap',
					'description' => esc_html__( 'Automatically wrap elements so they can take up as much space as needed.', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
				],
				// Design
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'choices' => 'margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'width',
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'center',
					'dependency' => array( 'element' => 'width', 'not_empty' => true ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Minimum Height', 'total-theme-core' ),
					'param_name' => 'min_height',
					'description' => self::param_description( 'height' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
			];
		}

	}

}

new Vcex_Flex_Container_Shortcode;

if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Flex_Container' ) ) {
	class WPBakeryShortCode_Vcex_Flex_Container extends WPBakeryShortCodesContainer {}
}
