<?php

namespace TotalTheme\Updates;

\defined( 'ABSPATH' ) || exit;

/**
 * Perform actions after the theme updates.
 */
final class After_Update {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Runs the updater.
	 */
	public static function run_updater(): void {
		if ( \totaltheme_version_check( 'db', \totaltheme_get_version(), '==' ) ) {
			return; // extra check.
		}

		require_once \WPEX_INC_DIR . 'updates/functions.php';

		$user_v = totaltheme_get_version( 'db' );

		foreach ( self::get_update_callbacks() as $version => $callback ) {
			if ( \version_compare( $version, $user_v, '>' ) && \is_callable( $callback ) ) {
				\call_user_func( $callback );
			}
		}

		self::clear_caches();
		self::update_theme_version();
	}

	/**
	 * Clear caches after each update.
	 */
	private static function clear_caches(): void {
		// Re-enable recommended plugins notice for updating plugins.
		\set_theme_mod( 'recommend_plugins_enable', true );
		\delete_metadata( 'user', null, 'tgmpa_dismissed_notice_totaltheme', null, true );

		// Clear ticons list.
		\totaltheme_call_static( 'Theme_Icons', 'delete_transient' );

		// Reset plugin updates transient.
		// set_site_transient( 'update_plugins', [] ); // removed in 5.3.1 - causes errors with plugins.
	}

	/**
	 * Returns array of update callbacks.
	 */
	private static function get_update_callbacks(): array {
		return [
			'3.0.0' => 'totaltheme_update_300',
			'3.3.0' => 'totaltheme_update_330',
			'3.3.2' => 'totaltheme_update_332',
			'3.3.3' => 'totaltheme_update_333',
			'3.4.0' => 'totaltheme_update_340',
			'3.5.0' => 'totaltheme_update_350',
			'4.0'   => 'totaltheme_update_40',
			'4.3'   => 'totaltheme_update_43',
			'4.4.1' => 'totaltheme_update_441',
			'4.5.2' => 'totaltheme_update_452',
			'5.0'   => 'totaltheme_update_50',
			'5.4'   => 'totaltheme_update_54',
			'5.7.2' => 'totaltheme_update_572',
			'5.10'  => 'totaltheme_update_510',
			'5.15'  => 'totaltheme_update_515',
			'5.16'  => 'totaltheme_update_516',
			'5.99'  => 'totaltheme_update_599',
			'6.1'   => 'totaltheme_update_61',
			'6.3'   => 'totaltheme_update_63',
		];
	}

	/**
	 * Update the theme version.
	 */
	private static function update_theme_version(): void {
		\update_option( 'totaltheme_version', \totaltheme_get_version(), false );
	}

}
