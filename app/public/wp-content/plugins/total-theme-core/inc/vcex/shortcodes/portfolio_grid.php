<?php

defined( 'ABSPATH' ) || exit;

/**
 * Portfolio Grid Shortcode.
 */
if ( ! class_exists( 'VCEX_Portfolio_Grid_Shortcode' ) ) {

	class VCEX_Portfolio_Grid_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_portfolio_grid';

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
			return esc_html__( 'Portfolio Grid', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Recent portfolio posts grid', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				// Main
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Header', 'total-theme-core' ),
					'param_name' => 'header',
					'admin_label' => true,
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Header Style', 'total-theme-core' ),
					'param_name' => 'header_style',
					'description' => self::param_description( 'header_style' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Grid Style', 'total-theme-core' ),
					'param_name' => 'grid_style',
					'value' => array(
						esc_html__( 'Fit Columns', 'total-theme-core' ) => 'fit_columns',
						esc_html__( 'Masonry', 'total-theme-core' ) => 'masonry',
						esc_html__( 'No Margins', 'total-theme-core' ) => 'no_margins',
					),
					'edit_field_class' => 'vc_col-sm-3 vc_column clear',
				),
				array(
					'type' => 'vcex_grid_columns',
					'heading' => esc_html__( 'Columns', 'total-theme-core' ),
					'param_name' => 'columns',
					'std' => '3',
					'edit_field_class' => 'vc_col-sm-3 vc_column',
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'grid_gap',
					'heading' => esc_html__( 'Gap', 'total-theme-core' ),
					'param_name' => 'columns_gap',
					'edit_field_class' => 'vc_col-sm-3 vc_column',
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Responsive', 'total-theme-core' ),
					'param_name' => 'columns_responsive',
					'value' => array(
						esc_html__( 'Yes', 'total-theme-core' ) => 'true',
						esc_html__( 'No', 'total-theme-core' ) => 'false',
					),
					'edit_field_class' => 'vc_col-sm-3 vc_column',
					'dependency' => array( 'element' => 'columns', 'value' => array( '2', '3', '4', '5', '6', '7', '8', '9', '10' ) ),
				),
				array(
					'type' => 'vcex_grid_columns_responsive',
					'heading' => esc_html__( 'Responsive Column Settings', 'total-theme-core' ),
					'param_name' => 'columns_responsive_settings',
					'dependency' => array( 'element' => 'columns_responsive', 'value' => 'true' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( '1 Column Style', 'total-theme-core' ),
					'param_name' => 'single_column_style',
					'value' => array(
						esc_html__( 'Default', 'total-theme-core' ) => 'default',
						esc_html__( 'Left Image And Right Content', 'total-theme-core' ) => 'left_thumbs',
					),
					'dependency' => array( 'element' => 'columns', 'value' => '1' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Link Target', 'total-theme-core' ),
					'param_name' => 'link_target',
					'value' => array(
						esc_html__( 'Default', 'total-theme-core' ) => 'self',
						esc_html__( 'Blank', 'total-theme-core' ) => 'blank',
					),
					'description' => esc_html__( 'This will apply to the image, title and readmore button', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'classes',
					'admin_label' => true,
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
				),
				vcex_vc_map_add_css_animation(),
				// Query
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Advanced Query', 'total-theme-core' ),
					'param_name' => 'custom_query',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to build a custom query using your own parameters.', 'total-theme-core' ),
				),
				array(
					'type' => 'textarea_safe',
					'heading' => esc_html__( 'Query Parameter String or Callback Function Name', 'total-theme-core' ),
					'param_name' => 'custom_query_args',
					'description' => self::param_description( 'advanced_query' ),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'true' ) ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Posts Per Page', 'total-theme-core' ),
					'param_name' => 'posts_per_page',
					'value' => '8',
					'description' => esc_html__( 'When pagination is disabled this is also used for the post count.', 'total-theme-core' ),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Pagination', 'total-theme-core' ),
					'param_name' => 'pagination',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Load More Button', 'total-theme-core' ),
					'param_name' => 'pagination_loadmore',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Offset', 'total-theme-core' ),
					'param_name' => 'offset',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Number of post to displace or pass over.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Include Categories', 'total-theme-core' ),
					'param_name' => 'include_categories',
					'param_holder_class' => 'vc_not-for-custom',
					'admin_label' => true,
					'settings' => array(
						'multiple' => true,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Exclude Categories', 'total-theme-core' ),
					'param_name' => 'exclude_categories',
					'param_holder_class' => 'vc_not-for-custom',
					'admin_label' => true,
					'settings' => array(
						'multiple' => true,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Limit By Author', 'total-theme-core' ),
					'param_name' => 'author_in',
					'settings' => array(
						'multiple' => true,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order', 'total-theme-core' ),
					'param_name' => 'order',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'value' => array(
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'DESC', 'total-theme-core' ) => 'DESC',
						esc_html__( 'ASC', 'total-theme-core' ) => 'ASC',
					),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Order By', 'total-theme-core' ),
					'param_name' => 'orderby',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Orderby: Meta Key', 'total-theme-core' ),
					'param_name' => 'orderby_meta_key',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'orderby', 'value' => array( 'meta_value_num', 'meta_value' ) ),
				),
				// Style
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Entry Shadow', 'total-theme-core' ),
					'param_name' => 'entry_shadow',
					'choices' => 'shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Content Style', 'total-theme-core' ),
					'param_name' => 'content_style',
					'description' => esc_html__( 'Does not apply to the 1 Column Left Thumbnail Style Grid.', 'total-theme-core' ),
					'std' => 'boxed',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Content Alignment', 'total-theme-core' ),
					'param_name' => 'content_alignment',
					'std' => '',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Equal Content Height', 'total-theme-core' ),
					'param_name' => 'equal_heights_grid',
					'std' => 'false',
					'description' => esc_html__( 'Enable so the content area for each entry is the same height.', 'total-theme-core' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'padding',
					'heading' => esc_html__( 'Content Padding', 'total-theme-core' ),
					'param_name' => 'content_padding_all',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Content Background', 'total-theme-core' ),
					'param_name' => 'content_background_color',
					'css' => [ 'selector' => '.entry-details', 'property' => 'background-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'border_style',
					'heading' => esc_html__( 'Content Border Style', 'total-theme-core' ),
					'param_name' => 'content_border_style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'border_width',
					'heading' => esc_html__( 'Content Border Width', 'total-theme-core' ),
					'param_name' => 'content_border_width',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'content_border_color',
					'css' => [ 'selector' => '.entry-details', 'property' => 'border-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Content Opacity', 'total-theme-core' ),
					'param_name' => 'content_opacity',
					'css' => [ 'selector' => '.entry-details', 'property' => 'opacity' ],
					'choices' => 'opacity',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				// Filter
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'filter',
					'description' => esc_html__( 'Enables a category filter to show and hide posts based on their categories. This does not load posts via AJAX, but rather filters items currently on the page.', 'total-theme-core' ),
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => array( 'false' ) ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Display All Link?', 'total-theme-core' ),
					'param_name' => 'filter_all_link',
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => array( 'element' => 'filter', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'no',
					'heading' => esc_html__( 'Center Filter Links', 'total-theme-core' ),
					'param_name' => 'center_filter',
					'vcex' => array(
						'off' => 'no',
						'on' => 'yes',
					),
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => array( 'element' => 'filter', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'breakpoint',
					'heading' => esc_html__( 'Switch to Select Dropdown at Breakpoint', 'total-theme-core' ),
					'param_name' => 'filter_select_bk',
					'description' => esc_html__( 'By default the filter will display buttons for all devices. Choose a custom breakpoint if you wish to display a select dropdown for smaller screens.', 'total-theme-core' ),
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => array( 'element' => 'filter', 'value' => 'true' ),
				),
				array(
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Default Active Category', 'total-theme-core' ),
					'param_name' => 'filter_active_category',
					'param_holder_class' => 'vc_not-for-custom',
					'admin_label' => true,
					'settings' => array(
						'multiple' => false,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					),
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => array( 'element' => 'filter', 'value' => 'true' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Filter "All" Text', 'total-theme-core' ),
					'param_name' => 'all_text',
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => array( 'element' => 'filter_all_link', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_button_styles',
					'heading' => esc_html__( 'Button Style', 'total-theme-core' ),
					'param_name' => 'filter_button_style',
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'std' => 'minimal-border',
					'dependency' => array( 'element' => 'filter', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_button_colors',
					'heading' => esc_html__( 'Button Color', 'total-theme-core' ),
					'param_name' => 'filter_button_color',
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => array( 'element' => 'filter', 'value' => 'true' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Layout Mode', 'total-theme-core' ),
					'param_name' => 'masonry_layout_mode',
					'value' => array(
						esc_html__( 'Masonry', 'total-theme-core' ) => 'masonry',
						esc_html__( 'Fit Rows', 'total-theme-core' ) => 'fitRows',
					),
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => array( 'element' => 'filter', 'value' => 'true' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Filter Speed', 'total-theme-core' ),
					'param_name' => 'filter_speed',
					'description' => esc_html__( 'Default is "0.4" seconds. Enter "0.0" to disable.', 'total-theme-core' ),
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => array( 'element' => 'filter', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'filter_font_size',
					'css' => [ 'selector' => '.vcex-portfolio-filter', 'property' => 'font-size' ],
					'group' => esc_html__( 'Filter', 'total-theme-core' ),
					'dependency' => array( 'element' => 'filter', 'value' => 'true' ),
				),
				// Images
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'entry_media',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Featured Videos/oEmbeds', 'total-theme-core' ),
					'param_name' => 'featured_video',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'entry_media', 'value' => 'true' ),
				),
				/*
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Gallery Slider', 'total-theme-core' ),
					'param_name' => 'gallery_slider',
					'value' => array(
						$s_no => 'false',
						$s_yes => 'true',
					),
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'description' => esc_html__( 'Displays a slider of the latest photos added to the post Image Gallery metabox.', 'total-theme-core' ),
				),
				*/
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Image Links To', 'total-theme-core' ),
					'param_name' => 'thumb_link',
					'value' => array(
						esc_html__( 'Post', 'total-theme-core' ) => 'post',
						esc_html__( 'Lightbox', 'total-theme-core' ) => 'lightbox',
						esc_html__( 'Post Gallery Lightbox', 'total-theme-core' ) => 'lightbox_gallery',
						esc_html__( 'Nowhere', 'total-theme-core' ) => 'nowhere',
					),
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'entry_media', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'std' => 'wpex_custom',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'entry_media', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_crop',
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'group' => esc_html__( 'Media', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'img_width',
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'description' => esc_html__( 'Enter a width in pixels.', 'total-theme-core' ),
					'group' => esc_html__( 'Media', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'param_name' => 'img_height',
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'group' => esc_html__( 'Media', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'border_radius',
					'supports_blobs' => true,
					'heading' => esc_html__( 'Image Border Radius', 'total-theme-core' ),
					'param_name' => 'img_border_radius',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'entry_media', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Overlay', 'total-theme-core' ),
					'param_name' => 'overlay_style',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'entry_media', 'value' => 'true' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Button Text', 'total-theme-core' ),
					'param_name' => 'overlay_button_text',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'overlay_style', 'value' => 'hover-button' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Excerpt Length', 'total-theme-core' ),
					'param_name' => 'overlay_excerpt_length',
					'value' => '15',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'overlay_style', 'value' => 'title-excerpt-hover' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Hover', 'total-theme-core' ),
					'param_name' => 'img_hover_style',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'entry_media', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Filter', 'total-theme-core' ),
					'param_name' => 'img_filter',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'entry_media', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Lightbox Gallery', 'total-theme-core' ),
					'param_name' => 'thumb_lightbox_gallery',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'thumb_link', 'value' => 'lightbox' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Lightbox Title', 'total-theme-core' ),
					'param_name' => 'thumb_lightbox_title',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'thumb_link', 'value' => array( 'lightbox' ) ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Lightbox Excerpt', 'total-theme-core' ),
					'param_name' => 'thumb_lightbox_caption',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'thumb_link', 'value' => 'lightbox' ),
				),
				// Title
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'title',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'HTML Tag', 'total-theme-core' ),
					'param_name' => 'title_tag',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'std' => 'h2',
					'value' => array(
						'h2' => 'h2',
						'h3' => 'h3',
						'h4' => 'h4',
						'h5' => 'h5',
						'h6' => 'h6',
						'div' => 'div',
					),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Links To', 'total-theme-core' ),
					'param_name' => 'title_link',
					'value' => array(
						esc_html__( 'Post', 'total-theme-core' ) => 'post',
						esc_html__( 'Lightbox', 'total-theme-core' ) => 'lightbox',
						esc_html__( 'Nowhere', 'total-theme-core' ) => 'nowhere',
					),
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'content_heading_color',
					'css' => [ 'selector' => '.entry-title', 'property' => 'color' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'content_heading_size',
					'css' => [ 'selector' => '.entry-title', 'property' => 'font-size' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'content_heading_line_height',
					'choices' => 'line_height',
					'css' => [ 'selector' => '.entry-title', 'property' => 'line-height' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'content_heading_margin',
					'description' => self::param_description( 'margin' ),
					'css' => [ 'selector' => '.entry-title', 'property' => 'margin' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'content_heading_weight',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-title', 'property' => 'font-weight' ],
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'content_heading_transform',
					'choices' => 'text_transform',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-title', 'property' => 'text-transform' ],
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				// Meta
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'show_categories',
					'group' => esc_html__( 'Categories', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Show Only The First Category', 'total-theme-core' ),
					'param_name' => 'show_first_category_only',
					'group' => esc_html__( 'Categories', 'total-theme-core' ),
					'dependency' => array( 'element' => 'show_categories', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Link to Archive', 'total-theme-core' ),
					'param_name' => 'categories_links',
					'group' => esc_html__( 'Categories', 'total-theme-core' ),
					'dependency' => array( 'element' => 'show_categories', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'categories_font_size',
					'css' => [ 'selector' => '.entry-categories', 'property' => 'font-size' ],
					'group' => esc_html__( 'Categories', 'total-theme-core' ),
					'dependency' => array( 'element' => 'show_categories', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'categories_margin',
					'css' => [ 'selector' => '.entry-categories', 'property' => 'margin' ],
					'group' => esc_html__( 'Categories', 'total-theme-core' ),
					'dependency' => array( 'element' => 'show_categories', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'categories_color',
					'css' => [ 'selector' => '.entry-categories', 'property' => 'color' ],
					'group' => esc_html__( 'Categories', 'total-theme-core' ),
					'dependency' => array( 'element' => 'show_categories', 'value' => 'true' ),
				),
				// Excerpt
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'excerpt',
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Excerpt Length', 'total-theme-core' ),
					'param_name' => 'excerpt_length',
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'description' => esc_html__( 'Enter how many words to display for the excerpt. To display the full post content enter "-1". To display the full post content up to the "more" tag enter "9999".', 'total-theme-core' ),
					'std' => '15',
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'content_color',
					'css' => [ 'selector' => '.entry-details', 'property' => 'color' ],
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'content_font_size',
					'css' => [ 'selector' => '.entry-excerpt', 'property' => 'font-size' ],
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
				),
				// Readmore
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'read_more',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Text', 'total-theme-core' ),
					'param_name' => 'read_more_text',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_button_styles',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'readmore_style',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_button_colors',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'readmore_style_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Arrow', 'total-theme-core' ),
					'param_name' => 'readmore_rarr',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'readmore_size',
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'font-size' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'readmore_border_radius',
					'choices' => 'border_radius',
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'readmore_padding',
					'description' => self::param_description( 'padding' ),
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'padding' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'readmore_margin',
					'description' => self::param_description( 'margin' ),
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'margin' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'readmore_background',
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'background' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'readmore_color',
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'color' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'readmore_hover_background',
					'css' => [ 'selector' => '.entry-readmore:hover', 'property' => 'background' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'readmore_hover_color',
					'css' => [ 'selector' => '.entry-readmore:hover', 'property' => 'color' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				// CSS
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'Entry CSS box', 'total-theme-core' ),
					'param_name' => 'entry_css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				),
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'Content CSS box', 'total-theme-core' ),
					'param_name' => 'content_css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				),
				// AJAX fields
				array( 'type' => 'hidden', 'param_name' => 'entry_count' ),
				array( 'type' => 'hidden', 'param_name' => 'paged' ),
				// Deprecated fields
				array( 'type' => 'hidden', 'param_name' => 'term_slug' ),
				// Deprecated with inline styles.
				array(
					'type' => 'hidden',
					'param_name' => 'content_background',
					'css' => [ 'selector' => '.entry-details', 'property' => 'background-color' ]
				),
				array(
					'type' => 'hidden',
					'param_name' => 'content_border',
					'css' => [ 'selector' => '.entry-details', 'property' => 'border' ]
				),
				array(
					'type' => 'hidden',
					'param_name' => 'content_margin',
					'css' => [ 'selector' => '.entry-details', 'property' => 'margin' ]
				),
				array(
					'type' => 'hidden',
					'param_name' => 'content_padding',
					'css' => [ 'selector' => '.entry-details', 'property' => 'padding' ]
				),
			);
		}

		/**
		 * Parse WPBakery attributes on edit.
		 */
		public static function vc_edit_form_fields_attributes( $atts = [] ) {
			$atts = vcex_parse_deprecated_grid_entry_content_css( $atts );
			unset( $atts['content_background'] );
			unset( $atts['content_border'] );
			unset( $atts['content_margin'] );
			unset( $atts['content_padding'] );
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
			// Add autcomplete callbacks.
			add_filter(
				'vc_autocomplete_vcex_portfolio_grid_include_categories_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Portfolio_Categories::callback'
			);
			add_filter(
				'vc_autocomplete_vcex_portfolio_grid_exclude_categories_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Portfolio_Categories::callback'
			);
			add_filter(
				'vc_autocomplete_vcex_portfolio_grid_filter_active_category_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Portfolio_Categories::callback'
			);
			add_filter(
				'vc_autocomplete_vcex_portfolio_grid_author_in_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Users::callback'
			);
			// Render autocomplete suggestions.
			add_filter(
				'vc_autocomplete_vcex_portfolio_grid_include_categories_render',
				'TotalThemeCore\WPBakery\Autocomplete\Portfolio_Categories::render'
			);
			add_filter(
				'vc_autocomplete_vcex_portfolio_grid_exclude_categories_render',
				'TotalThemeCore\WPBakery\Autocomplete\Portfolio_Categories::render'
			);

			add_filter(
				'vc_autocomplete_vcex_portfolio_grid_filter_active_category_render',
				'TotalThemeCore\WPBakery\Autocomplete\Portfolio_Categories::render'
			);
			add_filter(
				'vc_autocomplete_vcex_portfolio_grid_author_in_render',
				'TotalThemeCore\WPBakery\Autocomplete\Users::render'
			);
		}

	}

}

new VCEX_Portfolio_Grid_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Portfolio_Grid' ) ) {
	class WPBakeryShortCode_Vcex_Portfolio_Grid extends WPBakeryShortCode {}
}
