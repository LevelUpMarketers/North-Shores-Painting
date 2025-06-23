<?php

defined( 'ABSPATH' ) || exit;

/**
 * Blog Carousel Shortcode.
 */
if ( ! class_exists( 'Vcex_Blog_Carousel_Shortcode' ) ) {

	class Vcex_Blog_Carousel_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		const TAG = 'vcex_blog_carousel';

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
			return esc_html__( 'Blog Carousel', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Recent blog posts carousel', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$params = [
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
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'classes',
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
				],
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
					'heading' => esc_html__( 'Post Count', 'total-theme-core' ),
					'param_name' => 'count',
					'value' => '8',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_query', 'value' => 'false' ],
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
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'std' => 'false',
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
						'groups' => true,
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
						'groups' => true,
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
						esc_html__( 'Default', 'total-theme-core' )=> '',
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
					'dependency' => [
						'element' => 'orderby',
						'value' => [ 'meta_value_num', 'meta_value' ]
					],
				],
				// Style
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'std' => 'default',
					'choices' => [
						'default' => esc_html__( 'Default', 'total-theme-core' ),
						'no-margins' => esc_html__( 'No Margins', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Content Style', 'total-theme-core' ),
					'param_name' => 'content_style',
					'std' => 'boxed',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Content Text Align', 'total-theme-core' ),
					'param_name' => 'content_alignment',
					'std' => '',
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
					'choices' => 'opacity',
					'css' => [ 'selector' => '.entry-details', 'property' => 'opacity' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				// Image
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'media',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'std' => 'true',
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Image Links To', 'total-theme-core' ),
					'param_name' => 'thumbnail_link',
					'std' => 'post',
					'choices' => [
						'post' => esc_html__( 'Post', 'total-theme-core' ),
						'lightbox' => esc_html__( 'Lightbox', 'total-theme-core' ),
						'none' => esc_html__( 'None', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'media', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Lightbox Gallery', 'total-theme-core' ),
					'param_name' => 'lightbox_gallery',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'thumbnail_link', 'value' => 'lightbox' ],
				],
				[
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'std' => 'wpex_custom',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'media', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_crop',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'img_width',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'description' => esc_html__( 'Enter a width in pixels.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
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
					'choices' => 'border_radius',
					'supports_blobs' => true,
					'heading' => esc_html__( 'Image Border Radius', 'total-theme-core' ),
					'param_name' => 'img_border_radius',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'media', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Overlay', 'total-theme-core' ),
					'param_name' => 'overlay_style',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'media', 'value' => 'true' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Button Text', 'total-theme-core' ),
					'param_name' => 'overlay_button_text',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'overlay_style', 'value' => 'hover-button' ],
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
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Hover', 'total-theme-core' ),
					'param_name' => 'img_hover_style',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'media', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Filter', 'total-theme-core' ),
					'param_name' => 'img_filter',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'media', 'value' => 'true' ],
				],
				// Title
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'title',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'std' => 'true',
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
					'choices' => 'text_transform',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'content_heading_transform',
					'css' => [ 'selector' => '.entry-title', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => [ 'element' => 'title', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__(  'Margin', 'total-theme-core' ),
					'param_name' => 'content_heading_margin',
					'description' => self::param_description( 'margin' ),
					'css' => [ 'selector' => '.entry-title', 'property' => 'margin' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'dependency' => [ 'element' => 'title', 'value' => 'true' ],
				],
				// Date
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'date',
					'group' => esc_html__( 'Date', 'total-theme-core' ),
					'std' => 'true',
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
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'excerpt',
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'std' => 'true',
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Length', 'total-theme-core' ),
					'param_name' => 'excerpt_length',
					'value' => '15',
					'description' => esc_html__( 'Enter how many words to display for the excerpt. To display the full post content enter "-1". To display the full post content up to the "more" tag enter "9999".', 'total-theme-core' ),
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
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
					'heading' => esc_html__( 'Text Color', 'total-theme-core' ),
					'param_name' => 'content_color',
					'css' => [ 'selector' => '.entry-excerpt', 'property' => 'color' ],
					'group' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'dependency' => [ 'element' => 'excerpt', 'value' => 'true' ],
				],
				// Readmore
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'read_more',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'std' => 'false',
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Text', 'total-theme-core' ),
					'param_name' => 'read_more_text',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'description' => self::param_description( 'text' ),
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
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Arrow', 'total-theme-core' ),
					'param_name' => 'readmore_rarr',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'std' => 'false',
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'readmore_background',
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'background-color' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'readmore_color',
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'color' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'readmore_hover_background',
					'css' => [ 'selector' => '.entry-readmore:hover', 'property' => 'background-color' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'readmore_hover_color',
					'css' => [ 'selector' => '.entry-readmore:hover', 'property' => 'color' ],
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
					'css' => [ 'selector' => '.entry-readmore', 'property' => 'margin' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'read_more', 'value' => 'true' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'content_css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				// Hidden/Deprecated fields
				[ 'type' => 'hidden', 'param_name' => 'term_slug' ],
				[ 'type' => 'hidden', 'param_name' => 'content_background' ],
				[ 'type' => 'hidden', 'param_name' => 'content_margin' ],
				[ 'type' => 'hidden', 'param_name' => 'content_padding' ],
				[ 'type' => 'hidden', 'param_name' => 'content_border' ],
			];

			return array_merge( $params, vcex_vc_map_carousel_settings() );
		}

		/**
		 * Parse WPBakery attributes on edit.
		 */
		public static function vc_edit_form_fields_attributes( $atts = [] ) {
			return self::parse_deprecated_attributes( vcex_parse_deprecated_grid_entry_content_css( $atts ) );
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
				'vc_autocomplete_vcex_blog_carousel_include_categories_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Categories::callback',
				10,
				1
			);
			\add_filter(
				'vc_autocomplete_vcex_blog_carousel_exclude_categories_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Categories::callback',
				10,
				1
			);
			// Render autocomplete suggestions.
			\add_filter(
				'vc_autocomplete_vcex_blog_carousel_include_categories_render',
				'TotalThemeCore\WPBakery\Autocomplete\Categories::render',
				10,
				1
			);
			\add_filter(
				'vc_autocomplete_vcex_blog_carousel_exclude_categories_render',
				'TotalThemeCore\WPBakery\Autocomplete\Categories::render',
				10,
				1
			);
		}

	}

}

new Vcex_Blog_Carousel_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Blog_Carousel' ) ) {
	class WPBakeryShortCode_Vcex_Blog_Carousel extends WPBakeryShortCode {}
}
