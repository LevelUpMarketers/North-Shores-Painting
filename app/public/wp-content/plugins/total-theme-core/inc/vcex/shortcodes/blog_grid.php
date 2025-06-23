<?php

defined( 'ABSPATH' ) || exit;

/**
 * Blog Grid Shortcode.
 *
 * @package TotalThemeCore
 */
if ( ! class_exists( 'Vcex_Blog_Grid_Shortcode' ) ) {

	class Vcex_Blog_Grid_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		const TAG = 'vcex_blog_grid';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Shortcode title.
		 */
		public static function get_title() {
			return esc_html__( 'Blog Grid', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Recent blog posts grid', 'total-theme-core' );
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
					'description' => self::param_description( 'text' ),
					'admin_label' => true,
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Header Style', 'total-theme-core' ),
					'param_name' => 'header_style',
					'description' => self::param_description( 'header_style' ),
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Grid Style', 'total-theme-core' ),
					'param_name' => 'grid_style',
					'edit_field_class' => 'vc_col-sm-3 vc_column clear',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => 'default',
						esc_html__( 'Fit Columns', 'total-theme-core' ) => 'fit_columns',
						esc_html__( 'Masonry', 'total-theme-core' ) => 'masonry',
					],
					'admin_label' => true,
				],
				[
					'type' => 'vcex_grid_columns',
					'heading' => esc_html__( 'Columns', 'total-theme-core' ),
					'param_name' => 'columns',
					'std' => '4',
					'edit_field_class' => 'vc_col-sm-3 vc_column',
					'admin_label' => true,
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
						esc_html__( 'No', 'total-theme-core' ) => 'false',
					],
					'edit_field_class' => 'vc_col-sm-3 vc_column',
					'dependency' => [ 'element' => 'columns', 'value' => [ '2', '3', '4', '5', '6', '7', '8', '9', '10' ] ],
				],
				[
					'type' => 'vcex_grid_columns_responsive',
					'heading' => esc_html__( 'Responsive Column Settings', 'total-theme-core' ),
					'param_name' => 'columns_responsive_settings',
					'dependency' => [ 'element' => 'columns_responsive', 'value' => 'true' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( '1 Column Style', 'total-theme-core' ),
					'param_name' => 'single_column_style',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'Left Image And Right Content', 'total-theme-core' ) => 'left_thumbs',
					],
					'dependency' => [ 'element' => 'columns', 'value' => '1' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Link Target', 'total-theme-core' ),
					'param_name' => 'url_target',
					'std' => 'self',
					'choices' => 'link_target',
					'description' => esc_html__( 'This will apply to the image, title and readmore button.', 'total-theme-core' ),
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
					'param_name' => 'classes',
					'description' => self::param_description( 'el_class' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
				],
				vcex_vc_map_add_css_animation(),
				// Query
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Advanced Query', 'total-theme-core' ),
					'param_name' => 'custom_query',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to build a custom query using your own parameters.', 'total-theme-core' ),
				],
				[
					'type' => 'textarea_safe',
					'heading' => esc_html__( 'Query Parameter String or Callback Function Name', 'total-theme-core' ),
					'param_name' => 'custom_query_args',
					'description' => self::param_description( 'advanced_query' ),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_query', 'value' => 'true' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Posts Per Page', 'total-theme-core' ),
					'param_name' => 'posts_per_page',
					'value' => '4',
					'description' => esc_html__( 'You can enter "-1" to display all posts.', 'total-theme-core' ),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_query', 'value' => 'false' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Pagination', 'total-theme-core' ),
					'param_name' => 'pagination',
					'std' => 'false',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_query', 'value' => 'false' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Load More Button', 'total-theme-core' ),
					'param_name' => 'pagination_loadmore',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Offset', 'total-theme-core' ),
					'param_name' => 'offset',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Number of post to displace or pass over.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_query', 'value' => 'false' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Post With Thumbnails Only', 'total-theme-core' ),
					'param_name' => 'thumbnail_query',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_query', 'value' => 'false' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Ignore Sticky Posts', 'total-theme-core' ),
					'param_name' => 'ignore_sticky_posts',
					'std' => 'false',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_query', 'value' => 'false' ],
				],
				[
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Include Categories', 'total-theme-core' ),
					'param_name' => 'include_categories',
					'param_holder_class' => 'vc_not-for-custom',
					'admin_label' => true,
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
					'dependency' => [ 'element' => 'custom_query', 'value' => 'false' ],
				],
				[
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Exclude Categories', 'total-theme-core' ),
					'param_name' => 'exclude_categories',
					'param_holder_class' => 'vc_not-for-custom',
					'admin_label' => true,
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
					'dependency' => [ 'element' => 'custom_query', 'value' => 'false' ],
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
					'dependency' => [ 'element' => 'custom_query', 'value' => 'false' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order', 'total-theme-core' ),
					'param_name' => 'order',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'DESC', 'total-theme-core' ) => 'DESC',
						esc_html__( 'ASC', 'total-theme-core' ) => 'ASC',
					],
					'dependency' => [ 'element' => 'custom_query', 'value' => 'false' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Order By', 'total-theme-core' ),
					'param_name' => 'orderby',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_query', 'value' => 'false' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Orderby: Meta Key', 'total-theme-core' ),
					'param_name' => 'orderby_meta_key',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'orderby', 'value' => [ 'meta_value_num', 'meta_value' ] ],
				],
				// Style
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'shadow',
					'heading' => esc_html__( 'Entry Shadow', 'total-theme-core' ),
					'param_name' => 'entry_shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Content Style', 'total-theme-core' ),
					'description' => esc_html__( 'Does not apply to the 1 Column Left Thumbnail Style Grid.', 'total-theme-core' ),
					'param_name' => 'content_style',
					'std' => 'bordered',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Content Alignment', 'total-theme-core' ),
					'param_name' => 'content_alignment',
					'std' => '',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Equal Content Height', 'total-theme-core' ),
					'param_name' => 'equal_heights_grid',
					'std' => 'false',
					'dependency' => [ 'element' => 'columns', 'value_not_equal_to' => '1' ],
					'description' => esc_html__( 'Enable so the content area for each entry is the same height.', 'total-theme-core' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'padding',
					'heading' => esc_html__( 'Content Padding', 'total-theme-core' ),
					'param_name' => 'content_padding_all',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Content Background', 'total-theme-core' ),
					'param_name' => 'content_background_color',
					'css' => [ 'selector' => '.entry-details', 'property' => 'background-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_style',
					'heading' => esc_html__( 'Content Border Style', 'total-theme-core' ),
					'param_name' => 'content_border_style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_width',
					'heading' => esc_html__( 'Content Border Width', 'total-theme-core' ),
					'param_name' => 'content_border_width',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'content_border_color',
					'css' => [ 'selector' => '.entry-details', 'property' => 'border-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Content Opacity', 'total-theme-core' ),
					'param_name' => 'content_opacity',
					'css' => [ 'selector' => '.entry-details', 'property' => 'opacity' ],
					'choices' => 'opacity',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				// Filter
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Category Filter', 'total-theme-core' ),
					'param_name' => 'filter',
					'std' => 'false',
					'description' => esc_html__( 'Enables a category filter to show and hide posts based on their categories. This does not load posts via AJAX, but rather filters items currently on the page.', 'total-theme-core' ),
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_query', 'value' => 'false' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Display All Link?', 'total-theme-core' ),
					'param_name' => 'filter_all_link',
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => [ 'element' => 'filter', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'no',
					'heading' => esc_html__( 'Center Filter Links', 'total-theme-core' ),
					'param_name' => 'center_filter',
					'dependency' => [ 'element' => 'filter', 'value' => 'true' ],
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'vcex' => [ 'on' => 'yes', 'off' => 'no' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'breakpoint',
					'heading' => esc_html__( 'Switch to Select Dropdown at Breakpoint', 'total-theme-core' ),
					'param_name' => 'filter_select_bk',
					'description' => esc_html__( 'By default the filter will display buttons for all devices. Choose a custom breakpoint if you wish to display a select dropdown for smaller screens.', 'total-theme-core' ),
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => [ 'element' => 'filter', 'value' => 'true' ],
				],
				[
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Default Active Category', 'total-theme-core' ),
					'param_name' => 'filter_active_category',
					'param_holder_class' => 'vc_not-for-custom',
					'admin_label' => true,
					'settings' => [
						'multiple' => false,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					],
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => [ 'element' => 'filter', 'value' => 'true' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Filter "All" Text', 'total-theme-core' ),
					'param_name' => 'all_text',
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => [ 'element' => 'filter_all_link', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_button_styles',
					'heading' => esc_html__( 'Button Style', 'total-theme-core' ),
					'param_name' => 'filter_button_style',
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'std' => 'minimal-border',
					'dependency' => [ 'element' => 'filter', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_button_colors',
					'heading' => esc_html__( 'Button Color', 'total-theme-core' ),
					'param_name' => 'filter_button_color',
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => [ 'element' => 'filter', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Filter Layout', 'total-theme-core' ),
					'param_name' => 'masonry_layout_mode',
					'std' => 'masonry',
					'choices' => 'masonry_layout_mode',
					'dependency' => [ 'element' => 'filter', 'value' => 'true' ],
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Filter Speed', 'total-theme-core' ),
					'param_name' => 'filter_speed',
					'description' => esc_html__( 'Default is "0.4" seconds. Enter "0.0" to disable.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'filter', 'value' => 'true' ],
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'filter_font_size',
					'css' => [ 'selector' => '.vcex-filter-links', 'property' => 'font-size' ],
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => [ 'element' => 'filter', 'value' => 'true' ],
				],
				// Media
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'entry_media',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Display Featured Videos?', 'total-theme-core' ),
					'param_name' => 'featured_video',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => [ 'element' => 'entry_media', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Image Links To', 'total-theme-core' ),
					'param_name' => 'thumb_link',
					'std' => 'post',
					'choices' => [
						'post' => esc_html__( 'Post', 'total-theme-core' ),
						'lightbox' => esc_html__( 'Lightbox', 'total-theme-core' ),
						'nowhere' => esc_html__( 'Nowhere', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => [ 'element' => 'entry_media', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'std' => 'wpex_custom',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => [ 'element' => 'entry_media', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_crop',
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'group' => esc_html__( 'Media', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'img_width',
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'description' => esc_html__( 'Enter a width in pixels.', 'total-theme-core' ),
					'group' => esc_html__( 'Media', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'param_name' => 'img_height',
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'group' => esc_html__( 'Media', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_radius',
					'supports_blobs' => true,
					'heading' => esc_html__( 'Image Border Radius', 'total-theme-core' ),
					'param_name' => 'img_border_radius',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => [ 'element' => 'entry_media', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Overlay', 'total-theme-core' ),
					'param_name' => 'overlay_style',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => [ 'element' => 'entry_media', 'value' => 'true' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Button Text', 'total-theme-core' ),
					'param_name' => 'overlay_button_text',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => [ 'element' => 'overlay_style', 'value' => 'hover-button' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Excerpt Length', 'total-theme-core' ),
					'param_name' => 'overlay_excerpt_length',
					'value' => '15',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => [ 'element' => 'overlay_style', 'value' => 'title-excerpt-hover' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Hover', 'total-theme-core' ),
					'param_name' => 'img_hover_style',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => [ 'element' => 'entry_media', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Filter', 'total-theme-core' ),
					'param_name' => 'img_filter',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => [ 'element' => 'entry_media', 'value' => 'true' ],
				],
				// Title
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Title', 'total-theme-core' ),
					'param_name' => 'title',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'HTML Tag', 'total-theme-core' ),
					'param_name' => 'title_tag',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'std' => 'h2',
					'choices' => 'html_tag',
					'dependency' => [ 'element' => 'title', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'content_heading_color',
					'css' => [ 'selector' => '.entry-title', 'property' => 'color' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => [ 'element' => 'title', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'content_heading_size',
					'css' => [ 'selector' => '.entry-title', 'property' => 'font-size' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => [ 'element' => 'title', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'content_heading_line_height',
					'choices' => 'line_height',
					'css' => [ 'selector' => '.entry-title', 'property' => 'line-height' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => [ 'element' => 'title', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'content_heading_margin',
					'css' => [ 'selector' => '.entry-title', 'property' => 'margin' ],
					'description' => self::param_description( 'margin' ),
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => [ 'element' => 'title', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'content_heading_weight',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-title', 'property' => 'font-weight' ],
					'dependency' => [ 'element' => 'title', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'content_heading_transform',
					'choices' => 'text_transform',
					'css' => [ 'selector' => '.entry-title', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => [ 'element' => 'title', 'value' => 'true' ],
				],
				// Date
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'date',
					'group' => esc_html__( 'Date', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'date_color',
					'css' => [ 'selector' => '.entry-date', 'property' => 'color' ],
					'group' => esc_html__( 'Date', 'total-theme-core' ),
					'dependency' => [ 'element' => 'date', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'date_font_size',
					'css' => [ 'selector' => '.entry-date', 'property' => 'font-size' ],
					'group' => esc_html__( 'Date', 'total-theme-core' ),
					'dependency' => [ 'element' => 'date', 'value' => 'true' ],
				],
				// Excerpt
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'excerpt',
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Length', 'total-theme-core' ),
					'param_name' => 'excerpt_length',
					'std' => '15',
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'description' => esc_html__( 'Enter how many words to display for the excerpt. To display the full post content enter "-1". To display the full post content up to the "more" tag enter "9999".', 'total-theme-core' ),
					'dependency' => [ 'element' => 'excerpt', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'content_font_size',
					'css' => [ 'selector' => '.entry-excerpt', 'property' => 'font-size' ],
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'dependency' => [ 'element' => 'excerpt', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'content_color',
					'css' => [ 'selector' => '.entry-excerpt', 'property' => 'color' ],
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'dependency' => [ 'element' => 'excerpt', 'value' => 'true' ],
				],
				// Readmore
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'read_more',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Text', 'total-theme-core' ),
					'param_name' => 'read_more_text',
					'description' => self::param_description( 'text' ),
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_button_styles',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'readmore_style',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_button_colors',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'readmore_style_color',
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'color' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Arrow', 'total-theme-core' ),
					'param_name' => 'readmore_rarr',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'readmore_size',
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'font-size' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'readmore_border_radius',
					'choices' => 'border_radius',
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'readmore_padding',
					'description' => self::param_description( 'padding' ),
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'padding' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'readmore_margin',
					'description' => self::param_description( 'margin' ),
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'margin' ],
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'readmore_background',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'background-color' ],
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'readmore_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'color' ],
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'readmore_hover_background',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-readmore:hover', 'property' => 'background-color' ],
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'readmore_hover_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-readmore:hover', 'property' => 'color' ],
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'Outer CSS Box', 'total-theme-core' ),
					'param_name' => 'entry_css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'Inner CSS box', 'total-theme-core' ),
					'param_name' => 'content_css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				// AJAX fields
				[ 'type' => 'hidden', 'param_name' => 'entry_count' ],
				[ 'type' => 'hidden', 'param_name' => 'paged' ],
				// Deprecated fields
				[ 'type' => 'hidden', 'param_name' => 'term_slug' ],
				[ 'type' => 'hidden', 'param_name' => 'content_background' ],
				[ 'type' => 'hidden', 'param_name' => 'content_border' ],
				[ 'type' => 'hidden', 'param_name' => 'content_margin' ],
				[ 'type' => 'hidden', 'param_name' => 'content_padding' ],
			];
		}

		/**
		 * Parse WPBakery attributes on edit.
		 */
		public static function vc_edit_form_fields_attributes( $atts = [] ) {
			$atts = vcex_parse_deprecated_grid_entry_content_css( $atts );
			return self::parse_deprecated_attributes( $atts );
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( ! empty( $atts['term_slug'] ) && empty( $atts['include_categories']) ) {
				$atts['include_categories'] = $atts['term_slug'];
				unset( $atts['term_slug'] );
			}
			return $atts;
		}

		/**
		 * Register autocomplete hooks.
		 */
		public static function register_vc_autocomplete_hooks(): void {
			// Get autocomplete suggestion.
			\add_filter(
				'vc_autocomplete_vcex_blog_grid_include_categories_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Categories::callback'
			);
			\add_filter(
				'vc_autocomplete_vcex_blog_grid_exclude_categories_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Categories::callback'
			);
			\add_filter(
				'vc_autocomplete_vcex_blog_grid_filter_active_category_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Categories::callback'
			);
			\add_filter(
				'vc_autocomplete_vcex_blog_grid_author_in_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Users::callback'
			);
			// Render autocomplete suggestions.
			\add_filter(
				'vc_autocomplete_vcex_blog_grid_include_categories_render',
				'TotalThemeCore\WPBakery\Autocomplete\Categories::render'
			);
			\add_filter(
				'vc_autocomplete_vcex_blog_grid_exclude_categories_render',
				'TotalThemeCore\WPBakery\\Autocomplete\\Categories::render'
			);
			\add_filter(
				'vc_autocomplete_vcex_blog_grid_filter_active_category_render',
				'TotalThemeCore\WPBakery\Autocomplete\Categories::render'
			);
			\add_filter(
				'vc_autocomplete_vcex_blog_grid_author_in_render',
				'TotalThemeCore\WPBakery\Autocomplete\Users::render'
			);
		}

	}

}

new Vcex_Blog_Grid_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Blog_Grid' ) ) {
	class WPBakeryShortCode_Vcex_Blog_Grid extends WPBakeryShortCode {}
}
