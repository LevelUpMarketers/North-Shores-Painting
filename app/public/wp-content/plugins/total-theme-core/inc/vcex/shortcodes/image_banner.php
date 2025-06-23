<?php

defined( 'ABSPATH' ) || exit;

/**
 * Image Banner Shortcode.
 */
if ( ! class_exists( 'Vcex_Image_Banner_Shortcode' ) ) {

	class Vcex_Image_Banner_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_image_banner';

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
			return esc_html__( 'Image Banner', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Image Banner with overlay text and animation', 'total-theme-core' );
		}

		/**
		 * Returns custom vc map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			return [
				'admin_enqueue_js' => 'image',
				'js_view'          => 'vcexBackendViewImage',
			];
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'el_class',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core' ),
					'param_name' => 'animation_duration',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
					'css' => true,
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core' ),
					'param_name' => 'animation_delay',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
					'css' => true,
				],
				// Style
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'supports_blobs' => true,
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
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Fill Column', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to make the element fill the parent column. If other elements are added to the same column, it will fill the remaining space.', 'total-theme-core' ),
					'param_name' => 'fill_column',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Minimum Height', 'total-theme-core' ),
					'param_name' => 'min_height',
					'description' => self::param_description( 'height' ),
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Content or Image Placement', 'total-theme-core' ),
					'param_name' => 'justify_content',
					'std' => 'center',
					'value' => [
						esc_html__( 'Top', 'total-theme-core' ) => 'start',
						esc_html__( 'Center', 'total-theme-core' ) => 'center',
						esc_html__( 'Bottom', 'total-theme-core' ) => 'end',
					],
					'description' => esc_html__( 'Select your content placement when adding a Minimum Height. Important: If you enable the "Use Image Tag" option under the Image tab this setting will alter your image placement otherwise it alters your content placement. When enabling the "Cover Image" option this setting won\'t do anything.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'min_height', 'not_empty' => true ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Content Placement', 'total-theme-core' ),
					'param_name' => 'flex_align', // @todo would be good to rename to "content_align_items".
					'std' => 'center',
					'value' => [
						esc_html__( 'Top', 'total-theme-core' ) => 'start',
						esc_html__( 'Center', 'total-theme-core' ) => 'center',
						esc_html__( 'Bottom', 'total-theme-core' ) => 'end',
					],
					'dependency' => [ 'element' => 'use_img_tag', 'value' => "true" ],
					'description' => esc_html__( 'Select your content placement. This is used when setting a Minimum Height or using the "Use Image Tag" option under the Image tab.', 'total-theme-core' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'width',
					'css' => true,
					'description' => self::param_description( 'width' ),
					'value' => '', // @todo can we remove this? why is it here?
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Module Align', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'center',
					'dependency' => [ 'element' => 'width', 'not_empty' => true ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Content Align', 'total-theme-core' ),
					'param_name' => 'content_align',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'text_align',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Inner Padding', 'total-theme-core' ),
					'description' => self::param_description( 'padding' ),
					'css' => [ 'selector' => '.vcex-ib-content-wrap', 'property' => 'padding' ],
					'param_name' => 'padding',
					'elementor' => [
						'description' => '',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Content Background', 'total-theme-core' ),
					'param_name' => 'content_bg',
					'css' => [ 'selector' => '.vcex-ib-content', 'property' => 'background-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Content Max Width', 'total-theme-core' ),
					'param_name' => 'content_width',
					'css' => [ 'selector' => '.vcex-ib-content', 'property' => 'max-width' ],
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Content Padding', 'total-theme-core' ),
					'description' => self::param_description( 'padding' ),
					'css' => [ 'selector' => '.vcex-ib-content', 'property' => 'padding' ],
					'param_name' => 'content_padding',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'elementor' => [
						'description' => '',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Image
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Image Source', 'total-theme-core' ),
					'param_name' => 'image_source',
					'std' => 'media_library',
					'value' => [
						esc_html__( 'Media Library', 'total-theme-core' ) => 'media_library',
						esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
						esc_html__( 'Featured Image', 'total-theme-core' ) => 'featured',
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
					'heading' => esc_html__( 'External Image', 'total-theme-core' ),
					'param_name' => 'external_image',
					'description' => self::param_description( 'text' ),
					'dependency' => [ 'element' => 'image_source', 'value' => 'external' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Background Position', 'total-theme-core' ),
					'param_name' => 'image_position',
					'description' => esc_html__( 'Enter your custom background position. Example: "center center"', 'total-theme-core' ),
					'dependency' => [ 'element' => 'use_img_tag', 'value_not_equal_to' => 'true' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Use Image Tag', 'total-theme-core' ),
					'param_name' => 'use_img_tag',
					'description' => esc_html__( 'This will make your image display as a standard image via the html img tag instead of an absolutely positioned background image which may render better responsively in certain situations. However, this also limits the content area to the size of your image so your content may not exceed the height of your image at any given screen size unless you apply a Minimum Height via the General tab.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Cover Image', 'total-theme-core' ),
					'param_name' => 'img_cover',
					'description' => esc_html__( 'Enable to stretch your image. This setting should be used in conjuction with the Minimum Height option in the Style tab.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'use_img_tag', 'value' => 'true' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Aspect Ratio', 'total-theme-core' ),
					'param_name' => 'img_aspect_ratio',
					'choices' => 'aspect_ratio',
					'dependency' => [ 'element' => 'use_img_tag', 'value' => 'true' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
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
				// Overlay
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Overlay', 'total-theme-core' ),
					'param_name' => 'overlay',
					'group' => esc_html__( 'Overlay', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Overlay Mix Blend Mode', 'total-theme-core' ),
					'description' => esc_html__( 'Sets how the element should blend with the content of the element\'s parent and the element\'s background.', 'total-theme-core' ),
					'param_name' => 'overlay_blend',
					'choices' => 'mix_blend_mode',
					'group' => esc_html__( 'Overlay', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Overlay Color', 'total-theme-core' ),
					'param_name' => 'overlay_color',
					'css' => [ 'selector' => '.vcex-ib-overlay-bg', 'property' => 'background-color' ],
					'group' => esc_html__( 'Overlay', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Overlay Opacity', 'total-theme-core' ),
					'param_name' => 'overlay_opacity',
					'choices' => 'opacity',
					'css' => [ 'selector' => '.vcex-ib-overlay-bg', 'property' => 'opacity' ],
					'group' => esc_html__( 'Overlay', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Border
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Inner Border', 'total-theme-core' ),
					'param_name' => 'inner_border',
					'group' => esc_html__( 'Border', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'inner_border_style',
					'group' => esc_html__( 'Border', 'total-theme-core' ),
					'value' => [
						esc_html__( 'Solid', 'total-theme-core' ) => 'solid',
						esc_html__( 'Dashed', 'total-theme-core' ) => 'dashed',
						esc_html__( 'Dotted', 'total-theme-core' ) => 'dotted',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'inner_border_width',
					'group' => esc_html__( 'Border', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-ib-border', 'property' => 'border-width' ],
					'description' => self::param_description( 'px' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'inner_border_color',
					'css' => [ 'selector' => '.vcex-ib-border', 'property' => 'border-color' ],
					'group' => esc_html__( 'Border', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_radius',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'inner_border_radius',
					'supports_blobs' => true,
					'group' => esc_html__( 'Border', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'inner_border_margin',
					'group' => esc_html__( 'Border', 'total-theme-core' ),
					'choices' => 'margin',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Heading
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Heading', 'total-theme-core' ),
					'param_name' => 'heading',
					'description' => self::param_description( 'text' ),
					'value' => esc_html__( 'Add Your Heading', 'total-theme-core' ),
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Bottom Padding', 'total-theme-core' ),
					'param_name' => 'heading_bottom_padding',
					'description' => self::param_description( 'padding' ),
					'css' => [ 'selector' => '.vcex-ib-title', 'property' => 'padding-block-end' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'heading' => esc_html__( 'HTML Tag', 'total-theme-core' ),
					'param_name' => 'heading_tag',
					'std' => 'div',
					'type' => 'vcex_select_buttons',
					'choices' => 'html_tag',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'heading_color',
					'css' => [ 'selector' => '.vcex-ib-title', 'property' => 'color' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'heading_font_family',
					'css' => [ 'selector' => '.vcex-ib-title', 'property' => 'font-family' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'heading_font_size',
					'css' => [ 'selector' => '.vcex-ib-title', 'property' => 'font-size' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'heading_line_height',
					'choices' => 'line_height',
					'css' => [ 'selector' => '.vcex-ib-title', 'property' => 'line-height' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'heading_letter_spacing',
					'choices' => 'letter_spacing',
					'css' => [ 'selector' => '.vcex-ib-title', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'heading_font_weight',
					'css' => [ 'selector' => '.vcex-ib-title', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Italic', 'total-theme-core' ),
					'param_name' => 'heading_italic',
					'css' => [ 'selector' => '.vcex-ib-title', 'property' => 'italic' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'heading_typography',
					'selector' => '.vcex-ib-title',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Caption
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Caption', 'total-theme-core' ),
					'param_name' => 'caption',
					'description' => self::param_description( 'text' ),
					'value' => esc_html__( 'Add your custom caption', 'total-theme-core' ),
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Bottom Padding', 'total-theme-core' ),
					'param_name' => 'caption_bottom_padding',
					'description' => self::param_description( 'padding' ),
					'css' => [ 'selector' => '.vcex-ib-caption', 'property' => 'padding-block-end' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'caption_color',
					'css' => [ 'selector' => '.vcex-ib-caption', 'property' => 'color' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'caption_font_family',
					'css' => [ 'selector' => '.vcex-ib-caption', 'property' => 'font-family' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'caption_font_size',
					'css' => [ 'selector' => '.vcex-ib-caption', 'property' => 'font-size' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'caption_line_height',
					'choices' => 'line_height',
					'css' => [ 'selector' => '.vcex-ib-caption', 'property' => 'line-height' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'caption_letter_spacing',
					'choices' => 'letter_spacing',
					'css' => [ 'selector' => '.vcex-ib-caption', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'caption_font_weight',
					'css' => [ 'selector' => '.vcex-ib-caption', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Italic', 'total-theme-core' ),
					'param_name' => 'caption_italic',
					'css' => [ 'selector' => '.vcex-ib-caption', 'property' => 'italic' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'caption_typography',
					'selector' => '.vcex-ib-caption',
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// OnClick
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
					'std' => 'false',
					'heading' => esc_html__( 'Video Overlay Icon?', 'total-theme-core' ),
					'param_name' => 'onclick_video_overlay_icon',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
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
				// Button
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Button', 'total-theme-core' ),
					'param_name' => 'button',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Apply Link To Entire Element?', 'total-theme-core' ),
					'param_name' => 'link_wrap',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Text', 'total-theme-core' ),
					'param_name' => 'button_text',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'value' => esc_html__( 'learn more', 'total-theme-core' ),
					'description' => self::param_description( 'text' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'button_font_family',
					'css' => [
						'selector' => '.vcex-ib-button-inner',
						'property' => 'font-family',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_button_styles',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'button_style',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_button_colors',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'button_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'button_font_weight',
					'css' => [
						'selector' => '.vcex-ib-button-inner',
						'property' => 'font-weight',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'button_letter_spacing',
					'choices' => 'letter_spacing',
					'css' => [ 'selector' => '.vcex-ib-button-inner', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'button_font_size',
					'css' => [ 'selector' => '.vcex-ib-button-inner', 'property' => 'font-size' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Italic', 'total-theme-core' ),
					'param_name' => 'button_italic',
					'css' => [ 'selector' => '.vcex-ib-button-inner', 'property' => 'italic' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'button_custom_background',
					'css' => [ 'selector' => '.vcex-ib-button-inner', 'property' => 'background' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'button_custom_hover_background',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-ib-button-inner:hover', 'property' => 'background' ],
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'button_custom_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-ib-button-inner', 'property' => 'color' ],
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'button_custom_hover_color',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-ib-button-inner:hover', 'property' => 'color' ],
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Width', 'total-theme-core' ),
					'param_name' => 'button_width',
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'css' => [
						'selector' => '.vcex-ib-button-inner',
						'property' => 'width', // max-width won't work.
					],
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'button_border_radius',
					'choices' => 'border_radius',
					'css' => [ 'selector' => '.vcex-ib-button-inner', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'button_padding',
					'description' => self::param_description( 'padding' ),
					'css' => [ 'selector' => '.vcex-ib-button-inner', 'property' => 'padding' ],
					'elementor' => [
						'description' => '',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'button_typography',
					'selector' => '.vcex-ib-button-inner',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Hover
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Text on Hover', 'total-theme-core' ),
					'param_name' => 'show_on_hover',
					'group' => esc_html__( 'Hover', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Hover Text Animation', 'total-theme-core' ),
					'param_name' => 'show_on_hover_anim',
					'std' => 'fade-up',
					'choices' => [
						'fade-up' => esc_html__( 'Fade Up', 'total-theme-core' ),
						'fade' => esc_html__( 'Fade', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Hover', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_on_hover', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Hover Image Zoom', 'total-theme-core' ),
					'param_name' => 'image_zoom',
					'group' => esc_html__( 'Hover', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Hover Image Zoom Factor', 'total-theme-core' ),
					'param_name' => 'image_zoom_factor',
					'placeholder' => '1.15',
					'css' => [ 'property' => '--vcex-image-banner-zoom' ],
					'description' => esc_html__( 'Value in seconds', 'total-theme-core' ),
					'group' => esc_html__( 'Hover', 'total-theme-core' ),
					'dependency' => [ 'element' => 'image_zoom', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Hover Image Zoom Speed', 'total-theme-core' ),
					'param_name' => 'image_zoom_speed',
					'placeholder' => '0.4',
					'css' => [ 'selector' => '.vcex-ib-img', 'property' => 'transition-duration' ],
					'description' => esc_html__( 'Value in seconds', 'total-theme-core' ),
					'group' => esc_html__( 'Hover', 'total-theme-core' ),
					'dependency' => [ 'element' => 'image_zoom', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Hidden items.
				[ 'type' => 'hidden', 'param_name' => 'link' ],
				[ 'type' => 'hidden', 'param_name' => 'link_local_scroll' ],
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
			if ( ! empty( $atts['border_radius'] ) ) {
				$atts['border_radius'] = \vcex_sanitize_border_radius( $atts['border_radius'] );
			}
			if ( empty( $atts['onclick'] ) && ! empty( $atts['link'] ) ) {
				$link = (string) $atts['link'];
				if ( str_contains( $link, 'url:' ) ) {
					$link_parsed = vcex_build_link( $link );
				}
				if ( ! empty( $atts['link_local_scroll'] ) ) {
					$atts['onclick'] = 'local_scroll';
					$atts['onclick_url'] = $link_parsed['url'] ?? $link;
					unset( $atts['link_local_scroll'] );
				} else {
					if ( isset( $link_parsed ) ) {
						$atts['onclick'] = 'internal_link';
						$atts['onclick_internal_link'] = $link;
						if ( isset( $link_parsed['title'] ) ) {
							$atts['onclick_title'] = sanitize_text_field( $link_parsed['title'] );
						}
						if ( isset( $link_parsed['target'] ) && '_blank' === $link_parsed['target'] ) {
							$atts['onclick_target'] = '_blank';
						}
						if ( isset( $link_parsed['rel'] ) && in_array( $link_parsed['rel'], [ 'nofollow', 'sponsored' ], true ) ) {
							$atts['onclick_rel'] = sanitize_text_field( $link_parsed['rel'] );
						}
					} else {
						$atts['onclick'] = 'custom_link';
						$atts['onclick_url'] = $link;
					}
				}
				unset( $atts['link'] );
			}
			return $atts;
		}

	}

}

new Vcex_Image_Banner_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Image_Banner' ) ) {
	class WPBakeryShortCode_Vcex_Image_Banner extends WPBakeryShortCode {}
}
