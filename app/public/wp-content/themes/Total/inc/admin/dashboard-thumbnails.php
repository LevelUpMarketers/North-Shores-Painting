<?php

namespace TotalTheme\Admin;

\defined( 'ABSPATH' ) || exit;

/**
 * Display thumbnails in the dashboard.
 */
final class Dashboard_Thumbnails {

	/**
	 * Instance.
	 */
	private static $instance = null;

	/**
	 * Create or retrieve the instance of Dashboard_Thumbnails.
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
		\add_action( 'admin_init', [ $this, '_on_admin_init' ] );
	}

	/**
	 * Hooks into admin init.
	 */
	public function _on_admin_init() {
		$post_types = [
			'post',
			'page',
			'portfolio',
			'staff',
			'testimonials',
		];

		$post_types = (array) apply_filters( 'wpex_dashboard_thumbnails_post_types', $post_types ); // @deprecated
	//	$post_types = apply_filters( 'totaltheme/admin/thumbnail_posts_column/post_types', $post_types );

		if ( empty( $post_types ) ) {
			return;
		}

		foreach ( $post_types as $post_type ) {
			if ( \post_type_supports( $post_type, 'thumbnail' ) ) {
				\add_filter( "manage_{$post_type}_posts_columns", [ $this, '_add_column' ] );
				\add_action( "manage_{$post_type}_posts_custom_column", [ $this, '_display_column' ], 10, 2 );
			}
		}
	}

	/**
	 * Add new admin column.
	 */
	public function _add_column( $columns ) {
		$columns['wpex_post_thumbs'] = \esc_html__( 'Thumbnail', 'total' );
		return $columns;
	}

	/**
	 * Display admin column.
	 */
	public function _display_column( $column_name, $id ) {
		if ( 'wpex_post_thumbs' === $column_name ) {
			if ( \has_post_thumbnail( $id ) ) {
				\the_post_thumbnail(
					[ 60, 60 ],
					[ 'style' => 'width:60px;height:60px;object-fit:cover;border:1px solid rgba(0,0,0,.07);' ]
				);
			} else {
				echo '&#8212;';
			}
		}
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
