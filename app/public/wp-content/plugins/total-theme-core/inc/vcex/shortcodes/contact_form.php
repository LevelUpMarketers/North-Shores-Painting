<?php

defined( 'ABSPATH' ) || exit;

/**
 * Contact Form Shortcode.
 */
if ( ! class_exists( 'VCEX_Contact_Form_Shortcode' ) ) {

	class VCEX_Contact_Form_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_contact_form';

		/**
		 * Main constructor.
		 */
		public function __construct() {
			$this->scripts = $this->scripts_to_register();

			// Ajax form submission.
			add_action( 'wp_ajax_vcex_contact_form_action', [ self::class, 'form_submission' ] );
			add_action( 'wp_ajax_nopriv_vcex_contact_form_action', [ self::class, 'form_submission' ] );

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Shortcode title.
		 */
		public static function get_title(): string {
			return \esc_html__( 'Contact Form', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Simple contact form', 'total-theme-core' );
		}

		/**
		 * Register form scripts.
		 */
		public function scripts_to_register(): array {
			$scripts = [
				[
					'vcex-contact-form',
					vcex_get_js_file( 'frontend/contact-form' ),
					[],
					'1.0',
					true
				],
			];
			if ( function_exists( 'wpex_get_recaptcha_keys' ) && $site_key = wpex_get_recaptcha_keys( 'site' ) ) {
				$site_key_safe = sanitize_text_field( $site_key );
				if ( $site_key_safe ) {
					$scripts[] = [
						'recaptcha',
						"https://www.google.com/recaptcha/api.js?render={$site_key_safe}",
						[],
						null,
						[ 'strategy' => 'defer' ]
					];
				}
			}
			return $scripts;
		}

		/**
		 * Enqueue form scripts.
		 */
		protected static function enqueue_scripts( array $atts ): void {
			wp_enqueue_script( 'vcex-contact-form' );
			if ( vcex_validate_att_boolean( 'enable_recaptcha', $atts, true ) ) {
				wp_enqueue_script( 'recaptcha' );
			}
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$loader_options = [];

			if ( is_callable( 'TotalTheme\Pagination\Load_More::get_loader_svg_options' ) ) {
				$loader_options = array_flip( TotalTheme\Pagination\Load_More::get_loader_svg_options() );
			}

			return array(
				// General
				array(
					'type' => 'vcex_notice',
					'param_name' => 'editor_notice',
					'text' => esc_html__( 'Forms will be sent to the "Administration Email Address" as defined under Settings > General or via your custom email address defined in the Theme Options Panel.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Privacy Checkbox', 'total-theme-core' ),
					'param_name' => 'enable_privacy_check',
					'std' => 'true',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'reCAPTCHA', 'total-theme-core' ),
					'param_name' => 'enable_recaptcha',
					'std' => 'true',
					'description' => sprintf( esc_html__( 'Enable Google reCAPTCHA to help prevent spam submissions. You will need to generate your site and secret keys %shere%s (use the v3 API) then enter these keys in the %sTheme Panel%s.', 'total-theme-core' ), '<a href="https://www.google.com/recaptcha/admin/create" target="_blank" rel="nofollow noopener noreferrer">', '&#8599;</a>', '<a href="' . esc_url( admin_url( '?page=wpex-panel' ) ) . '" target="_blank" rel="nofollow noopener noreferrer">', '&#8599;</a>' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Hide reCAPTCHA Badge', 'total-theme-core' ),
					'param_name' => 'enable_recaptcha_notice',
					'std' => 'false',
					'description' => sprintf( esc_html__( 'According to the %sreCAPTCHA guidelines%s, if you wish to hide the default reCAPTCHA badge you must include the reCAPTCHA branding visibly in the user flow. Enable this setting to display the reCAPTCHA branding after your form and hide the default badge.', 'total-theme-core' ), '<a href="https://developers.google.com/recaptcha/docs/faq#id-like-to-hide-the-recaptcha-badge.-what-is-allowed" target="_blank" rel="nofollow noopener noreferrer">', '&#8599;</a>' ),
					'dependency' => array( 'element' => 'enable_recaptcha', 'value' => 'true' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Subject', 'total-theme-core' ),
					'param_name' => 'email_subject',
					'description' => esc_html__( 'Override the default email subject that shows up when you receive a new message.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
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
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				// Style
				array(
					'type' => 'vcex_select',
					'choices' => 'form_style',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'description' => esc_html__( 'Select a preset form style or go to Appearance > Customize > General Theme Options > Forms where you can customize the design of all forms.', 'total-theme-core' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Max Width', 'total-theme-core' ),
					'param_name' => 'width',
					'css' => [ 'property' => 'max-width' ],
					'description' => self::param_description( 'width' ),
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
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'text_align',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Item Spacing', 'total-theme-core' ),
					'param_name' => 'items_spacing',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'description' => esc_html__( 'Controls the bottom margin used between the various fields, submit button, loader and notices.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Labels
				array(
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Placeholders', 'total-theme-core' ),
					'param_name' => 'enable_placeholders',
					'std' => 'false',
					'description' => esc_html__( 'Enable to display placeholders instead of labels.', 'total-theme-core' ),
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Name Label', 'total-theme-core' ),
					'param_name' => 'label_name',
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Email Label', 'total-theme-core' ),
					'param_name' => 'label_email',
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Message Label', 'total-theme-core' ),
					'param_name' => 'label_message',
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Privacy Policy Label', 'total-theme-core' ),
					'param_name' => 'label_privacy',
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
					'dependency' => array( 'element' => 'enable_privacy_check', 'value' => 'true' ),
					'description' => esc_html__( 'To create a link to your privacy page simply add double curly brackets around the text you want to link, example: {{Privacy Page}} and the element will automatically link to your privacy page as defined in the WordPress Settings > Privacy tab.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'labels_bottom_margin',
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
					'dependency' => array(
						'element' => 'enable_placeholders',
						'value_not_equal_to' => 'true',
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'labels_color',
					'dependency' => array(
						'element' => 'enable_placeholders',
						'value_not_equal_to' => 'true',
					),
					'css' => [
						'selector' => '.vcex-contact-form__label',
						'property' => 'color',
					],
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'labels_font_size',
					'css' => [
						'selector' => '.vcex-contact-form__label',
						'property' => 'font-size',
					],
					'dependency' => array( 'element' => 'enable_placeholders', 'value_not_equal_to' => 'true' ),
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'labels_font_weight',
					'dependency' => array( 'element' => 'enable_placeholders', 'value_not_equal_to' => 'true' ),
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Required Symbol?', 'total-theme-core' ),
					'param_name' => 'enable_required_label',
					'std' => 'true',
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Required Label', 'total-theme-core' ),
					'param_name' => 'label_required',
					'dependency' => array( 'element' => 'enable_required_label', 'value' => 'true' ),
					'description' => esc_html__( 'Display a custom text instead of the default asterisk.', 'total-theme-core' ),
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Required Color', 'total-theme-core' ),
					'param_name' => 'required_color',
					'css' => [
						'selector' => '.vcex-contact-form__required',
						'property' => 'color',
					],
					'dependency' => array( 'element' => 'enable_required_label', 'value' => 'true' ),
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Inputs
				array(
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Stack Fields', 'total-theme-core' ),
					'param_name' => 'stack_fields',
					'std' => 'false',
					'description' => esc_html__( 'By default the name and email fields display side by side, enable this option to stack them vertically.', 'total-theme-core' ),
					'group' => esc_html__( 'Inputs', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Message Rows', 'total-theme-core' ),
					'param_name' => 'message_rows',
					'description' => esc_html__( 'Number of rows for the message textarea.', 'total-theme-core' ),
					'group' => esc_html__( 'Inputs', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Message Minlength', 'total-theme-core' ),
					'param_name' => 'message_minlength',
					'description' => esc_html__( 'Minimum length in characters for the message field.', 'total-theme-core' ),
					'group' => esc_html__( 'Inputs', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Message Maxlength', 'total-theme-core' ),
					'param_name' => 'message_maxlength',
					'description' => esc_html__( 'Maximum length in characters for the message field.', 'total-theme-core' ),
					'group' => esc_html__( 'Inputs', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'input_background',
					'css' => [
						'selector' => [ 'input', 'textarea' ],
						'property' => 'background',
					],
					'group' => esc_html__( 'Inputs', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Focus', 'total-theme-core' ),
					'param_name' => 'input_focus_background',
					'css' => [
						'selector' => [ 'input:focus', 'textarea:focus' ],
						'property' => 'background',
					],
					'group' => esc_html__( 'Inputs', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'input_color',
					'css' => [
						'selector' => [ 'input', 'textarea' ],
						'property' => 'color',
					],
					'group' => esc_html__( 'Inputs', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'input_border_width',
					'css' => [
						'selector' => [ 'input', 'textarea' ],
						'property' => 'border-width',
					],
					'description' => self::param_description( 'border_width' ),
					'group' => esc_html__( 'Inputs', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'input_border_color',
					'css' => [
						'selector' => [ 'input', 'textarea' ],
						'property' => 'border-color',
					],
					'group' => esc_html__( 'Inputs', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color: Focus', 'total-theme-core' ),
					'param_name' => 'input_focus_border_color',
					'css' => [
						'selector' => [ 'input:focus', 'textarea:focus' ],
						'property' => 'border-color',
					],
					'group' => esc_html__( 'Inputs', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'input_border_radius',
					'css' => [
						'selector' => [ 'input', 'textarea' ],
						'property' => 'border-radius',
					],
					'choices' => 'border_radius',
					'description' => self::param_description( 'border_radius' ),
					'group' => esc_html__( 'Inputs', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Button.
				array(
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Full Button', 'total-theme-core' ),
					'param_name' => 'button_fullwidth',
					'std' => 'true',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Button Text', 'total-theme-core' ),
					'param_name' => 'button_text',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'button_font_size',
					'css' => [
						'selector' => '.vcex-contact-form__submit',
						'property' => 'font-size',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'button_font_weight',
					'css' => [
						'selector' => '.vcex-contact-form__submit',
						'property' => 'font-weight',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'button_typo',
					'selector' => '.vcex-contact-form__submit',
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'button_border_radius',
					'choices' => 'border_radius',
					'css' => [
						'selector' => '.vcex-contact-form__submit',
						'property' => 'border-radius',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'button_padding',
					'css' => [
						'selector' => '.vcex-contact-form__submit',
						'property' => 'padding',
					],
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Button', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'button_background',
					'css' => [
						'selector' => '.vcex-contact-form__submit',
						'property' => 'background',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'button_color',
					'css' => [
						'selector' => '.vcex-contact-form__submit',
						'property' => 'color',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'button_hover_background',
					'css' => [
						'selector' => '.vcex-contact-form__submit:hover',
						'property' => 'background',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'button_hover_color',
					'css' => [
						'selector' => '.vcex-contact-form__submit:hover',
						'property' => 'color',
					],
					'group' => esc_html__( 'Button', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Notices
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Success Notice', 'total-theme-core' ),
					'label_block' => true, // elementor param.
					'param_name' => 'notice_success',
					'group' => esc_html__( 'Notices', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Error Notice', 'total-theme-core' ),
					'label_block' => true, // elementor param.
					'param_name' => 'notice_error',
					'group' => esc_html__( 'Notices', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'notice_font_size',
					'css' => [
						'selector' => '.vcex-contact-form__notice',
						'property' => 'font-size',
					],
					'group' => esc_html__( 'Notices', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'notice_color',
					'css' => [
						'selector' => '.vcex-contact-form__notice',
						'property' => 'color',
					],
					'group' => esc_html__( 'Notices', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'notice_background',
					'css' => [
						'selector' => '.vcex-contact-form__notice',
						'property' => [ 'background', 'border-color' ],
					],
					'group' => esc_html__( 'Notices', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Loader
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Loader Icon', 'total-theme-core' ),
					'param_name' => 'loader_icon',
					'value' => $loader_options,
					'description' => esc_html__( 'Select your loader icon svg.', 'total-theme-core' ),
					'group' => esc_html__( 'Loader', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Loader Icon Color', 'total-theme-core' ),
					'param_name' => 'loader_icon_color',
					'css' => [
						'selector' => '.vcex-contact-form__spinner',
						'property' => 'color',
					],
					'group' => esc_html__( 'Loader', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Loader Icon Size', 'total-theme-core' ),
					'param_name' => 'loader_icon_size',
					'css' => [
						'selector' => '.vcex-contact-form__spinner svg',
						'property' => [ 'height', 'width' ],
					],
					'description' => self::param_description( 'font_size' ),
					'group' => esc_html__( 'Loader', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// CSS
				array(
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				),
				// Elementor Fields.
				array(
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'typography',
					'selector' => '.vcex-contact-form',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				),
			);
		}

		/**
		 * Submit form.
		 */
		public static function form_submission() {
			check_ajax_referer( 'vcex-contact-form-nonce', 'nonce' ); // security check.

			$error = '';
			$status = 'error';
			$captcha_pass = false;

			// reCAPTCHA check
			if ( ! empty( $_POST['recaptcha'] ) && function_exists( 'wpex_get_recaptcha_keys' ) ) {

				$keys = wpex_get_recaptcha_keys();

				if ( empty( $keys['site'] ) || empty( $keys['secret'] ) ) {
					$captcha_pass = true; // no keys saved.
				} else {
					$recaptcha = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . esc_attr( trim( $keys['secret'] ) ) .'&response=' . esc_attr( wp_unslash( $_POST['recaptcha'] ) ) );

					if ( empty( $recaptcha['body'] ) ) {
						$error = 'reCAPTCHA keys are most likely incorrect.';
					} else {

						$recaptcha = json_decode( $recaptcha['body'], false );

						// This is a human.
						if ( true == $recaptcha->success
							&& 0.5 <= $recaptcha->score
							&& 'vcex_contact_form' === $recaptcha->action
						) {
							$captcha_pass = true;
						}

						// Score less than 0.5 indicates suspicious activity. Return an error.
						else {
							$error = 'We don\'t take kindly to Bots around here.';
						}

					}

				}

			} else {
				$captcha_pass = true; // captcha is disabled.
			}

			if ( $captcha_pass ) {

				// If all required fields exist try and send the email.
				if ( empty( $_POST['name'] ) || empty( $_POST['email'] ) || empty( $_POST['message'] ) ) {
					$error = 'empty_fields';
				} else {
					$send_email = self::send_email( $_POST );
					if ( true === $send_email ) {
						$status = 'success';
					} else {
						$error = 'wp_mail error';
					}
				}

			}

			$response = $error ?: $status;

			header( "Content-Type: application/json" );
			echo json_encode( $response );
			wp_die();
		}

		/**
		 * Send the email.
		 */
		protected static function send_email( $data ) {
			if ( true === apply_filters( 'vcex_contact_form_demo_mode', false ) ) {
				return true;
			}

			if ( empty( $data ) || ! is_array( $data ) ) {
				return false;
			}

			$mail_to = sanitize_email( self::email_to_address( $data ) );

			if ( ! is_email( $mail_to ) ) {
				return false;
			}

			$mail_headers = [
				'Content-Type: text/html; charset=UTF-8'
			];

			$mail_subject = ( ! empty( $data['subject'] ) && 'undefined' !== $data['subject'] ) ? sanitize_text_field( $data['subject'] ) : sprintf( esc_html__( 'New contact form submission from %s', 'total-theme-core' ), get_bloginfo( 'name' ) );

			$mail_body = '';

			if ( ! empty( $data['name'] ) ) {
				$label_name = ( ! empty( $data['label_name'] ) && 'undefined' !== $data['label_name'] ) ? sanitize_text_field( $data['label_name'] ) : esc_html__( 'Your Name', 'total-theme-core' );
				$mail_body .= '<strong>' . esc_html( str_replace( ':', '', $label_name ) ) . '</strong>: ' . esc_html( $data['name'] ) . '<br>';
			}

			if ( ! empty( $data['email'] ) ) {
				if ( $sanitized_email = sanitize_email( $data['email'] ) ) {
					$reply_to = "<{$sanitized_email}>";
					if ( ! empty( $data['name'] ) ) {
						$reply_to = sanitize_text_field( $data['name'] ) . ' ' . $reply_to;
					}
					$mail_headers[] = 'Reply-To: ' . $reply_to;
				}
				$label_email = ( ! empty( $data['label_email'] ) && 'undefined' !== $data['label_email'] ) ? sanitize_text_field( $data['label_email'] ) : esc_html__( 'Your Email', 'total-theme-core' );
				$mail_body .= '<strong>' . esc_html( str_replace( ':', '', $label_email ) ) . '</strong>: ' . esc_html( $data['email'] ) . '<br>';
			}

			if ( ! empty( $data['message'] ) ) {
				$label_message = ( ! empty( $data['label_message'] ) && 'undefined' !== $data['label_message'] ) ? sanitize_text_field( $data['label_message'] ) : esc_html__( 'Message', 'total-theme-core' );
				$mail_body .= '<strong>' . esc_html( str_replace( ':', '', $label_message ) ) . '</strong>:<br />' . wpautop( wp_kses_post( stripslashes( $data['message'] ) ) );
			}

			$mail_body = (string) apply_filters( 'vcex_contact_form_mail_body', $mail_body, $data );

			if ( $mail_to && $mail_body ) {
				return wp_mail( $mail_to, $mail_subject, $mail_body, $mail_headers );
			}

		}

		/**
		 * Get the to email for the contact form.
		 */
		protected static function email_to_address( $data ): string {
			if ( ! empty( $data['post_id'] ) && is_numeric( $data['post_id'] ) ) {
				$email_address_meta_key = (string) get_theme_mod( 'contact_form_mail_to_address_meta_key' );
				if ( $email_address_meta_key ) {
					$meta_email_address = get_post_meta( absint( $data['post_id'] ), $email_address_meta_key, true );
					if ( $meta_email_address && is_email( $meta_email_address ) ) {
						$email_address = $meta_email_address;
					}
				}
			}

			if ( empty( $email_address ) ) {
				$custom_email_address = (string) get_theme_mod( 'contact_form_mail_to_address' );
				if ( $custom_email_address && is_email( $custom_email_address ) ) {
					$email_address = $custom_email_address;
				} else {
					$email_address = (string) get_bloginfo( 'admin_email' );
				}
			}

			$allowed_data = array_flip( [
				'post_id',
				'name',
				'subject',
				'email',
			] );

			$data = array_intersect_key( $data, $allowed_data );

			return (string) apply_filters( 'vcex_contact_form_mail_to_address', $email_address, $data );
		}

	}

}

new VCEX_Contact_Form_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Contact_Form' ) ) {
	class WPBakeryShortCode_Vcex_Contact_Form extends WPBakeryShortCode {}
}
