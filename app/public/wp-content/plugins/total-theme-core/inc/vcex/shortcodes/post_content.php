<?php

defined( 'ABSPATH' ) || exit;

/**
 * Post Content Shortcode.
 */
if ( ! class_exists( 'VCEX_Post_Content_Shortcode' ) ) {

	class VCEX_Post_Content_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_post_content';

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
			return esc_html__( 'Post Content', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display your post content', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Max Width', 'total-theme-core' ),
					'param_name' => 'width',
					'css' => [ 'property' => 'max-width' ],
					'description' => self::param_description( 'width' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Remove Last Bottom Margin', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to remove the bottom margin on the last element of the content block (usually a paragraph).', 'total-theme-core' ),
					'param_name' => 'remove_last_mb',
					'std' => 'false',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Enable Sidebar', 'total-theme-core' ),
					'param_name' => 'sidebar',
					'std' => 'false',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Sidebar Position', 'total-theme-core' ),
					'param_name' => 'sidebar_position',
					'value' => [
						esc_html__( 'Right', 'total-theme-core' ) => 'right',
						esc_html__( 'Left', 'total-theme-core' ) => 'left',
					],
					'dependency' => [ 'element' => 'sidebar', 'value' => 'true' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Post Blocks', 'total-theme-core' ),
					'description' => esc_html__( 'By default the post content element only displays the post content. Enable this setting to insert other post blocks. This setting is essentially deprecated since you can use other builder elements to insert post blocks and customize them.', 'total-theme-core' ),
					'param_name' => 'enable_blocks',
					'std' => 'false',
					'editors' => [ 'wpbakery' ],
				],
				[
					'type' => 'vcex_sorter',
					'heading' => esc_html__( 'Blocks', 'total-theme-core' ),
					'param_name' => 'blocks',
					'std' => 'the_content',
					'admin_label' => true,
					'choices' => apply_filters( 'vcex_post_content_blocks', [
						'the_content'    => esc_html__( 'The Content', 'total-theme-core' ),
						'featured_media' => esc_html__( 'Featured Media', 'total-theme-core' ),
						'title'          => esc_html__( 'Title', 'total-theme-core' ),
						'meta'           => esc_html__( 'Meta', 'total-theme-core' ),
						'series'         => esc_html__( 'Series', 'total-theme-core' ),
						'social_share'   => esc_html__( 'Social Share', 'total-theme-core' ),
						'author_bio'     => esc_html__( 'Author Bio', 'total-theme-core' ),
						'related'        => esc_html__( 'Related Posts', 'total-theme-core' ),
						'comments'       => esc_html__( 'Comments', 'total-theme-core' ),
					] ),
					'dependency' => [ 'element' => 'enable_blocks', 'value' => 'true' ],
					'group' => esc_html__( 'Blocks', 'total-theme-core' ),
					'editors' => [ 'wpbakery' ],
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
				// Typography.
				[
					'type' => 'vcex_notice',
					'param_name' => 'vcex_notice__typo',
					'text' => esc_html__( 'The following settings are applied to the post content only.', 'total-theme-core' ),
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'color',
					'css' => [
						'selector' => '.vcex-post-content-c',
					],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'css' => [
						'selector' => '.vcex-post-content-c',
					],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'description' => esc_html__( 'Applies to the content block only.', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => [
						'selector' => '.vcex-post-content-c',
					],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'line_height',
					'css' => [
						'selector' => '.vcex-post-content-c',
					],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'letter_spacing',
					'css' => [
						'selector' => '.vcex-post-content-c',
					],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'typography',
					'selector' => '.vcex-post-content-c',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
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

		/**
		 * Parse WPBakery attributes on edit.
		 */
		public static function vc_edit_form_fields_attributes( $atts = [] ) {
			$atts = self::parse_deprecated_attributes( $atts );

			// Enable blocks - this is a fix for v1.8 when we introduced the setting.
			// This is only needed in the vc editor.
			if ( isset( $atts['blocks'] ) && is_string( $atts['blocks'] ) && str_contains( $atts['blocks'], ',' ) ) {
				$atts['enable_blocks'] = 'true';
			}

			return $atts;
		}

		/**
		 * Parses deprecated params.
		 */
		public static function parse_deprecated_attributes( $atts = [] ) {
			if ( empty( $atts ) || ! is_array( $atts ) ) {
				return $atts;
			}

			// Fallback for when blocks were added in 1.2.
			// @important - This works on the front-end but causes issues in the VC editor because the blocks element has "the_content" as std but it's fine.
			if ( empty( $atts['blocks'] ) ) {
				$settings_to_check = [
					'post_series',
					'the_content',
					'social_share',
					'author_bio',
					'related',
					'comments',
				];
				foreach ( $settings_to_check as $setting ) {
					if ( 'the_content' === $setting ) {
						$blocks[] = $setting; // added here to keep correct order and it was always enabled.
					} elseif ( isset( $atts[ $setting ] ) && vcex_validate_boolean( $atts[ $setting ] ) ) {
						$blocks[] = $setting;
					}
				}
				if ( $blocks ) {
					$atts['blocks'] = \implode( ',', $blocks );
				}
			}

			return $atts;
		}

	}

}

new VCEX_Post_Content_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Post_Content' ) ) {
	class WPBakeryShortCode_Vcex_Post_Content extends WPBakeryShortCode {}
}
