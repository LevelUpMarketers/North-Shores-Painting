<?php

defined( 'ABSPATH' ) || exit;

/**
 * Testimonials Slider Shortcode.
 */
if ( ! class_exists( 'VCEX_Testimonials_Slider_Shortcode' ) ) {

	class VCEX_Testimonials_Slider_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_testimonials_slider';

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
			return esc_html__( 'Testimonials Slider', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Recent testimonials slider', 'total-theme-core' );
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
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				// General
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
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'classes',
					'admin_label' => true,
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
				// Slider Settings
				array(
					'type' => 'vcex_subheading',
					'param_name' => 'vcex_subheading__slider',
					'text' => esc_html__( 'Slider Settings', 'total-theme-core' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Animation', 'total-theme-core' ),
					'param_name' => 'animation',
					'value' => array(
						esc_html__( 'Fade', 'total-theme-core' ) => 'fade_slides',
						esc_html__( 'Slide', 'total-theme-core' ) => 'slide',
					),
					'elementor' => [
						'name' => 'slide_animation', // the "animation" default param causes the element to be hidden on the front-end.
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text',
					'std' => '600',
					'heading' => esc_html__( 'Animation Speed', 'total-theme-core' ),
					'param_name' => 'animation_speed',
					'description' => esc_html__( 'Enter a value in milliseconds.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Auto Play', 'total-theme-core' ),
					'param_name' => 'slideshow',
					'description' => esc_html__( 'Enable automatic slideshow? Disabled in front-end composer to prevent page "jumping".', 'total-theme-core' ),
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Auto Play Delay', 'total-theme-core' ),
					'param_name' => 'slideshow_speed',
					'std' => '5000',
					'description' => esc_html__( 'Enter a value in milliseconds.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'slideshow', 'value' => 'true' ),
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
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Loop', 'total-theme-core' ),
					'param_name' => 'loop',
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
					'heading' => esc_html__( 'Arrows', 'total-theme-core' ),
					'param_name' => 'direction_nav',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Thumbnails', 'total-theme-core' ),
					'param_name' => 'control_thumbs',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'control_thumbs_crop',
					'dependency' => array( 'element' => 'control_thumbs', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'control_thumbs_width',
					'std' => 50,
					'dependency' => array( 'element' => 'control_thumbs', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'param_name' => 'control_thumbs_height',
					'std' => 50,
					'dependency' => array( 'element' => 'control_thumbs', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Style
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Skin', 'total-theme-core' ),
					'param_name' => 'skin',
					'value' => array(
						esc_html__( 'Dark Text', 'total-theme-core' ) => 'dark',
						esc_html__( 'Light Text', 'total-theme-core' ) => 'light',
					),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'center',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Query
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Posts Count', 'total-theme-core' ),
					'param_name' => 'count',
					'value' => '3',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Offset', 'total-theme-core' ),
					'param_name' => 'offset',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Number of post to displace or pass over.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Include Categories', 'total-theme-core' ),
					'param_name' => 'include_categories',
					'param_holder_class' => 'vc_not-for-custom',
					'admin_label' => true,
					'settings' => array(
						'multiple' => true,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					),
					'elementor' => [
						'type' => 'text',
						'description' => esc_html__( 'Enter a comma separated list of category ID\'s.', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Exclude Categories', 'total-theme-core' ),
					'param_name' => 'exclude_categories',
					'param_holder_class' => 'vc_not-for-custom',
					'admin_label' => true,
					'settings' => array(
						'multiple' => true,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					),
					'elementor' => [
						'type' => 'text',
						'description' => esc_html__( 'Enter a comma separated list of category ID\'s to include.', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order', 'total-theme-core' ),
					'param_name' => 'order',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'value' => array(
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'DESC', 'total-theme-core' ) => 'DESC',
						esc_html__( 'ASC', 'total-theme-core' ) => 'ASC',
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Order By', 'total-theme-core' ),
					'param_name' => 'orderby',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Orderby: Meta Key', 'total-theme-core' ),
					'param_name' => 'orderby_meta_key',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array(
						'element' => 'orderby',
						'value' => array( 'meta_value_num', 'meta_value' ),
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Image
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'yes',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'display_author_avatar',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'vcex' => array( 'on' => 'yes', 'off' => 'no' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'img_bottom_margin',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'description' => self::param_description( 'margin' ),
					'dependency' => array( 'element' => 'display_author_avatar', 'value' => 'yes' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'img_border_radius',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'choices' => 'border_radius',
					'supports_blobs' => true,
					'dependency' => array( 'element' => 'display_author_avatar', 'value' => 'yes' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_image_sizes',
					'heading' => esc_html__( 'Image Size', 'total-theme-core' ),
					'param_name' => 'img_size',
					'std' => 'wpex_custom',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'display_author_avatar', 'value' => 'yes' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_image_crop_locations',
					'heading' => esc_html__( 'Image Crop Location', 'total-theme-core' ),
					'param_name' => 'img_crop',
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Width', 'total-theme-core' ),
					'param_name' => 'img_width',
					'description' => esc_html__( 'Enter a width in pixels.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Image Crop Height', 'total-theme-core' ),
					'param_name' => 'img_height',
					'description' => esc_html__( 'Leave empty to disable vertical cropping and keep image proportions.', 'total-theme-core' ),
					'group' => esc_html__( 'Image', 'total-theme-core' ),
					'dependency' => array( 'element' => 'img_size', 'value' => 'wpex_custom' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Content
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'text_color',
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'font_weight',
					'group' => esc_html__( 'Content', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'no',
					'heading' => esc_html__( 'Excerpt', 'total-theme-core' ),
					'param_name' => 'excerpt',
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'vcex' => array( 'off' => 'no', 'on' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Excerpt Length', 'total-theme-core' ),
					'param_name' => 'excerpt_length',
					'value' => '20',
					'description' => esc_html__( 'Enter a custom excerpt length. Will trim the excerpt by this number of words. Enter "-1" to display the_content instead of the auto excerpt.', 'total-theme-core' ),
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Read More', 'total-theme-core' ),
					'param_name' => 'read_more',
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'dependency' => array( 'element' => 'excerpt', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Read More Text', 'total-theme-core' ),
					'param_name' => 'read_more_text',
					'description' => self::param_description( 'text' ),
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'value' => esc_html__( 'read more', 'total-theme-core' ),
					'dependency' => array( 'element' => 'read_more', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'content_typo',
					'selector' => '.vcex-testimonials-fullslider-inner > .entry',
					'group' => esc_html__( 'Content', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				),
				// Meta
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Rating', 'total-theme-core' ),
					'param_name' => 'rating',
					'group' => esc_html__( 'Meta', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'yes',
					'heading' => esc_html__( 'Author', 'total-theme-core' ),
					'param_name' => 'display_author_name',
					'group' => esc_html__( 'Meta', 'total-theme-core' ),
					'vcex' => array( 'on' => 'yes', 'off' => 'no' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'typography',
					'heading' => esc_html__( 'Author Typography', 'total-theme-core' ),
					'param_name' => 'author_typo',
					'selector' => '.vcex-testimonials-fullslider-author-name',
					'group' => esc_html__( 'Meta', 'total-theme-core' ),
					'elementor' => [
						'condition' => [
							'display_author_avatar' => 'yes',
						],
					],
					'editors' => [ 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'no',
					'heading' => esc_html__( 'Company', 'total-theme-core' ),
					'param_name' => 'display_author_company',
					'group' => esc_html__( 'Meta', 'total-theme-core' ),
					'vcex' => array( 'on' => 'yes', 'off' => 'no' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'typography',
					'heading' => esc_html__( 'Company Typography', 'total-theme-core' ),
					'param_name' => 'company_typo',
					'selector' => '.vcex-testimonials-fullslider-company',
					'group' => esc_html__( 'Meta', 'total-theme-core' ),
					'elementor' => [
						'condition' => [
							'display_author_company' => 'yes',
						],
					],
					'editors' => [ 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'meta_color',
					'group' => esc_html__( 'Meta', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'meta_font_size',
					'group' => esc_html__( 'Meta', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'meta_font_weight',
					'group' => esc_html__( 'Meta', 'total-theme-core' ),
				),
				// CSS
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				),
				// Deprecated fields
				array( 'type' => 'hidden', 'param_name' => 'term_slug' ),
				// Hidden fields.
				array( 'type' => 'hidden', 'param_name' => 'slide_animation' ),
			);
		}

		/**
		 * Parse WPBakery attributes on edit.
		 */
		public static function vc_edit_form_fields_attributes( $atts = [] ) {
			if ( ! empty( $atts['animation'] ) && 'fade' === $atts['animation'] ) {
				$atts['animation'] = 'fade_slides';
			}
			return self::parse_deprecated_attributes( $atts );
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( ! empty( $atts['term_slug'] ) && empty( $atts['include_categories']) ) {
				$atts['include_categories'] = $atts['term_slug'];
				unset( $atts['term_slug'] );
			}
			if ( isset( $atts['control_thumbs'] ) && 'no' === $atts['control_thumbs'] ) {
				$atts['control_thumbs'] = 'false';
			}
			return $atts;
		}

		/**
		 * Register autocomplete hooks.
		 */
		public static function register_vc_autocomplete_hooks(): void {
			// Get autocomplete suggestion
			add_filter(
				'vc_autocomplete_vcex_testimonials_slider_include_categories_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Testimonial_Categories::callback'
			);
			add_filter(
				'vc_autocomplete_vcex_testimonials_slider_exclude_categories_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Testimonial_Categories::callback'
			);
			// Render autocomplete suggestions
			add_filter(
				'vc_autocomplete_vcex_testimonials_slider_include_categories_render',
				'TotalThemeCore\WPBakery\Autocomplete\Testimonial_Categories::render'
			);
			add_filter(
				'vc_autocomplete_vcex_testimonials_slider_exclude_categories_render',
				'TotalThemeCore\WPBakery\Autocomplete\Testimonial_Categories::render'
			);
		}

	}

}

new VCEX_Testimonials_Slider_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Testimonials_Slider' ) ) {
	class WPBakeryShortCode_Vcex_Testimonials_Slider extends WPBakeryShortCode {}
}
