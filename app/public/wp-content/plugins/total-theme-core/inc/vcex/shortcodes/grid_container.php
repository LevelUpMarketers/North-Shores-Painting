<?php

defined( 'ABSPATH' ) || exit;

/**
 * Grid Container Shortcode.
 */
if ( ! class_exists( 'Vcex_Grid_Container_Shortcode' ) ) {

	class Vcex_Grid_Container_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_grid_container';

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
			return esc_html__( 'Grid Container', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Place certain elements in a grid', 'total-theme-core' );
		}

		/**
		 * Returns custom vc map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			$allowed_elements = \apply_filters(
				'vcex_grid_container_allowed_elements',
				'vcex_icon_box,vcex_milestone,vcex_bullets,vcex_list_item,vcex_teaser,vc_column_text,vcex_image,vcex_image_swap,vcex_pricing,vcex_custom_field,vcex_image_banner,vcex_icon,vcex_feature_box,vcex_post_next_prev,vcex_shortcode,vc_raw_html,vcex_callout'
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
			$column_options = [];

			$alt_col_options = [
				esc_html__( 'Inherit', 'total-theme-core' ) => '',
			];

			$columns_count = 12;

			for ( $i = 1; $i <= 12; $i++ ) {
				$column_options[ $i ] = $i;
				$alt_col_options[ $i ] = $i;
			}

			$params = [
				[
					'type' => 'vcex_notice',
					'param_name' => 'editor_notice',
					'text' => esc_html__( 'Because of how the frontend editor works, there could be some design inconsistencies when using this element, so it\'s best used via the backend.', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Align Items', 'total-theme-core' ),
					'param_name' => 'align_items',
					'description' => esc_html__( 'Set the align-items CSS property.', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Justify Items', 'total-theme-core' ),
					'param_name' => 'justify_items',
					'description' => esc_html__( 'Set the justify-items CSS property.', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Gap', 'total-theme-core' ),
					'param_name' => 'gap',
					'description' => esc_html__( 'Spacing between elements. Default is 20px.', 'total-theme-core' ),
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Columns', 'total-theme-core' ),
					'param_name' => 'columns',
					'value' => $column_options,
					'std' => '1',
					'description' => esc_html__( 'This element uses a mobile-first design aproach, so the number of columns you select will be used on all devices. To display more columns or less columns on larger devices you can use the settings below. If you wish to stack your elements on devices smaller than 640px, select 1 for this option then use the settings below to select the columns you want displayed for larger screens.', 'total-theme-core' ),
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Columns xl', 'total-theme-core' ),
					'param_name' => 'columns_xl',
					'std' => '',
					'edit_field_class' => 'vc_col-sm-3 vc_column',
					'value' => $alt_col_options,
					'description' => esc_html__( 'For screens 1280px and greater.', 'total-theme-core' ),
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Columns lg', 'total-theme-core' ),
					'param_name' => 'columns_lg',
					'std' => '3',
					'edit_field_class' => 'vc_col-sm-3 vc_column',
					'value' => $alt_col_options,
					'description' => esc_html__( 'For screens 1024px and greater.', 'total-theme-core' ),
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Columns md', 'total-theme-core' ),
					'param_name' => 'columns_md',
					'std' => '',
					'edit_field_class' => 'vc_col-sm-3 vc_column',
					'value' => $alt_col_options,
					'description' => esc_html__( 'For screens 768px and greater.', 'total-theme-core' ),
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Columns sm', 'total-theme-core' ),
					'param_name' => 'columns_sm',
					'std' => '',
					'edit_field_class' => 'vc_col-sm-3 vc_column',
					'value' => $alt_col_options,
					'description' => esc_html__( 'For screens 640px and greater.', 'total-theme-core' ),
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
				// CSS.
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
			];

			return $params;
		}

	}

}

new Vcex_Grid_Container_Shortcode;

if ( class_exists( 'WPBakeryShortCodesContainer' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Grid_Container' ) ) {
	class WPBakeryShortCode_Vcex_Grid_Container extends WPBakeryShortCodesContainer {}
}
