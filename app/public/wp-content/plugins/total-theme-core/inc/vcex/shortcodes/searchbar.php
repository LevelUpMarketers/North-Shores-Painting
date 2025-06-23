<?php

defined( 'ABSPATH' ) || exit;

/**
 * Searchbar Shortcode.
 */
if ( ! class_exists( 'VCEX_Searchbar_Shortcode' ) ) {

	class VCEX_Searchbar_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_searchbar';

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
			return esc_html__( 'Search Bar', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Custom search form', 'total-theme-core' );
		}

		/**
		 * Register scripts.
		 */
		public function scripts_to_register(): array {
			return [
				[
					'vcex-searchbar-clear',
					vcex_get_js_file( 'frontend/searchbar-clear' ),
					[],
					TTC_VERSION,
					true
				]
			];
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				// General
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Aria Label', 'total-theme-core' ),
					'param_name' => 'aria_label',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Search Role Landmark', 'total-theme-core' ),
					'param_name' => 'role_landmark',
					'std' => 'false',
					'editors' => [ 'wpbakery', 'elementor' ],
					'description' => esc_html__( 'When enabled the form will include the role="search" aria landmark.', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
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
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
					'css' => true,
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core' ),
					'css' => true,
				],
				// Query
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Advanced Search', 'total-theme-core' ),
					'param_name' => 'advanced_query',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Example: ', 'total-theme-core' ) . 'post_type=portfolio&taxonomy=portfolio_category&term=advertising' . '<br>' . esc_html__( 'You can use term=current_term and author=current_author for dynamic templates.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Action URL', 'total-theme-core' ),
					'param_name' => 'action',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Enter a custom URL for the form action attribute. Leave empty to use the default WordPress search.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Custom Input Name', 'total-theme-core' ),
					'param_name' => 'input_name',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Enter a custom action name if your site is using a custom search parameter instead of "?s=".', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Layout > Widths
				[
					'type' => 'vcex_text',
					'placeholder' => 'auto',
					'heading' => esc_html__( 'Element Width', 'total-theme-core' ),
					'param_name' => 'wrap_width',
					'description' => esc_html__( 'Applied to the parent element.', 'total-theme-core' ),
					'group' => esc_html__( 'Layout', 'total-theme-core' ),
					'css' => [ 'property' => 'width' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Aligment', 'total-theme-core' ),
					'param_name' => 'wrap_float',
					'std' => 'none',
					'exclude_choices' => [ '' ],
					'dependency' => [ 'element' => 'wrap_width', 'not_empty' => true ],
					'group' => esc_html__( 'Layout', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Gap Between Input & Button', 'total-theme-core' ),
					'param_name' => 'gap',
					'dependency' => [ 'element' => 'has_button', 'value' => 'true' ],
					'description' => esc_html__( 'Spacing between elements. Default is 20px.', 'total-theme-core' ),
					'group' => esc_html__( 'Layout', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Stack at Breakpoint', 'total-theme-core' ),
					'dependency' => [ 'element' => 'has_button', 'value' => 'true' ],
					'param_name' => 'fullwidth_mobile',
					'group' => esc_html__( 'Layout', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Breakpoint', 'total-theme-core' ),
					'param_name' => 'breakpoint',
					'dependency' => [ 'element' => 'fullwidth_mobile', 'value' => 'true' ],
					'group' => esc_html__( 'Layout', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Input Width', 'total-theme-core' ),
					'param_name' => 'input_width',
					'description' => self::param_description( 'width' ),
					'dependency' => [ 'element' => 'has_button', 'value' => 'true' ],
					'group' => esc_html__( 'Layout', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Button Width', 'total-theme-core' ),
					'param_name' => 'button_width',
					'description' => self::param_description( 'width' ),
					'dependency' => [ 'element' => 'has_button', 'value' => 'true' ],
					'group' => esc_html__( 'Layout', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Input
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Autofocus', 'total-theme-core' ),
					'param_name' => 'autofocus',
					'admin_label' => true,
					'description' => esc_html__( 'Enable to add the autofocus attribute to the search field so that it\'s focused when the page loads. Note: If your searchbar appears below the fold this will cause the page to "jump".', 'total-theme-core' ),
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'dependency' => [ 'element' => 'display', 'value' => 'inline' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Clear Button', 'total-theme-core' ),
					'param_name' => 'has_clear',
					'description' => esc_html__( 'Enable to display a clear (x) button when typing so you can clear the search field.', 'total-theme-core' ),
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Reload on Clear', 'total-theme-core' ),
					'param_name' => 'clear_reload',
					'dependency' => [ 'element' => 'has_clear', 'value' => 'true' ],
					'description' => esc_html__( 'Enable to reload the page without a search query when the clear button is clicked.', 'total-theme-core' ),
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Show Search Query', 'total-theme-core' ),
					'param_name' => 'auto_fill',
					'description' => esc_html__( 'Enable to fill the search bar with the currently searched terms when shown on the search results page.', 'total-theme-core' ),
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Placeholder', 'total-theme-core' ),
					'param_name' => 'placeholder',
					'placeholder' => esc_html__( 'Keywords...', 'total-theme-core' ),
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Placeholder Color', 'total-theme-core' ),
					'param_name' => 'placeholder_color',
					'css' => [ 'selector' => '.vcex-searchbar-input::placeholder', 'property' => 'color' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'input_background_color',
					'css' => [ 'selector' => '.vcex-searchbar-input', 'property' => 'background' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'input_color',
					'css' => [ 'selector' => ':is(.vcex-searchbar-input,.vcex-searchbar-clear)', 'property' => 'color' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'input_font_size',
					'css' => [ 'selector' => '.vcex-searchbar-form', 'property' => 'font-size' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'input_letter_spacing',
					'choices' => 'letter_spacing',
					'css' => [ 'selector' => '.vcex-searchbar-form', 'property' => 'letter-spacing' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'input_text_transform',
					'choices' => 'text_transform',
					'css' => [ 'selector' => '.vcex-searchbar-form', 'property' => 'text-transform' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'input_font_weight',
					'css' => [ 'selector' => '.vcex-searchbar-form', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'input_border_radius',
					'choices' => 'border_radius',
					'css' => [ 'selector' => '.vcex-searchbar-input', 'property' => 'border-radius' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_width',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'input_border_width',
					'css' => [ 'selector' => '.vcex-searchbar-input', 'property' => 'border-width' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'input_border_color',
					'css' => [ 'selector' => '.vcex-searchbar-input', 'property' => 'border-color' ],
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'input_padding',
					'css' => [ 'selector' => '.vcex-searchbar-input', 'property' => 'padding' ],
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Input', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'input_typo',
					'selector' => '.vcex-searchbar-input',
					'group' => esc_html__( 'Input', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Submit
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Submit Button', 'total-theme-core' ),
					'param_name' => 'has_button',
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Button Text', 'total-theme-core' ),
					'param_name' => 'button_text',
					'placeholder' => esc_html__( 'Search', 'total-theme-core' ),
					'dependency' => [ 'element' => 'has_button', 'value' => 'true' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'button_text_transform',
					'choices' => 'text_transform',
					'css' => [ 'selector' => '.vcex-searchbar-button', 'property' => 'text-transform' ],
					'dependency' => [ 'element' => 'has_button', 'value' => 'true' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'button_font_weight',
					'css' => [ 'selector' => '.vcex-searchbar-button', 'property' => 'font-weight' ],
					'dependency' => [ 'element' => 'has_button', 'value' => 'true' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'button_font_size',
					'css' => [ 'selector' => '.vcex-searchbar-button', 'property' => 'font-size' ],
					'dependency' => [ 'element' => 'has_button', 'value' => 'true' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'button_letter_spacing',
					'choices' => 'letter_spacing',
					'css' => [ 'selector' => '.vcex-searchbar-button', 'property' => 'letter-spacing' ],
					'dependency' => [ 'element' => 'has_button', 'value' => 'true' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'button_border_radius',
					'choices' => 'border_radius',
					'css' => [ 'selector' => '.vcex-searchbar-button', 'property' => 'border-radius' ],
					'dependency' => [ 'element' => 'has_button', 'value' => 'true' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'button_bg',
					'css' => [ 'selector' => '.vcex-searchbar-button', 'property' => 'background' ],
					'dependency' => [ 'element' => 'has_button', 'value' => 'true' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'button_bg_hover',
					'css' => [ 'selector' => '.vcex-searchbar-button:hover', 'property' => 'background' ],
					'dependency' => [ 'element' => 'has_button', 'value' => 'true' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'button_color',
					'css' => [ 'selector' => '.vcex-searchbar-button', 'property' => 'color' ],
					'dependency' => [ 'element' => 'has_button', 'value' => 'true' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'button_color_hover',
					'css' => [ 'selector' => '.vcex-searchbar-button:hover', 'property' => 'color' ],
					'dependency' => [ 'element' => 'has_button', 'value' => 'true' ],
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_trbl',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'button_padding',
					'css' => [ 'selector' => '.vcex-searchbar-button', 'property' => 'padding' ],
					'description' => self::param_description( 'padding' ),
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'button_typo',
					'selector' => '.vcex-searchbar-button',
					'group' => esc_html__( 'Submit', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'Input CSS', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
			];
		}

	}

}

new VCEX_Searchbar_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Searchbar' ) ) {
	class WPBakeryShortCode_Vcex_Searchbar extends WPBakeryShortCode {}
}
