<?php

defined( 'ABSPATH' ) || exit;

/**
 * Next & Previous Posts Shortcode.
 */
if ( ! class_exists( 'VCEX_Post_Next_Prev_Shortcode' ) ) {

	class VCEX_Post_Next_Prev_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_post_next_prev';

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
			return esc_html__( 'Next/Previous Post Links', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Display next/prev post buttons', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			return [
				[
					'type' => 'vcex_select_buttons',
					'std' => 'icon',
					'heading' => esc_html__( 'Link Format', 'total-theme-core' ),
					'param_name' => 'link_format',
					'choices' => [
						'icon' => esc_html__( 'Icon Only', 'total-theme-core' ),
						'title' => esc_html__( 'Post Name', 'total-theme-core' ),
						'custom' => esc_html__( 'Custom Text', 'total-theme-core' ),
						'card' => esc_html__( 'Card', 'total-theme-core' ),
					],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Card Style', 'total-theme-core' ),
					'param_name' => 'card_style',
					'dependency' => [ 'element' => 'link_format', 'value' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'std' => 'chevron',
					'heading' => esc_html__( 'Arrows Style', 'total-theme-core' ),
					'param_name' => 'icon_style',
					'choices' => [
						'chevron' => esc_html__( 'Chevron', 'total-theme-core' ),
						'chevron-circle' => esc_html__( 'Chevron Circle', 'total-theme-core' ),
						'angle' => esc_html__( 'Angle', 'total-theme-core' ),
						'angle-double' => esc_html__( 'Double Angle', 'total-theme-core' ),
						'arrow' => esc_html__( 'Arrow', 'total-theme-core' ),
						'long-arrow' => esc_html__( 'Long Arrow', 'total-theme-core' ),
						'caret' => esc_html__( 'Caret', 'total-theme-core' ),
						'arrow-circle' => esc_html__( 'Cirle', 'total-theme-core' ),
						'ios' => esc_html__( 'iOS', 'total-theme-core' ),
						'none' => esc_html__( 'None', 'total-theme-core' ),
					],
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Icon Size', 'total-theme-core' ),
					'param_name' => 'icon_size',
					'css' => [ 'selector' => '.vcex-post-next-prev__icon', 'property' => 'font-size' ],
					'description' => self::param_description( 'font_size' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Arrow Side Margin', 'total-theme-core' ),
					'param_name' => 'icon_margin',
					'dependency' => [ 'element' => 'link_format', 'value' => [ 'title', 'custom' ] ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Previous Text', 'total-theme-core' ),
					'param_name' => 'previous_link_custom_text',
					'placeholder' => esc_html__( 'Previous', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value' => 'custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text',
					'heading' => esc_html__( 'Next Text', 'total-theme-core' ),
					'param_name' => 'next_link_custom_text',
					'placeholder' => esc_html__( 'Next', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value' => 'custom' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Previous Link', 'total-theme-core' ),
					'param_name' => 'previous_link',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'true',
					'heading' => esc_html__( 'Next Link', 'total-theme-core' ),
					'param_name' => 'next_link',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Infinite Loop', 'total-theme-core' ),
					'param_name' => 'loop',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Reverse Order', 'total-theme-core' ),
					'param_name' => 'reverse_order',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'In Same Term?', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to display next and previous posts from the same terms as the current post (category, tag or custom).', 'total-theme-core' ),
					'param_name' => 'in_same_term',
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'taxonomy',
					'heading' => esc_html__( 'Same Term Taxonomy', 'total-theme-core' ),
					'description' => esc_html__( 'Leave this field empty to let the theme automatically detect the primary taxonomy for the post type.', 'total-theme-core' ),
					'param_name' => 'same_term_tax',
					'dependency' => [ 'element' => 'in_same_term', 'value' => 'true' ],
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
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
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
					'param_name' => 'float', // can't use "align" because it's already taken for the Text Align.
					'dependency' => [ 'element' => 'max_width', 'not_empty' => true ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Gap', 'total-theme-core' ),
					'param_name' => 'grid_gap',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'dropdown',
					'std' => 'sm',
					'heading' => esc_html__( 'Stacking Breakpoint', 'total-theme-core' ),
					'param_name' => 'grid_bk',
					'value' => [
						esc_html__( 'sm - 640px', 'total-theme-core' ) => 'sm',
						esc_html__( 'md - 768px', 'total-theme-core' ) => 'md',
						esc_html__( 'lg - 1024px', 'total-theme-core' ) => 'lg',
						esc_html__( 'xl - 1280px', 'total-theme-core' ) => 'xl',
						esc_html__( 'None (no stacking)', 'total-theme-core' ) => 'none',
					],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Displace Links', 'total-theme-core' ),
					'param_name' => 'expand',
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to place the left item on the far left and the right item on the far right.', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_text_align',
					'heading' => esc_html__( 'Text Align', 'total-theme-core' ),
					'param_name' => 'align',
					'dependency' => [ 'element' => 'expand', 'value' => 'false' ],
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_select',
					'choices' => 'margin',
					'heading' => esc_html__( 'Button Spacing', 'total-theme-core' ),
					'param_name' => 'spacing',
					'description' => esc_html__( 'Margin applied to each button. If you want a 10px spacing between your buttons select 5px.', 'total-theme-core' ),
					'group' => esc_html__( 'Style', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				// Buttons
				[
					'type' => 'vcex_button_styles',
					'heading' => esc_html__( 'Style', 'total-theme-core' ),
					'param_name' => 'button_style',
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_button_colors',
					'heading' => esc_html__( 'Preset Color', 'total-theme-core' ),
					'param_name' => 'button_color',
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background', 'total-theme-core' ),
					'param_name' => 'button_background',
					'css' => [
						'selector' => '.vcex-post-next-prev__link',
						'property' => 'background-color',
					],
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Background: Hover', 'total-theme-core' ),
					'param_name' => 'button_background_hover',
					'css' => [
						'selector' => '.vcex-post-next-prev__link:hover',
						'property' => 'background-color',
					],
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color', 'total-theme-core' ),
					'param_name' => 'button_color_custom',
					'css' => [
						'selector' => '.vcex-post-next-prev__link',
						'property' => 'color',
					],
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_colorpicker',
					'heading' => esc_html__( 'Color: Hover', 'total-theme-core' ),
					'param_name' => 'button_color_hover',
					'css' => [
						'selector' => '.vcex-post-next-prev__link:hover',
						'property' => 'color',
					],
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_font_size',
					'heading' => esc_html__( 'Font Size', 'total-theme-core' ),
					'param_name' => 'font_size',
					'css' => true,
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
				],
				[
					'type' => 'vcex_font_family_select',
					'heading' => esc_html__( 'Font Family', 'total-theme-core' ),
					'param_name' => 'font_family',
					'css' => true,
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
				],
				[
					'type' => 'dropdown',
					'heading' => esc_html__( 'Font Style', 'total-theme-core' ),
					'param_name' => 'italic',
					'value' => [
						esc_html__( 'Normal', 'total-theme-core' ) => '',
						esc_html__( 'Italic', 'total-theme-core' ) => 'true',
					],
					'css' => true,
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Font Weight', 'total-theme-core' ),
					'param_name' => 'font_weight',
					'css' => true,
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
				],
				[
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Text Transform', 'total-theme-core' ),
					'param_name' => 'text_transform',
					'css' => true,
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Line Height', 'total-theme-core' ),
					'param_name' => 'line_height',
					'css' => true,
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card'],
				],
				[
					'type' => 'vcex_preset_textfield',
					'heading' => esc_html__( 'Letter Spacing', 'total-theme-core' ),
					'param_name' => 'letter_spacing',
					'css' => true,
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
				],
				[
					'type' => 'textfield',
					'heading' => esc_html__( 'Min-Width', 'total-theme-core' ),
					'param_name' => 'button_min_width',
					'css' => [
						'selector' => '.vcex-post-next-prev__link',
						'property' => 'min-width',
					],
					'description' => self::param_description( 'width' ),
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'vcex_ofswitch',
					'heading' => esc_html__( 'Disable Underline', 'total-theme-core' ),
					'param_name' => 'button_no_underline',
					'std' => 'false',
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'button_style', 'value' => 'plain-text' ],
					'editors' => [ 'wpbakery', 'elementor' ],
				],
				[
					'type' => 'typography',
					'heading' => esc_html__( 'Typography', 'total-theme-core' ),
					'param_name' => 'typography',
					'selector' => '.vcex-post-next-prev__link',
					'group' => esc_html__( 'Buttons', 'total-theme-core' ),
					'dependency' => [ 'element' => 'link_format', 'value_not_equal_to' => 'card' ],
					'editors' => [ 'elementor' ],
				]
			];
		}

		/**
		 * Helper function returns the next or previous post when loop is enabled and we are at the start or end.
		 *
		 * This function is similar to get_adjacent_post()
		 * @link https://developer.wordpress.org/reference/functions/get_adjacent_post/
		 */
		public static function get_first_last_post( $which, $in_same_term, $same_term_tax ) {
			$post = get_post();

			if ( ! $post ) {
				return;
			}

			$post_type = get_post_type( $post );

			$query_args = [
				'post_type'      => $post_type,
				'posts_per_page' => '1',
				'order'          => ( 'first' === $which ) ? 'DESC' : 'ASC',
				'no_found_rows'  => true,
				'meta_query'     => [
					'relation' => 'OR',
					[
						'key'     => 'wpex_post_link',
						'compare' => 'NOT EXISTS'
					],
					[
						'key'     => 'wpex_post_link',
						'value'   => '_wp_zero_value',
						'compare' => '='
					],
				],
			];

			if ( $in_same_term && $same_term_tax ) {
				if ( ! taxonomy_exists( $same_term_tax ) || ! is_object_in_taxonomy( $post_type, $same_term_tax ) ) {
					return;
				}

				$term_array = wp_get_object_terms( $post->ID, $same_term_tax, [ 'fields' => 'ids' ] );

				if ( ! $term_array || is_wp_error( $term_array ) ) {
					return;
				}

				$query_args['tax_query'] = [
					[
						'taxonomy' => $same_term_tax,
						'field'    => 'term_id',
						'terms'    => $term_array,
						'operator' => 'IN',
					]
				];
			}

			$query_args = apply_filters( 'vcex_post_next_prev_first_last_post_query_args', $query_args, $which );

			add_filter( 'pto/posts_order', [ self::class, 'filter_pto_posts_order' ], 15, 2 );
			$query = new \WP_Query( $query_args );
			remove_filter( 'pto/posts_order', [ self::class, 'filter_pto_posts_order' ], 15 );

			if ( ! empty( $query->posts[0] ) && is_a( $query->posts[0], 'WP_Post' ) ) {
				return $query->posts[0];
			}
		}

		/**
		 * Integration for the Post Types Order Plugin.
		 */
		public static function filter_pto_posts_order( $order, $query ) {
			if ( isset( $query->query_vars['order'] ) ) {
				$query_order = $query->query_vars['order'];
				$query_order = ( 'ASC' === $query_order ) ? 'DESC' : 'ASC';
				$order = " {$query_order}";
			}
			return $order;
		}

	}

}

new VCEX_Post_Next_Prev_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Post_Next_Prev' ) ) {
	class WPBakeryShortCode_Vcex_Post_Next_Prev extends WPBakeryShortCode {}
}
