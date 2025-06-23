<?php

namespace TotalTheme\Updates;

\defined( 'ABSPATH' ) || exit;

/**
 * Displays plugin update notifications for some bundled theme plugins to make it easier for the end user.
 */
final class Plugin_Updater {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Plugin_Updater.
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {
		if ( ! $this->is_enabled() ) {
			return;
		}

		// For testing purposes only !!!
		//set_site_transient( 'update_plugins', [] );

		\add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_for_updates' ] );
	}

	/**
	 * Checks if auto updates are enabled.
	 */
	public function is_enabled() {
		$check = \apply_filters( 'wpex_has_bundled_plugin_update_notices', true ); // @deprecated
		return (bool) \apply_filters( 'totaltheme/updates/plugin_updater/is_enabled', (bool) $check );
	}

	/**
	 * Returns list of plugins to check
	 */
	public function get_plugins_to_check() {
		$recommended_plugins = \totaltheme_call_static( 'Admin\Recommended_Plugins', 'get_list' );

		if ( empty( $recommended_plugins ) ) {
			return;
		}

		$plugins_to_check = [
			'total-theme-core',
		];

		foreach ( $plugins_to_check as $k => $v ) {
			if ( \array_key_exists( $v, $recommended_plugins ) ) {
				$plugin = $recommended_plugins[ $v ];
				if ( empty( $plugin['package' ] ) && isset( $plugin['source' ] ) ) {
					$plugin['package' ] = $plugin['source' ];
					unset( $plugin['source' ] );
				}
				$plugin['id' ]     = $this->get_plugin_base( $plugin );
				$plugin['plugin' ] = $plugin['id'];
			} else {
				unset( $plugins_to_check[ $k ] );
			}
			$plugins_to_check[ $k ] = $plugin;
		}

		return $plugins_to_check;
	}

	/**
	 * Check transients
	 */
	public function check_for_updates( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		// Get plugins to check
		$plugins_to_check = $this->get_plugins_to_check();

		if ( empty( $plugins_to_check ) ) {
			return $transient;
		}

		// Return array of installed plugins
		$installed_plugins = $this->get_installed_plugins();

		// No plugins installed
		if ( empty( $installed_plugins ) || ! \is_array( $installed_plugins ) ) {
			return $transient;
		}

		// Loop through plugins and check if an update is available
		foreach ( $plugins_to_check as $plugin ) {

			if ( $this->is_plugin_installed( $plugin, $installed_plugins ) ) {

				$has_update = $this->has_update( $plugin, $installed_plugins );

				if ( $has_update ) {

					$response = (object) [
						'id'            => $plugin['id'],
						'slug'          => $plugin['slug'],
						'plugin'        => $plugin['plugin'],
						'new_version'   => $plugin['version'],
						'package'       => $plugin['package'],
						'url'           => '',
						'icons'         => [],
						'banners'       => [],
						'banners_rtl'   => [],
						'tested'        => '',
						'requires_php'  => '',
						'compatibility' => '',

					];

					$transient->response[ $plugin['id' ] ] = $response;

				} elseif ( isset( $transient->no_update ) ) {

					$item = (object) [
						'id'            => $plugin['id'],
						'slug'          => $plugin['slug'],
						'plugin'        => $plugin['plugin'],
						'new_version'   => $plugin['version'],
						'package'       => '',
						'url'           => '',
						'icons'         => [],
						'banners'       => [],
						'banners_rtl'   => [],
						'tested'        => '',
						'requires_php'  => '',
						'compatibility' => '',
					];

					$transient->no_update[ $plugin['id' ] ] = $item;

				}

			}

		}

		return $transient;
	}

	/**
	 * Get list of installed plugins.
	 */
	public function get_installed_plugins( $plugin_folder = '' ) {
		if ( ! \function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return \get_plugins( $plugin_folder );
	}

	/**
	 * Check if a specific plugin is installed.
	 */
	public function is_plugin_installed( $plugin, $installed_plugins ) {
		return \array_key_exists( $this->get_plugin_base( $plugin ), $installed_plugins );
	}

	/**
	 * Check if a plugin has an update available
	 */
	private function has_update( $plugin, $installed_plugins ) {
		$base = $this->get_plugin_base( $plugin );
		if ( ! empty( $installed_plugins[$base]['Version'] ) ) {
			return \version_compare( $plugin['version'], $installed_plugins[$base]['Version'], '>' );
		}
	}

	/**
	 * Returns plugin base from slug
	 */
	private function get_plugin_base( $plugin ) {
		return "{$plugin['slug']}/{$plugin['slug']}.php";
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 */
	public function __wakeup() {
		\trigger_error( 'Cannot unserialize a Singleton.', \E_USER_WARNING);
	}

}
