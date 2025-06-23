<?php

namespace TotalTheme\Testimonials;

\defined( 'ABSPATH' ) || exit;

/**
 * Testimonials Post Type Class.
 */
class Post_Type {

	/**
	 * Checks if the post type is enabled.
	 */
	public static function is_enabled(): bool {
		return \class_exists( 'TotalThemeCore\Cpt\Testimonials', false );
	}

	/**
	 * Returns the post type name.
	 */
	public static function get_name(): string {
		return get_post_type_object( 'testimonials' )->labels->name ?? \esc_html__( 'Testimonials', 'total' );
	}

	/**
	 * Returns the post type menu icon.
	 */
	public static function get_menu_icon(): string {
		return get_post_type_object( 'testimonials' )->menu_icon ?? 'dashicons-testimonial';
	}

}
