<?php

defined( 'ABSPATH' ) || exit;

/**
 * Image Swap Shortcode.
 */
if ( ! class_exists( 'Vcex_Image_Swap_Shortcode' ) ) {

	class Vcex_Image_Swap_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_image_swap';

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
			return esc_html__( 'Image Swap', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Double Image Hover Effect', 'total-theme-core' );
		}

		/**
		 * Returns custom vc map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			return [
				'admin_enqueue_js' => 'image-before-after',
				'js_view'          => 'vcexBackendViewImageBeforeAfter',
			];
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				// Images
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Source', 'total-theme-core' ),
					'param_name' => 'source',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'std' => 'media_library',
					'value' => [
						esc_html__( 'Media Library', 'total-theme-core' ) => 'media_library',
						esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
						esc_html__( 'Featured and Secondary Image', 'total-theme-core' ) => 'featured',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'attach_image',
					'heading' => esc_html__( 'Primary Image', 'total-theme-core' ),
					'param_name' => 'primary_image',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'dependency' => [ 'element' => 'source', 'value' => 'media_library' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'attach_image',
					'heading' => esc_html__( 'Secondary Image', 'total-theme-core' ),
					'param_name' => 'secondary_image',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'dependency' => [ 'element' => 'source', 'value' => 'media_library' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'image',
					'heading' => esc_html__( 'Primary Image Custom Field Name', 'total-theme-core' ),
					'param_name' => 'primary_image_custom_field',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'dependency' => [ 'element' => 'source', 'value' => 'custom_field' ],
					'description' => esc_html__( 'Your custom field should return an attachment ID.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'images',
					'heading' => esc_html__( 'Secondary Image Custom Field Name', 'total-theme-core' ),
					'param_name' => 'secondary_image_custom_field',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'dependency' => [ 'element' => 'source', 'value' => 'custom_field' ],
					'description' => esc_html__( 'Your custom field should return an attachment ID.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				array(
					'type' => 'vcex_select',
					'choices' => 'aspect_ratio',
					'heading' => esc_html__( 'Image Aspect Ratio', 'total-theme-core' ),
					'param_name' => 'img_aspect_ratio',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'object_fit',
					'heading' => esc_html__( 'Image Fit', 'total-theme-core' ),
					'param_name' => 'img_object_fit',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'dependency' => array( 'element' => 'img_aspect_ratio', 'not_empty' => true ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				[
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'std' => 'wpex_custom',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_crop',
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'img_width',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'param_name' => 'img_height',
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'dependency' => [ 'element' => 'img_size', 'value' => 'wpex_custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// General
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Hover Swap Speed', 'total-theme-core' ),
					'param_name' => 'hover_speed',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						'75ms' => '75',
						'100ms' => '100',
						'150ms' => '150',
						'200ms' => '200',
						'300ms' => '300',
						'500ms' => '500',
						'700ms' => '700',
						'1000ms' => '1000',
					],
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
					'type' => 'vcex_select',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'css' => [ 'selector' => '{{WRAPPER}} img', 'property' => 'border-radius' ],
					'supports_blobs' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'container_width',
					'css' => [ 'property' => 'width' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'description' => esc_html__( 'By default the images are stretched to 100% to fit the parent container. Enter a custom width (px or %) to restrict the width of your images.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'center',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'container_width', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Image Overlay', 'total-theme-core' ),
					'param_name' => 'overlay_style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'exclude_choices' => [ 'thumb-swap', 'thumb-swap-title' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Onclick
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Link Type', 'total-theme-core' ),
					'param_name' => 'onclick',
					'value' => array(
						esc_html__( 'None', 'total-theme-core' ) => '',
						esc_html__( 'Custom Link', 'total-theme-core' ) => 'custom_link',
						esc_html__( 'Internal Page', 'total-theme-core' ) => 'internal_link',
						esc_html__( 'Current Post', 'total-theme-core' ) => 'post_permalink',
						esc_html__( 'Scroll to Section', 'total-theme-core' ) => 'local_scroll',
						esc_html__( 'Toggle Element', 'total-theme-core' ) => 'toggle_element',
						esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
						esc_html__( 'Callback Function', 'total-theme-core' ) => 'callback_function',
						esc_html__( 'Inline Content or iFrame Popup', 'total-theme-core' ) => 'popup',
						esc_html__( 'Video Lightbox', 'total-theme-core' ) => 'lightbox_video',
						esc_html__( 'Post Video Lightbox', 'total-theme-core' ) => 'lightbox_post_video',
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Link', 'total-theme-core' ),
					'param_name' => 'onclick_url',
					'description' => self::param_description( 'text' ),
					'dependency' => array(
						'element' => 'onclick',
						'value' => array( 'custom_link', 'local_scroll', 'popup', 'lightbox_video', 'toggle_element' ),
					),
					'description' => esc_html__( 'Enter your custom link url, lightbox url or local/toggle element ID (including a # at the front).', 'total-theme-core' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vc_link',
					'heading' => esc_html__( 'Internal Link', 'total-theme-core' ),
					'param_name' => 'onclick_internal_link',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value' => 'internal_link' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_custom_field',
					'choices' => 'links',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name' => 'onclick_custom_field',
					'dependency' => array( 'element' => 'onclick', 'value' => 'custom_field' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_callback_function',
					'heading' => esc_html__( 'Callback Function', 'total-theme-core' ),
					'param_name' => 'onclick_callback_function',
					'dependency' => array( 'element' => 'onclick', 'value' => 'callback_function' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'description' => sprintf( esc_html__( 'Callback functions must be %swhitelisted%s for security reasons.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', '</a>' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Title Attribute', 'total-theme-core' ),
					'param_name' => 'onclick_title',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'not_empty' => true ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Target', 'total-theme-core' ),
					'param_name' => 'onclick_target',
					'std' => 'self',
					'choices' => array(
						'self'   => esc_html__( 'Self', 'total-theme-core' ),
						'_blank' => esc_html__( 'Blank', 'total-theme-core' ),
					),
					'dependency' => array(
						'element' => 'onclick',
						'value' => array( 'custom_link', 'internal_link', 'custom_field', 'callback_function' ),
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Rel Attribute', 'total-theme-core' ),
					'param_name' => 'onclick_rel',
					'std' => '',
					'choices' => array(
						'' => esc_html__( 'None', 'total-theme-core' ),
						'nofollow' => esc_html__( 'Nofollow', 'total-theme-core' ),
						'sponsored' => esc_html__( 'Sponsored', 'total-theme-core' ),
					),
					'dependency' => array(
						'element' => 'onclick',
						'value' => array( 'custom_link', 'internal_link', 'custom_field', 'callback_function' ),
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Lightbox Dimensions (optional)', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_dims',
					'description' => esc_html__( 'Enter a custom width and height for your lightbox pop-up window. Use format widthxheight. Example: 1920x1080.', 'total-theme-core' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value' => array( 'lightbox_video', 'popup' ) ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Lightbox Title', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_title',
					'dependency' => array(
						'element' => 'onclick',
						'value' => array( 'lightbox_video', 'popup' )
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textarea',
					'heading' => esc_html__( 'Lightbox Caption', 'total-theme-core' ),
					'param_name' => 'onclick_lightbox_caption',
					'dependency' => array(
						'element' => 'onclick',
						'value' => array( 'lightbox_video', 'popup' )
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'exploded_textarea',
					'heading' => esc_html__( 'Custom Data Attributes', 'total-theme-core' ),
					'param_name' => 'onclick_data_attributes',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array(
						'element' => 'onclick',
						'value' => array(
							'custom_link',
							'custom_field',
							'callback_function',
							'popup',
						),
					),
					'description' => esc_html__( 'Enter your custom data attributes in the format of data|value. Hit enter after each set of data attributes.', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				// Design Options
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				// Hidden
				[ 'type' => 'hidden', 'param_name' => 'link' ],
				[ 'type' => 'hidden', 'param_name' => 'link_title' ],
				[ 'type' => 'hidden', 'param_name' => 'link_target' ],
				[ 'type' => 'hidden', 'param_name' => 'dynamic_images' ],
			];
		}

		/**
		 * Parses deprecated attributes.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}

			if ( isset( $atts['dynamic_images'] )
				&& ( 'true' == $atts['dynamic_images'] || 'yes' === $atts['dynamic_images'] )
			) {
				$atts['source'] = 'featured';
				unset( $atts['dynamic_images'] );
			}

			if ( ! empty( $atts['link'] ) && empty( $atts['onclick'] ) ) {
				if ( ! empty( $atts['link_target'] ) && in_array( $atts['link_target'], [ 'blank', '_blank' ] ) ) {
					$atts['onclick_target'] = '_blank';
					unset( $atts['link_target'] );
				}
				if ( ! empty( $atts['link_title'] ) ) {
					$atts['onclick_title'] = $atts['link_title'];
					unset( $atts['link_title'] );
				}
				if ( is_string( $atts['link'] ) && str_contains( $atts['link'], 'url:' ) ) {
					$atts['onclick'] = 'internal_link';
					$atts['onclick_internal_link'] = $atts['link'];
					if ( str_contains( $atts['link'], 'target:_blank' ) ) {
						$atts['onclick_target'] = '_blank';
					}
					if ( str_contains( $atts['link'], 'rel:nofollow' ) ) {
						$atts['onclick_rel'] = 'nofollow';
					}
				} else {
					$atts['onclick'] = 'custom_link';
					$atts['onclick_url'] = $atts['link'];
				}
				unset( $atts['link'] );
			}

			return $atts;
		}

	}

}

new Vcex_Image_Swap_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Image_Swap' ) ) {
	class WPBakeryShortCode_Vcex_Image_Swap extends WPBakeryShortCode {}
}
