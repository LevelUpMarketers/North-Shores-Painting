<?php

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Under Construction.
 */
class Under_Construction {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		if ( \wpex_is_request( 'admin' ) ) {
			\add_action( 'admin_menu', [ self::class, 'add_submenu_page' ], 5 );
			\add_action( 'admin_init', [ self::class, 'register_page_options' ] );
		}

		if ( \wpex_is_request( 'frontend' ) && \get_theme_mod( 'under_construction', false ) ) {
			\add_action( 'template_redirect', [ self::class, 'redirect' ] );
		}
	}

	/**
	 * Add sub menu page for the custom CSS input.
	 */
	public static function add_submenu_page() {
		$hook_suffix = \add_submenu_page(
			\WPEX_THEME_PANEL_SLUG,
			\esc_html__( 'Under Construction', 'total' ),
			\esc_html__( 'Under Construction', 'total' ),
			'edit_theme_options',
			\WPEX_THEME_PANEL_SLUG . '-under-construction',
			[ self::class, 'render_admin_page' ]
		);

		\add_action( "load-{$hook_suffix}", [ self::class, 'admin_help_tab' ] );
	}

	/**
	 * Add admin help tab.
	 */
	public static function admin_help_tab() {
		$screen = \get_current_screen();

		if ( ! $screen ) {
			return;
		}

		$screen->add_help_tab(
			[
				'id'      => 'totaltheme_under_construction',
				'title'   => \esc_html__( 'Overview', 'total' ),
				'content' => '<p>' . \esc_html__( 'Enable to redirect all non-logged in traffic to a specific page. This functionality is useful when building the site or making changes to the site and you don\'t want anyone viewing the website.', 'total' ) . '</p>'
			]
		);
	}

	/**
	 * Function that will register admin page options.
	 */
	public static function register_page_options() {

		// Register settings.
		\register_setting( 'wpex_under_construction', 'under_construction', [
			'show_in_rest'      => false,
			'sanitize_callback' => [ self::class, 'save_options' ],
		] );

		// Add main section to our options page.
		\add_settings_section(
			'wpex_under_construction_main',
			false,
			[ self::class, 'section_main_callback' ],
			'wpex-under-construction-admin'
		);

		// Enable field.
		\add_settings_field(
			'under_construction',
			\esc_html__( 'Enable Under Constuction', 'total' ),
			[ self::class, 'enable_field_callback' ],
			'wpex-under-construction-admin',
			'wpex_under_construction_main',
			[
				'label_for' => 'wpex-under-construction-enable',
			]
		);

		// Under construction page select field.
		\add_settings_field(
			'under_construction_page_id',
			\esc_html__( 'Under Construction Page', 'total' ),
			[ self::class, 'content_id_field_callback' ],
			'wpex-under-construction-admin',
			'wpex_under_construction_main',
			[
				'label_for' => 'wpex-under-construction-page-select',
			]
		);

		// Exclude pages field.
		\add_settings_field(
			'under_construction_exclude_pages',
			\esc_html__( 'Exclude Pages From Redirection', 'total' ),
			[ self::class, 'under_construction_exclude_pages_callback' ],
			'wpex-under-construction-admin',
			'wpex_under_construction_main',
			[
				'label_for' => 'wpex-under-construction-exclude-pages-select',
			]
		);

		// Logged in roles.
		\add_settings_field(
			'under_construction_access_roles',
			\esc_html__( 'Site Access Roles', 'total' ),
			[ self::class, 'under_construction_access_roles_callback' ],
			'wpex-under-construction-admin',
			'wpex_under_construction_main',
			[
				'label_for' => 'wpex-under-construction__access-roles-field',
			]
		);

	}

	/**
	 * Save options.
	 */
	public static function save_options( $options ) {
		if ( ! isset( $_POST['totaltheme-under-construction-admin-nonce'] )
			|| ! wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['totaltheme-under-construction-admin-nonce'] ) ), 'totaltheme-under-construction-admin' )
			|| ! \current_user_can( 'edit_theme_options' )
		) {
			return;
		}

		if ( isset( $options['enable'] ) ) {
			\set_theme_mod( 'under_construction', 1 );
		} else {
			\remove_theme_mod( 'under_construction' );
		}

		if ( isset( $options['content_id'] ) ) {
			\set_theme_mod( 'under_construction_page_id', $options['content_id'] );
		}

		if ( isset( $options['exclude_pages'] ) && \is_array( $options['exclude_pages'] ) ) {
			$exclude_pages_sanitized = array_map( '\absint', $options['exclude_pages'] );
			\set_theme_mod( 'under_construction_exclude_pages', $exclude_pages_sanitized );
		} else {
			\remove_theme_mod( 'under_construction_exclude_pages' );
		}

		if ( isset( $options['access_roles'] ) && \is_array( $options['access_roles'] ) ) {
			$access_roles_sanitized = array_map( 'esc_html', $options['access_roles'] );
			\set_theme_mod( 'under_construction_access_roles', $access_roles_sanitized );
		} else {
			\remove_theme_mod( 'under_construction_access_roles' );
		}

		// Don't save in options table.
		return '';
	}

	/**
	 * Main Settings section callback.
	 */
	public static function section_main_callback( $options ) {
		// Leave blank
	}

	/**
	 * Enable under construction field callback.
	 */
	public static function enable_field_callback() {
		$check = \get_theme_mod( 'under_construction', false );

		?>
		<span class="totaltheme-admin-checkbox">
			<input type="checkbox" name="under_construction[enable]" id="wpex-under-construction-enable" <?php \checked( $check, true, true ); ?>>
			<span class="totaltheme-admin-checkbox__track"></span>
			<span class="totaltheme-admin-checkbox__thumb"></span>
		</span>
	<?php
	}

	/**
	 * Under construction page select field callback.
	 */
	public static function content_id_field_callback() {
		\wp_enqueue_script( 'wpex-chosen' );
		\wp_enqueue_style( 'wpex-chosen' );

		// Get construction page id.
		$page_id = \get_theme_mod( 'under_construction_page_id' );
		?>

		<select name="under_construction[content_id]" id="wpex-under-construction-page-select" class="wpex-chosen">

			<option value=""><?php \esc_html_e( 'None', 'total' ); ?></option>

			<?php
			$pages = \get_pages( [
				'exclude' => \get_option( 'page_on_front' ),
			] );
			if ( $pages ) {
				foreach ( $pages as $page ) {
					echo '<option value="' . \absint( $page->ID ) . '"' . \selected( $page_id, $page->ID, false ) . '>' . \esc_attr( $page->post_title ) . '</option>';
				}
			} ?>

		</select>

		<p class="description"><?php \esc_html_e( 'Select your custom page for your under construction display. Every page and post will redirect to your selected page for non-logged in users.', 'total' ) ?></p>

		<?php
		// Display edit and preview buttons.
		if ( $page_id ) { ?>
			<div class="totaltheme-admin-button-group totaltheme-admin-button-group--top-margin">
				<?php if ( $edit_link = \get_edit_post_link( $page_id ) ) { ?>
					<a href="<?php echo \esc_url( $edit_link ); ?>" class="button" target="_blank" rel="noopener noreferrer"><?php \esc_html_e( 'Backend Edit', 'total' ); ?> &#8599;</a>
					<?php if ( \WPEX_VC_ACTIVE ) { ?>
						<a href="<?php echo \esc_url( \admin_url( 'post.php?vc_action=vc_inline&post_id=' . $page_id . '&post_type=page' ) ); ?>" class="button" target="_blank" rel="noopener noreferrer"><?php \esc_html_e( 'Frontend Edit', 'total' ); ?> &#8599;</a>
					<?php } ?>
				<?php } ?>
				<a href="<?php \the_permalink( $page_id ); ?>" class="button button-primary" target="_blank" rel="noopener noreferrer"><?php \esc_html_e( 'Preview', 'total' ); ?> &#8599;</a>
			</div>
		<?php } ?>

	<?php }

	/**
	 * Exclude pages field callback.
	 */
	public static function under_construction_exclude_pages_callback() {
		$exclude_pages = (array) \get_theme_mod( 'under_construction_exclude_pages', [] );
		$pages = \get_pages( array(
			'exclude' => \get_option( 'page_on_front' ),
		) );
		if ( ! $pages ) {
			return;
		} ?>
		<select data-placeholder="<?php \esc_html_e( 'Click to select&hellip;', 'total' ); ?>" multiple name="under_construction[exclude_pages][]" id="wpex-under-construction-exclude-pages-select" class="wpex-chosen-multiselect" style="min-width:270px;">
			<option value=""><?php \esc_html_e( 'None', 'total' ); ?></option>
			<?php
			foreach ( $pages as $page ) {
				echo '<option value="' . \absint( $page->ID ) . '"' . \selected( \in_array( $page->ID, $exclude_pages ), true, false ) . '>' . \esc_attr( $page->post_title ) . '</option>';
			} ?>
		</select>
	<?php }

	/**
	 * Access roles callback.
	 */
	public static function under_construction_access_roles_callback() {
		$access_roles = (array) \get_theme_mod( 'under_construction_access_roles', [] );
		$get_roles = get_editable_roles();
		if ( ! \is_array( $get_roles ) || empty( $get_roles ) ) {
			return;
		}
		?>
		<select data-placeholder="<?php \esc_html_e( 'Click to select&hellip;', 'total' ); ?>" multiple name="under_construction[access_roles][]" id="wpex-under-construction-exclude-pages-select" class="wpex-chosen-multiselect" style="min-width:270px;">
			<option value=""><?php \esc_html_e( 'None', 'total' ); ?></option>
			<?php foreach ( $get_roles as $role_name => $role_info ) {
				echo '<option value="' . \esc_attr( $role_name ) . '"' . \selected( \in_array( $role_name, $access_roles ), true, false ) . '>' . \esc_attr( $role_info['name'] ) . '</option>';
			} ?>
		</select>
		<p class="description"><?php \esc_html_e( 'Limit access to the site to logged in users with specific roles.', 'total' ) ?></p>
	<?php }

	/**
	 * Clear site cache.
	 */
	protected static function clear_cache() {
		if ( \function_exists( 'rocket_clean_domain' ) ) {
			\rocket_clean_domain();
		}
	}

	/**
	 * Settings page output.
	 */
	public static function render_admin_page() {
		if ( ! \current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		\wp_enqueue_style( 'wpex-chosen' );
		\wp_enqueue_script( 'wpex-chosen' );

		\wp_enqueue_style( 'totaltheme-admin-pages' );
		\wp_enqueue_script( 'totaltheme-admin-pages' );

		// Do stuff after saving.
		if ( isset( $_GET['settings-updated'] ) && \wp_validate_boolean( $_GET['settings-updated'] ) ) {
			self::clear_cache();
		}
		?>
		<div class="wrap totaltheme-admin-wrap">
			<?php
			// Display warning if the WooCommerce coming soon page is enabled
			if ( \class_exists( '\WooCommerce', false ) && 'yes' === \get_option( 'woocommerce_coming_soon' ) ) {
				echo '<div class="notice notice-error"><p>' . esc_html__( 'Your site is using the WooCommerce Coming Soon feature which will override the theme\'s Under Construction page. Please go to WooCommerce > Settings > Site Visibility to disable it.', 'total' ) . '</p></div>';
			} ?>
			<form method="post" action="options.php">
				<?php \settings_fields( 'wpex_under_construction' ); ?>
				<?php \do_settings_sections( 'wpex-under-construction-admin' ); ?>
				<?php \wp_nonce_field( 'totaltheme-under-construction-admin', 'totaltheme-under-construction-admin-nonce' ); ?>
				<?php \submit_button(); ?>
			</form>
		</div>
	<?php }

	/**
	 * Redirect all pages to the under construction page if user is not logged in.
	 */
	public static function redirect() {
		$redirect = false;
		$page_id = ( $page_id = \get_theme_mod( 'under_construction_page_id' ) ) ? \wpex_parse_obj_id( \absint( $page_id ), 'page' ) : 0;

		if ( ! $page_id ) {
			return;
		}

		if ( $page_id === \absint( \get_option( 'page_for_posts' ) ) ) {
			return; // prevent infinite loop
		}

		if ( $exclude_pages = \get_theme_mod( 'under_construction_exclude_pages', null ) ) {
			// Allow for lose check because the panel used to save the ID's as strings
			if ( \is_array( $exclude_pages ) && \in_array( wpex_get_current_post_id(), $exclude_pages ) ) {
				return;
			}
		}

		if ( \is_user_logged_in() ) {
			$access_roles = (array) \get_theme_mod( 'under_construction_access_roles', [] );
			if ( $access_roles ) {
				$user = \wp_get_current_user();
				if ( $user && ! empty( $user->roles ) && ! \array_intersect( $access_roles, $user->roles ) ) {
				   $redirect = true;
				}
			}
		} else {
			$redirect = true;
		}

		$redirect = \apply_filters( 'wpex_has_under_construction_redirect', $redirect );

		if ( $redirect ) {
			$permalink = \get_permalink( $page_id );
			if ( $permalink && ! \is_page( $page_id ) ) {
				$redirect = true;
			} else {
				$redirect = false;
			}
		}

		if ( $redirect && ! empty( $permalink ) ) {
			\nocache_headers();
			\wp_safe_redirect( \esc_url( $permalink ), 307, 'Under Construction Redirect' );
			exit();
		}

	}

}
