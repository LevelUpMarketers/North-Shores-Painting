<?php

defined( 'ABSPATH' ) || exit;

/**
 * Heading Shortcode.
 */
if ( ! class_exists( 'VCEX_Heading_Shortcode' ) ) {

	class VCEX_Heading_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_heading';

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
			return esc_html__( 'Heading', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Advanced heading element', 'total-theme-core' );
		}

		/**
		 * Returns custom vc map settings.
		 */
		public static function get_vc_lean_map_settings(): array {
			return [
				'admin_enqueue_js' => 'heading',
				'js_view'          => 'vcexHeadingView',
			];
		}

		/**
		 * Shortcode output => Get template file and display shortcode.
		 *
		 * @todo update to use abstract method.
		 */
		public static function output( $atts, $content = null, $shortcode_tag = '' ): ?string {
			if ( ! vcex_maybe_display_shortcode( self::TAG, $atts ) ) {
				return null;
			}

			$atts = vcex_shortcode_atts( self::TAG, $atts, self::class );

			// Parses the add_css_to_inner attribute to disable it when not using certain header styles.
			if ( vcex_validate_att_boolean( 'add_css_to_inner', $atts )
				&& in_array( $atts['style'], [ 'plain', 'side-border', 'bottom-border' ], true )
			) {
				$atts['add_css_to_inner'] = true;
			} else {
				$atts['add_css_to_inner'] = false;
			}

			ob_start();

			if ( class_exists( 'TotalThemeCore\Vcex\Shortcode_CSS' ) ) {
				$shortcode_css = new TotalThemeCore\Vcex\Shortcode_CSS( self::class, $atts );
			}

			if ( isset( $shortcode_css ) ) {
				$unique_selector = $shortcode_css->get_unique_selector();

				/*** Adds extra styles that can't be added easily because of dependencies ***/
				if ( ! empty( $atts['background_color'] ) && isset( $atts['style'] ) && 'plain' === $atts['style'] ) {
					if ( $atts['add_css_to_inner'] ) {
						$selector = "{$unique_selector} .vcex-heading-inner";
					} else {
						$selector = $unique_selector;
					}
					$shortcode_css->add_css_to_array( [
						'selector' => $selector,
						'property' => 'background-color',
						'val'      => $atts['background_color'],
					] );
				}

				if ( ! empty( $atts['top_margin' ] ) && ! empty( $atts['typography_style'] ) ) {
					$shortcode_css->add_css_to_array( [
						'selector' => $unique_selector,
						'property' => 'margin-block-start',
						'val'      => $atts['top_margin'],
					] );
				}
				
				if ( ! empty( $atts['bottom_margin' ] ) && ! empty( $atts['typography_style'] ) ) {
					$shortcode_css->add_css_to_array( [
						'selector' => $unique_selector,
						'property' => 'margin-block-end',
						'val'      => $atts['bottom_margin'],
					] );
				}

				if ( ! empty( $atts['color_hover'] ) || ! empty( $atts['background_hover'] ) ) {
					if ( $atts['add_css_to_inner'] ) {
						$selector = "{$unique_selector} .vcex-heading-inner:hover";
					} else {
						$selector = "{$unique_selector}:hover";
					}

					if ( ! empty( $atts['color_hover'] ) ) {
						$shortcode_css->add_css_to_array( [
							'selector' => $selector,
							'property' => 'color',
							'val'      => $atts['color_hover'],
						] );
					}

					if ( ! empty( $atts['background_hover'] ) ) {
						$shortcode_css->add_css_to_array( [
							'selector' => $selector,
							'property' => 'background',
							'val'      => $atts['background_hover'],
						] );
					}
				}

				if ( ! empty( $atts['border_color'] ) && isset( $atts['style'] ) ) {
					if ( 'side-border' === $atts['style'] ) {
						$selector = "{$unique_selector} .vcex-heading-side-border__border";
					} else {
						$selector = $unique_selector;
					}
					$shortcode_css->add_css_to_array( [
						'selector' => $selector,
						'property' => 'border-color',
						'val'      => $atts['border_color'],
					] );
				}

				/**** End extra styles ****/
				$shortcode_style = $shortcode_css->render_style( false );
				
				if ( $shortcode_style ) {
					$atts['vcex_class'] = $shortcode_css->get_unique_classname();
				}
			}

			// Get the shortcode html.
			do_action( 'vcex_shortcode_before', self::TAG, $atts );
			include vcex_get_shortcode_template( self::TAG );
			do_action( 'vcex_shortcode_after', self::TAG, $atts );
			$html = (string) ob_get_clean();

			// Add the inline CSS before the output if the shortcode is not empty.
			if ( $html && ! empty( $shortcode_style ) ) {
				$html = "<style>{$shortcode_style}</style>{$html}";
			}

			return $html;
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return array(
				// General
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Source', 'total-theme-core' ),
					'param_name' => 'source',
					'value' => array(
						esc_html__( 'Custom Text', 'total-theme-core' ) => 'custom',
						esc_html__( 'Post Title', 'total-theme-core' ) => 'post_title',
						esc_html__( 'Post Subheading', 'total-theme-core' ) => 'post_subheading',
						esc_html__( 'Post Publish Date', 'total-theme-core' ) => 'post_date',
						esc_html__( 'Post Modified Date', 'total-theme-core' ) => 'post_modified_date',
						esc_html__( 'Post Author', 'total-theme-core' ) => 'post_author',
						esc_html__( 'Archive Title', 'total-theme-core' ) => 'archive_title',
						esc_html__( 'Current User', 'total-theme-core' ) => 'current_user',
						esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
						esc_html__( 'Term Name', 'total-theme-core' ) => 'term_name',
						esc_html__( 'Site Name', 'total-theme-core' ) => 'site_name',
						esc_html__( 'Callback Function', 'total-theme-core' ) => 'callback_function',
					),
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_custom_field',
					'choices' => 'text',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name' => 'custom_field',
					'dependency' => array( 'element' => 'source', 'value' => 'custom_field' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_callback_function',
					'heading' => esc_html__( 'Callback Function', 'total-theme-core' ),
					'param_name' => 'callback_function',
					'dependency' => array( 'element' => 'source', 'value' => 'callback_function' ),
					'description' => sprintf( esc_html__( 'Callback functions must be %swhitelisted%s for security reasons.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', '</a>' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textarea_safe',
					'heading' => esc_html__( 'Text', 'total-theme-core' ),
					'param_name' => 'text',
					'value' => esc_html__( 'Heading', 'total-theme-core' ),
					'vcex_rows' => 2,
					'description' => self::param_description( 'text_html' ),
					'dependency' => array( 'element' => 'source', 'value' => 'custom' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Badge', 'total-theme-core' ),
					'description' => self::param_description( 'text' ),
					'param_name' => 'badge',
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Badge Background', 'total-theme-core' ),
					'param_name' => 'badge_background_color',
					'css' => [
						'selector' => '.vcex-heading-badge',
						'property' => 'background-color',
					],
					'dependency' => array( 'element' => 'badge', 'not_empty' => true ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Badge Font Size', 'total-theme-core' ),
					'param_name' => 'badge_font_size',
					'css' => [
						'selector' => '.vcex-heading-badge',
						'property' => 'font-size',
					],
					'dependency' => array( 'element' => 'badge', 'not_empty' => true ),
				),
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Balance Text', 'total-theme-core' ),
					'description' => esc_html__( 'Text is wrapped in a way that best balances the number of characters on each line, enhancing layout quality and legibility. Ideal for lengthy headings that extend across multiple lines.', 'total-theme-core' ),
					'param_name' => 'text_balance',
				],
				array(
					'heading' => esc_html__( 'HTML Tag', 'total-theme-core' ),
					'admin_label' => true,
					'param_name' => 'tag',
					'type' => 'vcex_select_buttons',
					'choices' => 'html_tag',
					'description' => esc_html__( 'Used for SEO reasons only (not styling). You can select your heading font style under the "Typography" tab. The default tag for this element is a <div> but you can modify the default tag via the Customizer.', 'total-theme-core' ),
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
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
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
					'type' => 'dropdown',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'std' => 'plain',
					// @todo add filter.
					'value' => array(
						esc_html__( 'Plain', 'total-theme-core' ) => 'plain',
						esc_html__( 'Bottom Border', 'total-theme-core' ) => 'bottom-border',
						esc_html__( 'Bottom Border w/ Color', 'total-theme-core' ) => 'bottom-border-w-color',
						esc_html__( 'Side Border', 'total-theme-core' ) => 'side-border',
						esc_html__( 'Graphical', 'total-theme-core' ) => 'graphical',
					),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Top Margin', 'total-theme-core' ),
					'param_name' => 'top_margin',
					'choices' => 'margin',
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Shadow', 'total-theme-core' ),
					'param_name' => 'shadow',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => array( 'element' => 'style', 'value' => 'plain' ),
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
					'heading' => esc_html__( 'Align', 'total-theme-core' ),
					'param_name' => 'align',
					'std' => 'center',
					'dependency' => array( 'element' => 'width', 'not_empty' => true ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Float', 'total-theme-core' ),
					'param_name' => 'float',
					'description' => esc_html__( 'This is an older option that isn\'t recommended anymore as it won\'t play nicely with most page builders.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'align', 'value' => array( 'left', 'right' ) ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Add Design to Inner Span', 'total-theme-core' ),
					'param_name' => 'add_css_to_inner',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to add the background, padding, border, etc only around your text and icons and not the whole heading container.', 'total-theme-core' ),
					'dependency' => array(
						'element' => 'style',
						'value' => array( 'plain', 'side-border', 'bottom-border' )
					),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'background_color',
					'dependency' => array( 'element' => 'style', 'value' => 'plain' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'background_hover',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => array( 'element' => 'style', 'value' => 'plain' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				[
					'type' => 'vcex_select',
					'choices' => 'padding',
					'heading' => esc_html__( 'Vertical Padding', 'total-theme-core' ),
					'param_name' => 'padding_y',
					'dependency' => array( 'element' => 'style', 'value' => 'plain' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'padding',
					'heading' => esc_html__( 'Horizontal Padding', 'total-theme-core' ),
					'param_name' => 'padding_x',
					'dependency' => array( 'element' => 'style', 'value' => 'plain' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Side Margin', 'total-theme-core' ),
					'param_name' => 'border_side_margin',
					'choices' => 'margin',
					'dependency' => array( 'element' => 'style', 'value' => 'side-border' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Accent Border Color', 'total-theme-core' ),
					'param_name' => 'inner_bottom_border_color',
					'dependency' => array( 'element' => 'style', 'value' => 'bottom-border-w-color' ),
					'css' => [ 'selector' => '.vcex-heading-inner', 'property' => 'border-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'inner_bottom_border_color_main',
					'dependency' => array( 'element' => 'style', 'value' => 'bottom-border-w-color' ),
					'css' => [ 'property' => 'border-color' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Style', 'total-theme-core' ),
					'param_name' => 'border_style',
					'dependency' => array( 'element' => 'style', 'value' => array( 'bottom-border', 'side-border' ) ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Width', 'total-theme-core' ),
					'param_name' => 'border_width',
					'dependency' => array(
						'element' => 'style',
						'value' => array(
							'bottom-border',
							'side-border',
							//'bottom-border-w-color',
						)
					),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Border Color', 'total-theme-core' ),
					'param_name' => 'border_color',
					'dependency' => array(
						'element' => 'style',
						'value' => [ 'bottom-border', 'side-border' ]
					),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Border Radius', 'total-theme-core' ),
					'param_name' => 'border_radius',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => array( 'element' => 'style', 'value' => 'plain' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Typography
				array(
					'type' => 'vcex_notice',
					'param_name' => 'typo_notice',
					'text' => esc_html__( 'You can set custom styles for your this specific heading module below but you can also go to Appearance > Customize > Typography to set global styles for all your heading modules.', 'total-theme-core' ),
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Typography Style', 'total-theme-core' ),
					'param_name' => 'typography_style',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'text_align',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'color',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'color_hover',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Font Style', 'total-theme-core' ),
					'param_name' => 'italic',
					'value' => array(
						esc_html__( 'Normal', 'total-theme-core' ) => '',
						esc_html__( 'Italic', 'total-theme-core' ) => 'true',
					),
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'font_weight',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'text_transform',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'line_height',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'letter_spacing',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_min_max',
					'heading' => esc_html__( 'Min-Max Font Size (Full Width Text)', 'total-theme-core' ),
					'param_name' => 'responsive_text_min_max',
					'unit' => 'px',
					'description' => esc_html__( 'This setting allows you to define a minimum and maximum font size in pixels. Javascript will then be used to calculate an ideal font size for your text. Important: This setting works independently and will override any other predefined font size and is recommend only for very large banners/headings.', 'total-theme-core' ),
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				),
				array(
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'typography',
					'selector' => '.vcex-heading',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				),
				// Link
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Link Type', 'total-theme-core' ),
					'param_name' => 'onclick',
					'value' => array(
						esc_html__( 'None', 'total-theme-core' ) => '',
						esc_html__( 'Custom Link', 'total-theme-core' ) => 'custom_link',
						esc_html__( 'Internal Page', 'total-theme-core' ) => 'internal_link',
						esc_html__( 'Homepage', 'total-theme-core' ) => 'home',
						esc_html__( 'Current Post', 'total-theme-core' ) => 'post_permalink',
						esc_html__( 'Post Author', 'total-theme-core' ) => 'post_author',
						esc_html__( 'Scroll to Section', 'total-theme-core' ) => 'local_scroll',
						esc_html__( 'Toggle Element', 'total-theme-core' ) => 'toggle_element',
						esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Link', 'total-theme-core' ),
					'param_name' => 'onclick_url',
					'description' => self::param_description( 'text' ),
					'dependency' => [
						'element' => 'onclick',
						'value' => [ 'custom_link', 'local_scroll', 'toggle_element' ],
					],
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vc_link',
					'heading' => esc_html__( 'Internal Link', 'total-theme-core' ),
					'param_name' => 'onclick_internal_link',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => array( 'element' => 'onclick', 'value' => 'internal_link' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_custom_field',
					'choices' => 'links',
					'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
					'param_name' => 'onclick_custom_field',
					'dependency' => array( 'element' => 'onclick', 'value' => 'custom_field' ),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Target', 'total-theme-core' ),
					'param_name' => 'onclick_target',
					'std' => '_self',
					'choices' => [
						'_self'   => esc_html__( 'Self', 'total-theme-core' ),
						'_blank' => esc_html__( 'Blank', 'total-theme-core' ),
					],
					'dependency' => array(
						'element' => 'onclick',
						'value_not_equal_to' => [ '', 'local_scroll', 'toggle_element' ],
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Rel Attribute', 'total-theme-core' ),
					'param_name' => 'onclick_rel',
					'std' => '',
					'choices' => array(
						'' => esc_html__( 'None', 'total-theme-core' ),
						'nofollow' => esc_html__( 'Nofollow', 'total-theme-core' ),
						'sponsored' => esc_html__( 'Sponsored', 'total-theme-core' ),
					),
					'dependency' => array(
						'element' => 'onclick',
						'value' => [ 'custom_link', 'internal_link', 'custom_field' ],
					),
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Title Attribute', 'total-theme-core' ),
					'param_name' => 'onclick_title',
					'group' => esc_html__( 'Link', 'total-theme-core' ),
					'dependency' => [ 'element' => 'onclick', 'not_empty' => true ],
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// Icon
				array(
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Position', 'total-theme-core' ),
					'param_name' => 'icon_position',
					'std' => 'left',
					'choices' => array(
						'left' => esc_html__( 'Left', 'total-theme-core' ),
						'right' => esc_html__( 'Right', 'total-theme-core' ),
					),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Icon library', 'total-theme-core' ),
					'param_name' => 'icon_type',
					'description' => esc_html__( 'For optimal site speed, it\'s strongly recommended to use the theme\'s built-in icon library or upload a custom icon.', 'total-theme-core' ),
					'value' => array(
						esc_html__( 'Theme Icons', 'total-theme-core' ) => 'ticons',
						esc_html__( 'Font Awesome', 'total-theme-core' ) => 'fontawesome',
						esc_html__( 'Open Iconic', 'total-theme-core' ) => 'openiconic',
						esc_html__( 'Typicons', 'total-theme-core' ) => 'typicons',
						esc_html__( 'Entypo', 'total-theme-core' ) => 'entypo',
						esc_html__( 'Linecons', 'total-theme-core' ) => 'linecons',
						esc_html__( 'Material', 'total-theme-core' ) => 'material',
						esc_html__( 'Pixel', 'total-theme-core' ) => 'pixelicons',
					),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				),
				array(
					'type' => 'vcex_select_icon',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon',
					'dependency' => array( 'element' => 'icon_type', 'value' => 'ticons' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_fontawesome',
					'value' => '',
					'settings' => array( 'emptyIcon' => true, 'iconsPerPage' => 100, 'type' => 'fontawesome' ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'fontawesome' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_openiconic',
					'settings' => array( 'emptyIcon' => true, 'type' => 'openiconic', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'openiconic' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_typicons',
					'settings' => array( 'emptyIcon' => true, 'type' => 'typicons', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'typicons' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_entypo',
					'settings' => array( 'emptyIcon' => true, 'type' => 'entypo', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'entypo' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_linecons',
					'settings' => array( 'emptyIcon' => true, 'type' => 'linecons', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'linecons' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_material',
					'settings' => array( 'emptyIcon' => true, 'type' => 'material', 'iconsPerPage' => 100 ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'material' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				),
				array(
					'type' => 'iconpicker',
					'heading' => esc_html__( 'Icon', 'total-theme-core' ),
					'param_name' => 'icon_pixelicons',
					'settings' => array( 'emptyIcon' => true, 'type' => 'pixelicons', 'source' => vcex_pixel_icons() ),
					'dependency' => array( 'element' => 'icon_type', 'value' => 'pixelicons' ),
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Alternative Character', 'total-theme-core' ),
					'param_name' => 'icon_alternative_character',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'description' => self::param_description( 'text' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'icon_color',
					'css' => [
						'selector' => '.vcex-heading-icon',
						'property' => 'color',
					],
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Side Margin', 'total-theme-core' ),
					'param_name' => 'icon_side_margin',
					'choices' => 'margin',
					'group' => esc_html__( 'Icon', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				),
				// CSS
				[
					'type' => 'css_editor',
					'heading' => esc_html__( 'CSS box', 'total-theme-core' ),
					'param_name' => 'css',
					'group' => esc_html__( 'CSS', 'total-theme-core' ),
				],
				// Hidden/Deprecated fields
				[ 'type' => 'hidden', 'param_name' => 'link', 'std' => '' ],
				[ 'type' => 'hidden', 'param_name' => 'link_local_scroll', 'std' => '' ],
				[ 'type' => 'hidden', 'param_name' => 'text_accent', 'std' => '' ],
				[ 'type' => 'hidden', 'param_name' => 'hover_white_text', 'std' => '' ],
				[ 'type' => 'hidden', 'param_name' => 'hover_text_accent', 'std' => '' ],
				[ 'type' => 'hidden', 'param_name' => 'responsive_text', 'std' => '' ],
				[ 'type' => 'hidden', 'param_name' => 'min_font_size', 'std' => '' ],
				[ 'type' => 'hidden', 'param_name' => 'padding_all' ],
			);
		}

		/**
		 * Parse WPBakery attributes on edit.
		 */
		public static function vc_edit_form_fields_attributes( $atts = [] ) {
			return self::parse_deprecated_attributes( $atts );
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}

			// Convert padding_all to padding_y and padding_x
			if ( ! empty( $atts['padding_all'] ) && ( empty( $atts['padding_y'] ) && empty( $atts['padding_x'] ) ) ) {
				$atts['padding_x'] = $atts['padding_y'] = $atts['padding_all'];
				unset( $atts['padding_all'] );
			}

			// Move items to new onclick param.
			if ( ! empty( $atts['link'] ) && empty( $atts['onclick'] ) ) {
				$atts = self::upgrade_link_atts_to_onclick( $atts );
			}

			if ( ! empty( $atts['text_accent'] ) ) {
				if ( 'true' == $atts['text_accent'] ) {
					$atts['color'] = 'accent';
				}
				$atts['text_accent'] = '';
			}

			if ( isset( $atts['responsive_text'] )
				&& 'true' == $atts['responsive_text']
				&& ! empty( $atts['font_size'] )
				&& ! empty( $atts['min_font_size'] )
			) {
				$min = vcex_parse_min_max_text_font_size( $atts['min_font_size'] );
				$max = vcex_parse_min_max_text_font_size( $atts['font_size'] );
				if ( $min && $max ) {
					$atts['responsive_text_min_max'] = wp_strip_all_tags( $min . '|' . $max );
					$atts['min_font_size'] = '';
					$atts['font_size'] = '';
					$atts['responsive_text'] = '';
				}
			}

			if ( ! empty( $atts['hover_white_text'] ) && 'true' == $atts['hover_white_text'] ) {
				$atts['color_hover'] = '#ffffff';
			} elseif ( ! empty( $atts['hover_text_accent'] ) && 'true' == $atts['hover_text_accent'] ) {
				$atts['color_hover'] = 'accent';
			}

			unset( $atts['hover_white_text'] );
			unset( $atts['hover_text_accent'] );

			return $atts;
		}

		/**
		 * Parses deprecated params.
		 */
		protected static function upgrade_link_atts_to_onclick( $atts = '' ) {
			$link = vcex_build_link( $atts['link'] );

			if ( empty( $link['url'] ) ) {
				return $atts;
			}

			if ( empty( $atts['onclick_url'] ) ) {
				$atts['onclick'] = 'custom_link';
				$atts['onclick_url'] = $link['url'];
			}

			if ( isset( $atts['link_local_scroll'] )
				&& 'true' === $atts['link_local_scroll']
				&& str_starts_with( $atts['onclick_url'], '#' )
			) {
				$atts['onclick'] = 'local_scroll';
			} else {
				if ( ! empty( $link['target'] ) ) {
					$atts['onclick_target'] = $link['target'];
				}
				if ( ! empty( $link['rel'] ) && ( 'nofollow' === $link['rel'] ) ) {
					$atts['onclick_rel'] = $link['rel'];
				}
			}

			if ( isset( $link['title'] ) ) {
				$atts['onclick_title'] = $link['title'];
			}

			unset( $atts['link'] );

			return $atts;
		}

	}

}

new VCEX_Heading_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Heading' ) ) {
	class WPBakeryShortCode_Vcex_Heading extends WPBakeryShortCode {
		protected function outputTitle( $title ) {
			$icon = $this->settings( 'icon' );
			$title = VCEX_Heading_Shortcode::get_title();
			return '<h4 class="wpb_element_title"><i class="vc_general vc_element-icon' . ( ! empty( $icon ) ? ' ' . $icon : '' ) . '" aria-hidden="true"></i><span class="wpb_element_title_vcex_text" data-title="' . esc_attr( $title ) . '">' . esc_html( $title ) . '</span></h4>';
		}
	}
}
