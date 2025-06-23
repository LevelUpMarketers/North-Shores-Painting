<?php

defined( 'ABSPATH' ) || exit;

/**
 * Image Shortcode.
 */
if ( ! class_exists( 'VCEX_Image_Shortcode' ) ) {

	class VCEX_Image_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_image';

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
			return esc_html__( 'Image', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Single Image', 'total-theme-core' );
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
			if ( function_exists( 'totaltheme_call_static' ) && totaltheme_call_static( 'Dark_Mode', 'is_enabled' ) ) {
				$dark_mode = [
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Dark Mode Support', 'total-theme-core' ),
					'param_name' => 'dark_mode_check',
					'std' => 'false',
					'description' => esc_html__( 'If you want to provide an alternative image for dark mode, upload the new image with the same name and a -dark suffix. For example, if your original image is named logo.webp, your dark mode image should be named logo-dark.webp.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				];
			} else {
				$dark_mode = '';
			}
			return [
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Source', 'total-theme-core' ),
					'param_name' => 'source',
					'std' => 'media_library',
					'value' => self::get_source_choices(),
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'External Image', 'total-theme-core' ),
					'param_name' => 'external_image',
					'description' => self::param_description( 'text' ),
					'dependency' => [ 'element' => 'source', 'value' => 'external' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'image',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name' => 'custom_field_name',
					'dependency' => [ 'element' => 'source', 'value' => 'custom_field' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_callback_function',
					'heading' => esc_html__( 'Callback Function', 'total-theme-core' ),
					'param_name' => 'callback_function',
					'dependency' => [ 'element' => 'source', 'value' => 'callback_function' ],
					'description' => sprintf( esc_html__( 'Callback functions must be %swhitelisted%s for security reasons.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', '</a>' ),
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'attach_image',
					'heading' => esc_html__( 'Image', 'total-theme-core' ),
					'description' =>  esc_html__( 'When selecting a source that is not "Media Library" this image will be used as the fallback.', 'total-theme-core' ),
					'param_name' => 'image_id',
					'dependency' => [ 'element' => 'source', 'value_not_equal_to' => [ 'external' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Fetch Priority', 'total-theme-core' ),
					'param_name' => 'fetchpriority',
					'choices' => [
						'' => esc_html__( 'Auto', 'total-theme-core' ),
						'low' => esc_html__( 'Low', 'total-theme-core' ),
						'high' => esc_html__( 'High', 'total-theme-core' ),
					],
					'description' => esc_html__( 'Set the fetchpriority attribute for your image.', 'total-theme-core' ) . ' <a href="https://web.dev/priority-hints/" target="_blank" rel="noopener noreferrer">' . esc_html( 'Learn more from Google\'s web.dev blog') . '</a>',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				$dark_mode,
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Lazy Load', 'total-theme-core' ),
					'param_name' => 'lazy_load',
					'std' => 'true',
					'description' => esc_html__( 'Consider disabling if your element is above the fold.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Caption', 'total-theme-core' ),
					'param_name' => 'caption',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Alt Attribute', 'total-theme-core' ),
					'param_name' => 'alt_attr',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Title', 'total-theme-core' ),
					'param_name' => 'img_title',
					'description' => esc_html__( 'Used for image overlay styles.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Caption', 'total-theme-core' ),
					'param_name' => 'img_caption',
					'description' => esc_html__( 'Used when enabling the image caption or when using image overlay styles that display excerpts.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
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
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => '', // uses text align so it should inherit.
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'supports_blobs' => true,
					'css' => [
						'selector' => [ '.vcex-image-img', '.overlay-parent' ],
						'property' => 'border-radius',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'padding_all',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Inner Background', 'total-theme-core' ),
					'param_name' => 'inner_bg',
					'dependency' => [ 'element' => 'padding_all', 'not_empty' => true ],
					'css' => [ 'selector' => '.vcex-image-img', 'property' => 'background-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'border_style',
					'dependency' => [ 'element' => 'border_width', 'not_empty' => true ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'css' => [ 'selector' => '.vcex-image-img', 'property' => 'border-color' ],
					'dependency' => [ 'element' => 'border_width', 'not_empty' => true ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Mix Blend Mode', 'total-theme-core' ),
					'description' => esc_html__( 'Sets how the element should blend with the content of the element\'s parent and the element\'s background.', 'total-theme-core' ),
					'param_name' => 'mix_blend_mode',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Filter', 'total-theme-core' ),
					'param_name' => 'img_filter',
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
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Overlay', 'total-theme-core' ),
					'param_name' => 'overlay_style',
					'std' => 'none',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'exclude_choices' => [ 'thumb-swap', 'thumb-swap-title' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Excerpt Length', 'total-theme-core' ),
					'param_name' => 'overlay_excerpt_length',
					'value' => '15',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'overlay_style', 'value' => 'title-excerpt-hover' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Overlay Button Text', 'total-theme-core' ),
					'param_name' => 'overlay_button_text',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'overlay_style', 'value' => 'hover-button' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Hover', 'total-theme-core' ),
					'description' => esc_html__( 'Note: Disabled when the image "Filter" is enabled.', 'total-theme-core' ),
					'param_name' => 'img_hover_style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'hover_animation', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Dimensions.
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Fill Column', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to make the element fill the parent column. If other elements are added to the same column, it will fill the remaining space.', 'total-theme-core' ),
					'param_name' => 'fill_column',
					'group' => esc_html__( 'Dimensions', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Aspect Ratio', 'total-theme-core' ),
					'param_name' => 'aspect_ratio',
					'group' => esc_html__( 'Dimensions', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'width',
					'css' => [
						'selector' => '.vcex-image-inner',
						'property' => 'max-width',
					],
					'description' => esc_html__( 'Constrain your image to a specific width without having to crop it. Can also be used to force a specific width on an SVG image. Enter 100% to stretch your image to fill the parent container.', 'total-theme-core' ),
					'group' => esc_html__( 'Dimensions', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Height', 'total-theme-core' ),
					'param_name' => 'height',
					'css' => [
						'selector' => '.vcex-image-img',
						'property' => 'height',
					],
					'description' => esc_html__( 'Force your image to display at a specific height without having to crop it.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'aspect_ratio', 'is_empty' => true ],
					'group' => esc_html__( 'Dimensions', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Image Fit', 'total-theme-core' ),
					'description' => esc_html__( 'This setting is used when selecting an aspect ratio or using a custom image height.', 'total-theme-core' ),
					'param_name' => 'object_fit',
					'group' => esc_html__( 'Dimensions', 'total-theme-core' ),
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
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Image Position', 'total-theme-core' ),
					'description' => esc_html__( 'This setting is used when selecting an aspect ratio or using a custom image height.', 'total-theme-core' ),
					'param_name' => 'object_position',
					'group' => esc_html__( 'Dimensions', 'total-theme-core' ),
					'placeholder' => 'center center',
					'css' => [
						'selector' => '.vcex-image-img',
						'property' => 'object-position',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Image Crop.
				[
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'std' => 'wpex_custom',
					'group' => esc_html__( 'Crop', 'total-theme-core' ),
					'description' => esc_html__( 'Note: For security reasons custom cropping only works on images hosted on your own server in the WordPress uploads folder. If you are using an external image it will display in full.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_crop',
					'group' => esc_html__( 'Crop', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'img_width',
					'group' => esc_html__( 'Crop', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'param_name' => 'img_height',
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'group' => esc_html__( 'Crop', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Onclick
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Link Type', 'total-theme-core' ),
					'param_name' => 'onclick',
					'value' => [
						esc_html__( 'None', 'total-theme-core' ) => '',
						esc_html__( 'Custom Link', 'total-theme-core' ) => 'custom_link',
						esc_html__( 'Internal Page', 'total-theme-core' ) => 'internal_link',
						esc_html__( 'Homepage', 'total-theme-core' ) => 'home',
						esc_html__( 'Current Post', 'total-theme-core' ) => 'post_permalink',
						esc_html__( 'Scroll to Section', 'total-theme-core' ) => 'local_scroll',
						esc_html__( 'Toggle Element', 'total-theme-core' ) => 'toggle_element',
						esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
						esc_html__( 'Callback Function', 'total-theme-core' ) => 'callback_function',
						esc_html__( 'Inline Content or iFrame Popup', 'total-theme-core' ) => 'popup',
						esc_html__( 'Image File', 'total-theme-core' ) => 'image',
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
						'value' => [ 'custom_link', 'internal_link', 'custom_field', 'callback_function', 'post_permalink', 'home', 'image' ],
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
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Outer Background', 'total-theme-core' ),
					'param_name' => 'background_color',
					'css' => true,
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Deprecated params.
				[ 'type' => 'hidden', 'param_name' => 'link' ],
				[ 'type' => 'hidden', 'param_name' => 'link_local_scroll' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_post_gallery' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_url' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_type' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_dimensions' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_custom_img' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_title' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_gallery' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_caption' ],
				[ 'type' => 'hidden', 'param_name' => 'lightbox_video_overlay_icon' ],
			];
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}

			// Convert to onclick method.
			if ( empty( $atts['onclick'] ) ) {

				if ( isset( $atts['lightbox'] ) && vcex_validate_att_boolean( 'lightbox', $atts ) ) {

					if ( ! empty( $atts['lightbox_type'] ) ) {
						switch ( $atts['lightbox_type'] ) {
							case 'iframe':
							case 'url':
							case 'inline':
								$atts['onclick'] = 'popup';
								break;
							case 'html5':
							case 'video':
								$atts['onclick'] = 'lightbox_video';
								break;
							case 'image':
							default:
								$atts['onclick'] = 'lightbox_image';
								break;
						}
					} else {
						$atts['onclick'] = 'lightbox_image';
					}

					if ( isset( $atts['lightbox_post_gallery'] )
						&& vcex_validate_att_boolean( 'lightbox_post_gallery', $atts )
					) {
						$atts['onclick'] = 'lightbox_post_gallery';
						unset( $atts['lightbox_post_gallery'] );
					} elseif ( ! empty( $atts['lightbox_gallery'] ) ) {
						$atts['onclick'] = 'lightbox_gallery';
					} elseif ( ! empty( $atts['lightbox_custom_img'] ) ) {
						$atts['onclick'] = 'lightbox_image';
					}

					if ( ! empty( $atts['lightbox_url'] ) ) {
						if ( empty( $atts['onclick_url'] ) ) {
							$atts['onclick_url'] = $atts['lightbox_url'];
						}
						if ( empty( $atts['onclick'] ) ) {
							if ( is_string( $atts['onclick'] )
								&& ( str_contains( $atts['lightbox_url'], 'youtu' )
									|| str_contains( $atts['lightbox_url'], 'vimeo' )
								)
							) {
								$atts['onclick'] = 'lightbox_video';
							} else {
								$atts['onclick'] = 'lightbox_image';
							}
						}
						unset( $atts['lightbox_url'] );
					}

					unset( $atts['lightbox'] );

				} else {

					if ( ! empty( $atts['link'] ) ) {

						$link = vcex_build_link( $atts['link'] );

						if ( ! empty( $link['url'] ) && empty( $atts['onclick_url'] ) ) {
							$atts['onclick_url'] = $link[ 'url' ];
						}

						if ( ! empty( $link['title'] ) ) {
							$atts['onclick_title'] = $link[ 'title' ];
						}

						if ( ! empty( $link['target'] ) ) {
							$atts['onclick_target'] = $link[ 'target' ];
						}

						if ( ! empty( $link['rel'] ) ) {
							$atts['onclick_rel'] = $link[ 'rel' ];
						}

						$atts['onclick'] = 'custom_link';

						unset( $atts['link'] );

					}
					if ( isset( $atts['link_local_scroll'] ) && 'true' === $atts['link_local_scroll' ] ) {
						$atts['onclick'] = 'local_scroll';
						$atts['onclick_target'] = 'self';
					}

				}

			}

			if ( ! empty( $atts['lightbox_dimensions'] ) ) {
				$atts['onclick_lightbox_dims'] = $atts['lightbox_dimensions'];
				unset( $atts['lightbox_dimensions'] );
			}

			if ( ! empty( $atts['lightbox_custom_img'] ) ) {
				$atts['onclick_lightbox_image'] = $atts['lightbox_custom_img'];
				unset( $atts['lightbox_custom_img'] );
			}

			if ( ! empty( $atts['lightbox_title'] ) ) {
				$atts['onclick_lightbox_title'] = $atts['lightbox_title'];
				unset( $atts['lightbox_title'] );
			}

			if ( ! empty( $atts['lightbox_gallery'] ) ) {
				$atts['onclick_lightbox_gallery'] = $atts['lightbox_gallery'];
				unset( $atts['lightbox_gallery'] );
			}

			if ( ! empty( $atts['lightbox_caption'] ) ) {
				$atts['onclick_lightbox_caption'] = $atts['lightbox_caption'];
				unset( $atts['lightbox_caption'] );
			}

			if ( ! empty( $atts['lightbox_video_overlay_icon'] ) ) {
				$atts['onclick_video_overlay_icon'] = $atts['lightbox_video_overlay_icon'];
				unset( $atts['lightbox_video_overlay_icon'] );
			}

			return $atts;
		}

		/**
		 * Returns the options for the source field.
		 */
		protected static function get_source_choices(): array {
			$choices = [
				esc_html__( 'Media Library', 'total-theme-core' ) => 'media_library',
				esc_html__( 'External', 'total-theme-core' ) => 'external',
				esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
				esc_html__( 'Post Featured Image', 'total-theme-core' ) => 'featured',
				esc_html__( 'Post Secondary Image', 'total-theme-core' ) => 'secondary_thumbnail',
				esc_html__( 'Post Series Thumbnail', 'total-theme-core' ) => 'post_series_thumbnail',
				esc_html__( 'Post Primary Term (Category) Image', 'total-theme-core' ) => 'primary_term_thumbnail',
				esc_html__( 'Post Author Avatar', 'total-theme-core' ) => 'author_avatar',
				esc_html__( 'Taxonomy Term Image', 'total-theme-core' ) => 'term_thumbnail',
				esc_html__( 'Current User Avatar', 'total-theme-core' ) => 'user_avatar',
				esc_html__( 'Callback Function', 'total-theme-core' ) => 'callback_function',
			];
			if ( totalthemecore_call_static( 'Cards\Meta', 'is_enabled' ) ) {
				$choices[ esc_html__( 'Card Thumbnail', 'total-theme-core' ) ] = 'card_thumbnail';
			}
			return $choices;
		}

	}

}

new VCEX_Image_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Image' ) ) {
	class WPBakeryShortCode_Vcex_Image extends WPBakeryShortCode {}
}
