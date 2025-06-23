<?php

defined( 'ABSPATH' ) || exit;

/**
 * Image Gallery Shortcode.
 */
if ( ! class_exists( 'Vcex_Image_Gallery_Slider' ) ) {

	class Vcex_Image_Gallery_Slider extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_image_galleryslider';

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
			return esc_html__( 'Gallery Slider', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Image slider with thumbnail navigation', 'total-theme-core' );
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
					'heading' => esc_html__( 'Images', 'total-theme-core' ),
					'param_name' => 'image_ids',
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'description' => esc_html__( 'You can display captions by giving your images a caption and you can also display videos by adding an image that has a Video URL defined for it.', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading'  => esc_html__( 'Post Gallery', 'total-theme-core' ),
					'param_name' => 'post_gallery',
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
					'description' => sprintf( esc_html__( 'Enable to display images from the current post "%sImage Gallery%s".', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/using-post-gallery-image-galleries/" target="_blank" rel="noopener noreferrer">', '</a>' ) . '<br>' . esc_html__( 'You can define images above to display as a fallback in the frontend editor when working with dynamic templates.', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'gallery',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name'  => 'custom_field_gallery',
					'group' => esc_html__( 'Gallery', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
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
					'param_name' => 'classes',
					'description' => self::param_description( 'el_class' ),
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				],
				// Slider settings
				[
					'type' => 'vcex_subheading',
					'param_name' => 'vcex_subheading__slider',
					'text' => esc_html__( 'Slider Settings', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Randomize', 'total-theme-core' ),
					'param_name' => 'randomize',
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Animation', 'total-theme-core' ),
					'param_name' => 'animation',
					'std' => 'slide',
					'choices' => [
						'slide' => esc_html__( 'Slide', 'total-theme-core' ),
						'fade_slides' => esc_html__( 'Fade', 'total-theme-core' ),
					],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Loop', 'total-theme-core' ),
					'param_name' => 'loop',
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Auto Height Animation', 'total-theme-core' ),
					'std' => '500',
					'param_name' => 'height_animation',
					'description' => esc_html__( 'You can enter "0.0" to disable the animation completely.', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Speed', 'total-theme-core' ),
					'param_name' => 'animation_speed',
					'std' => '600',
					'description' => esc_html__( 'Enter a value in milliseconds.', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Auto Play', 'total-theme-core' ),
					'param_name' => 'slideshow',
					'description' => esc_html__( 'Enable automatic slideshow? Disabled in front-end composer to prevent page "jumping".', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Auto Play Delay', 'total-theme-core' ),
					'param_name' => 'slideshow_speed',
					'std' => '5000',
					'description' => esc_html__( 'Enter a value in milliseconds.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'slideshow', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Arrows', 'total-theme-core' ),
					'param_name' => 'direction_nav',
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Arrows on Hover', 'total-theme-core' ),
					'param_name' => 'direction_nav_hover',
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Dot Navigation', 'total-theme-core' ),
					'param_name' => 'control_nav',
				],
				// Image
				[
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'std' => 'wpex_custom',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_crop',
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'img_width',
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'group' => esc_html__( 'Image', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'param_name' => 'img_height',
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'group' => esc_html__( 'Image', 'total-theme-core' )
				],
				// Thumbnails
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Columns', 'total-theme-core' ),
					'param_name' => 'thumbnails_columns',
					'std' => '5',
					'description' => esc_html__( 'This specific slider displays the thumbnails in "rows" if you want your thumbnails displayed under the slider as a carousel, use the "Image Slider" module instead.', 'total-theme-core' ),
					'group' => esc_html__( 'Thumbnails', 'total-theme-core' ),
					'choices' => [
						'6' => '6',
						'5' => '5',
						'4' => '4',
						'3' => '3',
						'2' => '2',
					],
				],
				[
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_thumb_crop',
					'std' => 'soft-crop',
					'group' => esc_html__( 'Thumbnails', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'img_thumb_width',
					'value' => '',
					'group' => esc_html__( 'Thumbnails', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'param_name' => 'img_thumb_height',
					'value' => '',
					'group' => esc_html__( 'Thumbnails', 'total-theme-core' ),
				],
				// Caption
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'caption',
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'caption_visibility',
					'choices' => 'visibility',
					'dependency' => [ 'element' => 'caption', 'value' => 'true' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Based On Image', 'total-theme-core' ),
					'param_name' => 'caption_type',
					'std' => 'caption',
					'choices' => [
						'caption' => esc_html__( 'Caption', 'total-theme-core' ),
						'title' => esc_html__( 'Title', 'total-theme-core' ),
						'description' => esc_html__( 'Description', 'total-theme-core' ),
						'alt' => esc_html__( 'Alt', 'total-theme-core' ),
					],
					'dependency' => [ 'element' => 'caption', 'value' => 'true' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'caption_style',
					'std' => 'black',
					'choices' => [
						'black' => esc_html__( 'Black', 'total-theme-core' ),
						'white' => esc_html__( 'White', 'total-theme-core' ),
					],
					'dependency' => [ 'element' => 'caption', 'value' => 'true' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Rounded?', 'total-theme-core' ),
					'param_name' => 'caption_rounded',
					'dependency' => [ 'element' => 'caption', 'value' => 'true' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Position', 'total-theme-core' ),
					'param_name' => 'caption_position',
					'std' => 'bottomCenter',
					'value' => [
						esc_html__( 'Bottom Center', 'total-theme-core' ) => 'bottomCenter',
						esc_html__( 'Bottom Left', 'total-theme-core' ) => 'bottomLeft',
						esc_html__( 'Bottom Right', 'total-theme-core' ) => 'bottomRight',
						esc_html__( 'Top Center', 'total-theme-core' ) => 'topCenter',
						esc_html__( 'Top Left', 'total-theme-core' ) => 'topLeft',
						esc_html__( 'Top Right', 'total-theme-core' ) => 'topRight',
						esc_html__( 'Center Center', 'total-theme-core' ) => 'centerCenter',
						esc_html__( 'Center Left', 'total-theme-core' ) => 'centerLeft',
						esc_html__( 'Center Right', 'total-theme-core' ) => 'centerRight',
					],
					'dependency' => [ 'element' => 'caption', 'value' => 'true' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Show Transition', 'total-theme-core' ),
					'param_name' => 'caption_show_transition',
					'std' => 'up',
					'value' => [
						esc_html__( 'None', 'total-theme-core' ) => 'false',
						esc_html__( 'Up', 'total-theme-core' ) => 'up',
						esc_html__( 'Down', 'total-theme-core' ) => 'down',
						esc_html__( 'Left', 'total-theme-core' ) => 'left',
						esc_html__( 'Right', 'total-theme-core' ) => 'right',
					],
					'dependency' => [ 'element' => 'caption', 'value' => 'true' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Hide Transition', 'total-theme-core' ),
					'param_name' => 'caption_hide_transition',
					'std' => 'down',
					'value' => [
						esc_html__( 'None', 'total-theme-core' ) => 'false',
						esc_html__( 'Up', 'total-theme-core' ) => 'up',
						esc_html__( 'Down', 'total-theme-core' ) => 'down',
						esc_html__( 'Left', 'total-theme-core' ) => 'left',
						esc_html__( 'Right', 'total-theme-core' ) => 'right',
					],
					'dependency' => [ 'element' => 'caption', 'value' => 'true' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'caption_width',
					'dependency' => [ 'element' => 'caption', 'value' => 'true' ],
					'value' => '100%',
					'description' => esc_html__( 'Enter a pixel or percentage value. You can also enter "auto" for content dependent width.', 'total-theme-core' ),
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'caption_font_size',
					'css' => [
						'selector' => '.wpex-slider-caption',
						'property' => 'font-size',
					],
					'description' => self::param_description( 'font_size' ),
					'dependency' => [ 'element' => 'caption', 'value' => 'true' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'caption_padding',
					'css' => [
						'selector' => '.wpex-slider-caption',
						'property' => 'padding',
					],
					'dependency' => [ 'element' => 'caption', 'value' => 'true' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Horizontal Offset', 'total-theme-core' ),
					'param_name' => 'caption_horizontal',
					'dependency' => [ 'element' => 'caption', 'value' => 'true' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'description' => self::param_description( 'px' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Vertical Offset', 'total-theme-core' ),
					'param_name' => 'caption_vertical',
					'dependency' => [ 'element' => 'caption', 'value' => 'true' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'description' => self::param_description( 'px' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Delay', 'total-theme-core' ),
					'param_name' => 'caption_delay',
					'std' => '500',
					'dependency' => [ 'element' => 'caption', 'value' => 'true' ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'description' => self::param_description( 'ms' ),
				],
				// Links
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Image Link', 'total-theme-core' ),
					'param_name' => 'thumbnail_link',
					'std' => 'none',
					'choices' => [
						'none' => esc_html__( 'None', 'total-theme-core' ),
						'lightbox' => esc_html__( 'Lightbox', 'total-theme-core' ),
						'custom_link' => esc_html__( 'Custom', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Links', 'total-theme-core' ),
				],
				[
					'type' => 'exploded_textarea',
					'heading' => esc_html__( 'Custom links', 'total-theme-core' ),
					'param_name' => 'custom_links',
					'description' => esc_html__( 'Enter links for each slide here. Divide links with linebreaks (Enter). For images without a link enter a # symbol.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'thumbnail_link', 'value' => 'custom_link' ],
					'group' => esc_html__( 'Links', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Link Meta Key', 'total-theme-core' ),
					'param_name' => 'link_meta_key',
					'description' => esc_html__( 'If you are using a meta value (custom field) for your image links you can enter the meta key here.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'thumbnail_link', 'value' => 'custom_link' ],
					'group' => esc_html__( 'Links', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Target', 'total-theme-core' ),
					'param_name' => 'custom_links_target',
					'dependency' => [ 'element' => 'thumbnail_link', 'value' => 'custom_link' ],
					'std' => 'self',
					'choices' => [
						'self' => esc_html__( 'Self', 'total-theme-core' ),
						'_blank' => esc_html__( 'Blank', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Links', 'total-theme-core' ),
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
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'enable',
					'vcex' => [ 'on' => 'enable', 'off' => 'false' ],
					'heading' => esc_html__( 'Lightbox Caption', 'total-theme-core' ),
					'param_name' => 'lightbox_caption',
					'group' => esc_html__( 'Links', 'total-theme-core' ),
					'dependency' => [ 'element' => 'thumbnail_link', 'value' => 'lightbox' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				// Deprecated params
				[ 'type' => 'hidden', 'param_name' => 'lightbox_path' ],
			];

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

	}

}

new Vcex_Image_Gallery_Slider;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Image_Galleryslider' ) ) {
	class WPBakeryShortCode_Vcex_Image_Galleryslider extends WPBakeryShortCode {}
}
