<?php

defined( 'ABSPATH' ) || exit;

/**
 * Login Form Shortcode.
 */
if ( ! class_exists( 'VCEX_Login_Form' ) ) {

	class VCEX_Login_Form extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_login_form';

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
			return esc_html__( 'Login Form', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Adds a WordPress login form', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				// Logged In Content
				array(
					'type' => 'textarea_html',
					'heading' => esc_html__( 'Logged in Content', 'total-theme-core' ),
					'param_name' => 'content',
					'value' => esc_html__( 'You are currently logged in.', 'total-theme-core' ) .' ' . '<a href="' . esc_url( wp_logout_url( home_url() ) ) . '">' . esc_html__( 'Logout?', 'total-theme-core' ) . '</a>',
					'description' => esc_html__( 'The content to displayed for logged in users.','total-theme-core'),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Redirect', 'total-theme-core' ),
					'param_name' => 'redirect',
					'description' => esc_html__( 'Enter a URL to redirect the user after they successfully log in. Leave blank to redirect to the current page.','total-theme-core'),
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
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				vcex_vc_map_add_css_animation(),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
				),
				// Style
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'std' => 'bordered',
					'choices' => array(
						'bordered' => esc_html__( 'Bordered', 'total-theme-core' ),
						'boxed' => esc_html__( 'Boxed', 'total-theme-core' ),
						'plain' => esc_html__( 'Plain', 'total-theme-core' ),
					),
					'group' =>  esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'form_style',
					'heading' => esc_html__( 'Form Style', 'total-theme-core' ),
					'param_name' => 'form_style',
					'group' =>  esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' =>  esc_html__( 'Style', 'total-theme-core' ),
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
					'heading' => esc_html__( 'Aligment', 'total-theme-core' ),
					'param_name' => 'float',
					'std' => 'center',
					'exclude_choices' => array( '' ),
					'dependency' => array( 'element' => 'width', 'not_empty' => true ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background_color',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'padding_all',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'border_style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Fields
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Remember Me', 'total-theme-core' ),
					'param_name' => 'remember',
					'group' =>  esc_html__( 'Fields', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Lost Password', 'total-theme-core' ),
					'param_name' => 'lost_password',
					'group' =>  esc_html__( 'Fields', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Register', 'total-theme-core' ),
					'param_name' => 'register',
					'group' =>  esc_html__( 'Fields', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Register URL', 'total-theme-core' ),
					'param_name' => 'register_url',
					'dependency' => array( 'element' => 'register', 'value' => 'true' ),
					'group' =>  esc_html__( 'Fields', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Labels
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Username Label', 'total-theme-core' ),
					'param_name' => 'label_username',
					'group' =>  esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Password Label', 'total-theme-core' ),
					'param_name' => 'label_password',
					'group' =>  esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Remember Me Label', 'total-theme-core' ),
					'param_name' => 'label_remember',
					'group' =>  esc_html__( 'Labels', 'total-theme-core' ),
					'dependency' => array( 'element' => 'remember', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Lost Password Label', 'total-theme-core' ),
					'param_name' => 'lost_password_label',
					'dependency' => array( 'element' => 'lost_password', 'value' => 'true' ),
					'group' =>  esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Register Label', 'total-theme-core' ),
					'param_name' => 'register_label',
					'dependency' => array( 'element' => 'register', 'value' => 'true' ),
					'group' =>  esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Button Label', 'total-theme-core' ),
					'param_name' => 'label_log_in',
					'group' =>  esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Typography
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'text_font_size',
					'css' => [ 'property' => 'font_size' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'colorpicker',
					'heading' => esc_html__( 'Text Color', 'total-theme-core' ),
					'param_name' => 'text_color',
					'css' => [ 'property' => 'color' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'colorpicker',
					'heading' => esc_html__( 'Link Color', 'total-theme-core' ),
					'param_name' => 'link_color',
					'css' => [ 'selector' => 'a', 'property' => 'color' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
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

new VCEX_Login_Form;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Login_Form' ) ) {
	class WPBakeryShortCode_Vcex_Login_Form extends WPBakeryShortCode {}
}
