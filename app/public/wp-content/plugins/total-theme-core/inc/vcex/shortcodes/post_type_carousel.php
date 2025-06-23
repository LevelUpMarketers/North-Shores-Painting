<?php

defined( 'ABSPATH' ) || exit;

/**
 * Post Type Carousel Shortcode.
 */
if ( ! class_exists( 'VCEX_Post_Type_Carousel_Shortcode' ) ) {

	class VCEX_Post_Type_Carousel_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_post_type_carousel';

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
			return esc_html__( 'Post Types Carousel', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Posts carousel', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$params = array(
				// General
				array(
					'type' => 'vcex_notice',
					'param_name' => 'main_notice',
					'text' => esc_html__( 'We recommend you use the newer and more powerful "Post Cards" element instead.', 'total-theme-core' ),
				),
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
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
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
				),
				vcex_vc_map_add_css_animation(),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				),
				// Style
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'std' => 'default',
					'choices' => array(
						'default' => esc_html__( 'Default', 'total-theme-core' ),
						'no-margins' => esc_html__( 'No Margins', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Content Style', 'total-theme-core' ),
					'param_name' => 'content_style',
					'std' => 'boxed',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Content Text Align', 'total-theme-core' ),
					'param_name' => 'content_alignment',
					'std' => '',
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
					'type' => 'posttypes',
					'heading' => esc_html__( 'Post types', 'total-theme-core' ),
					'param_name' => 'post_types',
					'std' => 'post',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'admin_label' => true,
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Post Count', 'total-theme-core' ),
					'param_name' => 'count',
					'value' => '8',
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
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Ignore Sticky Posts', 'total-theme-core' ),
					'param_name' => 'ignore_sticky_posts',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Post With Thumbnails Only', 'total-theme-core' ),
					'param_name' => 'thumbnail_query',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Limit By Post ID\'s', 'total-theme-core' ),
					'param_name' => 'posts_in',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Seperate by a comma.', 'total-theme-core' ),
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
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Query by Taxonomy', 'total-theme-core' ),
					'param_name' => 'tax_query',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'taxonomy',
					'heading' => esc_html__( 'Taxonomy', 'total-theme-core' ),
					'param_name' => 'tax_query_taxonomy',
					'dependency' => array( 'element' => 'tax_query', 'value' => 'true' ),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'If you do not see your taxonomy in the dropdown you can still enter the taxonomy name manually.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'tax_query', 'value' => 'true' ),
				),
				array(
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Terms', 'total-theme-core' ),
					'param_name' => 'tax_query_terms',
					'dependency' => array( 'element' => 'tax_query', 'value' => 'true' ),
					'settings' => array(
						'multiple' => true,
						'min_length' => 1,
						'groups' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'If you do not see your terms in the dropdown you can still enter the term slugs manually seperated by a space.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'tax_query_taxonomy', 'not_empty' => true ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order', 'total-theme-core' ),
					'param_name' => 'order',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'value' => array(
						esc_html__( 'Default', 'total-theme-core' ) => 'default',
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
				// Image
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'media',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Image Links To', 'total-theme-core' ),
					'param_name' => 'thumbnail_link',
					'std' => 'post',
					'choices' => array(
						'post' => esc_html__( 'Post', 'total-theme-core' ),
						'lightbox' => esc_html__( 'Lightbox', 'total-theme-core' ),
						'none' => esc_html__( 'None', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'media', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Lightbox Gallery', 'total-theme-core' ),
					'param_name' => 'lightbox_gallery',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'thumbnail_link', 'value' => 'lightbox' ),
				),
				array(
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'media', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_crop',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom', ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'img_width',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom', ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'param_name' => 'img_height',
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom', ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'border_radius',
					'supports_blobs' => true,
					'heading' => esc_html__( 'Image Border Radius', 'total-theme-core' ),
					'param_name' => 'img_border_radius',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'media', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Overlay', 'total-theme-core' ),
					'param_name' => 'overlay_style',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'media', 'value' => 'true' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Button Text', 'total-theme-core' ),
					'param_name' => 'overlay_button_text',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'overlay_style', 'value' => 'hover-button' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Excerpt Length', 'total-theme-core' ),
					'param_name' => 'overlay_excerpt_length',
					'value' => '15',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'overlay_style', 'value' => 'title-excerpt-hover' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Hover', 'total-theme-core' ),
					'param_name' => 'img_hover_style',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'media', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Filter', 'total-theme-core' ),
					'param_name' => 'img_filter',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'media', 'value' => 'true' ),
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
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-title', 'property' => 'font-size' ],
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'content_heading_margin',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-title', 'property' => 'margin' ],
					'description' => self::param_description( 'margin' ),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'content_heading_line_height',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'choices' => 'line_height',
					'css' => [ 'selector' => '.entry-title', 'property' => 'line-height' ],
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'content_heading_weight',
					'css' => [ 'selector' => '.entry-title', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'content_heading_transform',
					'choices' => 'text_transform',
					'css' => [ 'selector' => '.entry-title', 'property' => 'text-transform' ],
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
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'date_color',
					'css' => [ 'selector' => '.entry-date', 'property' => 'color' ],
					'group' => esc_html__( 'Date', 'total-theme-core' ),
					'dependency' => array( 'element' => 'date', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'date_font_size',
					'css' => [ 'selector' => '.entry-date', 'property' => 'font-size' ],
					'dependency' => array( 'element' => 'date', 'value' => 'true' ),
					'group' => esc_html__( 'Date', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'date_margin',
					'group' => esc_html__( 'Date', 'total-theme-core' ),
					'description' => self::param_description( 'margin' ),
					'css' => [ 'selector' => '.entry-date', 'property' => 'margin' ],
					'dependency' => array( 'element' => 'title', 'value' => 'true' ),
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
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'categories_color',
					'css' => [ 'selector' => '.entry-categories', 'property' => 'color' ],
					'group' => esc_html__( 'Categories', 'total-theme-core' ),
					'dependency' => array( 'element' => 'show_categories', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'categories_font_size',
					'group' => esc_html__( 'Categories', 'total-theme-core' ),
					'dependency' => array( 'element' => 'show_categories', 'value' => 'true' ),
					'css' => [ 'selector' => '.entry-categories', 'property' => 'font-size' ],
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'categories_margin',
					'group' => esc_html__( 'Categories', 'total-theme-core' ),
					'description' => self::param_description( 'margin' ),
					'css' => [ 'selector' => '.entry-categories', 'property' => 'margin' ],
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
					'heading' => esc_html__( 'Length', 'total-theme-core' ),
					'param_name' => 'excerpt_length',
					'value' => '15',
					'description' => esc_html__( 'Enter how many words to display for the excerpt. To display the full post content enter "-1". To display the full post content up to the "more" tag enter "9999".', 'total-theme-core' ),
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'content_font_size',
					'css' => [ 'selector' => '.entry-details', 'property' => 'font-size' ],
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Text Color', 'total-theme-core' ),
					'param_name' => 'content_color',
					'css' => [ 'selector' => '.entry-details', 'property' => 'color' ],
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
				),
				// Readmore
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'read_more',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Text', 'total-theme-core' ),
					'param_name' => 'read_more_text',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'description' => self::param_description( 'text' ),
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
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'font-size' ],
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
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'description' => self::param_description( 'padding' ),
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'padding' ],
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'readmore_margin',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'description' => self::param_description( 'margin' ),
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'margin' ],
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'readmore_background',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'background' ],
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
			);
			return array_merge( $params, vcex_vc_map_carousel_settings() );
		}

		/**
		 * Register autocomplete hooks.
		 */
		public static function register_vc_autocomplete_hooks(): void {
			// Get autocomplete suggestion
			add_filter(
				'vc_autocomplete_vcex_post_type_carousel_tax_query_terms_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms::callback'
			);
			add_filter(
				'vc_autocomplete_vcex_post_type_carousel_author_in_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Users::callback'
			);
			// Render autocomplete suggestions
			add_filter(
				'vc_autocomplete_vcex_post_type_carousel_tax_query_terms_render',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms::render'
			);
			add_filter(
				'vc_autocomplete_vcex_post_type_carousel_author_in_render',
				'TotalThemeCore\WPBakery\Autocomplete\Users::render'
			);
		}

	}

}

new VCEX_Post_Type_Carousel_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Post_Type_Carousel' ) ) {
	class WPBakeryShortCode_Vcex_Post_Type_Carousel extends WPBakeryShortCode {}
}
