<?php

namespace TotalTheme\Staff;

\defined( 'ABSPATH' ) || exit;

/**
 * Staff Post Type Class.
 */
class Post_Type {

	/**
	 * Checks if the post type is enabled.
	 */
	public static function is_enabled(): bool {
		return \class_exists( 'TotalThemeCore\Cpt\Staff', false );
	}

	/**
	 * Returns the post type name.
	 */
	public static function get_name(): string {
		return get_post_type_object( 'staff' )->labels->name ?? \esc_html__( 'Staff', 'total' );
	}

	/**
	 * Returns the post type menu icon.
	 */
	public static function get_menu_icon(): string {
		return get_post_type_object( 'staff' )->menu_icon ?? 'dashicons-businessman';
	}

}
