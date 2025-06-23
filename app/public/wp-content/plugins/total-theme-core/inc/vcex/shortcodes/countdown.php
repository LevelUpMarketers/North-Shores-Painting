<?php

defined( 'ABSPATH' ) || exit;

/**
 * Countdown Shortcode.
 */
if ( ! class_exists( 'VCEX_Countdown_Shortcode' ) ) {

	class VCEX_Countdown_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_countdown';

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
			return esc_html__( 'Countdown', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Animated countdown clock', 'total-theme-core' );
		}

		/**
		 * Register scripts.
		 */
		public function scripts_to_register(): array {
			return [
				[
					'countdown',
					vcex_get_js_file( 'vendor/countdown' ),
					[ 'jquery' ],
					'2.1.0',
					true
				],
				/*
				@todo implement Locale.
				[
					'moment-with-locales',
					vcex_get_js_file( 'vendor/moment-with-locales' ),
					[ 'jquery' ],
					'2.10.0',
					true
				],
				*/
				[
					'moment-timezone-with-data',
					vcex_get_js_file( 'vendor/moment-timezone-with-data' ),
					[ 'jquery' ],
					'2.10.0',
					true
				],
				[
					'vcex-countdown',
					vcex_get_js_file( 'frontend/countdown' ),
					[ 'jquery', 'countdown' ],
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
				'countdown',
				'moment',
			//	'moment-with-locales',
				'moment-timezone-with-data',
				'vcex-countdown',
			];
		}

		/**
		 * Enqueue scripts.
		 */
		protected static function enqueue_scripts( array $atts ): void {
			wp_enqueue_script( 'countdown' );
			/* @todo implement locale
			if ( ! empty( $atts['locale'] ) ) {
				wp_enqueue_script( 'moment-with-locales' );
			}*/
			if ( ! empty( $atts['timezone'] ) || ( isset( $atts['source'] ) && str_starts_with( $atts['source'], 'just_events_' ) ) ) {
				if ( empty( $atts['locale'] ) ) {
					wp_enqueue_script( 'moment' );
				}
				wp_enqueue_script( 'moment-timezone-with-data' ); // MUST load after moment.
			}
			wp_enqueue_script( 'vcex-countdown' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$source = [
				'' => esc_html__( 'Manual Input', 'total-theme-core' ),
				'custom_field' => esc_html__( 'Custom Field', 'total-theme-core' ),
			];

			if ( class_exists( 'Just_Events\Plugin', false ) ) {
				$source['just_events_start'] = esc_html__( 'Event Start Date', 'total-theme-core' );
				$source['just_events_end'] = esc_html__( 'Event End Date', 'total-theme-core' );
			}

			return [
				// General
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Source', 'total-theme-core' ),
					'param_name' => 'source',
					'choices' => $source,
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'date',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name' => 'custom_field',
					'admin_label' => true,
					'dependency' => [ 'element' => 'source', 'value' => 'custom_field' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'moment_js_timezones',
					'chosen_select' => true,
					'heading' => esc_html__( 'Time Zone', 'total-theme-core' ),
					'param_name' => 'timezone',
					'description' => esc_html__( 'If a time zone is not selected the time zone will be based on the visitors computer time.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'source', 'value_not_equal_to' => [ 'just_events_start', 'just_events_end' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'End Month', 'total-theme-core' ),
					'param_name' => 'end_month',
					'value' => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12' ],
					'admin_label' => true,
					'dependency' => [ 'element' => 'source', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'End Day', 'total-theme-core' ),
					'param_name' => 'end_day',
					'value' => [ '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12', '13' => '13', '14' => '14', '15' => '15', '16' => '16', '17' => '17', '18' => '18', '19' => '19', '20' => '20', '21' => '21', '22' => '22', '23' => '23', '24' => '24', '25' => '25', '26' => '26', '27' => '27', '28' => '28', '29' => '29', '30' => '30', '31' => '31' ],
					'admin_label' => true,
					'dependency' => [ 'element' => 'source', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'End Year', 'total-theme-core' ),
					'param_name' => 'end_year',
					'admin_label' => true,
					'dependency' => [ 'element' => 'source', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'End Time', 'total-theme-core' ),
					'param_name' => 'end_time',
					'description' => esc_html__( 'Enter your custom end time in military format. Example if your event starts at 1:30pm enter 13:30', 'total-theme-core' ),
					'dependency' => [ 'element' => 'source', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'el_class',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core' ),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core' ),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
				],
				// Style
				[
					'type' => 'vcex_select',
					'std' => 'inline',
					'heading' => esc_html__( 'Design', 'total-theme-core' ),
					'param_name' => 'style',
					'choices' => [
						'inline'  => esc_html__( 'Inline', 'total-theme-core' ),
						'outline' => esc_html__( 'Outline', 'total-theme-core' ),
						'boxed'   => esc_html__( 'Boxed', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'checkbox',
					'heading' => esc_html__( 'Items', 'total-theme-core' ),
					'param_name' => 'items',
					'std' => 'days,hours,minutes,seconds',
					'value' => [
						esc_html__( 'Days', 'total-theme-core' ) => 'days',
						esc_html__( 'Hours', 'total-theme-core' ) => 'hours',
						esc_html__( 'Minutes', 'total-theme-core' ) => 'minutes',
						esc_html__( 'Seconds', 'total-theme-core' ) => 'seconds',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'param_name' => 'labels_inline',
					'heading' => esc_html__( 'Labels Next to Numbers', 'total-theme-core' ),
					'dependency' => [ 'element' => 'style', 'value' => [ 'outline', 'boxed' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Main styling
				[
					'type' => 'vcex_subheading',
					'param_name' => 'vcex_subheading_main_style',
					'text' => esc_html__( 'Main Style', 'total-theme-core' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background_color',
					'css' => true,
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
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
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
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'border_style',
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
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				// Items Style
				[
					'type' => 'vcex_subheading',
					'param_name' => 'vcex_subheading__items_style',
					'text' => esc_html__( 'Items Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'style', 'value' => [ 'outline', 'boxed' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'breakpoint',
					'heading' => esc_html__( 'Stack Items Breakpoint', 'total-theme-core' ),
					'param_name' => 'stack_breakpoint',
					'dependency' => [ 'element' => 'style', 'value' => [ 'outline', 'boxed' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Stretch Items Horizontally', 'total-theme-core' ),
					'param_name' => 'stretch_items',
					'dependency' => [ 'element' => 'style', 'value' => [ 'outline', 'boxed' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'item_background',
					'css' => [
						'selector' => '.vcex-countdown__item',
						'property' => 'background-color',
					],
					'dependency' => [ 'element' => 'style', 'value' => [ 'outline', 'boxed' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'item_shadow',
					'choices' => 'shadow',
					'dependency' => [ 'element' => 'style', 'value' => [ 'outline', 'boxed' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'item_border_color',
					'css' => [
						'selector' => '.vcex-countdown__item',
						'property' => 'border-color',
					],
					'dependency' => [ 'element' => 'style', 'value' => [ 'outline' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'item_border_width',
					'choices' => 'border_width',
					'css' => [
						'selector' => '.vcex-countdown__item',
						'property' => 'border-width',
					],
					'dependency' => [ 'element' => 'style', 'value' => [ 'outline' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'item_padding',
					'css' => [
						'selector' => '.vcex-countdown__item',
						'property' => 'padding',
					],
					'description' => self::param_description( 'padding' ),
					'dependency' => [ 'element' => 'style', 'value' => [ 'outline', 'boxed' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'item_width',
					'css' => [
						'selector' => '.vcex-countdown__item',
						'property' => 'width',
					],
					'description' => self::param_description( 'px' ),
					'dependency' => [ 'element' => 'style', 'value' => [ 'outline', 'boxed' ] ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Typography
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'text_align',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
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
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Font Style', 'total-theme-core' ),
					'param_name' => 'italic',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'value' => [
						esc_html__( 'Normal', 'total-theme-core' ) => '',
						esc_html__( 'Italic', 'total-theme-core' ) => 'true',
					],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'font_weight',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'line_height',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'letter_spacing',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'typography',
					'selector' => '.vcex-countdown',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Labels
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Days', 'total-theme-core' ),
					'param_name' => 'days',
					'group' =>  esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Hours', 'total-theme-core' ),
					'param_name' => 'hours',
					'group' =>  esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Minutes', 'total-theme-core' ),
					'param_name' => 'minutes',
					'group' =>  esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Seconds', 'total-theme-core' ),
					'param_name' => 'seconds',
					'group' =>  esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'label_color',
					'css' => [ 'selector' => '.vcex-countdown__label', 'property' => 'color' ],
					'group' =>  esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'label_font_size',
					'css' => [ 'selector' => '.vcex-countdown__label', 'property' => 'font-size' ],
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Style', 'total-theme-core' ),
					'param_name' => 'label_italic',
					'choices' => 'font_style',
					'css' => [ 'selector' => '.vcex-countdown__label', 'property' => 'font_style' ],
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'label_font_weight',
					'choices' => 'font_weight',
					'css' => [ 'selector' => '.vcex-countdown__label', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'label_typography',
					'selector' => '.vcex-countdown__label',
					'group' => esc_html__( 'Labels', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
			];
		}

	}

}

new VCEX_Countdown_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Countdown' ) ) {
	class WPBakeryShortCode_Vcex_Countdown extends WPBakeryShortCode {}
}
