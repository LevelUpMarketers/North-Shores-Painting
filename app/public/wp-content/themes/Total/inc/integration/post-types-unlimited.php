<?php

namespace TotalTheme\Integration;

use WP_Query;

\defined( 'ABSPATH' ) || exit;

/**
 * Adds custom options to the Post Types Unlimited Plugin meta options.
 */
class Post_Types_Unlimited {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init(): void {
		self::global_hooks();

		if ( \is_admin() ) {
			self::admin_hooks();
		}
	}

	/**
	 * Hook into global actions and filters.
	 */
	public static function global_hooks(): void {
		\add_action( 'pre_get_posts', [ self::class, '_on_pre_get_posts'] );
		\add_filter( 'totaltheme/remove_cpt_slugs/post_types', [ self::class, '_filter_remove_cpt_slugs_types' ] );
		\add_filter( 'wpex_register_sidebars_array', [ self::class, 'register_sidebars' ] );

		if ( \get_theme_mod( 'image_sizes_enable', true ) ) {
			\add_filter( 'wpex_image_sizes', [ self::class, 'filter_wpex_image_sizes' ], 100 );
		}

		if ( \get_theme_mod( 'post_series_enable', true ) ) {
			\add_action( 'init', [ self::class, 'register_post_series' ] );
		}
	}

	/**
	 * Hook into admin actions and filters.
	 */
	public static function admin_hooks(): void {
		\add_filter( 'ptu/posttypes/meta_box_tabs', [ self::class, 'filter_posttypes_meta_box_tabs' ] );
		\add_filter( 'ptu/taxonomies/meta_box_tabs', [ self::class, 'filter_taxonomies_meta_box_tabs' ] );

		if ( \apply_filters( 'wpex_metaboxes', true ) ) {
			\add_filter( 'totalthemecore/meta/main_metabox/post_types', [ self::class, 'filter_main_metabox_post_types' ] );
			if ( \wp_validate_boolean( \get_theme_mod( 'theme_settings_metabox_core_fields_enable', true ) ) ) {
				\add_filter( 'totalthemecore/meta/main_metabox/has_core_fields', [ self::class, 'filter_main_metabox_has_core_fields' ], 10, 2 );
			}
			\add_filter( 'totalthemecore/meta/main_metabox/has_media_fields', [ self::class, 'filter_main_metabox_has_media_fields' ], 10, 2 );
		}

		if ( \get_theme_mod( 'card_metabox_enable', true ) ) {
			\add_filter( 'wpex_card_metabox_post_types', [ self::class, 'metabox_card' ] );
		}

		if ( \get_theme_mod( 'image_sizes_enable', true ) ) {
			\add_filter( 'wpex_image_sizes_tabs', [ self::class, 'filter_wpex_image_sizes_tabs' ], 50 );
		}

		if ( \get_theme_mod( 'gallery_metabox_enable', true ) ) {
			\add_filter( 'wpex_gallery_metabox_post_types', [ self::class, 'wpex_gallery_metabox_post_types' ] );
		}

		\add_filter( 'wpex_dashboard_thumbnails_post_types', [ self::class, 'wpex_dashboard_thumbnails_post_types' ] );
	}

	/**
	 * Hooks into the pre_get_posts hook.
	 */
	public static function _on_pre_get_posts( $query ): void {
		if ( \is_admin() || ! $query->is_main_query() || ! $query->is_archive() ) {
			return;
		}

		if ( $query->is_post_type_archive() ) {
			$post_type = $query->get_queried_object()->name ?? '';
			if ( $post_type && is_string( $post_type ) && \array_key_exists( $post_type, self::get_post_types() ) ) {
				$posts_per_page = ( $ppp = self::get_setting_value( $post_type, 'archive_posts_per_page' ) ) ? \intval( $ppp ) : '';
				$order = ( $order = self::get_setting_value( $post_type, 'archive_order' ) ) ? \sanitize_text_field( $order ) : '';
				$orderby = ( $orderby = self::get_setting_value( $post_type, 'archive_orderby' ) ) ? \sanitize_text_field( $orderby ) : '';
			}
		} elseif ( $query->is_tax() ) {
			$taxonomy = $query->get_queried_object()->taxonomy ?? '';
			if ( $taxonomy && is_string( $taxonomy ) && \array_key_exists( $taxonomy, self::get_taxonomies() ) ) {
				$posts_per_page = ( $ppp = self::get_tax_setting_value( $taxonomy, 'posts_per_page' ) ) ? \intval( $ppp ) : '';
				$order = ( $order = self::get_tax_setting_value( $taxonomy, 'order' ) ) ? \sanitize_text_field( $order ) : '';
				$orderby = ( $orderby = self::get_tax_setting_value( $taxonomy, 'orderby' ) ) ? \sanitize_text_field( $orderby ) : '';
			}
		}

		// Custom archive posts per page
		if ( ! empty( $posts_per_page ) ) {
			$query->set( 'posts_per_page', $posts_per_page );
		}

		// Custom post type order
		if ( ! empty( $order ) && \in_array( \strtoupper( $order ), [ 'ASC', 'DESC' ], true ) ) {
			$query->set( 'order', $order );
		}

		// Custom post type orderby
		if ( ! empty( $orderby ) && $orderby_safe = \sanitize_sql_orderby( $orderby ) ) {
			$query->set( 'orderby', $orderby_safe );
		}
	}

	/**
	 * Filters the list of post types to remove slugs from.
	 */
	public static function _filter_remove_cpt_slugs_types( $types ): array {
		foreach ( self::get_post_types() as $type => $id ) {
			if ( '0' === \get_post_meta( $id, '_ptu_slug', true ) ) {
				$types[] = $type;
			}
		}
		return $types;
	}

	/**
	 * Register new metabox tabs for post types.
	 */
	public static function filter_posttypes_meta_box_tabs( $tabs ): array {
		$tabs[] = self::type_general_metabox();
		$tabs[] = self::type_archive_metabox();
		$tabs[] = self::type_single_metabox();
		$tabs[] = self::type_related_metabox();
		return $tabs;
	}

	/**
	 * Register new metabox tabs for taxonomies.
	 */
	public static function filter_taxonomies_meta_box_tabs( $tabs ): array {
		$tabs[] = self::tax_general_metabox();
		$tabs[] = self::tax_archive_metabox();
		return $tabs;
	}

	/**
	 * Post Type general options.
	 */
	public static function type_general_metabox(): array {
		$fields = [];

		if ( \totaltheme_is_integration_active( 'wpbakery' ) ) {
			$fields[] =	[
				'name' => \esc_html__( 'Disable WPBakery', 'total' ),
				'id'   => 'total_disable_wpbakery',
				'type' => 'checkbox',
			];
		}

		if ( \apply_filters( 'wpex_metaboxes', true ) ) {

			if ( \wp_validate_boolean( \get_theme_mod( 'theme_settings_metabox_core_fields_enable', true ) ) ) {
				$fields[] =	[
					'name' => \esc_html__( 'Theme Settings Metabox', 'total' ),
					'id'   => 'total_ps_meta',
					'type' => 'checkbox',
				];
			}

			$fields[] =	[
				'name' => \esc_html__( 'Metabox Media Tab', 'total' ),
				'id'   => 'total_ps_meta_media',
				'type' => 'checkbox',
			];

		}

		if ( \get_theme_mod( 'card_metabox_enable', true ) ) {
			$fields[] =	[
				'name' => \esc_html__( 'Card Settings Metabox', 'total' ),
				'id'   => 'total_ps_meta_card',
				'type' => 'checkbox',
			];
		}

		if ( \get_theme_mod( 'post_series_enable', true ) ) {
			$fields[] =	[
				'name' => \esc_html__( 'Post Series', 'total' ),
				'id'   => 'total_post_series',
				'type' => 'checkbox',
			];
		}

		if ( \get_theme_mod( 'gallery_metabox_enable', true ) ) {
			$fields[] =	[
				'name' => \esc_html__( 'Image Gallery', 'total' ),
				'id'   => 'total_post_gallery',
				'type' => 'checkbox',
			];
		}

		$fields[] =	[
			'name' => \esc_html__( 'Admin Thumbnails', 'total' ),
			'id'   => 'total_show_admin_thumbnails',
			'type' => 'checkbox',
			'desc' => \esc_html__( 'Check to display your post featured images on the main admin edit screen.', 'total' ),
		];

		if ( \get_theme_mod( 'image_sizes_enable', true ) ) {
			$fields[] =	[
				'name' => \esc_html__( 'Image Sizes', 'total' ),
				'id'   => 'total_image_sizes',
				'type' => 'checkbox',
				'desc' => \esc_html__( 'Enable image size settings for this post type under Theme Panel > Image Sizes.', 'total' ),
			];
		}

		$fields[] =	[
			'name'    => \esc_html__( 'Main Page', 'total' ),
			'id'      => 'total_main_page',
			'type'    => 'page',
			'desc'    => \esc_html__( 'Used for breadcrumbs when using a custom page as the post type archive.', 'total' ),
		];

		$fields[] =	[
			'name'    => \esc_html__( 'Main Taxonomy', 'total' ),
			'id'      => 'total_main_taxonomy',
			'type'    => 'taxonomy',
			'desc'    => \esc_html__( 'Used for breadcrumbs, post meta categories and related items.', 'total' ),
		];

		$fields[] =	[
			'name' => \esc_html__( 'Custom Sidebar', 'total' ),
			'id'   => 'total_custom_sidebar',
			'type' => 'text',
			'desc' => \esc_html__( 'Enter a name to create a custom sidebar for the post type archive, single posts and attached taxonomies. The sidebar name can\'t contain any spaces and must use letters only. This is an older option and we recommend instead creating a custom widget area via Appearance > Widget Areas for your post type.', 'total' ),
		];

		return [
			'id'     => 'total_ptu',
			'title'  => \esc_html__( 'Theme Settings - General', 'total' ),
			'fields' => $fields,
		];
	}

	/**
	 * Post Type archive options.
	 */
	public static function type_archive_metabox(): array {
		return [
			'id'        => 'total_ptu_type_archive',
			'title'     => \esc_html__( 'Theme Settings - Archives', 'total' ),
			'condition' => [ 'has_archive', '=', 'true' ],
			'fields'    => [
				[
					'name'    => \esc_html__( 'Dynamic Template', 'total' ),
					'id'      => 'total_archive_template_id',
					'type'    => 'select',
					'choices' => self::class . '::choices_archive_template',
				],
				[
					'name' => \esc_html__( 'Custom Title', 'total' ),
					'id'   => 'total_archive_page_header_title',
					'type' => 'text',
				],
				[
					'name'    => \esc_html__( 'Title Style', 'total' ),
					'id'      => 'total_archive_page_header_title_style',
					'type'    => 'select',
					'choices' => self::class . '::choices_page_header_styles',
				],
				[
					'name'    => \esc_html__( 'Layout', 'total' ),
					'id'      => 'total_archive_layout',
					'type'    => 'select',
					'choices' => 'wpex_get_post_layouts',
				],
				[
					'name' => \esc_html__( 'Post Count', 'total' ),
					'id'   => 'total_archive_posts_per_page',
					'type' => 'text',
					'desc' => \esc_html__( 'How many posts do you want to display before showing the post pagination? Enter -1 to display all of them without pagination.', 'total' ),
				],
				[
					'name'    => \esc_html__( 'Post Order', 'total' ),
					'id'      => 'total_archive_order',
					'type'    => 'select',
					'choices' => self::class . '::choices_order',
				],
				[
					'name'    => \esc_html__( 'Post Order By', 'total' ),
					'id'      => 'total_archive_orderby',
					'type'    => 'select',
					'choices' => self::class . '::choices_orderby',
				],
				[
					'name'      => \esc_html__( 'Pagination Style', 'total' ),
					'id'        => 'total_archive_pagination_style',
					'type'      => 'select',
					'choices'   => 'TotalTheme\Pagination\Core::choices',
					'condition' => [ 'total_archive_template_id', '=', '' ],
				],
				[
					'name'      => \esc_html__( 'Columns', 'total' ),
					'id'        => 'total_archive_grid_columns',
					'type'      => 'select',
					'choices'   => self::class . '::choices_grid_columns',
					'condition' => [ 'total_archive_template_id', '=', '' ],
				],
				[
					'name'    => \esc_html__( 'Grid Style', 'total' ),
					'id'      => 'total_archive_grid_style',
					'type'    => 'select',
					'choices' => [
						''        => \esc_html__( 'Default', 'total' ),
						'masonry' => \esc_html__( 'Masonry', 'total' ),
					],
					'condition' => [ 'total_archive_template_id', '=', '' ],
				],
				[
					'name'      => \esc_html__( 'Gap', 'total' ),
					'id'        => 'total_archive_grid_gap',
					'type'      => 'select',
					'choices'   => 'wpex_column_gaps',
					'condition' => [ 'total_archive_template_id', '=', '' ],
				],
				[
					'name'      => \esc_html__( 'Card Style', 'total' ),
					'id'        => 'total_entry_card_style',
					'type'      => 'select',
					'choices'   => 'WPEX_Card::get_grouped_card_style_options',
					'condition' => [ 'total_archive_template_id', '=', '' ],
				],
				[
					'name'    => \esc_html__( 'Entry Image Overlay', 'total' ),
					'id'      => 'total_entry_overlay_style',
					'type'    => 'select',
					'choices' => totaltheme_call_static( 'Overlays', 'get_style_choices' ),
					'condition' => [ 'total_archive_template_id', '=', '' ],
				],
				[
					'name'      => \esc_html__( 'Blocks', 'total' ),
					'desc'      => \esc_html__( 'Used when a custom card hasn\'t been selected.', 'total' ),
					'id'        => 'total_entry_blocks',
					'type'      => 'multi_select',
					'default'   => [ 'media', 'title', 'meta', 'content', 'readmore' ],
					'choices'   => totaltheme_call_static( 'CPT\Entry_Blocks', 'choices' ),
					'condition' => [ 'total_archive_template_id', '=', '' ],
				],
				[
					'name'    => \esc_html__( 'Meta', 'total' ),
					'desc'    => \esc_html__( 'Used when a custom card hasn\'t been selected.', 'total' ),
					'id'      => 'total_entry_meta_blocks',
					'type'    => 'multi_select',
					'default' => [ 'date', 'author', 'categories', 'comments' ],
					'choices' => [
						'date'       => \esc_html__( 'Date', 'total' ),
						'author'     => \esc_html__( 'Author', 'total' ),
						'categories' => \esc_html__( 'Categories (Main Taxonomy)', 'total' ),
						'comments'   => \esc_html__( 'Comments', 'total' ),
					],
					'condition' => [ 'total_archive_template_id', '=', '' ],
				],
				[
					'name'        => \esc_html__( 'Excerpt Length', 'total' ),
					'id'          => 'total_entry_excerpt_length',
					'type'        => 'number', // important to allow 0 to save and -1
					'min'         => '-1',
					'step'        => '1',
					'max'         => '9999',
					'placeholder' => '40',
					'desc'        => \esc_html__( 'Number of words to display for your excerpt. Enter -1 to display the full post content. Note: custom excerpts are not trimmed.', 'total' ),
					'condition' => [ 'total_archive_template_id', '=', '' ],
				],
				[
					'name' => \esc_html__( 'Read More Button Text', 'total' ),
					'id'   => 'total_entry_readmore_text',
					'type' => 'text',
					'condition' => [ 'total_archive_template_id', '=', '' ],
				],
			],
		];
	}

	/**
	 * Post Type single options.
	 */
	public static function type_single_metabox(): array {
		return [
			'id'     => 'total_ptu_type_single',
			'title'  => \esc_html__( 'Theme Settings - Single Post', 'total' ),
			'fields' => [
				[
					'name'    => \esc_html__( 'Use Blank Template', 'total' ),
					'desc'    => \esc_html__( 'Enable to use a blank template for your post type. This will remove all parts of the site (top bar, header, callout, footer) exept your dynamic template and post content.', 'total' ),
					'id'      => 'total_use_blank_template',
					'type'    => 'checkbox',
					'default' => false,
				],
				[
					'name'    => \esc_html__( 'Dynamic Template', 'total' ),
					'id'      => 'total_singular_template_id',
					'type'    => 'select',
					'desc'    => \esc_html__( 'Select a template to be used for your singular post design.', 'total' ),
					'choices' => self::class . '::choices_single_template',
				],
				[
					'name' => \esc_html__( 'Title', 'total' ),
					'id'   => 'total_page_header_title',
					'type' => 'text',
					'desc' => \esc_html__( 'Use {{title}} to display the current title.', 'total' ),
					'condition' => [ 'total_use_blank_template', '=', 'false' ],
				],
				[
					'name'    => \esc_html__( 'Title Style', 'total' ),
					'id'      => 'total_page_header_title_style',
					'type'    => 'select',
					'choices' => self::class . '::choices_page_header_styles',
					'condition' => [ 'total_use_blank_template', '=', 'false' ],
				],
				[
					'name'    => \esc_html__( 'Title Tag', 'total' ),
					'id'      => 'total_page_header_title_tag',
					'type'    => 'select',
					'desc'    => \esc_html__( 'The theme uses a "span" for the default title tag unless the title field above contains the {{title}} variable.', 'total' ),
					'choices' => [
						''     => \esc_html__( 'Default', 'total' ),
						'h1'   => 'h1',
						'h2'   => 'h2',
						'h3'   => 'h3',
						'h4'   => 'h4',
						'h5'   => 'h5',
						'h6'   => 'h6',
						'div'  => 'div',
						'span' => 'span',
					],
					'condition' => [ 'total_use_blank_template', '=', 'false' ],
				],
				[
					'name'    => \esc_html__( 'Layout', 'total' ),
					'id'      => 'total_blank_template_layout',
					'type'    => 'select',
					'default' => 'full-width',
					'choices' => [
						'full-width'  => esc_html__( 'No Sidebar', 'total' ),
						'full-screen' => esc_html__( 'Full Screen', 'total' ),
					],
					'condition' => [ 'total_use_blank_template', '=', 'true' ],
				],
				[
					'name'    => \esc_html__( 'Layout', 'total' ),
					'id'      => 'total_post_layout',
					'type'    => 'select',
					'choices' => 'wpex_get_post_layouts',
					'condition' => [ 'total_use_blank_template', '=', 'false' ],
				],
				[
					'name'    => \esc_html__( 'Blocks', 'total' ),
					'id'      => 'total_single_blocks',
					'type'    => 'multi_select',
					'default' => [
						'media',
						'title',
						'meta',
						'post-series',
						'content',
						'page-links',
						'share',
						'author-bio',
						'related',
						'comments'
					],
					'choices' => totaltheme_call_static( 'CPT\Single_Blocks', 'choices' ),
					'condition' => [ 'total_singular_template_id', '=', '' ],
				],
				[
					'name'    => \esc_html__( 'Meta', 'total' ),
					'id'      => 'total_single_meta_blocks',
					'type'    => 'multi_select',
					'default' => [ 'date', 'author', 'categories', 'comments' ],
					'choices' => [
						'date'       => \esc_html__( 'Date', 'total' ),
						'author'     => \esc_html__( 'Author', 'total' ),
						'categories' => \esc_html__( 'Categories (Main Taxonomy)', 'total' ),
						'comments'   => \esc_html__( 'Comments', 'total' ),
					],
					'condition' => [ 'total_singular_template_id', '=', '' ],
				],
				[
					'name'    => \esc_html__( 'Next/Previous Links', 'total' ),
					'id'      => 'total_next_prev',
					'type'    => 'checkbox',
					'default' => true,
					'condition' => [ 'total_use_blank_template', '=', 'false' ],
				],
			]

		];
	}

	/**
	 * Post Type related options.
	 */
	public static function type_related_metabox(): array {
		return [
			'id'        => 'total_ptu_type_related',
			'title'     => \esc_html__( 'Theme Settings - Related Posts', 'total' ),
			'condition' => [ 'total_singular_template_id', '=', '' ],
			'fields'    => [
				[
					'name'    => \esc_html__( 'Related By', 'total' ),
					'id'      => 'total_related_taxonomy',
					'type'    => 'select',
					'choices' => self::class . '::choices_related_by',
				],
				[
					'name'    => \esc_html__( 'Order', 'total' ),
					'id'      => 'total_related_order',
					'type'    => 'select',
					'choices' => [
						''     => \esc_html__( 'Default', 'total' ),
						'desc' => \esc_html__( 'DESC', 'total' ),
						'asc'  => \esc_html__( 'ASC', 'total' ),
					],
				],
				[
					'name'    => \esc_html__( 'Order By', 'total' ),
					'id'      => 'total_related_orderby',
					'type'    => 'select',
					'choices' => [
						''     => \esc_html__( 'Default', 'total' ),
						'date'          => \esc_html__( 'Date', 'total' ),
						'title'         => \esc_html__( 'Title', 'total' ),
						'modified'      => \esc_html__( 'Modified', 'total' ),
						'author'        => \esc_html__( 'Author', 'total' ),
						'rand'          => \esc_html__( 'Random', 'total' ),
						'comment_count' => \esc_html__( 'Comment Count', 'total' ),
					],
				],
				[
					'name' => \esc_html__( 'Post Count', 'total' ),
					'id'   => 'total_related_count',
					'type' => 'text',
				],
				[
					'name'    => \esc_html__( 'Columns', 'total' ),
					'id'      => 'total_related_columns',
					'type'    => 'select',
					'choices' => self::class . '::choices_grid_columns',
				],
				[
					'name'    => \esc_html__( 'Gap', 'total' ),
					'id'      => 'total_related_gap',
					'type'    => 'select',
					'choices' => 'wpex_column_gaps',
				],
				[
					'name'    => \esc_html__( 'Card Style', 'total' ),
					'id'      => 'total_related_entry_card_style',
					'type'    => 'select',
					'choices' => self::class . '::choices_card_styles',
				],
				[
					'name'    => \esc_html__( 'Entry Image Overlay', 'total' ),
					'id'      => 'total_related_entry_overlay_style',
					'type'    => 'select',
					'choices' => totaltheme_call_static( 'Overlays', 'get_style_choices' ),
				],
				[
					'name'        => \esc_html__( 'Excerpt Length', 'total' ),
					'id'          => 'total_related_entry_excerpt_length',
					'type'        => 'number', // important to allow 0 to save and -1
					'min'         => '-1',
					'step'        => '1',
					'max'         => '9999',
					'placeholder' => '15',
					'desc'        => \esc_html__( 'Number of words to display for your excerpt. Enter -1 to display the full post content. Note: custom excerpts are not trimmed.', 'total' ),
				],
			]
		];
	}

	/**
	 * Taxonomy general options.
	 */
	public static function tax_general_metabox(): array {
		$fields = [];

		$fields[] = [
			'name'                 => \esc_html__( 'Main Page', 'total' ),
			'id'                   => 'total_tax_main_page',
			'type'                 => 'page',
			'desc'                 => \esc_html__( 'Used for breadcrumbs.', 'total' ),
			'include_cpt_archives' => true,
		];

		if ( \class_exists( 'TotalThemeCore\Term_Thumbnails', false ) ) {
			$fields[] = [
				'name'    => \esc_html__( 'Term Thumbnail', 'total' ),
				'id'      => 'total_tax_term_thumbnails',
				'type'    => 'checkbox',
				'default' => true,
				'desc'    => \esc_html__( 'Enables the "Image" field when adding or editing terms.', 'total' ),
			];
		}

		if ( \class_exists( 'TotalThemeCore\Term_Colors', false ) ) {
			$fields[] = [
				'name'    => \esc_html__( 'Term Colors', 'total' ),
				'id'      => 'total_tax_term_colors',
				'type'    => 'checkbox',
				'default' => false,
				'desc'    => \esc_html__( 'Enables the "Color" field when adding or editing terms.', 'total' ),
			];
		}

		return [
			'id'     => 'total_ptu_tax_general',
			'title'  => \esc_html__( 'Theme Settings - General', 'total' ),
			'fields' => $fields,
		];
	}

	/**
	 * Taxonomy archive options.
	 */
	public static function tax_archive_metabox(): array {
		return [
			'id'     => 'total_ptu_tax_archives',
			'title'  => \esc_html__( 'Theme Settings - Archives', 'total' ),
			'fields' => [
				[
					'name'    => \esc_html__( 'Title Style', 'total' ),
					'id'      => 'total_tax_page_header_title_style',
					'type'    => 'select',
					'choices' => self::class . '::choices_page_header_styles',
				],
				[
					'name' => \esc_html__( 'Custom Title', 'total' ),
					'id'   => 'total_tax_page_header_title',
					'type' => 'text',
					'desc' => \esc_html__( 'Use {{title}} to display the current title.', 'total' ),
				],
				[
					'name'    => \esc_html__( 'Dynamic Template', 'total' ),
					'id'      => 'total_tax_template_id',
					'type'    => 'select',
					'choices' => self::class . '::choices_archive_template',
				],
				[
					'name'    => \esc_html__( 'Layout', 'total' ),
					'id'      => 'total_tax_layout',
					'type'    => 'select',
					'choices' => 'wpex_get_post_layouts',
				],
				[
					'name' => \esc_html__( 'Post Count', 'total' ),
					'id'   => 'total_tax_posts_per_page',
					'type' => 'text',
					'desc' => \esc_html__( 'How many posts do you want to display before showing the post pagination? Enter -1 to display all of them without pagination.', 'total' ),
				],
				[
					'name'    => \esc_html__( 'Post Order', 'total' ),
					'id'      => 'total_tax_order',
					'type'    => 'select',
					'choices' => self::class . '::choices_order',
				],
				[
					'name'    => \esc_html__( 'Post Order By', 'total' ),
					'id'      => 'total_tax_orderby',
					'type'    => 'select',
					'choices' => self::class . '::choices_orderby',
				],
				[
					'name'      => \esc_html__( 'Pagination Style', 'total' ),
					'id'        => 'total_tax_pagination_style',
					'type'      => 'select',
					'choices'   => 'TotalTheme\Pagination\Core::choices',
					'condition' => [ 'total_tax_template_id', '=', '' ],
				],
				[
					'name'    => \esc_html__( 'Sidebar', 'total' ),
					'id'      => 'total_tax_sidebar',
					'type'    => 'select',
					'choices' => 'wpex_choices_widget_areas',
				],
				[
					'name'      => \esc_html__( 'Columns', 'total' ),
					'id'        => 'total_tax_grid_columns',
					'type'      => 'select',
					'choices'   => 'wpex_grid_columns',
					'condition' => [ 'total_tax_template_id', '=', '' ],
				],
				[
					'name'      => \esc_html__( 'Grid Style', 'total' ),
					'id'        => 'total_tax_grid_style',
					'type'      => 'select',
					'condition' => [ 'total_tax_template_id', '=', '' ],
					'choices'   => [
						''        => \esc_html__( 'Default', 'total' ),
						'masonry' => \esc_html__( 'Masonry', 'total' ),
					],
				],
				[
					'name'      => \esc_html__( 'Gap', 'total' ),
					'id'        => 'total_tax_grid_gap',
					'type'      => 'select',
					'choices'   => 'wpex_column_gaps',
					'condition' => [ 'total_tax_template_id', '=', '' ],
				],
				[
					'name'    => \esc_html__( 'Description Position', 'total' ),
					'id'      => 'total_tax_term_description_position',
					'type'    => 'select',
					'choices' => [
						'subheading' => \esc_html__( 'Under Title', 'total' ),
						'above_loop' => \esc_html__( 'Before Entries', 'total' ),
						'hidden'     => \esc_html__( 'Hidden', 'total' ),
					],
				],
				[
					'name'      => \esc_html__( 'Page Header Thumbnail', 'total' ),
					'id'        => 'total_tax_term_page_header_image_enabled',
					'type'      => 'checkbox',
					'default'   => true,
					'condition' => [ 'total_tax_term_thumbnails', '=', 'true' ],
				],
				[
					'name'      => \esc_html__( 'Card Style', 'total' ),
					'id'        => 'total_tax_entry_card_style',
					'type'      => 'select',
					'choices'   => 'WPEX_Card::get_grouped_card_style_options',
					'condition' => [ 'total_tax_template_id', '=', '' ],
				],
				[
					'name'      => \esc_html__( 'Entry Image Overlay', 'total' ),
					'id'        => 'total_tax_entry_overlay_style',
					'type'      => 'select',
					'choices'   => totaltheme_call_static( 'Overlays', 'get_style_choices' ),
					'condition' => [ 'total_tax_template_id', '=', '' ],
				],
				[
					'name'      => \esc_html__( 'Image Size', 'total' ),
					'id'        => 'total_tax_entry_image_size',
					'type'      => 'image_size',
					'condition' => [ 'total_tax_template_id', '=', '' ],
				],
				[
					'name' => \esc_html__( 'Excerpt Length', 'total' ),
					'id'   => 'total_tax_entry_excerpt_length',
					'type' => 'number', // important to allow 0 to save and -1
					'min'  => '-1',
					'step' => '1',
					'max'  => '9999',
					'desc' => \esc_html__( 'Number of words to display for your excerpt. Enter -1 to display the full post content. Note: custom excerpts are not trimmed.', 'total' ),
					'condition' => [ 'total_tax_template_id', '=', '' ],
				],
			]
		];
	}

	/**
	 * Grid column choices.
	 */
	public static function choices_grid_columns(): array {
		return [ '' => \esc_html__( 'Default', 'total' ) ] + wpex_grid_columns();
	}

	/**
	 * Order choices.
	 */
	public static function choices_order(): array {
		return [
			''     => \esc_html__( 'Default', 'total' ),
			'DESC' => \esc_html__( 'Descending', 'total' ),
			'ASC'  => \esc_html__( 'Ascending', 'total' ),
		];
	}

	/**
	 * Orderby choices.
	 */
	public static function choices_orderby(): array {
		return [
			''               => \esc_html__( 'Default', 'total' ),
			'date'           => \esc_html__( 'Date', 'total' ),
			'title'          => \esc_html__( 'Title', 'total' ),
			'name'           => \esc_html__( 'Name (post slug)', 'total' ),
			'modified'       => \esc_html__( 'Modified', 'total' ),
			'author'         => \esc_html__( 'Author', 'total' ),
			'parent'         => \esc_html__( 'Parent', 'total' ),
			'ID'             => \esc_html__( 'ID', 'total' ),
			'comment_count'  => \esc_html__( 'Comment Count', 'total' ),
			'menu_order'     => \esc_html__( 'Menu Order', 'total' ),
		];
	}

	/**
	 * Return template choices.
	 */
	protected static function choices_template( string $template_type ): array {
		$choices = [
			'' => esc_html__( '— Select —', 'total' ),
		];
		if ( $theme_builder = totaltheme_get_instance_of( 'Theme_Builder' ) ) {
			$templates = $theme_builder->get_template_choices( $template_type, false );
			if ( $templates ) {
				$choices = $choices + $templates;
			}
		}
		return $choices;
	}

	/**
	 * Single template Choices.
	 */
	public static function choices_single_template(): array {
		return self::choices_template( 'single' );
	}

	/**
	 * Archive template Choices.
	 */
	public static function choices_archive_template(): array {
		return self::choices_template( 'archive' );
	}

	/**
	 * Related by choices.
	 */
	public static function choices_related_by(): array {
		$choices = [
			''     => \esc_html__( '— Select —', 'total' ),
			'null' => \esc_html__( 'Anything', 'total' ),
		];
		$taxonomies = \get_taxonomies( [
			'public' => true,
		], 'objects' );
		if ( $taxonomies && ! \is_wp_error( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$choices[ $taxonomy->name ] = "{$taxonomy->label} ({$taxonomy->name})";
			}
		}
		return $choices;
	}

	/**
	 * Page Header Styles Select.
	 *
	 * We add a new "default" style that can be used to reset the global page header style.
	 */
	public static function choices_page_header_styles(): array {
		$styles = (array) totaltheme_call_static( 'Page\Header', 'style_choices' );
		$styles['default'] = \esc_html__( 'Standard', 'total' );
		return $styles;
	}

	/**
	 * Get array of registered custom post types.
	 */
	public static function get_post_types( bool $manual = false ): array {
		// Manually grab post types for cases where we need them at a hook <= init 1.
		if ( $manual ) {
			$ptu_types = \get_posts( [
				'numberposts' 	   => 100,
				'post_type' 	   => 'ptu',
				'post_status'      => 'publish',
				'suppress_filters' => false,
				'fields'           => 'ids',
			] );
			if ( \is_array( $ptu_types ) ) {
				$types = [];
				foreach ( $ptu_types as $ptu_type_id ) {
					if ( $ptu_type_name = get_post_meta( $ptu_type_id, '_ptu_name', true ) ) {
						$types[ $ptu_type_name ] = $ptu_type_id;
					}
				}
			}
		}
		// Grab registered types directly from the PTU plugin.
		else {
			if ( \is_callable( '\PTU\PostTypes::get_registered_items' ) ) {
				$types = \PTU\PostTypes::get_registered_items();
			}
		}
		return ( isset( $types ) && \is_array( $types ) ) ? $types : [];
	}

	/**
	 * Get array of registered custom taxonomies.
	 */
	public static function get_taxonomies() {
		if ( \is_callable( '\PTU\Taxonomies::get_registered_items' ) ) {
			$taxonomies = \PTU\Taxonomies::get_registered_items();
		}
		return ( isset( $taxonomies ) && \is_array( $taxonomies ) ) ? $taxonomies : [];
	}

	/**
	 * Return post type meta value.
	 */
	public static function get_setting_value( $post_type, $setting_id, $default = '' ) {
		$types = self::get_post_types();
		$setting_id = "_ptu_total_{$setting_id}";
		if ( $types && \is_string( $post_type ) && ! empty( $types[ $post_type ] ) ) {
			if ( $default && ! \metadata_exists( 'post', $types[ $post_type ], $setting_id ) ) {
				return $default;
			}
			return \get_post_meta( $types[ $post_type ], $setting_id, true );
		}
	}

	/**
	 * Return meta value.
	 */
	public static function get_tax_setting_value( $tax, $setting_id, $default = '' ) {
		$taxes = self::get_taxonomies();
		$setting_id = "_ptu_total_tax_{$setting_id}";
		if ( $taxes && \is_string( $tax ) && ! empty( $taxes[ $tax ] ) ) {
			if ( $default && ! \metadata_exists( 'post', $taxes[ $tax ], $setting_id ) ) {
				return $default;
			}
			return \get_post_meta( $taxes[ $tax ], $setting_id, true );
		}
	}

	/**
	 * Hooks into "totalthemecore/meta/main_metabox/post_types".
	 */
	public static function filter_main_metabox_post_types( $types ) {
		foreach ( self::get_post_types() as $type => $id ) {
			if ( \get_post_meta( $id, '_ptu_total_ps_meta', true ) || \get_post_meta( $id, '_ptu_total_ps_meta_media', true ) ) {
				$types[ $type ] = $type;
			}
		}
		return $types;
	}

	/**
	 * Hooks into "totalthemecore/meta/main_metabox/has_core_fields".
	 */
	public static function filter_main_metabox_has_core_fields( $check, $post_type ) {
		$ptu_type_id = self::get_post_types()[ $post_type ] ?? '';
		if ( $ptu_type_id ) {
			return \wp_validate_boolean( \get_post_meta( $ptu_type_id, '_ptu_total_ps_meta', true ) );
		}
		return $check;
	}

	/**
	 * Hooks into "totalthemecore/meta/main_metabox/has_media_fields".
	 */
	public static function filter_main_metabox_has_media_fields( $check, $post_type ) {
		$ptu_type_id = self::get_post_types()[ $post_type ] ?? '';
		if ( $ptu_type_id && \wp_validate_boolean( \get_post_meta( $ptu_type_id, '_ptu_total_ps_meta_media', true ) ) ) {
			return true;
		}
		return $check;
	}

	/**
	 * Enable card metabox for types.
	 */
	public static function metabox_card( $types ) {
		foreach ( self::get_post_types() as $type => $id ) {
			if ( \get_post_meta( $id, '_ptu_total_ps_meta_card', true ) ) {
				$types[ $type ] = $type;
			}
		}
		return $types;
	}

	/**
	 * Enable image sizes.
	 */
	public static function filter_wpex_image_sizes_tabs( $tabs ) {
		foreach ( self::get_post_types() as $type => $id ) {
			if ( \get_post_meta( $id, '_ptu_total_image_sizes', true )
				&& $postType = \get_post_type_object( $type )
			) {
				$tabs[ $type ] = $postType->labels->singular_name;
			}
		}
		return $tabs;
	}

	/**
	 * Add image size options.
	 */
	public static function filter_wpex_image_sizes( $sizes ) {
		foreach ( self::get_post_types() as $type => $id ) {
			if ( \get_post_meta( $id, '_ptu_total_image_sizes', true ) ) {
				$sizes[ "{$type}_archive" ] = [
					'label'   => \esc_html__( 'Archive', 'total' ),
					'section' => $type,
				];
				$sizes[ "{$type}_single" ] = [
					'label'   => \esc_html__( 'Post', 'total' ),
					'section' => $type,
					// This size has custom mod names for the single image size.
					'width'   => "{$type}_post_image_width",
					'height'  => "{$type}_post_image_height",
					'crop'    => "{$type}_post_image_crop",
				];
				$sizes[ "{$type}_single_related" ] = [
					'label'   => \esc_html__( 'Post Related Items', 'total' ),
					'section' => $type,
				];
			}
		}
		return $sizes;
	}

	/**
	 * Register sidebars.
	 */
	public static function register_sidebars( $sidebars ) {
		foreach ( self::get_post_types( true ) as $type => $id ) {
			$sidebar = (string) \get_post_meta( $id, '_ptu_total_custom_sidebar', true );
			if ( $sidebar ) {
				$id = \sanitize_text_field( $sidebar );
				$id = \str_replace( ' ', '_', $sidebar );
				$id = \strtolower( $sidebar );
				$sidebars[ $id ] = $sidebar;
			}
		}
		return $sidebars;
	}

	/**
	 * Register post series for selected post types.
	 */
	public static function register_post_series() {
		foreach ( self::get_post_types() as $type => $id ) {
			$check = \get_post_meta( $id, '_ptu_total_post_series', true );
			if ( \wp_validate_boolean( $check ) ) {
				\register_taxonomy_for_object_type( 'post_series', $type );
			}
		}
	}

	/**
	 * Enable gallery metabox.
	 */
	public static function wpex_gallery_metabox_post_types( $types ) {
		foreach ( self::get_post_types() as $type => $id ) {
			if ( \get_post_meta( $id, '_ptu_total_post_gallery', true ) ) {
				$types[ $id ] = $type;
			}
		}
		return $types;
	}

	/**
	 * Enable admin thumbnails.
	 */
	public static function wpex_dashboard_thumbnails_post_types( $types ) {
		foreach ( self::get_post_types() as $type => $id ) {
			if ( \get_post_meta( $id, '_ptu_total_show_admin_thumbnails', true ) ) {
				$types[ $id ] = $type;
			}
		}
		return $types;
	}

	/**
	 * Deprecated methods.
	 */
	public static function metabox_main( $types ) {
		\_deprecated_function( __METHOD__, 'Total Theme 5.18' );
	}

	public static function metabox_media( $settings, $post ) {
		\_deprecated_function( __METHOD__, 'Total Theme 5.18' );
	}

}
