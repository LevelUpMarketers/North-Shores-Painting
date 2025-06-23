<?php

namespace TotalThemeCore;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom Post Type parent class.
 *
 * This is an internal class, not intended for use outside of the Total Theme Core plugin.
 * Used exclusively for the built-in portfolio, testimonial and staff post types.
 */
abstract class Custom_Post_Type_Abstract {

	/**
	 * Post Type name.
	 */
	protected $post_type = '';

	/**
	 * Has custom sidebar.
	 */
	protected $has_custom_sidebar = '';

	/**
	 * Constructor.
	 */
	public function __construct( $post_type_name ) {
		$this->post_type = $post_type_name;
		$this->has_custom_sidebar = \get_theme_mod( "{$this->post_type}_custom_sidebar", true );

		// Hooks into the init action hook
		\add_action( 'init', [ $this, '_on_init' ], 0 );

		// Register custom sidebar
		if ( $this->has_custom_sidebar ) {
			\add_filter( 'wpex_register_sidebars_array', [ $this, '_register_sidebar' ] );
		}

		// Register custom image sizes
		\add_filter( 'wpex_image_sizes', [ $this, '_filter_image_sizes' ] );

		// Admin functions
		if ( \is_admin() ) {

			// Add custom image sizes panel tab
			\add_filter( 'wpex_image_sizes_tabs', [ $this, '_filter_image_sizes_panel_tabs' ] );

			// Enable meta settings
			\add_filter( 'wpex_card_metabox_post_types', [ $this, '_enable_meta' ] );
			\add_filter( 'wpex_main_metaboxes_post_types', [ $this, '_enable_meta' ] );

			// Enable gallery metabox
			if ( 'testimonials' !== $this->post_type ) {
				\add_filter( 'wpex_gallery_metabox_post_types', [ $this, '_enable_gallery_metabox' ], 20 );
			}

			// Adds columns in the admin view for taxonomies.
			\add_filter( "manage_edit-{$this->post_type}_columns", [ $this, '_add_admin_columns' ] );
			\add_action( "manage_{$this->post_type}_posts_custom_column", [ $this, '_render_admin_columns' ], 10, 2 );

			// Allows filtering of posts by taxonomy in the admin view.
			\add_action( 'restrict_manage_posts', [ $this, '_on_restrict_manage_posts' ] );
		}

		// Frontend functions
		if ( ! \is_admin() || \wp_doing_ajax() ) {

			// Modify the content layout
			\add_filter( 'wpex_post_layout_class', [ $this, '_filter_content_layout' ] );

			// Display custom sidebar
			if ( $this->has_custom_sidebar ) {
				\add_filter( 'totaltheme/sidebars/primary/name', [ $this, '_filter_primary_sidebar' ] );
			}

			// Filter page header title display
			\add_filter( 'totaltheme/page/header/is_enabled', [ $this, '_filter_page_header_check' ] );

			// Filter the breadcrumbs post type archive ID for use with custom main pages
			\add_filter( 'wpex_breadcrumbs_cpt_main_page_id', [ $this, '_filter_breadcrumbs_main_page_id' ] );

			// Hooks into pre_get_posts
			\add_action( 'pre_get_posts', [ $this, '_on_pre_get_posts' ] );

			// Exclude staff from live search results
			if ( ! \get_theme_mod( "{$this->post_type}_search", true ) ) {
				// This is needed because of a WP bug we can't set exclude_from_search param always.
				if ( \function_exists( 'totaltheme_is_live_search' ) && totaltheme_is_live_search() ) {
					\add_filter( "register_{$this->post_type}_post_type_args", [ $this, '_set_exclude_from_search_arg' ] );
				}
			}

		}

	}

	/**
	 * Runs on the init action hook at priority 0.
	 */
	public function _on_init(): void {

		// Register post type
		\register_post_type( $this->post_type, $this->get_post_type_args() );

		// Register taxonomies
		if ( 'testimonials' !== $this->post_type && \ttc_validate_boolean( \get_theme_mod( "{$this->post_type}_tags", true ) ) ) {
			\register_taxonomy( "{$this->post_type}_tag", [ $this->post_type ], $this->get_tag_args() );
		}

		if ( \ttc_validate_boolean( \get_theme_mod( "{$this->post_type}_categories", true ) ) ) {
			\register_taxonomy( "{$this->post_type}_category", [ $this->post_type ], $this->get_category_args() );
		}
	}

	/**
	 * Returns the post type args.
	 */
	private function get_post_type_args(): array {
		$has_archive = \wp_validate_boolean( \get_theme_mod( "{$this->post_type}_has_archive", false ) );

		// Get post type name label
		if ( \function_exists( '\wpex_get_translated_theme_mod' ) ) {
			$name = ( $name = \wpex_get_translated_theme_mod( "{$this->post_type}_labels" ) ) ? \sanitize_text_field( $name ) : '';
		} else {
			$name = ( $name = \get_theme_mod( "{$this->post_type}_labels" ) ) ? \sanitize_text_field( $name ) : '';
		}

		if ( ! $name ) {
			$name_map = [
				'portfolio'    => \esc_html__( 'Portfolio', 'total-theme-core' ),
				'staff'        => \esc_html__( 'Staff', 'total-theme-core' ),
				'testimonials' => \esc_html__( 'Testimonials', 'total-theme-core' ),
			];
			$name = $name_map[ $this->post_type ] ?? ucfirst( $this->post_type );
		}

		// Get post type singular name
		if ( \function_exists( '\wpex_get_translated_theme_mod' ) ) {
			$singular_name = ( $singular_name = \wpex_get_translated_theme_mod( "{$this->post_type}_singular_name" ) ) ? \sanitize_text_field( $singular_name ) : '';
		} else {
			$singular_name = ( $singular_name = \get_theme_mod( "{$this->post_type}_singular_name" ) ) ? \sanitize_text_field( $singular_name ) : '';
		}

		if ( ! $singular_name ) {
			$singular_name_map = [
				'portfolio'    => \esc_html__( 'Portfolio Item', 'total-theme-core' ),
				'staff'        => \esc_html__( 'Staff Member', 'total-theme-core' ),
				'testimonials' => \esc_html__( 'Testimonial', 'total-theme-core' ),
			];
			$singular_name = $singular_name_map[ $this->post_type ] ?? \ucfirst( $this->post_type );
		}

		// Get post type slug
		$slug = ( $slug = \get_theme_mod( "{$this->post_type}_slug" ) ) ? \sanitize_text_field( $slug ) : '';

		if ( ! $slug ) {
			switch ( $this->post_type ) {
				case 'staff':
					$slug = $has_archive ? 'staff' : 'staff-member';
					break;
				case 'testimonials':
					$slug = $has_archive ? 'testimonials' : 'testimonial';
					break;
				default:
					$slug = $has_archive ? $this->post_type : "{$this->post_type}-item";
					break;
			}
		}

		// Get menu icon
		$menu_icon = ( $icon = \get_theme_mod( "{$this->post_type}_admin_icon" ) ) ? \sanitize_key( $icon ) : '';

		if ( ! $menu_icon ) {
			$menu_icon_map = [
				'staff'        => 'businessman',
				'testimonials' => 'testimonial',
			];
			$menu_icon = $menu_icon_map[ $this->post_type ] ?? $this->post_type;
		}

		// Define labels
		$labels = [
			'name'               => $name,
			'singular_name'      => $singular_name,
			'add_new'            => \esc_html__( 'Add New', 'total-theme-core' ),
			'add_new_item'       => \sprintf( \esc_html_x( 'Add New %s', 'Add New {Single Post Type Name}', 'total-theme-core' ), $singular_name ),
			'edit_item'          => \sprintf( \esc_html_x( 'Edit %s', 'Edit {Single Post Type Name}', 'total-theme-core' ), $singular_name ),
			'new_item'           => \sprintf( \esc_html_x( 'Add New %s', 'Add New {Single Post Type Name}', 'total-theme-core' ), $singular_name ),
			'view_item'          => \sprintf( \esc_html_x( 'View %s', 'View {Single Post Type Name}', 'total-theme-core' ), $singular_name ),
			'search_items'       => \esc_html__( 'Search Items', 'total-theme-core' ),
			'not_found'          => \esc_html__( 'No Items Found', 'total-theme-core' ),
			'not_found_in_trash' => \esc_html__( 'No Items Found In Trash', 'total-theme-core' )
		];

		// Define args
		$args = [
			'labels'             => $labels,
			'public'             => true,
			'capability_type'    => 'post',
			'menu_position'      => 20,
			'menu_icon'          => "dashicons-{$menu_icon}",
			'has_archive'        => $has_archive,
			'publicly_queryable' => \wp_validate_boolean( \get_theme_mod( "{$this->post_type}_has_single", true ) ),
			'show_in_rest'       => \wp_validate_boolean( \get_theme_mod( "{$this->post_type}_show_in_rest", false ) ),
			'supports'           => [ 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'revisions', 'author', 'page-attributes' ],
			'rewrite'            => [ 'slug' => $slug, 'with_front' => false ],
		];

		return (array) apply_filters( "wpex_{$this->post_type}_args", $args );
	}

	/**
	 * Returns tag args.
	 */
	private function get_tag_args(): array {
		$name = ( $name = \get_theme_mod( "{$this->post_type}_tag_labels" ) ) ? \sanitize_text_field( $name ) : '';

		if ( ! $name ) {
			$name_map = [
				'portfolio' => \esc_html__( 'Portfolio Tags', 'total-theme-core' ),
				'staff'     => \esc_html__( 'Staff Tags', 'total-theme-core' ),
			];
			$name = $name_map[ $this->post_type ];
		}

		$slug = ( $slug = \get_theme_mod( "{$this->post_type}_tag_slug" ) ) ? \sanitize_text_field( $slug ) : '';

		if ( ! $slug ) {
			$slug = "{$this->post_type}-tag";
		}

		$args = [
			'labels'            => [
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
			'show_in_rest'      => \ttc_validate_boolean( \get_theme_mod( "{$this->post_type}_show_in_rest", false ) ),
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'show_tagcloud'     => true,
			'hierarchical'      => false,
			'query_var'         => true,
			'rewrite'           => [
				'slug'          => $slug,
				'with_front'    => false,
			],
		];

		return (array) \apply_filters( "wpex_taxonomy_{$this->post_type}_tag_args", $args );
	}

	/**
	 * Returns category args.
	 */
	private function get_category_args(): array {
		$name = ( $name = \get_theme_mod( "{$this->post_type}_cat_labels" ) ) ? \sanitize_text_field( $name ) : '';

		if ( ! $name ) {
			$name_map = [
				'portfolio'    => \esc_html__( 'Portfolio Categories', 'total-theme-core' ),
				'staff'        => \esc_html__( 'Staff Categories', 'total-theme-core' ),
				'testimonials' => \esc_html__( 'Testimonials Categories', 'total-theme-core' ),
			];
			$name = $name_map[ $this->post_type ];
		}

		$slug = ( $slug = \get_theme_mod( "{$this->post_type}_cat_slug" ) ) ? \sanitize_text_field( $slug ) : '';

		if ( ! $slug ) {
			$slug = "{$this->post_type}-category";
		}

		$args = [
			'labels' => [
				'name'                       => $name,
				'singular_name'              => $name,
				'menu_name'                  => $name,
				'search_items'               => \esc_html__( 'Search', 'total-theme-core' ),
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
			'show_in_rest'      => \ttc_validate_boolean( \get_theme_mod( "{$this->post_type}_show_in_rest", false ) ),
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'show_tagcloud'     => true,
			'hierarchical'      => true,
			'query_var'         => true,
			'rewrite'           => [
				'slug'          => $slug,
				'with_front'    => false
			],
		];

		return (array) \apply_filters( "wpex_taxonomy_{$this->post_type}_category_args", $args );
	}

	/**
	 * Sets the exclude_from_search_query arg to false.
	 */
	public function _set_exclude_from_search_arg( $args ) {
		$args['exclude_from_search'] = true;
		return $args;
	}

	/**
	 * Registers a new sidebar for the post type.
	 */
	public function _register_sidebar( $sidebars ): array {
		$obj = get_post_type_object( $this->post_type );
		if ( $obj ) {
			$sidebars[ "{$this->post_type}_sidebar" ] = \sprintf(
				\esc_html__( '%s Sidebar', '{Post Type Name} Sidebar', 'total-theme-core' ),
				\esc_html( $obj->labels->name )
			);
		}
		return $sidebars;
	}

	/**
	 * Filter the primary sidebar.
	 */
	public function _filter_primary_sidebar( $sidebar ) {
		if ( \is_singular( $this->post_type ) || $this->is_archive() ) {
			$sidebar = "{$this->post_type}_sidebar";
		}
		return $sidebar;
	}

	/**
	 * Filter the page header enabled check.
	 * 
	 * This is needed specifically for the taxonomies.
	 */
	public function _filter_page_header_check( $check ): bool {
		if ( $check && $this->is_tax() && ! \get_theme_mod( "{$this->post_type}_archive_has_page_header", true ) ) {
			$check = false;
		}
		return $check;
	}

	/**
	 * Filter the breadcrumbs trail.
	 */
	public function _filter_breadcrumbs_main_page_id( $page_id ) {
		if ( \is_singular( $this->post_type ) || $this->is_tax() ) {
			return ( $page = \get_theme_mod( "{$this->post_type}_page" ) ) ? absint( $page ) : '';
		}
		return $page_id;
	}

	/**
	 * Filter the content layout.
	 */
	public function _filter_content_layout( $layout ) {
		if ( \is_singular( $this->post_type ) ) {
			$default = 'portfolio' === $this->post_type ? 'full-width' : ''; // for some reason portfolio was different from the start.
			$layout = \get_theme_mod( "{$this->post_type}_single_layout", $default );
		} elseif ( $this->is_archive() ) {
			$layout = \get_theme_mod( "{$this->post_type}_archive_layout", 'full-width' );
		}
		return $layout;
	}

	/**
	 * Enable meta settings for the post type.
	 */
	public function _enable_meta( array $post_types ): array {
		$post_types[ $this->post_type ] = $this->post_type;
		return $post_types;
	}

	/**
	 * Adds the portfolio post type to the gallery metabox post types array.
	 */
	public function _enable_gallery_metabox( array $types ): array {
		$types[] = $this->post_type;
		return $types;
	}

	/**
	 * Adds new tab to the image sizes panel.
	 */
	public function _filter_image_sizes_panel_tabs( array $array ): array {
		if ( $obj = get_post_type_object( $this->post_type ) ) {
			$array[ $this->post_type ] = $obj->labels->name;
		}
		return $array;
	}

	/**
	 * Adds new image sizes.
	 */
	public function _filter_image_sizes( array $sizes ): array {
		$obj = get_post_type_object( $this->post_type );

		if ( ! $obj ) {
			return $sizes;
		}

		$sizes[ "{$this->post_type}_entry" ] = [
			'label'   => \sprintf( \esc_html_x( '%s Entry', '{Post Type Name} Entry', 'total-theme-core' ), $obj->labels->name ),
			'section' => $this->post_type,
		];

		$sizes[ "{$this->post_type}_post" ] = [
			'label'   => \sprintf( \esc_html_x( '%s Post', '{Post Type Name} Post', 'total-theme-core' ), $obj->labels->name ),
			'section' => $this->post_type,
		];

		if ( 'testimonials' !== $this->post_type ) {
			$sizes[ "{$this->post_type}_related" ] = [
				'label'   => \sprintf( \esc_html_x( '%s Post Related', '{Post Type Name} Related', 'total-theme-core' ), $obj->labels->name ),
				'section' => $this->post_type,
			];
		}

		return $sizes;
	}
	
	/**
	 * Hooks into pre_get_posts.
	 */
	public function _on_pre_get_posts( $query ) {
		if ( \is_admin() || ! $query->is_main_query() || ! $this->is_archive() ) {
			return;
		}

		$posts_per_page = ( $posts_per_page = \get_theme_mod( "{$this->post_type}_archive_posts_per_page" ) ) ? \sanitize_text_field( $posts_per_page ) : 12;
		$query->set( 'posts_per_page', (int) $posts_per_page );

		$orderby = ( $archive_orderby = \get_theme_mod( "{$this->post_type}_archive_orderby" ) ) ? \sanitize_text_field( $archive_orderby ) : '';
		if ( $orderby && $orderby_safe = \sanitize_sql_orderby( $orderby ) ) {
			$query->set( 'orderby', $orderby_safe );
		}

		$order = ( $order = \get_theme_mod( "{$this->post_type}_archive_order" ) ) ? \sanitize_text_field( $order ) : '';
		if ( $order && \in_array( \strtoupper( $order ), [ 'ASC', 'DESC' ], true ) ) {
			$query->set( 'order', $order );
		}
	}

	/**
	 * Adds custom admin columns.
	 */
	public function _add_admin_columns( $columns ) {
		if ( 'testimonials' === $this->post_type ) {
			$columns['testimonial_author'] = \esc_html__( 'By', 'total-theme-core' );
			$columns['testimonial_rating'] = \esc_html__( 'Rating', 'total-theme-core' );
		}
		if ( \taxonomy_exists( "{$this->post_type}_category" ) ) {
			$columns[ "{$this->post_type}_category" ] = \get_theme_mod( "{$this->post_type}_cat_labels" ) ? get_taxonomy( "{$this->post_type}_category" )->labels->singular_name : \esc_html__( 'Category', 'total-theme-core' );
		}
		if ( \taxonomy_exists( "{$this->post_type}_tag" ) ) {
			$columns[ "{$this->post_type}_tag" ] = \get_theme_mod( "{$this->post_type}_tag_labels" ) ? \get_taxonomy( "{$this->post_type}_tag" )->labels->singular_name : \esc_html__( 'Tags', 'total-theme-core' );
		}
		return $columns;
	}

	/**
	 * Renders custom admin columns.
	 */
	public function _render_admin_columns( $column, $post_id ) {
		switch ( $column ) :
			case "{$this->post_type}_category":
				$this->dashboard_column_tax_terms( $post_id, "{$this->post_type}_category" );
			break;
			case "{$this->post_type}_tag":
				$this->dashboard_column_tax_terms( $post_id, "{$this->post_type}_tag" );
			break;
			case 'testimonial_author':
				if ( $by = \get_post_meta( $post_id, 'wpex_testimonial_author', true ) ) {
					echo \esc_html( $by );
				} else {
					echo '&#8212;';
				}
				break;
			case 'testimonial_rating':
				if ( $rating = \get_post_meta( $post_id, 'wpex_post_rating', true ) ) {
					echo \esc_html( $rating );
				} else {
					echo '&#8212;';
				}
				break;
		endswitch;
	}

	/**
	 * Hooks into restrict_manage_posts.
	 */
	public function _on_restrict_manage_posts( $post_type ) {
		if ( $this->post_type !== $post_type ) {
			return;
		}

		$taxonomies = [
			"{$this->post_type}_category" => ! \get_theme_mod( "{$post_type}_cat_labels" ) ? \esc_html__( 'Category', 'total-theme-core' ) : '',
			"{$this->post_type}_tag"      => ! \get_theme_mod( "{$post_type}_tag_labels" ) ? \esc_html__( 'Tags', 'total-theme-core' ) : '',
		];

		foreach ( $taxonomies as $tax => $tax_label ) {
			if ( ! taxonomy_exists( $tax ) ) {
				continue;
			}
			$terms = \get_terms( $tax );
			if ( ! empty( $terms ) && ! \is_wp_error( $terms ) ) {
				$selected = ! empty( $_GET[ $tax ] ) ? \sanitize_text_field( \wp_unslash( $_GET[ $tax ] ) ) : false;
				?>
				<select name="<?php echo \esc_attr( $tax ); ?>" id="<?php echo \esc_attr( $tax ); ?>" class="postform">
					<option value=""><?php echo \esc_html( $tax_label ?: \get_taxonomy( $tax )->labels->name ); ?></option>
					<?php foreach ( $terms as $term ) { ?>
						<option value="<?php echo \esc_attr( $term->slug ); ?>" <?php selected( $selected, $term->slug, true ); ?>><?php echo \esc_html( $term->name ); ?></option>
					<?php } ?>
				</select>
			<?php }
		}
	}

	/**
	 * Returns label value.
	 */
	protected function get_label( string $which ): string {
		return $this->get_labels()[ $which ] ?? '';
	}

	/**
	 * Check if currently viewing an archive for the post type.
	 */
	protected function is_archive(): bool {
		return \is_post_type_archive( $this->post_type ) || $this->is_tax();
	}

	/**
	 * Check if currently viewing a taxonomy for the post type.
	 */
	protected function is_tax(): bool {
		if ( ! is_tax() ) {
			return false;
		}
		// @note we do a ! is_search() because of a WordPress core bug.
		$check = is_tax( [ "{$this->post_type}_category", "{$this->post_type}_tag" ] ) && ! is_search();
		return (bool) apply_filters( "wpex_is_{$this->post_type}_tax", $check );
	}

	/**
	 * Helper function to render admin column tax terms.
	 */
	private function dashboard_column_tax_terms( $post_id, $taxonomy ) {
		$terms = \get_the_terms( $post_id, $taxonomy );
		if ( ! empty( $terms ) && ! \is_wp_error( $terms ) ) {
			$terms_list = [];
			foreach ( $terms as $term ) {
				if ( \current_user_can('edit_terms', $taxonomy ) ) {
					$term_link = \get_edit_term_link( $term->term_id, $taxonomy );
				} else {
					$term_link = \get_term_link( $term, $taxonomy );
				}
				$terms_list[] = '<a href="' . \esc_url( $term_link ) . '">' . \esc_html( $term->name ) . '</a>';
			}
			echo implode(', ', $terms_list );
		} else {
			echo '&#8212;';
		}
	}

}
