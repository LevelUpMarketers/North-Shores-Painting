<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "get_next_post_where".
 */
final class Get_Next_Post_Where {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $where ) {
		$where .= " AND ( (m.meta_key = 'wpex_post_link' AND CAST(m.meta_value AS CHAR) = '' ) OR m.meta_id IS NULL ) ";
		return $where;
	}

}
