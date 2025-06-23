<?php declare(strict_types=1);

namespace TotalTheme;

\defined( 'ABSPATH' ) || exit;

/**
 * Initialize Theme.
 */
class Initialize {

	/**
	 * Class instance.
	 */
	protected static $instance;

	/**
	 * Create or retrieve the class instance.
	 */
	public static function instance() {
		if ( \is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->load_functions();

		$this->global();

		if ( \wpex_is_request( 'admin' ) ) {
			$this->admin();
		}

		if ( \wpex_is_request( 'frontend' ) ) {
			$this->frontend();
		}

		$this->init_hooks();
	}

	/**
	 * Load functions.
	 */
	private function load_functions(): void {
		require_once \WPEX_INC_DIR . 'deprecated.php';
		require_once \WPEX_INC_DIR . 'functions/core-functions.php';
		require_once \WPEX_INC_DIR . 'functions/conditionals.php';
		require_once \WPEX_INC_DIR . 'functions/css-utility.php';
		require_once \WPEX_INC_DIR . 'functions/parsers.php';
		require_once \WPEX_INC_DIR . 'functions/sanitize.php';
		require_once \WPEX_INC_DIR . 'functions/arrays.php';
		require_once \WPEX_INC_DIR . 'functions/translations.php';
		require_once \WPEX_INC_DIR . 'functions/template-parts.php';
		require_once \WPEX_INC_DIR . 'functions/fonts.php';
		require_once \WPEX_INC_DIR . 'functions/post-thumbnails.php';
		require_once \WPEX_INC_DIR . 'functions/aria-labels.php';

		// Primarily frontend functions.
		require_once \WPEX_INC_DIR . 'functions/frontend.php';
		require_once \WPEX_INC_DIR . 'functions/layouts.php';
		require_once \WPEX_INC_DIR . 'functions/social-share.php';
		require_once \WPEX_INC_DIR . 'functions/videos.php';
		require_once \WPEX_INC_DIR . 'functions/author.php';
		require_once \WPEX_INC_DIR . 'functions/post-media.php';
		require_once \WPEX_INC_DIR . 'functions/togglebar.php';
		require_once \WPEX_INC_DIR . 'functions/header-menu.php';
		require_once \WPEX_INC_DIR . 'functions/page-header.php';
		require_once \WPEX_INC_DIR . 'functions/grids.php';
		require_once \WPEX_INC_DIR . 'functions/page.php';
		require_once \WPEX_INC_DIR . 'functions/archives.php';
		require_once \WPEX_INC_DIR . 'functions/blog.php';
		require_once \WPEX_INC_DIR . 'functions/portfolio.php';
		require_once \WPEX_INC_DIR . 'functions/staff.php';
		require_once \WPEX_INC_DIR . 'functions/testimonials.php';
		require_once \WPEX_INC_DIR . 'functions/cpt.php';
	}

	/**
	 * Global.
	 */
	private function global(): void {
		
		// Set theme versions.
		$this->set_initial_version();
		$this->set_db_version();

		// Run after updates class.
		// update_option( 'totaltheme_version', '1.2' ); // !!! for internal testing !!!
		if ( \totaltheme_version_check( 'initial', totaltheme_get_version(), '!=' ) // fresh install check.
			&& \totaltheme_version_check( 'db', totaltheme_get_version(), '<' ) // old install check.
		) {
			\totaltheme_call_static( 'Updates\After_Update', 'run_updater' );
		}

		// Init classes.
		\totaltheme_init_class( 'Color_Scheme' );
		\totaltheme_init_class( 'Color_Palette' );
		\totaltheme_init_class( 'WP_Nav_Custom_Fields' );
		\totaltheme_init_class( 'Error_404' );
		\totaltheme_init_class( 'Widgets\Register_Widget_Areas' );
		\totaltheme_init_class( 'Register_AJAX_Callbacks' );

		// Register theme actions.
		\add_action( 'after_switch_theme', 'totaltheme_action_callback' );
		\add_action( 'admin_bar_menu', 'totaltheme_action_callback' );

		// Filters.
		\add_filter( 'http_request_args', 'totaltheme_filter_callback', 5, 2 );
		\add_filter( 'kses_allowed_protocols', 'totaltheme_filter_callback' );
		\add_filter( 'wp_get_attachment_url', 'totaltheme_filter_callback' );

		// Theme builder.
		require_once \WPEX_INC_DIR . 'theme-builder/functions.php'; // !! must load here !!
		require_once \WPEX_INC_DIR . 'theme-builder/theme-builder.php';

		/** Maybe include */

		if ( \get_theme_mod( 'header_builder_enable', true ) ) {
			require_once \WPEX_INC_DIR . 'theme-builder/header-builder.php';
		}

		if ( \get_theme_mod( 'footer_builder_enable', true ) ) {
			require_once \WPEX_INC_DIR . 'theme-builder/footer-builder.php';
		}

		if ( \get_theme_mod( 'page_animations_enable', true ) ) {
			\totaltheme_init_class( 'Page_Animations' );
		}

		if ( \get_theme_mod( 'header_image_enable', false ) ) {
			\totaltheme_init_class( 'WP_Custom_Header' );
		}

		if ( \totaltheme_call_static( 'WP_Post_Gallery', 'is_enabled' ) ) {
			\totaltheme_init_class( 'WP_Post_Gallery' );
		}

		if ( \get_theme_mod( 'under_construction_enable', true ) ) {
			\totaltheme_init_class( 'Under_Construction' );
		}

		if ( \wpex_disable_google_services() ) {
			\totaltheme_init_class( 'Disable_Google_Services' );
		}

		if ( ! \get_theme_mod( 'widget_block_editor_enable', true ) ) {
			\totaltheme_call_static( 'Widgets\Block_Editor', 'disable_block_editor' );
		}

		if ( \get_theme_mod( 'favicons_enable' ) ) {
			\totaltheme_init_class( 'Favicons' );
		}

		if ( \get_theme_mod( 'custom_admin_login_enable', true ) ) {
			\totaltheme_init_class( 'Custom_Login' );
		}

		if ( \get_theme_mod( 'custom_actions_enable', true ) ) {
			\totaltheme_init_class( 'Custom_Actions' );
		}

		\totaltheme_init_class( 'Remove_Cpt_Slugs' );
		\totaltheme_init_class( 'Updates\Plugin_Updater' );
		\totaltheme_init_class( 'Updates\Theme_Updater' );
		\totaltheme_init_class( 'Disable_WP_Emoji' );

		/* These Classes must Load last */
		\totaltheme_init_class( 'Integrations' );
		\totaltheme_init_class( 'Customizer' );
		\totaltheme_init_class( 'Image_Sizes' );
		\totaltheme_init_class( 'Typography' );
	}

	/**
	 * Admin functions.
	 */
	private function admin(): void {
		$this->health_checks();

		\totaltheme_init_class( 'Admin\Theme_Panel' );
		\totaltheme_init_class( 'Admin\Mce_Editor' );
		\totaltheme_init_class( 'Admin\Recommended_Plugins' );
		\totaltheme_init_class( 'Admin\CPT_Settings' );
		\totaltheme_init_class( 'Admin\Import_Export' );
		\totaltheme_init_class( 'Admin\License_Panel' );
		\totaltheme_init_class( 'Admin\Accessibility_Panel' );
		\totaltheme_init_class( 'Admin\Editor_Styles' );
		\totaltheme_init_class( 'Admin\Dashboard_Thumbnails' );
		\totaltheme_init_class( 'Admin\Scripts' );
	}

	/**
	 * Frontend functionality.
	 */
	private function frontend(): void {
		\totaltheme_init_class( 'Scripts\Loader' );

		if ( \get_theme_mod( 'bypostauthor_highlight' ) ) {
			\totaltheme_init_class( 'Comments\Author_Badge' );
		}

		if ( \get_theme_mod( 'thumbnail_format_icons', false ) ) {
			\totaltheme_init_class( 'Thumbnail_Format_Icons' );
		}
		
		\totaltheme_init_class( 'Redirections' );
		\totaltheme_init_class( 'Site_Backgrounds' );
		\totaltheme_init_class( 'Fonts\Global_Fonts' );
		\totaltheme_init_class( 'Advanced_Styles' );
		\totaltheme_init_class( 'Inline_CSS' );
		\totaltheme_init_class( 'Local_Scroll' );

		// Register the general totaltheme_action_callback() callback in various hooks.
		\add_action( 'pre_get_posts', 'totaltheme_action_callback' );

		// Register the general totaltheme_filter_callback() callback in various hooks.
		\add_filter( 'body_class', 'totaltheme_filter_callback' );
		\add_filter( 'post_class', 'totaltheme_filter_callback', 10, 3 );
		\add_filter( 'term_link', 'totaltheme_filter_callback', 10, 3 );
		\add_filter( 'widget_tag_cloud_args', 'totaltheme_filter_callback' );
		\add_filter( 'comment_form_fields', 'totaltheme_filter_callback' );
		\add_filter( 'redirect_canonical', 'totaltheme_filter_callback' );
		\add_filter( 'the_password_form', 'totaltheme_filter_callback' );
		\add_filter( 'widget_nav_menu_args', 'totaltheme_filter_callback', 10, 4 );
		\add_filter( 'embed_oembed_html', 'totaltheme_filter_callback', 100, 4 );
		\add_filter( 'get_previous_post_join', 'totaltheme_filter_callback' );
		\add_filter( 'get_next_post_join', 'totaltheme_filter_callback' );
		\add_filter( 'get_previous_post_where', 'totaltheme_filter_callback' );
		\add_filter( 'get_next_post_where', 'totaltheme_filter_callback' );
		\add_filter( 'dynamic_sidebar_params', 'totaltheme_filter_callback' );
		\add_filter( 'nav_menu_css_class', 'totaltheme_filter_callback', 10, 3 );

		if ( \apply_filters( 'wpex_widget_counter_span', true ) ) {
			\add_filter( 'wp_list_categories', 'totaltheme_filter_callback' );
			\add_filter( 'get_archives_link', 'totaltheme_filter_callback' );
		}

		if ( \get_theme_mod( 'remove_menu_ids', false ) && \apply_filters( 'wpex_accessibility_panel', true ) ) {
			add_filter( 'nav_menu_item_id', '__return_false' );
		}

		// Fix for WordPress messing with images and iFrames added in shortcodes.
		if ( \function_exists( '\wp_filter_content_tags' ) ) {
			\add_filter( 'the_content', 'wp_filter_content_tags', 10 );
			\remove_filter( 'the_content', 'wp_filter_content_tags', 12 );
		}
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks(): void {
		\add_action( 'after_setup_theme', 'totaltheme_action_callback' );
		\add_action( 'after_setup_theme', [ $this, 'hooks_actions' ] );
		\add_filter( 'woocommerce_create_pages', [ $this, 'disable_woocommerce_create_pages' ] );
	}

	/**
	 * Runs on the after_setup_theme hook.
	 */
	public function hooks_actions(): void {
		require_once \WPEX_INC_DIR . 'functions/hooks/hooks.php';
		require_once \WPEX_INC_DIR . 'functions/hooks/add-actions.php';
		require_once \WPEX_INC_DIR . 'functions/hooks/remove-actions.php';
		require_once \WPEX_INC_DIR . 'functions/hooks/partials.php';
	}

	/**
	 * Set initial theme version.
	 */
	private function set_initial_version(): void {
		if ( \get_option( 'totaltheme_initial_version' ) ) {
			return;
		}

		if ( $old_option = \get_option( 'total_initial_version' ) ) {
			$version = $old_option;
		} else {
			$version = \totaltheme_get_version();
		}

		$update_option = \update_option( 'totaltheme_initial_version', \sanitize_text_field( $version ), false );

		if ( $update_option && $old_option ) {
			\delete_option( 'total_initial_version' );
		}

		if ( ! \get_option( 'wpb_js_modules' ) ) {
			\update_option( 'wpb_js_modules', \wp_json_encode( [
				'vc-seo'                => false,
				'vc-ai'                 => false,
				'vc-design-options'     => false,
				'vc-post-custom-layout' => false,
				'vc-scroll-to-element'  => false,
				'vc-color-picker'       => false,
			] ), false );
		}
	}

	/**
	 * Set theme db version.
	 */
	private function set_db_version(): void {
		if ( ! \totaltheme_get_version( 'db' ) ) {
			\update_option( 'totaltheme_version', \totaltheme_get_version(), false );
		}
	}

	/**
	 * Registers health checks.
	 */
	private function health_checks(): void {
		if ( ! class_exists( 'TotalTheme\Health_Check' ) ) {
			return;
		}

		$health_checks = [
			'TotalTheme\Health_Check\Header_Menu_Dropdown_SuperFish',
			'TotalTheme\Health_Check\Jquery_Easing',
			'TotalTheme\Health_Check\Theme_Updates',
		];

		if ( \WPEX_VC_ACTIVE ) {
			$health_checks[] = 'TotalTheme\Health_Check\WPBakery_Slim_Mode';
		}

		foreach ( $health_checks as $health_check_classname ) {
			if ( \class_exists( $health_check_classname ) ) {
				(new $health_check_classname())->register_test();
			}
		}
	}

	/**
	 * Prevent Woocommerce from installing pages on installation.
	 *
	 * @return array $pages Array of pages to create when the WooCommerce plugin is installed.
	 */
	public function disable_woocommerce_create_pages( $pages ) {
		if ( \defined( '\WC_INSTALLING' ) && true === \WC_INSTALLING ) {
			return [];
		}
		return $pages;
	}

	/**
     * Prevent cloning.
     */
    protected function __clone() { }

    /**
     * Prevent wakeup.
     */
    public function __wakeup() {
		throw new \Exception( 'Cannot unserialize a singleton.' );
    }

}
