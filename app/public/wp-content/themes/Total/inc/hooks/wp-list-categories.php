<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "wp_list_categories".
 */
final class WP_List_Categories {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $output ) {
		if ( ! \str_contains( $output, '<span' ) ) {
			$output = \str_replace( '</a> (', '</a> <span class="cat-count-span wpex-float-right">(', $output );
			$output = \str_replace( ')', ')</span>', $output );
		}
		return $output;
	}

}
