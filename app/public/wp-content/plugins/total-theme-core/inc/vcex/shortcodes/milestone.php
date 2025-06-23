<?php

defined( 'ABSPATH' ) || exit;

/**
 * Milestone Shortcode.
 */
if ( ! class_exists( 'VCEX_Milestone_Shortcode' ) ) {

	class VCEX_Milestone_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_milestone';

		/**
		 * Define shortcode name.
		 *
		 * @deprecated 4.6.0
		 */
		public $shortcode = 'vcex_milestone';

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
			return esc_html__( 'Milestone', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Animated counter', 'total-theme-core' );
		}

		/**
		 * Register scripts.
		 */
		public function scripts_to_register(): array {
			return [
				[
					'countUp',
					vcex_get_js_file( 'vendor/countUp' ),
					[],
					'1.9.3',
					true
				],
				[
					'vcex-milestone',
					vcex_get_js_file( 'frontend/milestone' ),
					[ 'countUp' ],
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
				'countUp',
				'vcex-milestone',
			];
		}

		/**
		 * Override enqueue_scripts so we only load the scripts if the element is animated.
		 */
		protected static function enqueue_scripts( array $atts ): void {
			if ( vcex_validate_att_boolean( 'animated', $atts ) ) {
				wp_enqueue_script( 'vcex-milestone' );
			}
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'description' => self::param_description( 'unique_id' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'classes',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				vcex_vc_map_add_css_animation(),
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total-theme-core'),
					'param_name' => 'animation_duration',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total-theme-core'),
					'param_name' => 'animation_delay',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total-theme-core'),
				],
				// Style
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'std' => 'plain',
					'choices' => [
						'plain'    => esc_html__( 'Plain', 'total-theme-core' ),
						'bordered' => esc_html__( 'Bordered', 'total-theme-core' ),
						'boxed'    => esc_html__( 'Boxed', 'total-theme-core' ),
					],
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
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'text_align',
					'std' => 'center',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_hover_animations',
					'heading' => esc_html__( 'Hover Animation', 'total-theme-core'),
					'param_name' => 'hover_animation',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'width',
					'css' => true,
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Aligment', 'total-theme-core' ),
					'param_name' => 'float',
					'std' => 'none',
					'exclude_choices' => [ '' ],
					'dependency' => [ 'element' => 'width', 'not_empty' => true ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background_color',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'padding',
					'heading' => esc_html__( 'Padding', 'total-theme-core' ),
					'param_name' => 'padding_all',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'shadow',
					'heading' => esc_html__( 'Shadow: Hover', 'total-theme-core' ),
					'param_name' => 'shadow_hover',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'hover_animation', 'is_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'border_style',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'css' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Number
				[
					'type' => 'textfield',
					'admin_label' => true,
					'heading' => esc_html__( 'Number', 'total-theme-core' ),
					'param_name' => 'number',
					'std' => '45',
					'description' => self::param_description( 'text' ),
					'group' => esc_html__( 'Number', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Animated', 'total-theme-core' ),
					'group' => esc_html__( 'Number', 'total-theme-core' ),
					'param_name' => 'animated',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Re-trigger animation on scroll', 'total-theme-core' ),
					'group' => esc_html__( 'Number', 'total-theme-core' ),
					'param_name' => 'animate_onscroll',
					'dependency' => [ 'element' => 'animated', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Start Value', 'total-theme-core' ),
					'param_name' => 'startval',
					'placeholder' => '0',
					'description' => esc_html__( 'Enter the number which to start counting from, if the number is greater then the value set under the number tab then the counter will count down instead of up.','total-theme-core'),
					'dependency' => [ 'element' => 'animated', 'value' => 'true' ],
					'group' => esc_html__( 'Number', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Speed', 'total-theme-core' ),
					'param_name' => 'speed',
					'placeholder' => '2500',
					'description' => esc_html__( 'The number of milliseconds it should take to finish counting.','total-theme-core'),
					'dependency' => [ 'element' => 'animated', 'value' => 'true' ],
					'group' => esc_html__( 'Number', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'std' => ',',
					'heading' => esc_html__( 'Thousand Separator', 'total-theme-core' ),
					'param_name' => 'separator',
					'dependency' => [ 'element' => 'animated', 'value' => 'true' ],
					'group' => esc_html__( 'Number', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Decimal Places', 'total-theme-core' ),
					'param_name' => 'decimals',
					'placeholder' => '0',
					'group' => esc_html__( 'Number', 'total-theme-core' ),
					'dependency' => [ 'element' => 'animated', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'placeholder' => '.',
					'heading' => esc_html__( 'Decimal Separator', 'total-theme-core' ),
					'param_name' => 'decimal_separator',
					'dependency' => [ 'element' => 'animated', 'value' => 'true' ],
					'group' => esc_html__( 'Number', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Before', 'total-theme-core' ),
					'param_name' => 'before',
					'group' => esc_html__( 'Number', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'After', 'total-theme-core' ),
					'param_name' => 'after',
					'default' => '%',
					'group' => esc_html__( 'Number', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'number_font_family',
					'css' => [ 'selector' => '.vcex-milestone-number', 'property' => 'font-family' ],
					'group' => esc_html__( 'Number', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'number_color',
					'css' => [ 'selector' => '.vcex-milestone-number', 'property' => 'color' ],
					'group' => esc_html__( 'Number', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'number_size',
					'css' => [ 'selector' => '.vcex-milestone-number', 'property' => 'font-size' ],
					'group' => esc_html__( 'Number', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'number_weight',
					'css' => [ 'selector' => '.vcex-milestone-number', 'property' => 'font-weight' ],
					'group' => esc_html__( 'Number', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'number_bottom_margin',
					'choices' => 'margin',
					'css' => [ 'selector' => '.vcex-milestone-number', 'property' => 'margin-block-end' ],
					'group' => esc_html__( 'Number', 'total-theme-core' ),
				],
				// caption
				[
					'type' => 'textfield',
					'class' => 'vcex-animated-counter-caption',
					'heading' => esc_html__( 'Caption', 'total-theme-core' ),
					'param_name' => 'caption',
					'value' => 'Awards Won',
					'admin_label' => true,
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type'  => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'caption_font_family',
					'css' => [ 'selector' => '.vcex-milestone-caption', 'property' => 'font-family' ],
					'dependency' => [ 'element' => 'caption', 'not_empty' => true ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__(  'Color', 'total-theme-core' ),
					'param_name' => 'caption_color',
					'css' => [ 'selector' => '.vcex-milestone-caption', 'property' => 'color' ],
					'dependency' => [ 'element' => 'caption', 'not_empty' => true ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'caption_size',
					'css' => [ 'selector' => '.vcex-milestone-caption', 'property' => 'font-size' ],
					'dependency' => [ 'element' => 'caption', 'not_empty' => true ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'caption_font', // @todo rename setting to font_weight
					'css' => [ 'selector' => '.vcex-milestone-caption', 'property' => 'font-weight' ],
					'dependency' => [ 'element' => 'caption', 'not_empty' => true ],
					'group' => esc_html__( 'Caption', 'total-theme-core' ),
				],
				// Icons
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Enable Icon', 'total-theme-core' ),
					'param_name' => 'enable_icon',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Icon library', 'total-theme-core' ),
					'param_name' => 'icon_type',
					'description' => esc_html__( 'For optimal site speed, it\'s strongly recommended to use the theme\'s built-in icon library or upload a custom icon.', 'total-theme-core' ),
					'value' => [
						esc_html__( 'Theme Icons', 'total-theme-core' ) => 'ticons',
						esc_html__( 'Font Awesome', 'total-theme-core' ) => 'fontawesome',
						esc_html__( 'Open Iconic', 'total-theme-core' ) => 'openiconic',
						esc_html__( 'Typicons', 'total-theme-core' ) => 'typicons',
						esc_html__( 'Entypo', 'total-theme-core' ) => 'entypo',
						esc_html__( 'Linecons', 'total-theme-core' ) => 'linecons',
						esc_html__( 'Pixel', 'total-theme-core' ) => 'pixelicons',
					],
					'dependency' => [ 'element' => 'enable_icon', 'value' => 'true' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon',
					'dependency' => [ 'element' => 'icon_type', 'value' => 'ticons' ],
					'elementor' => [
						'conditions' => [
							'terms' => [
								[
									'name' => 'enable_icon',
									'operator' => '===',
									'value' => 'true',
								],
							],
						],
					],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_fontawesome',
					'settings' => [ 'emptyIcon' => true, 'type' => 'fontawesome', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'fontawesome' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_openiconic',
					'settings' => [ 'emptyIcon' => true, 'type' => 'openiconic', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'openiconic' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_typicons',
					'settings' => [ 'emptyIcon' => true, 'type' => 'typicons', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'typicons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_entypo',
					'settings' => [ 'emptyIcon' => true, 'type' => 'entypo', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'entypo' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_linecons',
					'settings' => [ 'emptyIcon' => true, 'type' => 'linecons', 'iconsPerPage' => 100 ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'linecons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_pixelicons',
					'settings' => [ 'emptyIcon' => true, 'type' => 'pixelicons', 'source' => vcex_pixel_icons() ],
					'dependency' => [ 'element' => 'icon_type', 'value' => 'pixelicons' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Font Alternative Classes', 'total-theme-core' ),
					'param_name' => 'icon_alternative_classes',
					'dependency' => [ 'element' => 'enable_icon', 'value' => 'true' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Icon Position', 'total-theme-core' ),
					'param_name' => 'icon_position',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'std' => 'inline', // this is required for vcex_select_buttons
					'choices' => [
						'inline' => esc_html__( 'Inline', 'total-theme-core' ),
						'top' => esc_html__( 'Top', 'total-theme-core' ),
						'left' => esc_html__( 'Left', 'total-theme-core' ),
						'right' => esc_html__( 'Right', 'total-theme-core' ),
					],
					'dependency' => [ 'element' => 'enable_icon', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Spacing', 'total-theme-core' ),
					'param_name' => 'icon_spacing',
					'dependency' => [ 'element' => 'enable_icon', 'value' => 'true' ],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__(  'Color', 'total-theme-core' ),
					'param_name' => 'icon_color',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-milestone-icon', 'property' => 'color' ],
					'dependency' => [ 'element' => 'enable_icon', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Size', 'total-theme-core' ),
					'param_name' => 'icon_size',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'css' => [ 'selector' => '.vcex-milestone-icon', 'property' => 'icon_size' ],
					'dependency' => [ 'element' => 'enable_icon', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Link
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Apply Link To Entire Element?', 'total-theme-core' ),
					'param_name' => 'url_wrap',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'URL', 'total-theme-core' ),
					'param_name' => 'url',
					'description' => self::param_description( 'text' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Target', 'total-theme-core' ),
					'param_name' => 'url_target',
					'std' => 'self',
					'choices' => [
						'self' => esc_html__( 'Self', 'total-theme-core' ),
						'blank' => esc_html__( 'Blank', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Rel', 'total-theme-core' ),
					'param_name' => 'url_rel',
					'std' => '',
					'choices' => [
						'' => esc_html__( 'None', 'total-theme-core' ),
						'nofollow' => esc_html__( 'Nofollow', 'total-theme-core' ),
						'sponsored' => esc_html__( 'Sponsored', 'total-theme-core' ),
					],

					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
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

new VCEX_Milestone_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Milestone' ) ) {
	class WPBakeryShortCode_Vcex_Milestone extends WPBakeryShortCode {}
}
