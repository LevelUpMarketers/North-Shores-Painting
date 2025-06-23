<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "get_previous_post_join".
 */
final class Get_Previous_Post_Join {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $join ) {
		global $wpdb;
		$join .= " LEFT JOIN $wpdb->postmeta AS m ON ( p.ID = m.post_id AND m.meta_key = 'wpex_post_link' )";
		return $join;
	}

}
