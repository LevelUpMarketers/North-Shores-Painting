<?php

namespace TotalThemeCore\Cards;

\defined( 'ABSPATH' ) || exit;

/**
 * Card Builder.
 */
final class Builder {

	/**
	 * Card builder post type name.
	 */
	public const POST_TYPE = 'wpex_card';

	/**
	 * Stores custom cards in memory to prevent extra lookups.
	 */
	private static $custom_cards = null;

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		if ( \is_admin() ) {
			\add_action( 'admin_init', [ self::class, 'on_admin_init' ] );
			\add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', [ self::class, '_modify_admin_columns' ] );
			\add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', [ self::class, '_display_admin_columns' ], 10, 2 );
			if ( \class_exists( '\Vc_Manager', false ) ) {
				\totalthemecore_call_static( 'WPBakery\Helpers', 'enable_editor', self::POST_TYPE );
			}
		}
		\add_action( 'init', [ self::class, 'register_post_type' ] );
		\add_filter( 'wpex_card_styles', [ self::class, 'filter_card_styles' ], 10 );
	}

	/**
	 * Admin init.
	 */
	public static function on_admin_init() {
		\add_action( 'admin_head-post.php', [ self::class, 'add_back_button' ] );

		if ( \class_exists( '\WPEX_Meta_Factory' ) ) {
			new \WPEX_Meta_Factory( self::get_metabox_settings() );
		}
	}

	/**
	 * Define new admin dashboard columns.
	 */
	public static function _modify_admin_columns( $columns ): array {
		unset( $columns['date'] );
		$columns['wpex_card_type'] = \esc_html__( 'Type', 'total-theme-core' );
		$columns['wpex_card_link'] = \esc_html__( 'Link', 'total-theme-core' );
		return $columns;
	}

	/**
	 * Display new admin dashboard columns.
	 */
	public static function _display_admin_columns( $column, $post_id ): void {
		switch ( $column ) {
			case 'wpex_card_type':
				$card_type = \get_post_meta( $post_id, 'type', true ) ?: 'Post';
				echo ( 'term' === $card_type ) ? \esc_html__( 'Term', 'total-theme-core' ) : \esc_html__( 'Post', 'total-theme-core' );
				break;
			case 'wpex_card_link':
				$cf_link = \get_post_meta( $post_id, 'link_custom_field', true );
				if ( $cf_link  ) {
					printf( esc_html__( 'Custom Field: %s', 'total-theme-core' ), $cf_link );
				} else {
					$link_type = \get_post_meta( $post_id, 'link_type', true );
					if ( \is_callable( '\WPEX_Card::get_link_types' ) ) {
						if ( 'post' === $link_type ) {
							$link_type = ''; // legacy option
						}
						echo esc_html( \WPEX_Card::get_link_types()[ $link_type ] ?? '' );
					} else {
						echo esc_html( $link_type );
					}
				}
				break;
		}
	}

	/**
	 * Returns parent menu.
	 */
	protected static function get_parent_menu() {
		if ( \defined( 'WPEX_THEME_PANEL_SLUG' ) && \current_user_can( 'edit_theme_options' ) ) {
			return \WPEX_THEME_PANEL_SLUG;
		}
		return 'tools.php';
	}

	/**
	 * Add a back button to the Font Manager main page.
	 */
	public static function add_back_button() {
		global $current_screen;

		if ( ! empty( $current_screen->post_type ) && self::POST_TYPE !== $current_screen->post_type ) {
			return;
		}

		wp_enqueue_script( 'jQuery' );

		?>

		<script>
			jQuery( function() {
				jQuery( 'body.post-type-<?php echo \sanitize_html_class( self::POST_TYPE ); ?> .wrap h1' ).append( '<a href="<?php echo \esc_url( \admin_url( 'edit.php?post_type=' . self::POST_TYPE ) ); ?>" class="page-title-action" style="margin-left:20px"><?php esc_html_e( 'All Cards', 'total-theme-core' ); ?></a>' );
			} );
		</script>

		<?php
	}

	/**
	 * Register post type.
	 */
	public static function register_post_type() {
		$args = [
			'labels' => [
				'name' => \esc_html__( 'Custom Cards', 'total-theme-core' ),
				'singular_name' => \esc_html__( 'Card', 'total-theme-core' ),
				'add_new' => \esc_html__( 'Add New Card' , 'total-theme-core' ),
				'add_new_item' => \esc_html__( 'Add New Card' , 'total-theme-core' ),
				'edit_item' => \esc_html__( 'Edit Card' , 'total-theme-core' ),
				'new_item' => \esc_html__( 'New Card' , 'total-theme-core' ),
				'view_item' => \esc_html__( 'View Card', 'total-theme-core' ),
				'search_items' => \esc_html__( 'Search Cards', 'total-theme-core' ),
				'not_found' => \esc_html__( 'No Cards found', 'total-theme-core' ),
				'not_found_in_trash' => \esc_html__( 'No Cards found in Trash', 'total-theme-core' ),
			],
			'public' => false,
			'has_archive' => false,
			'query_var' => true,
			'_builtin' => false,
			'show_ui' => true,
			'show_in_rest' => true, // enable Gutenberg.
			'show_in_menu' => self::get_parent_menu(),
			'show_in_nav_menus' => false,
			'show_in_admin_bar' => false,
			'exclude_from_search' => true, // !! important !!
			'publicly_queryable' => false,
			'capability_type' => 'page',
			'hierarchical' => false,
			'menu_position' => null,
			'rewrite' => false,
			'supports' => [
				'title',
				'editor',
			],
			'menu_position' => null,
		];

		if ( totalthemecore_call_static( 'Elementor\Helpers', 'is_cpt_in_frontend_mode', self::POST_TYPE )
			|| totalthemecore_call_static( 'WPBakery\Helpers', 'is_cpt_in_frontend_mode', self::POST_TYPE )
		) {
			$args['public']             = true;
			$args['publicly_queryable'] = true;
		}

		\register_post_type( self::POST_TYPE, $args );

		\add_post_type_support( self::POST_TYPE, 'elementor' );
	}

	/**
	 * Returns array of cards.
	 */
	public static function get_custom_cards(): array {
		if ( null === self::$custom_cards ) {
			self::$custom_cards = [];
			if ( post_type_exists( self::POST_TYPE ) ) {
				$custom_cards_query = new \WP_Query( [
					'posts_per_page'   => '200',
					'orderby'          => 'date',
					'order'            => 'ASC',
					'post_type'        => self::POST_TYPE,
					'post_status'      => 'publish',
					'fields'           => 'all', // since get_the_title() always calls get_post() we might as well grab the posts here
					'suppress_filters' => false, // note: When set to true it causes all translated cards to show up in the dropdown
				] );
				if ( $custom_cards_query->have_posts() ) {
					foreach ( $custom_cards_query->posts as $custom_card ) {
						// note we don't use the_title_attribute because it makes an extra get_post() call
						$card_name = \get_the_title( $custom_card );
						if ( $card_name ) {
							self::$custom_cards[ "template_{$custom_card->ID}" ] = [
								'name'  => $card_name,
								'group' => esc_html__( 'Custom Cards', 'total' ),
							];
						}
					}
				}
			}
		}
		return self::$custom_cards;
	}

	/**
	 * Adds the custom cards to the cards list.
	 */
	public static function filter_card_styles( $styles ) {
		return \array_merge( self::get_custom_cards(), $styles );
	}

	/**
	 * Returns metabox settings.
	 */
	protected static function get_metabox_settings(): array {
		return [
			'id'       => 'wpex-card',
			'title'    => \esc_html__( 'Card Settings', 'total-theme-core' ),
			'screen'   => [ self::POST_TYPE ],
			'context'  => 'advanced',
			'priority' => 'default',
			'fields'   => [ self::class, 'get_metabox_fields' ],
			'scripts'  => [
				[
					'totaltheme-cards-builder-metabox',
					\totalthemecore_get_js_file( 'admin/cards-builder-metabox' ),
					[],
					TTC_VERSION,
					true
				],
			],
		];
	}

	/**
	 * Returns the metabox fields.
	 */
	public static function get_metabox_fields(): array {
		$fields = [
			[
				'name'    => \esc_html__( 'Card Type', 'total-theme-core' ),
				'id'      => 'type',
				'default' => 'post',
				'type'    => 'button_group',
				'desc' => \esc_html__( 'Choose your card type.', 'total-theme-core' ),
				'choices' => [
					'post' => \esc_html__( 'Post', 'total-theme-core' ),
					'term' => \esc_html__( 'Term', 'total-theme-core' ),
				],
			],
			[
				'name' => \esc_html__( 'Frontend Editor Width', 'total-theme-core' ),
				'id'   => 'preview_width',
				'type' => 'text',
				'desc' => \esc_html__( 'Enter a custom width to contain your card while editing in front-end mode. Leave empty to use the default site width.', 'total-theme-core' ),
			],
			[
				'name' => \esc_html__( 'Extra class name', 'total-theme-core' ),
				'id'   => 'el_class',
				'type' => 'text',
				'desc' => \esc_html__( 'Add extra classes to the card element.', 'total-theme-core' ),
			],
		];

		if ( \is_callable( '\WPEX_Card::get_link_types' ) ) {
			$fields[] = [
				'name'    => \esc_html__( 'Link Type', 'total-theme-core' ),
				'id'      => 'link_type',
				'type'    => 'select',
				'choices' =>  'WPEX_Card::get_link_types',
				'desc'    => \esc_html__( 'By default custom cards will have a link around the entire card so any links inside the card will be stripped out. Select the "none" link type if you wish to add your own links within the card.', 'total-theme-core' ),
			];
			$fields[] = [
				'name' => \esc_html__( 'Link Custom Field Name', 'total-theme-core' ),
				'id'   => 'link_custom_field',
				'type' => 'text',
				'desc' => \esc_html__( 'Enter the name of a custom field to use for your card link.', 'total-theme-core' ),
			];
		}

		return $fields;
	}

}
