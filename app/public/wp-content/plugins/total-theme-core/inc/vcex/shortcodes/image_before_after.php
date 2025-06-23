<?php

defined( 'ABSPATH' ) || exit;

/**
 * Image Before/After Shortcode.
 */
if ( ! class_exists( 'VCEX_Image_Before_After_Shortcode' ) ) {

	class VCEX_Image_Before_After_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_image_ba';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			$this->scripts = $this->scripts_to_register();

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Shortcode title.
		 */
		public static function get_title(): string {
			// @note we can't use & because it gets turned into html by esc_html__
			return esc_html__( 'Image Before/After', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display custom field meta value', 'total-theme-core' );
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
		 * Register scripts.
		 */
		public function scripts_to_register(): array {
			return [
				[
					'jquery-move',
					vcex_get_js_file( 'vendor/jquery.event.move' ),
					[ 'jquery' ],
					'2.0',
					true
				],
				[
					'twentytwenty',
					vcex_get_js_file( 'vendor/jquery.twentytwenty' ),
					[ 'jquery', 'jquery-move' ],
					'1.0',
					true,
				],
				[
					'vcex-image-before-after',
					vcex_get_js_file( 'frontend/image-before-after' ),
					[ 'jquery', 'jquery-move', 'twentytwenty' ],
					TTC_VERSION,
					true
				],
			];
		}

		/**
		 * Returns list of script dependencies.
		 */
		public static function get_script_depends(): array {
			return [
				'jquery',
				'imagesloaded',
				'jquery-move',
				'twentytwenty',
				'vcex-image-before-after',
			];
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				// Images
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Source', 'total-theme-core' ),
					'param_name' => 'source',
					'admin_label' => true,
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'std' => 'media_library',
					'value' => array(
						esc_html__( 'Media Library', 'total-theme-core' ) => 'media_library',
						esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
						esc_html__( 'Featured and Secondary Image', 'total-theme-core' ) => 'featured',
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'attach_image',
					'heading' => esc_html__( 'Before', 'total-theme-core' ),
					'param_name' => 'before_img',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'dependency' => array( 'element' => 'source', 'value' => 'media_library' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'attach_image',
					'heading' => esc_html__( 'After', 'total-theme-core' ),
					'param_name' => 'after_img',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'dependency' => array( 'element' => 'source', 'value' => 'media_library' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_custom_field',
					'choices' => 'image',
					'heading' => esc_html__( 'Before Image Custom Field Name', 'total-theme-core' ),
					'param_name' => 'before_img_custom_field',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'dependency' => array( 'element' => 'source', 'value' => 'custom_field' ),
					'description' => esc_html__( 'Your custom field should return an attachment ID.', 'total-theme-core' ),
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_custom_field',
					'choices' => 'images',
					'heading' => esc_html__( 'After Image Custom Field Name', 'total-theme-core' ),
					'param_name' => 'after_img_custom_field',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'dependency' => array( 'element' => 'source', 'value' => 'custom_field' ),
					'description' => esc_html__( 'Your custom field should return an attachment ID.', 'total-theme-core' ),
					'label_block' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'std' => 'wpex_custom',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_crop',
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'img_width',
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'param_name' => 'img_height',
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'group' => esc_html__( 'Images', 'total-theme-core' ),
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Default Offset Percentage', 'total-theme-core' ),
					'std' => '50%',
					'param_name' => 'default_offset_pct',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// General
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'el_class',
					'editors' => [ 'wpbakery', 'elementor' ],
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
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Handle Style', 'total-theme-core' ),
					'param_name' => 'handle_style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'std' => 'outline',
					'choices' => array(
						'outline' => esc_html__( 'Outline', 'total-theme-core' ),
						'solid' => esc_html__( 'Solid', 'total-theme-core' ),
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Orientation', 'total-theme-core' ),
					'param_name' => 'orientation',
					'std' => 'horizontal',
					'choices' => array(
						'horizontal' => esc_html__( 'Horizontal', 'total-theme-core' ),
						'vertical' => esc_html__( 'Vertical', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Accent Color', 'total-theme-core' ),
					'param_name' => 'accent_color',
					'css' => [
						'property' => '--accent',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Arrow Color', 'total-theme-core' ),
					'param_name' => 'arrow_color',
					'css' => [
						'selector' => '.twentytwenty-handle',
						'property' => '--arrow-color',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'width',
					'css' => true,
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'none',
					'dependency' => array( 'element' => 'width', 'not_empty' => true ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Handle Border Width', 'total-theme-core' ),
					'param_name' => 'handle_border_width',
					'std' => '3px',
					'choices' => [
						'1px' => '1px',
						'2px' => '2px',
						'3px' => '3px',
						'4px' => '4px',
					],
					'css' => [
						'property' => '--handle-border-width',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Overlay
				array(
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Overlay on Hover', 'total-theme-core' ),
					'std' => 'true',
					'param_name' => 'overlay',
					'group' => esc_html__( 'Overlay', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Before Label', 'total-theme-core' ),
					'param_name' => 'before_label',
					'dependency' => array( 'element' => 'overlay', 'value' => 'true' ),
					'group' => esc_html__( 'Overlay', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'After Label', 'total-theme-core' ),
					'param_name' => 'after_label',
					'dependency' => array( 'element' => 'overlay', 'value' => 'true' ),
					'group' => esc_html__( 'Overlay', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// CSS
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				),
			);
		}

	}

}

new VCEX_Image_Before_After_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Image_Ba' ) ) {
	class WPBakeryShortCode_Vcex_Image_Ba extends WPBakeryShortCode {}
}
