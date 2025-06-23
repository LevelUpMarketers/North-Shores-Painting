<?php

defined( 'ABSPATH' ) || exit;

/**
 * Post Meta Shortcode.
 */
if ( ! class_exists( 'VCEX_Post_Meta_Shortcode' ) ) {

	class VCEX_Post_Meta_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_post_meta';

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
			return esc_html__( 'Post Meta', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Author, date, comments, etc', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				// Sections
				[
					'type' => 'param_group',
					'param_name' => 'sections',
					'value' => urlencode( json_encode( [
						[
							'type' => 'date',
							'icon' => 'calendar-o',
						],
						[
							'type' => 'author',
							'icon' => 'user-o',
						],
						[
							'type' => 'comments',
							'icon' => 'comment-o',
						],
						[
							'type' => 'post_terms',
							'taxonomy' => 'category',
							'fist_only' => 'false',
							'icon' => 'folder-o',
						],
					] ) ),
					'elementor' => [
						'title_field' => '{{{ type }}}',
						'default' => [
							[
								'type' => 'date',
								'icon' => [
									'library' => 'ticon',
									'value' => 'ticon ticon-calendar-o',
								],
							],
							[
								'type' => 'author',
								'icon' => [
									'library' => 'ticon',
									'value' => 'ticon ticon-user-o',
								],
							],
							[
								'type' => 'comments',
								'icon' => [
									'library' => 'ticon',
									'value' => 'ticon ticon-comment-o',
								],
							],
							[
								'type' => 'post_terms',
								'taxonomy' => 'category',
								'fist_only' => 'false',
								'icon' => [
									'library' => 'ticon',
									'value' => 'ticon ticon-folder-o',
								],
							],
						],
					],
					'params' => [
						[
							'type' => 'dropdown',
							'heading' => esc_html__( 'Section', 'total-theme-core' ),
							'param_name' => 'type',
							'admin_label' => true,
							'value' => apply_filters( 'vcex_post_meta_sections', [
								esc_html__( 'Date', 'total-theme-core' ) => 'date',
								esc_html__( 'Author', 'total-theme-core' ) => 'author',
								esc_html__( 'Author Avatar + Name', 'total-theme-core' ) => 'author_w_avatar',
								esc_html__( 'Comments', 'total-theme-core' ) => 'comments',
								esc_html__( 'Post Terms', 'total-theme-core' ) => 'post_terms',
								esc_html__( 'Last Updated', 'total-theme-core' ) => 'modified_date',
								esc_html__( 'Estimated Read Time', 'total-theme-core' ) => 'estimated_read_time',
								esc_html__( 'Custom Field', 'total-theme-core' ) => 'custom_field',
								esc_html__( 'Callback Function', 'total-theme-core' ) => 'callback',
						 	] ),
							'editors' => [ 'wpbakery', 'elementor' ],
						],
						[
							'type' => 'textfield',
							'heading' => esc_html__( 'Label', 'total-theme-core' ),
							'param_name' => 'label',
							'editors' => [ 'wpbakery', 'elementor' ],
						],
						[
							'type' => 'vcex_select',
							'heading' => esc_html__( 'Taxonony', 'total-theme-core' ),
							'param_name' => 'taxonomy',
							'choices' => 'taxonomy',
							'dependency' => [ 'element' => 'type', 'value' => 'post_terms' ],
							'editors' => [ 'wpbakery', 'elementor' ],
						],
						[
							'type' => 'textfield',
							'heading' => esc_html__( 'Date Format', 'total-theme-core' ),
							'param_name' => 'date_format',
							'dependency' => [ 'element' => 'type', 'value' => [ 'date', 'last_modified' ] ],
							'description' => sprintf( esc_html__( 'Enter your preferred date format according to the %sWordPress manual%s.', 'total-theme-core' ), '<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank" rel="noopener noreferrer">', '</a>' ),
							'editors' => [ 'wpbakery', 'elementor' ],
						],
						[
							'type' => 'vcex_custom_field',
							'choices' => 'text',
							'heading' => esc_html__( 'Custom Field ID', 'total-theme-core' ),
							'param_name' => 'custom_field_name',
							'dependency' => [ 'element' => 'type', 'value' => 'custom_field' ],
							'editors' => [ 'wpbakery', 'elementor' ],
						],
						[
							'type' => 'vcex_select_callback_function',
							'heading' => esc_html__( 'Callback Function', 'total-theme-core' ),
							'param_name' => 'callback_function',
							'dependency' => [ 'element' => 'type', 'value' => 'callback' ],
							'description' => sprintf( esc_html__( 'Callback functions must be %swhitelisted%s for security reasons.', 'total-theme-core' ), '<a href="https://totalwptheme.com/docs/how-to-whitelist-callback-functions-for-elements/" target="_blank" rel="noopener noreferrer">', '</a>' ),
						],
						[
							'type' => 'vcex_text',
							'heading' => esc_html__( 'Avatar Size', 'total-theme-core' ),
							'param_name' => 'avatar_size',
							'dependency' => [ 'element' => 'type', 'value' => 'author_w_avatar' ],
							'placeholder' => '25',
							'editors' => [ 'wpbakery', 'elementor' ],
						],
						[
							'type' => 'vcex_ofswitch',
							'std' => 'false',
							'heading' => esc_html__( 'Enable Link', 'total-theme-core' ),
							'param_name' => 'has_link',
							'dependency' => [ 'element' => 'type', 'value' => 'comments' ],
							'editors' => [ 'wpbakery', 'elementor' ],
						],
						[
							'type' => 'dropdown',
							'heading' => esc_html__( 'Icon library', 'total-theme-core' ),
							'param_name' => 'icon_type',
							'description' => esc_html__( 'For optimal site speed, it\'s strongly recommended to use the theme\'s built-in icon library or upload a custom icon.', 'total-theme-core' ),
							'dependency' => [ 'element' => 'type', 'value_not_equal_to' => 'author_w_avatar' ],
							'value' => [
								esc_html__( 'Theme Icons', 'total-theme-core' )  => 'ticons',
								esc_html__( 'Font Awesome', 'total-theme-core' ) => 'fontawesome',
								esc_html__( 'Typicons', 'total-theme-core' )     => 'typicons',
							],
						],
						[
							'type' => 'vcex_select_icon',
							'heading' => esc_html__( 'Icon', 'total-theme-core' ),
							'param_name' => 'icon',
							'dependency' => [ 'element' => 'icon_type', 'value' => 'ticons' ],
							'elementor' => [ 'dependency' => '' ],
							'editors' => [ 'wpbakery', 'elementor' ],
						],
						[
							'type' => 'iconpicker',
							'heading' => esc_html__( 'Icon', 'total-theme-core' ),
							'param_name' => 'icon_fontawesome',
							'settings' => [ 'emptyIcon' => true, 'iconsPerPage' => 100 ],
							'dependency' => [ 'element' => 'icon_type', 'value' => 'fontawesome' ],
						],
						[
							'type' => 'iconpicker',
							'heading' => esc_html__( 'Icon', 'total-theme-core' ),
							'param_name' => 'icon_typicons',
							'settings' => [ 'emptyIcon' => true, 'type' => 'typicons', 'iconsPerPage' => 100 ],
							'dependency' => [ 'element' => 'icon_type', 'value' => 'typicons' ],
						],
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Label Colon', 'total-theme-core' ),
					'param_name' => 'label_colon',
					'description' => esc_html__( 'Add a colon automatically after the custom labels.', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'font_weight',
					'heading' => esc_html__( 'Label Font Weight', 'total-theme-core' ),
					'param_name' => 'label_font_weight',
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
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'param_name' => 'el_class',
					'description' => self::param_description( 'el_class' ),
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
					'type' => 'vcex_select_buttons',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'style',
					'choices' => [
						'' => esc_html__( 'Default', 'total-theme-core' ),
						'vertical' => esc_html__( 'Vertical', 'total-theme-core' ),
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Separator', 'total-theme-core' ),
					'param_name' => 'separator',
					'value' => [
						esc_html__( 'Empty Space', 'total-theme-core' ) => 'empty_space',
						esc_html__( 'Dot', 'total-theme-core' ) => 'dot',
						esc_html__( 'Dash', 'total-theme-core' ) => 'dash',
						esc_html__( 'Long Dash', 'total-theme-core' ) => 'long_dash',
						esc_html__( 'Forward Slash', 'total-theme-core' ) => 'forward_slash',
						esc_html__( 'Backslash', 'total-theme-core' ) => 'backslash',
						esc_html__( 'Pipe', 'total-theme-core' ) => 'pipe',
					],
					'dependency' => [ 'element' => 'style', 'value_not_equal_to' => 'vertical' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin', // can't name it margin_bottom due to WPBakery parsing issue
					'admin_label' => true,
					'group' => esc_html__( 'Style', 'total-theme-core' ),

				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Gutter', 'total-theme-core' ),
					'param_name' => 'gutter',
					'css' => [ 'property' => '--wpex-meta-gutter' ],
					'description' => esc_html__( 'Alters the space between the meta items.', 'total-theme-core' ) . ' ' . self::param_description( 'margin' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Icon Margin', 'total-theme-core' ),
					'param_name' => 'icon_margin',
					'css' => [ 'property' => '--wpex-meta-icon-margin' ],
					'description' => self::param_description( 'margin' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Width', 'total-theme-core' ),
					'param_name' => 'max_width',
					'css' => [ 'property' => 'width' ],
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Aligment', 'total-theme-core' ),
					'std' => 'center',
					'param_name' => 'float', // can't use "align" because it was already taken for the text align.
					'dependency' => [ 'element' => 'max_width', 'not_empty' => true ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Typography
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'align',
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
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'text_transform',
					'css' => true,
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'typography',
					'selector' => '.vcex-post-meta',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'elementor' ],
				],
				// Links.
				[
					'type' => 'vcex_subheading',
					'text' => esc_html__( 'Links', 'total-theme-core' ),
					'param_name' => 'vcex_subheading__links',
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Link Color', 'total-theme-core' ),
					'param_name' => 'link_color',
					'css' => [ 'selector' => 'a', 'property' => 'color' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Link Underline', 'total-theme-core' ),
					'param_name' => 'link_underline',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'Underline', 'total-theme-core' ) => 'underline',
						esc_html__( 'No underline', 'total-theme-core' ) => 'none',
					],
					'css' => [ 'selector' => 'a', 'property' => 'text-decoration' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Link Underline: Hover', 'total-theme-core' ),
					'param_name' => 'link_underline_hover',
					'value' => [
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'Underline', 'total-theme-core' ) => 'underline',
						esc_html__( 'No underline', 'total-theme-core' ) => 'none',
					],
					'css' => [ 'selector' => 'a:hover',  'property' => 'text-decoration' ],
					'group' => esc_html__( 'Typography', 'total-theme-core' ),
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

new VCEX_Post_Meta_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Post_Meta' ) ) {
	class WPBakeryShortCode_Vcex_Post_Meta extends WPBakeryShortCode {}
}
