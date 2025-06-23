<?php

namespace TotalTheme\Demo_Importer;

use Automatic_Upgrader_Skin;
use Plugin_Upgrader;

\defined( 'ABSPATH' ) || exit;

class Plugin_Installer {

	/**
	 * Contains the data for all the possible plugins that might be used by
	 * the theme. The data consists of the plugin's name, slug, the source of the
	 * installable files and the file path of the plugin inside the 'plugins' directory.
	 */
	private $plugins_data = [];

	/**
	 * Contains data for all the plugins that are installed, whether they are activated or not.
	 */
	private $installed_plugins = [];

	/**
	 * Start things up
	 */
	public function __construct() {
		if ( ! \function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php'; // this file contains the necessary methods for checking if a plugin is activated or installed.
		}
	}

	/**
	 * Set the data for the required plugins.
	 */
	public function set_plugins_data( $data ) {
		$this->plugins_data = $data;
		foreach ( $this->plugins_data as $plugin_slug => $plugin_data ) {
			$this->set_plugin_file_path( $plugin_slug );
		}
	}

	/**
	 * Return the complete data for the plugins required by the theme.
	 */
	public function get_plugins_data() {
		return $this->plugins_data;
	}

	/**
	 * Set the correct plugin file paths.
	 */
	public function set_plugin_file_path( $slug ): bool {
		foreach ( $this->get_installed_plugins( true ) as $file_path => $plugin ) {
			if ( $slug === \basename( $file_path, '.php' ) ) {
				$this->plugins_data[ $slug ]['file_path'] = $file_path;
				return true;
			}
		}
		return false;
	}

	/**
	 * Get the file path of the plugin.
	 */
	public function get_plugin_file_path( $slug ) {
		$plugins_data = $this->get_plugins_data();
		if ( isset( $plugins_data[ $slug ][ 'file_path' ] ) !== false ) {
			return $plugins_data[ $slug ][ 'file_path' ];
		}
		return false;
	}

	/**
	 * Return the list of installed plugins.
	 */
	protected function get_installed_plugins( $refresh = false ): array {
		if ( empty( $this->installed_plugins ) || $refresh === true ) {
			$this->installed_plugins = \get_plugins();
		}
		return (array) $this->installed_plugins;
	}

	/**
	 * Check if the given plugin is installed.
	 */
	public function is_plugin_installed( $slug ): bool {
		return isset( $this->get_plugins_data()[ $slug ]['file_path'] );
	}

	/**
	 * Check if the given plugin is activated.
	 */
	public function is_plugin_activated( $slug ): bool {
		$plugins_data = $this->get_plugins_data();
		return isset( $plugins_data[ $slug ]['file_path'] ) && \is_plugin_active( $plugins_data[ $slug ]['file_path'] );
	}

	/**
	 * Attempt to activate a plugin.
	 */
	public function activate_plugin( $file_path ): bool {
		$result = \activate_plugin( $file_path, '', false, true );
		return null === $result ? true : false;
	}

	/**
	 * Attempt to install a plugin.
	 */
	public function install_plugin( $slug ): bool {
		if ( ! \current_user_can( 'install_plugins' ) ) {
			return false; // extra security check.
		}

		$source = $this->get_download_link( $slug );

		if ( ! $source ) {
			return false;
		}

		if ( ! \class_exists( 'Plugin_Upgrader', false ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		if ( ! \class_exists( 'Automatic_Upgrader_Skin', false ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skins.php';
		}

		$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$result   = $upgrader->install( $source );

		if ( true === $result ) {
			// Since it's a newly installed plugin, its file path is not yet set in the plugins data,
			// so we need to set it.
			$this->set_plugin_file_path( $slug );
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Return the download link for the plugin.
	 */
	public function get_download_link( $slug ) {
		$plugins_data = $this->get_plugins_data();

		// If a source was specified in the plugin data, return that.
		if ( isset( $plugins_data[ $slug ][ 'source' ] ) ) {
			return $plugins_data[ $slug ][ 'source' ];
		}

		// Else, it means that the plugin is hosted on the WP repo and we need to fetch the download link from there
		if ( ! \function_exists( 'plugins_api' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		}

		// Contact form 7 fix since folder name doesn't match plugin name.
		if ( 'wp-contact-form-7' === $slug ) {
			$slug = 'contact-form-7';
		}

		$result = \plugins_api( 'plugin_information', [ 'slug' => $slug ] );

		if ( ! \is_wp_error( $result ) ) {
			return $result->download_link;
		}
	}

}
