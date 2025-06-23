<?php

defined( 'ABSPATH' ) || exit;

/**
 * Recent News Shortcode.
 */
if ( ! class_exists( 'VCEX_Recent_News_Shortcode' ) ) {

	class VCEX_Recent_News_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_recent_news';

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
			return esc_html__( 'Recent News', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Posts with calendar style date', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				// General
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
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Columns', 'total-theme-core' ),
					'param_name' => 'grid_columns',
					'std' => '1',
					'admin_label' => true,
				),
				array(
					'type' => 'vcex_grid_columns_responsive',
					'heading' => esc_html__( 'Responsive Column Settings', 'total-theme-core' ),
					'param_name' => 'columns_responsive_settings',
					'dependency' => array( 'element' => 'grid_columns', 'value' => array( '2', '3', '4', '5', '6', '7', '8', '9', '10' ) ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'grid_gap',
					'heading' => esc_html__( 'Gap', 'total-theme-core' ),
					'param_name' => 'columns_gap',
					'dependency' => array( 'element' => 'grid_columns', 'value' => array( '2', '3', '4', '5', '6', '7', '8', '9', '10' ) ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Divider Style', 'total-theme-core' ),
					'param_name' => 'divider_style',
					'dependency' => array( 'element' => 'grid_columns', 'value' => '1' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Divider Margin', 'total-theme-core' ),
					'param_name' => 'divider_margin',
					'dependency' => array( 'element' => 'grid_columns', 'value' => '1' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'border_width',
					'heading' => esc_html__( 'Divider Size', 'total-theme-core' ),
					'param_name' => 'divider_width',
					'dependency' => array( 'element' => 'grid_columns', 'value' => '1' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Divider Color', 'total-theme-core' ),
					'param_name' => 'divider_color',
					'css' => [
						'selector' => '.vcex-recent-news-entry__divider',
						'property' => 'border-color',
					],
					'dependency' => array( 'element' => 'grid_columns', 'value' => '1' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Remove Divider on Last Entry?', 'total-theme-core' ),
					'param_name' => 'divider_remove_last',
					'std' => 'true',
					'dependency' => array( 'element' => 'grid_columns', 'value' => '1' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'description' => self::param_description( 'unique_id' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'classes',
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
					'dependency' => array( 'element' => 'custom_query', 'value' => 'true' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Get Posts From', 'total-theme-core' ),
					'param_name' => 'get_posts',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'std' => 'standard_post_types',
					'value' => array(
						esc_html__( 'Standard Posts','total-theme-core' ) => 'standard_post_types',
						esc_html__( 'Custom Post types','total-theme-core' ) => 'custom_post_types',
					),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'posttypes',
					'heading' => esc_html__( 'Post types', 'total-theme-core' ),
					'param_name' => 'post_types',
					'std' => 'post',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'get_posts', 'value' => 'custom_post_types' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Posts Per Page', 'total-theme-core' ),
					'param_name' => 'count',
					'value' => '3',
					'description' => esc_html__( 'When pagination is disabled this is also used for the post count.', 'total-theme-core' ),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Pagination', 'total-theme-core' ),
					'param_name' => 'pagination',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Load More Button', 'total-theme-core' ),
					'param_name' => 'pagination_loadmore',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Ignore Sticky Posts', 'total-theme-core' ),
					'param_name' => 'ignore_sticky_posts',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Offset', 'total-theme-core' ),
					'param_name' => 'offset',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Number of post to displace or pass over.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
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
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
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
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
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
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
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
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Order By', 'total-theme-core' ),
					'param_name' => 'orderby',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Orderby: Meta Key', 'total-theme-core' ),
					'param_name' => 'orderby_meta_key',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'orderby', 'value' => array( 'meta_value_num', 'meta_value' ) ),
				),
				// Media
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Display Featured Media?', 'total-theme-core' ),
					'param_name' => 'featured_image',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Display Featured Videos?', 'total-theme-core' ),
					'param_name' => 'featured_video',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'featured_image', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'std' => 'wpex_custom',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'featured_image', 'value' => 'true' ),
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
					'dependency' => array( 'element' => 'featured_image', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Overlay', 'total-theme-core' ),
					'param_name' => 'overlay_style',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'featured_image', 'value' => 'true' ),
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
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Button Text', 'total-theme-core' ),
					'param_name' => 'overlay_button_text',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'overlay_style', 'value' => 'hover-button' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Hover', 'total-theme-core' ),
					'param_name' => 'img_hover_style',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'featured_image', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Filter', 'total-theme-core' ),
					'param_name' => 'img_filter',
					'group' => esc_html__( 'Media', 'total-theme-core' ),
					'dependency' => array( 'element' => 'featured_image', 'value' => 'true' ),
				),
				// Categories
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'show_categories',
					'group' => esc_html__( 'Categories', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'taxonomy',
					'heading' => esc_html__( 'Taxonomy', 'total-theme-core' ),
					'param_name' => 'categories_taxonomy',
					'group' => esc_html__( 'Categories', 'total-theme-core' ),
					'description' => esc_html__( 'Enter a custom taxonomy to display instead of the standard category.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'show_categories', 'value' => 'true' ),
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
					'type' => 'textfield',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'categories_font_size',
					'description' => self::param_description( 'font_size' ),
					'group' => esc_html__( 'Categories', 'total-theme-core' ),
					'css' => [
						'selector' => '.vcex-recent-news-entry-categories',
						'property' => 'font-size',
					],
					'dependency' => array( 'element' => 'show_categories', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'categories_margin',
					'css' => [
						'selector' => '.vcex-recent-news-entry-categories',
						'property' => 'margin',
					],
					'group' => esc_html__( 'Categories', 'total-theme-core' ),
					'dependency' => array( 'element' => 'show_categories', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'categories_color',
					'css' => [
						'selector' => '.vcex-recent-news-entry-categories',
						'property' => 'color',
					],
					'group' => esc_html__( 'Categories', 'total-theme-core' ),
					'dependency' => array( 'element' => 'show_categories', 'value' => 'true' ),
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
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'HTML Tag', 'total-theme-core' ),
					'param_name' => 'title_tag',
					'std' => 'h2',
					'choices' => 'html_tag',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'title_weight',
					'css' => [
						'selector' => '.vcex-recent-news-entry-title-heading',
						'property' => 'font-weight',
					],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'title_transform',
					'choices' => 'text_transform',
					'css' => [
						'selector' => '.vcex-recent-news-entry-title-heading',
						'property' => 'text-transform',
					],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'title_size',
					'css' => [
						'selector' => '.vcex-recent-news-entry-title-heading',
						'property' => 'font-size',
					],
					'description' => self::param_description( 'font_size' ),
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'title_line_height',
					'choices' => 'line_height',
					'css' => [
						'selector' => '.vcex-recent-news-entry-title-heading',
						'property' => 'line-height',
					],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'title_margin',
					'css' => [
						'selector' => '.vcex-recent-news-entry-title-heading',
						'property' => 'margin',
					],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'title_color',
					'css' => [
						'selector' => '.vcex-recent-news-entry-title-heading',
						'property' => 'color',
					],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				// Date
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'date',
					'group' => esc_html__( 'Date', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Top Format', 'total-theme-core' ),
					'param_name' => 'day_format',
					'value' => 'd',
					'dependency' => array( 'element' => 'date', 'value' => 'true' ),
					'group' => esc_html__( 'Date', 'total-theme-core' ),
					'description' => sprintf( esc_html__( 'Enter your preferred date format according to the %sWordPress manual%s.', 'total-theme-core' ), '<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank" rel="noopener noreferrer">', '</a>' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Bottom Format', 'total-theme-core' ),
					'param_name' => 'month_year_format',
					'value' => 'M y',
					'group' => esc_html__( 'Date', 'total-theme-core' ),
					'dependency' => array( 'element' => 'date', 'value' => 'true' ),
					'description' => sprintf( esc_html__( 'Enter your preferred date format according to the %sPHP manual%s.', 'total-theme-core' ), '<a href="https://www.php.net/manual/en/function.date.php" target="_blank" rel="noopener noreferrer">', '</a>' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Side Margin', 'total-theme-core' ),
					'param_name' => 'date_side_margin',
					'group' => esc_html__( 'Date', 'total-theme-core' ),
					'dependency' => array( 'element' => 'date', 'value' => 'true' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Min-Width', 'total-theme-core' ),
					'param_name' => 'date_min_width',
					'dependency' => array( 'element' => 'date', 'value' => 'true' ),
					'css' => [
						'selector' => '.vcex-recent-news-date',
						'property' => 'min-width',
					],
					'group' => esc_html__( 'Date', 'total-theme-core' ),
					'description' => esc_html__( 'Default', 'total-theme-core' ) . ': 60px',
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Month Background', 'total-theme-core' ),
					'param_name' => 'month_background',
					'css' => [
						'selector' => '.vcex-recent-news-date__month',
						'property' => 'background-color',
					],
					'group' => esc_html__( 'Date', 'total-theme-core' ),
					'dependency' => array( 'element' => 'date', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Month Color', 'total-theme-core' ),
					'param_name' => 'month_color',
					'css' => [
						'selector' => '.vcex-recent-news-date__month',
						'property' => 'color',
					],
					'group' => esc_html__( 'Date', 'total-theme-core' ),
					'dependency' => array( 'element' => 'date', 'value' => 'true' ),
				),
				// Excerpt
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'param_name' => 'excerpt',
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Length', 'total-theme-core' ),
					'param_name' => 'excerpt_length',
					'value' => '30',
					'description' => esc_html__( 'Enter how many words to display for the excerpt. To display the full post content enter "-1". To display the full post content up to the "more" tag enter "9999".', 'total-theme-core' ),
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'excerpt_font_size',
					'css' => [
						'selector' => '.vcex-recent-news-entry-excerpt',
						'property' => 'font-size',
					],
					'description' => self::param_description( 'font_size' ),
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'excerpt_color',
					'css' => [
						'selector' => '.vcex-recent-news-entry-excerpt',
						'property' => 'color',
					],
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
				),
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
					'type' => 'textfield',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'readmore_size',
					'css' => [
						'selector' => 'a.vcex-recent-news-entry-readmore',
						'property' => 'font-size',
					],
					'description' => self::param_description( 'font_size' ),
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'readmore_border_radius',
					'choices' => 'border_radius',
					'css' => [
						'selector' => 'a.vcex-recent-news-entry-readmore',
						'property' => 'border-radius',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'readmore_padding',
					'css' => [
						'selector' => 'a.vcex-recent-news-entry-readmore',
						'property' => 'padding',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'readmore_margin',
					'css' => [
						'selector' => 'a.vcex-recent-news-entry-readmore',
						'property' => 'margin',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'readmore_background',
					'css' => [
						'selector' => 'a.vcex-recent-news-entry-readmore',
						'property' => 'background',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'readmore_color',
					'css' => [
						'selector' => 'a.vcex-recent-news-entry-readmore',
						'property' => 'color',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'readmore_hover_background',
					'css' => [
						'selector' => '.vcex-recent-news-entry-readmore:hover',
						'property' => 'background',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'readmore_hover_color',
					'css' => [
						'selector' => '.vcex-recent-news-entry-readmore:hover',
						'property' => 'color',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				// Design options
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				),
				// AJAX fields
				array( 'type' => 'hidden', 'param_name' => 'entry_count' ),
				array( 'type' => 'hidden', 'param_name' => 'paged' ),
				// Deprecated params
				array( 'type' => 'hidden', 'param_name' => 'entry_bottom_border_color' ),
				array( 'type' => 'hidden', 'param_name' => 'term_slug' ),
			);
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}

			if ( empty( $atts['divider_color'] ) && ! empty( $atts['entry_bottom_border_color'] ) ) {
				$atts['divider_color'] = $atts['entry_bottom_border_color'];
				unset( $atts['entry_bottom_border_color'] );
			}

			if ( ! empty( $atts['term_slug'] ) && empty( $atts['include_categories'] ) ) {
				$atts['include_categories'] = $atts['term_slug'];
				unset( $atts['term_slug'] );
			}

			return $atts;
		}

		/**
		 * Register autocomplete hooks.
		 */
		public static function register_vc_autocomplete_hooks(): void {
			add_filter(
				'vc_autocomplete_vcex_recent_news_include_categories_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Categories::callback'
			);
			add_filter(
				'vc_autocomplete_vcex_recent_news_include_categories_render',
				'TotalThemeCore\WPBakery\Autocomplete\Categories::render'
			);
			add_filter(
				'vc_autocomplete_vcex_recent_news_exclude_categories_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Categories::callback'
			);
			add_filter(
				'vc_autocomplete_vcex_recent_news_exclude_categories_render',
				'TotalThemeCore\WPBakery\Autocomplete\Categories::render'
			);
			add_filter(
				'vc_autocomplete_vcex_recent_news_author_in_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Users::callback'
			);
			add_filter(
				'vc_autocomplete_vcex_recent_news_author_in_render',
				'TotalThemeCore\WPBakery\Autocomplete\Users::render'
			);
		}

	}

}

new VCEX_Recent_News_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Recent_News' ) ) {
	class WPBakeryShortCode_Vcex_Recent_News extends WPBakeryShortCode {}
}
