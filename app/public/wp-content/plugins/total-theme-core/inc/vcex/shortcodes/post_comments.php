<?php

defined( 'ABSPATH' ) || exit;

/**
 * Post Comments Shortcode.
 */
if ( ! class_exists( 'VCEX_Comments_Shortcode' ) ) {

	class VCEX_Comments_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_post_comments';

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
			return esc_html__( 'Post Comments', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display your post comments', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Display Heading?', 'total-theme-core' ),
					'param_name' => 'show_heading',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'max_width',
					'css' => [ 'property' => 'width' ],
					'description' => self::param_description( 'width' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Aligment', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'center',
					'dependency' => array( 'element' => 'max_width', 'not_empty' => true ),
					'editors' => [ 'wpbakery', 'elementor' ],
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
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
			);
		}

	}

}

new VCEX_Comments_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Post_Comments' ) ) {
	class WPBakeryShortCode_Vcex_Post_Comments extends WPBakeryShortCode {}
}
