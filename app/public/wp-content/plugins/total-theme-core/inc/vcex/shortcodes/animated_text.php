<?php

defined( 'ABSPATH' ) || exit;

/**
 * Animated Text Shortcode.
 */
if ( ! class_exists( 'Vcex_Animated_Text_Shortcode' ) ) {
	class Vcex_Animated_Text_Shortcode extends \TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_animated_text';

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
			return esc_html__( 'Animated Typed Text', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Animated text', 'total-theme-core' );
		}

		/**
		 * Register scripts.
		 */
		public function scripts_to_register(): array {
			return [
				[
					'typed',
					vcex_get_js_file( 'vendor/typed' ),
					[],
					'2.0.12',
					true
				],
				[
					'vcex-animated-text',
					vcex_get_js_file( 'frontend/animated-text' ),
					[ 'typed' ],
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
				'typed',
				'vcex-animated-text',
			];
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$default = [
				[ 'text' => 'Welcome' ],
				[ 'text' => 'Bienvenido' ],
				[ 'text' => 'Welkom' ],
				[ 'text' => 'Bienvenue' ],
			];
			return [
				// General
				[
					'type' => 'param_group',
					'param_name' => 'strings',
					'heading' => esc_html__( 'Strings', 'total-theme-core' ),
					'value' => urlencode( json_encode( $default ) ),
					'params' => [
						[
							'type' => 'textfield',
							'heading' => esc_html__( 'Text', 'total-theme-core' ),
							'param_name' => 'text',
							'admin_label' => true,
							'editors' => [ 'wpbakery', 'elementor' ],
							'elementor' => [
								'default' => 'Lorem ipsum dolor sit amet',
							],
						],
					],
					'elementor' => [
						'title_field' => '{{{ text }}}',
						'default' => $default,
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
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
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background_color',
					'css' => [ 'property' => 'background' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'padding_all',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'border_style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'border_width', 'not_empty' => true ],
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'css' => true,
					'dependency' => [ 'element' => 'border_style', 'not_empty' => true ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				// Typography
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Tag', 'total-theme-core' ),
					'param_name' => 'tag',
					'choices' => 'html_tag',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'text_align',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'heading' => esc_html__( 'Font Style', 'total-theme-core' ),
					'param_name' => 'font_style',
					'type' => 'vcex_select_buttons',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'color',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'param_name' => 'font_weight',
					'css' => true,
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'param_name' => 'typography',
					'selector' => '.vcex-animated-text',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Animated Text
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Loop', 'total-theme-core' ),
					'param_name' => 'loop',
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Cursor', 'total-theme-core' ),
					'param_name' => 'type_cursor',
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Speed', 'total-theme-core' ),
					'param_name' => 'speed',
					'placeholder' => '40',
					'description' => esc_html__( 'Enter a value in milliseconds.', 'total-theme-core' ),
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Back Delay', 'total-theme-core' ),
					'param_name' => 'back_delay',
					'placeholder' => '500',
					'description' => esc_html__( 'Enter a value in milliseconds.', 'total-theme-core' ),
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Back Speed', 'total-theme-core' ),
					'param_name' => 'back_speed',
					'placeholder' => '0',
					'description' => esc_html__( 'Enter a value in milliseconds.', 'total-theme-core' ),
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Start Delay', 'total-theme-core' ),
					'param_name' => 'start_delay',
					'placeholder' => '0',
					'description' => esc_html__( 'Enter a value in milliseconds.', 'total-theme-core' ),
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type'  => 'textfield',
					'heading' => esc_html__( 'Fixed Width', 'total-theme-core' ),
					'param_name' => 'animated_span_width',
					'css' => [
						'selector' => '.vcex-typed-text-inner',
						'property' => 'width', // can't be max-width.
					],
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
					'description' => esc_html__( 'Enter a custom width to keep the animated container fixed. Useful when adding custom background or static text after the animated text.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type'  => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'animated_text_align',
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-typed-text-inner', 'property' => 'text-align' ],
					'dependency' => [ 'element' => 'animated_span_width', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Text Color', 'total-theme-core' ),
					'param_name' => 'animated_color',
					'css' => [ 'selector' => '.vcex-typed-text-inner', 'property' => 'color' ],
					'dependency' => [ 'element' => 'static_text', 'value' => 'true' ],
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'animated_background_color',
					'css' => [ 'selector' => '.vcex-typed-text-inner', 'property' => 'background' ],
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'animated_font_family',
					'css' => [ 'selector' => '.vcex-typed-text-inner', 'property' => 'font-family' ],
					'dependency' => [ 'element' => 'static_text', 'value' => 'true' ],
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'param_name' => 'animated_font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-typed-text-inner', 'property' => 'font-weight' ],
					'dependency' => [ 'element' => 'static_text', 'value' => 'true' ],
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'animated_line_height',
					'choices' => 'line_height',
					'css' => [ 'selector' => '.vcex-typed-text-inner', 'property' => 'line-height' ],
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
				],
				[
					'heading' => esc_html__( 'Font Style', 'total-theme-core' ),
					'param_name' => 'animated_font_style',
					'type' => 'vcex_select_buttons',
					'choices' => 'font_style',
					'css' => [ 'selector' => '.vcex-typed-text-inner', 'property' => 'font-style' ],
					'dependency' => [ 'element' => 'static_text', 'value' => 'true' ],
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Text Decoration', 'total-theme-core' ),
					'param_name' => 'animated_text_decoration',
					'choices' => 'text_decoration',
					'css' => [ 'selector' => '.vcex-typed-text-inner', 'property' => 'text-decoration' ],
					'dependency' => [ 'element' => 'static_text', 'value' => 'true' ],
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'animated_padding',
					'choices' => 'padding',
					'css' => [ 'selector' => '.vcex-typed-text-inner', 'property' => 'padding' ],
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'animated_typography',
					'selector' => '.vcex-typed-text-inner',
					'group' => esc_html__( 'Animated Text', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Static Text
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Enable', 'total-theme-core' ),
					'param_name' => 'static_text',
					'group' => esc_html__( 'Static Text', 'total-theme-core' ),
					'std' => 'false',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Before', 'total-theme-core' ),
					'param_name' => 'static_before',
					'group' => esc_html__( 'Static Text', 'total-theme-core' ),
					'description' => self::param_description( 'text' ),
					'dependency' => [ 'element' => 'static_text', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'After', 'total-theme-core' ),
					'param_name' => 'static_after',
					'group' => esc_html__( 'Static Text', 'total-theme-core' ),
					'description' => self::param_description( 'text' ),
					'dependency' => [ 'element' => 'static_text', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'Outer CSS', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'Inner CSS', 'total-theme-core' ),
					'param_name' => 'animated_css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
			];
		}

	}
}

new Vcex_Animated_Text_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_vcex_animated_text' ) ) {
	class WPBakeryShortCode_vcex_animated_text extends WPBakeryShortCode {}
}
