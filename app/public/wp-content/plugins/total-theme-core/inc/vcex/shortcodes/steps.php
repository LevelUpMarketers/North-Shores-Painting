<?php

defined( 'ABSPATH' ) || exit;

/**
 * Steps Shortcode.
 */
if ( ! class_exists( 'Vcex_Steps_Shortcode' ) ) {

	class Vcex_Steps_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_steps';

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
		public static function get_title() {
			return esc_html__( 'Steps', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display numerical steps', 'total-theme-core' );
		}

		/**
		 * Register scripts.
		 */
		public function scripts_to_register(): array {
			return [
				[
					'vcex-steps-accordion',
					vcex_get_js_file( 'frontend/steps-accordion' ),
					[],
					TTC_VERSION,
					true
				],
			];
		}

		/**
		 * Return script dependencies.
		 */
		public static function get_script_depends(): array {
			return [
				'vcex-steps-accordion',
			];
		}

		/**
		 * Override enqueue_scripts so we only load the scripts if the element is animated.
		 */
		protected static function enqueue_scripts( array $atts ): void {
			if ( isset( $atts['direction'] )
				&& 'vertical' === $atts['direction']
				&& vcex_validate_att_boolean( 'accordion', $atts )
			) {
				wp_enqueue_script( 'vcex-steps-accordion' );
			}
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$default = [
				[
					'heading' => 'Donec tempus quam',
					'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit quis libero efficitur.',
				],
				[
					'heading' => 'Donec tempus quam',
					'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit quis libero efficitur.',
				],
				[
					'heading' => 'Donec tempus quam',
					'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit quis libero efficitur.',
				],
			];
			return [
				[
					'type' => 'param_group',
					'param_name' => 'steps',
					'group' => esc_html__( 'Steps', 'total-theme-core' ),
					'value' => urlencode( json_encode( $default ) ),
					'params' => [
						[
							'type' => 'textfield',
							'heading' => esc_html__( 'Heading', 'total-theme-core' ),
							'param_name' => 'heading',
							'admin_label' => true,
							'elementor' => [
								'default' => 'Donec tempus quam',
							],
							'editors' => [ 'wpbakery', 'elementor' ],
						],
						[
							'type' => 'textarea',
							'heading' => esc_html__( 'Text', 'total-theme-core' ),
							'param_name' => 'text',
							'admin_label' => false,
							'elementor' => [
								'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit quis libero efficitur.',
							],
							'editors' => [ 'wpbakery', 'elementor' ],
						],
					],
					'elementor' => [
						'title_field' => '{{{ heading }}}',
						'default' => $default,
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// General
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
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
					'param_name' => 'classes',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				vcex_vc_map_add_css_animation(),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
					'editors' => [ 'wpbakery' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
					'editors' => [ 'wpbakery' ],
				),
				// Style
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Direction', 'total-theme-core' ),
					'param_name' => 'direction',
					'std' => 'horizontal',
					'choices' => [
						'horizontal' => esc_html__( 'Horizontal', 'total-theme-core' ),
						'vertical' => esc_html__( 'Vertical', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'gap',
					'heading' => esc_html__( 'Horizontal Gap', 'total-theme-core' ),
					'param_name' => 'gap_x',
					'description' => esc_html__( 'Select the horizontal gap between steps.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'direction', 'value' => 'horizontal' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'gap',
					'heading' => esc_html__( 'Vertical Gap', 'total-theme-core' ),
					'param_name' => 'gap_y',
					'std' => '40px',
					'description' => esc_html__( 'Select the vertical gap between steps.', 'total-theme-core' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Accordion', 'total-theme-core' ),
					'param_name' => 'accordion',
					'std' => 'false',
					'description' => esc_html__( 'Hides all the steps excerpt the first one so that they are displayed when clicked on. If enabled it\'s recommended to go to the Headings tab and add a Minimum Height for your headings.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'direction', 'value' => 'vertical' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Animation Speed', 'total-theme-core' ),
					'param_name' => 'accordion_animation_duration',
					'choices' => [
						'' => esc_html__( 'No Animation', 'total-theme-core' ),
						'75ms' => '75',
						'100ms' => '100',
						'150ms' => '150',
						'200ms' => '200',
						'300ms' => '300',
						'400ms' => '400',
						'500ms' => '500',
						'600ms' => '600',
						'700ms' => '700',
						'1000ms' => '1000',
					],
					'dependency' => array( 'element' => 'accordion', 'value' => 'true' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Breakpoint', 'total-theme-core' ),
					'param_name' => 'breakpoint',
					'description' => esc_html__( 'Browser width at which point the element becomes horizontal.', 'total-theme-core' ),
					'choices' => 'breakpoint',
					'dependency' => [ 'element' => 'direction', 'value' => 'horizontal' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Number Style', 'total-theme-core' ),
					'param_name' => 'symbol_style',
					'std' => 'solid',
					'choices' => [
						'solid' => esc_html__( 'Solid', 'total-theme-core' ),
						'outline' => esc_html__( 'Outline', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Line Width', 'total-theme-core' ),
					'param_name' => 'line_width',
					'std' => '1px',
					'choices' => [
						'1px' => '1px',
						'2px' => '2px',
						'3px' => '3px',
						'4px' => '4px',
						'5px' => '5px',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Center Items', 'total-theme-core' ),
					'param_name' => 'center',
					'std' => 'false',
					'description' => esc_html__( 'Centers the items when displayed horizontally.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'direction', 'value' => 'horizontal' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Numbers
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Accent Color', 'total-theme-core' ),
					'param_name' => 'accent',
					'css' => [ 'selector' => '.vcex-steps-item__figure', 'property' => '--wpex-accent' ],
					'group' => esc_html__( 'Numbers', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Number Text Color', 'total-theme-core' ),
					'param_name' => 'symbol_color',
					'css' => [ 'selector' => '.vcex-steps-item__symbol', 'property' => 'color' ],
					'group' => esc_html__( 'Numbers', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Separator Line Color', 'total-theme-core' ),
					'param_name' => 'line_color',
					'css' => [ 'selector' => '.vcex-steps-item__line', 'property' => '--wpex-accent' ],
					'group' => esc_html__( 'Numbers', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Dimensions', 'total-theme-core' ),
					'param_name' => 'symbol_dims',
					'css' => [ 'selector' => '.vcex-steps-item__symbol', 'property' => [ 'width', 'height' ] ],
					'placeholder' => '3em',
					'description' => esc_html__( 'Used to set the width and height of each number. Using an em value is recommended so that it adjusts automatically according to your font size, however, if you are using a vertical design you may want to use a fixed value equal to that of the minimum height added for your headings.', 'total-theme-core' ),
					'group' => esc_html__( 'Numbers', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'figure_margin',
					'description' => esc_html__( 'Select the margin between the number and the content.', 'total-theme-core' ),
					'group' => esc_html__( 'Numbers', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Stacked Margin', 'total-theme-core' ),
					'param_name' => 'figure_margin_stacked',
					'dependency' => [ 'element' => 'direction', 'value' => 'horizontal' ],
					'description' => esc_html__( 'Select the margin between the number and the content when stacked.', 'total-theme-core' ),
					'group' => esc_html__( 'Numbers', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'symbol_font_family',
					'css' => [ 'selector' => '.vcex-steps-item__symbol', 'property' => 'font-family' ],
					'group' => esc_html__( 'Numbers', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'symbol_font_size',
					'css' => [ 'selector' => '.vcex-steps-item__symbol', 'property' => 'font-size', ],
					'group' => esc_html__( 'Numbers', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'symbol_font_weight',
					'choices' => 'font_weight',
					'css' => [ 'selector' => '.vcex-steps-item__symbol', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Numbers', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'symbol_typography',
					'selector' => '.vcex-steps-item__symbol',
					'group' => esc_html__( 'Numbers', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Headings
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'HTML Tag', 'total-theme-core' ),
					'param_name' => 'heading_tag',
					'std' => 'div',
					'choices' => [
						'div'  => 'div',
						'h2'   => 'h2',
						'h3'   => 'h3',
						'h4'   => 'h4',
						'h5'   => 'h5',
					],
					'group' => esc_html__( 'Headings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Minimum Height', 'total-theme-core' ),
					'param_name' => 'heading_min_height',
					'std' => '',
					'css' => [ 'selector' => '.vcex-steps-item__heading', 'property' => 'min-height' ],
					'description' => esc_html__( 'You can give your headings a minium height to help align it vertically with the icon.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'direction', 'value' => 'vertical' ],
					'group' => esc_html__( 'Headings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'heading_color',
					'css' => [ 'selector' => '.vcex-steps-item__heading', 'property' => 'color' ],
					'group' => esc_html__( 'Headings', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'heading_font_family',
					'css' => [ 'selector' => '.vcex-steps-item__heading', 'property' => 'font-family' ],
					'group' => esc_html__( 'Headings', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Heading Font Size', 'total-theme-core' ),
					'param_name' => 'heading_font_size',
					'css' => [ 'selector' => '.vcex-steps-item__heading', 'property' => 'font-size' ],
					'group' => esc_html__( 'Headings', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'heading_font_weight',
					'choices' => 'font_weight',
					'css' => [ 'selector' => '.vcex-steps-item__heading', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Headings', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'heading_typography',
					'selector' => '.vcex-steps-item__heading',
					'group' => esc_html__( 'Headings', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Text
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Margin', 'total-theme-core' ),
					'param_name' => 'text_margin',
					'description' => esc_html__( 'Select the margin between the heading and the text.', 'total-theme-core' ),
					'group' => esc_html__( 'Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'text_color',
					'css' => [ 'selector' => '.vcex-steps-item__text', 'property' => 'color' ],
					'group' => esc_html__( 'Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'text_font_family',
					'css' => [ 'selector' => '.vcex-steps-item__text', 'property' => 'font-family' ],
					'group' => esc_html__( 'Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Content Font Size', 'total-theme-core' ),
					'param_name' => 'text_font_size',
					'css' => [ 'selector' => '.vcex-steps-item__text', 'property' => 'font-size' ],
					'group' => esc_html__( 'Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'text_font_weight',
					'choices' => 'font_weight',
					'css' => [ 'selector' => '.vcex-steps-item__text', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Text', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'text_typography',
					'selector' => '.vcex-steps-item__text',
					'group' => esc_html__( 'Text', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
			];
		}

	}

}

new Vcex_Steps_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Steps' ) ) {
	class WPBakeryShortCode_Vcex_Steps extends WPBakeryShortCode {}
}
