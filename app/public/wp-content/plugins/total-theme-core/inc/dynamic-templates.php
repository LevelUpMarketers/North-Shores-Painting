<?php

namespace TotalThemeCore;

\defined( 'ABSPATH' ) || exit;

/**
 * Theme Templates Post Type.
 */
class Dynamic_Templates {

	/**
	 * Dynamic Templates post type name.
	 */
	public const POST_TYPE = 'wpex_templates';

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Initialize class.
	 */
	public static function init(): void {
		\add_action( 'init', [ self::class, 'on_init' ] );

		if ( \is_admin() ) {
			if ( \class_exists( '\Vc_Manager', false ) ) {
				\totalthemecore_call_static( 'WPBakery\Helpers', 'enable_editor', self::POST_TYPE );
			}
			\add_action( 'admin_init', [ self::class, 'on_admin_init'] );
		}
	}

	/**
	 * Returns parent menu.
	 */
	public static function on_init(): void {
		self::register_post_type();
		\totalthemecore_init_class( 'Shortcodes\Shortcode_Wpex_Template' );
	}

	/**
	 * Admin hooks.
	 */
	public static function on_admin_init(): void {
		self::register_metabox();

		\add_action( 'admin_head-post.php', [ self::class, 'add_back_button' ] );
		\add_action( 'admin_enqueue_scripts', [ self::class, 'quick_bulk_edit_scripts' ] );
		\add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', [ self::class, 'add_admin_columns' ] );
		\add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', [ self::class, 'display_admin_columns' ], 10, 2 );
		\add_action( 'bulk_edit_custom_box', [ self::class, 'quick_bulk_edit' ], 10, 2 );
		\add_action( 'quick_edit_custom_box', [ self::class, 'quick_bulk_edit' ], 10, 2 );
		\add_action( 'save_post_' . self::POST_TYPE, [ self::class, 'save_post' ], 10, 2 );
		\add_action( 'pre_get_posts', [ self::class, 'admin_filter_results' ] );

		if ( self::maybe_show_templatera_notice() ) {
			\add_action( 'views_edit-' . self::POST_TYPE, [ self::class, 'templatera_notice' ] );
			\add_action( 'wp_ajax_totaltheme_dynamic_templates_migrate_templatera', [ self::class, 'migrate_templatera_ajax' ] );
		}

		\add_filter( 'views_edit-' . self::POST_TYPE, [ self::class, 'admin_tabs' ] );
	}

	/**
	 * Check if the templatera template migration notice is dismissed.
	 */
	protected static function maybe_show_templatera_notice(): bool {
		if ( ! \current_user_can( 'publish_pages' )
			|| \class_exists( 'SitePress', false ) // WPML has bugs.
			|| ! \class_exists( '\VcTemplateManager', false )
			|| \get_option( 'totaltheme_dynamic_templates_migrate_templatera_dissmiss', false )
		) {
			return false;
		}

		if ( isset( $_GET['totaltheme_dynamic_templates_migrate_templatera_dissmiss'] )
			&& '1' === \sanitize_text_field( \wp_unslash( $_GET['totaltheme_dynamic_templates_migrate_templatera_dissmiss'] ) )
		) {
			\update_option( 'totaltheme_dynamic_templates_migrate_templatera_dissmiss', 1, false );
			return false;
		}

		$templatera_templates = new \WP_Query( [
			'posts_per_page'   => 1,
			'post_type'        => 'templatera',
			'fields'           => 'ids',
			'suppress_filters' => true, // Fix for WPML
			'lang'             => '', // polylang fix
		] );

		return ! empty( $templatera_templates->posts );
	}

	/**
	 * Metabox.
	 */
	protected static function register_metabox(): void {
		if ( \class_exists( '\WPEX_Meta_Factory' ) ) {
			new \WPEX_Meta_Factory( [
				'id'       => 'template_type',
				'title'    => \esc_html__( 'Template Type', 'total-theme-core' ),
				'screen'   => [ self::POST_TYPE ],
				'context'  => 'side',
				'priority' => 'default',
				'fields'   => [ self::class, 'get_metabox_fields' ],
			] );
		}
	}

	/**
	 * Metabox fields
	 */
	public static function get_metabox_fields(): array {
		$default_type = '';

		if ( isset( $_GET['wpex_template_type'] )
			&& \array_key_exists( $_GET['wpex_template_type'], self::get_template_types() )
		) {
			$default_type = \sanitize_text_field( \wp_unslash( $_GET['wpex_template_type'] ) );
		}

		return [
			[
				'id'      => 'wpex_template_type',
				'type'    => 'select',
				'desc'    => \esc_html__( 'Selecting a template type is completely optional but helps organize your templates to make it easier to locate and use them.', 'total-theme-core' ),
				'choices' => self::get_template_type_choices(),
				'default' => $default_type,
			],
		];
	}

	/**
	 * Add a back button to the Font Manager main page.
	 */
	public static function add_back_button(): void {
		global $current_screen;

		if ( ! empty( $current_screen->post_type ) && self::POST_TYPE !== $current_screen->post_type ) {
			return;
		}

		\wp_enqueue_script( 'jQuery' );

		?>

		<script>
			jQuery( function() {
				jQuery( 'body.post-type-<?php echo \sanitize_html_class( self::POST_TYPE ); ?> .wrap h1' ).append( '<a href="<?php echo \esc_url( \admin_url( 'edit.php?post_type=' . self::POST_TYPE ) ); ?>" class="page-title-action" style="margin-left:20px"><?php \esc_html_e( 'View All', 'total-theme-core' ); ?></a>' );
			} );
		</script>

		<?php
	}

	/**
	 * Quick/Bulk edit scripts.
	 */
	public static function quick_bulk_edit_scripts( $hook ): void {
		if ( 'edit.php' !== $hook
			|| ! isset( $_GET['post_type'] )
			|| self::POST_TYPE !== sanitize_text_field( wp_unslash( $_GET['post_type'] ) )
		) {
			return;
		}

		\wp_enqueue_script(
			'totalthemecore-admin-dynamic-templates-quick-edit',
			\totalthemecore_get_js_file( 'admin/dynamic-templates-quick-edit' ),
			[ 'jquery' ],
			'1.0',
			true
		);
	}

	/**
	 * Define new admin dashboard columns.
	 */
	public static function add_admin_columns( $columns ): array {
		$columns['wpex_template_type'] = \esc_html__( 'Type', 'total-theme-core' );
		if ( isset( $_GET['wpex_template_type'] ) && 'part' === \sanitize_text_field( \wp_unslash( $_GET['wpex_template_type'] ) ) ) {
			$columns['wpex_template_shortcode'] = \esc_html__( 'Shortcode', 'total-theme-core' );
		}
		$columns['wpex_template_id'] = \esc_html__( 'ID', 'total-theme-core' );
		return $columns;
	}

	/**
	 * Display new admin dashboard columns.
	 */
	public static function display_admin_columns( $column, $post_id ): void {
		$template_type = self::get_template_type( $post_id );
		switch ( $column ) {
			case 'wpex_template_type' :
				if ( $template_type ) {
					echo '<a href="' . \esc_url( \admin_url( 'edit.php?post_type=wpex_templates&wpex_template_type=' . $template_type ) ) . '">' . \esc_html( self::get_template_type_name( $post_id ) ) . '</a>';
				} else {
					echo '&mdash;';
				}
				echo '<input type="hidden" value="' . \esc_attr( $template_type ) . '" disabled>';
			break;
			case 'wpex_template_shortcode' :
				if ( 'part' === $template_type ) {
					echo '<input type="text" onClick="this.select();" value=\'[wpex_template id="' . \esc_attr( \absint( $post_id ) ) . '"]\' readonly>';
				} else {
					echo '&mdash;';
				}
			break;
			case 'wpex_template_id' :
				echo \esc_html( \absint( $post_id ) );
			break;
		}
	}

	/**
	 * Custom quick/bulk edit fields.
	 */
	public static function quick_bulk_edit( $column_name, $post_type ) {
		if ( 'wpex_template_type' !== $column_name || self::POST_TYPE !== $post_type ) {
			return;
		}

		?>

		<fieldset class="inline-edit-col-right">
			<div class="inline-edit-col">
				<div class="inline-edit-group wp-clearfix">
					<label class="inline-edit-wpex_template_type alignleft">
						<span class="title"><?php \esc_html_e( 'Type', 'total-theme-core' ); ?></span>
						<span class="input-text-wrap">
							<select name="wpex_template_type">
								<?php
								foreach ( self::get_template_type_choices() as $key => $value ) {
									echo '<option value="' . \esc_attr( $key ) . '">' . \esc_html( $value ) . '</option>';
								}
								?>
							</select>
						</span>
					</label>
				</div>
			</div>
		</fieldset>

		<?php
	}

	/**
	 * Runs on the save post hook.
	 */
	public static function save_post( $post_id, $post ) {
		if ( self::POST_TYPE !== get_post_type( $post )
			|| \wp_is_post_revision( $post_id )
			|| \wp_is_post_autosave( $post_id )
			|| ! \current_user_can( 'edit_post', $post_id )
		) {
			return;
		}

		// Save inline edit.
		if ( isset( $_POST['_inline_edit'] )
			&& \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['_inline_edit'] ) ), 'inlineeditnonce' )
			&& isset( $_POST['wpex_template_type'] )
		) {
			if ( ! empty( $_POST['wpex_template_type'] ) ) {
				\update_post_meta( $post_id, 'wpex_template_type', \sanitize_text_field( \wp_unslash( $_POST['wpex_template_type'] ) ) );
			} else {
				\delete_post_meta( $post_id, 'wpex_template_type' );
			}
		}

		// Save bulk edit
		if ( isset( $_REQUEST['_wpnonce'] )
			&& \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_REQUEST['_wpnonce'] ) ), 'bulk-posts' )
			&& isset( $_REQUEST['wpex_template_type'] )
		) {
			if ( ! empty( $_REQUEST['wpex_template_type'] ) ) {
				\update_post_meta( $post_id, 'wpex_template_type', \sanitize_text_field(  \wp_unslash( $_REQUEST['wpex_template_type'] ) ) );
			} else {
				\delete_post_meta( $post_id, 'wpex_template_type' );
			}
		}
	}

	/**
	 * Admin tabs.
	 */
	public static function admin_tabs( $views ) {
		$types  = self::get_template_types();
		$active = isset( $_GET['wpex_template_type'] ) ? \sanitize_text_field( \wp_unslash( $_GET['wpex_template_type'] ) ) : '';

		foreach ( $types as $key => $value ) {
			$current_class = ( $key === $active ) ? ' class="current"' : '';
			$views["wpex_template_type_{$key}"] = '<a href="' . \esc_url( \admin_url( "edit.php?post_type=wpex_templates&wpex_template_type={$key}" ) ) . '"' . $current_class . '>' . \esc_html( $value ) . '</a>';
		}

		$current_class = ( 'none' === $active ) ? ' class="current"' : '';

		$views['wpex_template_type_none'] = '<a href="' . \esc_url( \admin_url( "edit.php?post_type=wpex_templates&wpex_template_type=none" ) ) . '"' . $current_class . '>' . \esc_html__( 'Uncategorized', 'total-theme-core' ) . '</a>';

		return $views;
	}

	/**
	 * Filter admin results.
	 */
	public static function admin_filter_results( $query ) {
		global $pagenow;

		if ( ! is_admin()
			|| 'edit.php' !== $pagenow
			|| empty( $_GET['wpex_template_type'] )
			|| empty( $query->query_vars['post_type'] )
			|| self::POST_TYPE !== $query->query_vars['post_type']
			|| ! $query->is_main_query()
		) {
			return;
		}

		$query->query_vars['meta_key'] = 'wpex_template_type';

		$safe_template_type = \sanitize_text_field( \wp_unslash( $_GET['wpex_template_type'] ) );

		if ( 'none' === $safe_template_type ) {
			$query->query_vars['meta_compare'] = 'NOT EXISTS';
		} else {
			$query->query_vars['meta_value'] = $safe_template_type;
		}
	}

	/**
	 * Returns parent menu.
	 */
	protected static function get_parent_menu(): string {
		return ( \defined( '\WPEX_THEME_PANEL_SLUG' ) && \current_user_can( 'edit_theme_options' ) ) ? \WPEX_THEME_PANEL_SLUG : 'tools.php';
	}

	/**
	 * Registers the custom post type.
	 */
	protected static function register_post_type(): void {
		$args = [
			'labels' => [
				'name' => \esc_html__( 'Dynamic Templates', 'total-theme-core' ),
				'singular_name' => \esc_html__( 'Dynamic Template', 'total-theme-core' ),
				'add_new' => \esc_html__( 'Add New' , 'total-theme-core' ),
				'add_new_item' => \esc_html__( 'Add New Dynamic Template' , 'total-theme-core' ),
				'edit_item' => \esc_html__( 'Edit Dynamic Template' , 'total-theme-core' ),
				'new_item' => \esc_html__( 'New Dynamic Template' , 'total-theme-core' ),
				'view_item' => \esc_html__( 'View Dynamic Template', 'total-theme-core' ),
				'search_items' => \esc_html__( 'Search Dynamic Templates', 'total-theme-core' ),
				'not_found' => \esc_html__( 'No Dynamic Templates found', 'total-theme-core' ),
				'not_found_in_trash' => \esc_html__( 'No Dynamic Templates found in Trash', 'total-theme-core' ),
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

		// Add Elementor support.
		\add_post_type_support( self::POST_TYPE, 'elementor' );
	}

	/**
	 * Returns template type given a template ID.
	 */
	public static function get_template_type( int $template_id ): string {
		return (string) \get_post_meta( $template_id, 'wpex_template_type', true ) ?: '';
	}

	/**
	 * Returns template type name given a template ID.
	 */
	public static function get_template_type_name( int $template_id ): string {
		return self::get_template_types()[ self::get_template_type( $template_id ) ] ?? '';
	}

	/**
	 * Returns array of template type choices.
	 */
	protected static function get_template_type_choices(): array {
		if ( $types = self::get_template_types() ) {
			return \array_merge( [
				'' => \esc_html( '— Select —', 'total-theme-core' ),
			], $types );
		}
		return [];
	}

	/**
	 * Returns array of template types.
	 */
	protected static function get_template_types(): array {
		$types = [
			'part'      => \esc_html__( 'Part', 'total-theme-core' ),
		//	'section'   => \esc_html__( 'Section', 'total-theme-core' ), // @todo enable when you can assign templates to other parts of the site.
			'header'    => \esc_html__( 'Header', 'total-theme-core' ),
			'footer'    => \esc_html__( 'Footer', 'total-theme-core' ),
			'single'    => \esc_html__( 'Single', 'total-theme-core' ),
			'archive'   => \esc_html__( 'Archive', 'total-theme-core' ),
			'search'    => \esc_html__( 'Search Results', 'total-theme-core' ),
			'error_404' => \esc_html__( 'Error 404', 'total-theme-core' ),
		];
		if ( \class_exists( '\acf_pro' ) ) {
			$types['acf_repeater'] = \esc_html__( 'ACF Repeater', 'total-theme-core' );
		}
		return (array) \apply_filters( 'totalthemecore/dynamic_templates/template_types', $types );
	}

	/**
	 * Templatera notice.
	 */
	public static function templatera_notice( $views ) {
		$current_screen = \get_current_screen();

		if ( empty( $current_screen->id ) || 'edit-wpex_templates' !== $current_screen->id ) {
			return;
		}

		\wp_enqueue_script(
			'totalthemecore-admin-dynamic-templates-migrate-templatera',
			\totalthemecore_get_js_file( 'admin/dynamic-templates-migrate-templatera' ),
			[ 'jquery' ],
			'1.0',
			true
		);

		?>

		<div class="totaltheme-admin-notice totaltheme-admin-notice--warning" style="margin-block-start:20px">
			<h3>Templatera Templates Found!</h3>
			<p><?php \esc_html_e( 'It looks like you have some Templatera templates, do you want to migrate them to the Dynamic Templates?', 'total-theme-core' ); ?></p>
			<p><a href="#" class="button button-primary" data-totaltheme-dynamic-templates-migrate-templatera data-nonce="<?php echo \esc_attr( \wp_create_nonce( 'totaltheme_dynamic_templates_migrate_templatera' ) ); ?>"><?php \esc_html_e( 'Migrate Templates', 'total-theme-core' ); ?></a></strong></p>
			<p class="totaltheme-admin-notice__loader"><svg height="20px" width="20px" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg"><circle cx="18" cy="18" r="18" fill="#a2a2a2" fill-opacity=".5"/><circle cx="18" cy="8" r="4" fill="#fff"><animateTransform attributeName="transform" dur="1100ms" from="0 18 18" repeatCount="indefinite" to="360 18 18" type="rotate"/></circle></svg></p>
			<a href="<?php echo \esc_url( \add_query_arg( [ 'totaltheme_dynamic_templates_migrate_templatera_dissmiss' => '1' ] ) ); ?>" class="totaltheme-admin-notice__dismiss"><span class="screen-reader-text"><?php \esc_html_e( 'Dismiss Notice', 'total-theme-core' ); ?></span></a>
		</div>
	<?php
		return $views;
	}

	/**
	 * Migrate templatera templates.
	 */
	public static function migrate_templatera_ajax() {
		if ( ! \current_user_can( 'publish_pages' ) ) {
			\wp_die();
		}

		\check_ajax_referer( 'totaltheme_dynamic_templates_migrate_templatera', 'nonce' );

		@set_time_limit(0);

		$templatera_templates = new \WP_Query( [
			'posts_per_page'   => -1,
			'post_type'        => 'templatera',
			'fields'           => 'ids',
			'cache_results'    => false,
			'suppress_filters' => true, // Fix for WPML
			'lang'             => '', // polylang fix
		] );

		$migrated_list = [];

		if ( ! empty( $templatera_templates->posts ) ) {
			foreach ( $templatera_templates->posts as $template ) {
				if ( 'templatera' === \get_post_type( $template ) ) {
					$migrated_list[] = \esc_html( \get_the_title( $template ) );
					\set_post_type( $template, self::POST_TYPE );
				}
			}
		}

		if ( $migrated_list ) {
			echo \wp_json_encode( $migrated_list );
		}

		\wp_die();
	}

}
