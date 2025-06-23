<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "get_archives_link".
 */
final class Get_Archives_Link {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $link ) {
		if ( ! \str_contains( $link, '<span' ) ) {
			$link = \str_replace( '</a>&nbsp;(', '</a> <span class="get_archives_link-span wpex-float-right">(', $link );
			$link = \str_replace( ')', ')</span>', $link );
		}
		return $link;
	}

}
