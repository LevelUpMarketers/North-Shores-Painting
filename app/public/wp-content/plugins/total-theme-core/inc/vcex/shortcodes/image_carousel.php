<?php

defined( 'ABSPATH' ) || exit;

/**
 * Image Carousel Shortcode.
 */
if ( ! class_exists( 'VCEX_Image_Carousel' ) ) {

	class VCEX_Image_Carousel extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_image_carousel';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Returns list of style dependencies.
		 */
		public static function get_style_depends(): array {
			return (array) totalthemecore_call_static( 'Vcex\Carousel\Core', 'get_style_depends' );
		}

		/**
		 * Returns list of script dependencies.
		 */
		public static function get_script_depends(): array {
			return (array) totalthemecore_call_static( 'Vcex\Carousel\Core', 'get_script_depends' );
		}

		/**
		 * Shortcode title.
		 */
		public static function get_title(): string {
			return esc_html__( 'Image Carousel', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Image based jQuery carousel', 'total-theme-core' );
		}

		/**
		 * Returns custom vc map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			return [
				'admin_enqueue_js' => 'image-gallery',
				'js_view'          => 'vcexBackendViewImageGallery',
			];
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$params = [
				// Gallery
				[
					'type' => 'attach_images',
					'heading'  => esc_html__( 'Images', 'total-theme-core' ),
					'param_name' => 'image_ids',
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order By', 'total-theme-core' ),
					'param_name' => 'orderby',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => [ 'element' => 'custom_query', 'value' => 'false' ],
					'std' => '',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' )  => '',
						esc_html__( 'Date', 'total-theme-core' )     => 'date',
						esc_html__( 'Title', 'total-theme-core' )    => 'title',
						esc_html__( 'Slug', 'total-theme-core' )     => 'name',
						esc_html__( 'Random', 'total-theme-core' )   => 'rand',
					],
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order', 'total-theme-core' ),
					'param_name' => 'order',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'value' => [
						esc_html__( 'DESC', 'total-theme-core' ) => 'DESC',
						esc_html__( 'ASC', 'total-theme-core' ) => 'ASC',
					],
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'dependency' => [ 'element' => 'orderby', 'value' => [ 'date', 'title', 'name' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading'  => esc_html__( 'Post Gallery', 'total-theme-core' ),
					'param_name' => 'post_gallery',
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'description' => sprintf( esc_html__( 'Enable to display images from the current post "%sImage Gallery%s".', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/using-post-gallery-image-galleries/" target="_blank" rel="noopener noreferrer">', '</a>' ) . '<br>' . esc_html__( 'You can define images above to display as a fallback in the frontend editor when working with dynamic templates.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'gallery',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name'  => 'custom_field_gallery',
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
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
					'dependency' => [ 'element' => 'header', 'not_empty' => true ],
				],
				[
					'type' => 'textfield',
					'heading'  => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'classes',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core' ),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core' ),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
				],
				// Style
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading'  => esc_html__( 'Vertical Align Items', 'total-theme-core' ),
					'param_name' => 'vertical_align',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
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
					'description' => esc_html__( 'Styling when the image title and/or caption is enabled.', 'total-theme-core' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Content Text Align', 'total-theme-core' ),
					'param_name' => 'content_alignment',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'padding',
					'heading' => esc_html__( 'Content Padding', 'total-theme-core' ),
					'param_name' => 'content_padding_all',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Content Background', 'total-theme-core' ),
					'param_name' => 'content_background_color',
					'css' => [ 'selector' => '.entry-details', 'property' => 'background-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_style',
					'heading' => esc_html__( 'Content Border Style', 'total-theme-core' ),
					'param_name' => 'content_border_style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_width',
					'heading' => esc_html__( 'Content Border Width', 'total-theme-core' ),
					'param_name' => 'content_border_width',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'content_border_color',
					'css' => [ 'selector' => '.entry-details', 'property' => 'border-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Image
				[
					'type' => 'vcex_select',
					'choices' => 'aspect_ratio',
					'heading' => esc_html__( 'Image Aspect Ratio', 'total-theme-core' ),
					'param_name' => 'img_aspect_ratio',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'object_fit',
					'heading' => esc_html__( 'Image Fit', 'total-theme-core' ),
					'param_name' => 'img_object_fit',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_aspect_ratio', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'std' => 'wpex_custom',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_crop',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'img_width',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'param_name' => 'img_height',
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_radius',
					'supports_blobs' => true,
					'heading' => esc_html__( 'Image Border Radius', 'total-theme-core' ),
					'param_name' => 'img_border_radius',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Overlay', 'total-theme-core' ),
					'param_name' => 'overlay_style',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Button Text', 'total-theme-core' ),
					'param_name' => 'overlay_button_text',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'overlay_style', 'value' => 'hover-button' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Hover', 'total-theme-core' ),
					'param_name' => 'img_hover_style',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Filter', 'total-theme-core' ),
					'param_name' => 'img_filter',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Links
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Link', 'total-theme-core' ),
					'param_name' => 'thumbnail_link',
					'std' => 'none',
					'choices' => [
						'none' => esc_html__( 'None', 'total-theme-core' ),
						'lightbox' => esc_html__( 'Lightbox', 'total-theme-core' ),
						'full_image' => esc_html__( 'Full Image', 'total-theme-core' ),
						'attachment_page' => esc_html__( 'Attachment Page', 'total-theme-core' ),
						'parent_page' => esc_html__( 'Uploaded To Page', 'total-theme-core' ),
						'custom_link' => esc_html__( 'Custom Links', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'exploded_textarea',
					'heading'  => esc_html__( 'Custom links', 'total-theme-core' ),
					'param_name' => 'custom_links',
					'description' => esc_html__( 'Enter links for each slide here. Divide links with linebreaks (Enter). For images without a link enter a # symbol.', 'total-theme-core' ),
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'dependency' => [ 'element' => 'thumbnail_link', 'value' => 'custom_link' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Link Meta Key', 'total-theme-core' ),
					'param_name' => 'link_meta_key',
					'description' => esc_html__( 'If you are using a meta value (custom field) for your image links you can enter the meta key here.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'thumbnail_link', 'value' => 'custom_link' ],
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading'  => esc_html__( 'Target', 'total-theme-core' ),
					'param_name' => 'custom_links_target',
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'choices' => 'link_target',
					'dependency' => [
						'element' => 'thumbnail_link',
						'value' => [ 'custom_link', 'attachment_page', 'full_image' ],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Lightbox Title', 'total-theme-core' ),
					'param_name' => 'lightbox_title',
					'std' => 'none',
					'choices' => [
						'none' => esc_html__( 'None', 'total-theme-core' ),
						'alt' => esc_html__( 'Alt', 'total-theme-core' ),
						'title' => esc_html__( 'Title', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'dependency' => [ 'element' => 'thumbnail_link', 'value' => 'lightbox' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Lightbox Caption', 'total-theme-core' ),
					'param_name' => 'lightbox_caption',
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'dependency' => [ 'element' => 'thumbnail_link', 'value' => 'lightbox' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Lightbox Gallery', 'total-theme-core' ),
					'param_name' => 'lightbox_gallery',
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'dependency' => [ 'element' => 'thumbnail_link', 'value' => 'lightbox' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Title
				[
					'type' => 'vcex_ofswitch',
					'std' => 'no',
					'vcex' => [ 'on' => 'yes', 'off' => 'no' ],
					'heading' => esc_html__( 'Title', 'total-theme-core' ),
					'param_name' => 'title',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Title Based On Image', 'total-theme-core' ),
					'param_name' => 'title_type',
					'std' => 'title',
					'choices' => [
						'title' => esc_html__( 'Title', 'total-theme-core' ),
						'alt' => esc_html__( 'Alt', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'content_heading_color',
					'css' => [ 'selector' => '.entry-title', 'property' => 'color' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'content_heading_weight',
					'css' => [ 'selector' => '.entry-title', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'content_heading_transform',
					'choices' => 'text_transform',
					'css' => [ 'selector' => '.entry-title', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'content_heading_size',
					'css' => [ 'selector' => '.entry-title', 'property' => 'font_size' ],
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'content_heading_margin',
					'css' => [ 'selector' => '.entry-title', 'property' => 'margin' ],
					'description' => self::param_description( 'margin_shorthand' ),
					'group' => esc_html__( 'Title', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'title_typo',
					'selector' => '.entry-title',
					'group' => esc_html__( 'Title', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Caption
				[
					'type' => 'vcex_ofswitch',
					'std' => 'no',
					'vcex' => [ 'on' => 'yes', 'off' => 'no' ],
					'heading' => esc_html__( 'Display Caption', 'total-theme-core' ),
					'param_name' => 'caption',
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'content_color',
					'css' => [ 'selector' => '.entry-details', 'property' => 'color' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'responsive' => false,
					'heading'  => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'content_font_size',
					'css' => [ 'selector' => '.entry-details', 'property' => 'font-size' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'content_typo',
					'selector' => '.entry-excerpt',
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'Entry CSS Box', 'total-theme-core' ),
					'param_name' => 'entry_css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'Entry Content CSS Box', 'total-theme-core' ),
					'param_name' => 'content_css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				// Deprecated params.
				[ 'type' => 'hidden', 'param_name' => 'lightbox_path' ],
				[ 'type' => 'hidden', 'param_name' => 'rounded_image' ],
				[ 'type' => 'hidden', 'param_name' => 'content_background' ],
				[ 'type' => 'hidden', 'param_name' => 'content_margin' ],
				[ 'type' => 'hidden', 'param_name' => 'content_padding' ],
				[ 'type' => 'hidden', 'param_name' => 'content_border' ],
				[ 'type' => 'hidden', 'param_name' => 'randomize_images' ],
			];

			// Real Media Library integration.
			if ( \defined( 'RML_VERSION' ) ) {
				$params[] = [
					'type' => 'vcex_select',
					'choices' => 'real_media_library_folders',
					'heading' => \esc_html__( 'Real Media Library Folder', 'total-theme-core' ),
					'param_name' => 'rml_folder',
					'group' => \esc_html__( 'Gallery', 'total-theme-core' ),
				];
				$params[] = [
					'type' => 'textfield',
					'heading' => \esc_html__( 'Count', 'total-theme-core' ),
					'param_name' => 'posts_per_page',
					'value' => '12',
					'description' => \esc_html__( 'How many images to grab from this folder. Enter -1 to display all of them.', 'total-theme-core' ),
					'group' => \esc_html__( 'Gallery', 'total-theme-core' ),
					'dependency' => [ 'element' => 'rml_folder', 'not_empty' => true ],
				];
			}

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
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}

			if ( isset( $atts['rounded_image'] ) ) {
				if ( empty( $atts['img_border_radius'] ) && 'yes' === $atts['rounded_image'] ) {
					$atts['img_border_radius'] = 'round';
				}
				unset( $atts['rounded_image'] );
			}

			if ( isset( $atts['randomize_images'] ) ) {
				if ( empty( $atts['orderby'] ) && 'true' == $atts['randomize_images'] ) {
					$atts['orderby'] = 'rand';
				}
				unset( $atts['randomize_images'] );
			}

			return $atts;
		}

	}

}

new VCEX_Image_Carousel;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Image_Carousel' ) ) {
	class WPBakeryShortCode_Vcex_Image_Carousel extends WPBakeryShortCode {}
}
