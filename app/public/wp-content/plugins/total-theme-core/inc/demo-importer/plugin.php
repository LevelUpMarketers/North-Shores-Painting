<?php

namespace TotalTheme\Demo_Importer;

\defined( 'ABSPATH' ) || exit;

/**
 * Custom Demo Importer exclusive for the Total theme.
 */
class Plugin {

	/**
	 * Contains the data for the demos.
	 */
	private $demos;

	/**
	 * Transient name.
	 */
	private const TRANSIENT_NAME = 'totaltheme_demo_importer_list';

	/**
	 * Previously imported demo data option name.
	 */
	private const IMPORTED_DATA_OPTION_NAME = '';

	/**
	 * Contains the categories of demos.
	 */
	private $categories;

	/**
	 * Contains the plugins required by the demos.
	 */
	private $plugins;

	/**
	 * Instance of the Plugin_Installer class which is used to
	 * activate and install plugins automatically.
	 */
	private $plugin_installer;

	/**
	 * Instance of the Content_Importer class, which is used to import
	 * the XML content, theme customizations, widgets, sliders and other
	 * available data.
	 */
	private $content_importer;

	/**
	 * Start things up.
	 */
	public function __construct() {
		if ( ! \defined( '\TOTAL_THEME_ACTIVE' ) || ! \is_admin() || \is_customize_preview() || ! \current_user_can( 'import' ) ) {
			return;
		}

		// Define plugin constants
		\define( 'TOTALTHEME_DEMO_IMPORTER_DIR_PATH', TTC_PLUGIN_DIR_PATH . 'inc/demo-importer/' );
		\define( 'TOTALTHEME_DEMO_IMPORTER_DIR_URL', TTC_PLUGIN_DIR_URL . 'inc/demo-importer/' );

		// Include Helpers
		require_once TOTALTHEME_DEMO_IMPORTER_DIR_PATH . 'includes/helpers.php';
		
		if ( ! \function_exists( 'download_url' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// Start things up
		\add_action( 'admin_menu', [ $this, 'on_admin_menu' ], 2 );
		\add_action( 'admin_init', [ $this, 'on_admin_init' ], 20 );
	}

	/**
	 * Runs on the admin_init hook.
	 */
	public function on_admin_init() {
		// Enqueue the admin style and javascript.
		\add_action( 'admin_enqueue_scripts', [ $this, 'on_admin_enqueue_scripts' ] );

		// Register the AJAX methods.
		\add_action( 'wp_ajax_totaltheme_demo_importer_get_selected', [ $this, 'ajax_get_selected_demo_data' ] );
		\add_action( 'wp_ajax_totaltheme_demo_importer_step', [ $this, 'ajax_import_step' ] );
		\add_action( 'wp_ajax_totaltheme_demo_importer_install_plugin', [ $this, 'ajax_install_plugin' ] );
		\add_action( 'wp_ajax_totaltheme_demo_importer_activate_plugin', [ $this, 'ajax_activate_plugin' ] );
		\add_action( 'wp_ajax_totaltheme_demo_importer_refresh_list', [ $this, 'ajax_refresh_list' ] );
		\add_action( 'wp_ajax_totaltheme_demo_importer_delete_imported_data', [ $this, 'ajax_delete_imported_data' ] );
	}

	/**
	 * Add sub menu page.
	 */
	public function on_admin_menu() {
		if ( \defined( '\WPEX_THEME_PANEL_SLUG' ) ) {
			\add_submenu_page(
				WPEX_THEME_PANEL_SLUG,
				\esc_html__( 'Demo Importer', 'total-theme-core' ),
				\esc_html__( 'Demo Importer', 'total-theme-core' ),
				'import',
				\WPEX_THEME_PANEL_SLUG . '-demo-importer',
				[ $this, 'render_admin_page' ]
			);
		}
	}

	/**
	 * Renders the demo importer page.
	 */
	public function render_admin_page() {
		if ( ! \current_user_can( 'administrator' ) ) {
			return;
		}

		$this->init_demos_data();

		if ( ! empty( $this->demos ) ) {
			require_once TOTALTHEME_DEMO_IMPORTER_DIR_PATH . 'views/demos.php';
		} else {
			\delete_transient( self::TRANSIENT_NAME );
			require_once TOTALTHEME_DEMO_IMPORTER_DIR_PATH . 'views/no-demos.php';
		}
	}

	/**
	 * Hooks into admin_enqueue_scripts
	 */
	public function on_admin_enqueue_scripts( $hook ): void {
		if ( Helpers::is_admin_page( $hook ) ) {
			$this->enqueue_admin_styles();
			$this->enqueue_admin_scripts();
		}
	}

	/**
	 * Enqueue admin stylesheets.
	 */
	private function enqueue_admin_styles(): void {
		\wp_enqueue_style(
			'totaltheme-demo-importer',
			\totalthemecore_get_css_file( 'admin/demo-importer' ),
			[ 'dashicons' ],
			TTC_VERSION
		);
	}

	/**
	 * Enqueue admin JavaScript files.
	 */
	private function enqueue_admin_scripts(): void {
		\wp_enqueue_script(
			'totaltheme-demo-importer',
			\totalthemecore_get_js_file( 'admin/demo-importer' ),
			[ 'jquery' ],
			TTC_VERSION,
			[
				'strategy' => 'defer',
				'in_footer' => true,
			]
		);

		\wp_localize_script(
			'totaltheme-demo-importer',
			'totaltheme_demo_importer_params',
			[
				'ajaxurl' => \esc_url( \admin_url( 'admin-ajax.php' ) ),
				'nonce'   => \wp_create_nonce( 'totaltheme_demo_importer_get_selected' ),
				'strings' => [
					'installing_plugin'        => \esc_html__( 'Installing plugin.', 'total-theme-core' ),
					'activating_plugin'        => \esc_html__( 'Activating plugin.', 'total-theme-core' ),
					'plugin_activated'         => \esc_html__( 'Plugin activated.', 'total-theme-core' ),
					'importing_mods'           => \esc_html__( 'Importing Customizer Settings.', 'total-theme-core' ),
					'importing_widgets'        => \esc_html__( 'Importing Widgets.', 'total-theme-core' ),
					'importing_sliders'        => \esc_html__( 'Importing Sliders.', 'total-theme-core' ),
					'importing_xml'            => \esc_html__( 'Importing XML Data (Posts, Pages, Terms, Images, etc).', 'total-theme-core' ),
					'finishing_import'         => \esc_html__( 'Finishing things up.', 'total-theme-core' ),
					'plugin_failed_activation' => \esc_html__( 'Plugin failed to activate.', 'total-theme-core' ),
					'content_importing_error'  => \esc_html__( 'There was a problem during the importing process resulting in the following error code from your server:', 'total-theme-core' ),
				]
			]
		);
	}

	/**
	 * Initializes the demos data.
	 */
	protected function init_demos_data(): void {
		// Get demo data.
		$demos_data = $this->get_demos_data();

		// List of all the currently available demos.
		$this->demos = $demos_data[ 'demos' ];

		// List of all the currently available categories.
		$this->categories = $demos_data[ 'categories' ];

		// List of all the currently available plugins.
		$this->plugins = $demos_data[ 'plugins' ];

		// Add the slug and source for each plugin.
		foreach ( $this->plugins as $key => $value ) {

			// Set plugin slug.
			$this->plugins[ $key ]['slug'] = $key;

			// Fix contact form 7 issue where the plugin name isn't the same as the folder name.
			if ( 'contact-form-7' == $key ) {
				$this->plugins[ $key ][ 'slug' ] = 'wp-contact-form-7';
				$this->plugins[ 'wp-contact-form-7' ] = $this->plugins[ 'contact-form-7' ];
				unset( $this->plugins[ 'contact-form-7' ] );
			}

			// Get plugin URL from the theme's Recommended_Plugins class.
			if ( $value['location'] === 'bundled' && function_exists( '\totaltheme_call_static' ) ) {
				$plugins = \totaltheme_call_static( 'Admin\Recommended_Plugins', 'get_list' );
				if ( ! empty( $plugins[ $key ][ 'source' ] ) ) {
					$this->plugins[ $key ]['source'] = esc_url( $plugins[ $key ][ 'source' ] );
				}
			}
		}

	}

	/**
	 * Gets the list of demos, demo categories and required plugins from the json file.
	 */
	protected function get_demos_data(): array {
		$defaults = [
			'demos'      => [],
			'categories' => [],
			'plugins'    => [],
		];

		$demos = get_transient( self::TRANSIENT_NAME );

		if ( ! empty( $demos ) && \is_array( $demos ) ) {
			return [
				'demos'      => $demos['demos'] ?? '',
				'categories' => $demos['categories'] ?? '',
				'plugins'    => $demos['plugins'] ?? '',
			];
		}

		// Get list of demos.
		$response = \wp_safe_remote_get( 'https://totalwpthemedemo.com/wp-json/twtd/v1/list', [
			'redirection' => 0,
		] );

		$is_wp_error = \is_wp_error( $response );

		if ( $is_wp_error || ( \wp_remote_retrieve_response_code( $response ) !== 200 ) ) {
			if ( $is_wp_error ) {
				$this->get_demos_list_error = $response->get_error_message();
			}
			return $defaults;
		} else {

			$body = \wp_remote_retrieve_body( $response );

			if ( empty( $body ) ) {
				return $defaults;
			}

			// Extract json data
			$data = json_decode( $body, true );

			if ( JSON_ERROR_NONE === json_last_error() ) {
				$demos = (array) $data;
				\set_transient( self::TRANSIENT_NAME, $demos, DAY_IN_SECONDS );
				return $demos;
			} else {
				return $defaults;
			}
		}
	}

	/**
	 * Gets the popup content associated with the selected demo.
	 */
	public function ajax_get_selected_demo_data(): void {
		if ( ! \array_key_exists( 'nonce', $_GET )
			|| ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_GET['nonce'] ) ), 'totaltheme_demo_importer_get_selected' )
		) {
			\wp_die( 'This action was stopped for security purposes.' );
		}

		if ( empty( $_GET['demo'] ) ) {
			\wp_die( 'No demo selected.' );
		}

		$this->init_demos_data();

		$this->plugin_installer = Helpers::new_plugin_installer();

		$this->plugin_installer->set_plugins_data( $this->plugins );

		$demo = \sanitize_text_field( \wp_unslash( $_GET['demo'] ) ); // @note this var needs to pass to selected.php

		$demo_data = $this->demos[ $demo ];

		require TOTALTHEME_DEMO_IMPORTER_DIR_PATH . 'views/selected.php';

		wp_die();
	}

	/**
	 * Activates a plugin.
	 */
	public function ajax_activate_plugin() {
		if ( ! isset( $_POST['plugin_slug'] ) || ! \current_user_can( 'activate_plugins' ) ) {
			\wp_die();
		}
	
		$plugin = \sanitize_text_field( $_POST['plugin_slug'] );
		
		if ( ! isset( $_POST['nonce'] )
			|| ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['nonce'] ) ), "totaltheme_demo_importer_activate_plugin_{$plugin}" ) ) {
			\wp_die( 'This action was stopped for security purposes.' );
		}

		$this->init_demos_data();

		$this->plugin_installer = Helpers::new_plugin_installer();
		$this->plugin_installer->set_plugins_data( $this->plugins );

		// Check if the plugin is not already activated
		if ( $this->plugin_installer->is_plugin_activated( $plugin ) ) {
			echo '1';
		} else {
			echo \strval( (int) $this->plugin_installer->activate_plugin( $plugin ) );
		}

		\wp_die();
	}

	/**
	 * Installs a plugin and then activates it.
	 */
	public function ajax_install_plugin() {
		if ( ! isset( $_POST['plugin_slug'] ) || ! \current_user_can( 'install_plugins' ) ) {
			\wp_die();
		}

		$plugin = \sanitize_text_field( $_POST['plugin_slug'] );
		$plugin_installed = false;

		if ( ! isset( $_POST['nonce'] )
			|| ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['nonce'] ) ), "totaltheme_demo_importer_install_plugin_{$plugin}" )
		) {
			\wp_die( 'This action was stopped for security purposes.' );
		}

		$this->init_demos_data();

		$this->plugin_installer = Helpers::new_plugin_installer();
		$this->plugin_installer->set_plugins_data( $this->plugins );
		$plugin_installed = $this->plugin_installer->is_plugin_installed( $plugin );

		// If not installed then try and install it.
		if ( ! $plugin_installed ) {
			$plugin_installed = $this->plugin_installer->install_plugin( $plugin );
		}

		// If the plugins is installed.
		if ( $plugin_installed ) {
			if ( $this->plugin_installer->is_plugin_activated( $plugin ) ) {
				echo '1';
			} else {
				$plugin_file_path = $this->plugin_installer->get_plugin_file_path( $plugin );
				if ( $this->plugin_installer->activate_plugin( $plugin_file_path ) ) {
					echo '1';
				}
			}
		}

		\wp_die();
	}

	/**
	 * Deletes transient to refresh demos list.
	 */
	public function ajax_refresh_list() {
		\check_ajax_referer( 'totaltheme_demo_importer_refresh_list', 'nonce' );
		\delete_transient( self::TRANSIENT_NAME );
		echo 1;
		exit;
	}

	/**
	 * Import step ajax callback.
	 */
	public function ajax_import_step(): void {
		if ( ! $this->verify_demo_import_nonce() ) {
			wp_die( 'Total Theme Demo Importer step stopped for security reasons' );
		}

		@set_time_limit(0);

		$method = "ajax_import_{$_POST['step']}";

		if ( method_exists( $this, $method ) ) {
			$result = $this->$method();
			echo \is_wp_error( $result ) ? \json_encode( $result->errors ) : 'success';
		}

		wp_die();
	}

	/**
	 * Import XML Data and, optionally, the attachments.
	 */
	private function ajax_import_xml() {
		$this->init_demos_data();
		$this->content_importer = Helpers::new_content_importer( $_POST['demo'], $this->demos[ $_POST['demo'] ] );
		$import_images = isset( $_POST[ 'importXML' ] ) && 'true' === $_POST['importXML'];
		return $this->content_importer->process_xml( $import_images );
	}

	/**
	 * Import customizer settings.
	 */
	private function ajax_import_mods() {
		$this->init_demos_data();
		$this->content_importer = Helpers::new_content_importer( $_POST['demo'], $this->demos[ $_POST['demo'] ] );
		return $this->content_importer->process_theme_mods();
	}

	/**
	 * Import widgets.
	 */
	private function ajax_import_widgets() {
		$this->init_demos_data();
		$this->content_importer = Helpers::new_content_importer( $_POST['demo'], $this->demos[ $_POST['demo'] ] );
		return $this->content_importer->process_widget_import();
	}

	/**
	 * Import sliders.
	 */
	private function ajax_import_sliders() {
		$this->init_demos_data();
		$this->content_importer = Helpers::new_content_importer( $_POST['demo'], $this->demos[ $_POST['demo'] ] );
		return $this->content_importer->process_sliders_import();
	}

	/**
	 * Called when all the selected content has been imported.
	 */
	private function ajax_import_complete() {
		if ( isset( $_POST['run_extras'] ) && 'true' === $_POST['run_extras'] ) {
			$this->init_demos_data();
			$this->content_importer = Helpers::new_content_importer( $_POST['demo'], $this->demos[ $_POST['demo'] ] );
			$this->content_importer->set_menus();
			$this->content_importer->set_homepage();
			$this->content_importer->set_posts_page();
			$this->content_importer->set_shop_page();
			$this->content_importer->import_elementor_settings();
			\do_action( 'wpex_demo_importer_ajax_import_complete' );
		}
	}
	
	/**
	 * Called when the remove imported data button is clicked.
	 */
	public function ajax_delete_imported_data() {
		if ( ! \array_key_exists( 'nonce', $_GET )
			|| ! \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_GET['nonce'] ) ), 'totaltheme_demo_importer_delete_imported_data' )
		) {
			\wp_die( 'This action was stopped for security purposes.' );
		}

		require_once TOTALTHEME_DEMO_IMPORTER_DIR_PATH . 'includes/delete-imported-data.php';
		
		$delete_data = new Delete_Imported_Data();
		$delete_data->run();

		if ( $delete_data->failed_items ) {
			echo \json_encode( [
				'result' => 'error',
				'items'  => $delete_data->failed_items,
			] );
		} else {
			echo \json_encode( [
				'result' => 'success',
				'items'  => $delete_data->deleted_items
			] );
		}

		\wp_die();
	}

	/**
	 * Very demo import nonce.
	 */
	private function verify_demo_import_nonce(): bool {
		if ( empty( $_POST['nonce'] ) || empty( $_POST['demo'] ) || empty( $_POST['step'] ) ) {
			return false;
		}
		return (bool) \wp_verify_nonce( \sanitize_text_field( \wp_unslash( $_POST['nonce'] ) ), 'totaltheme_demo_importer_import_' . $_POST['demo'] );
	}

}

new Plugin();
