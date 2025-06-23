<?php

defined( 'ABSPATH' ) || exit;

/**
 * Skillbar Shortcode.
 */
if ( ! class_exists( 'VCEX_Skillbar_Shortcode' ) ) {

	class VCEX_Skillbar_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_skillbar';

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
			return esc_html__( 'Percentage/Skill Bar', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Animated percentage bar', 'total-theme-core' );
		}

		/**
		 * Register scripts.
		 */
		public function scripts_to_register(): array {
			return [
				[
					'vcex-skillbar',
					vcex_get_js_file( 'frontend/skillbar' ),
					[],
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
				'vcex-skillbar',
			];
		}

		/**
		 * Override enqueue_scripts so we only load the scripts if the element is animated.
		 */
		protected static function enqueue_scripts( array $atts ): void {
			if ( vcex_validate_att_boolean( 'animate_percent', $atts ) ) {
				wp_enqueue_script( 'vcex-skillbar' );
			}
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				// General
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Text', 'total-theme-core' ),
					'param_name' => 'title',
					'admin_label' => true,
					'value' => 'Web Design',
					'description' => self::param_description( 'text' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Percentage Source', 'total-theme-core' ),
					'param_name' => 'source',
					'value' => [
						esc_html__( 'Custom', 'total-theme-core' ) => 'custom',
						esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
						esc_html__( 'Callback Function', 'total-theme-core' ) => 'callback_function',
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_custom_field',
					'choices' => 'percent',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name' => 'custom_field',
					'dependency' => [ 'element' => 'source', 'value' => 'custom_field' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_callback_function',
					'heading' => esc_html__( 'Callback Function', 'total-theme-core' ),
					'param_name' => 'callback_function',
					'dependency' => [ 'element' => 'source', 'value' => 'callback_function' ],
					'description' => sprintf( esc_html__( 'Callback functions must be %swhitelisted%s for security reasons.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', '</a>' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Percentage', 'total-theme-core' ),
					'param_name' => 'percentage',
					'placeholder' => '70',
					'dependency' => [ 'element' => 'source', 'value' => 'custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Animate', 'total-theme-core' ),
					'param_name' => 'animate_percent',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Re-trigger animation on scroll', 'total-theme-core' ),
					'param_name' => 'animate_percent_onscroll',
					'dependency' => [ 'element' => 'animate_percent', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Display Percentage', 'total-theme-core' ),
					'param_name' => 'show_percent',
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
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'classes',
					'description' => self::param_description( 'el_class' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'css' => true,
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				],
				// Style
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'choices' => [
						'' => esc_html__( 'Default', 'total-theme-core' ),
						'alt-1' => esc_html__( 'Title Above', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'border_radius',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Bar Background Color', 'total-theme-core' ),
					'param_name' => 'color',
					'css' => [
						'selector' => '.vcex-skillbar-bar',
						'property' => 'background-color',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Label Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Label Color', 'total-theme-core' ),
					'param_name' => 'label_color',
					'css' => [
						'selector' => '.vcex-skillbar-title',
						'property' => 'color',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Percentage Font Size', 'total-theme-core' ),
					'param_name' => 'percentage_font_size',
					'css' => [
						'selector' => '.vcex-skill-bar-percent',
						'property' => 'font-size',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Percentage Color', 'total-theme-core' ),
					'param_name' => 'percentage_color',
					'css' => [
						'selector' => '.vcex-skill-bar-percent',
						'property' => 'color',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Container Background', 'total-theme-core' ),
					'param_name' => 'background',
					'css' => [
						'selector' => '.vcex-skillbar',
						'property' => 'background-color',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Container Inset Shadow', 'total-theme-core' ),
					'param_name' => 'box_shadow',
					'dependency' => [ 'element' => 'style', 'is_empty' => true ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Container Height', 'total-theme-core' ),
					'param_name' => 'container_height',
					'placeholder' => '3em',
					'css' => [
						'selector' => '.vcex-skillbar',
						'property' => 'height',
					],
					'description' => self::param_description( 'height' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Container Left Padding', 'total-theme-core' ),
					'param_name' => 'container_padding_left',
					'description' => self::param_description( 'padding' ),
					'css' => [
						'selector' => '.vcex-skillbar-title',
						'property' => 'padding-inline-start',
					],
					'dependency' => [ 'element' => 'style', 'is_empty' => true ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Icon
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Display Icon', 'total-theme-core' ),
					'param_name' => 'show_icon',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Icon library', 'total-theme-core' ),
					'param_name' => 'icon_type',
					'value' => [
						esc_html__( 'Theme Icons', 'total-theme-core' ) => 'ticons',
						esc_html__( 'Font Awesome', 'total-theme-core' ) => 'fontawesome',
						esc_html__( 'Open Iconic', 'total-theme-core' ) => 'openiconic',
						esc_html__( 'Typicons', 'total-theme-core' ) => 'typicons',
						esc_html__( 'Entypo', 'total-theme-core' ) => 'entypo',
						esc_html__( 'Linecons', 'total-theme-core' ) => 'linecons',
						esc_html__( 'Pixel', 'total-theme-core' ) => 'pixelicons',
					],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_icon', 'value' => 'true' ],
				],
				[
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon',
					'dependency' => [ 'element' => 'icon_type', 'value' => 'ticons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_fontawesome',
					'settings' => [ 'emptyIcon' => true, 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'fontawesome' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_openiconic',
					'settings' => [
						'emptyIcon' => true,
						'iconsPerPage' => 100,
						'type' => 'openiconic',
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'openiconic' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_typicons',
					'settings' => [
						'emptyIcon' => true,
						'iconsPerPage' => 100,
						'type' => 'typicons',
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'typicons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_entypo',
					'settings' => [
						'emptyIcon' => false,
						'type' => 'entypo',
						'iconsPerPage' => 300,
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'entypo' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_linecons',
					'settings' => [
						'emptyIcon' => true,
						'iconsPerPage' => 100,
						'type' => 'linecons',
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'linecons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_pixelicons',
					'settings' => [
						'emptyIcon' => true,
						'iconsPerPage' => 100,
						'type' => 'pixelicons',
						'source' => vcex_pixel_icons(),
					],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'pixelicons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Side Margin', 'total-theme-core' ),
					'param_name' => 'icon_margin',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'dependency' => [ 'element' => 'show_icon', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
			];
		}

		/**
		 * Parses the shortcode atts.
		 *
		 * Used to parse the percentage value before sending it to css_pre_render to avoid doing it twice.
		 */
		public static function shortcode_atts( $atts ) {
			$atts = \vcex_shortcode_atts( self::TAG, $atts, self::class );

			if ( ! empty( $atts['source'] ) && 'custom' !== $atts['source'] ) {
				$atts['percentage'] = vcex_get_source_value( $atts['source'], $atts );
			} else {
				$atts['percentage'] = ( ! empty( $atts['percentage'] ) || '0' === $atts['percentage'] ) ? sanitize_text_field( $atts['percentage'] ) : '70';
			}

			if ( $atts['percentage'] || '0' === $atts['percentage'] ) {
				$atts['percentage'] = intval( vcex_parse_text_safe( (string) $atts['percentage'] ) );
				if ( $atts['percentage'] > 100 ) {
					$atts['percentage'] = 100;
				}
			}

			return $atts;
		}

		/**
		 * Advanced CSS.
		 */
		protected static function css_pre_render( $css, $atts = [] ): void {
			if ( ! empty( $atts['font_size'] ) ) {
				if ( empty( $atts['style'] ) || 'default' === $atts['style'] ) {
					$font_size_selector = '.vcex-skillbar';
				} else {
					$font_size_selector = '.vcex-skillbar-title';
				}
				$css->add_extra_css( [
					'selector' => $font_size_selector,
					'property' => 'font-size',
					'val'      => $atts['font_size'],
				] );
			}
			if ( ! empty( $atts['percentage'] ) && ! vcex_validate_att_boolean( 'animate_percent', $atts, true ) ) {
				$css->add_extra_css( [
					'selector' => '.vcex-skillbar-bar',
					'property' => 'width',
					'val'      => intval( $atts['percentage'] ) . '%',
				] );
			}
		}

	}

}

new VCEX_Skillbar_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Skillbar' ) ) {
	class WPBakeryShortCode_Vcex_Skillbar extends WPBakeryShortCode {}
}
