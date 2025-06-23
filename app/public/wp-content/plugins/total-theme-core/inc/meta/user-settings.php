<?php

namespace TotalThemeCore\Meta;

defined( 'ABSPATH' ) || exit;

/**
 * Register social options for users.
 */
class User_Settings {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Hook into actions and filters.
	 */
	public static function init() {
		add_filter( 'user_contactmethods', [ self::class, 'filter_methods' ] );
		add_action( 'personal_options_update',  [ self::class, 'on_user_update' ] );
		add_action( 'edit_user_profile_update', [ self::class, 'on_user_update' ] );
	}

	/**
	 * Filter methods.
	 */
	public static function filter_methods( $contactmethods ) {
		if ( function_exists( 'wpex_get_user_social_profile_settings_array' ) ) {
			$settings = wpex_get_user_social_profile_settings_array();
			if ( ! empty( $settings ) && is_array( $settings ) ) {
				if ( function_exists( 'wpex_get_theme_branding' ) ) {
					$branding = wpex_get_theme_branding();
					$branding = $branding ? "{$branding} - " : '';
				} else {
					$branding = '';
				}
				foreach ( $settings as $id => $settings ) {
					$label = $settings['name'] ?? $settings['label'] ?? $settings;
					$contactmethods[ "wpex_{$id}" ] = esc_html( $branding . $label );
				}
			}
		}
		return $contactmethods;
	}

	/**
	 * Runs on the user update edit page.
	 */
	public static function on_user_update( $user_id ): void {
		if ( get_user_meta( $user_id, 'wpex_twitter', true ) ) {
			add_action( 'wp_update_user',  [ self::class, 'migrate_user_twitter_to_x_twitter' ] );
		}
	}

	/**
	 * Migrate twitter to x-twitter.
	 */
	public static function migrate_user_twitter_to_x_twitter( $user_id ): void {
		if ( $twitter_url = get_user_meta( $user_id, 'wpex_twitter', true ) ) {
			if ( ! get_user_meta( $user_id, 'wpex_x-twitter', true ) ) {
				if ( current_user_can( 'edit_user', $user_id ) ) {
					$updated_meta = update_user_meta( $user_id, 'wpex_x-twitter', sanitize_text_field( $twitter_url ), '' );
					if ( $updated_meta ) {
						delete_user_meta( $user_id, 'wpex_twitter', $twitter_url );
					}
				}
			}
		}
	}

}
