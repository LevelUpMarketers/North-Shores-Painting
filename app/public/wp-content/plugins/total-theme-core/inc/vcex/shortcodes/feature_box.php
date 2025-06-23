<?php

defined( 'ABSPATH' ) || exit;

/**
 * Feature Box Shortcode.
 */
if ( ! class_exists( 'VCEX_Feature_Box_Shortcode' ) ) {

	class VCEX_Feature_Box_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_feature_box';

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
			return esc_html__( 'Feature Box', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Side content and image', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
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
					'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.',
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
				// Style
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'std' => 'left-content-right-image',
					'choices' => [
						'left-content-right-image' => esc_html__( 'Right Image', 'total-theme-core' ),
						'left-image-right-content' => esc_html__( 'Left Image', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Vertical Align Center', 'total-theme-core' ),
					'param_name' => 'content_vertical_align',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'css' => true,
					'param_name' => 'text_align',
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
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Gap', 'total-theme-core' ),
					'param_name' => 'gap',
					'css' => true,
					'description' => self::param_description( 'gap' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Stacked Gap', 'total-theme-core' ),
					'param_name' => 'stack_gap',
					'choices' => 'gap',
					'description' => self::param_description( 'gap' ),
					'dependency' => [ 'element' => 'stack_bk', 'value_not_equal_to' => 'false' ],
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
					'type' => 'vcex_select',
					'choices' => 'shadow',
					'heading' => esc_html__( 'Shadow: Hover', 'total-theme-core' ),
					'param_name' => 'shadow_hover',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_hover_animations',
					'heading' => esc_html__( 'Hover Animation', 'total-theme-core'),
					'param_name' => 'hover_animation',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'padding',
					'css' => true,
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Border', 'total-theme-core' ),
					'description' => self::param_description( 'border' ),
					'css' => true,
					'param_name' => 'border',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				// Widths
				[
					'type' => 'vcex_select',
					'choices' => 'justify_content',
					'heading' => esc_html__( 'Justify Content', 'total-theme-core' ),
					'param_name' => 'justify',
					'group' => esc_html__( 'Widths', 'total-theme-core' ),
					'description' => esc_html__( 'Used to adjust the layout when the content and image widths don\'t add up to 100%.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Content Width', 'total-theme-core' ),
					'param_name' => 'content_width',
					'value' => '50%',
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Widths', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Width', 'total-theme-core' ),
					'param_name' => 'media_width',
					'value' => '50%',
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Widths', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Breakpoint', 'total-theme-core' ),
					'param_name' => 'stack_bk',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'sm - 640px', 'total-theme-core' ) => 'sm',
						esc_html__( 'md - 768px', 'total-theme-core' ) => 'md',
						esc_html__( 'lg - 1024px', 'total-theme-core' ) => 'lg',
						esc_html__( 'xl - 1280px', 'total-theme-core' ) => 'xl',
						esc_html__( 'Do not stack', 'total-theme-core' ) => 'false',
						esc_html__( 'Custom', 'total-theme-core' ) => 'custom',
					],
					'description' => esc_html__( 'Select the breakpoint at which point the element will go from a stacked layout to a left/right layout. The default value is 640px (sm) which can be altered by hooking into the "vcex_feature_box_default_breakpoint" filter.', 'total-theme-core' ),
					'group' => esc_html__( 'Widths', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Breakpoint', 'total-theme-core' ),
					'param_name' => 'custom_stack_bk',
					'description' => self::param_description( 'px' ),
					'dependency' => [ 'element' => 'stack_bk', 'value' => 'custom' ],
					'group' => esc_html__( 'Widths', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Heading
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'heading_margin_bottom',
					'choices' => 'margin',
					'css' => [ 'selector' => '.vcex-feature-box-heading', 'property' => 'margin-block-end' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
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
					'choices' => 'html_tag',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'heading_size',
					'css' => [ 'selector' => '.vcex-feature-box-heading', 'property' => 'font-size' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'heading_font_family',
					'css' => [ 'selector' => '.vcex-feature-box-heading', 'property' => 'font-family' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'heading_color',
					'css' => [ 'selector' => '.vcex-feature-box-heading', 'property' => 'color' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'heading_weight',
					'css' => [ 'selector' => '.vcex-feature-box-heading', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'heading_transform',
					'choices' => 'text_transform',
					'css' => [ 'selector' => '.vcex-feature-box-heading', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'heading_letter_spacing',
					'choices' => 'letter_spacing',
					'css' => [ 'selector' => '.vcex-feature-box-heading', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'heading_line_height',
					'choices' => 'line_height',
					'css' => [ 'selector' => '.vcex-feature-box-heading', 'property' => 'line-height' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vc_link',
					'heading' => esc_html__( 'Link (deprecated)', 'total-theme-core' ),
					'param_name' => 'heading_url',
					'dependency' => [ 'element' => 'onclick_el', 'value_not_equal_to' => 'container' ],
					'description' => esc_html__( 'Note: This is an older setting added before the introduction of the "Link" tab.', 'total-theme-core' ),
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Margin (deprecated)', 'total-theme-core' ),
					'param_name' => 'heading_margin',
					'description' => esc_html__( 'This setting is soft deprecated, we recommend instead using the "Bottom Margin" setting at the top of this tab.', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-feature-box-heading', 'property' => 'margin' ],
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'heading_typo',
					'selector' => '.vcex-feature-box-heading',
					'group' => esc_html__( 'Heading', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Content
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'content_font_size',
					'css' => [ 'selector' => '.vcex-feature-box-text', 'property' => 'font-size' ],
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				],
				[
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'content_font_family',
					'css' => [ 'selector' => '.vcex-feature-box-text', 'property' => 'font-family' ],
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'content_color',
					'css' => [ 'selector' => '.vcex-feature-box-text', 'property' => 'color' ],
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'content_font_weight',
					'css' => [ 'selector' => '.vcex-feature-box-text', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'content_background',
					'css' => [ 'selector' => '.vcex-feature-box-content', 'property' => 'background' ],
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Inner Padding', 'total-theme-core' ),
					'param_name' => 'content_padding',
					'css' => [ 'selector' => '.vcex-feature-box-padding-container', 'property' => 'padding' ],
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'content_typo',
					'selector' => '.vcex-feature-box-text',
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Image.
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Source', 'total-theme-core' ),
					'param_name' => 'image_source',
					'std' => 'media_library',
					'value' => [
						esc_html__( 'Media Library', 'total-theme-core' ) => 'media_library',
						esc_html__( 'Featured Image', 'total-theme-core' ) => 'featured',
						esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
					],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'attach_image',
					'heading' => esc_html__( 'Image', 'total-theme-core' ),
					'param_name' => 'image',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'image_source', 'value' => 'media_library' ],
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
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'image_bg',
					'description' => esc_html__( 'Can be used with see through images or the "Mix Blend Mode" setting below.', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-feature-box-image', 'property' => 'background-color' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Stretch Image', 'total-theme-core' ),
					'param_name' => 'equal_heights',
					'description' => esc_html__( 'When enabled the image will strech to fill the column so it will always be at least as tall as the content.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Lazy Load', 'total-theme-core' ),
					'param_name' => 'img_lazy_load',
					'description' => esc_html__( 'Consider disabling if your element is above the fold.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Image Alignment', 'total-theme-core' ),
					'param_name' => 'media_align',
					'description' => esc_html__( 'Note: When stacked the image will be aligned according to your content aligment for consistency.', 'total-theme-core' ),
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
					'description' => esc_html__( 'Set the fetchpriority attribute for your image.', 'total-theme-core' ) . ' <a href="https://web.dev/priority-hints/" target="_blank" rel="noopener noreferrer">' . esc_html( 'Learn more from Google\'s web.dev blog') . '</a>',
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
					'heading' => esc_html__( 'Image Width', 'total-theme-core' ),
					'param_name' => 'img_width',
					'description' => esc_html__( 'Enter a width in pixels.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Height', 'total-theme-core' ),
					'param_name' => 'img_height',
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
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
					'std' => 'cover',
					'value' => [
						esc_html__( 'Cover', 'total-theme-core' ) => 'cover',
						esc_html__( 'Contain', 'total-theme-core' ) => 'contain',
						esc_html__( 'Fill', 'total-theme-core' ) => 'fill',
						esc_html__( 'Scale Down', 'total-theme-core' ) => 'scale-down',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'object_position',
					'heading' => esc_html__( 'Image Position', 'total-theme-core' ),
					'param_name' => 'img_object_position',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'img_border_radius',
					'choices' => 'border_radius',
					'supports_blobs' => true,
					'css' => [ 'selector' => '.vcex-feature-box-image img', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Mix Blend Mode', 'total-theme-core' ),
					'description' => esc_html__( 'Sets how the element should blend with the content of the element\'s parent and the element\'s background.', 'total-theme-core' ),
					'param_name' => 'img_mix_blend_mode',
					'choices' => 'mix_blend_mode',
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
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Filter', 'total-theme-core' ),
					'param_name' => 'img_filter',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vc_link',
					'heading' => esc_html__( 'Image URL', 'total-theme-core' ),
					'param_name' => 'image_url',
					'description' => esc_html__( 'Note: This is an older setting added before the introduction of the "Link" tab.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'onclick_el', 'value_not_equal_to' => 'container' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Lightbox Type', 'total-theme-core' ),
					'param_name' => 'image_lightbox',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'value' => [
						esc_html__( 'None', 'total-theme-core' ) => '',
						esc_html__( 'Auto Detect (Image, Video or Inline)', 'total-theme-core' ) => 'auto-detect',
						esc_html__( 'Self', 'total-theme-core' ) => 'image',
						esc_html__( 'URL', 'total-theme-core' ) => 'url',
						esc_html__( 'Video', 'total-theme-core' ) => 'video_embed',
						esc_html__( 'Inline Content', 'total-theme-core' ) => 'inline',
						esc_html__( 'HTML5', 'total-theme-core' ) => 'html5',
					],
					'dependency' => [ 'element' => 'image_url', 'not_empty' => true ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Lightbox Dimensions', 'total-theme-core' ),
					'param_name' => 'lightbox_dimensions',
					'description' => esc_html__( 'Enter a custom width and height for your lightbox pop-up window. Use format widthxheight. Example: 1920x1080.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => [ 'element' => 'image_lightbox', 'value' => [ 'video_embed', 'url', 'html5', 'iframe' ] ],
				],
				// Video
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Video link', 'total-theme-core' ),
					'param_name' => 'video',
					'description' => esc_html__('Enter a URL that is compatible with WP\'s built-in oEmbed feature or a self-hosted video URL.', 'total-theme-core' ),
					'group' => esc_html__( 'Video', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Link (button)
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Link Type', 'total-theme-core' ),
					'param_name' => 'onclick_el',
					'std' => 'button',
					'choices' => [
						'button' => esc_html__( 'Button', 'total-theme-core' ),
						'container' => esc_html__( 'Whole Container', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Button Text', 'total-theme-core' ),
					'param_name' => 'button_text',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => [ 'element' => 'onclick_el', 'value' => 'button' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Button Class', 'total-theme-core' ),
					'param_name' => 'button_el_class',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'dependency' => [ 'element' => 'onclick_el', 'value' => 'button' ],
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
							'toggle_element'
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
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'description' => sprintf( esc_html__( 'Callback functions must be %swhitelisted%s for security reasons.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', '</a>' ),
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
						'value' => [ 'custom_link', 'internal_link', 'custom_field', 'callback_function' ],
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
					'type' => 'dropdown',
					'heading' => esc_html__( 'Video Icon', 'total-theme-core' ),
					'param_name' => 'video_icon',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'value' => [
						esc_html__( 'None', 'total-theme-core' ) => '',
						esc_html__( 'Style 1', 'total-theme-core' ) => '1',
						esc_html__( 'Style 2', 'total-theme-core' ) => '2',
						esc_html__( 'Style 3', 'total-theme-core' ) => '3',
						esc_html__( 'Style 4', 'total-theme-core' ) => '4',
					],
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
						],
					],
					'description' => esc_html__( 'Enter your custom data attributes in the format of data|value. Hit enter after each set of data attributes.', 'total-theme-core' ),
				],
				// Hidden fields
				[ 'type' => 'hidden', 'param_name' => 'tablet_widths' ],
				[ 'type' => 'hidden', 'param_name' => 'phone_widths' ],
				[ 'type' => 'hidden', 'param_name' => 'image_object_fit' ],
			];
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}

			if ( ! empty( $atts['tablet_widths'] ) ) {
				$stack_bk = 'custom';
				$atts['custom_stack_bk'] = '960px';
				unset( $atts['tablet_widths'] );
			} elseif ( ! empty( $atts['phone_widths'] ) ) {
				$stack_bk = 'md';
				unset( $atts['phone_widths'] );
			}

			if ( isset( $stack_bk ) ) {
				$atts['stack_bk'] = $stack_bk;
			}

			// Rename some settings.
			if ( ! empty( $atts['image_object_fit'] ) ) {
				$atts['img_object_fit'] = $atts['image_object_fit'];
				unset( $atts['image_object_fit'] );
			}

			return $atts;
		}

	}

}

new VCEX_Feature_Box_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Feature_Box' ) ) {
	class WPBakeryShortCode_Vcex_Feature_Box extends WPBakeryShortCode {}
}
