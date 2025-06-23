<?php

defined( 'ABSPATH' ) || exit;

/**
 * Teaser Shortcode.
 */
if ( ! class_exists( 'VCEX_Teaser_Shortcode' ) ) {

	class VCEX_Teaser_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_teaser';

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
			return esc_html__( 'Teaser Box', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'A teaser content box', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				// General
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Heading', 'total-theme-core' ),
					'param_name' => 'heading',
					'value' => 'Sample Heading',
					'description' => self::param_description( 'text' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textarea_html',
					'holder' => 'div',
					'heading' => esc_html__( 'Content', 'total-theme-core' ),
					'param_name' => 'content',
					'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed faucibus feugiat convallis. Integer nec eros et risus condimentum tristique vel vitae arcu.',
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
					'param_name' => 'classes',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_hover_animations',
					'heading' => esc_html__( 'Hover Animation', 'total-theme-core'),
					'param_name' => 'hover_animation',
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				],
				// Style
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'Plain', 'total-theme-core' )   => 'one',
						esc_html__( 'Boxed Rounded', 'total-theme-core' ) => 'two',
						esc_html__( 'Boxed Square', 'total-theme-core' ) => 'three',
						esc_html__( 'Outline', 'total-theme-core' ) => 'four',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'text_align',
					'std' => '',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'padding',
					'dependency' => [ 'element' => 'style', 'value' => [ 'two', 'three' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background',
					'dependency' => [ 'element' => 'style', 'value' => [ 'two', 'three' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'css' => true,
					'dependency' => [ 'element' => 'style', 'value' => [ 'four' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'description' => self::param_description( 'border_radius' ),
					'css' => true,
					'dependency' => [ 'element' => 'style', 'value' => [ 'two', 'three', 'four' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Heading
				[
					'type' => 'vcex_select',
					'choices' => 'typography_style',
					'heading' => esc_html__( 'Typography Style', 'total-theme-core' ),
					'param_name' => 'heading_typography_style',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Tag', 'total-theme-core' ),
					'param_name' => 'heading_type',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'std' => 'h2',
					'choices' => 'html_tag',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'heading_size',
					'css' => [ 'selector' => '.vcex-teaser-heading', 'property' => 'font-size' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'heading_font_family',
					'css' => [ 'selector' => '.vcex-teaser-heading', 'property' => 'font-family' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'heading_color',
					'css' => [ 'selector' => '.vcex-teaser-heading', 'property' => 'color' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'heading_weight',
					'css' => [ 'selector' => '.vcex-teaser-heading', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'text_transform',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'heading_transform',
					'css' => [ 'selector' => '.vcex-teaser-heading', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'heading_letter_spacing',
					'choices' => 'letter_spacing',
					'css' => [ 'selector' => '.vcex-teaser-heading', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'heading_line_height',
					'choices' => 'line_height',
					'css' => [ 'selector' => '.vcex-teaser-heading', 'property' => 'line-height' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'heading_margin',
					'description' => self::param_description( 'margin' ),
					'css' => [ 'selector' => '.vcex-teaser-heading', 'property' => 'margin' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'heading_typo',
					'selector' => '.vcex-teaser-heading',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Content
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Top Spacing', 'total-theme-core' ),
					'param_name' => 'content_top_margin',
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'content_font_size',
					'css' => [ 'selector' => '.vcex-teaser-text', 'property' => 'font-size' ],
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'content_font_weight',
					'css' => [ 'selector' => '.vcex-teaser-text', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'content_background',
					'css' => [ 'selector' => '.vcex-teaser-content', 'property' => 'background' ],
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'content_color',
					'css' => [ 'selector' => '.vcex-teaser-text', 'property' => 'color' ],
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'content_typo',
					'selector' => '.vcex-teaser-text',
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'content_margin',
					'css' => [ 'selector' => '.vcex-teaser-content', 'property' => 'margin' ],
					'description' => self::param_description( 'margin' ),
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'content_padding',
					'css' => [ 'selector' => '.vcex-teaser-content', 'property' => 'padding' ],
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				],
				// Media
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Source', 'total-theme-core' ),
					'param_name' => 'image_source',
					'std' => 'media_library',
					'value' => [
						esc_html__( 'Media Library', 'total-theme-core' ) => 'media_library',
						esc_html__( 'Featured Image', 'total-theme-core' ) => 'featured',
						esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
						esc_html__( 'External', 'total-theme-core' ) => 'external',
					],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'attach_image',
					'heading' => esc_html__( 'Image', 'total-theme-core' ),
					'param_name' => 'image',
					'dependency' => [ 'element' => 'image_source', 'value' => 'media_library' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'External Image', 'total-theme-core' ),
					'param_name' => 'external_image',
					'dependency' => [ 'element' => 'image_source', 'value' => 'external' ],
					'description' => self::param_description( 'text' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'image',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name' => 'image_custom_field',
					'dependency' => [ 'element' => 'image_source', 'value' => 'custom_field' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Alt Attribute', 'total-theme-core' ),
					'param_name' => 'image_alt',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Image Style', 'total-theme-core' ),
					'param_name' => 'img_style',
					'std' => '',
					'choices' => [
						'' => esc_html__( 'Auto', 'total-theme-core' ),
						'stretch' => esc_html__( 'Stretch', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'img_align',
					'std' => '',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Lazy Load', 'total-theme-core' ),
					'param_name' => 'img_lazy_load',
					'std' => 'true',
					'description' => esc_html__( 'Consider disabling if your element is above the fold.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Fetch Priority', 'total-theme-core' ),
					'param_name' => 'img_fetchpriority',
					'choices' => [
						'' => esc_html__( 'Auto', 'total-theme-core' ),
						'low' => esc_html__( 'Low', 'total-theme-core' ),
						'high' => esc_html__( 'High', 'total-theme-core' ),
					],
					'description' => sprintf( esc_html__( 'Set the fetchpriority attribute for your image. %sLearn more from Google\'s web.dev blog%s', 'total-theme-core' ), '<a href="https://web.dev/priority-hints/" target="_blank" rel="noopener noreferrer">', '&#8599;</a>' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_radius',
					'supports_blobs' => true,
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'img_border_radius',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'img_bottom_margin',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'aspect_ratio',
					'heading' => esc_html__( 'Image Aspect Ratio', 'total-theme-core' ),
					'param_name' => 'img_aspect_ratio',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Image Fit', 'total-theme-core' ),
					'param_name' => 'img_object_fit',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'value' => [
						esc_html__( 'Cover', 'total-theme-core' ) => 'cover',
						esc_html__( 'Contain', 'total-theme-core' ) => 'contain',
						esc_html__( 'Fill', 'total-theme-core' ) => 'fill',
						esc_html__( 'Scale Down', 'total-theme-core' ) => 'scale-down',
					],
					'dependency' => [ 'element' => 'img_aspect_ratio', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'std' => 'wpex_custom',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'image_source', 'value_not_equal_to' => 'external' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_crop',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'dependency' => [ 'element' => 'image_source', 'value_not_equal_to' => 'external' ],
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
					'heading' => esc_html__( 'Image Filter', 'total-theme-core' ),
					'param_name' => 'img_filter',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Hover', 'total-theme-core' ),
					'param_name' => 'img_hover_style',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Video
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Video link', 'total-theme-core' ),
					'param_name' => 'video',
					'description' => esc_html__( 'Enter in a video URL that is compatible with WordPress\'s built-in oEmbed feature.', 'total-theme-core' ),
					'group' => esc_html__( 'Video', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Onclick
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Link Type', 'total-theme-core' ),
					'param_name' => 'onclick_el',
					'choices' => [
						'' => esc_html__( 'Default', 'total-theme-core' ),
						'container' => esc_html__( 'Whole Container', 'total-theme-core' ),
					],
					'description' => esc_html__( 'By default the link is applied to the image, heading and button seperately.', 'total-theme-core' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Link Type', 'total-theme-core' ),
					'param_name' => 'onclick',
					'value' => [
						esc_html__( 'None', 'total-theme-core' ) => '',
						esc_html__( 'Custom Link', 'total-theme-core' ) => 'custom_link',
						esc_html__( 'Internal Page', 'total-theme-core' ) => 'internal_link',
						esc_html__( 'Current Post', 'total-theme-core' ) => 'post_permalink',
						esc_html__( 'Scroll to Section', 'total-theme-core' ) => 'local_scroll',
						esc_html__( 'Toggle Element', 'total-theme-core' ) => 'toggle_element',
						esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
						esc_html__( 'Callback Function', 'total-theme-core' ) => 'callback_function',
						esc_html__( 'Inline Content or iFrame Popup', 'total-theme-core' ) => 'popup',
						esc_html__( 'Image lightbox', 'total-theme-core' ) => 'lightbox_image',
						esc_html__( 'Image Gallery Lightbox', 'total-theme-core' ) => 'lightbox_gallery',
						esc_html__( 'Post Image Gallery Lightbox', 'total-theme-core' ) => 'lightbox_post_gallery',
						esc_html__( 'Video Lightbox', 'total-theme-core' ) => 'lightbox_video',
						esc_html__( 'Post Video Lightbox', 'total-theme-core' ) => 'lightbox_post_video',
					],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Link', 'total-theme-core' ),
					'param_name' => 'onclick_url',
					'description' => self::param_description( 'text' ),
					'dependency' => [
						'element' => 'onclick',
						'value' => [
							'custom_link',
							'local_scroll',
							'popup',
							'lightbox_image',
							'lightbox_video',
							'toggle_element',
						],
					],
					'description' => esc_html__( 'Enter your custom link url, lightbox url or local/toggle element ID (including a # at the front).', 'total-theme-core' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vc_link',
					'heading' => esc_html__( 'Internal Link', 'total-theme-core' ),
					'param_name' => 'onclick_internal_link',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'description' => esc_html__( 'This setting is used only if you want to link to an internal page to make it easier to find and select it. Any extra settings in the popup (title, target, nofollow) are ignored.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'onclick', 'value' => 'internal_link' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'link',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name' => 'onclick_custom_field',
					'dependency' => [ 'element' => 'onclick', 'value' => 'custom_field' ],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_callback_function',
					'heading' => esc_html__( 'Callback Function', 'total-theme-core' ),
					'param_name' => 'onclick_callback_function',
					'dependency' => [ 'element' => 'onclick', 'value' => 'callback_function' ],
					'description' => sprintf( esc_html__( 'Callback functions must be %swhitelisted%s for security reasons.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', '</a>' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'attach_image',
					'heading' => esc_html__( 'Lightbox Image', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_image',
					'dependency' => [ 'element' => 'onclick', 'value' => 'lightbox_image' ],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'attach_images',
					'heading' => esc_html__( 'Lightbox Gallery', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_gallery',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => [ 'element' => 'onclick', 'value' => 'lightbox_gallery' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Title Attribute', 'total-theme-core' ),
					'param_name' => 'onclick_title',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => [ 'element' => 'onclick', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Target', 'total-theme-core' ),
					'param_name' => 'onclick_target',
					'std' => 'self',
					'choices' => [
						'self'   => esc_html__( 'Self', 'total-theme-core' ),
						'_blank' => esc_html__( 'Blank', 'total-theme-core' ),
					],
					'dependency' => [
						'element' => 'onclick',
						'value' => [ 'custom_link', 'internal_link', 'custom_field', 'callback_function', 'post_permalink' ],
					],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Rel Attribute', 'total-theme-core' ),
					'param_name' => 'onclick_rel',
					'std' => '',
					'choices' => [
						'' => esc_html__( 'None', 'total-theme-core' ),
						'nofollow' => esc_html__( 'Nofollow', 'total-theme-core' ),
						'sponsored' => esc_html__( 'Sponsored', 'total-theme-core' ),
					],
					'dependency' => [
						'element' => 'onclick',
						'value' => [ 'custom_link', 'internal_link', 'custom_field', 'callback_function' ],
					],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Video Overlay Icon?', 'total-theme-core' ),
					'param_name' => 'onclick_video_overlay_icon',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'std' => 'false',
					'description' => esc_html__( 'More options available under Style > Image Overlay.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'onclick', 'value' => 'lightbox_video' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Lightbox Dimensions (optional)', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_dims',
					'description' => esc_html__( 'Enter a custom width and height for your lightbox pop-up window. Use format widthxheight. Example: 1920x1080.', 'total-theme-core' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => [ 'element' => 'onclick', 'value' => [ 'lightbox_video', 'popup' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Lightbox Title', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_title',
					'dependency' => [
						'element' => 'onclick',
						'value' => [ 'lightbox_image', 'lightbox_video', 'popup' ],
					],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textarea',
					'heading' => esc_html__( 'Lightbox Caption', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_caption',
					'dependency' => [
						'element' => 'onclick',
						'value' => [ 'lightbox_image', 'lightbox_video', 'popup' ],
					],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'exploded_textarea',
					'heading' => esc_html__( 'Custom Data Attributes', 'total-theme-core' ),
					'param_name' => 'onclick_data_attributes',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => [
						'element' => 'onclick',
						'value' => [
							'custom_link',
							'custom_field',
							'callback_function',
							'popup',
							'toggle_element',
						],
					],
					'description' => esc_html__( 'Enter your custom data attributes in the format of data|value. Hit enter after each set of data attributes.', 'total-theme-core' ),
				],
				// Button.
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Show Button?', 'total-theme-core' ),
					'param_name' => 'show_button',
					'std' => 'false',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'onclick', 'not_empty' => true ],
					'description' => esc_html__( 'Note: If you use the Grid Container to display multiple icon boxes the buttons will be bottom aligned.', 'total-theme-core' ),
					'elementor' => [
						'condition' => '', // for some reason checking the URL field fails in Elementor.
						'description' => '',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Text', 'total-theme-core' ),
					'param_name' => 'button_text',
					'placeholder' => esc_html__( 'Learn more', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => [ 'true' ] ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'button_font_size',
					'css' => [ 'selector' => '.vcex-teaser-button', 'property' => 'font-size' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => [ 'true' ] ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'button_typo',
					'selector' => '.vcex-teaser-button',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => [ 'true' ] ],
					'editors' => [ 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'button_border_radius',
					'choices' => 'border_radius',
					'css' => [ 'selector' => '.vcex-teaser-button', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => [ 'true' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'button_padding',
					'css' => [ 'selector' => '.vcex-teaser-button', 'property' => 'padding' ],
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => [ 'true' ] ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'button_background',
					'css' => [ 'selector' => '.vcex-teaser-button', 'property' => 'background' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => [ 'true' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'button_color',
					'css' => [ 'selector' => '.vcex-teaser-button', 'property' => 'color' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => [ 'true' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'button_hover_background',
					'css' => [ 'selector' => '.vcex-teaser-button:hover', 'property' => 'background' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => [ 'true' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'button_hover_color',
					'css' => [ 'selector' => '.vcex-teaser-button:hover', 'property' => 'color' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_button', 'value' => [ 'true' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				[ 'type' => 'hidden', 'param_name' => 'url' ],
				[ 'type' => 'hidden', 'param_name' => 'url_title' ],
				[ 'type' => 'hidden', 'param_name' => 'url_target' ],
				[ 'type' => 'hidden', 'param_name' => 'url_local_scroll' ],
			];
		}

		/**
		 * Parse WPBakery attributes on edit.
		 */
		public static function vc_edit_form_fields_attributes( $atts = [] ) {
			return self::parse_deprecated_attributes( $atts );
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}
			/**
			 * Conver old URL param to new onclick param.
			 *
			 * @note these settings must convert old Elementor URL param as well.
			 */
			if ( empty( $atts['onclick'] ) && ! empty( $atts['url'] ) ) {
				$elementor_link = is_array( $atts['url'] );
				if ( $elementor_link ) {
					$link_parsed = $atts['url'];
					$atts['onclick'] = 'custom_link';
					$atts['onclick_url'] = $link_parsed['url'] ?? '';
				} else {
					$link = (string) $atts['url'];
					if ( str_contains( $link, 'url:' ) ) {
						$link_parsed = vcex_build_link( $link );
					}
				}
				if ( ! empty( $atts['url_local_scroll'] ) ) {
					$atts['onclick'] = 'local_scroll';
					$atts['onclick_url'] = $link_parsed['url'] ?? $link;
					unset( $atts['url_local_scroll'] );
				} else {
					if ( isset( $link_parsed ) ) {
						if ( ! $elementor_link ) {
							$atts['onclick'] = 'internal_link';
							$atts['onclick_internal_link'] = $link;
						}
						if ( isset( $link_parsed['title'] ) ) {
							$atts['onclick_title'] = $link_parsed['title'];
						}
						$target = $link_parsed['target'] ??  $link_parsed['is_external'] ?? '';
						if ( '_blank' === $target || 'on' === $target ) {
							$atts['onclick_target'] = '_blank';
						}
						if ( isset( $link_parsed['rel'] ) && in_array( $link_parsed['rel'], [ 'nofollow', 'sponsored' ], true ) ) {
							$atts['onclick_rel'] = $link_parsed['rel'];
						}
					} else {
						$atts['onclick'] = 'custom_link';
						$atts['onclick_url'] = $link;
					}
				}
				if ( ! empty( $atts['url_title'] ) && empty( $atts['onclick_title'] ) ) {
					$atts['onclick_title'] = $atts['url_title'];
				}
				if ( ! empty( $atts['url_target'] ) && empty( $atts['onclick_target'] ) ) {
					$atts['onclick_target'] = $atts['url_target'];
				}
				unset( $atts['url'] );
			}
			return $atts;
		}

	}

}

new VCEX_Teaser_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Teaser' ) ) {
	class WPBakeryShortCode_Vcex_Teaser extends WPBakeryShortCode {}
}
