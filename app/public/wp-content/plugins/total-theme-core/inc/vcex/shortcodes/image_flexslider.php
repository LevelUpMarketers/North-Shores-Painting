<?php

defined( 'ABSPATH' ) || exit;

/**
 * Image Slider Shortcode.
 */
if ( ! class_exists( 'VCEX_Image_Flexslider_Shortcode' ) ) {

	class VCEX_Image_Flexslider_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_image_flexslider';

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
			return esc_html__( 'Image Slider', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Custom image slider', 'total-theme-core' );
		}

		/**
		 * Returns list of style dependencies.
		 */
		public static function get_style_depends(): array {
			return (array) totalthemecore_call_static( 'Vcex\Slider\Core', 'get_style_depends' );
		}

		/**
		 * Returns list of script dependencies.
		 */
		public static function get_script_depends(): array {
			return (array) totalthemecore_call_static( 'Vcex\Slider\Core', 'get_script_depends' );
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
			$params = array(
				// Gallery
				array(
					'type' => 'attach_images',
					'heading' => esc_html__( 'Images', 'total-theme-core' ),
					'param_name' => 'image_ids',
					'description' => esc_html__( 'You can display captions by giving your images a caption and you can also display videos by adding an image that has a Video URL defined for it.', 'total-theme-core' ),
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading'  => esc_html__( 'Post Gallery', 'total-theme-core' ),
					'param_name' => 'post_gallery',
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'description' => sprintf( esc_html__( 'Enable to display images from the current post "%sImage Gallery%s".', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/using-post-gallery-image-galleries/" target="_blank" rel="noopener noreferrer">', '</a>' ) . '<br>' . esc_html__( 'You can define images above to display as a fallback in the frontend editor when working with dynamic templates.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_custom_field',
					'choices' => 'gallery',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name'  => 'custom_field_gallery',
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue
					'admin_label' => true,
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'classes',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				vcex_vc_map_add_css_animation(),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				),
				// Slider settings
				array(
					'type'       => 'vcex_subheading',
					'param_name' => 'vcex_subheading__slider',
					'text'       => esc_html__( 'Slider Settings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Animation', 'total-theme-core' ),
					'param_name' => 'animation',
					'std' => 'slide',
					'choices' => array(
						'slide' => esc_html__( 'Slide', 'total-theme-core' ),
						'fade_slides' => esc_html__( 'Fade', 'total-theme-core' ),
					),
					// @note: The "animation" default param causes the element to be hidden on the front-end.
					'elementor' => [ 'name' => 'slide_animation' ],
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Speed', 'total-theme-core' ),
					'param_name' => 'animation_speed',
					'std' => '600',
					'description' => esc_html__( 'Enter a value in milliseconds.', 'total-theme-core' ),
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Auto Height', 'total-theme-core' ),
					'param_name' => 'auto_height',
					'description' => esc_html__( 'If disabled the slider height for all items will be based on the tallest slide.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Auto Height Animation Speed', 'total-theme-core' ),
					'std' => '500',
					'param_name' => 'height_animation',
					'description' => esc_html__( 'You can enter "0.0" to disable the animation completely.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'auto_height', 'value' => 'true' ),
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Placeholder', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to display the first image as a placeholder while the slider script loads. Disable to wait until the slider is fully loaded to render.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'randomize', 'value' => 'false' ),
					'param_name' => 'placeholder',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Randomize', 'total-theme-core' ),
					'param_name' => 'randomize',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Loop', 'total-theme-core' ),
					'param_name' => 'loop',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Touch Swipe on Desktop', 'total-theme-core' ),
					'description' => esc_html__( 'Automatically disabled when sliders have links.', 'total-theme-core' ),
					'param_name' => 'desktop_touch',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Auto Play', 'total-theme-core' ),
					'param_name' => 'slideshow',
					'description' => esc_html__( 'Enable automatic slideshow? Disabled in front-end composer to prevent page "jumping".', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Auto Play Delay', 'total-theme-core' ),
					'param_name' => 'slideshow_speed',
					'std' => '5000',
					'description' => esc_html__( 'Enter a value in milliseconds.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'slideshow', 'value' => 'true' ),
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Auto Play Hover Effect', 'total-theme-core' ),
					'param_name' => 'autoplay_on_hover',
					'std' => 'pause',
					'choices' => array(
						'pause' => esc_html__( 'Pause', 'total-theme-core' ),
						'stop' => esc_html__( 'Stop', 'total-theme-core' ),
						'none' => esc_html__( 'None', 'total-theme-core' ),
					),
					'dependency' => array( 'element' => 'slideshow', 'value' => 'true' ),
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' =>  esc_html__( 'Auto Play Videos', 'total-theme-core' ),
					'param_name' => 'autoplay_videos',
					'dependency' => array( 'element' => 'direction_nav', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Arrows', 'total-theme-core' ),
					'param_name' => 'direction_nav',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Arrows on Hover', 'total-theme-core' ),
					'param_name' => 'direction_nav_hover',
					'dependency' => array( 'element' => 'direction_nav', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Dot Navigation', 'total-theme-core' ),
					'param_name' => 'control_nav',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Pagination Counter', 'total-theme-core' ),
					'param_name' => 'counter',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Image
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Full-Width Images', 'total-theme-core' ),
					'param_name' => 'img_strech',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'aspect_ratio',
					'heading' => esc_html__( 'Image Aspect Ratio', 'total-theme-core' ),
					'param_name' => 'img_aspect_ratio',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'std' => 'wpex_custom',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_crop',
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'img_width',
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'param_name' => 'img_height',
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Thumbnails
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Thumbnails', 'total-theme-core' ),
					'param_name' => 'control_thumbs',
					'group' => esc_html__( 'Thumbnails', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Thumbnail Carousel', 'total-theme-core' ),
					'param_name' => 'control_thumbs_carousel',
					'description' => esc_html__( 'It is recommended to disable this option when not needed. Disabling this setting will give you more control over the thumbnails display.', 'total-theme-core' ),
					'group' => esc_html__( 'Thumbnails', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Thumbnails Pointer', 'total-theme-core' ),
					'param_name' => 'control_thumbs_pointer',
					'dependency' => array( 'element' => 'control_thumbs_carousel', 'value' => 'true' ),
					'group' => esc_html__( 'Thumbnails', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Resize Image', 'total-theme-core' ),
					'param_name' => 'control_thumbs_resize',
					'description' => esc_html__( 'Enable to run the image through the resizing script, disable to simply resize via CSS.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'control_thumbs_carousel', 'value' => 'false' ),
					'group' => esc_html__( 'Thumbnails', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Auto Fit Thumbnails', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to keep your thumbnails in a row.', 'total-theme-core' ),
					'param_name' => 'control_thumbs_fit',
					'dependency' => array( 'element' => 'control_thumbs_carousel', 'value' => 'false' ),
					'group' => esc_html__( 'Thumbnails', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Navigation Thumbnails Height', 'total-theme-core' ),
					'param_name' => 'control_thumbs_height',
					'std' => '70',
					'group' => esc_html__( 'Thumbnails', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Navigation Thumbnails Width', 'total-theme-core' ),
					'param_name' => 'control_thumbs_width',
					'std' => '70',
					'group' => esc_html__( 'Thumbnails', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'choices' => 'gap',
					'heading' => esc_html__( 'Gap', 'total-theme-core' ),
					'param_name' => 'control_thumbs_gap',
					'css' => [
						'selector' => '.wpex-slider-thumbnails',
						'property' => [ 'gap', 'padding-block-start' ]
					],
					'dependency' => array( 'element' => 'control_thumbs_carousel', 'value' => 'false' ),
					'group' => esc_html__( 'Thumbnails', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Caption
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Caption', 'total-theme-core' ),
					'param_name' => 'caption',
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Captions for Videos', 'total-theme-core' ),
					'param_name' => 'video_captions',
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'caption_visibility',
					'choices' => 'visibility',
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Based On Image', 'total-theme-core' ),
					'param_name' => 'caption_type',
					'std' => 'caption',
					'choices' => array(
						'caption' => esc_html__( 'Caption', 'total-theme-core' ),
						'title' => esc_html__( 'Title', 'total-theme-core' ),
						'description' => esc_html__( 'Description', 'total-theme-core' ),
						'alt' => esc_html__( 'Alt', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'caption_style',
					'std' => 'black',
					'choices' => array(
						'black' => esc_html__( 'Black', 'total-theme-core' ),
						'white' => esc_html__( 'White', 'total-theme-core' ),
						'none' => esc_html__( 'None', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Show Transition', 'total-theme-core' ),
					'param_name' => 'caption_show_transition',
					'std' => 'up',
					'value' => array(
						esc_html__( 'None', 'total-theme-core' ) => 'false',
						esc_html__( 'Up', 'total-theme-core' ) => 'up',
						esc_html__( 'Down', 'total-theme-core' ) => 'down',
						esc_html__( 'Left', 'total-theme-core' ) => 'left',
						esc_html__( 'Right', 'total-theme-core' ) => 'right',
					),
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'dependency' => array( 'element' => 'caption_position', 'value_not_equal_to' => 'static' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Hide Transition', 'total-theme-core' ),
					'param_name' => 'caption_hide_transition',
					'std' => 'down',
					'value' => array(
						esc_html__( 'None', 'total-theme-core' ) => 'false',
						esc_html__( 'Up', 'total-theme-core' ) => 'up',
						esc_html__( 'Down', 'total-theme-core' ) => 'down',
						esc_html__( 'Left', 'total-theme-core' ) => 'left',
						esc_html__( 'Right', 'total-theme-core' ) => 'right',
					),
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'dependency' => array( 'element' => 'caption_position', 'value_not_equal_to' => 'static' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Transition Delay', 'total-theme-core' ),
					'param_name' => 'caption_delay',
					'placeholder' => '500',
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'description' => self::param_description( 'ms' ),
					'dependency' => array( 'element' => 'caption_position', 'value_not_equal_to' => 'static' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Position', 'total-theme-core' ),
					'param_name' => 'caption_position',
					'std' => 'bottomCenter',
					'value' => array(
						esc_html__( 'Bottom Center', 'total-theme-core' ) => 'bottomCenter',
						esc_html__( 'Bottom Left', 'total-theme-core' ) => 'bottomLeft',
						esc_html__( 'Bottom Right', 'total-theme-core' ) => 'bottomRight',
						esc_html__( 'Top Center', 'total-theme-core' ) => 'topCenter',
						esc_html__( 'Top Left', 'total-theme-core' ) => 'topLeft',
						esc_html__( 'Top Right', 'total-theme-core' ) => 'topRight',
						esc_html__( 'Center Center', 'total-theme-core' ) => 'centerCenter',
						esc_html__( 'Center Left', 'total-theme-core' ) => 'centerLeft',
						esc_html__( 'Center Right', 'total-theme-core' ) => 'centerRight',
						esc_html__( 'After Image', 'total-theme-core' ) => 'static',
					),
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Horizontal Offset', 'total-theme-core' ),
					'param_name' => 'caption_horizontal',
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'description' => self::param_description( 'px' ),
					'dependency' => array( 'element' => 'caption_position', 'value_not_equal_to' => 'static' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Vertical Offset', 'total-theme-core' ),
					'param_name' => 'caption_vertical',
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'description' => self::param_description( 'px' ),
					'dependency' => array( 'element' => 'caption_position', 'value_not_equal_to' => 'static' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'caption_width',
					'placeholder' => '100%',
					'description' => esc_html__( 'Enter a pixel or percentage value. You can also enter "auto" for content dependent width.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'caption_position', 'value_not_equal_to' => 'static' ),
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Rounded', 'total-theme-core' ),
					'param_name' => 'caption_rounded',
					'dependency' => array( 'element' => 'caption_position', 'value_not_equal_to' => 'static' ),
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'caption_font_size',
					'css' => [ 'selector' => '.wpex-slider-caption', 'property' => 'font-size' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'caption_font_weight',
					'css' => [ 'selector' => '.wpex-slider-caption', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'caption_color',
					'css' => [ 'selector' => '.wpex-slider-caption', 'property' => 'color' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'caption_text_align',
					'std' => 'center',
					'css' => [ 'selector' => '.wpex-slider-caption', 'property' => 'text-align' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'caption_padding',
					'css' => [ 'selector' => '.wpex-slider-caption', 'property' => 'padding' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				),
				// Links
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Image Link', 'total-theme-core' ),
					'param_name' => 'thumbnail_link',
					'std' => 'none',
					'choices' => array(
						'none' => esc_html__( 'None', 'total-theme-core' ),
						'lightbox' => esc_html__( 'Lightbox', 'total-theme-core' ),
						'custom_link' => esc_html__( 'Custom', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'exploded_textarea',
					'heading' => esc_html__( 'Custom links', 'total-theme-core' ),
					'param_name' => 'custom_links',
					'description' => esc_html__( 'Enter links for each slide here. Divide links with linebreaks (Enter). For images without a link enter a # symbol.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'thumbnail_link', 'value' => 'custom_link' ),
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Link Meta Key', 'total-theme-core' ),
					'param_name' => 'link_meta_key',
					'description' => esc_html__( 'If you are using a meta value (custom field) for your image links you can enter the meta key here.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'thumbnail_link', 'value' => 'custom_link' ),
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Target', 'total-theme-core' ),
					'param_name' => 'custom_links_target',
					'dependency' => array( 'element' => 'thumbnail_link', 'value' => 'custom_link' ),
					'std' => 'self',
					'choices' => array(
						'self' => esc_html__( 'Self', 'total-theme-core' ),
						'_blank' => esc_html__( 'Blank', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Lightbox Title', 'total-theme-core' ),
					'param_name' => 'lightbox_title',
					'std' => 'none',
					'choices' => array(
						'none' => esc_html__( 'None', 'total-theme-core' ),
						'alt' => esc_html__( 'Alt', 'total-theme-core' ),
						'title' => esc_html__( 'Title', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'dependency' => array( 'element' => 'thumbnail_link', 'value' => 'lightbox' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Lightbox Caption', 'total-theme-core' ),
					'param_name' => 'lightbox_caption',
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'dependency' => array( 'element' => 'thumbnail_link', 'value' => 'lightbox' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Lightbox Videos', 'total-theme-core' ),
					'param_name' => 'lighbox_videos',
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'description' => esc_html__( 'If enabled the slider will display the image associated with the video in the slider and the video itself in lithbox.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'thumbnail_link', 'value' => 'lightbox' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Overlay
				array(
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Overlay', 'total-theme-core' ),
					'param_name' => 'overlay',
					'std' => 'false',
					'group' => esc_html__( 'Overlay', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Overlay Color', 'total-theme-core' ),
					'param_name' => 'overlay_color',
					'css' => [ 'selector' => '.wpex-slider__overlay', 'property' => 'background' ],
					'group' => esc_html__( 'Overlay', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Overlay Opacity', 'total-theme-core' ),
					'param_name' => 'overlay_opacity',
					'choices' => 'opacity',
					'css' => [ 'selector' => '.wpex-slider__overlay', 'property' => 'opacity' ],
					'group' => esc_html__( 'Overlay', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Design options
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				),
				// Deprecated params
				array( 'type' => 'hidden', 'param_name' => 'lightbox_path' ),
				// Hidden fields.
				array( 'type' => 'hidden', 'param_name' => 'slide_animation' ),
			);

			// Real Media Library integration.
			if ( \defined( 'RML_VERSION' ) ) {
				$params[] = [
					'type' => 'vcex_select',
					'choices' => 'real_media_library_folders',
					'heading' => \esc_html__( 'Real Media Library Folder', 'total-theme-core' ),
					'param_name' => 'rml_folder',
					'group' => \esc_html__( 'Gallery', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				];
				$params[] = [
					'type' => 'textfield',
					'heading' => \esc_html__( 'Count', 'total-theme-core' ),
					'param_name' => 'posts_per_page',
					'value' => '12',
					'description' => \esc_html__( 'How many images to grab from this folder. Enter -1 to display all of them.', 'total-theme-core' ),
					'group' => \esc_html__( 'Gallery', 'total-theme-core' ),
					'dependency' => array( 'element' => 'rml_folder', 'not_empty' => true ),
					'editors' => [ 'wpbakery', 'elementor' ],
				];
			}

			return $params;
		}

		/**
		 * Adds extra CSS styles that need multiple checks.
		 */
		protected static function css_pre_render( $css, $atts = [] ): void {
			// Thumbnail width/height when carousel and resize thumbs are disabled.
			if ( vcex_validate_att_boolean( 'control_thumbs', $atts, true )
				&& ! vcex_validate_att_boolean( 'control_thumbs_carousel', $atts, true )
				&& ! vcex_validate_att_boolean( 'control_thumbs_resize', $atts, true )
			) {
				if ( ! empty( $atts['control_thumbs_height'] ) ) {
					$css->add_extra_css( [
						'selector' => '.wpex-slider-thumbnail',
						'property' => 'height',
						'val' => $atts['control_thumbs_height'],
					] );
				}
				if ( ! empty( $atts['control_thumbs_width'] ) ) {
					$css->add_extra_css( [
						'selector' => '.wpex-slider-thumbnail',
						'property' => 'width',
						'val' => $atts['control_thumbs_width'],
					] );
				}
			}
		}

	}

}

new VCEX_Image_Flexslider_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Image_Flexslider' ) ) {
	class WPBakeryShortCode_Vcex_Image_Flexslider extends WPBakeryShortCode {}
}
