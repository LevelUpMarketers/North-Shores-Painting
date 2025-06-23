<?php

namespace TotalThemeCore\WPBakery;

use TotalTheme\Integration\WPBakery\Slim_Mode;

defined( 'ABSPATH' ) || exit;

/**
 * WPBakery helper functions.
 */
class Helpers {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Check if currently editing the page using the vc frontend editor.
	 *
	 * @note checking is_admin() doesn't work.
	 */
	public static function is_frontend_edit_mode(): bool {
		return \function_exists( 'vc_is_inline' ) && \vc_is_inline();
	}

	/**
	 * Check if currently editing a specific post type in front-end mode.
	 */
	public static function is_cpt_in_frontend_mode( string $post_type = '' ): bool {
	   return ( \function_exists( 'vc_get_param' )
			&& 'true' === \vc_get_param( 'vc_editable' )
			&& $post_type === \get_post_type( vc_get_param( 'vc_post_id' ) )
		);
	}

	/**
	 * Checks if slim mode is enabled.
	 */
	public static function is_slim_mode_enabled(): bool {
		return \is_callable( '\TotalTheme\Integration\WPBakery\Slim_Mode::is_enabled' ) && Slim_Mode::is_enabled();
	}

	/**
	 * Returns the current post type we are editing in the admin.
	 */
	public static function get_admin_post_type(): string {
		$post_type = \get_post_type();
		if ( empty( $post_type ) ) {
			if ( $post = \vc_get_param( 'post' ) ) {
				$post_type = \get_post_type( (int) $post );
			} else {
				$post_type = \vc_get_param( 'post_type' ) ?: '';
			}
		}
		return (string) $post_type;
	}

	/**
	 * Enables the WPBakery editor.
	 */
	public static function enable_editor( string $post_type ): void {
		if ( $post_type === self::get_admin_post_type() ) {
			\add_action( 'admin_init', [ self::class, '__enable_editor_filters' ], 1 ); // must use priority of 1
		}
	}

	/**
	 * Applys filters to enable the WPBakery editor.
	 */
	public static function __enable_editor_filters(): void {
		\add_filter( 'vc_role_access_with_post_types_get_state', '__return_true' );
		\add_filter( 'vc_role_access_with_backend_editor_get_state', '__return_true' );
		\add_filter( 'vc_role_access_with_frontend_editor_get_state', '__return_true' );
		\add_filter( 'vc_check_post_type_validation', '__return_true' );
		\add_filter( 'vc_is_valid_post_type_be', '__return_true' );
		\add_filter( 'vc_is_valid_post_type_fe', '__return_true' );
	}

}
