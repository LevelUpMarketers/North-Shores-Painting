<?php

namespace TotalTheme\Integration\WPBakery;

\defined( 'ABSPATH' ) || exit;

final class Disable_Updates {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Init.
	 */
	public static function init() {
		\add_action( 'admin_menu', [ self::class, 'remove_admin_license_tab' ], 999 );
		\add_filter( 'auto_update_plugin', [ self::class, 'disable_auto_updates' ], 10, 2 );
		\add_action( 'init', [ self::class, 'disable_vc_updater' ] );

	// This actually breaks the TGMPA script and prevents all updates from working.
	//	\add_filter( 'site_transient_update_plugins', [ self::class, 'remove_update_transient' ] );
	}

	/**
	 * Remove plugin license admin tab.
	 */
	public static function remove_admin_license_tab(){
		if ( \defined( '\VC_PAGE_MAIN_SLUG' ) ) {
			\remove_submenu_page( \VC_PAGE_MAIN_SLUG, 'vc-updater' );
		}
	}

	/**
	 * Disable WP auto updates.
	 */
	public static function disable_auto_updates( $update, $item ) {
		if ( ! empty( $item->slug ) && 'js_composer' === $item->slug ) {
			return false;
		}
		return $update;
	}

	/**
	 * Disable update transients.
	 *
	 * @deprecated 5.8.0
	 * @todo try re-adding.
	 */
	public static function remove_update_transient( $value ) {
		if ( isset( $value ) && is_object( $value ) && isset( $value->response['js_composer/js_composer.php'] ) ) {
			unset( $value->response['js_composer/js_composer.php'] );
		}
		return $value;
	}

	/**
	 * Disable VC updater.
	 */
	public static function disable_vc_updater() {
		if ( ! \function_exists( 'vc_updater' ) ) {
			return;
		}

		\remove_filter( 'upgrader_pre_download', [ \vc_updater(), 'preUpgradeFilter' ], 10);
		\remove_filter( 'pre_set_site_transient_update_plugins', [
			\vc_updater()->updateManager(),
			'check_update'
		] );

		if ( function_exists( 'vc_plugin_name' ) ) {
			\remove_action(
				'in_plugin_update_message-' . \vc_plugin_name(),
				[ \vc_updater(), 'addUpgradeMessageLink' ]
			);
		}
	}

}
