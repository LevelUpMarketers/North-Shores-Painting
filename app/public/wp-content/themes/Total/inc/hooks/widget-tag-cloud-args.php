<?php

namespace TotalTheme\Hooks;

\defined( 'ABSPATH' ) || exit;

/**
 * Hooks into "tag_cloud_args".
 */
final class Widget_Tag_Cloud_Args {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Callback method.
	 */
	public static function callback( $args ) {
		$args['largest']  = '1';
		$args['smallest'] = '1';
		$args['unit']     = 'em';
		return $args;
	}

}
