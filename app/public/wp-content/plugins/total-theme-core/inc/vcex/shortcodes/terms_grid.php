<?php

defined( 'ABSPATH' ) || exit;

/**
 * Terms Grid Shortcode.
 */
if ( ! class_exists( 'VCEX_Terms_Grid_Shortcode' ) ) {

	class VCEX_Terms_Grid_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_terms_grid';

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
			return esc_html__( 'Terms Grid', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Grid of categories, tags or other terms', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				// General
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Header', 'total-theme-core' ),
					'param_name' => 'header',
					'admin_label' => true,
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Header Style', 'total-theme-core' ),
					'param_name' => 'header_style',
					'description' => self::param_description( 'header_style' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Grid Style', 'total-theme-core' ),
					'param_name' => 'grid_style',
					'value' => [
						esc_html__( 'Fit Columns', 'total-theme-core' ) => 'fit_columns',
						esc_html__( 'Masonry', 'total-theme-core' ) => 'masonry',
					],
					'edit_field_class' => 'vc_col-sm-3 vc_column clear',
				],
				[
					'type' => 'vcex_grid_columns',
					'heading' => esc_html__( 'Columns', 'total-theme-core' ),
					'param_name' => 'columns',
					'std' => '3',
					'edit_field_class' => 'vc_col-sm-3 vc_column',
				],
				[
					'type' => 'vcex_select',
					'choices' => 'grid_gap',
					'heading' => esc_html__( 'Gap', 'total-theme-core' ),
					'param_name' => 'columns_gap',
					'edit_field_class' => 'vc_col-sm-3 vc_column',
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Responsive', 'total-theme-core' ),
					'param_name' => 'columns_responsive',
					'value' => [
						esc_html__( 'Yes', 'total-theme-core' ) => 'true',
						esc_html__( 'No', 'total-theme-core' ) => 'false'
					],
					'edit_field_class' => 'vc_col-sm-3 vc_column',
					'dependency' => [
						'element' => 'columns',
						'value' => [ '2', '3', '4', '5', '6', '7', '8', '9', '10' ]
					],
				],
				[
					'type' => 'vcex_grid_columns_responsive',
					'heading' => esc_html__( 'Responsive Column Settings', 'total-theme-core' ),
					'param_name' => 'columns_responsive_settings',
					'dependency' => [ 'element' => 'columns_responsive', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Link to Archive?', 'total-theme-core' ),
					'param_name' => 'archive_link',
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'classes',
					'admin_label' => true,
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core' ),
					'param_name' => 'animation_duration',
					'css' => [
						'selector' => '.vcex-terms-grid-entry',
						'property' => 'animation-duration',
					],
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
				],
				// Query
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Query Type', 'total-theme-core' ),
					'param_name' => 'query_type',
					'admin_label' => true,
					'value' => [
						esc_html__( 'Custom', 'total-theme-core' ) => 'custom',
						esc_html__( 'Current Post Terms', 'total-theme-core' ) => 'post_terms',
						esc_html__( 'Current Taxonomy Child Terms', 'total-theme-core' ) => 'tax_children',
						esc_html__( 'Current Taxonomy Direct Child Terms', 'total-theme-core' ) => 'tax_parent',
					],
					'group' => esc_html__( 'Query', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Taxonomy', 'total-theme-core' ),
					'param_name' => 'taxonomy',
					'std' => 'category',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'value' => [ 'custom', 'post_terms' ] ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Hide Empty Terms?', 'total-theme-core' ),
					'param_name' => 'hide_empty',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Parent Terms Only', 'total-theme-core' ),
					'param_name' => 'parent_terms',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'query_type', 'value' => [ 'custom', 'post_terms' ] ],
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
					'group' => esc_html__( 'Query', 'total-theme-core' ),
				],
				// Image
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'img',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'aspect_ratio',
					'heading' => esc_html__( 'Image Aspect Ratio', 'total-theme-core' ),
					'param_name' => 'img_aspect_ratio',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_crop',
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'img_width',
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'description' => esc_html__( 'Enter a width in pixels.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'param_name' => 'img_height',
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Overlay', 'total-theme-core' ),
					'param_name' => 'overlay_style',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'title_overlay', 'value' => 'false' ],
					'exclude_choices' => [
						'thumb-swap',
						'thumb-swap-title',
						'category-tag',
						'category-tag-two',
						'title-category-hover',
						'title-category-visible',
						'title-date-hover',
						'title-date-visible',
						'categories-title-bottom-visible'
					],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Excerpt Length', 'total-theme-core' ),
					'param_name' => 'overlay_excerpt_length',
					'value' => '15',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'overlay_style', 'value' => 'title-excerpt-hover' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Button Text', 'total-theme-core' ),
					'param_name' => 'overlay_button_text',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'overlay_style', 'value' => 'hover-button' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Hover', 'total-theme-core' ),
					'param_name' => 'img_hover_style',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Filter', 'total-theme-core' ),
					'param_name' => 'img_filter',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
				],
				// Title
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'title',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Overlay Title', 'total-theme-core' ),
					'param_name' => 'title_overlay',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Term Count', 'total-theme-core' ),
					'param_name' => 'term_count',
					'std' => 'false',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Term Count on New Line', 'total-theme-core' ),
					'param_name' => 'term_count_block',
					'std' => 'true',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => [ 'element' => 'title_overlay', 'value' => 'true' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Vertical Align', 'total-theme-core' ),
					'param_name' => 'title_overlay_align_items',
					'dependency' => [ 'element' => 'title_overlay', 'value' => 'true' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'Top', 'total-theme-core' ) => 'start',
						esc_html__( 'Center', 'total-theme-core' ) => 'center',
						esc_html__( 'Bottom', 'total-theme-core' ) => 'end',
					],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Overlay Background', 'total-theme-core' ),
					'param_name' => 'title_overlay_bg',
					'dependency' => [ 'element' => 'title_overlay', 'value' => 'true' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'opacity',
					'heading' => esc_html__( 'Overlay Background Opacity', 'total-theme-core' ),
					'param_name' => 'title_overlay_opacity',
					'dependency' => [ 'element' => 'title_overlay', 'value' => 'true' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'std' => '', // for some reason this is needed in wpbakery 6.6.0 +
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'title_color',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-title a',
						'property' => 'color',
					],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'title_font_family',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-title',
						'property' => 'font-family',
					],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'title_font_weight',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-title',
						'property' => 'font-weight',
					],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'HTML Tag', 'total-theme-core' ),
					'param_name' => 'title_tag',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'choices' => 'html_tag',
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'title_text_align',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-title',
						'property' => 'text-align',
					],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'title_font_size',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-title',
						'property' => 'font-size',
					],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'title_line_height',
					'choices' => 'line_height',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-title',
						'property' => 'line-height',
					],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type'  => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'title_bottom_margin',
					'choices' => 'margin',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-title',
						'property' => 'margin-block-end',
					],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				// Description
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'description',
					'group' => esc_html__( 'Description', 'total-theme-core' ),
					'dependency' => [ 'element' => 'title_overlay', 'value' => 'false' ],
				],
				[
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'description_font_family',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-excerpt',
						'property' => 'font-family',
					],
					'group' => esc_html__( 'Description', 'total-theme-core' ),
					'dependency' => [ 'element' => 'description', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'description_text_align',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-excerpt',
						'property' => 'text-align',
					],
					'group' => esc_html__( 'Description', 'total-theme-core' ),
					'dependency' => [ 'element' => 'description', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'description_font_size',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-excerpt',
						'property' => 'font-size',
					],
					'group' => esc_html__( 'Description', 'total-theme-core' ),
					'dependency' => [ 'element' => 'description', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'description_line_height',
					'choices' => 'line_height',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-excerpt',
						'property' => 'line-height',
					],
					'group' => esc_html__( 'Description', 'total-theme-core' ),
					'dependency' => [ 'element' => 'description', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'description_color',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-excerpt',
						'property' => 'color',
					],
					'group' => esc_html__( 'Description', 'total-theme-core' ),
					'dependency' => [ 'element' => 'description', 'value' => 'true' ],
				],
				// Readmore Button
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'button',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'title_overlay', 'value' => 'false' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Alignment', 'total-theme-core' ),
					'param_name' => 'button_align',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Text', 'total-theme-core' ),
					'param_name' => 'button_text',
					'description' => self::param_description( 'text' ),
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_button_styles',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'button_style',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_button_colors',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'button_style_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'button_size',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-button a',
						'property' => 'font-size',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'button_border_radius',
					'choices' => 'border_radius',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-button a',
						'property' => 'border-radius',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'button_padding',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-button a',
						'property' => 'padding',
					],
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'button_margin',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-button a',
						'property' => 'margin',
					],
					'description' => self::param_description( 'margin' ),
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'button_background',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-button a',
						'property' => 'background',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'button_color',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-button a',
						'property' => 'color',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'button_hover_background',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-button a:hover',
						'property' => 'background',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'button_hover_color',
					'css' => [
						'selector' => '.vcex-terms-grid-entry-button a:hover',
						'property' => 'color',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'Entry CSS', 'total-theme-core' ),
					'param_name' => 'entry_css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				// Deprecated (add to array to ensure these attributes are removed from the shortcode).
				[ 'param_name' => 'title_typo', 'type' => 'hidden' ],
				[ 'param_name' => 'description_typo', 'type' => 'hidden' ],
				[ 'param_name' => 'get_post_terms', 'type' => 'hidden' ],
			];
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}

			if ( ! empty( $atts['title_typo'] ) ) {
				$atts = vcex_migrate_font_container_param( 'title_typo', 'title', $atts );
				unset( $atts['title_typo'] );
			}

			if ( ! empty( $atts['description_typo'] ) ) {
				$atts = vcex_migrate_font_container_param( 'description_typo', 'description', $atts );
				unset( $atts['description_typo'] );
			}

			if ( ! empty( $atts['get_post_terms'] ) ) {
				if ( 'true' == $atts['get_post_terms'] ) {
					$atts['query_type'] = 'post_terms';
				}
				$atts['get_post_terms'] = '';
			}

			return $atts;
		}

		/**
		 * Register autocomplete hooks.
		 */
		public static function register_vc_autocomplete_hooks(): void {
			\add_filter(
				'vc_autocomplete_vcex_terms_grid_exclude_terms_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms::callback'
			);
			\add_filter(
				'vc_autocomplete_vcex_terms_grid_exclude_terms_render',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms::render'
			);
			\add_filter(
				'vc_autocomplete_vcex_terms_grid_child_of_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms::callback'
			);
			\add_filter(
				'vc_autocomplete_vcex_terms_grid_child_of_render',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms::render'
			);
		}

	}

}

new VCEX_Terms_Grid_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Terms_Grid' ) ) {
	class WPBakeryShortCode_Vcex_Terms_Grid extends WPBakeryShortCode {}
}
