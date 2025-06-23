<?php

defined( 'ABSPATH' ) || exit;

/**
 * Post Type Archive Shortcode.
 */
if ( ! class_exists( 'VCEX_Post_Type_Archive_Shortcode' ) ) {

	class VCEX_Post_Type_Archive_Shortcode extends TotalThemeCore\Vcex\Shortcode_Abstract {

		/**
		 * Shortcode tag.
		 */
		public const TAG = 'vcex_post_type_archive';

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
			return esc_html__( 'Post Types Archive', 'total-theme-core' );
		}

		/**
		 * Shortcode description.
		 */
		public static function get_description(): string {
			return esc_html__( 'Custom post type archive', 'total-theme-core' );
		}

		/**
		 * Array of shortcode parameters.
		 */
		public static function get_params_list(): array {
			$post_type_choices = [];

			if ( 'vc_edit_form' === vc_post_param( 'action' ) ) {
				$post_types = get_post_types( [
					'public' => true
				] );
				if ( $post_types ) {
					foreach ( $post_types as $post_type ) {
						if ( 'revision' !== $post_type && 'nav_menu_item' !== $post_type && 'attachment' !== $post_type ) {
							$post_type_choices[ esc_html( get_post_type_object( $post_type )->labels->name ) ] = sanitize_key( $post_type );
						}
					}
				}
			}

			return array(
				// General
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Header', 'total-theme-core' ),
					'param_name' => 'heading',
					'admin_label' => true,
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Header Style', 'total-theme-core' ),
					'param_name' => 'header_style',
					'description' => self::param_description( 'header_style' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Bottom Margin', 'total-theme-core' ),
					'param_name' => 'bottom_margin',
					'admin_label' => true,
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Visibility', 'total-theme-core' ),
					'param_name' => 'visibility',
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Element ID', 'total-theme-core' ),
					'param_name' => 'unique_id',
					'admin_label' => true,
					'description' => self::param_description( 'unique_id' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Extra class name', 'total-theme-core' ),
					'description' => self::param_description( 'el_class' ),
					'param_name' => 'classes',
				),
				vcex_vc_map_add_css_animation(),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Duration', 'total'),
					'param_name' => 'animation_duration',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Animation Delay', 'total'),
					'param_name' => 'animation_delay',
					'description' => esc_html__( 'Enter your custom time in seconds (decimals allowed).', 'total'),
				),
				// Query
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Advanced Query', 'total-theme-core' ),
					'param_name' => 'custom_query',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Enable to build a custom query using your own parameters.', 'total-theme-core' ),
				),
				array(
					'type' => 'textarea_safe',
					'heading' => esc_html__( 'Query Parameter String or Callback Function Name', 'total-theme-core' ),
					'param_name' => 'custom_query_args',
					'description' => self::param_description( 'advanced_query' ),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'true' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Post Type', 'total-theme-core' ),
					'param_name' => 'post_type',
					'value' => $post_type_choices,
					'std' => 'post', // needed because $post_types is empty on front-end
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => esc_html__( 'Order', 'total-theme-core' ),
					'param_name' => 'order',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'value' => array(
						esc_html__( 'Default', 'total-theme-core' ) => '',
						esc_html__( 'DESC', 'total-theme-core' ) => 'DESC',
						esc_html__( 'ASC', 'total-theme-core' ) => 'ASC',
					),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'vcex_select',
					'heading' => esc_html__( 'Order By', 'total-theme-core' ),
					'param_name' => 'orderby',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Orderby: Meta Key', 'total-theme-core' ),
					'param_name' => 'orderby_meta_key',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array(
						'element' => 'orderby',
						'value' => array( 'meta_value_num', 'meta_value' ),
					),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Offset', 'total-theme-core' ),
					'param_name' => 'offset',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Number of post to displace or pass over.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Posts Per Page', 'total-theme-core' ),
					'param_name' => 'posts_per_page',
					'value' => '12',
					'description' => esc_html__( 'You can enter "-1" to display all posts.', 'total-theme-core' ),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Query by Taxonomy', 'total-theme-core' ),
					'param_name' => 'tax_query',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'vcex_select',
					'choices' => 'taxonomy',
					'heading' => esc_html__( 'Taxonomy', 'total-theme-core' ),
					'param_name' => 'tax_query_taxonomy',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'If you do not see your taxonomy in the dropdown you can still enter the taxonomy name manually.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'tax_query', 'value' => 'true' ),
				),
				array(
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Terms', 'total-theme-core' ),
					'param_name' => 'tax_query_terms',
					'dependency' => array(
						'element' => 'tax_query',
						'value' => 'true',
					),
					'settings' => array(
						'multiple' => true,
						'min_length' => 1,
						'groups' => true,
						//'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'If you do not see your terms in the dropdown you can still enter the term slugs manually seperated by a space.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'tax_query_taxonomy', 'not_empty' => true ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Post With Thumbnails Only', 'total-theme-core' ),
					'param_name' => 'thumbnail_query',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Pagination', 'total-theme-core' ),
					'param_name' => 'pagination',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'vcex_ofswitch',
					'std' => 'false',
					'heading' => esc_html__( 'Load More Button', 'total-theme-core' ),
					'param_name' => 'pagination_loadmore',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
				),
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Limit By Post ID\'s', 'total-theme-core' ),
					'param_name' => 'posts_in',
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'description' => esc_html__( 'Seperate by a comma.', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				array(
					'type' => 'autocomplete',
					'heading' => esc_html__( 'Limit By Author', 'total-theme-core' ),
					'param_name' => 'author_in',
					'settings' => array(
						'multiple' => true,
						'min_length' => 1,
						'groups' => false,
						'unique_values' => true,
						'display_inline' => true,
						'delay' => 0,
						'auto_focus' => true,
					),
					'group' => esc_html__( 'Query', 'total-theme-core' ),
					'dependency' => array( 'element' => 'custom_query', 'value' => 'false' ),
				),
				// AJAX fields
				array( 'type' => 'hidden', 'param_name' => 'entry_count' ),
				array( 'type' => 'hidden', 'param_name' => 'paged' ),
			);
		}

		/**
		 * Register autocomplete hooks.
		 */
		public static function register_vc_autocomplete_hooks(): void {
			// Get autocomplete suggestion.
			\add_filter(
				'vc_autocomplete_vcex_post_type_archive_tax_query_terms_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms::callback'
			);
			\add_filter(
				'vc_autocomplete_vcex_post_type_archive_author_in_callback',
				'TotalThemeCore\WPBakery\Autocomplete\Users::callback'
			);
			// Render autocomplete suggestions.
			\add_filter(
				'vc_autocomplete_vcex_post_type_archive_tax_query_terms_render',
				'TotalThemeCore\WPBakery\Autocomplete\Taxonomy_Terms::render'
			);
			\add_filter(
				'vc_autocomplete_vcex_post_type_archive_author_in_render',
				'TotalThemeCore\WPBakery\Autocomplete\Users::render'
			);
		}

	}

}

new VCEX_Post_Type_Archive_Shortcode;

if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Vcex_Post_Type_Archive' ) ) {
	class WPBakeryShortCode_Vcex_Post_Type_Archive extends WPBakeryShortCode {}
}