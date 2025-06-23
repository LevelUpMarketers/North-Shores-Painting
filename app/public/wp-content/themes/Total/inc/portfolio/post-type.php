<?php

namespace TotalTheme\Portfolio;

\defined( 'ABSPATH' ) || exit;

/**
 * Portfolio Post Type Class.
 */
class Post_Type {

	/**
	 * Checks if the post type is enabled.
	 */
	public static function is_enabled(): bool {
		return \class_exists( 'TotalThemeCore\Cpt\Portfolio', false );
	}

	/**
	 * Returns the post type name.
	 */
	public static function get_name(): string {
		return get_post_type_object( 'portfolio' )->labels->name ?? \esc_html__( 'Portfolio', 'total' );
	}

	/**
	 * Returns the post type menu icon.
	 */
	public static function get_menu_icon(): string {
		return get_post_type_object( 'portfolio' )->menu_icon ?? 'dashicons-portfolio';
	}

}
