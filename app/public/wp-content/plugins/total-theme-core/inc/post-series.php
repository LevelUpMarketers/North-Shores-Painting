<?php

namespace TotalThemeCore;

\defined( 'ABSPATH' ) || exit;

/**
 * Post Series Class.
 */
class Post_Series {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		\add_filter( 'wpex_customizer_sections', [ self::class, 'customizer_settings' ] );
		\add_action( 'init', [ self::class, 'register_taxonomy' ], 0 );
		\add_action( 'pre_get_posts', [ self::class, 'fix_archives_order' ] );
		if ( \is_admin() ) {
			\add_action( 'restrict_manage_posts', [ self::class, 'tax_filters' ] );
			\add_action( 'manage_post_posts_custom_column', [ self::class, 'column_display' ], 10, 2 );
			\add_filter( 'manage_edit-post_columns', [ self::class, 'edit_columns' ] );
		}
	}

	/**
	 * Registers the taxonomy.
	 */
	public static function register_taxonomy() {
		$name = self::get_singular_name();
		$slug = ( $slug_mod = \get_theme_mod( 'post_series_slug' ) ) ? \sanitize_text_field( $slug_mod ) : 'post-series';

		$args = (array) \apply_filters( 'wpex_taxonomy_post_series_args', [
			'labels' => [
				'name'                       => $name,
				'singular_name'              => $name,
				'menu_name'                  => $name,
				'search_items'               => \esc_html__( 'Search','total-theme-core' ),
				'popular_items'              => \esc_html__( 'Popular', 'total-theme-core' ),
				'all_items'                  => \esc_html__( 'All', 'total-theme-core' ),
				'parent_item'                => \esc_html__( 'Parent', 'total-theme-core' ),
				'parent_item_colon'          => \esc_html__( 'Parent', 'total-theme-core' ),
				'edit_item'                  => \esc_html__( 'Edit', 'total-theme-core' ),
				'update_item'                => \esc_html__( 'Update', 'total-theme-core' ),
				'add_new_item'               => \esc_html__( 'Add New', 'total-theme-core' ),
				'new_item_name'              => \esc_html__( 'New', 'total-theme-core' ),
				'separate_items_with_commas' => \esc_html__( 'Separate with commas', 'total-theme-core' ),
				'add_or_remove_items'        => \esc_html__( 'Add or remove', 'total-theme-core' ),
				'choose_from_most_used'      => \esc_html__( 'Choose from the most used', 'total-theme-core' ),
			],
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'show_ui'           => true,
			'show_tagcloud'     => true,
			'hierarchical'      => true,
			'rewrite'           => [ 'slug'  => $slug ],
			'query_var'         => true
		] );

		// Post types to register the post series for.
		$obj_type = [ 'post' ];
		$mod_obj_type = \get_theme_mod( 'post_series_object_type' );
		if ( $mod_obj_type && \is_string( $mod_obj_type ) ) {
			$mod_obj_type = \explode( ',', $mod_obj_type );
			if ( \is_array( $mod_obj_type ) ) {
				$obj_type = $mod_obj_type;
			}
		}

		\register_taxonomy( 'post_series', $obj_type, $args );
	}

	/**
	 * Adds columns to the WP dashboard edit screen.
	 */
	public static function edit_columns( $columns ) {
		$columns['wpex_post_series'] = self::get_singular_name();
		return $columns;
	}

	/**
	 * Adds columns to the WP dashboard edit screen.
	 */
	public static function column_display( $column, $post_id ) {
		if ( 'wpex_post_series' === $column ) {
			$category_list = \get_the_term_list( $post_id, 'post_series', '', ', ', '' );
			if ( $category_list ) {
				echo $category_list;
			} else {
				echo '&#8212;';
			}
		}
	}

	/**
	 * Adds taxonomy filters to the posts admin page
	 */
	public static function tax_filters( $post_type ) {
		$object_taxonomies = get_object_taxonomies( $post_type );
		if ( ! is_array( $object_taxonomies ) || ! in_array( 'post_series', $object_taxonomies, true ) ) {
			return;
		}
		$terms = \get_terms( 'post_series' );
		if ( ! count( $terms ) ) {
			return;
		}
		$current_tax_slug = isset( $_GET['post_series'] ) ? \sanitize_text_field( \wp_unslash( $_GET['post_series'] ) ) : false;
		?>
			<select name="<?php echo \esc_attr( 'post_series' ); ?>" id="<?php echo \esc_attr( 'post_series' ); ?>" class="postform">
				<option value=""><?php \printf( esc_html_x( 'All %s', 'Dashboard Filter Select All Post Series Label', 'total-theme-core' ), \esc_html( \get_taxonomy( 'post_series' )->labels->name ) ); ?></option>
				<?php foreach ( $terms as $term ) { ?>
					<option value="<?php echo \esc_attr( $term->slug ); ?>" <?php \selected( $current_tax_slug, $term->slug, true ); ?>><?php echo \esc_html( $term->name ); ?></option>
				<?php } ?>
			</select>
		<?php
	}

	/**
	 * Adds customizer settings.
	 */
	public static function customizer_settings( $sections ) {
		$sections['wpex_post_series'] = [
			'title' => \esc_html__( 'Post Series', 'total-theme-core' ),
			'panel' => 'wpex_general',
			'desc' => \esc_html__( 'Post Series is a custom taxonomy that allows you to "link" posts together so when viewing a post from a series you will see links to all related posts at the top. You can disable this function completely via the Theme Panel.', 'total-theme-core' ),
			'settings' => [
				[
					'id' => 'post_series_template_id',
					'control' => [
						'label' => \esc_html__( 'Archives: Dynamic Template', 'total-theme-core' ),
						'type' => 'totaltheme_template_select',
						'template_type' => 'archive',
						'description' => \esc_html__( 'Select a template to override the default output for the post series archives.', 'total-theme-core' ),
					],
				],
				[
					'id' => 'post_series_singular_template',
					'control' => [
						'label' => \esc_html__( 'Single Post: Dynamic Template', 'total-theme-core' ),
						'type' => 'totaltheme_template_select',
						'template_type' => 'single',
						'description' => \esc_html__( 'Select a template for any singular post that is part of a post series..', 'total-theme-core' ),
					],
				],
				[
					'id' => 'post_series_object_type',
					'transport' => 'postMessage',
					'control' => [
						'label' => \esc_html__( 'Target Post Types', 'total-theme-core' ),
						'type' => 'text',
						'description' => \esc_html__( 'The Post Series is added only to posts by default. Enter a comma separated list of the post types you want it added to. If you want to keep it on posts make sure to include "post" in your list.', 'total-theme-core' ),
						'input_attrs' => [
							'placeholder' => 'post',
						],
					],
				],
				[
					'id' => 'post_series_order',
					'default' => 'ascending',
					'control' => [
						'label' => \esc_html__( 'Order', 'total-theme-core' ),
						'type' => 'select',
						'choices' => [
							'ascending' => \esc_html__( 'Oldest to Newest', 'total-theme-core' ),
							'descending' => \esc_html__( 'Newest to Oldest', 'total-theme-core' ),
						],
					],
				],
				[
					'id' => 'post_series_labels',
					'transport' => 'postMessage',
					'control' => [
						'label' => \esc_html__( 'Admin Label', 'total-theme-core' ),
						'type' => 'text',
						'input_attrs' => [
							'placeholder' => \esc_html__( 'Post Series', 'total-theme-core' ),
						],
					],
				],
				[
					'id' => 'post_series_slug',
					'transport' => 'postMessage',
					'control' => [
						'label' => \esc_html__( 'Slug', 'total-theme-core' ),
						'type' => 'text',
						'input_attrs' => [
							'placeholder' => 'post-series',
						],
					],
				],
				[
					'id' => 'post_series_bg',
					'transport' => 'postMessage',
					'control' => [
						'label' => \esc_html__( 'Background', 'total-theme-core' ),
						'type' => 'totaltheme_color',
					],
					'inline_css' => [
						'target' => '.wpex-post-series-toc',
						'alter' => 'background',
					],
				],
				[
					'id' => 'post_series_borders',
					'transport' => 'postMessage',
					'control' => [
						'label' => \esc_html__( 'Border', 'total-theme-core' ),
						'type' => 'totaltheme_color',
					],
					'inline_css' => [
						'target' => '.wpex-post-series-toc',
						'alter' => 'border-color',
					],
				],
				[
					'id' => 'post_series_header_color',
					'transport' => 'postMessage',
					'control' => [
						'label' => \esc_html__( 'Header Color', 'total-theme-core' ),
						'type' => 'totaltheme_color',
					],
					'inline_css' => [
						'target' => '.wpex-post-series-toc-header a',
						'alter' => 'color',
					],
				],
				[
					'id' => 'post_series_color',
					'transport' => 'postMessage',
					'control' => [
						'label' => \esc_html__( 'Text Color', 'total-theme-core' ),
						'type' => 'totaltheme_color',
					],
					'inline_css' => [
						'target' => '.wpex-post-series-toc-list',
						'alter' => 'color',
					],
				],
				[
					'id' => 'post_series_link_color',
					'transport' => 'postMessage',
					'control' => [
						'label' => \esc_html__( 'Link Color', 'total-theme-core' ),
						'type' => 'totaltheme_color',
					],
					'inline_css' => [
						'target' => '.wpex-post-series-toc-list a',
						'alter' => 'color',
					],
				],
			]
		];
		return $sections;
	}

	/**
	 * Fix archives order
	 */
	public static function fix_archives_order( $query ) {
		if ( ! \is_admin() && $query->is_main_query() && \is_tax( 'post_series' ) ) {
			$query->set( 'order', self::get_query_order() );
		}
	}

	/**
	 * Returns order for the query.
	 *
	 * Note: This function is used in the partials template file.
	 */
	public static function get_query_order(): string {
		$order = (string) \get_theme_mod( 'post_series_order' );
		if ( 'descending' === $order || 'DESC' === \strtoupper( $order ) ) {
			$order = 'DESC';
		} else {
			$order = 'ASC';
		}
		return $order;
	}

	/**
	 * Returns singular name.
	 */
	private static function get_singular_name() {
		return ( $name_mod = \get_theme_mod( 'post_series_labels' ) ) ? \sanitize_text_field( $name_mod ) : \esc_html__( 'Post Series', 'total-theme-core' );
	}

	/**
	 * Instance.
	 *
	 * @deprecated 5.8.1
	 */
	public static function instance() {
		return new self;
	}

}
