<?php

namespace TotalTheme\Updates;

\defined( 'ABSPATH' ) || exit;

/**
 * Provides updates for the Total theme.
 */
final class Theme_Updater {

	/**
	 * Total theme updater API url.
	 */
	private const API_URL = 'https://wpexplorer-updates.com/api/v1/';

	/**
	 * Active theme license.
	 */
	private $theme_license = null;

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Theme_Updater.
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

		// This is for testing only !!!!
		//delete_site_transient( 'update_themes' );

		if ( $this->get_theme_license() ) {
			\add_filter( 'pre_set_site_transient_update_themes', [ $this, 'check_for_update' ] );
		}
	}

	/**
	 * Returns the active theme license.
	 */
	private function get_theme_license() {
		if ( null === $this->theme_license ) {
			$this->theme_license = \totaltheme_get_license();
		}
		return $this->theme_license;
	}

	/**
	 * Checks if auto updates are enabled.
	 */
	public function is_enabled() {
		return (bool) \apply_filters( 'totaltheme/updates/theme_updater/is_enabled', true );
	}

	/**
	 * Makes a call to the API.
	 */
	private function call_api( $action, $params ) {
		$api = \add_query_arg( $params, self::API_URL . $action );
		$request = \wp_safe_remote_get( $api );
		if ( \is_wp_error( $request ) ) {
			return false;
		}
		$body = \wp_remote_retrieve_body( $request );
		return \json_decode( $body );
	}

	/**
	 * Checks the API response to see if there was an error.
	 */
	private function is_api_error( $response ): bool {
		return $response === false || ! \is_object( $response ) || isset( $response->error );
	}

	/**
	 * Calls the License Manager API to get the license information for the
	 * current product.
	 */
	private function get_license_info() {
		return $this->call_api( 'info', [
			'theme'   => 'Total',
			'license' => \urlencode( \sanitize_text_field( $this->get_theme_license() ) ),
			'version' => \wp_get_theme( 'Total' )->get( 'Version' ),
		] );
	}

	/**
	 * Check for updates.
	 */
	private function update_request() {
		$license_info = $this->get_license_info();
		if ( $this->is_api_error( $license_info ) ) {
			return false;
		}
		return $license_info;
	}

	/**
	 * The filter that checks if there are updates to the theme.
	 */
	public function check_for_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$update = $this->update_request();

		if ( $this->is_api_error( $update ) ) {
			return $transient;
		}

		// Get the current theme version.
		$current_version = \wp_get_theme( 'Total' )->get( 'Version' );

		// Update is available.
		if ( ! empty( $update->version )
			&& ! empty( $update->package )
			&& \version_compare( $current_version, $update->version, '<' )
		) {
			$transient->response['Total'] = [
				'theme'        => 'Total',
				'new_version'  => $update->version,
				'package'      => $update->package,
				'requires'     => $update->requires ?? '',
				'requires_php' => $update->requires_php ?? '',
				'url'          => $update->changelog ?? \WPEX_THEME_CHANGELOG_URL,
			];
		}
		// No update is available.
		elseif ( isset( $transient->no_update ) ) {
			// Adding the "mock" item to the `no_update` property is required
			// for the enable/disable auto-updates links to correctly appear in UI.
			$transient->no_update['Total'] = [
				'theme'        => 'Total',
				'new_version'  => $current_version,
				'package'      => '',
				'requires'     => '',
				'requires_php' => '',
				'url'          => '',
			];
		}

		return $transient;
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
