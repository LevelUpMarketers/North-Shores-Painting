<?php

defined( 'ABSPATH' ) || exit;

/**
 * Staff Social Shortcode.
 */
if ( ! class_exists( 'Vcex_Staff_Social_Shortcode' ) ) {

	class Vcex_Staff_Social_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'staff_social';

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
			return esc_html__( 'Staff Social Links', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Single staff social links', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				array(
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Staff Member ID', 'total-theme-core' ),
					'param_name' => 'post_id',
					'admin_label' => true,
					'param_holder_class' => 'vc_not-for-custom',
					'description' => esc_html__( 'Select a staff member to display their social links. By default it will diplay the current staff member links.', 'total-theme-core'),
					'settings' => array(
						'multiple' => false,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					),
				),
				array(
					'type' => 'vcex_social_button_styles',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'std' => get_theme_mod( 'staff_social_default_style', 'minimal-round' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Link Target', 'total-theme-core' ),
					'param_name' => 'link_target',
					'value' => array(
						esc_html__( 'Blank', 'total-theme-core' ) => 'blank',
						esc_html__( 'Self', 'total-theme-core') => 'self',
					),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Size', 'total-theme-core' ),
					'param_name' => 'font_size',
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Icon Margin', 'total-theme-core' ),
					'param_name' => 'icon_margin',
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
				// CSS
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				),
			);
		}

		/**
		 * Register autocomplete hooks.
		 */
		public static function register_vc_autocomplete_hooks(): void {
			add_filter(
				'vc_autocomplete_staff_social_post_id_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Staff_Members::callback'
			);
			add_filter(
				'vc_autocomplete_staff_social_post_id_render',
				'TotalThemeCore\WPBakery\Autocomplete\Staff_Members::render'
			);
		}

	}

}

new Vcex_Staff_Social_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Staff_Social' ) ) {
	class WPBakeryShortCode_Staff_Social extends WPBakeryShortCode {}
}
